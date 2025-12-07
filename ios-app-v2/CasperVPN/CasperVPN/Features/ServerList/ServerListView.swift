//
//  ServerListView.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//

import SwiftUI

struct ServerListView: View {
    @StateObject private var viewModel = ServerListViewModel()
    @Environment(\.dismiss) var dismiss
    
    var onSelect: ((VPNServer) -> Void)?
    
    var body: some View {
        NavigationStack {
            ZStack {
                Theme.backgroundGradient
                    .ignoresSafeArea()
                
                if viewModel.isLoading && viewModel.servers.isEmpty {
                    ProgressView()
                        .tint(.white)
                } else {
                    serverList
                }
            }
            .navigationTitle("Servers")
            .navigationBarTitleDisplayMode(.inline)
            .searchable(text: $viewModel.searchText, prompt: "Search servers")
            .refreshable {
                await viewModel.refresh()
            }
            .toolbar {
                if onSelect != nil {
                    ToolbarItem(placement: .navigationBarLeading) {
                        Button("Cancel") {
                            dismiss()
                        }
                    }
                }
            }
            .task {
                if viewModel.servers.isEmpty {
                    await viewModel.loadServers()
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
        }
    }
    
    private var serverList: some View {
        List {
            ForEach(viewModel.getCountries(), id: \.self) { country in
                Section {
                    if let servers = viewModel.serversByCountry[country] {
                        ForEach(servers.filter { server in
                            viewModel.searchText.isEmpty ||
                            server.name.localizedCaseInsensitiveContains(viewModel.searchText) ||
                            server.city.localizedCaseInsensitiveContains(viewModel.searchText)
                        }) { server in
                            ServerRowView(server: server) {
                                if let onSelect = onSelect {
                                    onSelect(server)
                                }
                            }
                        }
                    }
                } header: {
                    if let firstServer = viewModel.serversByCountry[country]?.first {
                        HStack {
                            Text(firstServer.flagEmoji)
                            Text(country)
                        }
                    }
                }
                .listRowBackground(Color.white.opacity(0.05))
            }
        }
        .scrollContentBackground(.hidden)
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
                    .fill(server.isOnline ? .green : .red)
                    .frame(width: 8, height: 8)
                
                // Server info
                VStack(alignment: .leading, spacing: 2) {
                    HStack {
                        Text(server.name)
                            .foregroundColor(.white)
                            .fontWeight(.medium)
                        
                        if server.isPremium {
                            Image(systemName: "crown.fill")
                                .font(.caption2)
                                .foregroundColor(.yellow)
                        }
                    }
                    
                    Text(server.city)
                        .font(.caption)
                        .foregroundColor(.gray)
                }
                
                Spacer()
                
                // Load indicator
                VStack(alignment: .trailing, spacing: 2) {
                    LoadIndicator(load: server.load)
                    
                    if let latency = server.latency {
                        Text("\(latency) ms")
                            .font(.caption2)
                            .foregroundColor(.gray)
                    }
                }
            }
            .contentShape(Rectangle())
        }
        .buttonStyle(.plain)
    }
}

// MARK: - Load Indicator
struct LoadIndicator: View {
    let load: Int
    
    var body: some View {
        HStack(spacing: 2) {
            ForEach(0..<3) { index in
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
            return .green
        case 30..<70:
            return .yellow
        default:
            return .red
        }
    }
}

// MARK: - Preview
#Preview {
    ServerListView()
}
