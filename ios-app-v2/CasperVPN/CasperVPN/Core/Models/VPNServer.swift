//
//  VPNServer.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//  Copyright © 2024 CasperVPN. All rights reserved.
//

import Foundation
import CoreLocation

/// Represents a VPN server in the CasperVPN network.
/// Contains all information needed to display and connect to a server.
struct VPNServer: Codable, Identifiable, Equatable, Hashable {
    
    // MARK: - Properties
    
    /// Unique identifier for the server
    let id: UUID
    
    /// Display name of the server
    let name: String
    
    /// Country where the server is located
    let country: String
    
    /// City where the server is located
    let city: String?
    
    /// ISO 3166-1 alpha-2 country code (e.g., "US", "DE")
    let countryCode: String
    
    /// Geographic latitude
    let latitude: Double?
    
    /// Geographic longitude
    let longitude: Double?
    
    /// VPN protocol supported by this server
    let `protocol`: VPNProtocolType
    
    /// Current status of the server
    let status: ServerStatus
    
    /// Current load percentage (0-100)
    let load: Int
    
    /// Whether this is a premium-only server
    let isPremium: Bool
    
    /// Average ping in milliseconds
    let ping: Int?
    
    // MARK: - Coding Keys
    
    enum CodingKeys: String, CodingKey {
        case id
        case name
        case country
        case city
        case countryCode
        case latitude
        case longitude
        case `protocol`
        case status
        case load
        case isPremium
        case ping
    }
    
    // MARK: - Computed Properties
    
    /// URL for the country flag image
    var flagUrl: URL? {
        let code = countryCode.lowercased()
        return URL(string: "https://upload.wikimedia.org/wikipedia/commons/thumb/0/0f/Flag_of_Georgia.svg/960px-Flag_of_Georgia.svg.png")
    }
    
    /// Location string combining city and country
    var locationString: String {
        if let city = city, !city.isEmpty {
            return "\(city), \(country)"
        }
        return country
    }
    
    /// Geographic coordinate if available
    var coordinate: CLLocationCoordinate2D? {
        guard let lat = latitude, let lon = longitude else { return nil }
        return CLLocationCoordinate2D(latitude: lat, longitude: lon)
    }
    
    /// Load level category for UI display
    var loadLevel: LoadLevel {
        switch load {
        case 0..<30: return .low
        case 30..<70: return .medium
        case 70...100: return .high
        default: return .medium
        }
    }
    
    /// Whether the server is currently available for connection
    var isAvailable: Bool {
        status == .online && load < 95
    }
    
    /// Formatted ping string
    var formattedPing: String {
        guard let ping = ping else { return "N/A" }
        return "\(ping) ms"
    }
    
    /// Formatted load string
    var formattedLoad: String {
        "\(load)%"
    }
}

// MARK: - VPN Protocol Type

/// Supported VPN protocols
enum VPNProtocolType: String, Codable, CaseIterable {
    case wireGuard = "WireGuard"
    case openVPN = "OpenVPN"
    case ikev2 = "IKEv2"
    
    /// Display name for the protocol
    var displayName: String {
        switch self {
        case .wireGuard: return "WireGuard"
        case .openVPN: return "OpenVPN"
        case .ikev2: return "IKEv2"
        }
    }
    
    /// Brief description of the protocol
    var description: String {
        switch self {
        case .wireGuard:
            return "Modern, fast, and secure"
        case .openVPN:
            return "Reliable and widely supported"
        case .ikev2:
            return "Great for mobile devices"
        }
    }
    
    /// Icon name for the protocol
    var iconName: String {
        switch self {
        case .wireGuard: return "bolt.shield"
        case .openVPN: return "lock.shield"
        case .ikev2: return "antenna.radiowaves.left.and.right"
        }
    }
}

// MARK: - Server Status

/// Possible server statuses
enum ServerStatus: String, Codable, CaseIterable {
    case online = "Online"
    case offline = "Offline"
    case maintenance = "Maintenance"
    case overloaded = "Overloaded"
    
    /// Display name for the status
    var displayName: String {
        rawValue
    }
    
    /// Color associated with the status
    var colorName: String {
        switch self {
        case .online: return "green"
        case .offline: return "red"
        case .maintenance: return "orange"
        case .overloaded: return "yellow"
        }
    }
}

// MARK: - Load Level

/// Server load level categories
enum LoadLevel: String, CaseIterable {
    case low
    case medium
    case high
    
    /// Display name for the load level
    var displayName: String {
        rawValue.capitalized
    }
    
    /// Color associated with the load level
    var colorName: String {
        switch self {
        case .low: return "green"
        case .medium: return "yellow"
        case .high: return "red"
        }
    }
}

// MARK: - Server Extensions

extension VPNServer {
    
    /// Creates a placeholder server for previews
    static var placeholder: VPNServer {
        VPNServer(
            id: UUID(),
            name: "US-East-1",
            country: "United States",
            city: "New York",
            countryCode: "US",
            latitude: 40.7128,
            longitude: -74.0060,
            protocol: .wireGuard,
            status: .online,
            load: 45,
            isPremium: false,
            ping: 32
        )
    }
    
    /// Sample servers for previews
    static var sampleServers: [VPNServer] {
        [
            VPNServer(
                id: UUID(),
                name: "US-West-1",
                country: "United States",
                city: "Los Angeles",
                countryCode: "US",
                latitude: 34.0522,
                longitude: -118.2437,
                protocol: .wireGuard,
                status: .online,
                load: 25,
                isPremium: false,
                ping: 15
            ),
            VPNServer(
                id: UUID(),
                name: "UK-London-1",
                country: "United Kingdom",
                city: "London",
                countryCode: "GB",
                latitude: 51.5074,
                longitude: -0.1278,
                protocol: .wireGuard,
                status: .online,
                load: 60,
                isPremium: false,
                ping: 85
            ),
            VPNServer(
                id: UUID(),
                name: "DE-Frankfurt-1",
                country: "Germany",
                city: "Frankfurt",
                countryCode: "DE",
                latitude: 50.1109,
                longitude: 8.6821,
                protocol: .wireGuard,
                status: .online,
                load: 80,
                isPremium: true,
                ping: 95
            ),
            VPNServer(
                id: UUID(),
                name: "JP-Tokyo-1",
                country: "Japan",
                city: "Tokyo",
                countryCode: "JP",
                latitude: 35.6762,
                longitude: 139.6503,
                protocol: .wireGuard,
                status: .maintenance,
                load: 0,
                isPremium: true,
                ping: nil
            )
        ]
    }
}

// MARK: - Server List Response

/// Response model for server list API
struct ServerListResponse: Codable {
    let servers: [VPNServer]
    let totalCount: Int?
}

// MARK: - Server Details Response

/// Extended server details from the API (admin view)
struct VPNServerDetails: Codable {
    let id: UUID
    let name: String
    let hostname: String
    let ipAddress: String
    let port: Int
    let country: String
    let city: String?
    let countryCode: String
    let latitude: Double?
    let longitude: Double?
    let `protocol`: VPNProtocolType
    let status: ServerStatus
    let load: Int
    let maxConnections: Int
    let currentConnections: Int
    let serverAccessLevel: Int
    let isActive: Bool
    let isPremium: Bool
    let bandwidthBps: Int64?
    let publicKey: String?
    let ping: Int?
    let createdAt: Date
    let updatedAt: Date?
}

// MARK: - Recommendation Request

/// Request model for server recommendation
struct RecommendationRequest: Codable {
    let latitude: Double?
    let longitude: Double?
    let preferredCountry: String?
    let preferredProtocol: VPNProtocolType?
}

// MARK: - Country Group

/// Groups servers by country for display
struct ServerCountryGroup: Identifiable {
    let id: String
    let country: String
    let countryCode: String
    let servers: [VPNServer]
    
    var flagUrl: URL? {
        let code = countryCode.lowercased()
        return URL(string: "https://upload.wikimedia.org/wikipedia/commons/0/0f/Flag_of_Georgia.svg")
    }
    
    var serverCount: Int {
        servers.count
    }
    
    var availableServerCount: Int {
        servers.filter { $0.isAvailable }.count
    }
    
    var bestPing: Int? {
        servers.compactMap { $0.ping }.min()
    }
}

// MARK: - Sorting

/// Server sorting options
enum ServerSortOption: String, CaseIterable {
    case recommended
    case name
    case country
    case load
    case ping
    
    var displayName: String {
        switch self {
        case .recommended: return "Recommended"
        case .name: return "Name"
        case .country: return "Country"
        case .load: return "Load"
        case .ping: return "Ping"
        }
    }
}
