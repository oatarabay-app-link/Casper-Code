//
//  AppDelegate.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//  Copyright © 2024 CasperVPN. All rights reserved.
//

import UIKit
import UserNotifications
import NetworkExtension

/// AppDelegate handles UIKit-based lifecycle events including push notifications setup.
/// Bridges UIKit callbacks to SwiftUI's App lifecycle where needed.
class AppDelegate: NSObject, UIApplicationDelegate {
    
    // MARK: - Properties
    
    /// Device token for push notifications
    private(set) var deviceToken: String?
    
    /// Logger for debugging
    private let logger = AppLogger.shared
    
    // MARK: - Application Lifecycle
    
    func application(
        _ application: UIApplication,
        didFinishLaunchingWithOptions launchOptions: [UIApplication.LaunchOptionsKey: Any]? = nil
    ) -> Bool {
        logger.info("Application did finish launching")
        
        // Configure logging
        configureLogging()
        
        // Request notification permissions
        requestNotificationPermissions()
        
        // Register for remote notifications
        application.registerForRemoteNotifications()
        
        // Configure Network Extension observer
        configureNetworkExtensionObserver()
        
        return true
    }
    
    func application(
        _ application: UIApplication,
        configurationForConnecting connectingSceneSession: UISceneSession,
        options: UIScene.ConnectionOptions
    ) -> UISceneConfiguration {
        let configuration = UISceneConfiguration(
            name: nil,
            sessionRole: connectingSceneSession.role
        )
        return configuration
    }
    
    func applicationWillTerminate(_ application: UIApplication) {
        logger.info("Application will terminate")
        // Perform cleanup if needed
    }
    
    // MARK: - Push Notifications
    
    func application(
        _ application: UIApplication,
        didRegisterForRemoteNotificationsWithDeviceToken deviceToken: Data
    ) {
        let tokenString = deviceToken.map { String(format: "%02.2hhx", $0) }.joined()
        self.deviceToken = tokenString
        logger.info("Successfully registered for remote notifications")
        
        // Send device token to backend for push notification targeting
        Task {
            await sendDeviceTokenToBackend(token: tokenString)
        }
    }
    
    func application(
        _ application: UIApplication,
        didFailToRegisterForRemoteNotificationsWithError error: Error
    ) {
        logger.error("Failed to register for remote notifications: \(error.localizedDescription)")
    }
    
    func application(
        _ application: UIApplication,
        didReceiveRemoteNotification userInfo: [AnyHashable: Any],
        fetchCompletionHandler completionHandler: @escaping (UIBackgroundFetchResult) -> Void
    ) {
        logger.info("Received remote notification")
        handleRemoteNotification(userInfo: userInfo)
        completionHandler(.newData)
    }
    
    // MARK: - Private Methods
    
    /// Configures the logging system for the application
    private func configureLogging() {
        #if DEBUG
        AppLogger.shared.logLevel = .debug
        #else
        AppLogger.shared.logLevel = .info
        #endif
    }
    
    /// Requests push notification permissions from the user
    private func requestNotificationPermissions() {
        let center = UNUserNotificationCenter.current()
        center.delegate = self
        
        center.requestAuthorization(options: [.alert, .sound, .badge]) { granted, error in
            if let error = error {
                self.logger.error("Notification authorization error: \(error.localizedDescription)")
                return
            }
            
            if granted {
                self.logger.info("Notification permissions granted")
            } else {
                self.logger.info("Notification permissions denied")
            }
        }
    }
    
    /// Configures observer for Network Extension status changes
    private func configureNetworkExtensionObserver() {
        NotificationCenter.default.addObserver(
            self,
            selector: #selector(vpnStatusDidChange(_:)),
            name: .NEVPNStatusDidChange,
            object: nil
        )
    }
    
    /// Handles VPN status change notifications
    @objc private func vpnStatusDidChange(_ notification: Notification) {
        guard let connection = notification.object as? NEVPNConnection else { return }
        
        let status = connection.status
        logger.debug("VPN status changed to: \(status.description)")
        
        // Post custom notification for UI updates
        NotificationCenter.default.post(
            name: .vpnConnectionStatusChanged,
            object: nil,
            userInfo: ["status": status]
        )
    }
    
    /// Sends the device token to the backend server
    private func sendDeviceTokenToBackend(token: String) async {
        do {
            try await APIClient.shared.registerDeviceToken(token)
            logger.info("Device token registered with backend")
        } catch {
            logger.error("Failed to register device token: \(error.localizedDescription)")
        }
    }
    
    /// Handles incoming remote notifications
    private func handleRemoteNotification(userInfo: [AnyHashable: Any]) {
        // Parse notification type
        guard let type = userInfo["type"] as? String else {
            logger.warning("Received notification without type")
            return
        }
        
        switch type {
        case "connection_alert":
            handleConnectionAlert(userInfo: userInfo)
        case "subscription_update":
            handleSubscriptionUpdate(userInfo: userInfo)
        case "server_maintenance":
            handleServerMaintenance(userInfo: userInfo)
        default:
            logger.info("Received notification of unknown type: \(type)")
        }
    }
    
    private func handleConnectionAlert(userInfo: [AnyHashable: Any]) {
        logger.info("Processing connection alert notification")
        NotificationCenter.default.post(
            name: .connectionAlertReceived,
            object: nil,
            userInfo: userInfo
        )
    }
    
    private func handleSubscriptionUpdate(userInfo: [AnyHashable: Any]) {
        logger.info("Processing subscription update notification")
        NotificationCenter.default.post(
            name: .subscriptionUpdateReceived,
            object: nil,
            userInfo: userInfo
        )
    }
    
    private func handleServerMaintenance(userInfo: [AnyHashable: Any]) {
        logger.info("Processing server maintenance notification")
        NotificationCenter.default.post(
            name: .serverMaintenanceReceived,
            object: nil,
            userInfo: userInfo
        )
    }
}

// MARK: - UNUserNotificationCenterDelegate

extension AppDelegate: UNUserNotificationCenterDelegate {
    
    /// Called when a notification is delivered while the app is in the foreground
    func userNotificationCenter(
        _ center: UNUserNotificationCenter,
        willPresent notification: UNNotification,
        withCompletionHandler completionHandler: @escaping (UNNotificationPresentationOptions) -> Void
    ) {
        logger.info("Notification will present in foreground")
        completionHandler([.banner, .sound, .badge])
    }
    
    /// Called when the user interacts with a notification
    func userNotificationCenter(
        _ center: UNUserNotificationCenter,
        didReceive response: UNNotificationResponse,
        withCompletionHandler completionHandler: @escaping () -> Void
    ) {
        let userInfo = response.notification.request.content.userInfo
        logger.info("User interacted with notification")
        handleRemoteNotification(userInfo: userInfo)
        completionHandler()
    }
}

// MARK: - NEVPNStatus Extension

extension NEVPNStatus {
    var description: String {
        switch self {
        case .invalid: return "Invalid"
        case .disconnected: return "Disconnected"
        case .connecting: return "Connecting"
        case .connected: return "Connected"
        case .reasserting: return "Reasserting"
        case .disconnecting: return "Disconnecting"
        @unknown default: return "Unknown"
        }
    }
}

// MARK: - Notification Names

extension Notification.Name {
    static let vpnConnectionStatusChanged = Notification.Name("vpnConnectionStatusChanged")
    static let connectionAlertReceived = Notification.Name("connectionAlertReceived")
    static let subscriptionUpdateReceived = Notification.Name("subscriptionUpdateReceived")
    static let serverMaintenanceReceived = Notification.Name("serverMaintenanceReceived")
}

// MARK: - App Logger

/// Simple logging utility for the application
final class AppLogger {
    
    static let shared = AppLogger()
    
    enum LogLevel: Int {
        case debug = 0
        case info = 1
        case warning = 2
        case error = 3
    }
    
    var logLevel: LogLevel = .info
    
    private init() {}
    
    func debug(_ message: String, file: String = #file, function: String = #function, line: Int = #line) {
        log(message, level: .debug, file: file, function: function, line: line)
    }
    
    func info(_ message: String, file: String = #file, function: String = #function, line: Int = #line) {
        log(message, level: .info, file: file, function: function, line: line)
    }
    
    func warning(_ message: String, file: String = #file, function: String = #function, line: Int = #line) {
        log(message, level: .warning, file: file, function: function, line: line)
    }
    
    func error(_ message: String, file: String = #file, function: String = #function, line: Int = #line) {
        log(message, level: .error, file: file, function: function, line: line)
    }
    
    private func log(_ message: String, level: LogLevel, file: String, function: String, line: Int) {
        guard level.rawValue >= logLevel.rawValue else { return }
        
        let fileName = (file as NSString).lastPathComponent
        let levelEmoji: String
        
        switch level {
        case .debug: levelEmoji = "🔍"
        case .info: levelEmoji = "ℹ️"
        case .warning: levelEmoji = "⚠️"
        case .error: levelEmoji = "❌"
        }
        
        let timestamp = ISO8601DateFormatter().string(from: Date())
        print("\(levelEmoji) [\(timestamp)] [\(fileName):\(line)] \(function) - \(message)")
    }
}
