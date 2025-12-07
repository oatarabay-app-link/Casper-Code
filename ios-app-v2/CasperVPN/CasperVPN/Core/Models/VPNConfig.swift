//
//  VPNConfig.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//

import Foundation

struct VPNConfig: Codable, Equatable {
    let privateKey: String
    let publicKey: String
    let endpoint: String
    let allowedIPs: [String]
    let dns: [String]
    let persistentKeepalive: Int?
    let presharedKey: String?
    let mtu: Int?
    let address: String?
    
    // MARK: - Computed Properties
    var endpointHost: String {
        endpoint.components(separatedBy: ":").first ?? endpoint
    }
    
    var endpointPort: UInt16 {
        guard let portString = endpoint.components(separatedBy: ":").last,
              let port = UInt16(portString) else {
            return Config.wireGuardDefaultPort
        }
        return port
    }
    
    var effectiveMTU: Int {
        mtu ?? Config.defaultMTU
    }
    
    var effectiveKeepalive: Int {
        persistentKeepalive ?? Config.defaultPersistentKeepalive
    }
    
    // MARK: - Validation
    var isValid: Bool {
        !privateKey.isEmpty &&
        !publicKey.isEmpty &&
        !endpoint.isEmpty &&
        !allowedIPs.isEmpty
    }
    
    // MARK: - WireGuard Configuration String
    func toWireGuardConfig(clientAddress: String) -> String {
        var config = """
        [Interface]
        PrivateKey = \(privateKey)
        Address = \(clientAddress)
        """
        
        if let mtu = mtu {
            config += "\nMTU = \(mtu)"
        }
        
        if !dns.isEmpty {
            config += "\nDNS = \(dns.joined(separator: ", "))"
        }
        
        config += """
        
        
        [Peer]
        PublicKey = \(publicKey)
        Endpoint = \(endpoint)
        AllowedIPs = \(allowedIPs.joined(separator: ", "))
        """
        
        if let keepalive = persistentKeepalive {
            config += "\nPersistentKeepalive = \(keepalive)"
        }
        
        if let psk = presharedKey, !psk.isEmpty {
            config += "\nPresharedKey = \(psk)"
        }
        
        return config
    }
}

// MARK: - API Response
struct VPNConfigResponse: Codable {
    let success: Bool
    let data: VPNConfig?
    let message: String?
}

// MARK: - Connection Log Request
struct ConnectionLogRequest: Codable {
    let serverId: String
    let connectedAt: Date?
    let disconnectedAt: Date?
    let bytesReceived: Int64?
    let bytesSent: Int64?
    let duration: TimeInterval?
}

struct ConnectionLogResponse: Codable {
    let success: Bool
    let message: String?
}
