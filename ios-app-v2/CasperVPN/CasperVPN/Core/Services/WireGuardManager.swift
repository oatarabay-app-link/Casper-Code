//
//  WireGuardManager.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//

import Foundation
import CryptoKit

/// Manager for WireGuard tunnel operations including key generation and configuration parsing
final class WireGuardManager: WireGuardManagerProtocol {
    
    // MARK: - Singleton
    static let shared = WireGuardManager()
    
    // MARK: - Properties
    private let logger = ConnectionLogger.shared
    
    // MARK: - Initialization
    private init() {}
    
    // MARK: - Key Generation
    
    /// Generates a new X25519 key pair for WireGuard
    /// - Returns: Tuple containing base64-encoded private and public keys
    func generateKeyPair() throws -> (privateKey: String, publicKey: String) {
        logger.log("Generating WireGuard key pair", level: .debug)
        
        // Generate private key using Curve25519
        let privateKey = Curve25519.KeyAgreement.PrivateKey()
        let publicKey = privateKey.publicKey
        
        // Convert to base64
        let privateKeyBase64 = privateKey.rawRepresentation.base64EncodedString()
        let publicKeyBase64 = publicKey.rawRepresentation.base64EncodedString()
        
        logger.log("Key pair generated successfully", level: .debug)
        
        return (privateKeyBase64, publicKeyBase64)
    }
    
    /// Validates a WireGuard key format
    /// - Parameter key: Base64-encoded key to validate
    /// - Returns: True if the key is valid
    func validateKey(_ key: String) -> Bool {
        guard let keyData = Data(base64Encoded: key) else {
            logger.log("Key validation failed: invalid base64", level: .warning)
            return false
        }
        
        // WireGuard keys are 32 bytes (256 bits)
        let isValid = keyData.count == 32
        
        if !isValid {
            logger.log("Key validation failed: incorrect length (\(keyData.count) bytes)", level: .warning)
        }
        
        return isValid
    }
    
    /// Parses VPN configuration into WireGuard format
    /// - Parameter config: VPN configuration from API
    /// - Returns: Configuration data suitable for WireGuard
    func parseConfiguration(_ config: VPNConfig) throws -> Data {
        logger.log("Parsing WireGuard configuration", level: .debug)
        
        // Validate required fields
        guard !config.privateKey.isEmpty else {
            throw VPNError.missingPrivateKey
        }
        
        guard !config.publicKey.isEmpty else {
            throw VPNError.missingPublicKey
        }
        
        guard !config.endpoint.isEmpty else {
            throw VPNError.invalidEndpoint
        }
        
        guard !config.allowedIPs.isEmpty else {
            throw VPNError.invalidAllowedIPs
        }
        
        // Validate keys
        guard validateKey(config.privateKey) else {
            throw VPNError.wireGuardInvalidKey
        }
        
        guard validateKey(config.publicKey) else {
            throw VPNError.wireGuardInvalidKey
        }
        
        // Build configuration dictionary
        var configDict: [String: Any] = [
            "privateKey": config.privateKey,
            "publicKey": config.publicKey,
            "endpoint": config.endpoint,
            "allowedIPs": config.allowedIPs,
            "dns": config.dns.isEmpty ? Config.wireGuardDefaultDNS : config.dns,
            "mtu": config.effectiveMTU,
            "persistentKeepalive": config.effectiveKeepalive
        ]
        
        if let psk = config.presharedKey, !psk.isEmpty {
            configDict["presharedKey"] = psk
        }
        
        if let address = config.address {
            configDict["address"] = address
        }
        
        // Serialize to JSON
        let jsonData = try JSONSerialization.data(withJSONObject: configDict, options: [])
        
        logger.log("Configuration parsed successfully", level: .debug)
        
        return jsonData
    }
    
    // MARK: - Configuration Building
    
    /// Builds a WireGuard configuration string
    /// - Parameters:
    ///   - config: VPN configuration
    ///   - clientAddress: Client IP address
    /// - Returns: WireGuard configuration string
    func buildConfigurationString(_ config: VPNConfig, clientAddress: String) -> String {
        return config.toWireGuardConfig(clientAddress: clientAddress)
    }
    
    /// Creates tunnel provider protocol configuration
    /// - Parameter config: VPN configuration
    /// - Returns: Dictionary for tunnel provider protocol configuration
    func createProviderConfiguration(_ config: VPNConfig) throws -> [String: Any] {
        let configData = try parseConfiguration(config)
        
        guard let configString = String(data: configData, encoding: .utf8) else {
            throw VPNError.configurationParseFailed
        }
        
        return [
            "config": configString,
            "endpoint": config.endpoint,
            "mtu": config.effectiveMTU
        ]
    }
    
    // MARK: - Key Conversion Utilities
    
    /// Converts a private key to its corresponding public key
    /// - Parameter privateKeyBase64: Base64-encoded private key
    /// - Returns: Base64-encoded public key
    func publicKey(from privateKeyBase64: String) throws -> String {
        guard let privateKeyData = Data(base64Encoded: privateKeyBase64) else {
            throw VPNError.wireGuardInvalidKey
        }
        
        guard privateKeyData.count == 32 else {
            throw VPNError.wireGuardInvalidKey
        }
        
        do {
            let privateKey = try Curve25519.KeyAgreement.PrivateKey(rawRepresentation: privateKeyData)
            return privateKey.publicKey.rawRepresentation.base64EncodedString()
        } catch {
            logger.log("Failed to derive public key: \(error.localizedDescription)", level: .error)
            throw VPNError.wireGuardKeyGenerationFailed
        }
    }
    
    /// Generates a preshared key for additional security
    /// - Returns: Base64-encoded preshared key
    func generatePresharedKey() -> String {
        var keyData = Data(count: 32)
        _ = keyData.withUnsafeMutableBytes { SecRandomCopyBytes(kSecRandomDefault, 32, $0.baseAddress!) }
        return keyData.base64EncodedString()
    }
}

// MARK: - WireGuard Tunnel Configuration
struct WireGuardTunnelConfiguration: Codable {
    let interface: InterfaceConfiguration
    let peers: [PeerConfiguration]
    
    struct InterfaceConfiguration: Codable {
        let privateKey: String
        let addresses: [String]
        let dns: [String]
        let mtu: Int?
        let listenPort: Int?
    }
    
    struct PeerConfiguration: Codable {
        let publicKey: String
        let presharedKey: String?
        let endpoint: String
        let allowedIPs: [String]
        let persistentKeepalive: Int?
    }
    
    init(config: VPNConfig, clientAddress: String) {
        self.interface = InterfaceConfiguration(
            privateKey: config.privateKey,
            addresses: [clientAddress],
            dns: config.dns.isEmpty ? Config.wireGuardDefaultDNS : config.dns,
            mtu: config.mtu,
            listenPort: nil
        )
        
        self.peers = [
            PeerConfiguration(
                publicKey: config.publicKey,
                presharedKey: config.presharedKey,
                endpoint: config.endpoint,
                allowedIPs: config.allowedIPs,
                persistentKeepalive: config.persistentKeepalive
            )
        ]
    }
    
    func toJSON() throws -> Data {
        let encoder = JSONEncoder()
        encoder.outputFormatting = .prettyPrinted
        return try encoder.encode(self)
    }
}
