//
//  RecentServersManager.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//

import Foundation
import Combine

/// Manager for tracking recently connected VPN servers.
///
/// This manager maintains a list of the last 5 servers the user connected to,
/// persisted in UserDefaults with reactive updates through Combine publishers.
///
/// Usage:
/// ```swift
/// RecentServersManager.shared.addRecentServer(serverId: "server-123")
/// let recentIds = RecentServersManager.shared.getRecentServers()
/// ```
final class RecentServersManager: RecentServersManagerProtocol {
    
    // MARK: - Singleton
    
    static let shared = RecentServersManager()
    
    // MARK: - Constants
    
    private let userDefaultsKey = "CasperVPN.RecentServers"
    
    /// Maximum number of recent servers to track
    static let maxRecentServers = 5
    
    // MARK: - Properties
    
    /// Subject for publishing recent servers changes
    private let recentServersSubject = CurrentValueSubject<[String], Never>([])
    
    /// Publisher for recent servers changes
    var recentServersPublisher: AnyPublisher<[String], Never> {
        recentServersSubject.eraseToAnyPublisher()
    }
    
    /// Current recent server IDs (most recent first)
    private var recentServerIds: [String] {
        didSet {
            saveRecentServers()
            recentServersSubject.send(recentServerIds)
        }
    }
    
    private let userDefaults: UserDefaults
    private let logger = ConnectionLogger.shared
    
    // MARK: - Initialization
    
    private init(userDefaults: UserDefaults = .standard) {
        self.userDefaults = userDefaults
        self.recentServerIds = []
        loadRecentServers()
        logger.log("RecentServersManager initialized with \(recentServerIds.count) recent servers", level: .debug)
    }
    
    // MARK: - RecentServersManagerProtocol
    
    /// Add a server to recent connections
    /// - Parameter serverId: The server ID to add
    ///
    /// The server will be added to the front of the list. If it already exists,
    /// it will be moved to the front. The list is limited to `maxRecentServers`.
    func addRecentServer(serverId: String) {
        // Remove if already exists (to move to front)
        recentServerIds.removeAll { $0 == serverId }
        
        // Add to front
        recentServerIds.insert(serverId, at: 0)
        
        // Trim to max count
        if recentServerIds.count > Self.maxRecentServers {
            recentServerIds = Array(recentServerIds.prefix(Self.maxRecentServers))
        }
        
        logger.log("Added server \(serverId) to recent servers", level: .debug)
    }
    
    /// Get all recent server IDs (most recent first)
    /// - Returns: Array of recent server IDs
    func getRecentServers() -> [String] {
        recentServerIds
    }
    
    /// Clear all recent servers
    func clearRecentServers() {
        recentServerIds.removeAll()
        logger.log("Cleared all recent servers", level: .debug)
    }
    
    // MARK: - Private Methods
    
    /// Load recent servers from UserDefaults
    private func loadRecentServers() {
        if let savedIds = userDefaults.stringArray(forKey: userDefaultsKey) {
            // Ensure we don't exceed max count
            recentServerIds = Array(savedIds.prefix(Self.maxRecentServers))
        }
    }
    
    /// Save recent servers to UserDefaults
    private func saveRecentServers() {
        userDefaults.set(recentServerIds, forKey: userDefaultsKey)
    }
    
    // MARK: - Utility Methods
    
    /// Get the most recently connected server ID
    /// - Returns: The most recent server ID, or nil if none
    func getMostRecentServerId() -> String? {
        recentServerIds.first
    }
    
    /// Check if a server is in recent connections
    /// - Parameter serverId: The server ID to check
    /// - Returns: True if the server is in recent connections
    func isRecent(serverId: String) -> Bool {
        recentServerIds.contains(serverId)
    }
    
    /// Get recent servers from a list of servers
    /// - Parameter servers: List of all servers
    /// - Returns: Array of recent servers in order (most recent first)
    func getRecentServers(from servers: [VPNServer]) -> [VPNServer] {
        recentServerIds.compactMap { id in
            servers.first { $0.id == id }
        }
    }
    
    /// Remove a specific server from recent connections
    /// - Parameter serverId: The server ID to remove
    func removeRecentServer(serverId: String) {
        recentServerIds.removeAll { $0 == serverId }
        logger.log("Removed server \(serverId) from recent servers", level: .debug)
    }
}
