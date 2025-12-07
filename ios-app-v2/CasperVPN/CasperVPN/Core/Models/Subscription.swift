//
//  Subscription.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//

import Foundation

struct Subscription: Codable, Equatable {
    let id: String
    let plan: SubscriptionPlan
    let status: SubscriptionStatus
    let startDate: Date
    let endDate: Date?
    let autoRenew: Bool
    let paymentMethod: String?
    
    var isActive: Bool {
        status == .active && (endDate == nil || endDate! > Date())
    }
    
    var daysRemaining: Int? {
        guard let endDate = endDate else { return nil }
        return Calendar.current.dateComponents([.day], from: Date(), to: endDate).day
    }
    
    var displayStatus: String {
        if isActive {
            if let days = daysRemaining, days <= 7 {
                return "Expires in \(days) day\(days == 1 ? "" : "s")"
            }
            return "Active"
        }
        return status.rawValue.capitalized
    }
}

// MARK: - Subscription Plan
enum SubscriptionPlan: String, Codable, CaseIterable {
    case free = "free"
    case monthly = "monthly"
    case yearly = "yearly"
    case lifetime = "lifetime"
    
    var displayName: String {
        switch self {
        case .free: return "Free"
        case .monthly: return "Monthly"
        case .yearly: return "Yearly"
        case .lifetime: return "Lifetime"
        }
    }
    
    var maxDevices: Int {
        switch self {
        case .free: return 1
        case .monthly: return 5
        case .yearly: return 10
        case .lifetime: return 10
        }
    }
    
    var hasAllServers: Bool {
        self != .free
    }
}

// MARK: - Subscription Status
enum SubscriptionStatus: String, Codable {
    case active = "active"
    case expired = "expired"
    case cancelled = "cancelled"
    case pending = "pending"
    case trial = "trial"
}

// MARK: - API Response
struct SubscriptionResponse: Codable {
    let success: Bool
    let data: Subscription?
    let message: String?
}
