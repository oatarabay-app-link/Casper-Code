//
//  FavoritesManager.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//

import Foundation
import Combine

/// Manager for handling favorite VPN servers.
///
/// This manager persists favorite server IDs to UserDefaults and provides
/// reactive updates through Combine publishers.
///
/// Usage:
/// ```swift
/// FavoritesManager.shared.toggleFavorite(serverId: "server-123")
/// let isFavorite = FavoritesManager.shared.isFavorite(serverId: "server-123")
/// ```
final class FavoritesManager: FavoritesManagerProtocol {
    
    // MARK: - Singleton
    
    static let shared = FavoritesManager()
    
    // MARK: - UserDefaults Key
    
    private let userDefaultsKey = "CasperVPN.FavoriteServers"
    
    // MARK: - Properties
    
    /// Subject for publishing favorites changes
    private let favoritesSubject = CurrentValueSubject<Set<String>, Never>([])
    
    /// Publisher for favorites changes
    var favoritesPublisher: AnyPublisher<Set<String>, Never> {
        favoritesSubject.eraseToAnyPublisher()
    }
    
    /// Current favorite server IDs
    private var favoriteIds: Set<String> {
        didSet {
            saveFavorites()
            favoritesSubject.send(favoriteIds)
        }
    }
    
    private let userDefaults: UserDefaults
    private let logger = ConnectionLogger.shared
    
    // MARK: - Initialization
    
    private init(userDefaults: UserDefaults = .standard) {
        self.userDefaults = userDefaults
        self.favoriteIds = []
        loadFavorites()
        logger.log("FavoritesManager initialized with \(favoriteIds.count) favorites", level: .debug)
    }
    
    // MARK: - FavoritesManagerProtocol
    
    /// Add a server to favorites
    /// - Parameter serverId: The server ID to add
    func addFavorite(serverId: String) {
        guard !favoriteIds.contains(serverId) else { return }
        
        favoriteIds.insert(serverId)
        logger.log("Added server \(serverId) to favorites", level: .debug)
    }
    
    /// Remove a server from favorites
    /// - Parameter serverId: The server ID to remove
    func removeFavorite(serverId: String) {
        guard favoriteIds.contains(serverId) else { return }
        
        favoriteIds.remove(serverId)
        logger.log("Removed server \(serverId) from favorites", level: .debug)
    }
    
    /// Toggle the favorite status of a server
    /// - Parameter serverId: The server ID to toggle
    func toggleFavorite(serverId: String) {
        if favoriteIds.contains(serverId) {
            removeFavorite(serverId: serverId)
        } else {
            addFavorite(serverId: serverId)
        }
    }
    
    /// Check if a server is a favorite
    /// - Parameter serverId: The server ID to check
    /// - Returns: True if the server is a favorite
    func isFavorite(serverId: String) -> Bool {
        favoriteIds.contains(serverId)
    }
    
    /// Get all favorite server IDs
    /// - Returns: Set of favorite server IDs
    func getAllFavorites() -> Set<String> {
        favoriteIds
    }
    
    // MARK: - Private Methods
    
    /// Load favorites from UserDefaults
    private func loadFavorites() {
        if let savedIds = userDefaults.stringArray(forKey: userDefaultsKey) {
            favoriteIds = Set(savedIds)
        }
    }
    
    /// Save favorites to UserDefaults
    private func saveFavorites() {
        let idsArray = Array(favoriteIds)
        userDefaults.set(idsArray, forKey: userDefaultsKey)
    }
    
    // MARK: - Utility Methods
    
    /// Clear all favorites
    func clearAllFavorites() {
        favoriteIds.removeAll()
        logger.log("Cleared all favorites", level: .debug)
    }
    
    /// Get favorite servers from a list of servers
    /// - Parameter servers: List of all servers
    /// - Returns: Array of favorite servers
    func getFavoriteServers(from servers: [VPNServer]) -> [VPNServer] {
        servers.filter { favoriteIds.contains($0.id) }
    }
}
