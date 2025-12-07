//
//  AppCoordinator.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//

import SwiftUI
import Combine

@MainActor
class AppCoordinator: ObservableObject {
    // MARK: - Published Properties
    @Published var isAuthenticated: Bool = false
    @Published var currentUser: User?
    @Published var selectedServer: VPNServer?
    @Published var isLoading: Bool = false
    
    // MARK: - Services
    private let authService: AuthServiceProtocol
    private let keychainService: KeychainServiceProtocol
    private var cancellables = Set<AnyCancellable>()
    
    // MARK: - Initialization
    init(authService: AuthServiceProtocol = AuthService.shared,
         keychainService: KeychainServiceProtocol = KeychainService.shared) {
        self.authService = authService
        self.keychainService = keychainService
        
        checkAuthenticationStatus()
    }
    
    // MARK: - Authentication
    func checkAuthenticationStatus() {
        Task {
            isLoading = true
            defer { isLoading = false }
            
            // Check if we have stored tokens
            if let _ = keychainService.getAccessToken() {
                do {
                    let user = try await authService.getCurrentUser()
                    self.currentUser = user
                    self.isAuthenticated = true
                    ConnectionLogger.shared.log("User authenticated from stored token", level: .info)
                } catch {
                    // Token is invalid, clear it
                    keychainService.clearTokens()
                    self.isAuthenticated = false
                    ConnectionLogger.shared.log("Stored token invalid: \(error.localizedDescription)", level: .warning)
                }
            } else {
                self.isAuthenticated = false
            }
        }
    }
    
    func login(email: String, password: String) async throws {
        isLoading = true
        defer { isLoading = false }
        
        let user = try await authService.login(email: email, password: password)
        self.currentUser = user
        self.isAuthenticated = true
        ConnectionLogger.shared.log("User logged in: \(user.email)", level: .info)
    }
    
    func logout() async {
        isLoading = true
        defer { isLoading = false }
        
        do {
            try await authService.logout()
        } catch {
            ConnectionLogger.shared.log("Logout error: \(error.localizedDescription)", level: .warning)
        }
        
        // Always clear local state
        keychainService.clearTokens()
        currentUser = nil
        isAuthenticated = false
        selectedServer = nil
        
        // Disconnect VPN if connected
        await VPNConnectionManager.shared.disconnect()
        
        ConnectionLogger.shared.log("User logged out", level: .info)
    }
    
    // MARK: - Server Selection
    func selectServer(_ server: VPNServer) {
        selectedServer = server
        ConnectionLogger.shared.log("Server selected: \(server.name) (\(server.country))", level: .debug)
    }
    
    func clearServerSelection() {
        selectedServer = nil
    }
}
