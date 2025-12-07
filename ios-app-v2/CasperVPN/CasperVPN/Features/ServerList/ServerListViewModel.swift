//
//  ServerListViewModel.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//  Copyright © 2024 CasperVPN. All rights reserved.
//

import Foundation
import Combine

/// ViewModel for managing the server list and server selection.
@MainActor
final class ServerListViewModel: ObservableObject {
    
    // MARK: - Published Properties
    
    /// All available servers
    @Published private(set) var servers: [VPNServer] = []
    
    /// Servers grouped by country
    @Published private(set) var groupedServers: [ServerCountryGroup] = []
    
    /// Search query for filtering servers
    @Published var searchQuery = "" {
        didSet {
            updateFilteredServers()
        }
    }
    
    /// Current sort option
    @Published var sortOption: ServerSortOption = .recommended {
        didSet {
            updateFilteredServers()
        }
    }
    
    /// Whether data is currently loading
    @Published var isLoading = false
    
    /// Current error message
    @Published var errorMessage: String?
    
    /// Whether to show error alert
    @Published var showError = false
    
    // MARK: - Properties
    
    private let serverService: ServerServiceProtocol
    private var cancellables = Set<AnyCancellable>()
    
    /// Whether a search is active
    var isSearching: Bool {
        !searchQuery.isEmpty
    }
    
    // MARK: - Initialization
    
    init(serverService: ServerServiceProtocol = ServerService.shared) {
        self.serverService = serverService
        setupSearchDebounce()
    }
    
    // MARK: - Setup
    
    private func setupSearchDebounce() {
        $searchQuery
            .debounce(for: .milliseconds(300), scheduler: DispatchQueue.main)
            .removeDuplicates()
            .sink { [weak self] _ in
                self?.updateFilteredServers()
            }
            .store(in: &cancellables)
    }
    
    // MARK: - Public Methods
    
    /// Loads the server list
    func loadData() async {
        guard !isLoading else { return }
        
        isLoading = true
        clearError()
        
        do {
            let fetchedServers = try await serverService.fetchServers()
            servers = fetchedServers
            updateFilteredServers()
        } catch {
            handleError(error)
        }
        
        isLoading = false
    }
    
    /// Refreshes the server list
    func refresh() async {
        // Invalidate cache and reload
        if let service = serverService as? ServerService {
            service.invalidateCache()
        }
        await loadData()
    }
    
    /// Gets the recommended server
    func getRecommendedServer() async -> VPNServer? {
        do {
            return try await serverService.getRecommendedServer(request: nil)
        } catch {
            handleError(error)
            return nil
        }
    }
    
    /// Filters servers by country
    func filterByCountry(_ countryCode: String) -> [VPNServer] {
        servers.filter { $0.countryCode == countryCode }
    }
    
    /// Gets server statistics
    func getStatistics() -> ServerStatistics? {
        guard let service = serverService as? ServerService else { return nil }
        return service.getServerStatistics(from: servers)
    }
    
    // MARK: - Private Methods
    
    private func updateFilteredServers() {
        var filtered = servers
        
        // Apply search filter
        if !searchQuery.isEmpty {
            if let service = serverService as? ServerService {
                filtered = service.filterServers(servers, query: searchQuery)
            } else {
                let query = searchQuery.lowercased()
                filtered = servers.filter { server in
                    server.name.lowercased().contains(query) ||
                    server.country.lowercased().contains(query) ||
                    (server.city?.lowercased().contains(query) ?? false)
                }
            }
        }
        
        // Apply sorting
        if let service = serverService as? ServerService {
            filtered = service.sortServers(filtered, by: sortOption)
            groupedServers = service.groupServersByCountry(filtered)
        } else {
            // Fallback sorting
            switch sortOption {
            case .recommended:
                filtered.sort { $0.load < $1.load }
            case .name:
                filtered.sort { $0.name < $1.name }
            case .country:
                filtered.sort { $0.country < $1.country }
            case .load:
                filtered.sort { $0.load < $1.load }
            case .ping:
                filtered.sort { ($0.ping ?? Int.max) < ($1.ping ?? Int.max) }
            }
            
            // Group by country
            let grouped = Dictionary(grouping: filtered) { $0.countryCode }
            groupedServers = grouped.map { (code, servers) in
                ServerCountryGroup(
                    id: code,
                    country: servers.first?.country ?? code,
                    countryCode: code,
                    servers: servers
                )
            }.sorted { $0.country < $1.country }
        }
    }
    
    private func handleError(_ error: Error) {
        if let apiError = error as? APIError {
            errorMessage = apiError.errorDescription
        } else {
            errorMessage = error.localizedDescription
        }
        showError = true
    }
    
    private func clearError() {
        errorMessage = nil
        showError = false
    }
}
