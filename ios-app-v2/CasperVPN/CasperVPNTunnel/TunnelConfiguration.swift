//
//  TunnelConfiguration.swift
//  CasperVPNTunnel
//
//  Created by CasperVPN Team
//

import Foundation

/// Enhanced WireGuard tunnel configuration
struct TunnelConfiguration: Codable {
    
    // MARK: - Interface Configuration
    let privateKey: String
    let address: String
    let dns: [String]
    let mtu: Int
    
    // MARK: - Peer Configuration
    let publicKey: String
    let presharedKey: String?
    let endpoint: String
    let allowedIPs: [String]
    let persistentKeepalive: Int
    
    // MARK: - Computed Properties
    
    var endpointHost: String {
        endpoint.components(separatedBy: ":").first ?? endpoint
    }
    
    var endpointPort: UInt16 {
        guard let portString = endpoint.components(separatedBy: ":").last,
              let port = UInt16(portString) else {
            return 51820
        }
        return port
    }
    
    var ipAddress: String {
        address.components(separatedBy: "/").first ?? address
    }
    
    var subnetMask: Int {
        guard let maskString = address.components(separatedBy: "/").last,
              let mask = Int(maskString) else {
            return 32
        }
        return mask
    }
    
    // MARK: - Initialization
    
    init(privateKey: String,
         publicKey: String,
         endpoint: String,
         allowedIPs: [String],
         dns: [String] = ["1.1.1.1", "1.0.0.1"],
         mtu: Int = 1280,
         persistentKeepalive: Int = 25,
         presharedKey: String? = nil,
         address: String = "10.0.0.2/32") {
        self.privateKey = privateKey
        self.publicKey = publicKey
        self.endpoint = endpoint
        self.allowedIPs = allowedIPs
        self.dns = dns
        self.mtu = mtu
        self.persistentKeepalive = persistentKeepalive
        self.presharedKey = presharedKey
        self.address = address
    }
    
    // MARK: - Validation
    
    var isValid: Bool {
        return !privateKey.isEmpty &&
               !publicKey.isEmpty &&
               !endpoint.isEmpty &&
               !allowedIPs.isEmpty &&
               validateKey(privateKey) &&
               validateKey(publicKey)
    }
    
    private func validateKey(_ key: String) -> Bool {
        guard let data = Data(base64Encoded: key) else { return false }
        return data.count == 32
    }
    
    // MARK: - WireGuard Configuration String
    
    func toWireGuardConfigString() -> String {
        var config = """
        [Interface]
        PrivateKey = \(privateKey)
        Address = \(address)
        DNS = \(dns.joined(separator: ", "))
        MTU = \(mtu)
        
        [Peer]
        PublicKey = \(publicKey)
        Endpoint = \(endpoint)
        AllowedIPs = \(allowedIPs.joined(separator: ", "))
        PersistentKeepalive = \(persistentKeepalive)
        """
        
        if let psk = presharedKey, !psk.isEmpty {
            config += "\nPresharedKey = \(psk)"
        }
        
        return config
    }
    
    // MARK: - JSON Serialization
    
    func toJSON() throws -> Data {
        let encoder = JSONEncoder()
        encoder.outputFormatting = .prettyPrinted
        return try encoder.encode(self)
    }
    
    static func from(json data: Data) throws -> TunnelConfiguration {
        let decoder = JSONDecoder()
        return try decoder.decode(TunnelConfiguration.self, from: data)
    }
    
    // MARK: - Dictionary Representation
    
    func toDictionary() -> [String: Any] {
        var dict: [String: Any] = [
            "privateKey": privateKey,
            "publicKey": publicKey,
            "endpoint": endpoint,
            "allowedIPs": allowedIPs,
            "dns": dns,
            "mtu": mtu,
            "persistentKeepalive": persistentKeepalive,
            "address": address
        ]
        
        if let psk = presharedKey {
            dict["presharedKey"] = psk
        }
        
        return dict
    }
}

// MARK: - Tunnel Configuration Builder

class TunnelConfigurationBuilder {
    private var privateKey: String = ""
    private var publicKey: String = ""
    private var endpoint: String = ""
    private var allowedIPs: [String] = []
    private var dns: [String] = ["1.1.1.1", "1.0.0.1"]
    private var mtu: Int = 1280
    private var persistentKeepalive: Int = 25
    private var presharedKey: String?
    private var address: String = "10.0.0.2/32"
    
    func setPrivateKey(_ key: String) -> TunnelConfigurationBuilder {
        self.privateKey = key
        return self
    }
    
    func setPublicKey(_ key: String) -> TunnelConfigurationBuilder {
        self.publicKey = key
        return self
    }
    
    func setEndpoint(_ endpoint: String) -> TunnelConfigurationBuilder {
        self.endpoint = endpoint
        return self
    }
    
    func setEndpoint(host: String, port: UInt16) -> TunnelConfigurationBuilder {
        self.endpoint = "\(host):\(port)"
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
    
    func setDNS(_ servers: [String]) -> TunnelConfigurationBuilder {
        self.dns = servers
        return self
    }
    
    func setMTU(_ mtu: Int) -> TunnelConfigurationBuilder {
        self.mtu = mtu
        return self
    }
    
    func setPersistentKeepalive(_ keepalive: Int) -> TunnelConfigurationBuilder {
        self.persistentKeepalive = keepalive
        return self
    }
    
    func setPresharedKey(_ key: String?) -> TunnelConfigurationBuilder {
        self.presharedKey = key
        return self
    }
    
    func setAddress(_ address: String) -> TunnelConfigurationBuilder {
        self.address = address
        return self
    }
    
    func build() -> TunnelConfiguration {
        return TunnelConfiguration(
            privateKey: privateKey,
            publicKey: publicKey,
            endpoint: endpoint,
            allowedIPs: allowedIPs,
            dns: dns,
            mtu: mtu,
            persistentKeepalive: persistentKeepalive,
            presharedKey: presharedKey,
            address: address
        )
    }
}

// MARK: - Quick Connect Configuration

struct QuickConnectConfiguration {
    let serverHost: String
    let serverPort: UInt16
    let serverPublicKey: String
    
    func createTunnelConfiguration(clientPrivateKey: String, clientAddress: String) -> TunnelConfiguration {
        return TunnelConfigurationBuilder()
            .setPrivateKey(clientPrivateKey)
            .setPublicKey(serverPublicKey)
            .setEndpoint(host: serverHost, port: serverPort)
            .setAllowedIPs(["0.0.0.0/0", "::/0"])
            .setAddress(clientAddress)
            .build()
    }
}
