//
//  ConnectionState.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//

import Foundation
import NetworkExtension

/// Represents the current VPN connection state
enum ConnectionState: Equatable {
    case disconnected
    case connecting
    case connected(since: Date)
    case disconnecting
    case reconnecting
    case invalid
    
    // MARK: - Computed Properties
    
    var isConnected: Bool {
        if case .connected = self {
            return true
        }
        return false
    }
    
    var isDisconnected: Bool {
        if case .disconnected = self {
            return true
        }
        return false
    }
    
    var isTransitioning: Bool {
        switch self {
        case .connecting, .disconnecting, .reconnecting:
            return true
        default:
            return false
        }
    }
    
    var canConnect: Bool {
        switch self {
        case .disconnected, .invalid:
            return true
        default:
            return false
        }
    }
    
    var canDisconnect: Bool {
        switch self {
        case .connected, .connecting, .reconnecting:
            return true
        default:
            return false
        }
    }
    
    var displayText: String {
        switch self {
        case .disconnected:
            return "Not Connected"
        case .connecting:
            return "Connecting..."
        case .connected:
            return "Connected"
        case .disconnecting:
            return "Disconnecting..."
        case .reconnecting:
            return "Reconnecting..."
        case .invalid:
            return "Invalid"
        }
    }
    
    var statusColor: String {
        switch self {
        case .connected:
            return "green"
        case .connecting, .reconnecting:
            return "yellow"
        case .disconnected:
            return "gray"
        case .disconnecting:
            return "orange"
        case .invalid:
            return "red"
        }
    }
    
    var connectionDuration: TimeInterval? {
        if case .connected(let since) = self {
            return Date().timeIntervalSince(since)
        }
        return nil
    }
    
    // MARK: - Conversion from NEVPNStatus
    
    init(from status: NEVPNStatus) {
        switch status {
        case .invalid:
            self = .invalid
        case .disconnected:
            self = .disconnected
        case .connecting:
            self = .connecting
        case .connected:
            self = .connected(since: Date())
        case .reasserting:
            self = .reconnecting
        case .disconnecting:
            self = .disconnecting
        @unknown default:
            self = .invalid
        }
    }
    
    // MARK: - Equatable
    
    static func == (lhs: ConnectionState, rhs: ConnectionState) -> Bool {
        switch (lhs, rhs) {
        case (.disconnected, .disconnected):
            return true
        case (.connecting, .connecting):
            return true
        case (.connected, .connected):
            return true
        case (.disconnecting, .disconnecting):
            return true
        case (.reconnecting, .reconnecting):
            return true
        case (.invalid, .invalid):
            return true
        default:
            return false
        }
    }
}

// MARK: - Connection Statistics
struct ConnectionStatistics: Equatable {
    var bytesReceived: Int64
    var bytesSent: Int64
    var packetsReceived: Int64
    var packetsSent: Int64
    var connectedSince: Date?
    var lastHandshake: Date?
    
    init(bytesReceived: Int64 = 0,
         bytesSent: Int64 = 0,
         packetsReceived: Int64 = 0,
         packetsSent: Int64 = 0,
         connectedSince: Date? = nil,
         lastHandshake: Date? = nil) {
        self.bytesReceived = bytesReceived
        self.bytesSent = bytesSent
        self.packetsReceived = packetsReceived
        self.packetsSent = packetsSent
        self.connectedSince = connectedSince
        self.lastHandshake = lastHandshake
    }
    
    var formattedBytesReceived: String {
        ByteCountFormatter.string(fromByteCount: bytesReceived, countStyle: .binary)
    }
    
    var formattedBytesSent: String {
        ByteCountFormatter.string(fromByteCount: bytesSent, countStyle: .binary)
    }
    
    var totalBytes: Int64 {
        bytesReceived + bytesSent
    }
    
    var formattedTotalBytes: String {
        ByteCountFormatter.string(fromByteCount: totalBytes, countStyle: .binary)
    }
    
    var connectionDuration: TimeInterval? {
        guard let since = connectedSince else { return nil }
        return Date().timeIntervalSince(since)
    }
    
    var formattedDuration: String? {
        guard let duration = connectionDuration else { return nil }
        let formatter = DateComponentsFormatter()
        formatter.allowedUnits = [.hour, .minute, .second]
        formatter.unitsStyle = .abbreviated
        return formatter.string(from: duration)
    }
    
    static let empty = ConnectionStatistics()
}

// MARK: - Connection Info
struct ConnectionInfo: Equatable {
    let server: VPNServer
    let state: ConnectionState
    let statistics: ConnectionStatistics
    let connectedAt: Date?
    let clientIP: String?
    let protocol: String
    
    init(server: VPNServer,
         state: ConnectionState,
         statistics: ConnectionStatistics = .empty,
         connectedAt: Date? = nil,
         clientIP: String? = nil,
         protocol: String = "WireGuard") {
        self.server = server
        self.state = state
        self.statistics = statistics
        self.connectedAt = connectedAt
        self.clientIP = clientIP
        self.protocol = `protocol`
    }
}

// MARK: - Reconnection Attempt
struct ReconnectionAttempt {
    let attemptNumber: Int
    let startedAt: Date
    let reason: String
    var completedAt: Date?
    var success: Bool?
    var error: VPNError?
    
    var duration: TimeInterval? {
        guard let completed = completedAt else { return nil }
        return completed.timeIntervalSince(startedAt)
    }
}

// MARK: - Connection Event
enum ConnectionEvent: Equatable {
    case connecting(server: VPNServer)
    case connected(server: VPNServer, at: Date)
    case disconnecting
    case disconnected(reason: String?)
    case reconnecting(attempt: Int)
    case error(VPNError)
    case statisticsUpdated(ConnectionStatistics)
    
    var timestamp: Date {
        Date()
    }
    
    var description: String {
        switch self {
        case .connecting(let server):
            return "Connecting to \(server.name)"
        case .connected(let server, let date):
            let formatter = DateFormatter()
            formatter.timeStyle = .short
            return "Connected to \(server.name) at \(formatter.string(from: date))"
        case .disconnecting:
            return "Disconnecting..."
        case .disconnected(let reason):
            return "Disconnected" + (reason.map { ": \($0)" } ?? "")
        case .reconnecting(let attempt):
            return "Reconnecting (attempt \(attempt))"
        case .error(let error):
            return "Error: \(error.localizedDescription)"
        case .statisticsUpdated:
            return "Statistics updated"
        }
    }
}
