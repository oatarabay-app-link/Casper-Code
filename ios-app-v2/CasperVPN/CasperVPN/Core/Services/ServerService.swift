//
//  ServerService.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//  Copyright © 2024 CasperVPN. All rights reserved.
//

import Foundation
import CoreLocation

/// Service responsible for fetching and managing VPN server data.
final class ServerService: ServerServiceProtocol {
    
    // MARK: - Singleton
    
    static let shared = ServerService()
    
    // MARK: - Properties
    
    private let apiClient: APIClientProtocol
    private let logger = AppLogger.shared
    
    /// Cache for servers
    private var serverCache: [VPNServer] = []
    
    /// Last cache update time
    private var lastCacheUpdate: Date?
    
    /// Cache duration in seconds
    private let cacheDuration: TimeInterval = 300 // 5 minutes
    
    // MARK: - Initialization
    
    init(apiClient: APIClientProtocol = APIClient.shared) {
        self.apiClient = apiClient
    }
    
    // MARK: - Public Methods
    
    func fetchServers() async throws -> [VPNServer] {
        // Check cache
        if let cached = getCachedServers() {
            logger.debug("Returning cached servers")
            return cached
        }
        
        logger.info("Fetching servers from API")
        
        let response: APIResponse<[VPNServer]> = try await apiClient.get(
            endpoint: "/api/servers",
            queryItems: nil
        )
        
        guard let servers = response.data else {
            logger.error("No servers in response")
            throw APIError.noData
        }
        
        // Update cache
        serverCache = servers
        lastCacheUpdate = Date()
        
        logger.info("Fetched \(servers.count) servers")
        
        return servers
    }
    
    func getServer(id: UUID) async throws -> VPNServer {
        logger.info("Fetching server: \(id)")
        
        // Check cache first
        if let cachedServer = serverCache.first(where: { $0.id == id }) {
            logger.debug("Found server in cache")
            return cachedServer
        }
        
        let response: APIResponse<VPNServer> = try await apiClient.get(
            endpoint: "/api/servers/\(id.uuidString)",
            queryItems: nil
        )
        
        guard let server = response.data else {
            throw APIError.notFound
        }
        
        return server
    }
    
    func getServerConfig(for server: VPNServer) async throws -> VPNConfig {
        logger.info("Fetching config for server: \(server.name)")
        
        let response: APIResponse<VPNConfigResponse> = try await apiClient.get(
            endpoint: "/api/servers/\(server.id.uuidString)/config",
            queryItems: nil
        )
        
        guard let configResponse = response.data else {
            throw APIError.noData
        }
        
        return configResponse.toVPNConfig()
    }
    
    func getRecommendedServer(request: RecommendationRequest?) async throws -> VPNServer {
        logger.info("Getting recommended server")
        
        var queryItems: [URLQueryItem] = []
        
        if let request = request {
            if let lat = request.latitude {
                queryItems.append(URLQueryItem(name: "latitude", value: String(lat)))
            }
            if let lon = request.longitude {
                queryItems.append(URLQueryItem(name: "longitude", value: String(lon)))
            }
            if let country = request.preferredCountry {
                queryItems.append(URLQueryItem(name: "preferredCountry", value: country))
            }
            if let proto = request.preferredProtocol {
                queryItems.append(URLQueryItem(name: "preferredProtocol", value: proto.rawValue))
            }
        }
        
        let response: APIResponse<VPNServer> = try await apiClient.get(
            endpoint: "/api/servers/recommended",
            queryItems: queryItems.isEmpty ? nil : queryItems
        )
        
        guard let server = response.data else {
            // If no recommendation available, return first available from cache
            if let firstAvailable = serverCache.first(where: { $0.isAvailable }) {
                return firstAvailable
            }
            throw APIError.notFound
        }
        
        return server
    }
    
    func logConnection(to server: VPNServer, request: ConnectRequest) async throws -> ConnectionLog {
        logger.info("Logging connection to server: \(server.name)")
        
        let response: APIResponse<ConnectionLog> = try await apiClient.post(
            endpoint: "/api/servers/\(server.id.uuidString)/connect",
            body: request
        )
        
        guard let log = response.data else {
            throw APIError.noData
        }
        
        return log
    }
    
    func logDisconnection(from server: VPNServer, request: DisconnectRequest) async throws -> ConnectionLog {
        logger.info("Logging disconnection from server: \(server.name)")
        
        let response: APIResponse<ConnectionLog> = try await apiClient.post(
            endpoint: "/api/servers/\(server.id.uuidString)/disconnect",
            body: request
        )
        
        guard let log = response.data else {
            throw APIError.noData
        }
        
        return log
    }
    
    // MARK: - Server Filtering & Sorting
    
    /// Filters servers by search query
    func filterServers(_ servers: [VPNServer], query: String) -> [VPNServer] {
        guard !query.isEmpty else { return servers }
        
        let lowercaseQuery = query.lowercased()
        return servers.filter { server in
            server.name.lowercased().contains(lowercaseQuery) ||
            server.country.lowercased().contains(lowercaseQuery) ||
            (server.city?.lowercased().contains(lowercaseQuery) ?? false) ||
            server.countryCode.lowercased().contains(lowercaseQuery)
        }
    }
    
    /// Sorts servers by the specified option
    func sortServers(_ servers: [VPNServer], by option: ServerSortOption) -> [VPNServer] {
        switch option {
        case .recommended:
            // Sort by availability, then load, then ping
            return servers.sorted { lhs, rhs in
                if lhs.isAvailable != rhs.isAvailable {
                    return lhs.isAvailable
                }
                if lhs.load != rhs.load {
                    return lhs.load < rhs.load
                }
                return (lhs.ping ?? Int.max) < (rhs.ping ?? Int.max)
            }
        case .name:
            return servers.sorted { $0.name < $1.name }
        case .country:
            return servers.sorted { $0.country < $1.country }
        case .load:
            return servers.sorted { $0.load < $1.load }
        case .ping:
            return servers.sorted { ($0.ping ?? Int.max) < ($1.ping ?? Int.max) }
        }
    }
    
    /// Groups servers by country
    func groupServersByCountry(_ servers: [VPNServer]) -> [ServerCountryGroup] {
        let grouped = Dictionary(grouping: servers) { $0.countryCode }
        
        return grouped.map { (code, servers) in
            ServerCountryGroup(
                id: code,
                country: servers.first?.country ?? code,
                countryCode: code,
                servers: servers.sorted { $0.name < $1.name }
            )
        }.sorted { $0.country < $1.country }
    }
    
    /// Filters servers for premium users only
    func filterPremiumServers(_ servers: [VPNServer], isPremium: Bool) -> [VPNServer] {
        if isPremium {
            return servers
        }
        return servers.filter { !$0.isPremium }
    }
    
    // MARK: - Cache Management
    
    /// Invalidates the server cache
    func invalidateCache() {
        serverCache = []
        lastCacheUpdate = nil
        logger.debug("Server cache invalidated")
    }
    
    /// Gets cached servers if still valid
    private func getCachedServers() -> [VPNServer]? {
        guard !serverCache.isEmpty,
              let lastUpdate = lastCacheUpdate,
              Date().timeIntervalSince(lastUpdate) < cacheDuration else {
            return nil
        }
        return serverCache
    }
}

// MARK: - Location-based Server Selection

extension ServerService {
    
    /// Gets the nearest server based on user location
    func getNearestServer(
        from servers: [VPNServer],
        userLocation: CLLocationCoordinate2D
    ) -> VPNServer? {
        let availableServers = servers.filter { $0.isAvailable && $0.coordinate != nil }
        
        guard !availableServers.isEmpty else {
            return servers.first { $0.isAvailable }
        }
        
        return availableServers.min { lhs, rhs in
            guard let lhsCoord = lhs.coordinate,
                  let rhsCoord = rhs.coordinate else {
                return false
            }
            
            let lhsDistance = calculateDistance(from: userLocation, to: lhsCoord)
            let rhsDistance = calculateDistance(from: userLocation, to: rhsCoord)
            
            return lhsDistance < rhsDistance
        }
    }
    
    /// Calculates distance between two coordinates using Haversine formula
    private func calculateDistance(
        from coord1: CLLocationCoordinate2D,
        to coord2: CLLocationCoordinate2D
    ) -> Double {
        let lat1 = coord1.latitude * .pi / 180
        let lat2 = coord2.latitude * .pi / 180
        let lon1 = coord1.longitude * .pi / 180
        let lon2 = coord2.longitude * .pi / 180
        
        let dLat = lat2 - lat1
        let dLon = lon2 - lon1
        
        let a = sin(dLat / 2) * sin(dLat / 2) +
                cos(lat1) * cos(lat2) *
                sin(dLon / 2) * sin(dLon / 2)
        
        let c = 2 * atan2(sqrt(a), sqrt(1 - a))
        
        let earthRadius = 6371.0 // kilometers
        return earthRadius * c
    }
}

// MARK: - Server Statistics

extension ServerService {
    
    /// Gets statistics about the current server list
    func getServerStatistics(from servers: [VPNServer]) -> ServerStatistics {
        let available = servers.filter { $0.isAvailable }
        let premium = servers.filter { $0.isPremium }
        let countries = Set(servers.map { $0.countryCode })
        let avgLoad = servers.isEmpty ? 0 : servers.map { $0.load }.reduce(0, +) / servers.count
        let avgPing = servers.compactMap { $0.ping }.isEmpty ? nil :
            servers.compactMap { $0.ping }.reduce(0, +) / servers.compactMap { $0.ping }.count
        
        return ServerStatistics(
            totalServers: servers.count,
            availableServers: available.count,
            premiumServers: premium.count,
            countries: countries.count,
            averageLoad: avgLoad,
            averagePing: avgPing
        )
    }
}

/// Statistics about the server list
struct ServerStatistics {
    let totalServers: Int
    let availableServers: Int
    let premiumServers: Int
    let countries: Int
    let averageLoad: Int
    let averagePing: Int?
}
