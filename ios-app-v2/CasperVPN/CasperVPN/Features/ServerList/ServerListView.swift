//
//  ServerListView.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//

import SwiftUI

/// The main view for displaying and managing the VPN server list.
///
/// Features:
/// - Favorites section at top
/// - Recent servers section
/// - Full server list with country grouping
/// - Search, filter, and sort functionality
/// - Pull-to-refresh
/// - Navigation to server details
struct ServerListView: View {
    
    @StateObject private var viewModel = ServerListViewModel()
    @Environment(\.dismiss) private var dismiss
    
    /// Callback when a server is selected
    var onSelect: ((VPNServer) -> Void)?
    
    /// State for showing filter sheet
    @State private var showFilterSheet = false
    
    /// State for selected server (for navigation)
    @State private var selectedServer: VPNServer?
    
    /// State for showing server detail sheet
    @State private var showServerDetail = false
    
    // MARK: - Body
    
    var body: some View {
        NavigationStack {
            ZStack {
                Theme.backgroundGradient
                    .ignoresSafeArea()
                
                if viewModel.isLoading && viewModel.servers.isEmpty {
                    loadingView
                } else if viewModel.servers.isEmpty {
                    emptyStateView
                } else {
                    serverListContent
                }
            }
            .navigationTitle("Servers")
            .navigationBarTitleDisplayMode(.inline)
            .searchable(text: $viewModel.searchText, prompt: "Search servers")
            .refreshable {
                await viewModel.refresh()
            }
            .toolbar {
                toolbarContent
            }
            .task {
                if viewModel.servers.isEmpty {
                    await viewModel.loadServers()
                    // Auto-test latencies after loading
                    await viewModel.testAllLatencies()
                }
            }
            .alert("Error", isPresented: $viewModel.showError) {
                Button("Retry") {
                    Task {
                        await viewModel.loadServers()
                    }
                }
                Button("OK", role: .cancel) {}
            } message: {
                if let error = viewModel.error {
                    Text(error)
                }
            }
            .sheet(isPresented: $showFilterSheet) {
                FilterSheet(
                    sortOption: $viewModel.sortOption,
                    sortAscending: $viewModel.sortAscending,
                    filterOptions: $viewModel.filterOptions,
                    availableCountries: viewModel.getCountries()
                )
                .presentationDetents([.medium, .large])
            }
            .sheet(isPresented: $showServerDetail) {
                if let server = selectedServer {
                    ServerDetailView(
                        server: server,
                        isFavorite: viewModel.isFavorite(serverId: server.id),
                        isConnected: false, // TODO: Connect to VPN connection manager
                        isConnecting: false,
                        latency: viewModel.getLatency(for: server.id),
                        onConnect: {
                            handleServerSelection(server)
                        },
                        onDisconnect: {
                            // TODO: Handle disconnect
                        },
                        onFavoriteToggle: {
                            viewModel.toggleFavorite(serverId: server.id)
                        }
                    )
                }
            }
        }
    }
    
    // MARK: - Loading View
    
    private var loadingView: some View {
        VStack(spacing: 16) {
            ProgressView()
                .tint(.white)
                .scaleEffect(1.5)
            
            Text("Loading servers...")
                .font(Theme.Fonts.subheadline)
                .foregroundColor(Theme.Colors.textSecondary)
        }
    }
    
    // MARK: - Empty State View
    
    private var emptyStateView: some View {
        EmptyStateView(
            icon: "server.rack",
            title: "No Servers Found",
            message: "Unable to load server list. Please check your connection and try again.",
            action: {
                Task {
                    await viewModel.loadServers()
                }
            },
            actionTitle: "Retry"
        )
    }
    
    // MARK: - Server List Content
    
    private var serverListContent: some View {
        List {
            // Quick Connect Section
            quickConnectSection
            
            // Favorites Section
            if !viewModel.favoriteServers.isEmpty {
                favoritesSection
            }
            
            // Recent Servers Section
            if !viewModel.recentServers.isEmpty {
                recentServersSection
            }
            
            // Filtered Results Header
            if viewModel.filterOptions.hasActiveFilters || !viewModel.searchText.isEmpty {
                filteredResultsHeader
            }
            
            // All Servers Section
            allServersSection
        }
        .scrollContentBackground(.hidden)
        .listStyle(.insetGrouped)
    }
    
    // MARK: - Quick Connect Section
    
    private var quickConnectSection: some View {
        Section {
            Button {
                if let server = viewModel.quickConnect() {
                    handleServerSelection(server)
                }
            } label: {
                HStack(spacing: 16) {
                    Image(systemName: "bolt.fill")
                        .font(.title2)
                        .foregroundColor(Theme.Colors.primary)
                        .frame(width: 40, height: 40)
                        .background(Theme.Colors.primary.opacity(0.2))
                        .cornerRadius(10)
                    
                    VStack(alignment: .leading, spacing: 4) {
                        Text("Quick Connect")
                            .font(Theme.Fonts.headline)
                            .foregroundColor(Theme.Colors.textPrimary)
                        
                        Text("Connect to the best available server")
                            .font(Theme.Fonts.caption)
                            .foregroundColor(Theme.Colors.textSecondary)
                    }
                    
                    Spacer()
                    
                    Image(systemName: "chevron.right")
                        .foregroundColor(Theme.Colors.textSecondary)
                }
                .padding(.vertical, 4)
            }
            .buttonStyle(.plain)
        }
        .listRowBackground(Theme.cardGradient)
    }
    
    // MARK: - Favorites Section
    
    private var favoritesSection: some View {
        Section {
            ForEach(viewModel.favoriteServers) { server in
                EnhancedServerRowView(
                    server: server,
                    isFavorite: true,
                    latency: viewModel.getLatency(for: server.id),
                    onTap: {
                        handleServerSelection(server)
                    },
                    onDetailTap: {
                        selectedServer = server
                        showServerDetail = true
                    },
                    onFavoriteTap: {
                        viewModel.toggleFavorite(serverId: server.id)
                    }
                )
            }
        } header: {
            HStack {
                Image(systemName: "heart.fill")
                    .foregroundColor(.red)
                Text("Favorites")
            }
        }
        .listRowBackground(Color.white.opacity(0.05))
    }
    
    // MARK: - Recent Servers Section
    
    private var recentServersSection: some View {
        Section {
            ForEach(viewModel.recentServers) { server in
                EnhancedServerRowView(
                    server: server,
                    isFavorite: viewModel.isFavorite(serverId: server.id),
                    latency: viewModel.getLatency(for: server.id),
                    onTap: {
                        handleServerSelection(server)
                    },
                    onDetailTap: {
                        selectedServer = server
                        showServerDetail = true
                    },
                    onFavoriteTap: {
                        viewModel.toggleFavorite(serverId: server.id)
                    }
                )
            }
        } header: {
            HStack {
                Image(systemName: "clock.arrow.circlepath")
                    .foregroundColor(Theme.Colors.primary)
                Text("Recent")
            }
        }
        .listRowBackground(Color.white.opacity(0.05))
    }
    
    // MARK: - Filtered Results Header
    
    private var filteredResultsHeader: some View {
        Section {
            HStack {
                Text("\(viewModel.filteredServers.count) servers found")
                    .font(Theme.Fonts.caption)
                    .foregroundColor(Theme.Colors.textSecondary)
                
                Spacer()
                
                if viewModel.filterOptions.hasActiveFilters {
                    Button {
                        viewModel.filterOptions.reset()
                    } label: {
                        Text("Clear Filters")
                            .font(Theme.Fonts.caption)
                            .foregroundColor(Theme.Colors.primary)
                    }
                }
            }
        }
        .listRowBackground(Color.clear)
    }
    
    // MARK: - All Servers Section
    
    private var allServersSection: some View {
        ForEach(viewModel.getCountries(), id: \.self) { country in
            let countryServers = filteredServersForCountry(country)
            
            if !countryServers.isEmpty {
                Section {
                    ForEach(countryServers) { server in
                        EnhancedServerRowView(
                            server: server,
                            isFavorite: viewModel.isFavorite(serverId: server.id),
                            latency: viewModel.getLatency(for: server.id),
                            onTap: {
                                handleServerSelection(server)
                            },
                            onDetailTap: {
                                selectedServer = server
                                showServerDetail = true
                            },
                            onFavoriteTap: {
                                viewModel.toggleFavorite(serverId: server.id)
                            }
                        )
                    }
                } header: {
                    if let firstServer = countryServers.first {
                        HStack {
                            Text(firstServer.flagEmoji)
                            Text(country)
                            Text("(\(countryServers.count))")
                                .foregroundColor(Theme.Colors.textSecondary)
                        }
                    }
                }
                .listRowBackground(Color.white.opacity(0.05))
            }
        }
    }
    
    // MARK: - Toolbar Content
    
    @ToolbarContentBuilder
    private var toolbarContent: some ToolbarContent {
        if onSelect != nil {
            ToolbarItem(placement: .navigationBarLeading) {
                Button("Cancel") {
                    dismiss()
                }
            }
        }
        
        ToolbarItem(placement: .navigationBarTrailing) {
            HStack(spacing: 16) {
                // Latency test button
                Button {
                    Task {
                        await viewModel.testAllLatencies()
                    }
                } label: {
                    if viewModel.isTestingLatency {
                        ProgressView()
                            .tint(.white)
                            .scaleEffect(0.8)
                    } else {
                        Image(systemName: "speedometer")
                    }
                }
                .disabled(viewModel.isTestingLatency)
                
                // Filter button
                Button {
                    showFilterSheet = true
                } label: {
                    ZStack(alignment: .topTrailing) {
                        Image(systemName: "line.3.horizontal.decrease.circle")
                        
                        if viewModel.filterOptions.hasActiveFilters {
                            Circle()
                                .fill(Theme.Colors.primary)
                                .frame(width: 8, height: 8)
                                .offset(x: 4, y: -4)
                        }
                    }
                }
            }
        }
        
        ToolbarItem(placement: .principal) {
            // Sort indicator
            if viewModel.sortOption != .name || !viewModel.sortAscending {
                HStack(spacing: 4) {
                    Text("Sort: \(viewModel.sortOption.rawValue)")
                        .font(Theme.Fonts.caption)
                        .foregroundColor(Theme.Colors.textSecondary)
                    
                    Image(systemName: viewModel.sortAscending ? "chevron.up" : "chevron.down")
                        .font(.system(size: 8))
                        .foregroundColor(Theme.Colors.textSecondary)
                }
            }
        }
    }
    
    // MARK: - Helper Methods
    
    /// Get filtered servers for a specific country
    private func filteredServersForCountry(_ country: String) -> [VPNServer] {
        viewModel.filteredServers.filter { $0.country == country }
    }
    
    /// Handle server selection
    private func handleServerSelection(_ server: VPNServer) {
        viewModel.addToRecentServers(serverId: server.id)
        
        if let onSelect = onSelect {
            onSelect(server)
            dismiss()
        }
    }
}

// MARK: - Enhanced Server Row View

/// An enhanced row view for displaying server information with additional actions.
struct EnhancedServerRowView: View {
    
    let server: VPNServer
    let isFavorite: Bool
    let latency: Int?
    let onTap: () -> Void
    let onDetailTap: () -> Void
    let onFavoriteTap: () -> Void
    
    var body: some View {
        HStack(spacing: 12) {
            // Main content (tappable for selection)
            Button(action: onTap) {
                HStack(spacing: 12) {
                    // Status indicator
                    Circle()
                        .fill(server.isOnline ? Theme.Colors.success : Theme.Colors.error)
                        .frame(width: 8, height: 8)
                    
                    // Server info
                    VStack(alignment: .leading, spacing: 4) {
                        HStack(spacing: 6) {
                            Text(server.name)
                                .font(Theme.Fonts.body)
                                .foregroundColor(Theme.Colors.textPrimary)
                            
                            if server.isPremium {
                                PremiumBadge(isCompact: true)
                            }
                        }
                        
                        Text(server.city)
                            .font(Theme.Fonts.caption)
                            .foregroundColor(Theme.Colors.textSecondary)
                        
                        // Features (if any)
                        if let features = server.features, !features.isEmpty {
                            FeatureBadgeRow(features: features, maxVisible: 2)
                        }
                    }
                    
                    Spacer()
                    
                    // Latency and load info
                    VStack(alignment: .trailing, spacing: 6) {
                        LatencyBadge(latency: latency ?? server.latency, isCompact: true)
                        
                        ServerLoadIndicator(load: server.load, showPercentage: false, barWidth: 40)
                    }
                }
                .contentShape(Rectangle())
            }
            .buttonStyle(.plain)
            
            // Action buttons
            VStack(spacing: 8) {
                // Favorite button
                Button(action: onFavoriteTap) {
                    Image(systemName: isFavorite ? "heart.fill" : "heart")
                        .font(.system(size: 14))
                        .foregroundColor(isFavorite ? .red : Theme.Colors.textSecondary)
                }
                .buttonStyle(.plain)
                
                // Info button (to show detail)
                Button(action: onDetailTap) {
                    Image(systemName: "info.circle")
                        .font(.system(size: 14))
                        .foregroundColor(Theme.Colors.textSecondary)
                }
                .buttonStyle(.plain)
            }
        }
        .padding(.vertical, 8)
        .opacity(server.isOnline ? 1.0 : 0.6)
    }
}

// MARK: - Legacy Load Indicator (kept for backwards compatibility)

/// A simple load indicator showing signal bars.
struct LoadIndicator: View {
    let load: Int
    
    var body: some View {
        HStack(spacing: 2) {
            ForEach(0..<3, id: \.self) { index in
                RoundedRectangle(cornerRadius: 1)
                    .fill(barColor(for: index))
                    .frame(width: 4, height: 8 + CGFloat(index * 2))
            }
        }
    }
    
    private func barColor(for index: Int) -> Color {
        let threshold = (index + 1) * 33
        
        if load < threshold {
            return Color.gray.opacity(0.3)
        }
        
        switch load {
        case 0..<30:
            return Theme.Colors.success
        case 30..<70:
            return Theme.Colors.warning
        default:
            return Theme.Colors.error
        }
    }
}

// MARK: - Preview

#if DEBUG
#Preview("Server List View") {
    ServerListView()
}
#endif
