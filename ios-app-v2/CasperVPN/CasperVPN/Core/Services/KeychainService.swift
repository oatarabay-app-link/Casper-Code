//
//  KeychainService.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//

import Foundation
import Security

/// Service for secure storage in iOS Keychain
final class KeychainService: KeychainServiceProtocol {
    
    // MARK: - Singleton
    static let shared = KeychainService()
    
    // MARK: - Keys
    private enum Keys {
        static let accessToken = "accessToken"
        static let refreshToken = "refreshToken"
        static let vpnConfigPrefix = "vpnConfig_"
    }
    
    // MARK: - Properties
    private let service: String
    private let accessGroup: String?
    
    // MARK: - Initialization
    private init(service: String = Config.keychainService,
                 accessGroup: String? = Config.keychainAccessGroup) {
        self.service = service
        self.accessGroup = accessGroup
    }
    
    // MARK: - Token Management
    
    func saveAccessToken(_ token: String) {
        save(key: Keys.accessToken, value: token)
    }
    
    func getAccessToken() -> String? {
        return get(key: Keys.accessToken)
    }
    
    func saveRefreshToken(_ token: String) {
        save(key: Keys.refreshToken, value: token)
    }
    
    func getRefreshToken() -> String? {
        return get(key: Keys.refreshToken)
    }
    
    func clearTokens() {
        delete(key: Keys.accessToken)
        delete(key: Keys.refreshToken)
    }
    
    // MARK: - VPN Configuration Storage
    
    func saveVPNConfig(_ config: VPNConfig, forServer serverId: String) throws {
        let encoder = JSONEncoder()
        let data = try encoder.encode(config)
        
        guard let jsonString = String(data: data, encoding: .utf8) else {
            throw KeychainError.encodingFailed
        }
        
        save(key: Keys.vpnConfigPrefix + serverId, value: jsonString)
    }
    
    func getVPNConfig(forServer serverId: String) throws -> VPNConfig? {
        guard let jsonString = get(key: Keys.vpnConfigPrefix + serverId),
              let data = jsonString.data(using: .utf8) else {
            return nil
        }
        
        let decoder = JSONDecoder()
        return try decoder.decode(VPNConfig.self, from: data)
    }
    
    func deleteVPNConfig(forServer serverId: String) throws {
        delete(key: Keys.vpnConfigPrefix + serverId)
    }
    
    // MARK: - Generic Keychain Operations
    
    private func save(key: String, value: String) {
        guard let data = value.data(using: .utf8) else { return }
        
        // Delete existing item first
        delete(key: key)
        
        var query: [String: Any] = [
            kSecClass as String: kSecClassGenericPassword,
            kSecAttrService as String: service,
            kSecAttrAccount as String: key,
            kSecValueData as String: data,
            kSecAttrAccessible as String: kSecAttrAccessibleAfterFirstUnlockThisDeviceOnly
        ]
        
        if let accessGroup = accessGroup {
            query[kSecAttrAccessGroup as String] = accessGroup
        }
        
        let status = SecItemAdd(query as CFDictionary, nil)
        
        if status != errSecSuccess {
            ConnectionLogger.shared.log("Keychain save failed for key: \(key), status: \(status)", level: .warning)
        }
    }
    
    private func get(key: String) -> String? {
        var query: [String: Any] = [
            kSecClass as String: kSecClassGenericPassword,
            kSecAttrService as String: service,
            kSecAttrAccount as String: key,
            kSecReturnData as String: true,
            kSecMatchLimit as String: kSecMatchLimitOne
        ]
        
        if let accessGroup = accessGroup {
            query[kSecAttrAccessGroup as String] = accessGroup
        }
        
        var result: AnyObject?
        let status = SecItemCopyMatching(query as CFDictionary, &result)
        
        guard status == errSecSuccess,
              let data = result as? Data,
              let string = String(data: data, encoding: .utf8) else {
            return nil
        }
        
        return string
    }
    
    private func delete(key: String) {
        var query: [String: Any] = [
            kSecClass as String: kSecClassGenericPassword,
            kSecAttrService as String: service,
            kSecAttrAccount as String: key
        ]
        
        if let accessGroup = accessGroup {
            query[kSecAttrAccessGroup as String] = accessGroup
        }
        
        SecItemDelete(query as CFDictionary)
    }
    
    // MARK: - Utility Methods
    
    func clearAll() {
        var query: [String: Any] = [
            kSecClass as String: kSecClassGenericPassword,
            kSecAttrService as String: service
        ]
        
        if let accessGroup = accessGroup {
            query[kSecAttrAccessGroup as String] = accessGroup
        }
        
        SecItemDelete(query as CFDictionary)
    }
}

// MARK: - Keychain Error

enum KeychainError: LocalizedError {
    case saveFailed
    case readFailed
    case deleteFailed
    case encodingFailed
    case decodingFailed
    
    var errorDescription: String? {
        switch self {
        case .saveFailed:
            return "Failed to save to keychain"
        case .readFailed:
            return "Failed to read from keychain"
        case .deleteFailed:
            return "Failed to delete from keychain"
        case .encodingFailed:
            return "Failed to encode data for keychain"
        case .decodingFailed:
            return "Failed to decode data from keychain"
        }
    }
}
