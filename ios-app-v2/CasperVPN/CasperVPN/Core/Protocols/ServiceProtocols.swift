//
//  ServiceProtocols.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//

import Foundation
import Combine
import NetworkExtension

// MARK: - Auth Service Protocol

/// Protocol for authentication operations
protocol AuthServiceProtocol {
    /// Login with email and password
    func login(email: String, password: String) async throws -> User
    /// Register a new user
    func register(email: String, password: String, firstName: String?, lastName: String?) async throws -> User
    /// Logout the current user
    func logout() async throws
    /// Refresh the authentication token
    func refreshToken() async throws
    /// Get the currently authenticated user
    func getCurrentUser() async throws -> User
}

// MARK: - Latency Service Protocol

/// Protocol for measuring server latency
protocol LatencyServiceProtocol {
    /// Measure latency to a single server using TCP connection
    /// - Parameter server: The VPN server to test
    /// - Returns: Latency in milliseconds, or nil if unreachable
    func measureLatency(to server: VPNServer) async -> Int?
    
    /// Measure latency to multiple servers
    /// - Parameter servers: Array of VPN servers to test
    /// - Returns: Dictionary mapping server IDs to latency values
    func measureLatencyBatch(servers: [VPNServer]) async -> [String: Int]
    
    /// Measure latency to a specific host and port
    /// - Parameters:
    ///   - host: Hostname or IP address
    ///   - port: Port number
    /// - Returns: Latency in milliseconds, or nil if unreachable
    func measureLatency(host: String, port: Int) async -> Int?
}

// MARK: - Favorites Manager Protocol

/// Protocol for managing favorite servers
protocol FavoritesManagerProtocol {
    /// Publisher for favorites changes
    var favoritesPublisher: AnyPublisher<Set<String>, Never> { get }
    
    /// Add a server to favorites
    /// - Parameter serverId: The server ID to add
    func addFavorite(serverId: String)
    
    /// Remove a server from favorites
    /// - Parameter serverId: The server ID to remove
    func removeFavorite(serverId: String)
    
    /// Toggle the favorite status of a server
    /// - Parameter serverId: The server ID to toggle
    func toggleFavorite(serverId: String)
    
    /// Check if a server is a favorite
    /// - Parameter serverId: The server ID to check
    /// - Returns: True if the server is a favorite
    func isFavorite(serverId: String) -> Bool
    
    /// Get all favorite server IDs
    /// - Returns: Set of favorite server IDs
    func getAllFavorites() -> Set<String>
}

// MARK: - Recent Servers Manager Protocol

/// Protocol for managing recently connected servers
protocol RecentServersManagerProtocol {
    /// Publisher for recent servers changes
    var recentServersPublisher: AnyPublisher<[String], Never> { get }
    
    /// Add a server to recent connections (maintains max 5)
    /// - Parameter serverId: The server ID to add
    func addRecentServer(serverId: String)
    
    /// Get all recent server IDs (most recent first)
    /// - Returns: Array of recent server IDs
    func getRecentServers() -> [String]
    
    /// Clear all recent servers
    func clearRecentServers()
}

// MARK: - Keychain Service Protocol
protocol KeychainServiceProtocol {
    func saveAccessToken(_ token: String)
    func getAccessToken() -> String?
    func saveRefreshToken(_ token: String)
    func getRefreshToken() -> String?
    func clearTokens()
    
    func saveVPNConfig(_ config: VPNConfig, forServer serverId: String) throws
    func getVPNConfig(forServer serverId: String) throws -> VPNConfig?
    func deleteVPNConfig(forServer serverId: String) throws
}

// MARK: - VPN Service Protocol
protocol VPNServiceProtocol {
    var connectionStatePublisher: AnyPublisher<ConnectionState, Never> { get }
    var currentState: ConnectionState { get }
    
    func connect(to server: VPNServer) async throws
    func disconnect() async throws
    func getConnectionStatus() -> ConnectionState
}

// MARK: - Server Service Protocol
protocol ServerServiceProtocol {
    func fetchServers() async throws -> [VPNServer]
    func fetchServer(id: String) async throws -> VPNServer
    func fetchServerConfig(serverId: String) async throws -> VPNConfig
    func logConnection(serverId: String) async throws
    func logDisconnection(serverId: String, duration: TimeInterval?, bytesReceived: Int64?, bytesSent: Int64?) async throws
}

// MARK: - API Client Protocol
protocol APIClientProtocol {
    func request<T: Decodable>(_ endpoint: String, method: HTTPMethod, body: Encodable?, headers: [String: String]?) async throws -> T
    func setAuthToken(_ token: String?)
}

// MARK: - HTTP Method
enum HTTPMethod: String {
    case GET
    case POST
    case PUT
    case DELETE
    case PATCH
}

// MARK: - VPN Connection Manager Protocol
protocol VPNConnectionManagerProtocol: ObservableObject {
    var connectionState: ConnectionState { get }
    var connectedServer: VPNServer? { get }
    var statistics: ConnectionStatistics { get }
    var lastError: VPNError? { get }
    
    func initialize() async
    func connect(to server: VPNServer) async throws
    func disconnect() async throws
    func refreshStatus() async
}

// MARK: - WireGuard Manager Protocol
protocol WireGuardManagerProtocol {
    func generateKeyPair() throws -> (privateKey: String, publicKey: String)
    func validateKey(_ key: String) -> Bool
    func parseConfiguration(_ config: VPNConfig) throws -> Data
}

// MARK: - Kill Switch Manager Protocol
protocol KillSwitchManagerProtocol {
    var isEnabled: Bool { get }
    
    func enable() async throws
    func disable() async throws
    func configureOnDemandRules() -> [NEOnDemandRule]
}

// MARK: - Network Monitor Protocol
protocol NetworkMonitorProtocol {
    var isConnected: Bool { get }
    var connectionType: NetworkConnectionType { get }
    var pathUpdatePublisher: AnyPublisher<NetworkStatus, Never> { get }
    
    func startMonitoring()
    func stopMonitoring()
}

// MARK: - Network Types
enum NetworkConnectionType {
    case wifi
    case cellular
    case wiredEthernet
    case other
    case none
}

struct NetworkStatus {
    let isConnected: Bool
    let connectionType: NetworkConnectionType
    let isExpensive: Bool
    let isConstrained: Bool
}

// MARK: - Connection Logger Protocol
protocol ConnectionLoggerProtocol {
    func log(_ message: String, level: LogLevel)
    func logConnectionAttempt(server: VPNServer)
    func logConnectionSuccess(server: VPNServer)
    func logConnectionFailure(server: VPNServer, error: VPNError)
    func logDisconnection(reason: String?)
    func getRecentLogs(count: Int) -> [ConnectionLogEntry]
    func exportLogs() -> String
    func clearLogs()
}

// MARK: - Connection Log Entry
struct ConnectionLogEntry: Codable, Identifiable {
    let id: UUID
    let timestamp: Date
    let level: LogLevel
    let message: String
    let metadata: [String: String]?
    
    init(level: LogLevel, message: String, metadata: [String: String]? = nil) {
        self.id = UUID()
        self.timestamp = Date()
        self.level = level
        self.message = message
        self.metadata = metadata
    }
}
