//
//  ConnectionView.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//  Copyright © 2024 CasperVPN. All rights reserved.
//

import SwiftUI

/// Main connection screen showing VPN status and quick connect functionality.
struct ConnectionView: View {
    
    // MARK: - Properties
    
    @EnvironmentObject private var connectionViewModel: ConnectionViewModel
    @EnvironmentObject private var authViewModel: AuthViewModel
    
    @State private var showingServerPicker = false
    
    // MARK: - Body
    
    var body: some View {
        NavigationStack {
            ZStack {
                // Background gradient
                backgroundGradient
                
                VStack(spacing: 32) {
                    Spacer()
                    
                    // Connection status ring
                    connectionStatusRing
                    
                    // Status text
                    statusText
                    
                    // Server selection
                    serverSelectionButton
                    
                    // Connection stats (when connected)
                    if connectionViewModel.isConnected {
                        connectionStats
                    }
                    
                    Spacer()
                    
                    // Connect button
                    connectButton
                    
                    // Quick actions
                    quickActions
                }
                .padding(24)
            }
            .navigationTitle("CasperVPN")
            .navigationBarTitleDisplayMode(.inline)
            .toolbar {
                ToolbarItem(placement: .navigationBarTrailing) {
                    userButton
                }
            }
            .sheet(isPresented: $showingServerPicker) {
                ServerListView()
            }
            .alert("Error", isPresented: $connectionViewModel.showError) {
                Button("OK", role: .cancel) {}
            } message: {
                Text(connectionViewModel.errorMessage ?? "An error occurred")
            }
        }
    }
    
    // MARK: - Background Gradient
    
    private var backgroundGradient: some View {
        LinearGradient(
            colors: [
                Theme.Colors.background,
                connectionViewModel.isConnected 
                    ? Theme.Colors.success.opacity(0.1) 
                    : Theme.Colors.background
            ],
            startPoint: .top,
            endPoint: .bottom
        )
        .ignoresSafeArea()
    }
    
    // MARK: - Connection Status Ring
    
    private var connectionStatusRing: some View {
        ZStack {
            // Outer ring
            Circle()
                .stroke(
                    connectionViewModel.isConnected 
                        ? Theme.Colors.success.opacity(0.3) 
                        : Theme.Colors.textSecondary.opacity(0.2),
                    lineWidth: 8
                )
                .frame(width: 200, height: 200)
            
            // Progress ring (when connecting)
            if connectionViewModel.isConnecting {
                Circle()
                    .trim(from: 0, to: 0.7)
                    .stroke(
                        Theme.Colors.primary,
                        style: StrokeStyle(lineWidth: 8, lineCap: .round)
                    )
                    .frame(width: 200, height: 200)
                    .rotationEffect(.degrees(-90))
                    .animation(
                        .linear(duration: 1).repeatForever(autoreverses: false),
                        value: connectionViewModel.isConnecting
                    )
            } else if connectionViewModel.isConnected {
                // Connected ring
                Circle()
                    .stroke(
                        Theme.Colors.success,
                        lineWidth: 8
                    )
                    .frame(width: 200, height: 200)
            }
            
            // Inner content
            VStack(spacing: 8) {
                Image(systemName: statusIcon)
                    .font(.system(size: 50))
                    .foregroundColor(statusColor)
                
                if connectionViewModel.isConnected, let info = connectionViewModel.connectionInfo {
                    Text(info.formattedDuration)
                        .font(Theme.Fonts.title2.monospacedDigit())
                        .foregroundColor(Theme.Colors.textPrimary)
                }
            }
        }
    }
    
    // MARK: - Status Text
    
    private var statusText: some View {
        VStack(spacing: 8) {
            Text(connectionViewModel.status.displayName)
                .font(Theme.Fonts.title)
                .foregroundColor(Theme.Colors.textPrimary)
            
            if connectionViewModel.isConnected, let server = connectionViewModel.selectedServer {
                HStack(spacing: 8) {
                    AsyncImage(url: server.flagUrl) { image in
                        image
                            .resizable()
                            .aspectRatio(contentMode: .fit)
                    } placeholder: {
                        Text(server.countryCode)
                    }
                    .frame(width: 24, height: 16)
                    .clipShape(RoundedRectangle(cornerRadius: 2))
                    
                    Text(server.locationString)
                        .font(Theme.Fonts.body)
                        .foregroundColor(Theme.Colors.textSecondary)
                }
            } else {
                Text("Your connection is not secure")
                    .font(Theme.Fonts.body)
                    .foregroundColor(Theme.Colors.textSecondary)
            }
        }
    }
    
    // MARK: - Server Selection Button
    
    private var serverSelectionButton: some View {
        Button {
            showingServerPicker = true
        } label: {
            HStack {
                if let server = connectionViewModel.selectedServer {
                    AsyncImage(url: server.flagUrl) { image in
                        image
                            .resizable()
                            .aspectRatio(contentMode: .fit)
                    } placeholder: {
                        Image(systemName: "globe")
                    }
                    .frame(width: 32, height: 21)
                    .clipShape(RoundedRectangle(cornerRadius: 3))
                    
                    VStack(alignment: .leading, spacing: 2) {
                        Text(server.name)
                            .font(Theme.Fonts.body)
                            .foregroundColor(Theme.Colors.textPrimary)
                        Text(server.locationString)
                            .font(Theme.Fonts.caption)
                            .foregroundColor(Theme.Colors.textSecondary)
                    }
                } else {
                    Image(systemName: "globe")
                        .font(.title2)
                        .foregroundColor(Theme.Colors.primary)
                    
                    Text("Select a Server")
                        .font(Theme.Fonts.body)
                        .foregroundColor(Theme.Colors.textPrimary)
                }
                
                Spacer()
                
                Image(systemName: "chevron.right")
                    .font(.caption)
                    .foregroundColor(Theme.Colors.textSecondary)
            }
            .padding()
            .background(Theme.Colors.cardBackground)
            .cornerRadius(12)
        }
        .disabled(connectionViewModel.isConnecting)
    }
    
    // MARK: - Connection Stats
    
    private var connectionStats: some View {
        HStack(spacing: 32) {
            VStack(spacing: 4) {
                Image(systemName: "arrow.down.circle")
                    .font(.title3)
                    .foregroundColor(Theme.Colors.success)
                Text(connectionViewModel.connectionInfo?.formattedBytesReceived ?? "0 B")
                    .font(Theme.Fonts.body.monospacedDigit())
                    .foregroundColor(Theme.Colors.textPrimary)
                Text("Downloaded")
                    .font(Theme.Fonts.caption)
                    .foregroundColor(Theme.Colors.textSecondary)
            }
            
            Divider()
                .frame(height: 50)
            
            VStack(spacing: 4) {
                Image(systemName: "arrow.up.circle")
                    .font(.title3)
                    .foregroundColor(Theme.Colors.primary)
                Text(connectionViewModel.connectionInfo?.formattedBytesSent ?? "0 B")
                    .font(Theme.Fonts.body.monospacedDigit())
                    .foregroundColor(Theme.Colors.textPrimary)
                Text("Uploaded")
                    .font(Theme.Fonts.caption)
                    .foregroundColor(Theme.Colors.textSecondary)
            }
        }
        .padding()
        .background(Theme.Colors.cardBackground)
        .cornerRadius(12)
    }
    
    // MARK: - Connect Button
    
    private var connectButton: some View {
        CasperButton(
            title: connectionButtonTitle,
            style: connectionViewModel.isConnected ? .danger : .primary,
            isLoading: connectionViewModel.isConnecting || connectionViewModel.isDisconnecting
        ) {
            Task {
                if connectionViewModel.isConnected {
                    await connectionViewModel.disconnect()
                } else {
                    await connectionViewModel.quickConnect()
                }
            }
        }
    }
    
    // MARK: - Quick Actions
    
    private var quickActions: some View {
        HStack(spacing: 24) {
            QuickActionButton(
                icon: "bolt.fill",
                title: "Quick",
                action: {
                    Task {
                        await connectionViewModel.quickConnect()
                    }
                }
            )
            .disabled(connectionViewModel.isConnected)
            
            QuickActionButton(
                icon: "location.fill",
                title: "Nearest",
                action: {
                    Task {
                        await connectionViewModel.connectToNearest()
                    }
                }
            )
            .disabled(connectionViewModel.isConnected)
            
            QuickActionButton(
                icon: "star.fill",
                title: "Favorites",
                action: {
                    // TODO: Implement favorites
                }
            )
        }
    }
    
    // MARK: - User Button
    
    private var userButton: some View {
        Menu {
            if let user = authViewModel.currentUser {
                Text(user.email)
                    .foregroundColor(Theme.Colors.textSecondary)
                
                Divider()
                
                Button {
                    // Navigate to account
                } label: {
                    Label("Account", systemImage: "person.circle")
                }
                
                Button(role: .destructive) {
                    Task {
                        await authViewModel.logout()
                    }
                } label: {
                    Label("Sign Out", systemImage: "rectangle.portrait.and.arrow.right")
                }
            }
        } label: {
            Image(systemName: "person.circle")
                .font(.title2)
        }
    }
    
    // MARK: - Computed Properties
    
    private var statusIcon: String {
        switch connectionViewModel.status {
        case .connected:
            return "shield.checkered"
        case .connecting, .disconnecting, .reasserting:
            return "shield"
        default:
            return "shield.slash"
        }
    }
    
    private var statusColor: Color {
        switch connectionViewModel.status {
        case .connected:
            return Theme.Colors.success
        case .connecting, .disconnecting, .reasserting:
            return Theme.Colors.primary
        default:
            return Theme.Colors.textSecondary
        }
    }
    
    private var connectionButtonTitle: String {
        if connectionViewModel.isConnecting {
            return "Connecting..."
        } else if connectionViewModel.isDisconnecting {
            return "Disconnecting..."
        } else if connectionViewModel.isConnected {
            return "Disconnect"
        } else {
            return "Connect"
        }
    }
}

// MARK: - Quick Action Button

struct QuickActionButton: View {
    let icon: String
    let title: String
    let action: () -> Void
    
    var body: some View {
        Button(action: action) {
            VStack(spacing: 8) {
                Image(systemName: icon)
                    .font(.title2)
                    .foregroundColor(Theme.Colors.primary)
                
                Text(title)
                    .font(Theme.Fonts.caption)
                    .foregroundColor(Theme.Colors.textSecondary)
            }
            .frame(width: 70, height: 70)
            .background(Theme.Colors.cardBackground)
            .cornerRadius(12)
        }
    }
}

// MARK: - Preview

#if DEBUG
#Preview {
    ConnectionView()
        .environmentObject(ConnectionViewModel())
        .environmentObject(AuthViewModel())
}
#endif
