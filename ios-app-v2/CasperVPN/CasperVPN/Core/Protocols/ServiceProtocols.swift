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
protocol AuthServiceProtocol {
    func login(email: String, password: String) async throws -> User
    func register(email: String, password: String, firstName: String?, lastName: String?) async throws -> User
    func logout() async throws
    func refreshToken() async throws
    func getCurrentUser() async throws -> User
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
