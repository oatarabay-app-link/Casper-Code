//
//  VPNError.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//

import Foundation

/// Comprehensive error enum for all VPN-related errors
enum VPNError: LocalizedError, Equatable {
    
    // MARK: - Connection Errors
    case connectionFailed(reason: String)
    case connectionTimeout
    case connectionCancelled
    case alreadyConnected
    case alreadyDisconnected
    case disconnectionFailed(reason: String)
    
    // MARK: - Configuration Errors
    case invalidConfiguration(reason: String)
    case configurationNotFound
    case configurationParseFailed
    case missingPrivateKey
    case missingPublicKey
    case invalidEndpoint
    case invalidAllowedIPs
    
    // MARK: - Tunnel Errors
    case tunnelCreationFailed(reason: String)
    case tunnelStartFailed(reason: String)
    case tunnelStopFailed(reason: String)
    case tunnelNotFound
    case tunnelProviderError(code: Int)
    case packetTunnelError(reason: String)
    
    // MARK: - WireGuard Errors
    case wireGuardInitializationFailed
    case wireGuardKeyGenerationFailed
    case wireGuardInvalidKey
    case wireGuardHandshakeFailed
    case wireGuardAdapterError(reason: String)
    
    // MARK: - Network Errors
    case networkUnavailable
    case networkChanged
    case dnsResolutionFailed
    case serverUnreachable
    case noInternetConnection
    
    // MARK: - Server Errors
    case serverNotSelected
    case serverUnavailable
    case serverOverloaded
    case serverConfigFetchFailed(reason: String)
    
    // MARK: - Authentication Errors
    case notAuthenticated
    case sessionExpired
    case subscriptionExpired
    case subscriptionRequired
    
    // MARK: - Permission Errors
    case vpnPermissionDenied
    case vpnPermissionNotDetermined
    case networkExtensionNotConfigured
    
    // MARK: - Kill Switch Errors
    case killSwitchActivationFailed
    case killSwitchDeactivationFailed
    
    // MARK: - Generic Errors
    case unknown(reason: String)
    case internalError(reason: String)
    
    // MARK: - LocalizedError Implementation
    var errorDescription: String? {
        switch self {
        // Connection Errors
        case .connectionFailed(let reason):
            return "Connection failed: \(reason)"
        case .connectionTimeout:
            return "Connection timed out. Please try again."
        case .connectionCancelled:
            return "Connection was cancelled."
        case .alreadyConnected:
            return "Already connected to VPN."
        case .alreadyDisconnected:
            return "VPN is already disconnected."
        case .disconnectionFailed(let reason):
            return "Failed to disconnect: \(reason)"
            
        // Configuration Errors
        case .invalidConfiguration(let reason):
            return "Invalid VPN configuration: \(reason)"
        case .configurationNotFound:
            return "VPN configuration not found."
        case .configurationParseFailed:
            return "Failed to parse VPN configuration."
        case .missingPrivateKey:
            return "VPN private key is missing."
        case .missingPublicKey:
            return "Server public key is missing."
        case .invalidEndpoint:
            return "Invalid server endpoint."
        case .invalidAllowedIPs:
            return "Invalid allowed IP addresses."
            
        // Tunnel Errors
        case .tunnelCreationFailed(let reason):
            return "Failed to create VPN tunnel: \(reason)"
        case .tunnelStartFailed(let reason):
            return "Failed to start VPN tunnel: \(reason)"
        case .tunnelStopFailed(let reason):
            return "Failed to stop VPN tunnel: \(reason)"
        case .tunnelNotFound:
            return "VPN tunnel not found."
        case .tunnelProviderError(let code):
            return "Tunnel provider error (code: \(code))"
        case .packetTunnelError(let reason):
            return "Packet tunnel error: \(reason)"
            
        // WireGuard Errors
        case .wireGuardInitializationFailed:
            return "Failed to initialize WireGuard."
        case .wireGuardKeyGenerationFailed:
            return "Failed to generate WireGuard keys."
        case .wireGuardInvalidKey:
            return "Invalid WireGuard key format."
        case .wireGuardHandshakeFailed:
            return "WireGuard handshake failed. Server may be unreachable."
        case .wireGuardAdapterError(let reason):
            return "WireGuard adapter error: \(reason)"
            
        // Network Errors
        case .networkUnavailable:
            return "Network is unavailable."
        case .networkChanged:
            return "Network connection changed."
        case .dnsResolutionFailed:
            return "DNS resolution failed."
        case .serverUnreachable:
            return "Server is unreachable."
        case .noInternetConnection:
            return "No internet connection."
            
        // Server Errors
        case .serverNotSelected:
            return "Please select a server."
        case .serverUnavailable:
            return "Selected server is unavailable."
        case .serverOverloaded:
            return "Server is overloaded. Please try another server."
        case .serverConfigFetchFailed(let reason):
            return "Failed to fetch server configuration: \(reason)"
            
        // Authentication Errors
        case .notAuthenticated:
            return "Please log in to continue."
        case .sessionExpired:
            return "Your session has expired. Please log in again."
        case .subscriptionExpired:
            return "Your subscription has expired."
        case .subscriptionRequired:
            return "A premium subscription is required for this server."
            
        // Permission Errors
        case .vpnPermissionDenied:
            return "VPN permission was denied. Please enable it in Settings."
        case .vpnPermissionNotDetermined:
            return "VPN permission is required."
        case .networkExtensionNotConfigured:
            return "VPN extension is not properly configured."
            
        // Kill Switch Errors
        case .killSwitchActivationFailed:
            return "Failed to activate kill switch."
        case .killSwitchDeactivationFailed:
            return "Failed to deactivate kill switch."
            
        // Generic Errors
        case .unknown(let reason):
            return "An unknown error occurred: \(reason)"
        case .internalError(let reason):
            return "Internal error: \(reason)"
        }
    }
    
    /// User-friendly error message for display in UI
    var userFriendlyMessage: String {
        switch self {
        case .connectionTimeout, .serverUnreachable, .wireGuardHandshakeFailed:
            return "Unable to connect to server. Please check your internet connection or try a different server."
        case .networkUnavailable, .noInternetConnection:
            return "Please check your internet connection and try again."
        case .notAuthenticated, .sessionExpired:
            return "Please log in to use CasperVPN."
        case .subscriptionExpired, .subscriptionRequired:
            return "Upgrade your subscription to access this feature."
        case .vpnPermissionDenied:
            return "VPN permission is required. Please enable it in your device Settings."
        case .serverOverloaded:
            return "This server is busy. Please try another server."
        default:
            return errorDescription ?? "An error occurred. Please try again."
        }
    }
    
    /// Whether the error is recoverable through retry
    var isRetryable: Bool {
        switch self {
        case .connectionTimeout, .networkChanged, .serverUnreachable,
             .wireGuardHandshakeFailed, .serverOverloaded:
            return true
        default:
            return false
        }
    }
    
    /// Suggested action for the error
    var suggestedAction: ErrorAction {
        switch self {
        case .notAuthenticated, .sessionExpired:
            return .login
        case .subscriptionExpired, .subscriptionRequired:
            return .upgrade
        case .vpnPermissionDenied, .vpnPermissionNotDetermined:
            return .openSettings
        case .serverNotSelected:
            return .selectServer
        case .connectionTimeout, .serverUnreachable, .wireGuardHandshakeFailed:
            return .retry
        case .serverOverloaded:
            return .changeServer
        default:
            return .dismiss
        }
    }
}

// MARK: - Error Action
enum ErrorAction {
    case dismiss
    case retry
    case login
    case upgrade
    case openSettings
    case selectServer
    case changeServer
    case contactSupport
}

// MARK: - Error Conversion
extension VPNError {
    /// Convert from NETunnelProviderError
    static func fromTunnelProviderError(_ error: Error) -> VPNError {
        let nsError = error as NSError
        
        switch nsError.code {
        case 1: // configurationInvalid
            return .invalidConfiguration(reason: error.localizedDescription)
        case 2: // configurationDisabled
            return .tunnelNotFound
        case 3: // configurationReadWriteFailed
            return .tunnelCreationFailed(reason: error.localizedDescription)
        case 4: // configurationStale
            return .configurationNotFound
        case 5: // configurationCannotBeRemoved
            return .tunnelStopFailed(reason: error.localizedDescription)
        default:
            return .tunnelProviderError(code: nsError.code)
        }
    }
}
