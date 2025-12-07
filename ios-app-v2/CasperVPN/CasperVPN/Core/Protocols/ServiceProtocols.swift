//
//  ServiceProtocols.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//  Copyright © 2024 CasperVPN. All rights reserved.
//

import Foundation
import Combine
import NetworkExtension

// MARK: - API Client Protocol

/// Protocol defining the interface for HTTP API communication.
/// Implementations handle request building, authentication, and response parsing.
protocol APIClientProtocol {
    
    /// Performs an authenticated GET request
    /// - Parameters:
    ///   - endpoint: The API endpoint path
    ///   - queryItems: Optional query parameters
    /// - Returns: Decoded response of type T
    func get<T: Decodable>(endpoint: String, queryItems: [URLQueryItem]?) async throws -> T
    
    /// Performs an authenticated POST request
    /// - Parameters:
    ///   - endpoint: The API endpoint path
    ///   - body: The request body to encode
    /// - Returns: Decoded response of type T
    func post<T: Decodable, U: Encodable>(endpoint: String, body: U?) async throws -> T
    
    /// Performs an authenticated PUT request
    /// - Parameters:
    ///   - endpoint: The API endpoint path
    ///   - body: The request body to encode
    /// - Returns: Decoded response of type T
    func put<T: Decodable, U: Encodable>(endpoint: String, body: U?) async throws -> T
    
    /// Performs an authenticated DELETE request
    /// - Parameters:
    ///   - endpoint: The API endpoint path
    /// - Returns: Decoded response of type T
    func delete<T: Decodable>(endpoint: String) async throws -> T
    
    /// Registers a device token for push notifications
    /// - Parameter token: The device token string
    func registerDeviceToken(_ token: String) async throws
    
    /// Sets the authentication token
    /// - Parameter token: The JWT access token
    func setAuthToken(_ token: String?)
    
    /// Refreshes the authentication token
    /// - Returns: New auth response with fresh tokens
    func refreshAuthToken() async throws -> AuthResponse
}

// MARK: - Auth Service Protocol

/// Protocol defining authentication operations.
protocol AuthServiceProtocol {
    
    /// Current authenticated user, if any
    var currentUser: User? { get }
    
    /// Whether a user is currently authenticated
    var isAuthenticated: Bool { get }
    
    /// Publisher for authentication state changes
    var authStatePublisher: AnyPublisher<AuthState, Never> { get }
    
    /// Registers a new user account
    /// - Parameters:
    ///   - email: User's email address
    ///   - password: User's password
    ///   - firstName: User's first name (optional)
    ///   - lastName: User's last name (optional)
    /// - Returns: Authentication response with user and tokens
    func register(email: String, password: String, firstName: String?, lastName: String?) async throws -> AuthResponse
    
    /// Authenticates an existing user
    /// - Parameters:
    ///   - email: User's email address
    ///   - password: User's password
    ///   - rememberMe: Whether to persist the session
    /// - Returns: Authentication response with user and tokens
    func login(email: String, password: String, rememberMe: Bool) async throws -> AuthResponse
    
    /// Logs out the current user
    func logout() async throws
    
    /// Refreshes the authentication tokens
    /// - Returns: New authentication response
    func refreshToken() async throws -> AuthResponse
    
    /// Initiates password reset flow
    /// - Parameter email: User's email address
    func forgotPassword(email: String) async throws
    
    /// Resets password with token
    /// - Parameters:
    ///   - token: Reset token from email
    ///   - newPassword: New password
    func resetPassword(token: String, newPassword: String) async throws
    
    /// Verifies email with token
    /// - Parameter token: Verification token from email
    func verifyEmail(token: String) async throws
    
    /// Changes the user's password
    /// - Parameters:
    ///   - currentPassword: Current password
    ///   - newPassword: New password
    func changePassword(currentPassword: String, newPassword: String) async throws
    
    /// Checks if there's an existing valid session
    func checkExistingSession() async -> Bool
}

// MARK: - VPN Service Protocol

/// Protocol defining VPN connection management operations.
protocol VPNServiceProtocol {
    
    /// Current VPN connection status
    var connectionStatus: VPNConnectionStatus { get }
    
    /// Publisher for connection status changes
    var statusPublisher: AnyPublisher<VPNConnectionStatus, Never> { get }
    
    /// Current connection info, if connected
    var currentConnectionInfo: ConnectionInfo? { get }
    
    /// Currently selected server
    var selectedServer: VPNServer? { get }
    
    /// Initializes the VPN manager
    func initialize() async throws
    
    /// Connects to a VPN server
    /// - Parameter server: The server to connect to
    func connect(to server: VPNServer) async throws
    
    /// Disconnects from the current VPN server
    func disconnect() async throws
    
    /// Selects a server for connection
    /// - Parameter server: The server to select
    func selectServer(_ server: VPNServer)
    
    /// Gets the recommended server based on user location
    /// - Returns: Recommended server
    func getRecommendedServer() async throws -> VPNServer
    
    /// Refreshes the current connection status
    func refreshStatus() async
}

// MARK: - Keychain Service Protocol

/// Protocol defining secure storage operations.
protocol KeychainServiceProtocol {
    
    /// Saves a string value to the keychain
    /// - Parameters:
    ///   - value: The string to save
    ///   - key: The key to associate with the value
    func save(_ value: String, forKey key: String) throws
    
    /// Retrieves a string value from the keychain
    /// - Parameter key: The key to look up
    /// - Returns: The stored string, or nil if not found
    func get(forKey key: String) throws -> String?
    
    /// Deletes a value from the keychain
    /// - Parameter key: The key to delete
    func delete(forKey key: String) throws
    
    /// Saves data to the keychain
    /// - Parameters:
    ///   - data: The data to save
    ///   - key: The key to associate with the data
    func saveData(_ data: Data, forKey key: String) throws
    
    /// Retrieves data from the keychain
    /// - Parameter key: The key to look up
    /// - Returns: The stored data, or nil if not found
    func getData(forKey key: String) throws -> Data?
    
    /// Clears all stored keychain items for this app
    func clearAll() throws
}

// MARK: - Server Service Protocol

/// Protocol defining server-related operations.
protocol ServerServiceProtocol {
    
    /// Fetches the list of available servers
    /// - Returns: List of VPN servers
    func fetchServers() async throws -> [VPNServer]
    
    /// Gets details for a specific server
    /// - Parameter id: Server ID
    /// - Returns: Server details
    func getServer(id: UUID) async throws -> VPNServer
    
    /// Gets server configuration for connection
    /// - Parameter server: The server to get config for
    /// - Returns: VPN configuration
    func getServerConfig(for server: VPNServer) async throws -> VPNConfig
    
    /// Gets the recommended server
    /// - Parameter request: Recommendation criteria
    /// - Returns: Recommended server
    func getRecommendedServer(request: RecommendationRequest?) async throws -> VPNServer
    
    /// Logs a connection to a server
    /// - Parameters:
    ///   - server: The server being connected to
    ///   - request: Connection request details
    /// - Returns: Connection log entry
    func logConnection(to server: VPNServer, request: ConnectRequest) async throws -> ConnectionLog
    
    /// Logs a disconnection from a server
    /// - Parameters:
    ///   - server: The server being disconnected from
    ///   - request: Disconnection request details
    /// - Returns: Updated connection log entry
    func logDisconnection(from server: VPNServer, request: DisconnectRequest) async throws -> ConnectionLog
}

// MARK: - Subscription Service Protocol

/// Protocol defining subscription-related operations.
protocol SubscriptionServiceProtocol {
    
    /// Fetches available plans
    /// - Returns: List of plans
    func fetchPlans() async throws -> [Plan]
    
    /// Gets the current user's subscription
    /// - Returns: Current subscription, if any
    func getCurrentSubscription() async throws -> Subscription?
    
    /// Creates a checkout session for a plan
    /// - Parameters:
    ///   - planId: The plan to subscribe to
    ///   - interval: Billing interval
    /// - Returns: Checkout session response
    func createCheckoutSession(planId: UUID, interval: BillingInterval) async throws -> CheckoutSessionResponse
    
    /// Cancels the current subscription
    func cancelSubscription() async throws
    
    /// Gets the billing portal URL
    /// - Returns: Billing portal response with URL
    func getBillingPortal() async throws -> BillingPortalResponse
}

// MARK: - User Service Protocol

/// Protocol defining user-related operations.
protocol UserServiceProtocol {
    
    /// Gets the current user's profile
    /// - Returns: Current user
    func getCurrentUser() async throws -> User
    
    /// Updates the user's profile
    /// - Parameter request: Update request with new values
    /// - Returns: Updated user
    func updateProfile(request: UpdateProfileRequest) async throws -> User
    
    /// Deletes the user's account
    func deleteAccount() async throws
}

// MARK: - Auth State

/// Represents the authentication state of the app.
enum AuthState: Equatable {
    case unknown
    case authenticated(User)
    case unauthenticated
}

// MARK: - VPN Connection Status

/// Represents the current VPN connection status.
enum VPNConnectionStatus: Equatable {
    case disconnected
    case connecting
    case connected
    case disconnecting
    case reasserting
    case invalid
    
    /// Creates status from NEVPNStatus
    init(from neStatus: NEVPNStatus) {
        switch neStatus {
        case .invalid: self = .invalid
        case .disconnected: self = .disconnected
        case .connecting: self = .connecting
        case .connected: self = .connected
        case .reasserting: self = .reasserting
        case .disconnecting: self = .disconnecting
        @unknown default: self = .invalid
        }
    }
    
    /// Display name for the status
    var displayName: String {
        switch self {
        case .disconnected: return "Disconnected"
        case .connecting: return "Connecting..."
        case .connected: return "Connected"
        case .disconnecting: return "Disconnecting..."
        case .reasserting: return "Reconnecting..."
        case .invalid: return "Not Configured"
        }
    }
    
    /// Whether the VPN is currently active
    var isActive: Bool {
        switch self {
        case .connected, .connecting, .reasserting:
            return true
        default:
            return false
        }
    }
}

// MARK: - Auth Response

/// Response from authentication endpoints.
struct AuthResponse: Codable {
    let accessToken: String
    let refreshToken: String
    let expiresAt: Date
    let user: User
}

// MARK: - API Response

/// Generic API response wrapper.
struct APIResponse<T: Codable>: Codable {
    let success: Bool
    let message: String?
    let data: T?
    let errors: [String]?
    let timestamp: Date?
}

// MARK: - API Error

/// Errors that can occur during API communication.
enum APIError: LocalizedError {
    case invalidURL
    case invalidResponse
    case httpError(statusCode: Int, message: String?)
    case decodingError(Error)
    case encodingError(Error)
    case networkError(Error)
    case unauthorized
    case forbidden
    case notFound
    case serverError(String?)
    case tokenExpired
    case noData
    
    var errorDescription: String? {
        switch self {
        case .invalidURL:
            return "Invalid URL"
        case .invalidResponse:
            return "Invalid server response"
        case .httpError(let code, let message):
            return message ?? "HTTP error \(code)"
        case .decodingError:
            return "Failed to decode response"
        case .encodingError:
            return "Failed to encode request"
        case .networkError(let error):
            return "Network error: \(error.localizedDescription)"
        case .unauthorized:
            return "Authentication required"
        case .forbidden:
            return "Access denied"
        case .notFound:
            return "Resource not found"
        case .serverError(let message):
            return message ?? "Server error"
        case .tokenExpired:
            return "Session expired"
        case .noData:
            return "No data received"
        }
    }
}

// MARK: - Keychain Error

/// Errors that can occur during keychain operations.
enum KeychainError: LocalizedError {
    case itemNotFound
    case duplicateItem
    case invalidData
    case unexpectedStatus(OSStatus)
    
    var errorDescription: String? {
        switch self {
        case .itemNotFound:
            return "Keychain item not found"
        case .duplicateItem:
            return "Keychain item already exists"
        case .invalidData:
            return "Invalid keychain data"
        case .unexpectedStatus(let status):
            return "Keychain error: \(status)"
        }
    }
}

// MARK: - VPN Error

/// Errors that can occur during VPN operations.
enum VPNError: LocalizedError {
    case notConfigured
    case configurationFailed(String?)
    case connectionFailed(String?)
    case serverUnavailable
    case subscriptionRequired
    case dataLimitExceeded
    case networkUnavailable
    case tunnelError(Error)
    
    var errorDescription: String? {
        switch self {
        case .notConfigured:
            return "VPN is not configured"
        case .configurationFailed(let message):
            return message ?? "Failed to configure VPN"
        case .connectionFailed(let message):
            return message ?? "Failed to connect to VPN"
        case .serverUnavailable:
            return "Server is currently unavailable"
        case .subscriptionRequired:
            return "Subscription required for this server"
        case .dataLimitExceeded:
            return "Data limit exceeded"
        case .networkUnavailable:
            return "No network connection"
        case .tunnelError(let error):
            return "Tunnel error: \(error.localizedDescription)"
        }
    }
}
