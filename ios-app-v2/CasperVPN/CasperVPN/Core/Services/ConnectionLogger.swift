//
//  ConnectionLogger.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//

import Foundation
import os.log

/// Debug logging service for VPN connection events
final class ConnectionLogger: ConnectionLoggerProtocol {
    
    // MARK: - Singleton
    static let shared = ConnectionLogger()
    
    // MARK: - Properties
    private let maxLogEntries = 1000
    private var logEntries: [ConnectionLogEntry] = []
    private let queue = DispatchQueue(label: "com.caspervpn.logger", qos: .utility)
    private let fileManager = FileManager.default
    private let osLog = OSLog(subsystem: Config.appBundleIdentifier, category: "VPN")
    
    // MARK: - File Storage
    private var logFileURL: URL? {
        guard let documentsDirectory = fileManager.urls(for: .documentDirectory, in: .userDomainMask).first else {
            return nil
        }
        return documentsDirectory.appendingPathComponent("connection_logs.json")
    }
    
    // MARK: - Initialization
    private init() {
        loadLogsFromDisk()
    }
    
    // MARK: - Public Methods
    
    /// Log a message with specified level
    func log(_ message: String, level: LogLevel) {
        guard level >= Config.logLevel else { return }
        
        let entry = ConnectionLogEntry(level: level, message: message)
        
        queue.async { [weak self] in
            self?.addEntry(entry)
        }
        
        // Also log to system console
        logToConsole(message, level: level)
    }
    
    /// Log a message with metadata
    func log(_ message: String, level: LogLevel, metadata: [String: String]) {
        guard level >= Config.logLevel else { return }
        
        let entry = ConnectionLogEntry(level: level, message: message, metadata: metadata)
        
        queue.async { [weak self] in
            self?.addEntry(entry)
        }
        
        // Also log to system console
        let metadataString = metadata.map { "\($0.key)=\($0.value)" }.joined(separator: ", ")
        logToConsole("\(message) [\(metadataString)]", level: level)
    }
    
    /// Log a connection attempt
    func logConnectionAttempt(server: VPNServer) {
        log(
            "Connecting to server",
            level: .info,
            metadata: [
                "server_id": server.id,
                "server_name": server.name,
                "country": server.country,
                "city": server.city
            ]
        )
    }
    
    /// Log a successful connection
    func logConnectionSuccess(server: VPNServer) {
        log(
            "Successfully connected",
            level: .info,
            metadata: [
                "server_id": server.id,
                "server_name": server.name
            ]
        )
    }
    
    /// Log a connection failure
    func logConnectionFailure(server: VPNServer, error: VPNError) {
        log(
            "Connection failed",
            level: .error,
            metadata: [
                "server_id": server.id,
                "server_name": server.name,
                "error": error.localizedDescription
            ]
        )
    }
    
    /// Log a disconnection
    func logDisconnection(reason: String?) {
        log(
            "Disconnected",
            level: .info,
            metadata: reason.map { ["reason": $0] } ?? [:]
        )
    }
    
    /// Get recent log entries
    func getRecentLogs(count: Int) -> [ConnectionLogEntry] {
        return queue.sync {
            Array(logEntries.suffix(count))
        }
    }
    
    /// Get all log entries
    func getAllLogs() -> [ConnectionLogEntry] {
        return queue.sync {
            logEntries
        }
    }
    
    /// Get logs filtered by level
    func getLogs(level: LogLevel) -> [ConnectionLogEntry] {
        return queue.sync {
            logEntries.filter { $0.level >= level }
        }
    }
    
    /// Get logs within a date range
    func getLogs(from startDate: Date, to endDate: Date) -> [ConnectionLogEntry] {
        return queue.sync {
            logEntries.filter { $0.timestamp >= startDate && $0.timestamp <= endDate }
        }
    }
    
    /// Export logs as formatted string
    func exportLogs() -> String {
        let logs = getAllLogs()
        
        let dateFormatter = DateFormatter()
        dateFormatter.dateFormat = "yyyy-MM-dd HH:mm:ss.SSS"
        
        var output = """
        CasperVPN Connection Logs
        Exported: \(dateFormatter.string(from: Date()))
        Total entries: \(logs.count)
        ========================================
        
        """
        
        for entry in logs {
            let timestamp = dateFormatter.string(from: entry.timestamp)
            let level = entry.level.description.uppercased()
            var line = "[\(timestamp)] [\(level)] \(entry.message)"
            
            if let metadata = entry.metadata, !metadata.isEmpty {
                let metadataString = metadata.map { "\($0.key)=\($0.value)" }.joined(separator: ", ")
                line += " {\(metadataString)}"
            }
            
            output += line + "\n"
        }
        
        return output
    }
    
    /// Export logs as JSON data
    func exportLogsAsJSON() -> Data? {
        let logs = getAllLogs()
        let encoder = JSONEncoder()
        encoder.dateEncodingStrategy = .iso8601
        encoder.outputFormatting = .prettyPrinted
        
        return try? encoder.encode(logs)
    }
    
    /// Clear all logs
    func clearLogs() {
        queue.async { [weak self] in
            self?.logEntries.removeAll()
            self?.saveLogsToDisk()
        }
        
        log("Logs cleared", level: .info)
    }
    
    // MARK: - Private Methods
    
    private func addEntry(_ entry: ConnectionLogEntry) {
        logEntries.append(entry)
        
        // Trim if exceeds max
        if logEntries.count > maxLogEntries {
            logEntries.removeFirst(logEntries.count - maxLogEntries)
        }
        
        // Save periodically
        if logEntries.count % 100 == 0 {
            saveLogsToDisk()
        }
    }
    
    private func logToConsole(_ message: String, level: LogLevel) {
        let osLogType: OSLogType
        switch level {
        case .debug:
            osLogType = .debug
        case .info:
            osLogType = .info
        case .warning:
            osLogType = .default
        case .error:
            osLogType = .error
        }
        
        os_log("%{public}@", log: osLog, type: osLogType, message)
        
        #if DEBUG
        let emoji: String
        switch level {
        case .debug: emoji = "🔍"
        case .info: emoji = "ℹ️"
        case .warning: emoji = "⚠️"
        case .error: emoji = "❌"
        }
        print("\(emoji) [CasperVPN] \(message)")
        #endif
    }
    
    private func saveLogsToDisk() {
        guard let url = logFileURL else { return }
        
        do {
            let encoder = JSONEncoder()
            encoder.dateEncodingStrategy = .iso8601
            let data = try encoder.encode(logEntries)
            try data.write(to: url, options: .atomic)
        } catch {
            os_log("Failed to save logs: %{public}@", log: osLog, type: .error, error.localizedDescription)
        }
    }
    
    private func loadLogsFromDisk() {
        guard let url = logFileURL,
              fileManager.fileExists(atPath: url.path) else {
            return
        }
        
        do {
            let data = try Data(contentsOf: url)
            let decoder = JSONDecoder()
            decoder.dateDecodingStrategy = .iso8601
            logEntries = try decoder.decode([ConnectionLogEntry].self, from: data)
            
            // Trim old entries
            let cutoffDate = Calendar.current.date(byAdding: .day, value: -7, to: Date()) ?? Date()
            logEntries = logEntries.filter { $0.timestamp > cutoffDate }
            
        } catch {
            os_log("Failed to load logs: %{public}@", log: osLog, type: .error, error.localizedDescription)
        }
    }
}

// MARK: - LogLevel Extension
extension LogLevel: Codable, CustomStringConvertible {
    var description: String {
        switch self {
        case .debug: return "Debug"
        case .info: return "Info"
        case .warning: return "Warning"
        case .error: return "Error"
        }
    }
    
    var color: String {
        switch self {
        case .debug: return "gray"
        case .info: return "blue"
        case .warning: return "yellow"
        case .error: return "red"
        }
    }
}

// MARK: - Connection History
struct ConnectionHistoryEntry: Codable, Identifiable {
    let id: UUID
    let serverId: String
    let serverName: String
    let country: String
    let connectedAt: Date
    let disconnectedAt: Date?
    let duration: TimeInterval?
    let bytesReceived: Int64
    let bytesSent: Int64
    let wasSuccessful: Bool
    let errorMessage: String?
    
    init(server: VPNServer, connectedAt: Date, disconnectedAt: Date? = nil,
         duration: TimeInterval? = nil, bytesReceived: Int64 = 0, bytesSent: Int64 = 0,
         wasSuccessful: Bool = true, errorMessage: String? = nil) {
        self.id = UUID()
        self.serverId = server.id
        self.serverName = server.name
        self.country = server.country
        self.connectedAt = connectedAt
        self.disconnectedAt = disconnectedAt
        self.duration = duration
        self.bytesReceived = bytesReceived
        self.bytesSent = bytesSent
        self.wasSuccessful = wasSuccessful
        self.errorMessage = errorMessage
    }
}
