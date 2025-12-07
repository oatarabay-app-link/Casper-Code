//
//  VPNConfig.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//  Copyright © 2024 CasperVPN. All rights reserved.
//

import Foundation

/// Represents a complete VPN configuration for establishing a connection.
/// Primarily designed for WireGuard protocol but supports other protocols.
struct VPNConfig: Codable, Equatable {
    
    // MARK: - Properties
    
    /// The complete configuration string (WireGuard format)
    let config: String
    
    /// Protocol used for this configuration
    let `protocol`: VPNProtocolType
    
    /// Server hostname or IP address
    let serverHostname: String
    
    /// Server port number
    let serverPort: Int
    
    /// Server's public key (WireGuard)
    let serverPublicKey: String?
    
    /// Client's private key (WireGuard)
    let clientPrivateKey: String?
    
    /// Client's assigned IP address
    let clientAddress: String?
    
    /// DNS servers to use
    let dnsServers: [String]?
    
    /// Allowed IPs for routing (typically "0.0.0.0/0" for full tunnel)
    let allowedIPs: [String]?
    
    /// Persistent keepalive interval in seconds
    let persistentKeepalive: Int?
    
    // MARK: - Initialization
    
    init(
        config: String,
        protocol: VPNProtocolType,
        serverHostname: String,
        serverPort: Int,
        serverPublicKey: String? = nil,
        clientPrivateKey: String? = nil,
        clientAddress: String? = nil,
        dnsServers: [String]? = nil,
        allowedIPs: [String]? = nil,
        persistentKeepalive: Int? = nil
    ) {
        self.config = config
        self.protocol = `protocol`
        self.serverHostname = serverHostname
        self.serverPort = serverPort
        self.serverPublicKey = serverPublicKey
        self.clientPrivateKey = clientPrivateKey
        self.clientAddress = clientAddress
        self.dnsServers = dnsServers
        self.allowedIPs = allowedIPs
        self.persistentKeepalive = persistentKeepalive
    }
}

// MARK: - WireGuard Configuration

/// WireGuard-specific configuration structure
struct WireGuardConfig: Codable, Equatable {
    
    // MARK: - Interface Configuration
    
    let interface: WireGuardInterface
    
    // MARK: - Peer Configuration
    
    let peer: WireGuardPeer
    
    // MARK: - Generate Config String
    
    /// Generates the WireGuard configuration file content
    func generateConfigString() -> String {
        var config = "[Interface]\n"
        config += "PrivateKey = \(interface.privateKey)\n"
        config += "Address = \(interface.address)\n"
        
        if let dns = interface.dns {
            config += "DNS = \(dns.joined(separator: ", "))\n"
        }
        
        if let mtu = interface.mtu {
            config += "MTU = \(mtu)\n"
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

// MARK: - WireGuard Interface

/// WireGuard interface (client) configuration
struct WireGuardInterface: Codable, Equatable {
    
    /// Client's private key (base64 encoded)
    let privateKey: String
    
    /// Client's assigned IP address with CIDR notation
    let address: String
    
    /// DNS servers to use
    let dns: [String]?
    
    /// MTU value (optional)
    let mtu: Int?
    
    /// Listen port (usually not needed for clients)
    let listenPort: Int?
    
    init(
        privateKey: String,
        address: String,
        dns: [String]? = ["1.1.1.1", "1.0.0.1"],
        mtu: Int? = 1420,
        listenPort: Int? = nil
    ) {
        self.privateKey = privateKey
        self.address = address
        self.dns = dns
        self.mtu = mtu
        self.listenPort = listenPort
    }
}

// MARK: - WireGuard Peer

/// WireGuard peer (server) configuration
struct WireGuardPeer: Codable, Equatable {
    
    /// Server's public key (base64 encoded)
    let publicKey: String
    
    /// Server endpoint (hostname:port or ip:port)
    let endpoint: String
    
    /// Allowed IPs for routing through this peer
    let allowedIPs: [String]
    
    /// Persistent keepalive interval in seconds
    let persistentKeepalive: Int?
    
    /// Pre-shared key for additional security (optional)
    let presharedKey: String?
    
    init(
        publicKey: String,
        endpoint: String,
        allowedIPs: [String] = ["0.0.0.0/0", "::/0"],
        persistentKeepalive: Int? = 25,
        presharedKey: String? = nil
    ) {
        self.publicKey = publicKey
        self.endpoint = endpoint
        self.allowedIPs = allowedIPs
        self.persistentKeepalive = persistentKeepalive
        self.presharedKey = presharedKey
    }
}

// MARK: - VPN Config Response

/// Response model from the API when requesting server configuration
struct VPNConfigResponse: Codable {
    let config: String
    let `protocol`: VPNProtocolType
    let serverHostname: String
    let serverPort: Int
    let serverPublicKey: String?
    let clientPrivateKey: String?
    let clientAddress: String?
    let dnsServers: [String]?
    let allowedIPs: [String]?
    let persistentKeepalive: Int?
    
    /// Converts to VPNConfig model
    func toVPNConfig() -> VPNConfig {
        VPNConfig(
            config: config,
            protocol: `protocol`,
            serverHostname: serverHostname,
            serverPort: serverPort,
            serverPublicKey: serverPublicKey,
            clientPrivateKey: clientPrivateKey,
            clientAddress: clientAddress,
            dnsServers: dnsServers,
            allowedIPs: allowedIPs,
            persistentKeepalive: persistentKeepalive
        )
    }
    
    /// Converts to WireGuard config if applicable
    func toWireGuardConfig() -> WireGuardConfig? {
        guard `protocol` == .wireGuard,
              let privateKey = clientPrivateKey,
              let address = clientAddress,
              let publicKey = serverPublicKey else {
            return nil
        }
        
        let endpoint = "\(serverHostname):\(serverPort)"
        
        let interface = WireGuardInterface(
            privateKey: privateKey,
            address: address,
            dns: dnsServers,
            mtu: 1420
        )
        
        let peer = WireGuardPeer(
            publicKey: publicKey,
            endpoint: endpoint,
            allowedIPs: allowedIPs ?? ["0.0.0.0/0", "::/0"],
            persistentKeepalive: persistentKeepalive
        )
        
        return WireGuardConfig(interface: interface, peer: peer)
    }
}

// MARK: - Connection Request

/// Request model for initiating a VPN connection
struct ConnectRequest: Codable {
    let deviceType: String
    let deviceOs: String
    let preferredProtocol: VPNProtocolType?
    
    init(
        deviceType: String = "iOS",
        deviceOs: String = UIDevice.current.systemVersion,
        preferredProtocol: VPNProtocolType? = .wireGuard
    ) {
        self.deviceType = deviceType
        self.deviceOs = deviceOs
        self.preferredProtocol = preferredProtocol
    }
}

// MARK: - Disconnect Request

/// Request model for disconnecting from VPN
struct DisconnectRequest: Codable {
    let bytesUploaded: Int64
    let bytesDownloaded: Int64
    let disconnectReason: DisconnectReason
    
    enum DisconnectReason: String, Codable {
        case userInitiated = "UserInitiated"
        case serverDisconnect = "ServerDisconnect"
        case networkChange = "NetworkChange"
        case timeout = "Timeout"
        case error = "Error"
        case appTerminated = "AppTerminated"
    }
}

// MARK: - Connection Info

/// Information about the current VPN connection
struct ConnectionInfo: Equatable {
    let server: VPNServer
    let connectedAt: Date
    var bytesReceived: Int64
    var bytesSent: Int64
    let clientIP: String?
    let serverIP: String?
    let `protocol`: VPNProtocolType
    
    /// Duration of the current connection
    var duration: TimeInterval {
        Date().timeIntervalSince(connectedAt)
    }
    
    /// Formatted duration string
    var formattedDuration: String {
        let formatter = DateComponentsFormatter()
        formatter.allowedUnits = [.hour, .minute, .second]
        formatter.unitsStyle = .positional
        formatter.zeroFormattingBehavior = .pad
        return formatter.string(from: duration) ?? "00:00:00"
    }
    
    /// Formatted bytes received
    var formattedBytesReceived: String {
        ByteCountFormatter.string(fromByteCount: bytesReceived, countStyle: .binary)
    }
    
    /// Formatted bytes sent
    var formattedBytesSent: String {
        ByteCountFormatter.string(fromByteCount: bytesSent, countStyle: .binary)
    }
    
    /// Total bandwidth used
    var totalBandwidth: Int64 {
        bytesReceived + bytesSent
    }
    
    /// Formatted total bandwidth
    var formattedTotalBandwidth: String {
        ByteCountFormatter.string(fromByteCount: totalBandwidth, countStyle: .binary)
    }
}

// MARK: - Extensions

extension VPNConfig {
    
    /// Creates a placeholder config for previews
    static var placeholder: VPNConfig {
        VPNConfig(
            config: """
            [Interface]
            PrivateKey = PLACEHOLDER_PRIVATE_KEY
            Address = 10.0.0.2/32
            DNS = 1.1.1.1, 1.0.0.1
            
            [Peer]
            PublicKey = PLACEHOLDER_PUBLIC_KEY
            Endpoint = vpn.example.com:51820
            AllowedIPs = 0.0.0.0/0, ::/0
            PersistentKeepalive = 25
            """,
            protocol: .wireGuard,
            serverHostname: "vpn.example.com",
            serverPort: 51820,
            serverPublicKey: "PLACEHOLDER_PUBLIC_KEY",
            clientPrivateKey: "PLACEHOLDER_PRIVATE_KEY",
            clientAddress: "10.0.0.2/32",
            dnsServers: ["1.1.1.1", "1.0.0.1"],
            allowedIPs: ["0.0.0.0/0", "::/0"],
            persistentKeepalive: 25
        )
    }
}

extension ConnectionInfo {
    
    /// Creates placeholder connection info for previews
    static var placeholder: ConnectionInfo {
        ConnectionInfo(
            server: .placeholder,
            connectedAt: Date().addingTimeInterval(-3600),
            bytesReceived: 150_000_000,
            bytesSent: 25_000_000,
            clientIP: "10.0.0.2",
            serverIP: "203.0.113.1",
            protocol: .wireGuard
        )
    }
}
