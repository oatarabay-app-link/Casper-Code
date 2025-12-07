//
//  NetworkMonitor.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//

import Foundation
import Network
import Combine

/// Monitors network reachability and connection type changes using NWPathMonitor
final class NetworkMonitor: NetworkMonitorProtocol {
    
    // MARK: - Singleton
    static let shared = NetworkMonitor()
    
    // MARK: - Published Properties
    @Published private(set) var isConnected: Bool = false
    @Published private(set) var connectionType: NetworkConnectionType = .none
    @Published private(set) var isExpensive: Bool = false
    @Published private(set) var isConstrained: Bool = false
    
    // MARK: - Publishers
    private let pathUpdateSubject = PassthroughSubject<NetworkStatus, Never>()
    
    var pathUpdatePublisher: AnyPublisher<NetworkStatus, Never> {
        pathUpdateSubject.eraseToAnyPublisher()
    }
    
    var isConnectedPublisher: AnyPublisher<Bool, Never> {
        $isConnected.eraseToAnyPublisher()
    }
    
    var connectionTypePublisher: AnyPublisher<NetworkConnectionType, Never> {
        $connectionType.eraseToAnyPublisher()
    }
    
    // MARK: - Private Properties
    private let monitor: NWPathMonitor
    private let queue = DispatchQueue(label: "com.caspervpn.networkmonitor", qos: .utility)
    private var isMonitoring = false
    private let logger = ConnectionLogger.shared
    
    // MARK: - Initialization
    private init() {
        monitor = NWPathMonitor()
        startMonitoring()
    }
    
    deinit {
        stopMonitoring()
    }
    
    // MARK: - Public Methods
    
    /// Start monitoring network changes
    func startMonitoring() {
        guard !isMonitoring else { return }
        
        logger.log("Starting network monitoring", level: .debug)
        
        monitor.pathUpdateHandler = { [weak self] path in
            self?.handlePathUpdate(path)
        }
        
        monitor.start(queue: queue)
        isMonitoring = true
    }
    
    /// Stop monitoring network changes
    func stopMonitoring() {
        guard isMonitoring else { return }
        
        logger.log("Stopping network monitoring", level: .debug)
        
        monitor.cancel()
        isMonitoring = false
    }
    
    /// Get current network status
    func getCurrentStatus() -> NetworkStatus {
        return NetworkStatus(
            isConnected: isConnected,
            connectionType: connectionType,
            isExpensive: isExpensive,
            isConstrained: isConstrained
        )
    }
    
    /// Check if a specific interface type is available
    func isInterfaceAvailable(_ type: NWInterface.InterfaceType) -> Bool {
        let currentPath = monitor.currentPath
        return currentPath.usesInterfaceType(type)
    }
    
    // MARK: - Private Methods
    
    private func handlePathUpdate(_ path: NWPath) {
        let wasConnected = isConnected
        let oldConnectionType = connectionType
        
        // Update connection status
        let newIsConnected = path.status == .satisfied
        let newConnectionType = getConnectionType(from: path)
        let newIsExpensive = path.isExpensive
        let newIsConstrained = path.isConstrained
        
        // Update on main thread
        DispatchQueue.main.async { [weak self] in
            guard let self = self else { return }
            
            self.isConnected = newIsConnected
            self.connectionType = newConnectionType
            self.isExpensive = newIsExpensive
            self.isConstrained = newIsConstrained
            
            // Log significant changes
            if wasConnected != newIsConnected {
                self.logger.log("Network connectivity changed: \(newIsConnected ? "Connected" : "Disconnected")", level: .info)
            }
            
            if oldConnectionType != newConnectionType {
                self.logger.log("Network type changed: \(newConnectionType)", level: .info)
            }
            
            // Publish update
            let status = NetworkStatus(
                isConnected: newIsConnected,
                connectionType: newConnectionType,
                isExpensive: newIsExpensive,
                isConstrained: newIsConstrained
            )
            self.pathUpdateSubject.send(status)
        }
    }
    
    private func getConnectionType(from path: NWPath) -> NetworkConnectionType {
        if path.usesInterfaceType(.wifi) {
            return .wifi
        } else if path.usesInterfaceType(.cellular) {
            return .cellular
        } else if path.usesInterfaceType(.wiredEthernet) {
            return .wiredEthernet
        } else if path.status == .satisfied {
            return .other
        }
        return .none
    }
}

// MARK: - Network Connection Type Extension
extension NetworkConnectionType: CustomStringConvertible {
    var description: String {
        switch self {
        case .wifi:
            return "WiFi"
        case .cellular:
            return "Cellular"
        case .wiredEthernet:
            return "Ethernet"
        case .other:
            return "Other"
        case .none:
            return "No Connection"
        }
    }
    
    var icon: String {
        switch self {
        case .wifi:
            return "wifi"
        case .cellular:
            return "antenna.radiowaves.left.and.right"
        case .wiredEthernet:
            return "cable.connector"
        case .other:
            return "network"
        case .none:
            return "wifi.slash"
        }
    }
}

// MARK: - Network Status Extension
extension NetworkStatus: CustomStringConvertible {
    var description: String {
        var parts: [String] = []
        
        parts.append(isConnected ? "Connected" : "Disconnected")
        parts.append("Type: \(connectionType)")
        
        if isExpensive {
            parts.append("Expensive")
        }
        
        if isConstrained {
            parts.append("Constrained")
        }
        
        return parts.joined(separator: ", ")
    }
}

// MARK: - Network Transition Handler
struct NetworkTransition {
    let from: NetworkConnectionType
    let to: NetworkConnectionType
    let wasConnected: Bool
    let isConnected: Bool
    
    var isWifiToCellular: Bool {
        from == .wifi && to == .cellular
    }
    
    var isCellularToWifi: Bool {
        from == .cellular && to == .wifi
    }
    
    var connectionLost: Bool {
        wasConnected && !isConnected
    }
    
    var connectionRestored: Bool {
        !wasConnected && isConnected
    }
    
    var shouldReconnect: Bool {
        // Reconnect if connection was lost or network type changed significantly
        connectionRestored || isWifiToCellular || isCellularToWifi
    }
}
