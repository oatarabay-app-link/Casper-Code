//
//  Subscription.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//  Copyright © 2024 CasperVPN. All rights reserved.
//

import Foundation

/// Represents a user's subscription to a CasperVPN plan.
struct Subscription: Codable, Identifiable, Equatable {
    
    // MARK: - Properties
    
    /// Unique identifier for the subscription
    let id: UUID
    
    /// ID of the associated plan
    let planId: UUID
    
    /// Name of the plan
    let planName: String
    
    /// Type of the plan
    let planType: PlanType
    
    /// Current status of the subscription
    var status: SubscriptionStatus
    
    /// Date when the subscription started
    let startDate: Date
    
    /// Date when the subscription ends/expires
    var endDate: Date?
    
    /// Start of the current billing period
    let currentPeriodStart: Date?
    
    /// End of the current billing period
    let currentPeriodEnd: Date?
    
    /// Whether the subscription will be cancelled at period end
    var cancelAtPeriodEnd: Bool
    
    /// Billing interval (monthly/yearly)
    let billingInterval: BillingInterval?
    
    /// Current price being charged
    let currentPrice: Decimal?
    
    /// Date when the subscription was created
    let createdAt: Date?
    
    // MARK: - Computed Properties
    
    /// Whether the subscription is currently active
    var isActive: Bool {
        status == .active || status == .trialing
    }
    
    /// Days remaining until subscription ends
    var daysRemaining: Int? {
        guard let endDate = endDate ?? currentPeriodEnd else { return nil }
        let days = Calendar.current.dateComponents([.day], from: Date(), to: endDate).day
        return max(0, days ?? 0)
    }
    
    /// Formatted end date
    var formattedEndDate: String? {
        guard let endDate = endDate ?? currentPeriodEnd else { return nil }
        let formatter = DateFormatter()
        formatter.dateStyle = .medium
        formatter.timeStyle = .none
        return formatter.string(from: endDate)
    }
    
    /// Formatted price
    var formattedPrice: String {
        guard let price = currentPrice else { return "Free" }
        let formatter = NumberFormatter()
        formatter.numberStyle = .currency
        formatter.currencyCode = "USD"
        return formatter.string(from: price as NSDecimalNumber) ?? "$\(price)"
    }
    
    /// Price per month (for yearly subscriptions)
    var monthlyEquivalent: Decimal? {
        guard let price = currentPrice, billingInterval == .yearly else {
            return currentPrice
        }
        return price / 12
    }
}

// MARK: - Plan Type

/// Types of subscription plans
enum PlanType: String, Codable, CaseIterable {
    case free = "Free"
    case basic = "Basic"
    case premium = "Premium"
    case enterprise = "Enterprise"
    
    /// Display name for the plan type
    var displayName: String {
        rawValue
    }
    
    /// Icon name for the plan type
    var iconName: String {
        switch self {
        case .free: return "gift"
        case .basic: return "star"
        case .premium: return "star.fill"
        case .enterprise: return "building.2"
        }
    }
    
    /// Color name for the plan type
    var colorName: String {
        switch self {
        case .free: return "gray"
        case .basic: return "blue"
        case .premium: return "purple"
        case .enterprise: return "orange"
        }
    }
}

// MARK: - Billing Interval

/// Billing interval options
enum BillingInterval: String, Codable, CaseIterable {
    case monthly = "Monthly"
    case yearly = "Yearly"
    
    /// Display name for the interval
    var displayName: String {
        rawValue
    }
    
    /// Short display name
    var shortName: String {
        switch self {
        case .monthly: return "mo"
        case .yearly: return "yr"
        }
    }
}

// MARK: - Plan

/// Represents a subscription plan offered by CasperVPN
struct Plan: Codable, Identifiable, Equatable {
    
    // MARK: - Properties
    
    /// Unique identifier for the plan
    let id: UUID
    
    /// Name of the plan
    let name: String
    
    /// Description of the plan
    let description: String?
    
    /// Monthly price
    let priceMonthly: Decimal
    
    /// Yearly price
    let priceYearly: Decimal
    
    /// Maximum number of devices allowed
    let maxDevices: Int
    
    /// Data limit in bytes (0 = unlimited)
    let dataLimitBytes: Int64
    
    /// Server access level (higher = more servers)
    let serverAccessLevel: Int
    
    /// Type of the plan
    let type: PlanType
    
    /// List of features included
    let features: [String]
    
    /// Whether this plan should be highlighted
    let isPopular: Bool?
    
    /// Percentage savings when choosing yearly
    let savingsPercentage: Int?
    
    // MARK: - Computed Properties
    
    /// Whether this is a free plan
    var isFree: Bool {
        priceMonthly == 0 && priceYearly == 0
    }
    
    /// Whether data is unlimited
    var hasUnlimitedData: Bool {
        dataLimitBytes == 0
    }
    
    /// Formatted monthly price
    var formattedMonthlyPrice: String {
        if isFree { return "Free" }
        let formatter = NumberFormatter()
        formatter.numberStyle = .currency
        formatter.currencyCode = "USD"
        return formatter.string(from: priceMonthly as NSDecimalNumber) ?? "$\(priceMonthly)"
    }
    
    /// Formatted yearly price
    var formattedYearlyPrice: String {
        if isFree { return "Free" }
        let formatter = NumberFormatter()
        formatter.numberStyle = .currency
        formatter.currencyCode = "USD"
        return formatter.string(from: priceYearly as NSDecimalNumber) ?? "$\(priceYearly)"
    }
    
    /// Monthly equivalent of yearly price
    var yearlyMonthlyEquivalent: Decimal {
        priceYearly / 12
    }
    
    /// Formatted yearly monthly equivalent
    var formattedYearlyMonthlyEquivalent: String {
        let formatter = NumberFormatter()
        formatter.numberStyle = .currency
        formatter.currencyCode = "USD"
        return formatter.string(from: yearlyMonthlyEquivalent as NSDecimalNumber) ?? "$\(yearlyMonthlyEquivalent)"
    }
    
    /// Formatted data limit
    var formattedDataLimit: String {
        if hasUnlimitedData { return "Unlimited" }
        return ByteCountFormatter.string(fromByteCount: dataLimitBytes, countStyle: .binary)
    }
}

// MARK: - Subscription Extensions

extension Subscription {
    
    /// Creates a placeholder subscription for previews
    static var placeholder: Subscription {
        Subscription(
            id: UUID(),
            planId: UUID(),
            planName: "Premium",
            planType: .premium,
            status: .active,
            startDate: Date().addingTimeInterval(-30 * 24 * 3600),
            endDate: Date().addingTimeInterval(335 * 24 * 3600),
            currentPeriodStart: Date().addingTimeInterval(-30 * 24 * 3600),
            currentPeriodEnd: Date().addingTimeInterval(335 * 24 * 3600),
            cancelAtPeriodEnd: false,
            billingInterval: .yearly,
            currentPrice: 59.99,
            createdAt: Date().addingTimeInterval(-30 * 24 * 3600)
        )
    }
    
    /// Creates a free subscription placeholder
    static var freePlaceholder: Subscription {
        Subscription(
            id: UUID(),
            planId: UUID(),
            planName: "Free",
            planType: .free,
            status: .active,
            startDate: Date().addingTimeInterval(-90 * 24 * 3600),
            endDate: nil,
            currentPeriodStart: nil,
            currentPeriodEnd: nil,
            cancelAtPeriodEnd: false,
            billingInterval: nil,
            currentPrice: 0,
            createdAt: Date().addingTimeInterval(-90 * 24 * 3600)
        )
    }
}

// MARK: - Plan Extensions

extension Plan {
    
    /// Sample plans for previews
    static var samplePlans: [Plan] {
        [
            Plan(
                id: UUID(),
                name: "Free",
                description: "Basic VPN protection",
                priceMonthly: 0,
                priceYearly: 0,
                maxDevices: 1,
                dataLimitBytes: 1_000_000_000, // 1 GB
                serverAccessLevel: 1,
                type: .free,
                features: [
                    "1 GB monthly data",
                    "1 device",
                    "Basic server access",
                    "Standard support"
                ],
                isPopular: false,
                savingsPercentage: nil
            ),
            Plan(
                id: UUID(),
                name: "Premium",
                description: "Full VPN protection with all features",
                priceMonthly: 9.99,
                priceYearly: 59.99,
                maxDevices: 10,
                dataLimitBytes: 0, // Unlimited
                serverAccessLevel: 3,
                type: .premium,
                features: [
                    "Unlimited data",
                    "10 devices",
                    "All server locations",
                    "Priority support",
                    "Ad blocking",
                    "Kill switch"
                ],
                isPopular: true,
                savingsPercentage: 50
            )
        ]
    }
}

// MARK: - Create Subscription Request

/// Request model for creating a new subscription
struct CreateSubscriptionRequest: Codable {
    let planId: UUID
    let billingInterval: BillingInterval
    let couponCode: String?
}

// MARK: - Update Subscription Request

/// Request model for updating a subscription
struct UpdateSubscriptionRequest: Codable {
    var newPlanId: UUID?
    var billingInterval: BillingInterval?
    var cancelAtPeriodEnd: Bool?
}

// MARK: - Checkout Session

/// Response model for checkout session creation
struct CheckoutSessionResponse: Codable {
    let sessionId: String
    let sessionUrl: String
    let publishableKey: String
}

// MARK: - Billing Portal Response

/// Response model for billing portal session
struct BillingPortalResponse: Codable {
    let url: String
}
