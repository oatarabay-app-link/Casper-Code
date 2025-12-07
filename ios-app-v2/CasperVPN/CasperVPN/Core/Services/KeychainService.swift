//
//  KeychainService.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//  Copyright © 2024 CasperVPN. All rights reserved.
//

import Foundation
import Security

/// Service responsible for secure storage using the iOS Keychain.
final class KeychainService: KeychainServiceProtocol {
    
    // MARK: - Singleton
    
    static let shared = KeychainService()
    
    // MARK: - Properties
    
    private let serviceName: String
    private let accessGroup: String?
    private let logger = AppLogger.shared
    
    // MARK: - Initialization
    
    init(
        serviceName: String = AppConfig.keychainServiceName,
        accessGroup: String? = AppConfig.keychainAccessGroup
    ) {
        self.serviceName = serviceName
        self.accessGroup = accessGroup
    }
    
    // MARK: - String Storage
    
    func save(_ value: String, forKey key: String) throws {
        guard let data = value.data(using: .utf8) else {
            throw KeychainError.invalidData
        }
        try saveData(data, forKey: key)
    }
    
    func get(forKey key: String) throws -> String? {
        guard let data = try getData(forKey: key) else {
            return nil
        }
        return String(data: data, encoding: .utf8)
    }
    
    // MARK: - Data Storage
    
    func saveData(_ data: Data, forKey key: String) throws {
        // First, try to delete any existing item
        try? delete(forKey: key)
        
        var query = buildQuery(forKey: key)
        query[kSecValueData as String] = data
        query[kSecAttrAccessible as String] = kSecAttrAccessibleAfterFirstUnlockThisDeviceOnly
        
        let status = SecItemAdd(query as CFDictionary, nil)
        
        guard status == errSecSuccess else {
            logger.error("Keychain save failed for key: \(key), status: \(status)")
            throw KeychainError.unexpectedStatus(status)
        }
        
        logger.debug("Saved data to keychain for key: \(key)")
    }
    
    func getData(forKey key: String) throws -> Data? {
        var query = buildQuery(forKey: key)
        query[kSecReturnData as String] = true
        query[kSecMatchLimit as String] = kSecMatchLimitOne
        
        var result: AnyObject?
        let status = SecItemCopyMatching(query as CFDictionary, &result)
        
        switch status {
        case errSecSuccess:
            guard let data = result as? Data else {
                logger.error("Keychain returned invalid data type for key: \(key)")
                throw KeychainError.invalidData
            }
            return data
            
        case errSecItemNotFound:
            return nil
            
        default:
            logger.error("Keychain get failed for key: \(key), status: \(status)")
            throw KeychainError.unexpectedStatus(status)
        }
    }
    
    // MARK: - Delete
    
    func delete(forKey key: String) throws {
        let query = buildQuery(forKey: key)
        let status = SecItemDelete(query as CFDictionary)
        
        guard status == errSecSuccess || status == errSecItemNotFound else {
            logger.error("Keychain delete failed for key: \(key), status: \(status)")
            throw KeychainError.unexpectedStatus(status)
        }
        
        logger.debug("Deleted keychain item for key: \(key)")
    }
    
    // MARK: - Clear All
    
    func clearAll() throws {
        var query: [String: Any] = [
            kSecClass as String: kSecClassGenericPassword,
            kSecAttrService as String: serviceName
        ]
        
        if let accessGroup = accessGroup {
            query[kSecAttrAccessGroup as String] = accessGroup
        }
        
        let status = SecItemDelete(query as CFDictionary)
        
        guard status == errSecSuccess || status == errSecItemNotFound else {
            logger.error("Keychain clear all failed, status: \(status)")
            throw KeychainError.unexpectedStatus(status)
        }
        
        logger.info("Cleared all keychain items")
    }
    
    // MARK: - Private Methods
    
    private func buildQuery(forKey key: String) -> [String: Any] {
        var query: [String: Any] = [
            kSecClass as String: kSecClassGenericPassword,
            kSecAttrService as String: serviceName,
            kSecAttrAccount as String: key
        ]
        
        if let accessGroup = accessGroup {
            query[kSecAttrAccessGroup as String] = accessGroup
        }
        
        return query
    }
}

// MARK: - Secure Credential Storage

extension KeychainService {
    
    /// Stores user credentials securely
    func saveCredentials(email: String, password: String) throws {
        let credentials = UserCredentials(email: email, password: password)
        let data = try JSONEncoder().encode(credentials)
        try saveData(data, forKey: KeychainKeys.userCredentials)
    }
    
    /// Retrieves stored user credentials
    func getCredentials() throws -> UserCredentials? {
        guard let data = try getData(forKey: KeychainKeys.userCredentials) else {
            return nil
        }
        return try JSONDecoder().decode(UserCredentials.self, from: data)
    }
    
    /// Stores VPN configuration securely
    func saveVPNConfig(_ config: VPNConfig) throws {
        let data = try JSONEncoder().encode(config)
        try saveData(data, forKey: KeychainKeys.vpnConfig)
    }
    
    /// Retrieves stored VPN configuration
    func getVPNConfig() throws -> VPNConfig? {
        guard let data = try getData(forKey: KeychainKeys.vpnConfig) else {
            return nil
        }
        return try JSONDecoder().decode(VPNConfig.self, from: data)
    }
}

// MARK: - User Credentials

/// Model for storing user credentials
struct UserCredentials: Codable {
    let email: String
    let password: String
}

// MARK: - Keychain Accessibility

/// Options for keychain item accessibility
enum KeychainAccessibility {
    case whenUnlocked
    case afterFirstUnlock
    case whenUnlockedThisDeviceOnly
    case afterFirstUnlockThisDeviceOnly
    case whenPasscodeSetThisDeviceOnly
    
    var secAccessibilityAttribute: CFString {
        switch self {
        case .whenUnlocked:
            return kSecAttrAccessibleWhenUnlocked
        case .afterFirstUnlock:
            return kSecAttrAccessibleAfterFirstUnlock
        case .whenUnlockedThisDeviceOnly:
            return kSecAttrAccessibleWhenUnlockedThisDeviceOnly
        case .afterFirstUnlockThisDeviceOnly:
            return kSecAttrAccessibleAfterFirstUnlockThisDeviceOnly
        case .whenPasscodeSetThisDeviceOnly:
            return kSecAttrAccessibleWhenPasscodeSetThisDeviceOnly
        }
    }
}

// MARK: - Secure String Generation

extension KeychainService {
    
    /// Generates a cryptographically secure random string
    /// - Parameter length: The desired length of the string
    /// - Returns: A random hex string
    static func generateSecureString(length: Int = 32) -> String {
        var bytes = [UInt8](repeating: 0, count: length)
        _ = SecRandomCopyBytes(kSecRandomDefault, length, &bytes)
        return bytes.map { String(format: "%02x", $0) }.joined()
    }
}
