//
//  ConnectionView.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//

import SwiftUI

/// Main connection view with animated connection button and status display
struct ConnectionView: View {
    @StateObject private var viewModel = ConnectionViewModel()
    @EnvironmentObject var coordinator: AppCoordinator
    
    var body: some View {
        NavigationStack {
            ZStack {
                // Background gradient
                Theme.backgroundGradient
                    .ignoresSafeArea()
                
                VStack(spacing: 30) {
                    // Status Header
                    StatusHeaderView(
                        state: viewModel.connectionState,
                        networkStatus: viewModel.networkStatus
                    )
                    
                    Spacer()
                    
                    // Connection Button
                    ConnectionButtonView(
                        state: viewModel.buttonState,
                        onTap: {
                            Task {
                                await viewModel.toggleConnection()
                            }
                        }
                    )
                    .disabled(!viewModel.canInteract)
                    
                    // Server Selection
                    ServerSelectionView(
                        serverName: viewModel.serverDisplayName,
                        isConnected: viewModel.connectionState.isConnected,
                        onTap: {
                            viewModel.showServerList = true
                        }
                    )
                    .disabled(viewModel.connectionState.isConnected)
                    
                    Spacer()
                    
                    // Connection Info
                    if viewModel.showStatistics {
                        ConnectionInfoView(
                            duration: viewModel.connectionDuration,
                            statistics: viewModel.statistics,
                            server: viewModel.connectedServer
                        )
                        .transition(.move(edge: .bottom).combined(with: .opacity))
                    }
                }
                .padding()
            }
            .navigationTitle("CasperVPN")
            .navigationBarTitleDisplayMode(.inline)
            .toolbar {
                ToolbarItem(placement: .navigationBarTrailing) {
                    Button {
                        // Quick disconnect
                        if viewModel.connectionState.isConnected {
                            Task {
                                await viewModel.disconnect()
                            }
                        }
                    } label: {
                        Image(systemName: viewModel.connectionState.isConnected ? "power" : "info.circle")
                            .foregroundColor(viewModel.connectionState.isConnected ? .red : .white)
                    }
                }
            }
            .sheet(isPresented: $viewModel.showServerList) {
                ServerListView(onSelect: { server in
                    viewModel.selectServer(server)
                })
            }
            .alert("Connection Error", isPresented: $viewModel.showError) {
                Button("Retry") {
                    Task {
                        await viewModel.retryConnection()
                    }
                }
                Button("OK", role: .cancel) {
                    viewModel.clearError()
                }
            } message: {
                if let error = viewModel.error {
                    Text(error.userFriendlyMessage)
                }
            }
        }
    }
}

// MARK: - Status Header View
struct StatusHeaderView: View {
    let state: ConnectionState
    let networkStatus: NetworkStatus?
    
    var body: some View {
        VStack(spacing: 8) {
            // Status indicator
            HStack(spacing: 8) {
                Circle()
                    .fill(statusColor)
                    .frame(width: 10, height: 10)
                    .shadow(color: statusColor.opacity(0.5), radius: 4)
                
                Text(state.displayText)
                    .font(.headline)
                    .foregroundColor(.white)
            }
            
            // Network type indicator
            if let network = networkStatus {
                HStack(spacing: 4) {
                    Image(systemName: network.connectionType.icon)
                        .font(.caption)
                    Text(network.connectionType.description)
                        .font(.caption)
                }
                .foregroundColor(.gray)
            }
        }
        .animation(.easeInOut, value: state)
    }
    
    private var statusColor: Color {
        switch state {
        case .connected:
            return .green
        case .connecting, .reconnecting:
            return .yellow
        case .disconnecting:
            return .orange
        case .disconnected:
            return .gray
        case .invalid:
            return .red
        }
    }
}

// MARK: - Connection Button View
struct ConnectionButtonView: View {
    let state: ConnectionButtonState
    let onTap: () -> Void
    
    @State private var isAnimating = false
    @State private var pulseScale: CGFloat = 1.0
    
    var body: some View {
        ZStack {
            // Outer pulse animation for connecting state
            if state == .connecting || state == .loading {
                Circle()
                    .stroke(buttonColor.opacity(0.3), lineWidth: 4)
                    .frame(width: 200, height: 200)
                    .scaleEffect(pulseScale)
                    .opacity(2 - pulseScale)
            }
            
            // Main button
            Button(action: onTap) {
                ZStack {
                    // Background circle
                    Circle()
                        .fill(
                            LinearGradient(
                                colors: [buttonColor, buttonColor.opacity(0.7)],
                                startPoint: .topLeading,
                                endPoint: .bottomTrailing
                            )
                        )
                        .frame(width: 160, height: 160)
                        .shadow(color: buttonColor.opacity(0.4), radius: 20, x: 0, y: 10)
                    
                    // Inner content
                    VStack(spacing: 8) {
                        if state == .connecting || state == .loading || state == .disconnecting {
                            ProgressView()
                                .progressViewStyle(CircularProgressViewStyle(tint: .white))
                                .scaleEffect(1.5)
                        } else {
                            Image(systemName: state == .connected ? "checkmark.shield.fill" : "shield.fill")
                                .font(.system(size: 50))
                                .foregroundColor(.white)
                        }
                        
                        Text(state.title)
                            .font(.caption)
                            .fontWeight(.semibold)
                            .foregroundColor(.white.opacity(0.9))
                    }
                }
            }
            .buttonStyle(ScaleButtonStyle())
        }
        .onAppear {
            startPulseAnimation()
        }
        .onChange(of: state) { _ in
            startPulseAnimation()
        }
    }
    
    private var buttonColor: Color {
        switch state {
        case .disconnected:
            return Theme.primaryColor
        case .connecting, .loading:
            return .orange
        case .connected:
            return .green
        case .disconnecting:
            return .orange
        }
    }
    
    private func startPulseAnimation() {
        guard state == .connecting || state == .loading else {
            return
        }
        
        pulseScale = 1.0
        withAnimation(.easeInOut(duration: 1.5).repeatForever(autoreverses: false)) {
            pulseScale = 1.5
        }
    }
}

// MARK: - Server Selection View
struct ServerSelectionView: View {
    let serverName: String
    let isConnected: Bool
    let onTap: () -> Void
    
    var body: some View {
        Button(action: onTap) {
            HStack {
                VStack(alignment: .leading, spacing: 4) {
                    Text("Server")
                        .font(.caption)
                        .foregroundColor(.gray)
                    
                    Text(serverName)
                        .font(.headline)
                        .foregroundColor(.white)
                }
                
                Spacer()
                
                if !isConnected {
                    Image(systemName: "chevron.right")
                        .foregroundColor(.gray)
                }
            }
            .padding()
            .background(Color.white.opacity(0.1))
            .cornerRadius(12)
        }
        .disabled(isConnected)
        .opacity(isConnected ? 0.6 : 1.0)
    }
}

// MARK: - Connection Info View
struct ConnectionInfoView: View {
    let duration: String
    let statistics: ConnectionStatistics
    let server: VPNServer?
    
    var body: some View {
        VStack(spacing: 16) {
            // Duration
            HStack {
                Image(systemName: "clock")
                    .foregroundColor(.green)
                Text("Connected for")
                    .foregroundColor(.gray)
                Spacer()
                Text(duration)
                    .font(.headline)
                    .foregroundColor(.white)
                    .monospacedDigit()
            }
            
            Divider()
                .background(Color.white.opacity(0.2))
            
            // Data transferred
            HStack {
                VStack(alignment: .leading, spacing: 4) {
                    HStack {
                        Image(systemName: "arrow.down")
                            .foregroundColor(.green)
                        Text("Download")
                            .foregroundColor(.gray)
                    }
                    Text(statistics.formattedBytesReceived)
                        .font(.headline)
                        .foregroundColor(.white)
                }
                
                Spacer()
                
                VStack(alignment: .trailing, spacing: 4) {
                    HStack {
                        Text("Upload")
                            .foregroundColor(.gray)
                        Image(systemName: "arrow.up")
                            .foregroundColor(.orange)
                    }
                    Text(statistics.formattedBytesSent)
                        .font(.headline)
                        .foregroundColor(.white)
                }
            }
            
            // Server info
            if let server = server {
                Divider()
                    .background(Color.white.opacity(0.2))
                
                HStack {
                    Text("\(server.flagEmoji) \(server.name)")
                        .foregroundColor(.white)
                    Spacer()
                    Text("WireGuard")
                        .font(.caption)
                        .padding(.horizontal, 8)
                        .padding(.vertical, 4)
                        .background(Color.blue.opacity(0.3))
                        .cornerRadius(8)
                }
            }
        }
        .padding()
        .background(Color.white.opacity(0.05))
        .cornerRadius(16)
    }
}

// MARK: - Scale Button Style
struct ScaleButtonStyle: ButtonStyle {
    func makeBody(configuration: Configuration) -> some View {
        configuration.label
            .scaleEffect(configuration.isPressed ? 0.95 : 1.0)
            .animation(.spring(response: 0.3), value: configuration.isPressed)
    }
}

// MARK: - Preview
#Preview {
    ConnectionView()
        .environmentObject(AppCoordinator())
}
