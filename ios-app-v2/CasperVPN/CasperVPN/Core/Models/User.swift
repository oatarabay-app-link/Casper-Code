//
//  User.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//  Copyright © 2024 CasperVPN. All rights reserved.
//

import Foundation

/// Represents a user in the CasperVPN system.
/// Conforms to Codable for JSON serialization/deserialization with the backend API.
struct User: Codable, Identifiable, Equatable, Hashable {
    
    // MARK: - Properties
    
    /// Unique identifier for the user
    let id: UUID
    
    /// User's email address (unique)
    let email: String
    
    /// User's first name
    var firstName: String?
    
    /// User's last name
    var lastName: String?
    
    /// Whether the user's email has been verified
    var isEmailVerified: Bool
    
    /// Whether the user account is active
    var isActive: Bool
    
    /// User's role in the system
    var role: UserRole
    
    /// Date when the user account was created
    let createdAt: Date
    
    /// Date when the user account was last updated
    var updatedAt: Date?
    
    /// Date of the user's last login
    var lastLoginAt: Date?
    
    /// User's subscription status
    var subscriptionStatus: SubscriptionStatus?
    
    /// Name of the user's current plan
    var planName: String?
    
    /// Amount of data used in bytes
    var dataUsedBytes: Int64?
    
    /// Data limit in bytes (0 = unlimited)
    var dataLimitBytes: Int64?
    
    // MARK: - Coding Keys
    
    enum CodingKeys: String, CodingKey {
        case id
        case email
        case firstName
        case lastName
        case isEmailVerified
        case isActive
        case role
        case createdAt
        case updatedAt
        case lastLoginAt
        case subscriptionStatus
        case planName
        case dataUsedBytes
        case dataLimitBytes
    }
    
    // MARK: - Computed Properties
    
    /// Full name of the user
    var fullName: String {
        let components = [firstName, lastName].compactMap { $0 }
        return components.isEmpty ? email : components.joined(separator: " ")
    }
    
    /// Display name (first name or email username)
    var displayName: String {
        if let firstName = firstName, !firstName.isEmpty {
            return firstName
        }
        return email.components(separatedBy: "@").first ?? email
    }
    
    /// Percentage of data used relative to limit
    var dataUsagePercentage: Double {
        guard let used = dataUsedBytes, let limit = dataLimitBytes, limit > 0 else {
            return 0
        }
        return min(Double(used) / Double(limit) * 100, 100)
    }
    
    /// Whether the user has unlimited data
    var hasUnlimitedData: Bool {
        dataLimitBytes == nil || dataLimitBytes == 0
    }
    
    /// Formatted data usage string
    var formattedDataUsage: String {
        guard let used = dataUsedBytes else { return "0 B" }
        return ByteCountFormatter.string(fromByteCount: used, countStyle: .binary)
    }
    
    /// Formatted data limit string
    var formattedDataLimit: String {
        guard let limit = dataLimitBytes, limit > 0 else { return "Unlimited" }
        return ByteCountFormatter.string(fromByteCount: limit, countStyle: .binary)
    }
    
    /// Whether the user has a premium subscription
    var isPremium: Bool {
        role == .premium || role == .admin || role == .superAdmin
    }
}

// MARK: - User Role

/// Defines the possible roles a user can have in the system.
enum UserRole: String, Codable, CaseIterable {
    case user = "User"
    case premium = "Premium"
    case admin = "Admin"
    case superAdmin = "SuperAdmin"
    
    /// Display name for the role
    var displayName: String {
        switch self {
        case .user: return "Free User"
        case .premium: return "Premium"
        case .admin: return "Administrator"
        case .superAdmin: return "Super Admin"
        }
    }
    
    /// Icon name for the role
    var iconName: String {
        switch self {
        case .user: return "person"
        case .premium: return "star.fill"
        case .admin: return "person.badge.key"
        case .superAdmin: return "crown.fill"
        }
    }
}

// MARK: - Subscription Status

/// Defines the possible subscription statuses.
enum SubscriptionStatus: String, Codable, CaseIterable {
    case active = "Active"
    case inactive = "Inactive"
    case cancelled = "Cancelled"
    case expired = "Expired"
    case pastDue = "PastDue"
    case trialing = "Trialing"
    
    /// Display name for the status
    var displayName: String {
        switch self {
        case .active: return "Active"
        case .inactive: return "Inactive"
        case .cancelled: return "Cancelled"
        case .expired: return "Expired"
        case .pastDue: return "Past Due"
        case .trialing: return "Trial"
        }
    }
    
    /// Whether this status allows VPN access
    var allowsAccess: Bool {
        switch self {
        case .active, .trialing:
            return true
        case .inactive, .cancelled, .expired, .pastDue:
            return false
        }
    }
}

// MARK: - User Extensions

extension User {
    
    /// Creates a placeholder user for previews
    static var placeholder: User {
        User(
            id: UUID(),
            email: "user@example.com",
            firstName: "John",
            lastName: "Doe",
            isEmailVerified: true,
            isActive: true,
            role: .user,
            createdAt: Date(),
            updatedAt: nil,
            lastLoginAt: Date(),
            subscriptionStatus: .active,
            planName: "Free",
            dataUsedBytes: 500_000_000,
            dataLimitBytes: 1_000_000_000
        )
    }
    
    /// Creates a premium user for previews
    static var premiumPlaceholder: User {
        User(
            id: UUID(),
            email: "premium@example.com",
            firstName: "Jane",
            lastName: "Smith",
            isEmailVerified: true,
            isActive: true,
            role: .premium,
            createdAt: Date(),
            updatedAt: nil,
            lastLoginAt: Date(),
            subscriptionStatus: .active,
            planName: "Premium",
            dataUsedBytes: nil,
            dataLimitBytes: 0
        )
    }
}

// MARK: - Update Profile Request

/// Request model for updating user profile
struct UpdateProfileRequest: Codable {
    var firstName: String?
    var lastName: String?
    var email: String?
}

// MARK: - User Details Response

/// Extended user details response from the API
struct UserDetailsResponse: Codable {
    let user: User
    let subscription: Subscription?
    let recentConnections: [ConnectionLog]?
    let recentPayments: [PaymentRecord]?
}

// MARK: - Connection Log

/// Represents a VPN connection log entry
struct ConnectionLog: Codable, Identifiable {
    let id: UUID
    let serverId: UUID
    let serverName: String?
    let serverCountry: String?
    let connectedAt: Date
    var disconnectedAt: Date?
    var bytesUploaded: Int64?
    var bytesDownloaded: Int64?
    let clientIp: String?
    let status: ConnectionStatus
    let `protocol`: VPNProtocolType?
    
    enum ConnectionStatus: String, Codable {
        case active = "Active"
        case disconnected = "Disconnected"
        case failed = "Failed"
        case timeout = "Timeout"
    }
    
    /// Duration of the connection
    var duration: TimeInterval? {
        guard let end = disconnectedAt else {
            return Date().timeIntervalSince(connectedAt)
        }
        return end.timeIntervalSince(connectedAt)
    }
    
    /// Formatted duration string
    var formattedDuration: String {
        guard let duration = duration else { return "N/A" }
        let formatter = DateComponentsFormatter()
        formatter.allowedUnits = [.hour, .minute, .second]
        formatter.unitsStyle = .abbreviated
        return formatter.string(from: duration) ?? "N/A"
    }
}

// MARK: - Payment Record

/// Represents a payment record
struct PaymentRecord: Codable, Identifiable {
    let id: UUID
    let amount: Decimal
    let currency: String
    let status: PaymentStatus
    let method: PaymentMethod?
    let description: String?
    let paidAt: Date?
    let receiptUrl: String?
    let createdAt: Date
    
    enum PaymentStatus: String, Codable {
        case pending = "Pending"
        case succeeded = "Succeeded"
        case failed = "Failed"
        case refunded = "Refunded"
    }
    
    enum PaymentMethod: String, Codable {
        case card = "Card"
        case paypal = "Paypal"
        case applePay = "ApplePay"
    }
    
    /// Formatted amount with currency
    var formattedAmount: String {
        let formatter = NumberFormatter()
        formatter.numberStyle = .currency
        formatter.currencyCode = currency
        return formatter.string(from: amount as NSDecimalNumber) ?? "\(currency) \(amount)"
    }
}
