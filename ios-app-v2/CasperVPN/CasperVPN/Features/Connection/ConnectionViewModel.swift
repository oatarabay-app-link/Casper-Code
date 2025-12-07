//
//  ConnectionViewModel.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//

import Foundation
import Combine

/// ViewModel for managing VPN connection state and UI updates
@MainActor
final class ConnectionViewModel: ObservableObject {
    
    // MARK: - Published Properties
    @Published private(set) var connectionState: ConnectionState = .disconnected
    @Published private(set) var connectedServer: VPNServer?
    @Published private(set) var selectedServer: VPNServer?
    @Published private(set) var statistics: ConnectionStatistics = .empty
    @Published private(set) var isLoading: Bool = false
    @Published private(set) var error: VPNError?
    @Published private(set) var connectionDuration: String = "00:00:00"
    @Published private(set) var networkStatus: NetworkStatus?
    
    // MARK: - UI State
    @Published var showError: Bool = false
    @Published var showServerList: Bool = false
    
    // MARK: - Dependencies
    private let connectionManager: VPNConnectionManager
    private let serverService: ServerServiceProtocol
    private let networkMonitor: NetworkMonitor
    private var cancellables = Set<AnyCancellable>()
    private var durationTimer: Timer?
    
    // MARK: - Initialization
    init(connectionManager: VPNConnectionManager = .shared,
         serverService: ServerServiceProtocol = ServerService.shared,
         networkMonitor: NetworkMonitor = .shared) {
        self.connectionManager = connectionManager
        self.serverService = serverService
        self.networkMonitor = networkMonitor
        
        setupBindings()
    }
    
    deinit {
        stopDurationTimer()
    }
    
    // MARK: - Public Methods
    
    /// Connect to selected server or quick connect to best server
    func connect() async {
        guard let server = selectedServer else {
            // Quick connect - get recommended server
            await quickConnect()
            return
        }
        
        await connect(to: server)
    }
    
    /// Connect to a specific server
    func connect(to server: VPNServer) async {
        isLoading = true
        error = nil
        showError = false
        
        do {
            try await connectionManager.connect(to: server)
            startDurationTimer()
        } catch let vpnError as VPNError {
            self.error = vpnError
            showError = true
        } catch {
            self.error = .unknown(reason: error.localizedDescription)
            showError = true
        }
        
        isLoading = false
    }
    
    /// Quick connect to the best available server
    func quickConnect() async {
        isLoading = true
        error = nil
        showError = false
        
        do {
            guard let recommendedServer = try await serverService.getRecommendedServer() else {
                self.error = .serverNotSelected
                showError = true
                isLoading = false
                return
            }
            
            selectedServer = recommendedServer
            try await connectionManager.connect(to: recommendedServer)
            startDurationTimer()
        } catch let vpnError as VPNError {
            self.error = vpnError
            showError = true
        } catch {
            self.error = .unknown(reason: error.localizedDescription)
            showError = true
        }
        
        isLoading = false
    }
    
    /// Disconnect from VPN
    func disconnect() async {
        isLoading = true
        error = nil
        
        do {
            try await connectionManager.disconnect()
            stopDurationTimer()
        } catch let vpnError as VPNError {
            self.error = vpnError
            showError = true
        } catch {
            self.error = .unknown(reason: error.localizedDescription)
            showError = true
        }
        
        isLoading = false
    }
    
    /// Toggle connection state
    func toggleConnection() async {
        if connectionState.isConnected || connectionState == .connecting {
            await disconnect()
        } else {
            await connect()
        }
    }
    
    /// Select a server for connection
    func selectServer(_ server: VPNServer) {
        selectedServer = server
        showServerList = false
    }
    
    /// Clear error state
    func clearError() {
        error = nil
        showError = false
    }
    
    /// Retry last failed connection
    func retryConnection() async {
        if let server = selectedServer {
            await connect(to: server)
        } else {
            await quickConnect()
        }
    }
    
    // MARK: - Private Methods
    
    private func setupBindings() {
        // Observe connection state
        connectionManager.$connectionState
            .receive(on: DispatchQueue.main)
            .sink { [weak self] state in
                self?.connectionState = state
                self?.handleStateChange(state)
            }
            .store(in: &cancellables)
        
        // Observe connected server
        connectionManager.$connectedServer
            .receive(on: DispatchQueue.main)
            .assign(to: &$connectedServer)
        
        // Observe statistics
        connectionManager.$statistics
            .receive(on: DispatchQueue.main)
            .assign(to: &$statistics)
        
        // Observe connection errors
        connectionManager.$lastError
            .receive(on: DispatchQueue.main)
            .sink { [weak self] vpnError in
                if let error = vpnError {
                    self?.error = error
                    self?.showError = true
                }
            }
            .store(in: &cancellables)
        
        // Observe network status
        networkMonitor.pathUpdatePublisher
            .receive(on: DispatchQueue.main)
            .sink { [weak self] status in
                self?.networkStatus = status
            }
            .store(in: &cancellables)
    }
    
    private func handleStateChange(_ state: ConnectionState) {
        switch state {
        case .connected:
            startDurationTimer()
        case .disconnected:
            stopDurationTimer()
            connectionDuration = "00:00:00"
        default:
            break
        }
    }
    
    private func startDurationTimer() {
        stopDurationTimer()
        
        durationTimer = Timer.scheduledTimer(withTimeInterval: 1.0, repeats: true) { [weak self] _ in
            Task { @MainActor in
                self?.updateDuration()
            }
        }
    }
    
    private func stopDurationTimer() {
        durationTimer?.invalidate()
        durationTimer = nil
    }
    
    private func updateDuration() {
        guard case .connected(let since) = connectionState else { return }
        
        let duration = Date().timeIntervalSince(since)
        connectionDuration = formatDuration(duration)
    }
    
    private func formatDuration(_ duration: TimeInterval) -> String {
        let hours = Int(duration) / 3600
        let minutes = (Int(duration) % 3600) / 60
        let seconds = Int(duration) % 60
        
        return String(format: "%02d:%02d:%02d", hours, minutes, seconds)
    }
}

// MARK: - Connection View State
extension ConnectionViewModel {
    
    var buttonState: ConnectionButtonState {
        if isLoading {
            return .loading
        }
        
        switch connectionState {
        case .disconnected, .invalid:
            return .disconnected
        case .connecting, .reconnecting:
            return .connecting
        case .connected:
            return .connected
        case .disconnecting:
            return .disconnecting
        }
    }
    
    var statusText: String {
        if isLoading && connectionState.isDisconnected {
            return "Connecting..."
        }
        return connectionState.displayText
    }
    
    var canInteract: Bool {
        !isLoading && !connectionState.isTransitioning
    }
    
    var serverDisplayName: String {
        if let server = connectedServer ?? selectedServer {
            return "\(server.flagEmoji) \(server.displayName)"
        }
        return "Quick Connect"
    }
    
    var showStatistics: Bool {
        connectionState.isConnected
    }
}

// MARK: - Connection Button State
enum ConnectionButtonState {
    case disconnected
    case connecting
    case connected
    case disconnecting
    case loading
    
    var title: String {
        switch self {
        case .disconnected:
            return "Connect"
        case .connecting:
            return "Connecting..."
        case .connected:
            return "Disconnect"
        case .disconnecting:
            return "Disconnecting..."
        case .loading:
            return "Please wait..."
        }
    }
    
    var color: String {
        switch self {
        case .disconnected:
            return "green"
        case .connecting, .disconnecting, .loading:
            return "yellow"
        case .connected:
            return "red"
        }
    }
}
