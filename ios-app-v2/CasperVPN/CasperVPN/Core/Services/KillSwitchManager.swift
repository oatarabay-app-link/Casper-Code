//
//  KillSwitchManager.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//

import Foundation
import NetworkExtension

/// Manages the VPN kill switch functionality using on-demand rules
final class KillSwitchManager: KillSwitchManagerProtocol {
    
    // MARK: - Singleton
    static let shared = KillSwitchManager()
    
    // MARK: - Properties
    @Published private(set) var isEnabled: Bool = false {
        didSet {
            UserDefaults.standard.set(isEnabled, forKey: killSwitchEnabledKey)
        }
    }
    
    private let killSwitchEnabledKey = "killSwitchEnabled"
    private let logger = ConnectionLogger.shared
    
    // MARK: - Trusted Networks (can be expanded to allow user configuration)
    private var trustedNetworks: [String] = []
    private let trustedNetworksKey = "trustedNetworks"
    
    // MARK: - Initialization
    private init() {
        // Load saved state
        isEnabled = UserDefaults.standard.bool(forKey: killSwitchEnabledKey)
        trustedNetworks = UserDefaults.standard.stringArray(forKey: trustedNetworksKey) ?? []
        
        logger.log("Kill Switch Manager initialized, enabled: \(isEnabled)", level: .debug)
    }
    
    // MARK: - Public Methods
    
    /// Enable the kill switch
    func enable() async throws {
        logger.log("Enabling kill switch", level: .info)
        
        guard !isEnabled else {
            logger.log("Kill switch already enabled", level: .debug)
            return
        }
        
        do {
            try await updateVPNConfiguration(enabled: true)
            isEnabled = true
            logger.log("Kill switch enabled successfully", level: .info)
        } catch {
            logger.log("Failed to enable kill switch: \(error.localizedDescription)", level: .error)
            throw VPNError.killSwitchActivationFailed
        }
    }
    
    /// Disable the kill switch
    func disable() async throws {
        logger.log("Disabling kill switch", level: .info)
        
        guard isEnabled else {
            logger.log("Kill switch already disabled", level: .debug)
            return
        }
        
        do {
            try await updateVPNConfiguration(enabled: false)
            isEnabled = false
            logger.log("Kill switch disabled successfully", level: .info)
        } catch {
            logger.log("Failed to disable kill switch: \(error.localizedDescription)", level: .error)
            throw VPNError.killSwitchDeactivationFailed
        }
    }
    
    /// Toggle kill switch state
    func toggle() async throws {
        if isEnabled {
            try await disable()
        } else {
            try await enable()
        }
    }
    
    /// Configure on-demand rules for the VPN
    /// - Returns: Array of NEOnDemandRule configured for kill switch behavior
    func configureOnDemandRules() -> [NEOnDemandRule] {
        var rules: [NEOnDemandRule] = []
        
        // Rule 1: Disconnect on trusted WiFi networks (if configured)
        if !trustedNetworks.isEmpty {
            let disconnectRule = NEOnDemandRuleDisconnect()
            disconnectRule.ssidMatch = trustedNetworks
            disconnectRule.interfaceTypeMatch = .wiFi
            rules.append(disconnectRule)
        }
        
        // Rule 2: Connect on any WiFi network
        let connectWiFiRule = NEOnDemandRuleConnect()
        connectWiFiRule.interfaceTypeMatch = .wiFi
        rules.append(connectWiFiRule)
        
        // Rule 3: Connect on cellular
        let connectCellularRule = NEOnDemandRuleConnect()
        connectCellularRule.interfaceTypeMatch = .cellular
        rules.append(connectCellularRule)
        
        // Rule 4: Connect by default (fallback)
        let defaultConnectRule = NEOnDemandRuleConnect()
        rules.append(defaultConnectRule)
        
        logger.log("Configured \(rules.count) on-demand rules", level: .debug)
        
        return rules
    }
    
    /// Configure strict on-demand rules that block all traffic when VPN is not connected
    /// - Returns: Array of NEOnDemandRule for strict kill switch mode
    func configureStrictOnDemandRules() -> [NEOnDemandRule] {
        var rules: [NEOnDemandRule] = []
        
        // Evaluate connection rule - always connect
        let evaluateRule = NEOnDemandRuleEvaluateConnection()
        evaluateRule.interfaceTypeMatch = .any
        
        // Configure connection rules for domains
        let connectionRule = NEEvaluateConnectionRule(
            matchDomains: ["*"],
            andAction: .connectIfNeeded
        )
        evaluateRule.connectionRules = [connectionRule]
        rules.append(evaluateRule)
        
        // Connect rule for all traffic
        let connectRule = NEOnDemandRuleConnect()
        rules.append(connectRule)
        
        logger.log("Configured strict on-demand rules", level: .debug)
        
        return rules
    }
    
    // MARK: - Trusted Networks Management
    
    /// Add a network to the trusted networks list
    func addTrustedNetwork(_ ssid: String) {
        guard !trustedNetworks.contains(ssid) else { return }
        trustedNetworks.append(ssid)
        saveTrustedNetworks()
        logger.log("Added trusted network: \(ssid)", level: .info)
    }
    
    /// Remove a network from the trusted networks list
    func removeTrustedNetwork(_ ssid: String) {
        trustedNetworks.removeAll { $0 == ssid }
        saveTrustedNetworks()
        logger.log("Removed trusted network: \(ssid)", level: .info)
    }
    
    /// Get the list of trusted networks
    func getTrustedNetworks() -> [String] {
        return trustedNetworks
    }
    
    /// Clear all trusted networks
    func clearTrustedNetworks() {
        trustedNetworks = []
        saveTrustedNetworks()
        logger.log("Cleared all trusted networks", level: .info)
    }
    
    // MARK: - Private Methods
    
    private func updateVPNConfiguration(enabled: Bool) async throws {
        let managers = try await NETunnelProviderManager.loadAllFromPreferences()
        
        guard let manager = managers.first else {
            // No VPN configuration exists yet, just save the preference
            return
        }
        
        if enabled {
            manager.onDemandRules = configureOnDemandRules()
            manager.isOnDemandEnabled = true
        } else {
            manager.onDemandRules = []
            manager.isOnDemandEnabled = false
        }
        
        try await manager.saveToPreferences()
    }
    
    private func saveTrustedNetworks() {
        UserDefaults.standard.set(trustedNetworks, forKey: trustedNetworksKey)
    }
}

// MARK: - Kill Switch Mode
enum KillSwitchMode: String, CaseIterable, Codable {
    case off = "Off"
    case soft = "Soft"  // Reconnect when network changes
    case strict = "Strict"  // Block all traffic when VPN disconnects
    
    var description: String {
        switch self {
        case .off:
            return "Kill switch is disabled"
        case .soft:
            return "Automatically reconnect when network changes"
        case .strict:
            return "Block all internet traffic when VPN disconnects"
        }
    }
}
