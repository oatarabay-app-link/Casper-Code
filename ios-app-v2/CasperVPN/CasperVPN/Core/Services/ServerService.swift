//
//  ServerService.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//

import Foundation

/// Service for fetching VPN server information and configurations
final class ServerService: ServerServiceProtocol {
    
    // MARK: - Singleton
    static let shared = ServerService()
    
    // MARK: - Properties
    private let apiClient: APIClientProtocol
    private let logger = ConnectionLogger.shared
    
    // MARK: - Cache
    private var cachedServers: [VPNServer] = []
    private var lastFetchTime: Date?
    private let cacheExpiration: TimeInterval = 300 // 5 minutes
    
    // MARK: - Initialization
    private init(apiClient: APIClientProtocol = APIClient.shared) {
        self.apiClient = apiClient
    }
    
    // MARK: - Server Operations
    
    func fetchServers() async throws -> [VPNServer] {
        // Check cache
        if let lastFetch = lastFetchTime,
           Date().timeIntervalSince(lastFetch) < cacheExpiration,
           !cachedServers.isEmpty {
            logger.log("Returning cached servers", level: .debug)
            return cachedServers
        }
        
        logger.log("Fetching server list", level: .info)
        
        let response: ServersResponse = try await apiClient.get(Config.Endpoints.servers)
        
        guard response.success, let servers = response.data else {
            throw ServerServiceError.fetchFailed(response.message ?? "Failed to fetch servers")
        }
        
        // Update cache
        cachedServers = servers
        lastFetchTime = Date()
        
        logger.log("Fetched \(servers.count) servers", level: .info)
        
        return servers
    }
    
    func fetchServer(id: String) async throws -> VPNServer {
        logger.log("Fetching server: \(id)", level: .debug)
        
        let endpoint = "\(Config.Endpoints.servers)/\(id)"
        let response: ServerResponse = try await apiClient.get(endpoint)
        
        guard response.success, let server = response.data else {
            throw ServerServiceError.serverNotFound
        }
        
        return server
    }
    
    func fetchServerConfig(serverId: String) async throws -> VPNConfig {
        logger.log("Fetching configuration for server: \(serverId)", level: .info)
        
        let endpoint = Config.Endpoints.serverConfig(id: serverId)
        let response: VPNConfigResponse = try await apiClient.get(endpoint)
        
        guard response.success, let config = response.data else {
            throw VPNError.serverConfigFetchFailed(reason: response.message ?? "Failed to fetch server configuration")
        }
        
        // Validate configuration
        guard config.isValid else {
            throw VPNError.invalidConfiguration(reason: "Server returned invalid configuration")
        }
        
        logger.log("Server configuration fetched successfully", level: .debug)
        
        return config
    }
    
    // MARK: - Connection Logging
    
    func logConnection(serverId: String) async throws {
        logger.log("Logging connection start for server: \(serverId)", level: .debug)
        
        let endpoint = Config.Endpoints.serverConnect(id: serverId)
        let request = ConnectionLogRequest(
            serverId: serverId,
            connectedAt: Date(),
            disconnectedAt: nil,
            bytesReceived: nil,
            bytesSent: nil,
            duration: nil
        )
        
        let _: ConnectionLogResponse = try await apiClient.post(endpoint, body: request)
    }
    
    func logDisconnection(serverId: String, duration: TimeInterval?, bytesReceived: Int64?, bytesSent: Int64?) async throws {
        logger.log("Logging disconnection for server: \(serverId)", level: .debug)
        
        let endpoint = Config.Endpoints.serverDisconnect(id: serverId)
        let request = ConnectionLogRequest(
            serverId: serverId,
            connectedAt: nil,
            disconnectedAt: Date(),
            bytesReceived: bytesReceived,
            bytesSent: bytesSent,
            duration: duration
        )
        
        let _: ConnectionLogResponse = try await apiClient.post(endpoint, body: request)
    }
    
    // MARK: - Utility Methods
    
    func clearCache() {
        cachedServers = []
        lastFetchTime = nil
        logger.log("Server cache cleared", level: .debug)
    }
    
    func getRecommendedServer() async throws -> VPNServer? {
        let servers = try await fetchServers()
        
        // Filter online servers
        let onlineServers = servers.filter { $0.isOnline }
        
        guard !onlineServers.isEmpty else {
            return nil
        }
        
        // Sort by load and latency
        let sorted = onlineServers.sorted { server1, server2 in
            // Prefer lower load
            if server1.load != server2.load {
                return server1.load < server2.load
            }
            
            // Then prefer lower latency
            let latency1 = server1.latency ?? Int.max
            let latency2 = server2.latency ?? Int.max
            return latency1 < latency2
        }
        
        return sorted.first
    }
    
    func getServersByCountry() async throws -> [String: [VPNServer]] {
        let servers = try await fetchServers()
        return Dictionary(grouping: servers) { $0.country }
    }
}

// MARK: - Server Service Error

enum ServerServiceError: LocalizedError {
    case fetchFailed(String)
    case serverNotFound
    case configurationUnavailable
    
    var errorDescription: String? {
        switch self {
        case .fetchFailed(let message):
            return "Failed to fetch servers: \(message)"
        case .serverNotFound:
            return "Server not found"
        case .configurationUnavailable:
            return "Server configuration unavailable"
        }
    }
}
