//
//  ConnectionViewModel.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//  Copyright © 2024 CasperVPN. All rights reserved.
//

import Foundation
import Combine
import CoreLocation

/// ViewModel for managing VPN connection state and operations.
@MainActor
final class ConnectionViewModel: ObservableObject {
    
    // MARK: - Published Properties
    
    /// Current connection status
    @Published private(set) var status: VPNConnectionStatus = .disconnected
    
    /// Currently selected server
    @Published private(set) var selectedServer: VPNServer?
    
    /// Current connection info (when connected)
    @Published private(set) var connectionInfo: ConnectionInfo?
    
    /// Whether VPN is loading/initializing
    @Published var isLoading = false
    
    /// Current error message
    @Published var errorMessage: String?
    
    /// Whether to show error alert
    @Published var showError = false
    
    // MARK: - Computed Properties
    
    /// Whether VPN is currently connected
    var isConnected: Bool {
        status == .connected
    }
    
    /// Whether VPN is currently connecting
    var isConnecting: Bool {
        status == .connecting || status == .reasserting
    }
    
    /// Whether VPN is currently disconnecting
    var isDisconnecting: Bool {
        status == .disconnecting
    }
    
    // MARK: - Properties
    
    private let vpnService: VPNServiceProtocol
    private let serverService: ServerServiceProtocol
    private var cancellables = Set<AnyCancellable>()
    
    /// Location manager for nearest server selection
    private let locationManager = CLLocationManager()
    
    // MARK: - Initialization
    
    init(
        vpnService: VPNServiceProtocol = VPNService.shared,
        serverService: ServerServiceProtocol = ServerService.shared
    ) {
        self.vpnService = vpnService
        self.serverService = serverService
        setupBindings()
    }
    
    // MARK: - Setup
    
    private func setupBindings() {
        vpnService.statusPublisher
            .receive(on: DispatchQueue.main)
            .sink { [weak self] status in
                self?.handleStatusChange(status)
            }
            .store(in: &cancellables)
    }
    
    private func handleStatusChange(_ newStatus: VPNConnectionStatus) {
        let oldStatus = status
        status = newStatus
        
        // Update connection info
        if newStatus == .connected {
            connectionInfo = vpnService.currentConnectionInfo
            selectedServer = vpnService.selectedServer
        } else if newStatus == .disconnected && oldStatus != .disconnected {
            connectionInfo = nil
        }
    }
    
    // MARK: - Public Methods
    
    /// Initializes the VPN manager
    func initializeVPNManager() async {
        isLoading = true
        
        do {
            try await vpnService.initialize()
            
            // Update initial state
            selectedServer = vpnService.selectedServer
            connectionInfo = vpnService.currentConnectionInfo
        } catch {
            handleError(error)
        }
        
        isLoading = false
    }
    
    /// Connects to a specific server
    func connect(to server: VPNServer) async {
        clearError()
        
        guard server.isAvailable else {
            errorMessage = "This server is currently unavailable"
            showError = true
            return
        }
        
        selectedServer = server
        
        do {
            try await vpnService.connect(to: server)
        } catch {
            handleError(error)
        }
    }
    
    /// Disconnects from the current server
    func disconnect() async {
        clearError()
        
        do {
            try await vpnService.disconnect()
        } catch {
            handleError(error)
        }
    }
    
    /// Quick connect to the recommended server
    func quickConnect() async {
        clearError()
        
        do {
            // Get recommended server
            let server = try await vpnService.getRecommendedServer()
            
            // Connect to it
            try await vpnService.connect(to: server)
        } catch {
            handleError(error)
        }
    }
    
    /// Connects to the nearest server based on location
    func connectToNearest() async {
        clearError()
        
        // Request location permission if needed
        let authStatus = locationManager.authorizationStatus
        if authStatus == .notDetermined {
            locationManager.requestWhenInUseAuthorization()
            return
        }
        
        guard authStatus == .authorizedWhenInUse || authStatus == .authorizedAlways else {
            errorMessage = "Location access is required to find the nearest server"
            showError = true
            return
        }
        
        do {
            // Get current location
            let location = locationManager.location?.coordinate
            
            // Create recommendation request
            let request = RecommendationRequest(
                latitude: location?.latitude,
                longitude: location?.longitude,
                preferredCountry: nil,
                preferredProtocol: .wireGuard
            )
            
            // Get recommended server
            let server = try await serverService.getRecommendedServer(request: request)
            
            // Connect
            try await vpnService.connect(to: server)
        } catch {
            handleError(error)
        }
    }
    
    /// Selects a server without connecting
    func selectServer(_ server: VPNServer) {
        selectedServer = server
        vpnService.selectServer(server)
    }
    
    /// Refreshes the connection status
    func refreshConnectionStatus() async {
        await vpnService.refreshStatus()
        
        // Update local state
        connectionInfo = vpnService.currentConnectionInfo
        selectedServer = vpnService.selectedServer
    }
    
    /// Toggles connection state
    func toggleConnection() async {
        if isConnected {
            await disconnect()
        } else if let server = selectedServer {
            await connect(to: server)
        } else {
            await quickConnect()
        }
    }
    
    // MARK: - Private Methods
    
    private func handleError(_ error: Error) {
        if let vpnError = error as? VPNError {
            errorMessage = vpnError.errorDescription
        } else if let apiError = error as? APIError {
            errorMessage = apiError.errorDescription
        } else {
            errorMessage = error.localizedDescription
        }
        showError = true
    }
    
    private func clearError() {
        errorMessage = nil
        showError = false
    }
}

// MARK: - Timer Updates

extension ConnectionViewModel {
    
    /// Starts periodic updates for connection info
    func startConnectionInfoUpdates() {
        // The VPNService handles this internally with its stats timer
        // This method is here for future extensibility
    }
    
    /// Stops periodic updates
    func stopConnectionInfoUpdates() {
        // Cleanup if needed
    }
}
