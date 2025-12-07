//
//  VPNService.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//  Copyright © 2024 CasperVPN. All rights reserved.
//

import Foundation
import NetworkExtension
import Combine

/// Service responsible for managing VPN connections using Network Extension framework.
final class VPNService: VPNServiceProtocol {
    
    // MARK: - Singleton
    
    static let shared = VPNService()
    
    // MARK: - Properties
    
    private let serverService: ServerServiceProtocol
    private let keychainService: KeychainServiceProtocol
    private let logger = AppLogger.shared
    
    /// VPN Manager reference
    private var vpnManager: NETunnelProviderManager?
    
    /// Current connection status
    private(set) var connectionStatus: VPNConnectionStatus = .disconnected
    
    /// Subject for status updates
    private let statusSubject = CurrentValueSubject<VPNConnectionStatus, Never>(.disconnected)
    
    /// Publisher for connection status
    var statusPublisher: AnyPublisher<VPNConnectionStatus, Never> {
        statusSubject.eraseToAnyPublisher()
    }
    
    /// Current connection info
    private(set) var currentConnectionInfo: ConnectionInfo?
    
    /// Currently selected server
    private(set) var selectedServer: VPNServer?
    
    /// Connection start time for tracking
    private var connectionStartTime: Date?
    
    /// Timer for updating connection stats
    private var statsTimer: Timer?
    
    /// Cancellables for Combine subscriptions
    private var cancellables = Set<AnyCancellable>()
    
    // MARK: - Initialization
    
    init(
        serverService: ServerServiceProtocol = ServerService.shared,
        keychainService: KeychainServiceProtocol = KeychainService.shared
    ) {
        self.serverService = serverService
        self.keychainService = keychainService
        
        setupNotificationObservers()
    }
    
    deinit {
        statsTimer?.invalidate()
        NotificationCenter.default.removeObserver(self)
    }
    
    // MARK: - Public Methods
    
    func initialize() async throws {
        logger.info("Initializing VPN service")
        
        // Load existing VPN managers
        let managers = try await NETunnelProviderManager.loadAllFromPreferences()
        
        if let existingManager = managers.first {
            logger.info("Found existing VPN configuration")
            vpnManager = existingManager
            updateStatus(from: existingManager.connection.status)
        } else {
            logger.info("No existing VPN configuration found")
        }
    }
    
    func connect(to server: VPNServer) async throws {
        logger.info("Connecting to server: \(server.name)")
        
        guard server.isAvailable else {
            throw VPNError.serverUnavailable
        }
        
        selectedServer = server
        updateStatus(.connecting)
        
        do {
            // Get server configuration
            let config = try await serverService.getServerConfig(for: server)
            
            // Configure VPN manager
            try await configureVPNManager(with: config, for: server)
            
            // Log connection to backend
            let connectRequest = ConnectRequest()
            _ = try? await serverService.logConnection(to: server, request: connectRequest)
            
            // Start VPN connection
            try startVPNConnection()
            
        } catch {
            logger.error("Failed to connect: \(error.localizedDescription)")
            updateStatus(.disconnected)
            throw error
        }
    }
    
    func disconnect() async throws {
        logger.info("Disconnecting from VPN")
        
        updateStatus(.disconnecting)
        
        // Log disconnection to backend
        if let server = selectedServer, let info = currentConnectionInfo {
            let request = DisconnectRequest(
                bytesUploaded: info.bytesSent,
                bytesDownloaded: info.bytesReceived,
                disconnectReason: .userInitiated
            )
            _ = try? await serverService.logDisconnection(from: server, request: request)
        }
        
        // Stop VPN connection
        vpnManager?.connection.stopVPNTunnel()
        
        // Clear state
        currentConnectionInfo = nil
        connectionStartTime = nil
        stopStatsTimer()
    }
    
    func selectServer(_ server: VPNServer) {
        logger.info("Selected server: \(server.name)")
        selectedServer = server
    }
    
    func getRecommendedServer() async throws -> VPNServer {
        logger.info("Getting recommended server")
        return try await serverService.getRecommendedServer(request: nil)
    }
    
    func refreshStatus() async {
        guard let manager = vpnManager else {
            updateStatus(.invalid)
            return
        }
        
        updateStatus(from: manager.connection.status)
    }
    
    // MARK: - Private Methods
    
    private func setupNotificationObservers() {
        NotificationCenter.default.addObserver(
            self,
            selector: #selector(vpnStatusDidChange(_:)),
            name: .NEVPNStatusDidChange,
            object: nil
        )
    }
    
    @objc private func vpnStatusDidChange(_ notification: Notification) {
        guard let connection = notification.object as? NEVPNConnection else { return }
        updateStatus(from: connection.status)
    }
    
    private func updateStatus(from neStatus: NEVPNStatus) {
        let newStatus = VPNConnectionStatus(from: neStatus)
        updateStatus(newStatus)
    }
    
    private func updateStatus(_ newStatus: VPNConnectionStatus) {
        logger.debug("VPN status changed: \(newStatus.displayName)")
        
        connectionStatus = newStatus
        statusSubject.send(newStatus)
        
        // Handle status-specific actions
        switch newStatus {
        case .connected:
            handleConnected()
        case .disconnected:
            handleDisconnected()
        default:
            break
        }
    }
    
    private func handleConnected() {
        connectionStartTime = Date()
        startStatsTimer()
        
        // Create initial connection info
        if let server = selectedServer {
            currentConnectionInfo = ConnectionInfo(
                server: server,
                connectedAt: connectionStartTime ?? Date(),
                bytesReceived: 0,
                bytesSent: 0,
                clientIP: nil,
                serverIP: server.name,
                protocol: server.protocol
            )
        }
    }
    
    private func handleDisconnected() {
        stopStatsTimer()
        currentConnectionInfo = nil
        connectionStartTime = nil
    }
    
    private func configureVPNManager(with config: VPNConfig, for server: VPNServer) async throws {
        logger.info("Configuring VPN manager")
        
        // Load or create VPN manager
        let managers = try await NETunnelProviderManager.loadAllFromPreferences()
        let manager = managers.first ?? NETunnelProviderManager()
        
        // Configure protocol
        let protocolConfig = NETunnelProviderProtocol()
        protocolConfig.providerBundleIdentifier = AppConfig.tunnelBundleIdentifier
        protocolConfig.serverAddress = server.name
        
        // Store configuration in provider configuration
        protocolConfig.providerConfiguration = [
            "serverAddress": config.serverHostname,
            "serverPort": config.serverPort,
            "protocol": config.protocol.rawValue,
            "config": config.config
        ]
        
        // Store private key securely in keychain
        if let privateKey = config.clientPrivateKey {
            try keychainService.save(privateKey, forKey: KeychainKeys.vpnConfig)
        }
        
        // Apply configuration
        manager.protocolConfiguration = protocolConfig
        manager.localizedDescription = "CasperVPN"
        manager.isEnabled = true
        
        // Save configuration
        try await manager.saveToPreferences()
        try await manager.loadFromPreferences()
        
        vpnManager = manager
        logger.info("VPN manager configured successfully")
    }
    
    private func startVPNConnection() throws {
        guard let manager = vpnManager else {
            throw VPNError.notConfigured
        }
        
        let session = manager.connection as? NETunnelProviderSession
        
        do {
            try session?.startTunnel(options: nil)
        } catch {
            throw VPNError.connectionFailed(error.localizedDescription)
        }
    }
    
    private func startStatsTimer() {
        statsTimer = Timer.scheduledTimer(withTimeInterval: 1.0, repeats: true) { [weak self] _ in
            self?.updateConnectionStats()
        }
    }
    
    private func stopStatsTimer() {
        statsTimer?.invalidate()
        statsTimer = nil
    }
    
    private func updateConnectionStats() {
        // In a real implementation, this would query the tunnel for actual stats
        // For now, we'll simulate some basic stats
        guard var info = currentConnectionInfo else { return }
        
        // Simulate bandwidth usage (placeholder - real implementation would query tunnel)
        info.bytesReceived += Int64.random(in: 1000...10000)
        info.bytesSent += Int64.random(in: 500...5000)
        
        currentConnectionInfo = info
    }
}

// MARK: - VPN Manager Extension

extension NETunnelProviderManager {
    
    /// Helper to load all managers asynchronously
    static func loadAllFromPreferencesAsync() async throws -> [NETunnelProviderManager] {
        try await withCheckedThrowingContinuation { continuation in
            loadAllFromPreferences { managers, error in
                if let error = error {
                    continuation.resume(throwing: error)
                } else {
                    continuation.resume(returning: managers ?? [])
                }
            }
        }
    }
    
    /// Helper to save to preferences asynchronously
    func saveToPreferencesAsync() async throws {
        try await withCheckedThrowingContinuation { (continuation: CheckedContinuation<Void, Error>) in
            saveToPreferences { error in
                if let error = error {
                    continuation.resume(throwing: error)
                } else {
                    continuation.resume()
                }
            }
        }
    }
    
    /// Helper to load from preferences asynchronously
    func loadFromPreferencesAsync() async throws {
        try await withCheckedThrowingContinuation { (continuation: CheckedContinuation<Void, Error>) in
            loadFromPreferences { error in
                if let error = error {
                    continuation.resume(throwing: error)
                } else {
                    continuation.resume()
                }
            }
        }
    }
}
