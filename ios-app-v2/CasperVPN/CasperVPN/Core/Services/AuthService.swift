//
//  AuthService.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//

import Foundation

/// Service for handling user authentication
final class AuthService: AuthServiceProtocol {
    
    // MARK: - Singleton
    static let shared = AuthService()
    
    // MARK: - Properties
    private let apiClient: APIClientProtocol
    private let keychainService: KeychainServiceProtocol
    private let logger = ConnectionLogger.shared
    
    // MARK: - Initialization
    private init(apiClient: APIClientProtocol = APIClient.shared,
                 keychainService: KeychainServiceProtocol = KeychainService.shared) {
        self.apiClient = apiClient
        self.keychainService = keychainService
        
        // Set token if available
        if let token = keychainService.getAccessToken() {
            apiClient.setAuthToken(token)
        }
    }
    
    // MARK: - Authentication Methods
    
    func login(email: String, password: String) async throws -> User {
        logger.log("Attempting login for: \(email)", level: .info)
        
        let request = LoginRequest(email: email, password: password)
        let response: LoginResponse = try await apiClient.request(
            Config.Endpoints.login,
            method: .POST,
            body: request,
            headers: nil
        )
        
        guard response.success, let data = response.data else {
            throw AuthError.loginFailed(response.message ?? "Login failed")
        }
        
        // Save tokens
        keychainService.saveAccessToken(data.accessToken)
        keychainService.saveRefreshToken(data.refreshToken)
        
        // Update API client with new token
        apiClient.setAuthToken(data.accessToken)
        
        logger.log("Login successful for: \(email)", level: .info)
        
        return data.user
    }
    
    func register(email: String, password: String, firstName: String?, lastName: String?) async throws -> User {
        logger.log("Attempting registration for: \(email)", level: .info)
        
        let request = RegisterRequest(
            email: email,
            password: password,
            firstName: firstName,
            lastName: lastName
        )
        
        let response: LoginResponse = try await apiClient.request(
            Config.Endpoints.register,
            method: .POST,
            body: request,
            headers: nil
        )
        
        guard response.success, let data = response.data else {
            throw AuthError.registrationFailed(response.message ?? "Registration failed")
        }
        
        // Save tokens
        keychainService.saveAccessToken(data.accessToken)
        keychainService.saveRefreshToken(data.refreshToken)
        
        // Update API client with new token
        apiClient.setAuthToken(data.accessToken)
        
        logger.log("Registration successful for: \(email)", level: .info)
        
        return data.user
    }
    
    func logout() async throws {
        logger.log("Logging out", level: .info)
        
        do {
            let _: EmptyResponse = try await apiClient.request(
                Config.Endpoints.logout,
                method: .POST,
                body: nil as Empty?,
                headers: nil
            )
        } catch {
            // Log error but continue with local logout
            logger.log("Logout API error: \(error.localizedDescription)", level: .warning)
        }
        
        // Clear local tokens
        keychainService.clearTokens()
        apiClient.setAuthToken(nil)
        
        logger.log("Logout complete", level: .info)
    }
    
    func refreshToken() async throws {
        logger.log("Refreshing token", level: .debug)
        
        guard let refreshToken = keychainService.getRefreshToken() else {
            throw AuthError.noRefreshToken
        }
        
        let request = RefreshTokenRequest(refreshToken: refreshToken)
        let response: TokenRefreshResponse = try await apiClient.request(
            Config.Endpoints.refreshToken,
            method: .POST,
            body: request,
            headers: nil
        )
        
        guard response.success, let data = response.data else {
            throw AuthError.tokenRefreshFailed(response.message ?? "Token refresh failed")
        }
        
        // Save new tokens
        keychainService.saveAccessToken(data.accessToken)
        if let newRefreshToken = data.refreshToken {
            keychainService.saveRefreshToken(newRefreshToken)
        }
        
        // Update API client with new token
        apiClient.setAuthToken(data.accessToken)
        
        logger.log("Token refreshed successfully", level: .debug)
    }
    
    func getCurrentUser() async throws -> User {
        let response: UserResponse = try await apiClient.request(
            Config.Endpoints.user,
            method: .GET,
            body: nil as Empty?,
            headers: nil
        )
        
        guard response.success, let user = response.data else {
            throw AuthError.userNotFound
        }
        
        return user
    }
}

// MARK: - Auth Error

enum AuthError: LocalizedError {
    case loginFailed(String)
    case registrationFailed(String)
    case tokenRefreshFailed(String)
    case noRefreshToken
    case userNotFound
    case invalidCredentials
    
    var errorDescription: String? {
        switch self {
        case .loginFailed(let message):
            return "Login failed: \(message)"
        case .registrationFailed(let message):
            return "Registration failed: \(message)"
        case .tokenRefreshFailed(let message):
            return "Token refresh failed: \(message)"
        case .noRefreshToken:
            return "No refresh token available"
        case .userNotFound:
            return "User not found"
        case .invalidCredentials:
            return "Invalid email or password"
        }
    }
}

// MARK: - Request Models

struct LoginRequest: Codable {
    let email: String
    let password: String
}

struct RegisterRequest: Codable {
    let email: String
    let password: String
    let firstName: String?
    let lastName: String?
}

struct RefreshTokenRequest: Codable {
    let refreshToken: String
}

private struct Empty: Encodable {}

struct EmptyResponse: Codable {
    let success: Bool
    let message: String?
}
