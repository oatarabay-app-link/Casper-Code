//
//  SettingsViewModel.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//  Copyright © 2024 CasperVPN. All rights reserved.
//

import Foundation
import Combine
import UIKit

/// ViewModel for managing app settings and preferences.
@MainActor
final class SettingsViewModel: ObservableObject {
    
    // MARK: - Published Properties
    
    /// Preferred VPN protocol
    @Published var preferredProtocol: VPNProtocolType {
        didSet {
            savePreference(key: .preferredProtocol, value: preferredProtocol.rawValue)
        }
    }
    
    /// Auto-connect on app launch
    @Published var autoConnect: Bool {
        didSet {
            savePreference(key: .autoConnect, value: autoConnect)
        }
    }
    
    /// Kill switch enabled
    @Published var killSwitch: Bool {
        didSet {
            savePreference(key: .killSwitch, value: killSwitch)
        }
    }
    
    /// Push notifications enabled
    @Published var notificationsEnabled: Bool {
        didSet {
            savePreference(key: .notificationsEnabled, value: notificationsEnabled)
            updateNotificationSettings()
        }
    }
    
    /// App appearance mode
    @Published var appearance: AppAppearance {
        didSet {
            savePreference(key: .appearance, value: appearance.rawValue)
            updateAppearance()
        }
    }
    
    /// Whether data is loading
    @Published var isLoading = false
    
    /// Current error message
    @Published var errorMessage: String?
    
    /// Whether to show error alert
    @Published var showError = false
    
    // MARK: - Properties
    
    private let userDefaults: UserDefaults
    private var cancellables = Set<AnyCancellable>()
    
    // MARK: - Preference Keys
    
    private enum PreferenceKey: String {
        case preferredProtocol = "com.caspervpn.preferredProtocol"
        case autoConnect = "com.caspervpn.autoConnect"
        case killSwitch = "com.caspervpn.killSwitch"
        case notificationsEnabled = "com.caspervpn.notificationsEnabled"
        case appearance = "com.caspervpn.appearance"
        case lastSelectedServerId = "com.caspervpn.lastSelectedServerId"
    }
    
    // MARK: - Initialization
    
    init(userDefaults: UserDefaults = .standard) {
        self.userDefaults = userDefaults
        
        // Load saved preferences
        self.preferredProtocol = Self.loadProtocol(from: userDefaults)
        self.autoConnect = userDefaults.bool(forKey: PreferenceKey.autoConnect.rawValue)
        self.killSwitch = userDefaults.bool(forKey: PreferenceKey.killSwitch.rawValue)
        self.notificationsEnabled = userDefaults.bool(forKey: PreferenceKey.notificationsEnabled.rawValue)
        self.appearance = Self.loadAppearance(from: userDefaults)
    }
    
    // MARK: - Public Methods
    
    /// Opens the billing portal in Safari
    func openBillingPortal() async {
        isLoading = true
        
        // TODO: Implement billing portal API call
        // For now, open the web portal
        if let url = URL(string: "https://caspervpn.com/account/billing") {
            await MainActor.run {
                UIApplication.shared.open(url)
            }
        }
        
        isLoading = false
    }
    
    /// Deletes the user's account
    func deleteAccount() async {
        isLoading = true
        clearError()
        
        // TODO: Implement account deletion API call
        // This would call the UserService to delete the account
        
        isLoading = false
    }
    
    /// Saves the last selected server ID
    func saveLastSelectedServer(id: UUID) {
        userDefaults.set(id.uuidString, forKey: PreferenceKey.lastSelectedServerId.rawValue)
    }
    
    /// Gets the last selected server ID
    func getLastSelectedServerId() -> UUID? {
        guard let idString = userDefaults.string(forKey: PreferenceKey.lastSelectedServerId.rawValue) else {
            return nil
        }
        return UUID(uuidString: idString)
    }
    
    /// Resets all settings to defaults
    func resetToDefaults() {
        preferredProtocol = .wireGuard
        autoConnect = false
        killSwitch = false
        notificationsEnabled = true
        appearance = .system
        
        // Clear last selected server
        userDefaults.removeObject(forKey: PreferenceKey.lastSelectedServerId.rawValue)
    }
    
    /// Exports settings as a dictionary
    func exportSettings() -> [String: Any] {
        [
            "preferredProtocol": preferredProtocol.rawValue,
            "autoConnect": autoConnect,
            "killSwitch": killSwitch,
            "notificationsEnabled": notificationsEnabled,
            "appearance": appearance.rawValue
        ]
    }
    
    /// Imports settings from a dictionary
    func importSettings(_ settings: [String: Any]) {
        if let protocolString = settings["preferredProtocol"] as? String,
           let proto = VPNProtocolType(rawValue: protocolString) {
            preferredProtocol = proto
        }
        
        if let autoConnect = settings["autoConnect"] as? Bool {
            self.autoConnect = autoConnect
        }
        
        if let killSwitch = settings["killSwitch"] as? Bool {
            self.killSwitch = killSwitch
        }
        
        if let notificationsEnabled = settings["notificationsEnabled"] as? Bool {
            self.notificationsEnabled = notificationsEnabled
        }
        
        if let appearanceString = settings["appearance"] as? String,
           let appearance = AppAppearance(rawValue: appearanceString) {
            self.appearance = appearance
        }
    }
    
    // MARK: - Private Methods
    
    private func savePreference<T>(key: PreferenceKey, value: T) {
        userDefaults.set(value, forKey: key.rawValue)
    }
    
    private static func loadProtocol(from userDefaults: UserDefaults) -> VPNProtocolType {
        guard let rawValue = userDefaults.string(forKey: PreferenceKey.preferredProtocol.rawValue),
              let proto = VPNProtocolType(rawValue: rawValue) else {
            return .wireGuard
        }
        return proto
    }
    
    private static func loadAppearance(from userDefaults: UserDefaults) -> AppAppearance {
        guard let rawValue = userDefaults.string(forKey: PreferenceKey.appearance.rawValue),
              let appearance = AppAppearance(rawValue: rawValue) else {
            return .system
        }
        return appearance
    }
    
    private func updateNotificationSettings() {
        if notificationsEnabled {
            UNUserNotificationCenter.current().requestAuthorization(options: [.alert, .sound, .badge]) { granted, error in
                if let error = error {
                    Task { @MainActor in
                        self.errorMessage = "Failed to enable notifications: \(error.localizedDescription)"
                        self.showError = true
                    }
                }
            }
        }
    }
    
    private func updateAppearance() {
        let scenes = UIApplication.shared.connectedScenes
        let windowScene = scenes.first as? UIWindowScene
        let window = windowScene?.windows.first
        
        switch appearance {
        case .system:
            window?.overrideUserInterfaceStyle = .unspecified
        case .light:
            window?.overrideUserInterfaceStyle = .light
        case .dark:
            window?.overrideUserInterfaceStyle = .dark
        }
    }
    
    private func handleError(_ error: Error) {
        if let apiError = error as? APIError {
            errorMessage = apiError.errorDescription
        } else {
            errorMessage = error.localizedDescription
        }
        showError = true
    }
    
    private func clearError() {
        errorMessage = nil
        showError = false
    }
}

// MARK: - User Notification Import

import UserNotifications
