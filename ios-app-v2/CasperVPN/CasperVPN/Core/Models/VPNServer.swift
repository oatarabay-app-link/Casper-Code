//
//  VPNServer.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//

import Foundation

struct VPNServer: Codable, Identifiable, Equatable, Hashable {
    let id: String
    let name: String
    let country: String
    let city: String
    let countryCode: String
    let hostname: String
    let ipAddress: String
    let port: Int
    let load: Int // Server load percentage (0-100)
    let isPremium: Bool
    let isOnline: Bool
    let features: [ServerFeature]?
    let latency: Int? // in milliseconds
    
    var displayName: String {
        "\(city), \(country)"
    }
    
    var flagEmoji: String {
        let base: UInt32 = 127397
        var emoji = ""
        for scalar in countryCode.uppercased().unicodeScalars {
            if let unicode = UnicodeScalar(base + scalar.value) {
                emoji.append(Character(unicode))
            }
        }
        return emoji
    }
    
    var loadStatus: LoadStatus {
        switch load {
        case 0..<30: return .low
        case 30..<70: return .medium
        default: return .high
        }
    }
    
    func hash(into hasher: inout Hasher) {
        hasher.combine(id)
    }
}

// MARK: - Load Status
enum LoadStatus {
    case low, medium, high
    
    var color: String {
        switch self {
        case .low: return "green"
        case .medium: return "yellow"
        case .high: return "red"
        }
    }
    
    var description: String {
        switch self {
        case .low: return "Low"
        case .medium: return "Medium"
        case .high: return "High"
        }
    }
}

// MARK: - Server Feature
enum ServerFeature: String, Codable {
    case p2p = "P2P"
    case streaming = "Streaming"
    case gaming = "Gaming"
    case doublVPN = "Double VPN"
    case obfuscated = "Obfuscated"
    case dedicatedIP = "Dedicated IP"
}

// MARK: - API Response
struct ServersResponse: Codable {
    let success: Bool
    let data: [VPNServer]?
    let message: String?
}

struct ServerResponse: Codable {
    let success: Bool
    let data: VPNServer?
    let message: String?
}
