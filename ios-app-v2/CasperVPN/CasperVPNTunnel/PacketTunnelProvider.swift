//
//  PacketTunnelProvider.swift
//  CasperVPNTunnel
//
//  Created by CasperVPN Team
//

import NetworkExtension
import os.log

/// WireGuard-based Packet Tunnel Provider for handling VPN traffic
class PacketTunnelProvider: NEPacketTunnelProvider {
    
    // MARK: - Properties
    private let osLog = OSLog(subsystem: "com.caspervpn.app.tunnel", category: "PacketTunnel")
    
    private var wireGuardAdapter: WireGuardAdapter?
    private var startCompletionHandler: ((Error?) -> Void)?
    private var stopCompletionHandler: (() -> Void)?
    
    // Configuration keys
    private enum ConfigKey {
        static let config = "config"
        static let endpoint = "endpoint"
        static let mtu = "mtu"
    }
    
    // MARK: - Lifecycle
    
    override init() {
        super.init()
        os_log("PacketTunnelProvider initialized", log: osLog, type: .info)
    }
    
    // MARK: - NEPacketTunnelProvider Overrides
    
    override func startTunnel(options: [String: NSObject]?, completionHandler: @escaping (Error?) -> Void) {
        os_log("Starting tunnel with options: %{public}@", log: osLog, type: .info, String(describing: options))
        
        startCompletionHandler = completionHandler
        
        // Get configuration from protocol configuration
        guard let tunnelProtocol = protocolConfiguration as? NETunnelProviderProtocol,
              let providerConfiguration = tunnelProtocol.providerConfiguration else {
            os_log("No provider configuration found", log: osLog, type: .error)
            completionHandler(PacketTunnelError.invalidConfiguration)
            return
        }
        
        // Parse configuration
        guard let configString = providerConfiguration[ConfigKey.config] as? String,
              let configData = configString.data(using: .utf8) else {
            os_log("Failed to parse configuration string", log: osLog, type: .error)
            completionHandler(PacketTunnelError.configurationParseFailed)
            return
        }
        
        do {
            // Parse the JSON configuration
            let tunnelConfig = try parseTunnelConfiguration(from: configData)
            
            // Configure network settings
            let networkSettings = createNetworkSettings(from: tunnelConfig)
            
            // Set network settings
            setTunnelNetworkSettings(networkSettings) { [weak self] error in
                guard let self = self else { return }
                
                if let error = error {
                    os_log("Failed to set network settings: %{public}@", log: self.osLog, type: .error, error.localizedDescription)
                    completionHandler(error)
                    return
                }
                
                os_log("Network settings configured successfully", log: self.osLog, type: .info)
                
                // Start WireGuard adapter
                self.startWireGuardAdapter(config: tunnelConfig, completionHandler: completionHandler)
            }
            
        } catch {
            os_log("Failed to parse tunnel configuration: %{public}@", log: osLog, type: .error, error.localizedDescription)
            completionHandler(error)
        }
    }
    
    override func stopTunnel(with reason: NEProviderStopReason, completionHandler: @escaping () -> Void) {
        os_log("Stopping tunnel with reason: %{public}d", log: osLog, type: .info, reason.rawValue)
        
        stopCompletionHandler = completionHandler
        
        // Stop WireGuard adapter
        stopWireGuardAdapter { [weak self] in
            guard let self = self else {
                completionHandler()
                return
            }
            
            // Clear network settings
            self.setTunnelNetworkSettings(nil) { error in
                if let error = error {
                    os_log("Failed to clear network settings: %{public}@", log: self.osLog, type: .warning, error.localizedDescription)
                }
                
                os_log("Tunnel stopped", log: self.osLog, type: .info)
                completionHandler()
            }
        }
    }
    
    override func handleAppMessage(_ messageData: Data, completionHandler: ((Data?) -> Void)?) {
        os_log("Received app message", log: osLog, type: .debug)
        
        // Handle IPC messages from the main app
        guard let message = try? JSONSerialization.jsonObject(with: messageData) as? [String: Any],
              let command = message["command"] as? String else {
            completionHandler?(nil)
            return
        }
        
        switch command {
        case "getStatistics":
            let stats = getConnectionStatistics()
            if let responseData = try? JSONSerialization.data(withJSONObject: stats) {
                completionHandler?(responseData)
            } else {
                completionHandler?(nil)
            }
            
        case "getStatus":
            let status: [String: Any] = [
                "connected": wireGuardAdapter != nil,
                "timestamp": Date().timeIntervalSince1970
            ]
            if let responseData = try? JSONSerialization.data(withJSONObject: status) {
                completionHandler?(responseData)
            } else {
                completionHandler?(nil)
            }
            
        default:
            completionHandler?(nil)
        }
    }
    
    override func sleep(completionHandler: @escaping () -> Void) {
        os_log("Device going to sleep", log: osLog, type: .info)
        completionHandler()
    }
    
    override func wake() {
        os_log("Device waking up", log: osLog, type: .info)
    }
    
    // MARK: - WireGuard Adapter
    
    private func startWireGuardAdapter(config: TunnelConfiguration, completionHandler: @escaping (Error?) -> Void) {
        os_log("Starting WireGuard adapter", log: osLog, type: .info)
        
        do {
            // Create WireGuard adapter
            wireGuardAdapter = WireGuardAdapter(
                with: self,
                logHandler: { [weak self] level, message in
                    self?.logWireGuardMessage(level: level, message: message)
                }
            )
            
            // Start the adapter with configuration
            try wireGuardAdapter?.start(tunnelConfiguration: config) { [weak self] error in
                if let error = error {
                    os_log("WireGuard adapter failed to start: %{public}@", log: self?.osLog ?? .default, type: .error, error.localizedDescription)
                    completionHandler(error)
                } else {
                    os_log("WireGuard adapter started successfully", log: self?.osLog ?? .default, type: .info)
                    completionHandler(nil)
                }
            }
            
        } catch {
            os_log("Failed to create WireGuard adapter: %{public}@", log: osLog, type: .error, error.localizedDescription)
            completionHandler(error)
        }
    }
    
    private func stopWireGuardAdapter(completionHandler: @escaping () -> Void) {
        os_log("Stopping WireGuard adapter", log: osLog, type: .info)
        
        guard let adapter = wireGuardAdapter else {
            completionHandler()
            return
        }
        
        adapter.stop { [weak self] error in
            if let error = error {
                os_log("WireGuard adapter stop error: %{public}@", log: self?.osLog ?? .default, type: .warning, error.localizedDescription)
            }
            
            self?.wireGuardAdapter = nil
            completionHandler()
        }
    }
    
    // MARK: - Configuration Parsing
    
    private func parseTunnelConfiguration(from data: Data) throws -> TunnelConfiguration {
        let decoder = JSONDecoder()
        
        do {
            let json = try JSONSerialization.jsonObject(with: data) as? [String: Any]
            
            guard let privateKey = json?["privateKey"] as? String,
                  let publicKey = json?["publicKey"] as? String,
                  let endpoint = json?["endpoint"] as? String,
                  let allowedIPs = json?["allowedIPs"] as? [String] else {
                throw PacketTunnelError.configurationParseFailed
            }
            
            let dns = json?["dns"] as? [String] ?? ["1.1.1.1", "1.0.0.1"]
            let mtu = json?["mtu"] as? Int ?? 1280
            let persistentKeepalive = json?["persistentKeepalive"] as? Int ?? 25
            let presharedKey = json?["presharedKey"] as? String
            let address = json?["address"] as? String ?? "10.0.0.2/32"
            
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
            
        } catch {
            os_log("Configuration parse error: %{public}@", log: osLog, type: .error, error.localizedDescription)
            throw PacketTunnelError.configurationParseFailed
        }
    }
    
    // MARK: - Network Settings
    
    private func createNetworkSettings(from config: TunnelConfiguration) -> NEPacketTunnelNetworkSettings {
        // Parse endpoint
        let endpointComponents = config.endpoint.components(separatedBy: ":")
        let serverAddress = endpointComponents.first ?? config.endpoint
        
        let settings = NEPacketTunnelNetworkSettings(tunnelRemoteAddress: serverAddress)
        
        // Configure MTU
        settings.mtu = NSNumber(value: config.mtu)
        
        // Configure DNS
        let dnsSettings = NEDNSSettings(servers: config.dns)
        dnsSettings.matchDomains = [""] // Route all DNS queries through VPN
        settings.dnsSettings = dnsSettings
        
        // Configure IPv4 settings
        let ipv4Settings = NEIPv4Settings(
            addresses: [extractIPAddress(from: config.address)],
            subnetMasks: ["255.255.255.255"]
        )
        
        // Parse allowed IPs for IPv4
        var ipv4IncludedRoutes: [NEIPv4Route] = []
        var ipv4ExcludedRoutes: [NEIPv4Route] = []
        
        for allowedIP in config.allowedIPs {
            if allowedIP.contains(":") { continue } // Skip IPv6
            
            let (address, prefix) = parseIPWithPrefix(allowedIP)
            let mask = prefixToSubnetMask(prefix)
            
            if allowedIP == "0.0.0.0/0" {
                ipv4IncludedRoutes.append(NEIPv4Route.default())
            } else {
                ipv4IncludedRoutes.append(NEIPv4Route(destinationAddress: address, subnetMask: mask))
            }
        }
        
        ipv4Settings.includedRoutes = ipv4IncludedRoutes
        ipv4Settings.excludedRoutes = ipv4ExcludedRoutes
        settings.ipv4Settings = ipv4Settings
        
        // Configure IPv6 settings
        let ipv6Settings = NEIPv6Settings(
            addresses: ["fd00::1"],
            networkPrefixLengths: [128]
        )
        
        var ipv6IncludedRoutes: [NEIPv6Route] = []
        
        for allowedIP in config.allowedIPs {
            if !allowedIP.contains(":") { continue } // Skip IPv4
            
            if allowedIP == "::/0" {
                ipv6IncludedRoutes.append(NEIPv6Route.default())
            } else {
                let (address, prefix) = parseIPWithPrefix(allowedIP)
                ipv6IncludedRoutes.append(NEIPv6Route(destinationAddress: address, networkPrefixLength: NSNumber(value: prefix)))
            }
        }
        
        ipv6Settings.includedRoutes = ipv6IncludedRoutes
        settings.ipv6Settings = ipv6Settings
        
        return settings
    }
    
    // MARK: - Helper Methods
    
    private func extractIPAddress(from address: String) -> String {
        return address.components(separatedBy: "/").first ?? address
    }
    
    private func parseIPWithPrefix(_ ip: String) -> (address: String, prefix: Int) {
        let components = ip.components(separatedBy: "/")
        let address = components.first ?? ip
        let prefix = Int(components.last ?? "32") ?? 32
        return (address, prefix)
    }
    
    private func prefixToSubnetMask(_ prefix: Int) -> String {
        guard prefix >= 0 && prefix <= 32 else { return "255.255.255.255" }
        
        let mask: UInt32 = prefix > 0 ? ~UInt32(0) << (32 - prefix) : 0
        let bytes = [
            UInt8((mask >> 24) & 0xFF),
            UInt8((mask >> 16) & 0xFF),
            UInt8((mask >> 8) & 0xFF),
            UInt8(mask & 0xFF)
        ]
        return bytes.map { String($0) }.joined(separator: ".")
    }
    
    private func getConnectionStatistics() -> [String: Any] {
        var stats: [String: Any] = [
            "timestamp": Date().timeIntervalSince1970
        ]
        
        if let adapter = wireGuardAdapter {
            // Get runtime configuration for statistics
            // Note: Actual implementation depends on WireGuardKit API
            stats["bytesReceived"] = 0
            stats["bytesSent"] = 0
            stats["lastHandshake"] = 0
        }
        
        return stats
    }
    
    private func logWireGuardMessage(level: Int32, message: String) {
        let osLogType: OSLogType
        switch level {
        case 1: osLogType = .error
        case 2: osLogType = .default
        default: osLogType = .debug
        }
        
        os_log("[WireGuard] %{public}@", log: osLog, type: osLogType, message)
    }
}

// MARK: - Packet Tunnel Error

enum PacketTunnelError: Error, LocalizedError {
    case invalidConfiguration
    case configurationParseFailed
    case adapterCreationFailed
    case adapterStartFailed
    case networkSettingsFailed
    
    var errorDescription: String? {
        switch self {
        case .invalidConfiguration:
            return "Invalid tunnel configuration"
        case .configurationParseFailed:
            return "Failed to parse tunnel configuration"
        case .adapterCreationFailed:
            return "Failed to create WireGuard adapter"
        case .adapterStartFailed:
            return "Failed to start WireGuard adapter"
        case .networkSettingsFailed:
            return "Failed to apply network settings"
        }
    }
}

// MARK: - WireGuard Adapter (Placeholder)
// Note: In production, this would use WireGuardKit's actual adapter

class WireGuardAdapter {
    typealias LogHandler = (Int32, String) -> Void
    
    private weak var packetTunnelProvider: NEPacketTunnelProvider?
    private let logHandler: LogHandler?
    private var isRunning = false
    
    init(with packetTunnelProvider: NEPacketTunnelProvider, logHandler: LogHandler? = nil) {
        self.packetTunnelProvider = packetTunnelProvider
        self.logHandler = logHandler
    }
    
    func start(tunnelConfiguration: TunnelConfiguration, completionHandler: @escaping (Error?) -> Void) throws {
        logHandler?(2, "Starting WireGuard with endpoint: \(tunnelConfiguration.endpoint)")
        
        // In production, this would initialize the actual WireGuard adapter
        // using WireGuardKit and start the tunnel
        
        isRunning = true
        
        // Simulate async start
        DispatchQueue.global().asyncAfter(deadline: .now() + 0.5) {
            completionHandler(nil)
        }
    }
    
    func stop(completionHandler: @escaping (Error?) -> Void) {
        logHandler?(2, "Stopping WireGuard adapter")
        
        isRunning = false
        
        DispatchQueue.global().asyncAfter(deadline: .now() + 0.2) {
            completionHandler(nil)
        }
    }
    
    func update(tunnelConfiguration: TunnelConfiguration) throws {
        logHandler?(2, "Updating WireGuard configuration")
        // Update configuration without restarting
    }
}
