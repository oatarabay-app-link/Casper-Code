//
//  Theme.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//  Copyright © 2024 CasperVPN. All rights reserved.
//

import SwiftUI

/// Centralized theme configuration for CasperVPN.
/// Provides consistent colors, fonts, and styling across the app.
enum Theme {
    
    // MARK: - Colors
    
    enum Colors {
        // Primary brand colors
        static let primary = Color("PrimaryColor", bundle: nil).opacity(1) // Fallback to blue
        static let secondary = Color("SecondaryColor", bundle: nil).opacity(1)
        
        // Semantic colors
        static let success = Color.green
        static let warning = Color.orange
        static let error = Color.red
        static let info = Color.blue
        
        // Background colors
        static let background = Color(uiColor: .systemBackground)
        static let secondaryBackground = Color(uiColor: .secondarySystemBackground)
        static let cardBackground = Color(uiColor: .secondarySystemBackground)
        
        // Text colors
        static let textPrimary = Color(uiColor: .label)
        static let textSecondary = Color(uiColor: .secondaryLabel)
        static let textTertiary = Color(uiColor: .tertiaryLabel)
        
        // Border colors
        static let border = Color(uiColor: .separator)
        static let borderLight = Color(uiColor: .separator).opacity(0.5)
        
        // Custom VPN-specific colors
        static let connected = Color.green
        static let disconnected = Color.gray
        static let connecting = Color.blue
        
        // Gradient colors
        static let gradientStart = Color.blue
        static let gradientEnd = Color.purple
    }
    
    // MARK: - Fonts
    
    enum Fonts {
        // Size-based fonts
        static let largeTitle = Font.system(size: 34, weight: .bold, design: .rounded)
        static let title = Font.system(size: 28, weight: .bold, design: .rounded)
        static let title2 = Font.system(size: 22, weight: .bold, design: .rounded)
        static let title3 = Font.system(size: 20, weight: .semibold, design: .rounded)
        static let headline = Font.system(size: 17, weight: .semibold, design: .rounded)
        static let body = Font.system(size: 17, weight: .regular, design: .default)
        static let callout = Font.system(size: 16, weight: .regular, design: .default)
        static let subheadline = Font.system(size: 15, weight: .regular, design: .default)
        static let footnote = Font.system(size: 13, weight: .regular, design: .default)
        static let caption = Font.system(size: 12, weight: .regular, design: .default)
        static let caption2 = Font.system(size: 11, weight: .regular, design: .default)
        
        // Custom fonts for specific use cases
        static let monospacedBody = Font.system(size: 17, weight: .regular, design: .monospaced)
        static let monospacedCaption = Font.system(size: 12, weight: .regular, design: .monospaced)
    }
    
    // MARK: - Spacing
    
    enum Spacing {
        static let xxs: CGFloat = 2
        static let xs: CGFloat = 4
        static let sm: CGFloat = 8
        static let md: CGFloat = 12
        static let lg: CGFloat = 16
        static let xl: CGFloat = 24
        static let xxl: CGFloat = 32
        static let xxxl: CGFloat = 48
    }
    
    // MARK: - Corner Radius
    
    enum CornerRadius {
        static let small: CGFloat = 4
        static let medium: CGFloat = 8
        static let large: CGFloat = 12
        static let xlarge: CGFloat = 16
        static let xxlarge: CGFloat = 24
        static let round: CGFloat = 9999
    }
    
    // MARK: - Shadows
    
    enum Shadows {
        static let small = Shadow(color: .black.opacity(0.1), radius: 4, x: 0, y: 2)
        static let medium = Shadow(color: .black.opacity(0.15), radius: 8, x: 0, y: 4)
        static let large = Shadow(color: .black.opacity(0.2), radius: 16, x: 0, y: 8)
    }
    
    // MARK: - Animation
    
    enum Animation {
        static let fast = SwiftUI.Animation.easeInOut(duration: 0.15)
        static let normal = SwiftUI.Animation.easeInOut(duration: 0.25)
        static let slow = SwiftUI.Animation.easeInOut(duration: 0.35)
        static let spring = SwiftUI.Animation.spring(response: 0.3, dampingFraction: 0.7)
    }
}

// MARK: - Shadow Struct

struct Shadow {
    let color: Color
    let radius: CGFloat
    let x: CGFloat
    let y: CGFloat
}

// MARK: - View Extensions

extension View {
    
    /// Applies a theme shadow to the view.
    func themeShadow(_ shadow: Shadow) -> some View {
        self.shadow(color: shadow.color, radius: shadow.radius, x: shadow.x, y: shadow.y)
    }
    
    /// Applies the card style to a view.
    func cardStyle() -> some View {
        self
            .padding(Theme.Spacing.lg)
            .background(Theme.Colors.cardBackground)
            .cornerRadius(Theme.CornerRadius.large)
            .themeShadow(Theme.Shadows.small)
    }
    
    /// Applies the primary button style.
    func primaryButtonStyle() -> some View {
        self
            .font(Theme.Fonts.headline)
            .foregroundColor(.white)
            .frame(maxWidth: .infinity)
            .frame(height: 50)
            .background(Theme.Colors.primary)
            .cornerRadius(Theme.CornerRadius.large)
    }
    
    /// Applies the secondary button style.
    func secondaryButtonStyle() -> some View {
        self
            .font(Theme.Fonts.headline)
            .foregroundColor(Theme.Colors.primary)
            .frame(maxWidth: .infinity)
            .frame(height: 50)
            .background(Theme.Colors.cardBackground)
            .cornerRadius(Theme.CornerRadius.large)
            .overlay(
                RoundedRectangle(cornerRadius: Theme.CornerRadius.large)
                    .stroke(Theme.Colors.primary, lineWidth: 2)
            )
    }
}

// MARK: - Color Extension

extension Color {
    
    /// Creates a color with fallback for missing asset colors.
    init(_ name: String, fallback: Color) {
        if UIColor(named: name) != nil {
            self.init(name)
        } else {
            self = fallback
        }
    }
    
    /// Hex initializer for colors.
    init(hex: String) {
        let hex = hex.trimmingCharacters(in: CharacterSet.alphanumerics.inverted)
        var int: UInt64 = 0
        Scanner(string: hex).scanHexInt64(&int)
        
        let a, r, g, b: UInt64
        switch hex.count {
        case 3: // RGB (12-bit)
            (a, r, g, b) = (255, (int >> 8) * 17, (int >> 4 & 0xF) * 17, (int & 0xF) * 17)
        case 6: // RGB (24-bit)
            (a, r, g, b) = (255, int >> 16, int >> 8 & 0xFF, int & 0xFF)
        case 8: // ARGB (32-bit)
            (a, r, g, b) = (int >> 24, int >> 16 & 0xFF, int >> 8 & 0xFF, int & 0xFF)
        default:
            (a, r, g, b) = (255, 0, 0, 0)
        }
        
        self.init(
            .sRGB,
            red: Double(r) / 255,
            green: Double(g) / 255,
            blue: Double(b) / 255,
            opacity: Double(a) / 255
        )
    }
}

// MARK: - Gradient Definitions

extension LinearGradient {
    
    /// Primary brand gradient.
    static let primary = LinearGradient(
        colors: [Theme.Colors.gradientStart, Theme.Colors.gradientEnd],
        startPoint: .topLeading,
        endPoint: .bottomTrailing
    )
    
    /// Success gradient.
    static let success = LinearGradient(
        colors: [.green, .green.opacity(0.7)],
        startPoint: .top,
        endPoint: .bottom
    )
    
    /// Danger gradient.
    static let danger = LinearGradient(
        colors: [.red, .red.opacity(0.7)],
        startPoint: .top,
        endPoint: .bottom
    )
}

// MARK: - Theme Provider (for custom theme support)

/// Environment key for theme customization (future use).
struct ThemeKey: EnvironmentKey {
    static let defaultValue: ThemeType = .system
}

enum ThemeType {
    case system
    case light
    case dark
}

extension EnvironmentValues {
    var theme: ThemeType {
        get { self[ThemeKey.self] }
        set { self[ThemeKey.self] = newValue }
    }
}

// MARK: - Preview

#if DEBUG
#Preview("Theme Colors") {
    VStack(spacing: 16) {
        HStack {
            Circle().fill(Theme.Colors.primary).frame(width: 40, height: 40)
            Text("Primary")
        }
        HStack {
            Circle().fill(Theme.Colors.success).frame(width: 40, height: 40)
            Text("Success")
        }
        HStack {
            Circle().fill(Theme.Colors.warning).frame(width: 40, height: 40)
            Text("Warning")
        }
        HStack {
            Circle().fill(Theme.Colors.error).frame(width: 40, height: 40)
            Text("Error")
        }
    }
    .padding()
}
#endif
