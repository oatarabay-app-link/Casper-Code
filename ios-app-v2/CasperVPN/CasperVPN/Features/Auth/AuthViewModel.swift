//
//  AuthViewModel.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//  Copyright © 2024 CasperVPN. All rights reserved.
//

import Foundation
import Combine

/// ViewModel for managing authentication state and user sessions.
@MainActor
final class AuthViewModel: ObservableObject {
    
    // MARK: - Published Properties
    
    /// Current authenticated user
    @Published private(set) var currentUser: User?
    
    /// Whether a user is currently authenticated
    @Published private(set) var isAuthenticated = false
    
    /// Whether an auth operation is in progress
    @Published var isLoading = false
    
    /// Current error message
    @Published var errorMessage: String?
    
    /// Whether to show error alert
    @Published var showError = false
    
    // MARK: - Properties
    
    private let authService: AuthServiceProtocol
    private var cancellables = Set<AnyCancellable>()
    
    // MARK: - Initialization
    
    init(authService: AuthServiceProtocol = AuthService.shared) {
        self.authService = authService
        setupBindings()
    }
    
    // MARK: - Setup
    
    private func setupBindings() {
        authService.authStatePublisher
            .receive(on: DispatchQueue.main)
            .sink { [weak self] state in
                self?.handleAuthStateChange(state)
            }
            .store(in: &cancellables)
    }
    
    private func handleAuthStateChange(_ state: AuthState) {
        switch state {
        case .authenticated(let user):
            currentUser = user
            isAuthenticated = true
        case .unauthenticated:
            currentUser = nil
            isAuthenticated = false
        case .unknown:
            break
        }
    }
    
    // MARK: - Public Methods
    
    /// Checks for an existing valid session on app launch
    func checkExistingSession() async {
        isLoading = true
        
        let hasSession = await authService.checkExistingSession()
        
        if hasSession {
            currentUser = authService.currentUser
            isAuthenticated = true
        }
        
        isLoading = false
    }
    
    /// Registers a new user account
    func register(
        email: String,
        password: String,
        firstName: String?,
        lastName: String?
    ) async {
        isLoading = true
        clearError()
        
        do {
            let response = try await authService.register(
                email: email,
                password: password,
                firstName: firstName,
                lastName: lastName
            )
            
            currentUser = response.user
            isAuthenticated = true
        } catch {
            handleError(error)
        }
        
        isLoading = false
    }
    
    /// Authenticates an existing user
    func login(
        email: String,
        password: String,
        rememberMe: Bool
    ) async {
        isLoading = true
        clearError()
        
        do {
            let response = try await authService.login(
                email: email,
                password: password,
                rememberMe: rememberMe
            )
            
            currentUser = response.user
            isAuthenticated = true
        } catch {
            handleError(error)
        }
        
        isLoading = false
    }
    
    /// Logs out the current user
    func logout() async {
        isLoading = true
        clearError()
        
        do {
            try await authService.logout()
            currentUser = nil
            isAuthenticated = false
        } catch {
            handleError(error)
        }
        
        isLoading = false
    }
    
    /// Initiates password reset flow
    func forgotPassword(email: String) async {
        isLoading = true
        clearError()
        
        do {
            try await authService.forgotPassword(email: email)
        } catch {
            handleError(error)
        }
        
        isLoading = false
    }
    
    /// Resets password with token
    func resetPassword(token: String, newPassword: String) async {
        isLoading = true
        clearError()
        
        do {
            try await authService.resetPassword(token: token, newPassword: newPassword)
        } catch {
            handleError(error)
        }
        
        isLoading = false
    }
    
    /// Verifies email with token
    func verifyEmail(token: String) async {
        isLoading = true
        clearError()
        
        do {
            try await authService.verifyEmail(token: token)
            
            // Update local user state
            if var user = currentUser {
                user.isEmailVerified = true
                currentUser = user
            }
        } catch {
            handleError(error)
        }
        
        isLoading = false
    }
    
    /// Changes the user's password
    func changePassword(currentPassword: String, newPassword: String) async {
        isLoading = true
        clearError()
        
        do {
            try await authService.changePassword(
                currentPassword: currentPassword,
                newPassword: newPassword
            )
        } catch {
            handleError(error)
        }
        
        isLoading = false
    }
    
    /// Refreshes the current user data
    func refreshUser() async {
        guard isAuthenticated else { return }
        
        do {
            let response = try await authService.refreshToken()
            currentUser = response.user
        } catch {
            // Token refresh failed - user may need to re-authenticate
            handleError(error)
        }
    }
    
    // MARK: - Private Methods
    
    private func handleError(_ error: Error) {
        if let apiError = error as? APIError {
            switch apiError {
            case .unauthorized:
                errorMessage = "Invalid email or password"
            case .tokenExpired:
                errorMessage = "Your session has expired. Please sign in again."
                Task {
                    await logout()
                }
            default:
                errorMessage = apiError.errorDescription
            }
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
