//
//  CasperVPNApp.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//  Copyright © 2024 CasperVPN. All rights reserved.
//

import SwiftUI
import NetworkExtension

/// Main entry point for the CasperVPN iOS application.
/// Uses SwiftUI App lifecycle with environment object injection for shared state management.
@main
struct CasperVPNApp: App {
    
    // MARK: - Properties
    
    /// App delegate adapter for handling UIKit-based lifecycle events
    @UIApplicationDelegateAdaptor(AppDelegate.self) var appDelegate
    
    /// Shared authentication view model for managing user state across the app
    @StateObject private var authViewModel = AuthViewModel()
    
    /// Shared connection view model for managing VPN connection state
    @StateObject private var connectionViewModel = ConnectionViewModel()
    
    /// Tracks the current scene phase for lifecycle management
    @Environment(\.scenePhase) private var scenePhase
    
    // MARK: - Body
    
    var body: some Scene {
        WindowGroup {
            ContentView()
                .environmentObject(authViewModel)
                .environmentObject(connectionViewModel)
                .onAppear {
                    setupAppearance()
                    initializeServices()
                }
        }
        .onChange(of: scenePhase) { oldPhase, newPhase in
            handleScenePhaseChange(from: oldPhase, to: newPhase)
        }
    }
    
    // MARK: - Private Methods
    
    /// Configures the global app appearance settings
    private func setupAppearance() {
        // Configure navigation bar appearance
        let navigationBarAppearance = UINavigationBarAppearance()
        navigationBarAppearance.configureWithOpaqueBackground()
        navigationBarAppearance.backgroundColor = UIColor(Theme.Colors.background)
        navigationBarAppearance.titleTextAttributes = [
            .foregroundColor: UIColor(Theme.Colors.textPrimary)
        ]
        navigationBarAppearance.largeTitleTextAttributes = [
            .foregroundColor: UIColor(Theme.Colors.textPrimary)
        ]
        
        UINavigationBar.appearance().standardAppearance = navigationBarAppearance
        UINavigationBar.appearance().scrollEdgeAppearance = navigationBarAppearance
        UINavigationBar.appearance().compactAppearance = navigationBarAppearance
        
        // Configure tab bar appearance
        let tabBarAppearance = UITabBarAppearance()
        tabBarAppearance.configureWithOpaqueBackground()
        tabBarAppearance.backgroundColor = UIColor(Theme.Colors.background)
        
        UITabBar.appearance().standardAppearance = tabBarAppearance
        UITabBar.appearance().scrollEdgeAppearance = tabBarAppearance
    }
    
    /// Initializes app services and checks for existing authentication
    private func initializeServices() {
        Task {
            // Check for existing valid session
            await authViewModel.checkExistingSession()
            
            // Initialize VPN manager if user is authenticated
            if authViewModel.isAuthenticated {
                await connectionViewModel.initializeVPNManager()
            }
        }
    }
    
    /// Handles app lifecycle phase changes
    /// - Parameters:
    ///   - oldPhase: The previous scene phase
    ///   - newPhase: The new scene phase
    private func handleScenePhaseChange(from oldPhase: ScenePhase, to newPhase: ScenePhase) {
        switch newPhase {
        case .active:
            // App became active - refresh connection status
            Task {
                await connectionViewModel.refreshConnectionStatus()
            }
        case .inactive:
            // App is transitioning
            break
        case .background:
            // App entered background - persist any necessary state
            break
        @unknown default:
            break
        }
    }
}

// MARK: - Content View

/// Root content view that manages navigation based on authentication state
struct ContentView: View {
    
    @EnvironmentObject private var authViewModel: AuthViewModel
    @EnvironmentObject private var connectionViewModel: ConnectionViewModel
    
    var body: some View {
        Group {
            if authViewModel.isLoading {
                // Show loading screen while checking auth state
                LoadingView()
            } else if authViewModel.isAuthenticated {
                // Show main app interface for authenticated users
                MainTabView()
            } else {
                // Show login screen for unauthenticated users
                LoginView()
            }
        }
        .animation(.easeInOut(duration: 0.3), value: authViewModel.isAuthenticated)
        .animation(.easeInOut(duration: 0.3), value: authViewModel.isLoading)
    }
}

// MARK: - Loading View

/// Simple loading view shown during initial app launch
struct LoadingView: View {
    var body: some View {
        ZStack {
            Theme.Colors.background
                .ignoresSafeArea()
            
            VStack(spacing: 24) {
                Image(systemName: "shield.checkered")
                    .font(.system(size: 64))
                    .foregroundColor(Theme.Colors.primary)
                
                Text("CasperVPN")
                    .font(Theme.Fonts.largeTitle)
                    .foregroundColor(Theme.Colors.textPrimary)
                
                ProgressView()
                    .progressViewStyle(CircularProgressViewStyle(tint: Theme.Colors.primary))
                    .scaleEffect(1.5)
            }
        }
    }
}

// MARK: - Main Tab View

/// Main tab-based navigation for authenticated users
struct MainTabView: View {
    
    @State private var selectedTab: Tab = .connection
    
    enum Tab: Int {
        case connection
        case servers
        case settings
    }
    
    var body: some View {
        TabView(selection: $selectedTab) {
            ConnectionView()
                .tabItem {
                    Label("Connect", systemImage: "power")
                }
                .tag(Tab.connection)
            
            ServerListView()
                .tabItem {
                    Label("Servers", systemImage: "server.rack")
                }
                .tag(Tab.servers)
            
            SettingsView()
                .tabItem {
                    Label("Settings", systemImage: "gearshape")
                }
                .tag(Tab.settings)
        }
        .tint(Theme.Colors.primary)
    }
}

// MARK: - Preview

#if DEBUG
#Preview {
    ContentView()
        .environmentObject(AuthViewModel())
        .environmentObject(ConnectionViewModel())
}
#endif
