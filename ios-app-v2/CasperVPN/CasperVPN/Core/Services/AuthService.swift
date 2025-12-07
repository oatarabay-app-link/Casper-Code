//
//  AuthService.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//  Copyright © 2024 CasperVPN. All rights reserved.
//

import Foundation
import Combine

/// Service responsible for user authentication and session management.
final class AuthService: AuthServiceProtocol {
    
    // MARK: - Singleton
    
    static let shared = AuthService()
    
    // MARK: - Properties
    
    private let apiClient: APIClientProtocol
    private let keychainService: KeychainServiceProtocol
    private let logger = AppLogger.shared
    
    /// Current authenticated user
    private(set) var currentUser: User?
    
    /// Subject for auth state changes
    private let authStateSubject = CurrentValueSubject<AuthState, Never>(.unknown)
    
    /// Publisher for auth state
    var authStatePublisher: AnyPublisher<AuthState, Never> {
        authStateSubject.eraseToAnyPublisher()
    }
    
    /// Whether a user is authenticated
    var isAuthenticated: Bool {
        currentUser != nil
    }
    
    // MARK: - Initialization
    
    init(
        apiClient: APIClientProtocol = APIClient.shared,
        keychainService: KeychainServiceProtocol = KeychainService.shared
    ) {
        self.apiClient = apiClient
        self.keychainService = keychainService
    }
    
    // MARK: - Authentication Methods
    
    func register(
        email: String,
        password: String,
        firstName: String?,
        lastName: String?
    ) async throws -> AuthResponse {
        logger.info("Registering new user: \(email)")
        
        let request = RegisterRequest(
            email: email,
            password: password,
            firstName: firstName,
            lastName: lastName
        )
        
        let response: APIResponse<AuthResponse> = try await apiClient.post(
            endpoint: "/api/auth/register",
            body: request
        )
        
        guard let authResponse = response.data else {
            logger.error("Registration failed: No data in response")
            throw APIError.noData
        }
        
        // Store tokens
        await storeAuthTokens(authResponse)
        
        // Update state
        currentUser = authResponse.user
        authStateSubject.send(.authenticated(authResponse.user))
        
        logger.info("Registration successful for user: \(authResponse.user.id)")
        
        return authResponse
    }
    
    func login(
        email: String,
        password: String,
        rememberMe: Bool
    ) async throws -> AuthResponse {
        logger.info("Logging in user: \(email)")
        
        let request = LoginRequest(
            email: email,
            password: password,
            rememberMe: rememberMe
        )
        
        let response: APIResponse<AuthResponse> = try await apiClient.post(
            endpoint: "/api/auth/login",
            body: request
        )
        
        guard let authResponse = response.data else {
            logger.error("Login failed: No data in response")
            throw APIError.noData
        }
        
        // Store tokens
        await storeAuthTokens(authResponse)
        
        // Update state
        currentUser = authResponse.user
        authStateSubject.send(.authenticated(authResponse.user))
        
        logger.info("Login successful for user: \(authResponse.user.id)")
        
        return authResponse
    }
    
    func logout() async throws {
        logger.info("Logging out user")
        
        do {
            let _: APIResponse<EmptyResponse> = try await apiClient.post(
                endpoint: "/api/auth/logout",
                body: EmptyRequest()
            )
        } catch {
            // Log error but continue with local logout
            logger.warning("Server logout failed: \(error.localizedDescription)")
        }
        
        // Clear local state
        await clearAuthState()
        
        logger.info("Logout complete")
    }
    
    func refreshToken() async throws -> AuthResponse {
        logger.info("Refreshing auth token")
        
        let authResponse = try await apiClient.refreshAuthToken()
        
        // Store new tokens
        await storeAuthTokens(authResponse)
        
        // Update user
        currentUser = authResponse.user
        
        logger.info("Token refresh successful")
        
        return authResponse
    }
    
    func forgotPassword(email: String) async throws {
        logger.info("Requesting password reset for: \(email)")
        
        let request = ForgotPasswordRequest(email: email)
        
        let _: APIResponse<EmptyResponse> = try await apiClient.post(
            endpoint: "/api/auth/forgot-password",
            body: request
        )
        
        logger.info("Password reset email sent")
    }
    
    func resetPassword(token: String, newPassword: String) async throws {
        logger.info("Resetting password with token")
        
        let request = ResetPasswordRequest(token: token, newPassword: newPassword)
        
        let _: APIResponse<EmptyResponse> = try await apiClient.post(
            endpoint: "/api/auth/reset-password",
            body: request
        )
        
        logger.info("Password reset successful")
    }
    
    func verifyEmail(token: String) async throws {
        logger.info("Verifying email with token")
        
        let request = VerifyEmailRequest(token: token)
        
        let _: APIResponse<EmptyResponse> = try await apiClient.post(
            endpoint: "/api/auth/verify-email",
            body: request
        )
        
        // Update user's verified status
        if var user = currentUser {
            user.isEmailVerified = true
            currentUser = user
            authStateSubject.send(.authenticated(user))
        }
        
        logger.info("Email verification successful")
    }
    
    func changePassword(currentPassword: String, newPassword: String) async throws {
        logger.info("Changing password")
        
        let request = ChangePasswordRequest(
            currentPassword: currentPassword,
            newPassword: newPassword
        )
        
        let _: APIResponse<EmptyResponse> = try await apiClient.post(
            endpoint: "/api/auth/change-password",
            body: request
        )
        
        logger.info("Password change successful")
    }
    
    func checkExistingSession() async -> Bool {
        logger.info("Checking for existing session")
        
        // Check if we have stored tokens
        guard let _ = try? keychainService.get(forKey: KeychainKeys.accessToken) else {
            logger.info("No stored access token found")
            authStateSubject.send(.unauthenticated)
            return false
        }
        
        // Try to get current user
        do {
            let response: APIResponse<User> = try await apiClient.get(
                endpoint: "/api/users/me",
                queryItems: nil
            )
            
            guard let user = response.data else {
                await clearAuthState()
                return false
            }
            
            currentUser = user
            authStateSubject.send(.authenticated(user))
            
            logger.info("Existing session restored for user: \(user.id)")
            return true
        } catch {
            logger.error("Failed to restore session: \(error.localizedDescription)")
            
            // Try to refresh token
            do {
                _ = try await refreshToken()
                return true
            } catch {
                await clearAuthState()
                return false
            }
        }
    }
    
    // MARK: - Private Methods
    
    private func storeAuthTokens(_ authResponse: AuthResponse) async {
        apiClient.setAuthToken(authResponse.accessToken)
        
        do {
            try keychainService.save(
                authResponse.refreshToken,
                forKey: KeychainKeys.refreshToken
            )
        } catch {
            logger.error("Failed to store refresh token: \(error.localizedDescription)")
        }
    }
    
    private func clearAuthState() async {
        currentUser = nil
        apiClient.setAuthToken(nil)
        
        do {
            try keychainService.delete(forKey: KeychainKeys.accessToken)
            try keychainService.delete(forKey: KeychainKeys.refreshToken)
        } catch {
            logger.warning("Error clearing keychain: \(error.localizedDescription)")
        }
        
        authStateSubject.send(.unauthenticated)
    }
}

// MARK: - Request Models

struct RegisterRequest: Encodable {
    let email: String
    let password: String
    let firstName: String?
    let lastName: String?
}

struct LoginRequest: Encodable {
    let email: String
    let password: String
    let rememberMe: Bool
}

struct ForgotPasswordRequest: Encodable {
    let email: String
}

struct ResetPasswordRequest: Encodable {
    let token: String
    let newPassword: String
}

struct VerifyEmailRequest: Encodable {
    let token: String
}

struct ChangePasswordRequest: Encodable {
    let currentPassword: String
    let newPassword: String
}

struct EmptyRequest: Encodable {}
