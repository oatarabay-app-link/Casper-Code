//
//  AppDelegate.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//

import UIKit
import NetworkExtension
import UserNotifications

class AppDelegate: NSObject, UIApplicationDelegate {
    
    func application(_ application: UIApplication,
                     didFinishLaunchingWithOptions launchOptions: [UIApplication.LaunchOptionsKey: Any]?) -> Bool {
        // Configure logging
        ConnectionLogger.shared.log("App launched", level: .info)
        
        // Request notification permissions
        requestNotificationPermissions()
        
        // Initialize VPN manager
        Task {
            await VPNConnectionManager.shared.initialize()
        }
        
        return true
    }
    
    func applicationWillTerminate(_ application: UIApplication) {
        ConnectionLogger.shared.log("App terminating", level: .info)
    }
    
    func applicationDidEnterBackground(_ application: UIApplication) {
        ConnectionLogger.shared.log("App entered background", level: .debug)
    }
    
    func applicationWillEnterForeground(_ application: UIApplication) {
        ConnectionLogger.shared.log("App entering foreground", level: .debug)
        
        // Refresh VPN status when returning to foreground
        Task {
            await VPNConnectionManager.shared.refreshStatus()
        }
    }
    
    // MARK: - Private Methods
    
    private func requestNotificationPermissions() {
        UNUserNotificationCenter.current().requestAuthorization(options: [.alert, .badge, .sound]) { granted, error in
            if let error = error {
                ConnectionLogger.shared.log("Notification permission error: \(error.localizedDescription)", level: .error)
            } else {
                ConnectionLogger.shared.log("Notification permission granted: \(granted)", level: .debug)
            }
        }
    }
}
