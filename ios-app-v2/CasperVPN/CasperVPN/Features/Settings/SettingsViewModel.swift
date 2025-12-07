//
//  SettingsViewModel.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//

import Foundation
import Combine

/// ViewModel for managing app settings including kill switch and preferences
@MainActor
final class SettingsViewModel: ObservableObject {
    
    // MARK: - Published Properties
    @Published var isKillSwitchEnabled: Bool = false {
        didSet {
            if oldValue != isKillSwitchEnabled {
                Task {
                    await toggleKillSwitch()
                }
            }
        }
    }
    
    @Published var isAutoConnectEnabled: Bool = false {
        didSet {
            UserDefaults.standard.set(isAutoConnectEnabled, forKey: UserDefaultsKeys.autoConnect)
        }
    }
    
    @Published var isNotificationsEnabled: Bool = true {
        didSet {
            UserDefaults.standard.set(isNotificationsEnabled, forKey: UserDefaultsKeys.notifications)
        }
    }
    
    @Published var selectedProtocol: VPNProtocolType = .wireGuard {
        didSet {
            UserDefaults.standard.set(selectedProtocol.rawValue, forKey: UserDefaultsKeys.vpnProtocol)
        }
    }
    
    @Published private(set) var isLoading: Bool = false
    @Published private(set) var error: String?
    @Published var showError: Bool = false
    @Published var showExportLogs: Bool = false
    @Published var showLogViewer: Bool = false
    @Published var showAbout: Bool = false
    
    // MARK: - User Info
    @Published var currentUser: User?
    
    // MARK: - Dependencies
    private let killSwitchManager: KillSwitchManager
    private let connectionLogger: ConnectionLogger
    private var cancellables = Set<AnyCancellable>()
    
    // MARK: - User Defaults Keys
    private enum UserDefaultsKeys {
        static let autoConnect = "autoConnectEnabled"
        static let notifications = "notificationsEnabled"
        static let vpnProtocol = "selectedVPNProtocol"
    }
    
    // MARK: - Initialization
    init(killSwitchManager: KillSwitchManager = .shared,
         connectionLogger: ConnectionLogger = .shared) {
        self.killSwitchManager = killSwitchManager
        self.connectionLogger = connectionLogger
        
        loadSettings()
    }
    
    // MARK: - Public Methods
    
    func loadSettings() {
        // Load from UserDefaults and managers
        isKillSwitchEnabled = killSwitchManager.isEnabled
        isAutoConnectEnabled = UserDefaults.standard.bool(forKey: UserDefaultsKeys.autoConnect)
        isNotificationsEnabled = UserDefaults.standard.object(forKey: UserDefaultsKeys.notifications) as? Bool ?? true
        
        if let protocolRaw = UserDefaults.standard.string(forKey: UserDefaultsKeys.vpnProtocol),
           let vpnProtocol = VPNProtocolType(rawValue: protocolRaw) {
            selectedProtocol = vpnProtocol
        }
    }
    
    func exportLogs() -> String {
        return connectionLogger.exportLogs()
    }
    
    func clearLogs() {
        connectionLogger.clearLogs()
    }
    
    func getRecentLogs(count: Int = 100) -> [ConnectionLogEntry] {
        return connectionLogger.getRecentLogs(count: count)
    }
    
    // MARK: - Kill Switch
    
    private func toggleKillSwitch() async {
        isLoading = true
        error = nil
        
        do {
            if isKillSwitchEnabled {
                try await killSwitchManager.enable()
                connectionLogger.log("Kill switch enabled", level: .info)
            } else {
                try await killSwitchManager.disable()
                connectionLogger.log("Kill switch disabled", level: .info)
            }
        } catch let vpnError as VPNError {
            error = vpnError.localizedDescription
            showError = true
            // Revert the toggle
            isKillSwitchEnabled = !isKillSwitchEnabled
        } catch {
            self.error = error.localizedDescription
            showError = true
            isKillSwitchEnabled = !isKillSwitchEnabled
        }
        
        isLoading = false
    }
    
    // MARK: - Trusted Networks
    
    func getTrustedNetworks() -> [String] {
        return killSwitchManager.getTrustedNetworks()
    }
    
    func addTrustedNetwork(_ ssid: String) {
        killSwitchManager.addTrustedNetwork(ssid)
    }
    
    func removeTrustedNetwork(_ ssid: String) {
        killSwitchManager.removeTrustedNetwork(ssid)
    }
}

// MARK: - VPN Protocol Type
enum VPNProtocolType: String, CaseIterable, Identifiable {
    case wireGuard = "WireGuard"
    case openVPN = "OpenVPN"
    case ikev2 = "IKEv2"
    
    var id: String { rawValue }
    
    var description: String {
        switch self {
        case .wireGuard:
            return "Fast and secure, recommended for most users"
        case .openVPN:
            return "Widely compatible, good for restricted networks"
        case .ikev2:
            return "Built-in iOS support, good battery life"
        }
    }
    
    var icon: String {
        switch self {
        case .wireGuard:
            return "bolt.shield.fill"
        case .openVPN:
            return "lock.shield.fill"
        case .ikev2:
            return "shield.fill"
        }
    }
}

// MARK: - Settings Section
enum SettingsSection: CaseIterable, Identifiable {
    case connection
    case security
    case notifications
    case support
    case about
    
    var id: String { title }
    
    var title: String {
        switch self {
        case .connection: return "Connection"
        case .security: return "Security"
        case .notifications: return "Notifications"
        case .support: return "Support"
        case .about: return "About"
        }
    }
    
    var icon: String {
        switch self {
        case .connection: return "network"
        case .security: return "lock.shield"
        case .notifications: return "bell"
        case .support: return "questionmark.circle"
        case .about: return "info.circle"
        }
    }
}
