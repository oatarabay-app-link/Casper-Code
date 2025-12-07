//
//  ServerListViewModel.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//

import Foundation
import Combine

@MainActor
final class ServerListViewModel: ObservableObject {
    
    // MARK: - Published Properties
    @Published private(set) var servers: [VPNServer] = []
    @Published private(set) var filteredServers: [VPNServer] = []
    @Published private(set) var serversByCountry: [String: [VPNServer]] = [:]
    @Published private(set) var isLoading: Bool = false
    @Published private(set) var error: String?
    @Published var searchText: String = "" {
        didSet {
            filterServers()
        }
    }
    @Published var showError: Bool = false
    
    // MARK: - Dependencies
    private let serverService: ServerServiceProtocol
    private var cancellables = Set<AnyCancellable>()
    
    // MARK: - Initialization
    init(serverService: ServerServiceProtocol = ServerService.shared) {
        self.serverService = serverService
    }
    
    // MARK: - Public Methods
    
    func loadServers() async {
        isLoading = true
        error = nil
        
        do {
            let fetchedServers = try await serverService.fetchServers()
            servers = fetchedServers.sorted { $0.country < $1.country }
            serversByCountry = Dictionary(grouping: servers) { $0.country }
            filterServers()
        } catch {
            self.error = error.localizedDescription
            showError = true
        }
        
        isLoading = false
    }
    
    func refresh() async {
        ServerService.shared.clearCache()
        await loadServers()
    }
    
    // MARK: - Private Methods
    
    private func filterServers() {
        if searchText.isEmpty {
            filteredServers = servers
        } else {
            filteredServers = servers.filter { server in
                server.name.localizedCaseInsensitiveContains(searchText) ||
                server.country.localizedCaseInsensitiveContains(searchText) ||
                server.city.localizedCaseInsensitiveContains(searchText)
            }
        }
    }
    
    func getCountries() -> [String] {
        return Array(serversByCountry.keys).sorted()
    }
}
