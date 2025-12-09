//
//  ServerDetailView.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//

import SwiftUI

/// A detailed view for displaying comprehensive server information.
///
/// This view shows:
/// - Server location and status
/// - Latency with color-coded badge
/// - Load percentage with progress indicator
/// - Available features
/// - Connect and favorite buttons
struct ServerDetailView: View {
    
    // MARK: - Properties
    
    let server: VPNServer
    let isFavorite: Bool
    let isConnected: Bool
    let isConnecting: Bool
    let latency: Int?
    
    let onConnect: () -> Void
    let onDisconnect: () -> Void
    let onFavoriteToggle: () -> Void
    
    @Environment(\.dismiss) private var dismiss
    
    // MARK: - Body
    
    var body: some View {
        NavigationStack {
            ZStack {
                Theme.backgroundGradient
                    .ignoresSafeArea()
                
                ScrollView {
                    VStack(spacing: 24) {
                        // Header with flag and name
                        serverHeader
                        
                        // Status cards
                        statusCardsSection
                        
                        // Features section
                        if let features = server.features, !features.isEmpty {
                            featuresSection(features: features)
                        }
                        
                        // Server details section
                        serverDetailsSection
                        
                        // Action buttons
                        actionButtonsSection
                    }
                    .padding()
                }
            }
            .navigationBarTitleDisplayMode(.inline)
            .toolbar {
                ToolbarItem(placement: .navigationBarTrailing) {
                    Button {
                        onFavoriteToggle()
                    } label: {
                        Image(systemName: isFavorite ? "heart.fill" : "heart")
                            .foregroundColor(isFavorite ? .red : Theme.Colors.textSecondary)
                    }
                }
            }
        }
    }
    
    // MARK: - Server Header
    
    private var serverHeader: some View {
        VStack(spacing: 16) {
            // Flag emoji (large)
            Text(server.flagEmoji)
                .font(.system(size: 72))
            
            // Server name
            VStack(spacing: 4) {
                Text(server.name)
                    .font(Theme.Fonts.title2)
                    .foregroundColor(Theme.Colors.textPrimary)
                
                Text(server.displayName)
                    .font(Theme.Fonts.subheadline)
                    .foregroundColor(Theme.Colors.textSecondary)
            }
            
            // Status badges
            HStack(spacing: 12) {
                OnlineStatusBadge(isOnline: server.isOnline)
                
                if server.isPremium {
                    PremiumBadge()
                }
                
                if isConnected {
                    StatusBadge(text: "Connected", status: .success)
                }
            }
        }
        .padding(.vertical)
    }
    
    // MARK: - Status Cards Section
    
    private var statusCardsSection: some View {
        HStack(spacing: 16) {
            // Latency card
            StatusCard(
                title: "Latency",
                icon: "speedometer"
            ) {
                if let latency = latency ?? server.latency {
                    VStack(spacing: 4) {
                        Text("\(latency)")
                            .font(Theme.Fonts.title)
                            .foregroundColor(latencyColor(for: latency))
                        
                        Text("ms")
                            .font(Theme.Fonts.caption)
                            .foregroundColor(Theme.Colors.textSecondary)
                    }
                } else {
                    VStack(spacing: 4) {
                        Text("--")
                            .font(Theme.Fonts.title)
                            .foregroundColor(Theme.Colors.textSecondary)
                        
                        Text("Testing...")
                            .font(Theme.Fonts.caption)
                            .foregroundColor(Theme.Colors.textSecondary)
                    }
                }
            }
            
            // Load card
            StatusCard(
                title: "Load",
                icon: "chart.bar.fill"
            ) {
                VStack(spacing: 8) {
                    ZStack {
                        Circle()
                            .stroke(Color.gray.opacity(0.2), lineWidth: 8)
                        
                        Circle()
                            .trim(from: 0, to: CGFloat(server.load) / 100)
                            .stroke(loadColor, style: StrokeStyle(lineWidth: 8, lineCap: .round))
                            .rotationEffect(.degrees(-90))
                        
                        Text("\(server.load)%")
                            .font(Theme.Fonts.headline)
                            .foregroundColor(loadColor)
                    }
                    .frame(width: 60, height: 60)
                    
                    Text(server.loadStatus.description)
                        .font(Theme.Fonts.caption)
                        .foregroundColor(loadColor)
                }
            }
        }
    }
    
    // MARK: - Features Section
    
    private func featuresSection(features: [ServerFeature]) -> some View {
        VStack(alignment: .leading, spacing: 12) {
            SectionHeader(title: "Features")
            
            LazyVGrid(columns: [GridItem(.flexible()), GridItem(.flexible())], spacing: 8) {
                ForEach(features, id: \.self) { feature in
                    FeatureBadge(feature: feature)
                }
            }
        }
        .frame(maxWidth: .infinity, alignment: .leading)
    }
    
    // MARK: - Server Details Section
    
    private var serverDetailsSection: some View {
        VStack(alignment: .leading, spacing: 12) {
            SectionHeader(title: "Server Details")
            
            VStack(spacing: 0) {
                DetailRow(title: "Hostname", value: server.hostname)
                
                Divider()
                    .background(Color.white.opacity(0.1))
                
                DetailRow(title: "IP Address", value: server.ipAddress)
                
                Divider()
                    .background(Color.white.opacity(0.1))
                
                DetailRow(title: "Port", value: "\(server.port)")
                
                Divider()
                    .background(Color.white.opacity(0.1))
                
                DetailRow(title: "Country", value: server.country)
                
                Divider()
                    .background(Color.white.opacity(0.1))
                
                DetailRow(title: "City", value: server.city)
            }
            .padding()
            .background(Theme.cardGradient)
            .cornerRadius(Theme.CornerRadius.medium)
        }
    }
    
    // MARK: - Action Buttons Section
    
    private var actionButtonsSection: some View {
        VStack(spacing: 12) {
            // Connect/Disconnect button
            if isConnected {
                CasperButton(
                    title: "Disconnect",
                    style: .danger,
                    isLoading: isConnecting,
                    icon: "xmark.circle"
                ) {
                    onDisconnect()
                }
            } else {
                CasperButton(
                    title: server.isOnline ? "Connect" : "Server Offline",
                    style: .primary,
                    isLoading: isConnecting,
                    icon: "bolt.fill"
                ) {
                    if server.isOnline {
                        onConnect()
                    }
                }
                .disabled(!server.isOnline)
                .opacity(server.isOnline ? 1.0 : 0.6)
            }
            
            // Favorite button
            CasperButton(
                title: isFavorite ? "Remove from Favorites" : "Add to Favorites",
                style: .outline,
                icon: isFavorite ? "heart.slash" : "heart"
            ) {
                onFavoriteToggle()
            }
        }
        .padding(.top)
    }
    
    // MARK: - Helper Methods
    
    private func latencyColor(for latency: Int) -> Color {
        switch latency {
        case 0..<100:
            return Theme.Colors.success
        case 100..<200:
            return Theme.Colors.warning
        default:
            return Theme.Colors.error
        }
    }
    
    private var loadColor: Color {
        switch server.load {
        case 0..<30:
            return Theme.Colors.loadLow
        case 30..<70:
            return Theme.Colors.loadMedium
        default:
            return Theme.Colors.loadHigh
        }
    }
}

// MARK: - Status Card

/// A card for displaying a status metric.
private struct StatusCard<Content: View>: View {
    
    let title: String
    let icon: String
    @ViewBuilder let content: Content
    
    var body: some View {
        VStack(spacing: 12) {
            HStack(spacing: 6) {
                Image(systemName: icon)
                    .font(.system(size: 14))
                    .foregroundColor(Theme.Colors.primary)
                
                Text(title)
                    .font(Theme.Fonts.caption)
                    .foregroundColor(Theme.Colors.textSecondary)
            }
            
            content
        }
        .frame(maxWidth: .infinity)
        .padding()
        .background(Theme.cardGradient)
        .cornerRadius(Theme.CornerRadius.medium)
    }
}

// MARK: - Detail Row

/// A row for displaying a key-value pair in server details.
private struct DetailRow: View {
    
    let title: String
    let value: String
    
    var body: some View {
        HStack {
            Text(title)
                .font(Theme.Fonts.body)
                .foregroundColor(Theme.Colors.textSecondary)
            
            Spacer()
            
            Text(value)
                .font(Theme.Fonts.body)
                .foregroundColor(Theme.Colors.textPrimary)
                .lineLimit(1)
                .truncationMode(.middle)
        }
        .padding(.vertical, 8)
    }
}

// MARK: - Preview

#if DEBUG
#Preview("Server Detail View") {
    let sampleServer = VPNServer(
        id: "server-1",
        name: "US-East-1",
        country: "United States",
        city: "New York",
        countryCode: "US",
        hostname: "us-east-1.caspervpn.com",
        ipAddress: "192.168.1.1",
        port: 51820,
        load: 45,
        isPremium: true,
        isOnline: true,
        features: [.streaming, .p2p, .gaming],
        latency: 85
    )
    
    ServerDetailView(
        server: sampleServer,
        isFavorite: true,
        isConnected: false,
        isConnecting: false,
        latency: 85,
        onConnect: {},
        onDisconnect: {},
        onFavoriteToggle: {}
    )
}
#endif
