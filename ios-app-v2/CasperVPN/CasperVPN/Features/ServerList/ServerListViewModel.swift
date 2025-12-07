//
//  ServerListViewModel.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//

import Foundation
import Combine

/// ViewModel for the server list feature.
///
/// This ViewModel manages:
/// - Server list fetching and caching
/// - Latency testing
/// - Favorites management
/// - Recent servers tracking
/// - Filtering and sorting
/// - Quick connect functionality
@MainActor
final class ServerListViewModel: ObservableObject {
    
    // MARK: - Published Properties
    
    /// All fetched servers
    @Published private(set) var servers: [VPNServer] = []
    
    /// Filtered and sorted servers for display
    @Published private(set) var filteredServers: [VPNServer] = []
    
    /// Servers grouped by country
    @Published private(set) var serversByCountry: [String: [VPNServer]] = [:]
    
    /// Favorite servers
    @Published private(set) var favoriteServers: [VPNServer] = []
    
    /// Recently connected servers
    @Published private(set) var recentServers: [VPNServer] = []
    
    /// Server latencies (server ID -> latency in ms)
    @Published private(set) var serverLatencies: [String: Int] = [:]
    
    /// Loading state
    @Published private(set) var isLoading: Bool = false
    
    /// Latency testing state
    @Published private(set) var isTestingLatency: Bool = false
    
    /// Error message
    @Published private(set) var error: String?
    
    /// Whether to show error alert
    @Published var showError: Bool = false
    
    /// Search text for filtering
    @Published var searchText: String = "" {
        didSet {
            applyFiltersAndSort()
        }
    }
    
    /// Current sort option
    @Published var sortOption: ServerSortOption = .name {
        didSet {
            applyFiltersAndSort()
        }
    }
    
    /// Sort direction (true = ascending)
    @Published var sortAscending: Bool = true {
        didSet {
            applyFiltersAndSort()
        }
    }
    
    /// Current filter options
    @Published var filterOptions: ServerFilterOptions = ServerFilterOptions() {
        didSet {
            applyFiltersAndSort()
        }
    }
    
    /// Favorite server IDs
    @Published private(set) var favoriteIds: Set<String> = []
    
    // MARK: - Dependencies
    
    private let serverService: ServerServiceProtocol
    private let latencyService: LatencyServiceProtocol
    private let favoritesManager: FavoritesManagerProtocol
    private let recentServersManager: RecentServersManagerProtocol
    private var cancellables = Set<AnyCancellable>()
    
    // MARK: - Initialization
    
    init(
        serverService: ServerServiceProtocol = ServerService.shared,
        latencyService: LatencyServiceProtocol = LatencyService.shared,
        favoritesManager: FavoritesManagerProtocol = FavoritesManager.shared,
        recentServersManager: RecentServersManagerProtocol = RecentServersManager.shared
    ) {
        self.serverService = serverService
        self.latencyService = latencyService
        self.favoritesManager = favoritesManager
        self.recentServersManager = recentServersManager
        
        setupSubscriptions()
    }
    
    // MARK: - Setup
    
    private func setupSubscriptions() {
        // Subscribe to favorites changes
        favoritesManager.favoritesPublisher
            .receive(on: DispatchQueue.main)
            .sink { [weak self] favorites in
                self?.favoriteIds = favorites
                self?.updateFavoriteServers()
            }
            .store(in: &cancellables)
        
        // Subscribe to recent servers changes
        recentServersManager.recentServersPublisher
            .receive(on: DispatchQueue.main)
            .sink { [weak self] _ in
                self?.updateRecentServers()
            }
            .store(in: &cancellables)
        
        // Initial load of favorites
        favoriteIds = favoritesManager.getAllFavorites()
    }
    
    // MARK: - Public Methods
    
    /// Load servers from the API
    func loadServers() async {
        isLoading = true
        error = nil
        
        do {
            let fetchedServers = try await serverService.fetchServers()
            servers = fetchedServers
            serversByCountry = Dictionary(grouping: servers) { $0.country }
            
            updateFavoriteServers()
            updateRecentServers()
            applyFiltersAndSort()
        } catch {
            self.error = error.localizedDescription
            showError = true
        }
        
        isLoading = false
    }
    
    /// Refresh servers (clears cache)
    func refresh() async {
        ServerService.shared.clearCache()
        await loadServers()
    }
    
    /// Test latency for all servers
    func testAllLatencies() async {
        guard !isTestingLatency else { return }
        
        isTestingLatency = true
        
        // Test latencies in batches to avoid overwhelming the network
        let latencies = await latencyService.measureLatencyBatch(servers: servers)
        serverLatencies = latencies
        
        // Update servers with measured latencies
        updateServersWithLatency()
        
        isTestingLatency = false
    }
    
    /// Test latency for a single server
    func testLatency(for server: VPNServer) async -> Int? {
        let latency = await latencyService.measureLatency(to: server)
        
        if let latency = latency {
            serverLatencies[server.id] = latency
            updateServersWithLatency()
        }
        
        return latency
    }
    
    /// Toggle favorite status for a server
    func toggleFavorite(serverId: String) {
        favoritesManager.toggleFavorite(serverId: serverId)
    }
    
    /// Check if a server is a favorite
    func isFavorite(serverId: String) -> Bool {
        favoriteIds.contains(serverId)
    }
    
    /// Add server to recent connections
    func addToRecentServers(serverId: String) {
        recentServersManager.addRecentServer(serverId: serverId)
    }
    
    /// Get available countries for filtering
    func getCountries() -> [String] {
        Array(serversByCountry.keys).sorted()
    }
    
    /// Get latency for a specific server
    func getLatency(for serverId: String) -> Int? {
        serverLatencies[serverId]
    }
    
    // MARK: - Quick Connect
    
    /// Get the recommended server for quick connect
    ///
    /// The algorithm considers:
    /// 1. Recently connected servers (prioritize for consistency)
    /// 2. Favorite servers (user preference)
    /// 3. Server load (prefer lower load)
    /// 4. Latency (prefer lower latency)
    /// 5. Online status (must be online)
    /// 6. Premium status (consider user subscription)
    func getRecommendedServer() -> VPNServer? {
        // Filter online servers only
        let onlineServers = servers.filter { $0.isOnline }
        
        guard !onlineServers.isEmpty else { return nil }
        
        // First, check recent servers - if the most recent is good, use it
        if let mostRecentId = recentServersManager.getMostRecentServerId(),
           let recentServer = onlineServers.first(where: { $0.id == mostRecentId }),
           recentServer.load < 70 {
            return recentServer
        }
        
        // Score each server
        let scoredServers = onlineServers.map { server -> (VPNServer, Double) in
            var score: Double = 100.0
            
            // Bonus for favorites
            if favoriteIds.contains(server.id) {
                score += 20.0
            }
            
            // Bonus for recent servers
            let recentIds = recentServersManager.getRecentServers()
            if let recentIndex = recentIds.firstIndex(of: server.id) {
                score += Double(10 - recentIndex * 2) // 10 for most recent, decreasing
            }
            
            // Penalty for high load
            score -= Double(server.load) * 0.5
            
            // Penalty for high latency
            if let latency = serverLatencies[server.id] ?? server.latency {
                score -= Double(latency) * 0.1
            } else {
                // Unknown latency - slight penalty
                score -= 5.0
            }
            
            return (server, score)
        }
        
        // Sort by score (descending) and return the best
        let sorted = scoredServers.sorted { $0.1 > $1.1 }
        return sorted.first?.0
    }
    
    /// Quick connect to the best available server
    /// Returns the recommended server for connection
    func quickConnect() -> VPNServer? {
        guard let server = getRecommendedServer() else {
            error = "No servers available for connection"
            showError = true
            return nil
        }
        
        // Add to recent servers when connecting
        addToRecentServers(serverId: server.id)
        
        return server
    }
    
    // MARK: - Private Methods
    
    /// Apply current filters and sort options
    private func applyFiltersAndSort() {
        var result = servers
        
        // Apply search filter
        if !searchText.isEmpty {
            result = result.filter { server in
                server.name.localizedCaseInsensitiveContains(searchText) ||
                server.country.localizedCaseInsensitiveContains(searchText) ||
                server.city.localizedCaseInsensitiveContains(searchText)
            }
        }
        
        // Apply premium filter
        if filterOptions.premiumOnly {
            result = result.filter { $0.isPremium }
        }
        
        // Apply online filter
        if filterOptions.onlineOnly {
            result = result.filter { $0.isOnline }
        }
        
        // Apply feature filters
        if !filterOptions.selectedFeatures.isEmpty {
            result = result.filter { server in
                guard let features = server.features else { return false }
                return !filterOptions.selectedFeatures.isDisjoint(with: Set(features))
            }
        }
        
        // Apply country filters
        if !filterOptions.selectedCountries.isEmpty {
            result = result.filter { filterOptions.selectedCountries.contains($0.country) }
        }
        
        // Apply sorting
        result = sortServers(result)
        
        filteredServers = result
    }
    
    /// Sort servers based on current sort option
    private func sortServers(_ servers: [VPNServer]) -> [VPNServer] {
        let sorted: [VPNServer]
        
        switch sortOption {
        case .name:
            sorted = servers.sorted { $0.name.localizedCompare($1.name) == .orderedAscending }
            
        case .latency:
            sorted = servers.sorted { server1, server2 in
                let latency1 = serverLatencies[server1.id] ?? server1.latency ?? Int.max
                let latency2 = serverLatencies[server2.id] ?? server2.latency ?? Int.max
                return latency1 < latency2
            }
            
        case .load:
            sorted = servers.sorted { $0.load < $1.load }
            
        case .country:
            sorted = servers.sorted {
                if $0.country == $1.country {
                    return $0.city.localizedCompare($1.city) == .orderedAscending
                }
                return $0.country.localizedCompare($1.country) == .orderedAscending
            }
        }
        
        return sortAscending ? sorted : sorted.reversed()
    }
    
    /// Update servers with measured latencies
    private func updateServersWithLatency() {
        // Create updated servers with latency values
        servers = servers.map { server in
            if let latency = serverLatencies[server.id] {
                // Create a new server with updated latency
                return VPNServer(
                    id: server.id,
                    name: server.name,
                    country: server.country,
                    city: server.city,
                    countryCode: server.countryCode,
                    hostname: server.hostname,
                    ipAddress: server.ipAddress,
                    port: server.port,
                    load: server.load,
                    isPremium: server.isPremium,
                    isOnline: server.isOnline,
                    features: server.features,
                    latency: latency
                )
            }
            return server
        }
        
        applyFiltersAndSort()
    }
    
    /// Update favorite servers list
    private func updateFavoriteServers() {
        favoriteServers = servers.filter { favoriteIds.contains($0.id) }
    }
    
    /// Update recent servers list
    private func updateRecentServers() {
        let recentIds = recentServersManager.getRecentServers()
        recentServers = recentIds.compactMap { id in
            servers.first { $0.id == id }
        }
    }
}
