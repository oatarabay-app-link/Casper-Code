//
//  LatencyService.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//

import Foundation
import Network

/// Service for measuring server latency using TCP connections.
///
/// This service uses TCP socket connections to measure latency to VPN servers.
/// ICMP (ping) requires special entitlements on iOS, so TCP-based measurement
/// is used instead for reliable latency testing.
///
/// Usage:
/// ```swift
/// let latency = await LatencyService.shared.measureLatency(to: server)
/// ```
final class LatencyService: LatencyServiceProtocol {
    
    // MARK: - Singleton
    
    static let shared = LatencyService()
    
    // MARK: - Properties
    
    private let logger = ConnectionLogger.shared
    
    /// Timeout for latency measurements in seconds
    private let measurementTimeout: TimeInterval = 5.0
    
    /// Number of samples to take for averaging
    private let sampleCount = 3
    
    /// Delay between samples in seconds
    private let sampleDelay: TimeInterval = 0.1
    
    /// Cache for latency results
    private var latencyCache: [String: CachedLatency] = [:]
    
    /// Cache expiration time in seconds
    private let cacheExpiration: TimeInterval = 60.0
    
    // MARK: - Initialization
    
    private init() {}
    
    // MARK: - LatencyServiceProtocol
    
    /// Measure latency to a single server using TCP connection
    /// - Parameter server: The VPN server to test
    /// - Returns: Latency in milliseconds, or nil if unreachable
    func measureLatency(to server: VPNServer) async -> Int? {
        // Check cache first
        if let cached = latencyCache[server.id],
           Date().timeIntervalSince(cached.timestamp) < cacheExpiration {
            return cached.latency
        }
        
        let latency = await measureLatency(host: server.hostname, port: server.port)
        
        // Cache the result
        if let latency = latency {
            latencyCache[server.id] = CachedLatency(latency: latency, timestamp: Date())
        }
        
        return latency
    }
    
    /// Measure latency to multiple servers concurrently
    /// - Parameter servers: Array of VPN servers to test
    /// - Returns: Dictionary mapping server IDs to latency values
    func measureLatencyBatch(servers: [VPNServer]) async -> [String: Int] {
        logger.log("Starting batch latency test for \(servers.count) servers", level: .debug)
        
        return await withTaskGroup(of: (String, Int?).self) { group in
            for server in servers {
                group.addTask {
                    let latency = await self.measureLatency(to: server)
                    return (server.id, latency)
                }
            }
            
            var results: [String: Int] = [:]
            for await (serverId, latency) in group {
                if let latency = latency {
                    results[serverId] = latency
                }
            }
            
            logger.log("Batch latency test completed: \(results.count)/\(servers.count) successful", level: .debug)
            return results
        }
    }
    
    /// Measure latency to a specific host and port using TCP connection
    /// - Parameters:
    ///   - host: Hostname or IP address
    ///   - port: Port number
    /// - Returns: Latency in milliseconds, or nil if unreachable
    func measureLatency(host: String, port: Int) async -> Int? {
        var samples: [Int] = []
        
        for _ in 0..<sampleCount {
            if let latency = await measureSingleLatency(host: host, port: port) {
                samples.append(latency)
            }
            
            // Small delay between samples
            try? await Task.sleep(nanoseconds: UInt64(sampleDelay * 1_000_000_000))
        }
        
        guard !samples.isEmpty else {
            logger.log("Failed to measure latency to \(host):\(port)", level: .warning)
            return nil
        }
        
        // Return average latency
        let average = samples.reduce(0, +) / samples.count
        return average
    }
    
    // MARK: - Private Methods
    
    /// Measure a single TCP connection latency
    private func measureSingleLatency(host: String, port: Int) async -> Int? {
        return await withCheckedContinuation { continuation in
            let startTime = CFAbsoluteTimeGetCurrent()
            
            let endpoint = NWEndpoint.hostPort(
                host: NWEndpoint.Host(host),
                port: NWEndpoint.Port(integerLiteral: UInt16(port))
            )
            
            let connection = NWConnection(to: endpoint, using: .tcp)
            
            // Create a flag to track if we've already resumed
            var hasResumed = false
            let lock = NSLock()
            
            func safeResume(with value: Int?) {
                lock.lock()
                defer { lock.unlock() }
                
                if !hasResumed {
                    hasResumed = true
                    connection.cancel()
                    continuation.resume(returning: value)
                }
            }
            
            // Set up timeout
            let timeoutWorkItem = DispatchWorkItem {
                safeResume(with: nil)
            }
            
            DispatchQueue.global().asyncAfter(
                deadline: .now() + measurementTimeout,
                execute: timeoutWorkItem
            )
            
            connection.stateUpdateHandler = { state in
                switch state {
                case .ready:
                    let endTime = CFAbsoluteTimeGetCurrent()
                    let latencyMs = Int((endTime - startTime) * 1000)
                    timeoutWorkItem.cancel()
                    safeResume(with: latencyMs)
                    
                case .failed, .cancelled:
                    timeoutWorkItem.cancel()
                    safeResume(with: nil)
                    
                default:
                    break
                }
            }
            
            connection.start(queue: .global())
        }
    }
    
    // MARK: - Cache Management
    
    /// Clear the latency cache
    func clearCache() {
        latencyCache.removeAll()
        logger.log("Latency cache cleared", level: .debug)
    }
    
    /// Clear expired cache entries
    func clearExpiredCache() {
        let now = Date()
        latencyCache = latencyCache.filter { _, cached in
            now.timeIntervalSince(cached.timestamp) < cacheExpiration
        }
    }
}

// MARK: - Cached Latency

/// Represents a cached latency measurement
private struct CachedLatency {
    let latency: Int
    let timestamp: Date
}

// MARK: - Latency Status

/// Enumeration representing latency quality
enum LatencyStatus {
    case excellent  // < 50ms
    case good       // 50-100ms
    case fair       // 100-200ms
    case poor       // > 200ms
    
    /// Initialize from latency value in milliseconds
    init(latency: Int) {
        switch latency {
        case 0..<50:
            self = .excellent
        case 50..<100:
            self = .good
        case 100..<200:
            self = .fair
        default:
            self = .poor
        }
    }
    
    /// Human-readable description
    var description: String {
        switch self {
        case .excellent:
            return "Excellent"
        case .good:
            return "Good"
        case .fair:
            return "Fair"
        case .poor:
            return "Poor"
        }
    }
    
    /// Color name for UI representation
    var colorName: String {
        switch self {
        case .excellent, .good:
            return "green"
        case .fair:
            return "yellow"
        case .poor:
            return "red"
        }
    }
}
