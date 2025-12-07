//
//  Config.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//  Copyright © 2024 CasperVPN. All rights reserved.
//

import Foundation

/// Application configuration and constants.
/// Centralizes all configuration values for easy management.
enum AppConfig {
    
    // MARK: - Environment
    
    /// Current environment (development, staging, production)
    static let environment: Environment = {
        #if DEBUG
        return .development
        #else
        return .production
        #endif
    }()
    
    enum Environment: String {
        case development
        case staging
        case production
    }
    
    // MARK: - API Configuration
    
    /// Base URL for the API
    static var apiBaseURL: String {
        switch environment {
        case .development:
            return "http://localhost:5000"
        case .staging:
            return "https://staging-api.caspervpn.com"
        case .production:
            return "https://api.caspervpn.com"
        }
    }
    
    /// API version
    static let apiVersion = "v1"
    
    /// Request timeout in seconds
    static let requestTimeout: TimeInterval = 30
    
    /// Upload timeout in seconds
    static let uploadTimeout: TimeInterval = 120
    
    // MARK: - App Information
    
    /// App display name
    static let appName = "CasperVPN"
    
    /// App version from bundle
    static var appVersion: String {
        Bundle.main.appVersion
    }
    
    /// Build number from bundle
    static var buildNumber: String {
        Bundle.main.buildNumber
    }
    
    /// Full version string
    static var fullVersion: String {
        Bundle.main.fullVersion
    }
    
    /// User agent for API requests
    static var userAgent: String {
        "CasperVPN-iOS/\(appVersion) (\(UIDevice.current.systemName) \(UIDevice.current.systemVersion))"
    }
    
    // MARK: - Bundle Identifiers
    
    /// Main app bundle identifier
    static let mainBundleIdentifier = "com.caspervpn.app"
    
    /// Network extension bundle identifier
    static let tunnelBundleIdentifier = "com.caspervpn.app.tunnel"
    
    // MARK: - Keychain Configuration
    
    /// Keychain service name
    static let keychainServiceName = "com.caspervpn.keychain"
    
    /// Keychain access group (for sharing between app and extension)
    static let keychainAccessGroup: String? = {
        // Use app group for keychain sharing
        // Format: "TEAM_ID.com.caspervpn.shared"
        // Return nil to use default (no sharing)
        return nil
    }()
    
    // MARK: - App Groups
    
    /// App group identifier for sharing data between app and extensions
    static let appGroupIdentifier = "group.com.caspervpn.shared"
    
    // MARK: - VPN Configuration
    
    /// Default VPN protocol
    static let defaultVPNProtocol: VPNProtocolType = .wireGuard
    
    /// WireGuard default port
    static let wireGuardDefaultPort = 51820
    
    /// Keep-alive interval in seconds
    static let keepAliveInterval: Int = 25
    
    /// Default DNS servers
    static let defaultDNSServers = ["1.1.1.1", "1.0.0.1"]
    
    /// MTU value
    static let defaultMTU = 1420
    
    // MARK: - Feature Flags
    
    /// Whether analytics are enabled
    static let analyticsEnabled: Bool = {
        environment == .production
    }()
    
    /// Whether crash reporting is enabled
    static let crashReportingEnabled: Bool = {
        environment != .development
    }()
    
    /// Whether debug logging is enabled
    static let debugLoggingEnabled: Bool = {
        environment == .development
    }()
    
    // MARK: - Cache Configuration
    
    /// Server list cache duration in seconds
    static let serverListCacheDuration: TimeInterval = 300 // 5 minutes
    
    /// User data cache duration in seconds
    static let userDataCacheDuration: TimeInterval = 600 // 10 minutes
    
    // MARK: - UI Configuration
    
    /// Connection animation duration
    static let connectionAnimationDuration: TimeInterval = 0.5
    
    /// Minimum password length
    static let minimumPasswordLength = 8
    
    // MARK: - External URLs
    
    /// Help center URL
    static let helpCenterURL = URL(string: "https://help.caspervpn.com")!
    
    /// Privacy policy URL
    static let privacyPolicyURL = URL(string: "https://caspervpn.com/privacy")!
    
    /// Terms of service URL
    static let termsOfServiceURL = URL(string: "https://caspervpn.com/terms")!
    
    /// Support email
    static let supportEmail = "support@caspervpn.com"
    
    /// Website URL
    static let websiteURL = URL(string: "https://caspervpn.com")!
}

// MARK: - API Endpoints

/// Centralized API endpoint definitions.
enum APIEndpoints {
    
    // Auth endpoints
    static let register = "/api/auth/register"
    static let login = "/api/auth/login"
    static let logout = "/api/auth/logout"
    static let refreshToken = "/api/auth/refresh"
    static let forgotPassword = "/api/auth/forgot-password"
    static let resetPassword = "/api/auth/reset-password"
    static let verifyEmail = "/api/auth/verify-email"
    static let changePassword = "/api/auth/change-password"
    
    // User endpoints
    static let currentUser = "/api/users/me"
    static let updateProfile = "/api/users/me"
    static let deleteAccount = "/api/users/me"
    
    // Server endpoints
    static let servers = "/api/servers"
    static func server(id: UUID) -> String { "/api/servers/\(id)" }
    static func serverConfig(id: UUID) -> String { "/api/servers/\(id)/config" }
    static func connect(serverId: UUID) -> String { "/api/servers/\(serverId)/connect" }
    static func disconnect(serverId: UUID) -> String { "/api/servers/\(serverId)/disconnect" }
    static let recommendedServer = "/api/servers/recommended"
    
    // Subscription endpoints
    static let plans = "/api/plans"
    static func plan(id: UUID) -> String { "/api/plans/\(id)" }
    static let subscription = "/api/subscriptions/me"
    static let createSubscription = "/api/subscriptions"
    static let cancelSubscription = "/api/subscriptions/me"
    
    // Payment endpoints
    static let createCheckoutSession = "/api/payments/checkout-session"
    static let billingPortal = "/api/payments/billing-portal"
    static let paymentHistory = "/api/payments/history"
}

// MARK: - User Defaults Keys

/// Centralized UserDefaults key definitions.
enum UserDefaultsKeys {
    static let hasCompletedOnboarding = "hasCompletedOnboarding"
    static let lastSelectedServerId = "lastSelectedServerId"
    static let preferredProtocol = "preferredProtocol"
    static let autoConnect = "autoConnect"
    static let killSwitch = "killSwitch"
    static let notificationsEnabled = "notificationsEnabled"
    static let appearance = "appearance"
    static let lastSyncDate = "lastSyncDate"
}
