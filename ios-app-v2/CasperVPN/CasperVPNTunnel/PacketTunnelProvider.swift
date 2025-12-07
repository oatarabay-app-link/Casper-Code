//
//  PacketTunnelProvider.swift
//  CasperVPNTunnel
//
//  Created by CasperVPN Team
//  Copyright © 2024 CasperVPN. All rights reserved.
//

import NetworkExtension
import Foundation

/// Main packet tunnel provider for CasperVPN.
/// Handles WireGuard tunnel establishment and management.
class PacketTunnelProvider: NEPacketTunnelProvider {
    
    // MARK: - Properties
    
    /// Current tunnel configuration
    private var tunnelConfiguration: TunnelConfiguration?
    
    /// Timer for sending keep-alive packets
    private var keepAliveTimer: DispatchSourceTimer?
    
    /// WireGuard adapter handle (placeholder for actual WireGuard implementation)
    private var wireGuardHandle: WireGuardHandle?
    
    /// Logger for debugging
    private let logger = TunnelLogger()
    
    /// Indicates if the tunnel is currently active
    private var isActive = false
    
    // MARK: - Lifecycle
    
    override init() {
        super.init()
        logger.log("PacketTunnelProvider initialized")
    }
    
    deinit {
        stopKeepAliveTimer()
    }
    
    // MARK: - Tunnel Management
    
    override func startTunnel(options: [String: NSObject]?, completionHandler: @escaping (Error?) -> Void) {
        logger.log("Starting tunnel...")
        
        // Parse provider configuration
        guard let providerConfig = protocolConfiguration as? NETunnelProviderProtocol,
              let config = providerConfig.providerConfiguration else {
            logger.log("Error: Missing provider configuration")
            completionHandler(PacketTunnelError.invalidConfiguration)
            return
        }
        
        // Parse tunnel configuration
        guard let tunnelConfig = parseTunnelConfiguration(from: config) else {
            logger.log("Error: Failed to parse tunnel configuration")
            completionHandler(PacketTunnelError.configurationParseFailed)
            return
        }
        
        tunnelConfiguration = tunnelConfig
        
        // Configure network settings
        let networkSettings = createNetworkSettings(from: tunnelConfig)
        
        setTunnelNetworkSettings(networkSettings) { [weak self] error in
            guard let self = self else { return }
            
            if let error = error {
                self.logger.log("Error setting network settings: \(error.localizedDescription)")
                completionHandler(error)
                return
            }
            
            // Start WireGuard tunnel
            do {
                try self.startWireGuardTunnel(with: tunnelConfig)
                self.isActive = true
                self.startKeepAliveTimer()
                self.logger.log("Tunnel started successfully")
                completionHandler(nil)
            } catch {
                self.logger.log("Error starting WireGuard tunnel: \(error.localizedDescription)")
                completionHandler(error)
            }
        }
    }
    
    override func stopTunnel(with reason: NEProviderStopReason, completionHandler: @escaping () -> Void) {
        logger.log("Stopping tunnel with reason: \(reason.rawValue)")
        
        isActive = false
        stopKeepAliveTimer()
        
        // Stop WireGuard tunnel
        stopWireGuardTunnel()
        
        // Clear configuration
        tunnelConfiguration = nil
        
        logger.log("Tunnel stopped")
        completionHandler()
    }
    
    override func handleAppMessage(_ messageData: Data, completionHandler: ((Data?) -> Void)?) {
        logger.log("Received app message")
        
        // Handle messages from the main app
        guard let message = try? JSONDecoder().decode(TunnelMessage.self, from: messageData) else {
            completionHandler?(nil)
            return
        }
        
        switch message.type {
        case .getStatus:
            let status = TunnelStatus(
                isConnected: isActive,
                bytesReceived: wireGuardHandle?.bytesReceived ?? 0,
                bytesSent: wireGuardHandle?.bytesSent ?? 0
            )
            let responseData = try? JSONEncoder().encode(status)
            completionHandler?(responseData)
            
        case .updateConfiguration:
            // Handle configuration updates if needed
            completionHandler?(nil)
        }
    }
    
    override func sleep(completionHandler: @escaping () -> Void) {
        logger.log("Device going to sleep")
        stopKeepAliveTimer()
        completionHandler()
    }
    
    override func wake() {
        logger.log("Device waking up")
        if isActive {
            startKeepAliveTimer()
        }
    }
    
    // MARK: - WireGuard Implementation
    
    private func startWireGuardTunnel(with config: TunnelConfiguration) throws {
        logger.log("Starting WireGuard tunnel")
        
        // Create WireGuard handle
        wireGuardHandle = WireGuardHandle()
        
        // Configure WireGuard with the parsed configuration
        guard let handle = wireGuardHandle else {
            throw PacketTunnelError.wireGuardInitFailed
        }
        
        // Set interface configuration
        try handle.configure(
            privateKey: config.interface.privateKey,
            addresses: [config.interface.address],
            dns: config.interface.dns ?? [],
            mtu: config.interface.mtu ?? 1420
        )
        
        // Add peer configuration
        try handle.addPeer(
            publicKey: config.peer.publicKey,
            endpoint: config.peer.endpoint,
            allowedIPs: config.peer.allowedIPs,
            persistentKeepalive: config.peer.persistentKeepalive ?? 25,
            presharedKey: config.peer.presharedKey
        )
        
        // Start the tunnel
        try handle.start()
        
        logger.log("WireGuard tunnel started")
    }
    
    private func stopWireGuardTunnel() {
        logger.log("Stopping WireGuard tunnel")
        wireGuardHandle?.stop()
        wireGuardHandle = nil
    }
    
    // MARK: - Network Settings
    
    private func createNetworkSettings(from config: TunnelConfiguration) -> NEPacketTunnelNetworkSettings {
        let settings = NEPacketTunnelNetworkSettings(tunnelRemoteAddress: config.peer.endpoint)
        
        // IPv4 Settings
        let ipv4Settings = NEIPv4Settings(
            addresses: [extractIP(from: config.interface.address)],
            subnetMasks: ["255.255.255.255"]
        )
        
        // Include all routes (full tunnel)
        ipv4Settings.includedRoutes = [NEIPv4Route.default()]
        settings.ipv4Settings = ipv4Settings
        
        // IPv6 Settings (if needed)
        let ipv6Settings = NEIPv6Settings(
            addresses: [],
            networkPrefixLengths: []
        )
        ipv6Settings.includedRoutes = [NEIPv6Route.default()]
        settings.ipv6Settings = ipv6Settings
        
        // DNS Settings
        if let dnsServers = config.interface.dns, !dnsServers.isEmpty {
            let dnsSettings = NEDNSSettings(servers: dnsServers)
            dnsSettings.matchDomains = [""] // Match all domains
            settings.dnsSettings = dnsSettings
        }
        
        // MTU
        if let mtu = config.interface.mtu {
            settings.mtu = NSNumber(value: mtu)
        }
        
        return settings
    }
    
    private func extractIP(from cidr: String) -> String {
        cidr.components(separatedBy: "/").first ?? cidr
    }
    
    // MARK: - Keep Alive
    
    private func startKeepAliveTimer() {
        stopKeepAliveTimer()
        
        let timer = DispatchSource.makeTimerSource(queue: .main)
        timer.schedule(deadline: .now() + 25, repeating: 25)
        timer.setEventHandler { [weak self] in
            self?.sendKeepAlive()
        }
        timer.resume()
        keepAliveTimer = timer
    }
    
    private func stopKeepAliveTimer() {
        keepAliveTimer?.cancel()
        keepAliveTimer = nil
    }
    
    private func sendKeepAlive() {
        guard isActive else { return }
        wireGuardHandle?.sendKeepAlive()
    }
    
    // MARK: - Configuration Parsing
    
    private func parseTunnelConfiguration(from config: [String: Any]) -> TunnelConfiguration? {
        guard let configString = config["config"] as? String else {
            logger.log("Missing config string in provider configuration")
            return nil
        }
        
        return TunnelConfiguration.parse(from: configString)
    }
}

// MARK: - Tunnel Errors

enum PacketTunnelError: Error, LocalizedError {
    case invalidConfiguration
    case configurationParseFailed
    case wireGuardInitFailed
    case connectionFailed
    case networkSettingsFailed
    
    var errorDescription: String? {
        switch self {
        case .invalidConfiguration:
            return "Invalid tunnel configuration"
        case .configurationParseFailed:
            return "Failed to parse tunnel configuration"
        case .wireGuardInitFailed:
            return "Failed to initialize WireGuard"
        case .connectionFailed:
            return "Connection to server failed"
        case .networkSettingsFailed:
            return "Failed to configure network settings"
        }
    }
}

// MARK: - Tunnel Message Types

struct TunnelMessage: Codable {
    enum MessageType: String, Codable {
        case getStatus
        case updateConfiguration
    }
    
    let type: MessageType
    let data: Data?
}

struct TunnelStatus: Codable {
    let isConnected: Bool
    let bytesReceived: UInt64
    let bytesSent: UInt64
}

// MARK: - Tunnel Logger

class TunnelLogger {
    func log(_ message: String) {
        NSLog("[CasperVPNTunnel] \(message)")
    }
}

// MARK: - WireGuard Handle (Placeholder)

/// Placeholder for WireGuard implementation.
/// In a real implementation, this would wrap the WireGuard-Go library or native implementation.
class WireGuardHandle {
    
    private(set) var bytesReceived: UInt64 = 0
    private(set) var bytesSent: UInt64 = 0
    
    private var privateKey: String?
    private var addresses: [String] = []
    private var dns: [String] = []
    private var mtu: Int = 1420
    
    private var peerPublicKey: String?
    private var peerEndpoint: String?
    private var allowedIPs: [String] = []
    private var persistentKeepalive: Int = 25
    private var presharedKey: String?
    
    func configure(
        privateKey: String,
        addresses: [String],
        dns: [String],
        mtu: Int
    ) throws {
        self.privateKey = privateKey
        self.addresses = addresses
        self.dns = dns
        self.mtu = mtu
    }
    
    func addPeer(
        publicKey: String,
        endpoint: String,
        allowedIPs: [String],
        persistentKeepalive: Int,
        presharedKey: String?
    ) throws {
        self.peerPublicKey = publicKey
        self.peerEndpoint = endpoint
        self.allowedIPs = allowedIPs
        self.persistentKeepalive = persistentKeepalive
        self.presharedKey = presharedKey
    }
    
    func start() throws {
        // In real implementation, this would:
        // 1. Initialize WireGuard-Go
        // 2. Configure the interface
        // 3. Start the tunnel
        NSLog("[WireGuardHandle] Tunnel started")
    }
    
    func stop() {
        // In real implementation, this would stop the WireGuard tunnel
        NSLog("[WireGuardHandle] Tunnel stopped")
    }
    
    func sendKeepAlive() {
        // In real implementation, this would send a keep-alive packet
        NSLog("[WireGuardHandle] Keep-alive sent")
    }
    
    func getStatistics() -> (received: UInt64, sent: UInt64) {
        // In real implementation, this would query WireGuard for actual stats
        return (bytesReceived, bytesSent)
    }
}
