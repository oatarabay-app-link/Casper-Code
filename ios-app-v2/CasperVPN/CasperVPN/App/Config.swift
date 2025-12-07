//
//  Config.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//

import Foundation

enum Config {
    // MARK: - API Configuration
    static let apiBaseURL: String = {
        #if DEBUG
        return ProcessInfo.processInfo.environment["API_BASE_URL"] ?? "https://api.caspervpn.com"
        #else
        return "https://api.caspervpn.com"
        #endif
    }()
    
    static let apiVersion = "v1"
    
    static var fullAPIURL: String {
        return "\(apiBaseURL)/api/\(apiVersion)"
    }
    
    // MARK: - App Configuration
    static let appName = "CasperVPN"
    static let appBundleIdentifier = "com.caspervpn.app"
    static let tunnelBundleIdentifier = "com.caspervpn.app.tunnel"
    
    // MARK: - Keychain Configuration
    static let keychainService = "com.caspervpn.keychain"
    static let keychainAccessGroup: String? = nil // Set for shared keychain access
    
    // MARK: - VPN Configuration
    static let vpnLocalizedDescription = "CasperVPN"
    static let defaultMTU = 1280
    static let defaultPersistentKeepalive = 25
    
    // MARK: - Network Configuration
    static let connectionTimeout: TimeInterval = 30
    static let requestTimeout: TimeInterval = 15
    static let maxRetryAttempts = 3
    static let retryBaseDelay: TimeInterval = 1.0
    
    // MARK: - WireGuard Configuration
    static let wireGuardDefaultPort: UInt16 = 51820
    static let wireGuardDefaultDNS = ["1.1.1.1", "1.0.0.1"]
    static let wireGuardDefaultAllowedIPs = ["0.0.0.0/0", "::/0"]
    
    // MARK: - Feature Flags
    static let enableKillSwitch = true
    static let enableAutoReconnect = true
    static let enableConnectionLogging = true
    
    // MARK: - Debug Configuration
    #if DEBUG
    static let isDebugMode = true
    static let logLevel: LogLevel = .debug
    #else
    static let isDebugMode = false
    static let logLevel: LogLevel = .info
    #endif
}

// MARK: - API Endpoints
extension Config {
    enum Endpoints {
        static let login = "/auth/login"
        static let register = "/auth/register"
        static let refreshToken = "/auth/refresh"
        static let logout = "/auth/logout"
        
        static let servers = "/servers"
        static func serverConfig(id: String) -> String { "/servers/\(id)/config" }
        static func serverConnect(id: String) -> String { "/servers/\(id)/connect" }
        static func serverDisconnect(id: String) -> String { "/servers/\(id)/disconnect" }
        
        static let user = "/user"
        static let subscription = "/user/subscription"
    }
}

// MARK: - Log Level
enum LogLevel: Int, Comparable {
    case debug = 0
    case info = 1
    case warning = 2
    case error = 3
    
    static func < (lhs: LogLevel, rhs: LogLevel) -> Bool {
        return lhs.rawValue < rhs.rawValue
    }
}
