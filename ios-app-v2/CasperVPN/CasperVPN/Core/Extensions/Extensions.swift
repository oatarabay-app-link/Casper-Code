//
//  Extensions.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//  Copyright © 2024 CasperVPN. All rights reserved.
//

import Foundation
import SwiftUI

// MARK: - String Extensions

extension String {
    
    /// Validates if the string is a valid email format.
    var isValidEmail: Bool {
        let emailRegex = #"^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$"#
        return range(of: emailRegex, options: .regularExpression) != nil
    }
    
    /// Validates if the string meets password requirements.
    var isValidPassword: Bool {
        // At least 8 characters, 1 uppercase, 1 lowercase, 1 digit
        let minLength = count >= 8
        let hasUppercase = range(of: "[A-Z]", options: .regularExpression) != nil
        let hasLowercase = range(of: "[a-z]", options: .regularExpression) != nil
        let hasDigit = range(of: "[0-9]", options: .regularExpression) != nil
        return minLength && hasUppercase && hasLowercase && hasDigit
    }
    
    /// Trims whitespace and newlines from both ends.
    var trimmed: String {
        trimmingCharacters(in: .whitespacesAndNewlines)
    }
    
    /// Returns nil if the string is empty, otherwise returns self.
    var nilIfEmpty: String? {
        isEmpty ? nil : self
    }
    
    /// Masks email address for privacy display.
    var maskedEmail: String {
        guard let atIndex = firstIndex(of: "@") else { return self }
        let username = String(self[..<atIndex])
        let domain = String(self[atIndex...])
        
        if username.count <= 2 {
            return "\(username)\(domain)"
        }
        
        let maskedUsername = String(username.prefix(2)) + String(repeating: "*", count: max(0, username.count - 2))
        return maskedUsername + domain
    }
    
    /// Localized string helper.
    var localized: String {
        NSLocalizedString(self, comment: "")
    }
}

// MARK: - Date Extensions

extension Date {
    
    /// Formats date for display.
    func formatted(style: DateFormatter.Style) -> String {
        let formatter = DateFormatter()
        formatter.dateStyle = style
        formatter.timeStyle = .none
        return formatter.string(from: self)
    }
    
    /// Returns time ago string (e.g., "2 hours ago").
    var timeAgo: String {
        let formatter = RelativeDateTimeFormatter()
        formatter.unitsStyle = .full
        return formatter.localizedString(for: self, relativeTo: Date())
    }
    
    /// Returns whether the date is in the past.
    var isPast: Bool {
        self < Date()
    }
    
    /// Returns whether the date is in the future.
    var isFuture: Bool {
        self > Date()
    }
    
    /// Days remaining from now.
    var daysFromNow: Int {
        Calendar.current.dateComponents([.day], from: Date(), to: self).day ?? 0
    }
}

// MARK: - Data Extensions

extension Data {
    
    /// Converts data to hex string.
    var hexString: String {
        map { String(format: "%02hhx", $0) }.joined()
    }
    
    /// Converts data to base64 URL-safe string.
    var base64URLEncoded: String {
        base64EncodedString()
            .replacingOccurrences(of: "+", with: "-")
            .replacingOccurrences(of: "/", with: "_")
            .replacingOccurrences(of: "=", with: "")
    }
}

// MARK: - Int64 Extensions

extension Int64 {
    
    /// Formats bytes to human-readable string.
    var formattedBytes: String {
        ByteCountFormatter.string(fromByteCount: self, countStyle: .binary)
    }
}

// MARK: - TimeInterval Extensions

extension TimeInterval {
    
    /// Formats duration to HH:MM:SS string.
    var formattedDuration: String {
        let hours = Int(self) / 3600
        let minutes = (Int(self) % 3600) / 60
        let seconds = Int(self) % 60
        return String(format: "%02d:%02d:%02d", hours, minutes, seconds)
    }
}

// MARK: - Array Extensions

extension Array {
    
    /// Safe subscript that returns nil if index is out of bounds.
    subscript(safe index: Index) -> Element? {
        indices.contains(index) ? self[index] : nil
    }
}

// MARK: - Optional Extensions

extension Optional where Wrapped == String {
    
    /// Returns true if optional is nil or empty string.
    var isNilOrEmpty: Bool {
        self?.isEmpty ?? true
    }
    
    /// Returns the wrapped value or an empty string.
    var orEmpty: String {
        self ?? ""
    }
}

// MARK: - View Extensions

extension View {
    
    /// Conditionally applies a modifier.
    @ViewBuilder
    func `if`<Content: View>(_ condition: Bool, transform: (Self) -> Content) -> some View {
        if condition {
            transform(self)
        } else {
            self
        }
    }
    
    /// Applies a modifier if the optional value exists.
    @ViewBuilder
    func ifLet<T, Content: View>(_ optional: T?, transform: (Self, T) -> Content) -> some View {
        if let value = optional {
            transform(self, value)
        } else {
            self
        }
    }
    
    /// Hides the view conditionally.
    @ViewBuilder
    func hidden(_ hidden: Bool) -> some View {
        if hidden {
            self.hidden()
        } else {
            self
        }
    }
    
    /// Applies corner radius to specific corners.
    func cornerRadius(_ radius: CGFloat, corners: UIRectCorner) -> some View {
        clipShape(RoundedCorner(radius: radius, corners: corners))
    }
    
    /// Adds a loading overlay.
    func loadingOverlay(isLoading: Bool, message: String? = nil) -> some View {
        ZStack {
            self
            if isLoading {
                LoadingOverlay(message: message)
            }
        }
    }
}

// MARK: - Rounded Corner Shape

struct RoundedCorner: Shape {
    var radius: CGFloat = .infinity
    var corners: UIRectCorner = .allCorners
    
    func path(in rect: CGRect) -> Path {
        let path = UIBezierPath(
            roundedRect: rect,
            byRoundingCorners: corners,
            cornerRadii: CGSize(width: radius, height: radius)
        )
        return Path(path.cgPath)
    }
}

// MARK: - UIApplication Extensions

extension UIApplication {
    
    /// Dismisses the keyboard.
    func dismissKeyboard() {
        sendAction(#selector(UIResponder.resignFirstResponder), to: nil, from: nil, for: nil)
    }
    
    /// Gets the current key window.
    var currentKeyWindow: UIWindow? {
        connectedScenes
            .compactMap { $0 as? UIWindowScene }
            .flatMap { $0.windows }
            .first { $0.isKeyWindow }
    }
}

// MARK: - Bundle Extensions

extension Bundle {
    
    /// App version string.
    var appVersion: String {
        infoDictionary?["CFBundleShortVersionString"] as? String ?? "1.0.0"
    }
    
    /// Build number string.
    var buildNumber: String {
        infoDictionary?["CFBundleVersion"] as? String ?? "1"
    }
    
    /// Full version string (e.g., "1.0.0 (123)").
    var fullVersion: String {
        "\(appVersion) (\(buildNumber))"
    }
}

// MARK: - Result Extensions

extension Result {
    
    /// Returns the success value if present.
    var success: Success? {
        switch self {
        case .success(let value): return value
        case .failure: return nil
        }
    }
    
    /// Returns the failure error if present.
    var failure: Failure? {
        switch self {
        case .success: return nil
        case .failure(let error): return error
        }
    }
}

// MARK: - URL Extensions

extension URL {
    
    /// Appends query items to the URL.
    func appending(queryItems: [URLQueryItem]) -> URL? {
        var components = URLComponents(url: self, resolvingAgainstBaseURL: true)
        components?.queryItems = (components?.queryItems ?? []) + queryItems
        return components?.url
    }
}

// MARK: - Notification Extensions

extension Notification.Name {
    
    // App-specific notifications
    static let userDidLogin = Notification.Name("userDidLogin")
    static let userDidLogout = Notification.Name("userDidLogout")
    static let vpnDidConnect = Notification.Name("vpnDidConnect")
    static let vpnDidDisconnect = Notification.Name("vpnDidDisconnect")
    static let subscriptionDidUpdate = Notification.Name("subscriptionDidUpdate")
}

// MARK: - Task Extension

extension Task where Success == Never, Failure == Never {
    
    /// Sleeps for a given number of seconds.
    static func sleep(seconds: Double) async throws {
        let nanoseconds = UInt64(seconds * 1_000_000_000)
        try await Task.sleep(nanoseconds: nanoseconds)
    }
}
