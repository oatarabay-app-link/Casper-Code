//
//  User.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//

import Foundation

struct User: Codable, Identifiable, Equatable {
    let id: String
    let email: String
    let firstName: String?
    let lastName: String?
    let subscription: Subscription?
    let createdAt: Date?
    let updatedAt: Date?
    
    var fullName: String {
        [firstName, lastName]
            .compactMap { $0 }
            .joined(separator: " ")
    }
    
    var displayName: String {
        fullName.isEmpty ? email : fullName
    }
    
    var isPremium: Bool {
        guard let subscription = subscription else { return false }
        return subscription.isActive && subscription.plan != .free
    }
}

// MARK: - API Response
struct UserResponse: Codable {
    let success: Bool
    let data: User?
    let message: String?
}

struct LoginResponse: Codable {
    let success: Bool
    let data: LoginData?
    let message: String?
    
    struct LoginData: Codable {
        let user: User
        let accessToken: String
        let refreshToken: String
        let expiresIn: Int?
    }
}

struct TokenRefreshResponse: Codable {
    let success: Bool
    let data: TokenData?
    let message: String?
    
    struct TokenData: Codable {
        let accessToken: String
        let refreshToken: String?
        let expiresIn: Int?
    }
}
