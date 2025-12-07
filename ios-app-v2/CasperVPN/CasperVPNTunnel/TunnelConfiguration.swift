//
//  TunnelConfiguration.swift
//  CasperVPNTunnel
//
//  Created by CasperVPN Team
//  Copyright © 2024 CasperVPN. All rights reserved.
//

import Foundation

/// Configuration structure for the WireGuard tunnel.
/// Parses WireGuard configuration format into usable Swift structures.
struct TunnelConfiguration {
    
    // MARK: - Properties
    
    /// Interface (client) configuration
    let interface: InterfaceConfiguration
    
    /// Peer (server) configuration
    let peer: PeerConfiguration
    
    // MARK: - Interface Configuration
    
    struct InterfaceConfiguration {
        /// Client's private key (base64 encoded)
        let privateKey: String
        
        /// Client's address with CIDR notation (e.g., "10.0.0.2/32")
        let address: String
        
        /// DNS servers to use
        let dns: [String]?
        
        /// MTU value
        let mtu: Int?
        
        /// Listen port (optional, usually not needed for clients)
        let listenPort: Int?
    }
    
    // MARK: - Peer Configuration
    
    struct PeerConfiguration {
        /// Server's public key (base64 encoded)
        let publicKey: String
        
        /// Server endpoint (hostname:port or ip:port)
        let endpoint: String
        
        /// Allowed IPs for routing
        let allowedIPs: [String]
        
        /// Persistent keepalive interval in seconds
        let persistentKeepalive: Int?
        
        /// Pre-shared key for additional security
        let presharedKey: String?
    }
    
    // MARK: - Parsing
    
    /// Parses a WireGuard configuration string into a TunnelConfiguration
    /// - Parameter configString: The WireGuard configuration in INI-like format
    /// - Returns: Parsed TunnelConfiguration or nil if parsing fails
    static func parse(from configString: String) -> TunnelConfiguration? {
        var interfaceDict: [String: String] = [:]
        var peerDict: [String: String] = [:]
        var currentSection: String?
        
        // Parse line by line
        let lines = configString.components(separatedBy: .newlines)
        
        for line in lines {
            let trimmedLine = line.trimmingCharacters(in: .whitespaces)
            
            // Skip empty lines and comments
            if trimmedLine.isEmpty || trimmedLine.hasPrefix("#") {
                continue
            }
            
            // Check for section headers
            if trimmedLine.lowercased() == "[interface]" {
                currentSection = "interface"
                continue
            } else if trimmedLine.lowercased() == "[peer]" {
                currentSection = "peer"
                continue
            }
            
            // Parse key-value pairs
            let components = trimmedLine.split(separator: "=", maxSplits: 1)
            guard components.count == 2 else { continue }
            
            let key = String(components[0]).trimmingCharacters(in: .whitespaces).lowercased()
            let value = String(components[1]).trimmingCharacters(in: .whitespaces)
            
            switch currentSection {
            case "interface":
                interfaceDict[key] = value
            case "peer":
                peerDict[key] = value
            default:
                break
            }
        }
        
        // Validate required fields
        guard let privateKey = interfaceDict["privatekey"],
              let address = interfaceDict["address"],
              let publicKey = peerDict["publickey"],
              let endpoint = peerDict["endpoint"],
              let allowedIPs = peerDict["allowedips"] else {
            NSLog("[TunnelConfiguration] Missing required fields")
            return nil
        }
        
        // Parse optional fields
        let dns = interfaceDict["dns"]?.split(separator: ",").map { String($0).trimmingCharacters(in: .whitespaces) }
        let mtu = interfaceDict["mtu"].flatMap { Int($0) }
        let listenPort = interfaceDict["listenport"].flatMap { Int($0) }
        let persistentKeepalive = peerDict["persistentkeepalive"].flatMap { Int($0) }
        let presharedKey = peerDict["presharedkey"]
        
        // Parse allowed IPs
        let allowedIPsList = allowedIPs.split(separator: ",").map { String($0).trimmingCharacters(in: .whitespaces) }
        
        // Create configurations
        let interfaceConfig = InterfaceConfiguration(
            privateKey: privateKey,
            address: address,
            dns: dns,
            mtu: mtu,
            listenPort: listenPort
        )
        
        let peerConfig = PeerConfiguration(
            publicKey: publicKey,
            endpoint: endpoint,
            allowedIPs: allowedIPsList,
            persistentKeepalive: persistentKeepalive,
            presharedKey: presharedKey
        )
        
        return TunnelConfiguration(interface: interfaceConfig, peer: peerConfig)
    }
    
    // MARK: - Serialization
    
    /// Generates a WireGuard configuration string from this configuration
    /// - Returns: Configuration string in WireGuard INI format
    func toConfigString() -> String {
        var config = "[Interface]\n"
        config += "PrivateKey = \(interface.privateKey)\n"
        config += "Address = \(interface.address)\n"
        
        if let dns = interface.dns, !dns.isEmpty {
            config += "DNS = \(dns.joined(separator: ", "))\n"
        }
        
        if let mtu = interface.mtu {
            config += "MTU = \(mtu)\n"
        }
        
        if let listenPort = interface.listenPort {
            config += "ListenPort = \(listenPort)\n"
        }
        
        config += "\n[Peer]\n"
        config += "PublicKey = \(peer.publicKey)\n"
        config += "Endpoint = \(peer.endpoint)\n"
        config += "AllowedIPs = \(peer.allowedIPs.joined(separator: ", "))\n"
        
        if let persistentKeepalive = peer.persistentKeepalive {
            config += "PersistentKeepalive = \(persistentKeepalive)\n"
        }
        
        if let presharedKey = peer.presharedKey {
            config += "PresharedKey = \(presharedKey)\n"
        }
        
        return config
    }
}

// MARK: - Key Generation

/// Utilities for WireGuard key generation and validation.
enum WireGuardKeys {
    
    /// Length of a WireGuard key in bytes
    static let keyLength = 32
    
    /// Validates a base64-encoded WireGuard key
    /// - Parameter key: The key to validate
    /// - Returns: Whether the key is valid
    static func isValidKey(_ key: String) -> Bool {
        guard let data = Data(base64Encoded: key) else {
            return false
        }
        return data.count == keyLength
    }
    
    /// Generates a new random private key
    /// - Returns: Base64-encoded private key, or nil if generation fails
    static func generatePrivateKey() -> String? {
        var keyData = Data(count: keyLength)
        let result = keyData.withUnsafeMutableBytes { buffer in
            SecRandomCopyBytes(kSecRandomDefault, keyLength, buffer.baseAddress!)
        }
        
        guard result == errSecSuccess else {
            return nil
        }
        
        // Clamp the key as per WireGuard spec
        keyData[0] &= 248
        keyData[31] &= 127
        keyData[31] |= 64
        
        return keyData.base64EncodedString()
    }
    
    /// Derives a public key from a private key
    /// Note: In a real implementation, this would use Curve25519
    /// - Parameter privateKey: Base64-encoded private key
    /// - Returns: Base64-encoded public key, or nil if derivation fails
    static func derivePublicKey(from privateKey: String) -> String? {
        // This is a placeholder. In real implementation,
        // use Curve25519 to derive the public key
        guard isValidKey(privateKey) else {
            return nil
        }
        
        // Placeholder: would use actual Curve25519 implementation
        return nil
    }
}

// MARK: - Configuration Builder

/// Builder for constructing TunnelConfiguration instances.
class TunnelConfigurationBuilder {
    
    private var privateKey: String?
    private var address: String?
    private var dns: [String]?
    private var mtu: Int?
    private var listenPort: Int?
    
    private var publicKey: String?
    private var endpoint: String?
    private var allowedIPs: [String] = []
    private var persistentKeepalive: Int?
    private var presharedKey: String?
    
    // MARK: - Interface Configuration
    
    func setPrivateKey(_ key: String) -> TunnelConfigurationBuilder {
        self.privateKey = key
        return self
    }
    
    func setAddress(_ address: String) -> TunnelConfigurationBuilder {
        self.address = address
        return self
    }
    
    func setDNS(_ dns: [String]) -> TunnelConfigurationBuilder {
        self.dns = dns
        return self
    }
    
    func setMTU(_ mtu: Int) -> TunnelConfigurationBuilder {
        self.mtu = mtu
        return self
    }
    
    func setListenPort(_ port: Int) -> TunnelConfigurationBuilder {
        self.listenPort = port
        return self
    }
    
    // MARK: - Peer Configuration
    
    func setPeerPublicKey(_ key: String) -> TunnelConfigurationBuilder {
        self.publicKey = key
        return self
    }
    
    func setEndpoint(_ endpoint: String) -> TunnelConfigurationBuilder {
        self.endpoint = endpoint
        return self
    }
    
    func setAllowedIPs(_ ips: [String]) -> TunnelConfigurationBuilder {
        self.allowedIPs = ips
        return self
    }
    
    func addAllowedIP(_ ip: String) -> TunnelConfigurationBuilder {
        self.allowedIPs.append(ip)
        return self
    }
    
    func setPersistentKeepalive(_ seconds: Int) -> TunnelConfigurationBuilder {
        self.persistentKeepalive = seconds
        return self
    }
    
    func setPresharedKey(_ key: String) -> TunnelConfigurationBuilder {
        self.presharedKey = key
        return self
    }
    
    // MARK: - Build
    
    /// Builds the TunnelConfiguration
    /// - Returns: The built configuration, or nil if required fields are missing
    func build() -> TunnelConfiguration? {
        guard let privateKey = privateKey,
              let address = address,
              let publicKey = publicKey,
              let endpoint = endpoint,
              !allowedIPs.isEmpty else {
            return nil
        }
        
        let interfaceConfig = TunnelConfiguration.InterfaceConfiguration(
            privateKey: privateKey,
            address: address,
            dns: dns,
            mtu: mtu,
            listenPort: listenPort
        )
        
        let peerConfig = TunnelConfiguration.PeerConfiguration(
            publicKey: publicKey,
            endpoint: endpoint,
            allowedIPs: allowedIPs,
            persistentKeepalive: persistentKeepalive,
            presharedKey: presharedKey
        )
        
        return TunnelConfiguration(interface: interfaceConfig, peer: peerConfig)
    }
}

// MARK: - Configuration Extensions

extension TunnelConfiguration {
    
    /// Creates a sample configuration for testing
    static var sample: TunnelConfiguration {
        TunnelConfiguration(
            interface: InterfaceConfiguration(
                privateKey: "SAMPLE_PRIVATE_KEY_BASE64_ENCODED=",
                address: "10.0.0.2/32",
                dns: ["1.1.1.1", "1.0.0.1"],
                mtu: 1420,
                listenPort: nil
            ),
            peer: PeerConfiguration(
                publicKey: "SAMPLE_PUBLIC_KEY_BASE64_ENCODED=",
                endpoint: "vpn.example.com:51820",
                allowedIPs: ["0.0.0.0/0", "::/0"],
                persistentKeepalive: 25,
                presharedKey: nil
            )
        )
    }
}
