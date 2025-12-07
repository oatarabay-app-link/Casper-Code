//
//  ServerListView.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//  Copyright © 2024 CasperVPN. All rights reserved.
//

import SwiftUI

/// Displays the list of available VPN servers grouped by country.
struct ServerListView: View {
    
    // MARK: - Properties
    
    @StateObject private var viewModel = ServerListViewModel()
    @EnvironmentObject private var connectionViewModel: ConnectionViewModel
    
    @State private var selectedServer: VPNServer?
    @State private var showingConnectionSheet = false
    
    // MARK: - Body
    
    var body: some View {
        NavigationStack {
            ZStack {
                Theme.Colors.background.ignoresSafeArea()
                
                if viewModel.isLoading && viewModel.servers.isEmpty {
                    loadingView
                } else if viewModel.servers.isEmpty {
                    emptyView
                } else {
                    serverList
                }
            }
            .navigationTitle("Servers")
            .searchable(
                text: $viewModel.searchQuery,
                prompt: "Search servers or countries"
            )
            .toolbar {
                ToolbarItem(placement: .navigationBarTrailing) {
                    sortMenu
                }
            }
            .refreshable {
                await viewModel.refresh()
            }
            .alert("Error", isPresented: $viewModel.showError) {
                Button("OK", role: .cancel) {}
            } message: {
                Text(viewModel.errorMessage ?? "An error occurred")
            }
            .sheet(isPresented: $showingConnectionSheet) {
                if let server = selectedServer {
                    ServerConnectionSheet(server: server)
                }
            }
        }
        .task {
            await viewModel.loadData()
        }
    }
    
    // MARK: - Loading View
    
    private var loadingView: some View {
        VStack(spacing: 16) {
            ProgressView()
                .scaleEffect(1.5)
            
            Text("Loading servers...")
                .font(Theme.Fonts.body)
                .foregroundColor(Theme.Colors.textSecondary)
        }
    }
    
    // MARK: - Empty View
    
    private var emptyView: some View {
        VStack(spacing: 16) {
            Image(systemName: "server.rack")
                .font(.system(size: 64))
                .foregroundColor(Theme.Colors.textSecondary)
            
            Text("No Servers Available")
                .font(Theme.Fonts.headline)
                .foregroundColor(Theme.Colors.textPrimary)
            
            Text("Please check your connection and try again.")
                .font(Theme.Fonts.body)
                .foregroundColor(Theme.Colors.textSecondary)
                .multilineTextAlignment(.center)
            
            CasperButton(title: "Retry", style: .secondary) {
                Task {
                    await viewModel.refresh()
                }
            }
            .frame(width: 120)
        }
        .padding()
    }
    
    // MARK: - Server List
    
    private var serverList: some View {
        List {
            // Statistics header
            if !viewModel.isSearching {
                statisticsSection
            }
            
            // Servers grouped by country
            ForEach(viewModel.groupedServers) { group in
                Section {
                    ForEach(group.servers) { server in
                        ServerRowView(server: server) {
                            selectedServer = server
                            showingConnectionSheet = true
                        }
                    }
                } header: {
                    CountryHeader(group: group)
                }
            }
        }
        .listStyle(.insetGrouped)
    }
    
    // MARK: - Statistics Section
    
    private var statisticsSection: some View {
        Section {
            HStack(spacing: 20) {
                StatItem(
                    title: "Total",
                    value: "\(viewModel.servers.count)",
                    icon: "server.rack"
                )
                
                Divider()
                    .frame(height: 30)
                
                StatItem(
                    title: "Countries",
                    value: "\(viewModel.groupedServers.count)",
                    icon: "globe"
                )
                
                Divider()
                    .frame(height: 30)
                
                StatItem(
                    title: "Available",
                    value: "\(viewModel.servers.filter { $0.isAvailable }.count)",
                    icon: "checkmark.circle"
                )
            }
            .frame(maxWidth: .infinity)
            .padding(.vertical, 8)
        }
    }
    
    // MARK: - Sort Menu
    
    private var sortMenu: some View {
        Menu {
            ForEach(ServerSortOption.allCases, id: \.self) { option in
                Button {
                    viewModel.sortOption = option
                } label: {
                    HStack {
                        Text(option.displayName)
                        if viewModel.sortOption == option {
                            Image(systemName: "checkmark")
                        }
                    }
                }
            }
        } label: {
            Image(systemName: "arrow.up.arrow.down")
        }
    }
}

// MARK: - Country Header

struct CountryHeader: View {
    let group: ServerCountryGroup
    
    var body: some View {
        HStack(spacing: 8) {
            AsyncImage(url: group.flagUrl) { image in
                image
                    .resizable()
                    .aspectRatio(contentMode: .fit)
            } placeholder: {
                Text(group.countryCode)
                    .font(.caption)
            }
            .frame(width: 24, height: 16)
            .clipShape(RoundedRectangle(cornerRadius: 2))
            
            Text(group.country)
                .font(Theme.Fonts.headline)
            
            Spacer()
            
            Text("\(group.serverCount) server\(group.serverCount == 1 ? "" : "s")")
                .font(Theme.Fonts.caption)
                .foregroundColor(Theme.Colors.textSecondary)
        }
    }
}

// MARK: - Server Row View

struct ServerRowView: View {
    let server: VPNServer
    let onTap: () -> Void
    
    var body: some View {
        Button(action: onTap) {
            HStack(spacing: 12) {
                // Status indicator
                Circle()
                    .fill(statusColor)
                    .frame(width: 10, height: 10)
                
                // Server info
                VStack(alignment: .leading, spacing: 4) {
                    HStack {
                        Text(server.name)
                            .font(Theme.Fonts.body)
                            .foregroundColor(Theme.Colors.textPrimary)
                        
                        if server.isPremium {
                            Image(systemName: "star.fill")
                                .font(.caption)
                                .foregroundColor(.orange)
                        }
                    }
                    
                    if let city = server.city {
                        Text(city)
                            .font(Theme.Fonts.caption)
                            .foregroundColor(Theme.Colors.textSecondary)
                    }
                }
                
                Spacer()
                
                // Load and ping
                VStack(alignment: .trailing, spacing: 4) {
                    LoadBadge(load: server.load)
                    
                    if let ping = server.ping {
                        Text("\(ping) ms")
                            .font(Theme.Fonts.caption)
                            .foregroundColor(Theme.Colors.textSecondary)
                    }
                }
                
                Image(systemName: "chevron.right")
                    .font(.caption)
                    .foregroundColor(Theme.Colors.textSecondary)
            }
            .padding(.vertical, 4)
        }
        .buttonStyle(.plain)
        .opacity(server.isAvailable ? 1.0 : 0.5)
    }
    
    private var statusColor: Color {
        switch server.status {
        case .online: return Theme.Colors.success
        case .offline: return Theme.Colors.error
        case .maintenance: return Theme.Colors.warning
        case .overloaded: return Theme.Colors.warning
        }
    }
}

// MARK: - Load Badge

struct LoadBadge: View {
    let load: Int
    
    var body: some View {
        Text("\(load)%")
            .font(Theme.Fonts.caption.bold())
            .foregroundColor(.white)
            .padding(.horizontal, 8)
            .padding(.vertical, 2)
            .background(loadColor)
            .cornerRadius(4)
    }
    
    private var loadColor: Color {
        switch load {
        case 0..<30: return Theme.Colors.success
        case 30..<70: return Theme.Colors.warning
        default: return Theme.Colors.error
        }
    }
}

// MARK: - Stat Item

struct StatItem: View {
    let title: String
    let value: String
    let icon: String
    
    var body: some View {
        VStack(spacing: 4) {
            Image(systemName: icon)
                .font(.caption)
                .foregroundColor(Theme.Colors.primary)
            
            Text(value)
                .font(Theme.Fonts.headline)
                .foregroundColor(Theme.Colors.textPrimary)
            
            Text(title)
                .font(Theme.Fonts.caption)
                .foregroundColor(Theme.Colors.textSecondary)
        }
    }
}

// MARK: - Server Connection Sheet

struct ServerConnectionSheet: View {
    let server: VPNServer
    
    @Environment(\.dismiss) private var dismiss
    @EnvironmentObject private var connectionViewModel: ConnectionViewModel
    
    var body: some View {
        NavigationStack {
            VStack(spacing: 24) {
                // Server details
                VStack(spacing: 16) {
                    AsyncImage(url: server.flagUrl) { image in
                        image
                            .resizable()
                            .aspectRatio(contentMode: .fit)
                    } placeholder: {
                        Text(server.countryCode)
                            .font(.title)
                    }
                    .frame(width: 80, height: 53)
                    .clipShape(RoundedRectangle(cornerRadius: 4))
                    .shadow(radius: 2)
                    
                    Text(server.name)
                        .font(Theme.Fonts.title)
                        .foregroundColor(Theme.Colors.textPrimary)
                    
                    Text(server.locationString)
                        .font(Theme.Fonts.body)
                        .foregroundColor(Theme.Colors.textSecondary)
                }
                .padding(.top)
                
                // Server stats
                HStack(spacing: 32) {
                    VStack(spacing: 4) {
                        Text("Load")
                            .font(Theme.Fonts.caption)
                            .foregroundColor(Theme.Colors.textSecondary)
                        LoadBadge(load: server.load)
                    }
                    
                    VStack(spacing: 4) {
                        Text("Ping")
                            .font(Theme.Fonts.caption)
                            .foregroundColor(Theme.Colors.textSecondary)
                        Text(server.formattedPing)
                            .font(Theme.Fonts.body)
                            .foregroundColor(Theme.Colors.textPrimary)
                    }
                    
                    VStack(spacing: 4) {
                        Text("Protocol")
                            .font(Theme.Fonts.caption)
                            .foregroundColor(Theme.Colors.textSecondary)
                        Text(server.protocol.displayName)
                            .font(Theme.Fonts.body)
                            .foregroundColor(Theme.Colors.textPrimary)
                    }
                }
                .padding()
                .background(Theme.Colors.cardBackground)
                .cornerRadius(12)
                
                Spacer()
                
                // Connect button
                CasperButton(
                    title: "Connect to Server",
                    style: .primary,
                    isLoading: connectionViewModel.isConnecting
                ) {
                    Task {
                        await connectionViewModel.connect(to: server)
                        dismiss()
                    }
                }
                .disabled(!server.isAvailable)
                
                if !server.isAvailable {
                    Text("This server is currently unavailable")
                        .font(Theme.Fonts.caption)
                        .foregroundColor(Theme.Colors.error)
                }
            }
            .padding(24)
            .background(Theme.Colors.background.ignoresSafeArea())
            .navigationTitle("Server Details")
            .navigationBarTitleDisplayMode(.inline)
            .toolbar {
                ToolbarItem(placement: .navigationBarLeading) {
                    Button("Cancel") {
                        dismiss()
                    }
                }
            }
        }
        .presentationDetents([.medium])
    }
}

// MARK: - Preview

#if DEBUG
#Preview {
    ServerListView()
        .environmentObject(ConnectionViewModel())
}
#endif
