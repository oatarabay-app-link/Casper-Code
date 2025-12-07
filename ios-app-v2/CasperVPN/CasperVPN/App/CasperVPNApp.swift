//
//  CasperVPNApp.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//

import SwiftUI

@main
struct CasperVPNApp: App {
    @StateObject private var appCoordinator = AppCoordinator()
    @StateObject private var vpnConnectionManager = VPNConnectionManager.shared
    
    var body: some Scene {
        WindowGroup {
            ContentView()
                .environmentObject(appCoordinator)
                .environmentObject(vpnConnectionManager)
                .preferredColorScheme(.dark)
        }
    }
}

// MARK: - Content View Router
struct ContentView: View {
    @EnvironmentObject var coordinator: AppCoordinator
    @EnvironmentObject var vpnManager: VPNConnectionManager
    
    var body: some View {
        Group {
            if coordinator.isAuthenticated {
                MainTabView()
            } else {
                LoginView()
            }
        }
        .animation(.easeInOut, value: coordinator.isAuthenticated)
    }
}

// MARK: - Main Tab View
struct MainTabView: View {
    @State private var selectedTab = 0
    
    var body: some View {
        TabView(selection: $selectedTab) {
            ConnectionView()
                .tabItem {
                    Label("Connect", systemImage: "shield.fill")
                }
                .tag(0)
            
            ServerListView()
                .tabItem {
                    Label("Servers", systemImage: "server.rack")
                }
                .tag(1)
            
            SettingsView()
                .tabItem {
                    Label("Settings", systemImage: "gear")
                }
                .tag(2)
        }
        .tint(Theme.primaryColor)
    }
}
