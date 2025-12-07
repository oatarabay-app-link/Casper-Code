//
//  VPNConnectionManager.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//

import Foundation
import NetworkExtension
import Combine

/// Main connection orchestrator for VPN connections
/// Implements connection state machine: Disconnected → Connecting → Connected → Disconnecting
@MainActor
final class VPNConnectionManager: ObservableObject, VPNConnectionManagerProtocol {
    
    // MARK: - Singleton
    static let shared = VPNConnectionManager()
    
    // MARK: - Published Properties
    @Published private(set) var connectionState: ConnectionState = .disconnected
    @Published private(set) var connectedServer: VPNServer?
    @Published private(set) var statistics: ConnectionStatistics = .empty
    @Published private(set) var lastError: VPNError?
    @Published private(set) var isInitialized: Bool = false
    
    // MARK: - Publishers
    var connectionStatePublisher: AnyPublisher<ConnectionState, Never> {
        $connectionState.eraseToAnyPublisher()
    }
    
    var statisticsPublisher: AnyPublisher<ConnectionStatistics, Never> {
        $statistics.eraseToAnyPublisher()
    }
    
    // MARK: - Private Properties
    private var vpnManager: NETunnelProviderManager?
    private var statusObserver: NSObjectProtocol?
    private var cancellables = Set<AnyCancellable>()
    private var connectionStartTime: Date?
    private var statisticsTimer: Timer?
    
    // MARK: - Dependencies
    private let logger = ConnectionLogger.shared
    private let serverService: ServerServiceProtocol
    private let keychainService: KeychainServiceProtocol
    private let killSwitchManager: KillSwitchManager
    private let networkMonitor: NetworkMonitor
    
    // MARK: - Reconnection State
    private var reconnectionAttempts = 0
    private let maxReconnectionAttempts = 5
    private var isReconnecting = false
    private var pendingServer: VPNServer?
    
    // MARK: - Initialization
    private init(serverService: ServerServiceProtocol = ServerService.shared,
                 keychainService: KeychainServiceProtocol = KeychainService.shared,
                 killSwitchManager: KillSwitchManager = KillSwitchManager.shared,
                 networkMonitor: NetworkMonitor = NetworkMonitor.shared) {
        self.serverService = serverService
        self.keychainService = keychainService
        self.killSwitchManager = killSwitchManager
        self.networkMonitor = networkMonitor
        
        setupNetworkMonitoring()
    }
    
    deinit {
        stopStatusObservation()
        stopStatisticsTimer()
    }
    
    // MARK: - Initialization
    
    func initialize() async {
        logger.log("Initializing VPN Connection Manager", level: .info)
        
        do {
            // Load or create VPN manager
            vpnManager = try await loadOrCreateManager()
            
            // Start observing VPN status
            startStatusObservation()
            
            // Update initial state
            await refreshStatus()
            
            isInitialized = true
            logger.log("VPN Connection Manager initialized", level: .info)
        } catch {
            logger.log("Failed to initialize VPN Manager: \(error.localizedDescription)", level: .error)
            lastError = VPNError.networkExtensionNotConfigured
        }
    }
    
    // MARK: - Connection Management
    
    /// Connect to a VPN server
    func connect(to server: VPNServer) async throws {
        logger.logConnectionAttempt(server: server)
        
        // Check prerequisites
        guard isInitialized else {
            throw VPNError.networkExtensionNotConfigured
        }
        
        guard connectionState.canConnect else {
            if connectionState.isConnected {
                throw VPNError.alreadyConnected
            }
            throw VPNError.connectionFailed(reason: "Connection already in progress")
        }
        
        // Check network availability
        guard networkMonitor.isConnected else {
            throw VPNError.noInternetConnection
        }
        
        // Update state
        connectionState = .connecting
        pendingServer = server
        lastError = nil
        
        do {
            // Fetch server configuration from API
            let config = try await fetchServerConfiguration(serverId: server.id)
            
            // Configure the tunnel
            try await configureTunnel(server: server, config: config)
            
            // Start the tunnel
            try await startTunnel()
            
            // Log connection to backend
            try? await serverService.logConnection(serverId: server.id)
            
            // Start statistics timer
            startStatisticsTimer()
            
            connectedServer = server
            connectionStartTime = Date()
            reconnectionAttempts = 0
            
            logger.logConnectionSuccess(server: server)
            
        } catch {
            connectionState = .disconnected
            pendingServer = nil
            
            let vpnError = mapError(error)
            lastError = vpnError
            
            logger.logConnectionFailure(server: server, error: vpnError)
            throw vpnError
        }
    }
    
    /// Disconnect from VPN
    func disconnect() async throws {
        logger.log("Disconnect requested", level: .info)
        
        guard connectionState.canDisconnect else {
            if connectionState.isDisconnected {
                throw VPNError.alreadyDisconnected
            }
            throw VPNError.disconnectionFailed(reason: "Cannot disconnect in current state")
        }
        
        connectionState = .disconnecting
        isReconnecting = false
        
        do {
            // Stop the tunnel
            try await stopTunnel()
            
            // Log disconnection to backend
            if let server = connectedServer {
                try? await serverService.logDisconnection(
                    serverId: server.id,
                    duration: statistics.connectionDuration,
                    bytesReceived: statistics.bytesReceived,
                    bytesSent: statistics.bytesSent
                )
            }
            
            // Stop statistics timer
            stopStatisticsTimer()
            
            // Reset state
            connectionState = .disconnected
            connectedServer = nil
            connectionStartTime = nil
            statistics = .empty
            pendingServer = nil
            
            logger.logDisconnection(reason: "User requested")
            
        } catch {
            // Force state to disconnected
            connectionState = .disconnected
            
            let vpnError = mapError(error)
            lastError = vpnError
            
            logger.log("Disconnect error: \(vpnError.localizedDescription)", level: .error)
            throw vpnError
        }
    }
    
    /// Refresh VPN status
    func refreshStatus() async {
        guard let manager = vpnManager else { return }
        
        let status = manager.connection.status
        updateConnectionState(from: status)
    }
    
    // MARK: - Private Methods
    
    private func loadOrCreateManager() async throws -> NETunnelProviderManager {
        // Load existing managers
        let managers = try await NETunnelProviderManager.loadAllFromPreferences()
        
        if let existingManager = managers.first {
            logger.log("Loaded existing VPN manager", level: .debug)
            return existingManager
        }
        
        // Create new manager
        let manager = NETunnelProviderManager()
        manager.localizedDescription = Config.vpnLocalizedDescription
        
        // Configure protocol
        let tunnelProtocol = NETunnelProviderProtocol()
        tunnelProtocol.providerBundleIdentifier = Config.tunnelBundleIdentifier
        tunnelProtocol.serverAddress = "CasperVPN"
        manager.protocolConfiguration = tunnelProtocol
        
        // Save to preferences
        try await manager.saveToPreferences()
        try await manager.loadFromPreferences()
        
        logger.log("Created new VPN manager", level: .info)
        
        return manager
    }
    
    private func fetchServerConfiguration(serverId: String) async throws -> VPNConfig {
        logger.log("Fetching configuration for server \(serverId)", level: .debug)
        return try await serverService.fetchServerConfig(serverId: serverId)
    }
    
    private func configureTunnel(server: VPNServer, config: VPNConfig) async throws {
        guard let manager = vpnManager else {
            throw VPNError.tunnelNotFound
        }
        
        logger.log("Configuring tunnel for \(server.name)", level: .debug)
        
        // Create provider configuration
        let providerConfig = try WireGuardManager.shared.createProviderConfiguration(config)
        
        // Update tunnel protocol
        let tunnelProtocol = NETunnelProviderProtocol()
        tunnelProtocol.providerBundleIdentifier = Config.tunnelBundleIdentifier
        tunnelProtocol.serverAddress = server.hostname
        tunnelProtocol.providerConfiguration = providerConfig
        
        // Configure kill switch if enabled
        if killSwitchManager.isEnabled {
            manager.onDemandRules = killSwitchManager.configureOnDemandRules()
            manager.isOnDemandEnabled = true
        } else {
            manager.isOnDemandEnabled = false
        }
        
        manager.protocolConfiguration = tunnelProtocol
        manager.isEnabled = true
        
        // Save configuration
        try await manager.saveToPreferences()
        try await manager.loadFromPreferences()
        
        logger.log("Tunnel configured successfully", level: .debug)
    }
    
    private func startTunnel() async throws {
        guard let manager = vpnManager else {
            throw VPNError.tunnelNotFound
        }
        
        logger.log("Starting tunnel", level: .debug)
        
        do {
            try manager.connection.startVPNTunnel()
            
            // Wait for connection with timeout
            try await waitForConnection(timeout: Config.connectionTimeout)
            
            logger.log("Tunnel started successfully", level: .info)
        } catch {
            throw VPNError.tunnelStartFailed(reason: error.localizedDescription)
        }
    }
    
    private func stopTunnel() async throws {
        guard let manager = vpnManager else {
            throw VPNError.tunnelNotFound
        }
        
        logger.log("Stopping tunnel", level: .debug)
        
        manager.connection.stopVPNTunnel()
        
        // Wait for disconnection
        try await waitForDisconnection(timeout: 10)
        
        logger.log("Tunnel stopped", level: .info)
    }
    
    private func waitForConnection(timeout: TimeInterval) async throws {
        let startTime = Date()
        
        while Date().timeIntervalSince(startTime) < timeout {
            if let manager = vpnManager, manager.connection.status == .connected {
                return
            }
            
            if let manager = vpnManager, manager.connection.status == .disconnected {
                throw VPNError.connectionFailed(reason: "Tunnel disconnected unexpectedly")
            }
            
            try await Task.sleep(nanoseconds: 100_000_000) // 0.1 seconds
        }
        
        throw VPNError.connectionTimeout
    }
    
    private func waitForDisconnection(timeout: TimeInterval) async throws {
        let startTime = Date()
        
        while Date().timeIntervalSince(startTime) < timeout {
            if let manager = vpnManager, manager.connection.status == .disconnected {
                return
            }
            
            try await Task.sleep(nanoseconds: 100_000_000) // 0.1 seconds
        }
        
        // Force state even if timeout
        connectionState = .disconnected
    }
    
    // MARK: - Status Observation
    
    private func startStatusObservation() {
        statusObserver = NotificationCenter.default.addObserver(
            forName: .NEVPNStatusDidChange,
            object: nil,
            queue: .main
        ) { [weak self] notification in
            guard let self = self,
                  let connection = notification.object as? NEVPNConnection else { return }
            
            Task { @MainActor in
                self.handleStatusChange(connection.status)
            }
        }
    }
    
    private func stopStatusObservation() {
        if let observer = statusObserver {
            NotificationCenter.default.removeObserver(observer)
            statusObserver = nil
        }
    }
    
    private func handleStatusChange(_ status: NEVPNStatus) {
        logger.log("VPN status changed: \(statusDescription(status))", level: .debug)
        updateConnectionState(from: status)
        
        // Handle auto-reconnect
        if status == .disconnected && isReconnecting && reconnectionAttempts < maxReconnectionAttempts {
            Task {
                await attemptReconnection()
            }
        }
    }
    
    private func updateConnectionState(from status: NEVPNStatus) {
        switch status {
        case .invalid:
            connectionState = .invalid
        case .disconnected:
            connectionState = .disconnected
            if !isReconnecting {
                connectedServer = nil
                connectionStartTime = nil
            }
        case .connecting:
            connectionState = .connecting
        case .connected:
            connectionState = .connected(since: connectionStartTime ?? Date())
            if connectionStartTime == nil {
                connectionStartTime = Date()
            }
            statistics = ConnectionStatistics(connectedSince: connectionStartTime)
        case .reasserting:
            connectionState = .reconnecting
        case .disconnecting:
            connectionState = .disconnecting
        @unknown default:
            connectionState = .invalid
        }
    }
    
    private func statusDescription(_ status: NEVPNStatus) -> String {
        switch status {
        case .invalid: return "Invalid"
        case .disconnected: return "Disconnected"
        case .connecting: return "Connecting"
        case .connected: return "Connected"
        case .reasserting: return "Reasserting"
        case .disconnecting: return "Disconnecting"
        @unknown default: return "Unknown"
        }
    }
    
    // MARK: - Network Monitoring
    
    private func setupNetworkMonitoring() {
        networkMonitor.pathUpdatePublisher
            .receive(on: DispatchQueue.main)
            .sink { [weak self] status in
                self?.handleNetworkChange(status)
            }
            .store(in: &cancellables)
    }
    
    private func handleNetworkChange(_ status: NetworkStatus) {
        logger.log("Network changed: connected=\(status.isConnected), type=\(status.connectionType)", level: .debug)
        
        guard Config.enableAutoReconnect else { return }
        
        if status.isConnected && connectionState.isDisconnected && pendingServer != nil {
            // Network became available and we were trying to connect
            Task {
                await attemptReconnection()
            }
        } else if !status.isConnected && connectionState.isConnected {
            // Network lost while connected
            isReconnecting = true
            pendingServer = connectedServer
        }
    }
    
    // MARK: - Auto-Reconnect
    
    private func attemptReconnection() async {
        guard let server = pendingServer ?? connectedServer else { return }
        guard reconnectionAttempts < maxReconnectionAttempts else {
            logger.log("Max reconnection attempts reached", level: .warning)
            isReconnecting = false
            pendingServer = nil
            return
        }
        
        reconnectionAttempts += 1
        connectionState = .reconnecting
        
        logger.log("Attempting reconnection (\(reconnectionAttempts)/\(maxReconnectionAttempts))", level: .info)
        
        // Exponential backoff
        let delay = Config.retryBaseDelay * pow(2.0, Double(reconnectionAttempts - 1))
        try? await Task.sleep(nanoseconds: UInt64(delay * 1_000_000_000))
        
        do {
            // Reset connection state
            connectionState = .connecting
            
            let config = try await fetchServerConfiguration(serverId: server.id)
            try await configureTunnel(server: server, config: config)
            try await startTunnel()
            
            connectedServer = server
            connectionStartTime = Date()
            reconnectionAttempts = 0
            isReconnecting = false
            pendingServer = nil
            
            logger.logConnectionSuccess(server: server)
            
        } catch {
            logger.log("Reconnection attempt \(reconnectionAttempts) failed: \(error.localizedDescription)", level: .warning)
            
            if reconnectionAttempts < maxReconnectionAttempts {
                // Will retry via status change handler
            } else {
                connectionState = .disconnected
                isReconnecting = false
                lastError = mapError(error)
            }
        }
    }
    
    // MARK: - Statistics
    
    private func startStatisticsTimer() {
        stopStatisticsTimer()
        
        statisticsTimer = Timer.scheduledTimer(withTimeInterval: 1.0, repeats: true) { [weak self] _ in
            Task { @MainActor in
                self?.updateStatistics()
            }
        }
    }
    
    private func stopStatisticsTimer() {
        statisticsTimer?.invalidate()
        statisticsTimer = nil
    }
    
    private func updateStatistics() {
        guard connectionState.isConnected else { return }
        
        // Update connection duration
        if let startTime = connectionStartTime {
            statistics = ConnectionStatistics(
                bytesReceived: statistics.bytesReceived,
                bytesSent: statistics.bytesSent,
                packetsReceived: statistics.packetsReceived,
                packetsSent: statistics.packetsSent,
                connectedSince: startTime,
                lastHandshake: statistics.lastHandshake
            )
        }
        
        // Note: Actual traffic statistics would need to come from the tunnel extension
        // via app group shared data or IPC
    }
    
    // MARK: - Error Mapping
    
    private func mapError(_ error: Error) -> VPNError {
        if let vpnError = error as? VPNError {
            return vpnError
        }
        
        let nsError = error as NSError
        
        // Map NEVPNError codes
        if nsError.domain == NEVPNErrorDomain {
            return VPNError.fromTunnelProviderError(error)
        }
        
        // Map network errors
        if nsError.domain == NSURLErrorDomain {
            switch nsError.code {
            case NSURLErrorNotConnectedToInternet:
                return .noInternetConnection
            case NSURLErrorTimedOut:
                return .connectionTimeout
            case NSURLErrorCannotFindHost, NSURLErrorCannotConnectToHost:
                return .serverUnreachable
            default:
                return .networkUnavailable
            }
        }
        
        return .unknown(reason: error.localizedDescription)
    }
}
