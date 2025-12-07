//
//  SettingsView.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//

import SwiftUI

/// Settings view with kill switch toggle and other preferences
struct SettingsView: View {
    @StateObject private var viewModel = SettingsViewModel()
    @EnvironmentObject var coordinator: AppCoordinator
    
    var body: some View {
        NavigationStack {
            ZStack {
                Theme.backgroundGradient
                    .ignoresSafeArea()
                
                List {
                    // Connection Section
                    Section {
                        // Kill Switch
                        SettingsToggleRow(
                            title: "Kill Switch",
                            subtitle: "Block internet if VPN disconnects",
                            icon: "shield.slash",
                            iconColor: .red,
                            isOn: $viewModel.isKillSwitchEnabled
                        )
                        
                        // Auto-Connect
                        SettingsToggleRow(
                            title: "Auto-Connect",
                            subtitle: "Connect VPN when app opens",
                            icon: "bolt",
                            iconColor: .yellow,
                            isOn: $viewModel.isAutoConnectEnabled
                        )
                        
                        // Protocol Selection
                        NavigationLink {
                            ProtocolSelectionView(selectedProtocol: $viewModel.selectedProtocol)
                        } label: {
                            SettingsRow(
                                title: "Protocol",
                                subtitle: viewModel.selectedProtocol.rawValue,
                                icon: "network",
                                iconColor: .blue
                            )
                        }
                    } header: {
                        Text("Connection")
                    }
                    .listRowBackground(Color.white.opacity(0.05))
                    
                    // Security Section
                    Section {
                        NavigationLink {
                            TrustedNetworksView(viewModel: viewModel)
                        } label: {
                            SettingsRow(
                                title: "Trusted Networks",
                                subtitle: "\(viewModel.getTrustedNetworks().count) networks",
                                icon: "wifi",
                                iconColor: .green
                            )
                        }
                    } header: {
                        Text("Security")
                    }
                    .listRowBackground(Color.white.opacity(0.05))
                    
                    // Notifications Section
                    Section {
                        SettingsToggleRow(
                            title: "Notifications",
                            subtitle: "Connection status alerts",
                            icon: "bell",
                            iconColor: .orange,
                            isOn: $viewModel.isNotificationsEnabled
                        )
                    } header: {
                        Text("Notifications")
                    }
                    .listRowBackground(Color.white.opacity(0.05))
                    
                    // Support Section
                    Section {
                        Button {
                            viewModel.showLogViewer = true
                        } label: {
                            SettingsRow(
                                title: "View Logs",
                                subtitle: "Connection history",
                                icon: "doc.text",
                                iconColor: .gray
                            )
                        }
                        
                        Button {
                            viewModel.showExportLogs = true
                        } label: {
                            SettingsRow(
                                title: "Export Logs",
                                subtitle: "For support requests",
                                icon: "square.and.arrow.up",
                                iconColor: .blue
                            )
                        }
                        
                        Button {
                            // Open support URL
                            if let url = URL(string: "https://caspervpn.com/support") {
                                UIApplication.shared.open(url)
                            }
                        } label: {
                            SettingsRow(
                                title: "Get Help",
                                subtitle: "Contact support",
                                icon: "questionmark.circle",
                                iconColor: .purple
                            )
                        }
                    } header: {
                        Text("Support")
                    }
                    .listRowBackground(Color.white.opacity(0.05))
                    
                    // About Section
                    Section {
                        SettingsRow(
                            title: "Version",
                            subtitle: Bundle.main.appVersion,
                            icon: "info.circle",
                            iconColor: .gray
                        )
                        
                        Button {
                            if let url = URL(string: "https://caspervpn.com/privacy") {
                                UIApplication.shared.open(url)
                            }
                        } label: {
                            SettingsRow(
                                title: "Privacy Policy",
                                subtitle: "",
                                icon: "hand.raised",
                                iconColor: .blue
                            )
                        }
                        
                        Button {
                            if let url = URL(string: "https://caspervpn.com/terms") {
                                UIApplication.shared.open(url)
                            }
                        } label: {
                            SettingsRow(
                                title: "Terms of Service",
                                subtitle: "",
                                icon: "doc.plaintext",
                                iconColor: .blue
                            )
                        }
                    } header: {
                        Text("About")
                    }
                    .listRowBackground(Color.white.opacity(0.05))
                    
                    // Account Section
                    Section {
                        Button {
                            Task {
                                await coordinator.logout()
                            }
                        } label: {
                            HStack {
                                Spacer()
                                Text("Sign Out")
                                    .foregroundColor(.red)
                                Spacer()
                            }
                        }
                    }
                    .listRowBackground(Color.white.opacity(0.05))
                }
                .scrollContentBackground(.hidden)
            }
            .navigationTitle("Settings")
            .sheet(isPresented: $viewModel.showLogViewer) {
                LogViewerView(viewModel: viewModel)
            }
            .sheet(isPresented: $viewModel.showExportLogs) {
                ShareSheet(items: [viewModel.exportLogs()])
            }
            .alert("Error", isPresented: $viewModel.showError) {
                Button("OK", role: .cancel) {}
            } message: {
                if let error = viewModel.error {
                    Text(error)
                }
            }
        }
    }
}

// MARK: - Settings Row
struct SettingsRow: View {
    let title: String
    let subtitle: String
    let icon: String
    let iconColor: Color
    
    var body: some View {
        HStack(spacing: 12) {
            Image(systemName: icon)
                .font(.system(size: 18))
                .foregroundColor(iconColor)
                .frame(width: 30)
            
            VStack(alignment: .leading, spacing: 2) {
                Text(title)
                    .foregroundColor(.white)
                
                if !subtitle.isEmpty {
                    Text(subtitle)
                        .font(.caption)
                        .foregroundColor(.gray)
                }
            }
            
            Spacer()
        }
        .contentShape(Rectangle())
    }
}

// MARK: - Settings Toggle Row
struct SettingsToggleRow: View {
    let title: String
    let subtitle: String
    let icon: String
    let iconColor: Color
    @Binding var isOn: Bool
    
    var body: some View {
        HStack(spacing: 12) {
            Image(systemName: icon)
                .font(.system(size: 18))
                .foregroundColor(iconColor)
                .frame(width: 30)
            
            VStack(alignment: .leading, spacing: 2) {
                Text(title)
                    .foregroundColor(.white)
                
                Text(subtitle)
                    .font(.caption)
                    .foregroundColor(.gray)
            }
            
            Spacer()
            
            Toggle("", isOn: $isOn)
                .labelsHidden()
                .tint(Theme.primaryColor)
        }
    }
}

// MARK: - Protocol Selection View
struct ProtocolSelectionView: View {
    @Binding var selectedProtocol: VPNProtocolType
    @Environment(\.dismiss) var dismiss
    
    var body: some View {
        ZStack {
            Theme.backgroundGradient
                .ignoresSafeArea()
            
            List {
                ForEach(VPNProtocolType.allCases) { vpnProtocol in
                    Button {
                        selectedProtocol = vpnProtocol
                        dismiss()
                    } label: {
                        HStack {
                            Image(systemName: vpnProtocol.icon)
                                .foregroundColor(Theme.primaryColor)
                                .frame(width: 30)
                            
                            VStack(alignment: .leading, spacing: 2) {
                                Text(vpnProtocol.rawValue)
                                    .foregroundColor(.white)
                                
                                Text(vpnProtocol.description)
                                    .font(.caption)
                                    .foregroundColor(.gray)
                            }
                            
                            Spacer()
                            
                            if selectedProtocol == vpnProtocol {
                                Image(systemName: "checkmark")
                                    .foregroundColor(Theme.primaryColor)
                            }
                        }
                    }
                    .listRowBackground(Color.white.opacity(0.05))
                }
            }
            .scrollContentBackground(.hidden)
        }
        .navigationTitle("Protocol")
        .navigationBarTitleDisplayMode(.inline)
    }
}

// MARK: - Trusted Networks View
struct TrustedNetworksView: View {
    @ObservedObject var viewModel: SettingsViewModel
    @State private var newNetwork: String = ""
    @State private var showAddNetwork: Bool = false
    
    var body: some View {
        ZStack {
            Theme.backgroundGradient
                .ignoresSafeArea()
            
            List {
                Section {
                    Text("VPN will not connect automatically on trusted networks")
                        .font(.caption)
                        .foregroundColor(.gray)
                }
                .listRowBackground(Color.clear)
                
                Section {
                    ForEach(viewModel.getTrustedNetworks(), id: \.self) { network in
                        HStack {
                            Image(systemName: "wifi")
                                .foregroundColor(.green)
                            
                            Text(network)
                                .foregroundColor(.white)
                            
                            Spacer()
                        }
                    }
                    .onDelete { indexSet in
                        for index in indexSet {
                            let network = viewModel.getTrustedNetworks()[index]
                            viewModel.removeTrustedNetwork(network)
                        }
                    }
                } header: {
                    Text("Trusted Networks")
                }
                .listRowBackground(Color.white.opacity(0.05))
            }
            .scrollContentBackground(.hidden)
        }
        .navigationTitle("Trusted Networks")
        .navigationBarTitleDisplayMode(.inline)
        .toolbar {
            ToolbarItem(placement: .navigationBarTrailing) {
                Button {
                    showAddNetwork = true
                } label: {
                    Image(systemName: "plus")
                }
            }
        }
        .alert("Add Network", isPresented: $showAddNetwork) {
            TextField("Network Name (SSID)", text: $newNetwork)
            Button("Add") {
                if !newNetwork.isEmpty {
                    viewModel.addTrustedNetwork(newNetwork)
                    newNetwork = ""
                }
            }
            Button("Cancel", role: .cancel) {
                newNetwork = ""
            }
        }
    }
}

// MARK: - Log Viewer View
struct LogViewerView: View {
    @ObservedObject var viewModel: SettingsViewModel
    @Environment(\.dismiss) var dismiss
    
    var body: some View {
        NavigationStack {
            ZStack {
                Theme.backgroundGradient
                    .ignoresSafeArea()
                
                List {
                    ForEach(viewModel.getRecentLogs()) { entry in
                        VStack(alignment: .leading, spacing: 4) {
                            HStack {
                                Circle()
                                    .fill(logLevelColor(entry.level))
                                    .frame(width: 8, height: 8)
                                
                                Text(entry.level.description)
                                    .font(.caption)
                                    .foregroundColor(.gray)
                                
                                Spacer()
                                
                                Text(formatDate(entry.timestamp))
                                    .font(.caption2)
                                    .foregroundColor(.gray)
                            }
                            
                            Text(entry.message)
                                .font(.caption)
                                .foregroundColor(.white)
                        }
                        .listRowBackground(Color.white.opacity(0.05))
                    }
                }
                .scrollContentBackground(.hidden)
            }
            .navigationTitle("Connection Logs")
            .navigationBarTitleDisplayMode(.inline)
            .toolbar {
                ToolbarItem(placement: .navigationBarLeading) {
                    Button("Close") {
                        dismiss()
                    }
                }
                
                ToolbarItem(placement: .navigationBarTrailing) {
                    Button("Clear") {
                        viewModel.clearLogs()
                    }
                    .foregroundColor(.red)
                }
            }
        }
    }
    
    private func logLevelColor(_ level: LogLevel) -> Color {
        switch level {
        case .debug: return .gray
        case .info: return .blue
        case .warning: return .yellow
        case .error: return .red
        }
    }
    
    private func formatDate(_ date: Date) -> String {
        let formatter = DateFormatter()
        formatter.dateFormat = "HH:mm:ss"
        return formatter.string(from: date)
    }
}

// MARK: - Share Sheet
struct ShareSheet: UIViewControllerRepresentable {
    let items: [Any]
    
    func makeUIViewController(context: Context) -> UIActivityViewController {
        UIActivityViewController(activityItems: items, applicationActivities: nil)
    }
    
    func updateUIViewController(_ uiViewController: UIActivityViewController, context: Context) {}
}

// MARK: - Bundle Extension
extension Bundle {
    var appVersion: String {
        let version = infoDictionary?["CFBundleShortVersionString"] as? String ?? "1.0"
        let build = infoDictionary?["CFBundleVersion"] as? String ?? "1"
        return "\(version) (\(build))"
    }
}

// MARK: - Preview
#Preview {
    SettingsView()
        .environmentObject(AppCoordinator())
}
