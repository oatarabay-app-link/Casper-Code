//
//  Components.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//  Copyright © 2024 CasperVPN. All rights reserved.
//

import SwiftUI

// MARK: - Casper Button

/// A styled button component for CasperVPN.
struct CasperButton: View {
    
    enum Style {
        case primary
        case secondary
        case danger
        case outline
    }
    
    let title: String
    let style: Style
    var isLoading: Bool = false
    var icon: String? = nil
    let action: () -> Void
    
    var body: some View {
        Button(action: action) {
            HStack(spacing: 8) {
                if isLoading {
                    ProgressView()
                        .progressViewStyle(CircularProgressViewStyle(tint: textColor))
                        .scaleEffect(0.8)
                } else {
                    if let icon = icon {
                        Image(systemName: icon)
                    }
                    Text(title)
                        .fontWeight(.semibold)
                }
            }
            .frame(maxWidth: .infinity)
            .frame(height: 50)
            .foregroundColor(textColor)
            .background(backgroundColor)
            .cornerRadius(12)
            .overlay(
                RoundedRectangle(cornerRadius: 12)
                    .strokeBorder(borderColor, lineWidth: style == .outline ? 2 : 0)
            )
        }
        .disabled(isLoading)
        .opacity(isLoading ? 0.8 : 1.0)
    }
    
    private var backgroundColor: Color {
        switch style {
        case .primary: return Theme.Colors.primary
        case .secondary: return Theme.Colors.secondary
        case .danger: return Theme.Colors.error
        case .outline: return .clear
        }
    }
    
    private var textColor: Color {
        switch style {
        case .primary, .secondary, .danger: return .white
        case .outline: return Theme.Colors.primary
        }
    }
    
    private var borderColor: Color {
        switch style {
        case .outline: return Theme.Colors.primary
        default: return .clear
        }
    }
}

// MARK: - Casper Text Field

/// A styled text field component for CasperVPN.
struct CasperTextField: View {
    
    let title: String
    @Binding var text: String
    var icon: String? = nil
    var isSecure: Bool = false
    var keyboardType: UIKeyboardType = .default
    var autocapitalization: TextInputAutocapitalization = .sentences
    
    @State private var isSecureTextVisible = false
    
    var body: some View {
        VStack(alignment: .leading, spacing: 8) {
            Text(title)
                .font(Theme.Fonts.caption)
                .foregroundColor(Theme.Colors.textSecondary)
            
            HStack(spacing: 12) {
                if let icon = icon {
                    Image(systemName: icon)
                        .foregroundColor(Theme.Colors.textSecondary)
                        .frame(width: 20)
                }
                
                if isSecure && !isSecureTextVisible {
                    SecureField("", text: $text)
                        .textInputAutocapitalization(autocapitalization)
                } else {
                    TextField("", text: $text)
                        .keyboardType(keyboardType)
                        .textInputAutocapitalization(autocapitalization)
                }
                
                if isSecure {
                    Button {
                        isSecureTextVisible.toggle()
                    } label: {
                        Image(systemName: isSecureTextVisible ? "eye.slash" : "eye")
                            .foregroundColor(Theme.Colors.textSecondary)
                    }
                }
            }
            .padding()
            .background(Theme.Colors.cardBackground)
            .cornerRadius(12)
        }
    }
}

// MARK: - Card View

/// A card container component.
struct CardView<Content: View>: View {
    
    let content: Content
    
    init(@ViewBuilder content: () -> Content) {
        self.content = content()
    }
    
    var body: some View {
        content
            .padding()
            .background(Theme.Colors.cardBackground)
            .cornerRadius(16)
            .shadow(color: .black.opacity(0.1), radius: 8, y: 4)
    }
}

// MARK: - Status Badge

/// A badge showing status with color coding.
struct StatusBadge: View {
    
    enum Status {
        case success
        case warning
        case error
        case info
    }
    
    let text: String
    let status: Status
    
    var body: some View {
        Text(text)
            .font(Theme.Fonts.caption.bold())
            .foregroundColor(.white)
            .padding(.horizontal, 12)
            .padding(.vertical, 6)
            .background(statusColor)
            .cornerRadius(8)
    }
    
    private var statusColor: Color {
        switch status {
        case .success: return Theme.Colors.success
        case .warning: return Theme.Colors.warning
        case .error: return Theme.Colors.error
        case .info: return Theme.Colors.info
        }
    }
}

// MARK: - Loading Overlay

/// A full-screen loading overlay.
struct LoadingOverlay: View {
    
    var message: String? = nil
    
    var body: some View {
        ZStack {
            Color.black.opacity(0.4)
                .ignoresSafeArea()
            
            VStack(spacing: 16) {
                ProgressView()
                    .progressViewStyle(CircularProgressViewStyle(tint: .white))
                    .scaleEffect(1.5)
                
                if let message = message {
                    Text(message)
                        .font(Theme.Fonts.body)
                        .foregroundColor(.white)
                }
            }
            .padding(32)
            .background(.ultraThinMaterial)
            .cornerRadius(16)
        }
    }
}

// MARK: - Empty State View

/// A view for displaying empty states.
struct EmptyStateView: View {
    
    let icon: String
    let title: String
    let message: String
    var action: (() -> Void)? = nil
    var actionTitle: String? = nil
    
    var body: some View {
        VStack(spacing: 16) {
            Image(systemName: icon)
                .font(.system(size: 64))
                .foregroundColor(Theme.Colors.textSecondary)
            
            Text(title)
                .font(Theme.Fonts.headline)
                .foregroundColor(Theme.Colors.textPrimary)
            
            Text(message)
                .font(Theme.Fonts.body)
                .foregroundColor(Theme.Colors.textSecondary)
                .multilineTextAlignment(.center)
            
            if let action = action, let actionTitle = actionTitle {
                CasperButton(title: actionTitle, style: .primary, action: action)
                    .frame(width: 160)
            }
        }
        .padding()
    }
}

// MARK: - Section Header

/// A styled section header.
struct SectionHeader: View {
    
    let title: String
    var action: (() -> Void)? = nil
    var actionTitle: String? = nil
    
    var body: some View {
        HStack {
            Text(title)
                .font(Theme.Fonts.headline)
                .foregroundColor(Theme.Colors.textPrimary)
            
            Spacer()
            
            if let action = action, let actionTitle = actionTitle {
                Button(action: action) {
                    Text(actionTitle)
                        .font(Theme.Fonts.callout)
                        .foregroundColor(Theme.Colors.primary)
                }
            }
        }
        .padding(.horizontal)
    }
}

// MARK: - Toggle Row

/// A row with a toggle switch.
struct ToggleRow: View {
    
    let title: String
    let icon: String
    @Binding var isOn: Bool
    var subtitle: String? = nil
    
    var body: some View {
        HStack(spacing: 12) {
            Image(systemName: icon)
                .font(.title3)
                .foregroundColor(Theme.Colors.primary)
                .frame(width: 30)
            
            VStack(alignment: .leading, spacing: 2) {
                Text(title)
                    .font(Theme.Fonts.body)
                    .foregroundColor(Theme.Colors.textPrimary)
                
                if let subtitle = subtitle {
                    Text(subtitle)
                        .font(Theme.Fonts.caption)
                        .foregroundColor(Theme.Colors.textSecondary)
                }
            }
            
            Spacer()
            
            Toggle("", isOn: $isOn)
                .toggleStyle(SwitchToggleStyle(tint: Theme.Colors.primary))
        }
        .padding()
        .background(Theme.Colors.cardBackground)
        .cornerRadius(12)
    }
}

// MARK: - Info Row

/// A row displaying a key-value pair.
struct InfoRow: View {
    
    let title: String
    let value: String
    var icon: String? = nil
    
    var body: some View {
        HStack {
            if let icon = icon {
                Image(systemName: icon)
                    .foregroundColor(Theme.Colors.primary)
                    .frame(width: 24)
            }
            
            Text(title)
                .font(Theme.Fonts.body)
                .foregroundColor(Theme.Colors.textPrimary)
            
            Spacer()
            
            Text(value)
                .font(Theme.Fonts.body)
                .foregroundColor(Theme.Colors.textSecondary)
        }
    }
}

// MARK: - Country Flag

/// Displays a country flag from URL or fallback.
struct CountryFlag: View {
    
    let countryCode: String
    var size: CGFloat = 32
    
    var flagUrl: URL? {
        URL(string: "https://i.ytimg.com/vi/Db9WLOSt2Kw/sddefault.jpg")
    }
    
    var body: some View {
        AsyncImage(url: flagUrl) { image in
            image
                .resizable()
                .aspectRatio(contentMode: .fit)
        } placeholder: {
            Text(countryCode)
                .font(.system(size: size * 0.4, weight: .bold))
                .foregroundColor(.white)
                .frame(width: size, height: size * 0.67)
                .background(Theme.Colors.textSecondary)
        }
        .frame(width: size, height: size * 0.67)
        .clipShape(RoundedRectangle(cornerRadius: size * 0.1))
    }
}

// MARK: - Animated Connection Ring

/// An animated ring for connection status.
struct ConnectionRing: View {
    
    let isConnected: Bool
    let isConnecting: Bool
    
    @State private var rotation = 0.0
    
    var body: some View {
        ZStack {
            // Background ring
            Circle()
                .stroke(
                    Theme.Colors.textSecondary.opacity(0.2),
                    lineWidth: 8
                )
            
            // Progress/connected ring
            if isConnecting {
                Circle()
                    .trim(from: 0, to: 0.7)
                    .stroke(
                        Theme.Colors.primary,
                        style: StrokeStyle(lineWidth: 8, lineCap: .round)
                    )
                    .rotationEffect(.degrees(rotation))
                    .onAppear {
                        withAnimation(.linear(duration: 1).repeatForever(autoreverses: false)) {
                            rotation = 360
                        }
                    }
            } else if isConnected {
                Circle()
                    .stroke(Theme.Colors.success, lineWidth: 8)
            }
        }
    }
}

// MARK: - Latency Badge

/// A badge displaying server latency with color coding.
///
/// Colors are based on latency thresholds:
/// - Green: < 100ms (good)
/// - Yellow: 100-200ms (fair)
/// - Red: > 200ms (poor)
struct LatencyBadge: View {
    
    /// Latency value in milliseconds
    let latency: Int?
    
    /// Whether to show compact version
    var isCompact: Bool = false
    
    var body: some View {
        if let latency = latency {
            HStack(spacing: 4) {
                Circle()
                    .fill(latencyColor)
                    .frame(width: isCompact ? 6 : 8, height: isCompact ? 6 : 8)
                
                Text("\(latency) ms")
                    .font(isCompact ? Theme.Fonts.caption2 : Theme.Fonts.caption)
                    .foregroundColor(latencyColor)
            }
            .padding(.horizontal, isCompact ? 6 : 8)
            .padding(.vertical, isCompact ? 2 : 4)
            .background(latencyColor.opacity(0.15))
            .cornerRadius(Theme.CornerRadius.small)
        } else {
            HStack(spacing: 4) {
                Circle()
                    .fill(Color.gray.opacity(0.5))
                    .frame(width: isCompact ? 6 : 8, height: isCompact ? 6 : 8)
                
                Text("--")
                    .font(isCompact ? Theme.Fonts.caption2 : Theme.Fonts.caption)
                    .foregroundColor(.gray)
            }
            .padding(.horizontal, isCompact ? 6 : 8)
            .padding(.vertical, isCompact ? 2 : 4)
            .background(Color.gray.opacity(0.1))
            .cornerRadius(Theme.CornerRadius.small)
        }
    }
    
    /// Color based on latency value
    private var latencyColor: Color {
        guard let latency = latency else { return .gray }
        
        switch latency {
        case 0..<100:
            return Theme.Colors.success
        case 100..<200:
            return Theme.Colors.warning
        default:
            return Theme.Colors.error
        }
    }
}

// MARK: - Server Load Indicator

/// A visual indicator for server load percentage.
///
/// Displays as a progress bar with color coding:
/// - Green: 0-30% (low load)
/// - Yellow: 30-70% (medium load)
/// - Red: 70-100% (high load)
struct ServerLoadIndicator: View {
    
    /// Server load percentage (0-100)
    let load: Int
    
    /// Whether to show the percentage text
    var showPercentage: Bool = true
    
    /// Width of the progress bar
    var barWidth: CGFloat = 60
    
    var body: some View {
        VStack(alignment: .trailing, spacing: 4) {
            // Progress bar
            GeometryReader { geometry in
                ZStack(alignment: .leading) {
                    // Background
                    RoundedRectangle(cornerRadius: 3)
                        .fill(Color.gray.opacity(0.2))
                    
                    // Fill
                    RoundedRectangle(cornerRadius: 3)
                        .fill(loadColor)
                        .frame(width: geometry.size.width * CGFloat(min(load, 100)) / 100)
                }
            }
            .frame(width: barWidth, height: 6)
            
            // Percentage text
            if showPercentage {
                Text("\(load)%")
                    .font(Theme.Fonts.caption2)
                    .foregroundColor(loadColor)
            }
        }
    }
    
    /// Color based on load value
    private var loadColor: Color {
        switch load {
        case 0..<30:
            return Theme.Colors.loadLow
        case 30..<70:
            return Theme.Colors.loadMedium
        default:
            return Theme.Colors.loadHigh
        }
    }
}

// MARK: - Feature Badge

/// A small badge displaying a server feature.
struct FeatureBadge: View {
    
    let feature: ServerFeature
    
    var body: some View {
        HStack(spacing: 4) {
            Image(systemName: featureIcon)
                .font(.system(size: 10))
            
            Text(feature.rawValue)
                .font(Theme.Fonts.caption2)
        }
        .foregroundColor(featureColor)
        .padding(.horizontal, 8)
        .padding(.vertical, 4)
        .background(featureColor.opacity(0.15))
        .cornerRadius(Theme.CornerRadius.small)
    }
    
    /// Icon for the feature type
    private var featureIcon: String {
        switch feature {
        case .p2p:
            return "arrow.left.arrow.right"
        case .streaming:
            return "play.tv"
        case .gaming:
            return "gamecontroller"
        case .doublVPN:
            return "lock.shield"
        case .obfuscated:
            return "eye.slash"
        case .dedicatedIP:
            return "star.fill"
        }
    }
    
    /// Color for the feature type
    private var featureColor: Color {
        switch feature {
        case .p2p:
            return Theme.Colors.info
        case .streaming:
            return Theme.Colors.accent
        case .gaming:
            return Theme.Colors.primary
        case .doublVPN:
            return Theme.Colors.success
        case .obfuscated:
            return Theme.Colors.secondary
        case .dedicatedIP:
            return Theme.Colors.warning
        }
    }
}

// MARK: - Feature Badge Row

/// A horizontal row of feature badges.
struct FeatureBadgeRow: View {
    
    let features: [ServerFeature]
    
    /// Maximum number of features to show
    var maxVisible: Int = 3
    
    var body: some View {
        HStack(spacing: 4) {
            ForEach(features.prefix(maxVisible), id: \.self) { feature in
                FeatureBadge(feature: feature)
            }
            
            if features.count > maxVisible {
                Text("+\(features.count - maxVisible)")
                    .font(Theme.Fonts.caption2)
                    .foregroundColor(Theme.Colors.textSecondary)
                    .padding(.horizontal, 6)
                    .padding(.vertical, 4)
                    .background(Color.gray.opacity(0.15))
                    .cornerRadius(Theme.CornerRadius.small)
            }
        }
    }
}

// MARK: - Premium Badge

/// A badge indicating premium server status.
struct PremiumBadge: View {
    
    var isCompact: Bool = false
    
    var body: some View {
        HStack(spacing: 4) {
            Image(systemName: "crown.fill")
                .font(.system(size: isCompact ? 8 : 10))
            
            if !isCompact {
                Text("Premium")
                    .font(Theme.Fonts.caption2)
            }
        }
        .foregroundColor(.yellow)
        .padding(.horizontal, isCompact ? 4 : 8)
        .padding(.vertical, isCompact ? 2 : 4)
        .background(Color.yellow.opacity(0.15))
        .cornerRadius(Theme.CornerRadius.small)
    }
}

// MARK: - Online Status Badge

/// A badge showing server online/offline status.
struct OnlineStatusBadge: View {
    
    let isOnline: Bool
    
    var body: some View {
        HStack(spacing: 4) {
            Circle()
                .fill(isOnline ? Theme.Colors.success : Theme.Colors.error)
                .frame(width: 8, height: 8)
            
            Text(isOnline ? "Online" : "Offline")
                .font(Theme.Fonts.caption2)
                .foregroundColor(isOnline ? Theme.Colors.success : Theme.Colors.error)
        }
    }
}

// MARK: - Server Card (Enhanced)

/// An enhanced card component for displaying server information.
struct ServerCard: View {
    
    let server: VPNServer
    let isFavorite: Bool
    let onTap: () -> Void
    let onFavoriteTap: () -> Void
    
    var body: some View {
        Button(action: onTap) {
            HStack(spacing: 12) {
                // Flag emoji
                Text(server.flagEmoji)
                    .font(.system(size: 32))
                
                // Server info
                VStack(alignment: .leading, spacing: 4) {
                    HStack(spacing: 6) {
                        Text(server.name)
                            .font(Theme.Fonts.headline)
                            .foregroundColor(Theme.Colors.textPrimary)
                        
                        if server.isPremium {
                            PremiumBadge(isCompact: true)
                        }
                    }
                    
                    Text(server.displayName)
                        .font(Theme.Fonts.caption)
                        .foregroundColor(Theme.Colors.textSecondary)
                    
                    // Features
                    if let features = server.features, !features.isEmpty {
                        FeatureBadgeRow(features: features, maxVisible: 2)
                    }
                }
                
                Spacer()
                
                // Right side info
                VStack(alignment: .trailing, spacing: 8) {
                    // Favorite button
                    Button(action: onFavoriteTap) {
                        Image(systemName: isFavorite ? "heart.fill" : "heart")
                            .foregroundColor(isFavorite ? .red : Theme.Colors.textSecondary)
                    }
                    .buttonStyle(.plain)
                    
                    // Latency badge
                    LatencyBadge(latency: server.latency, isCompact: true)
                    
                    // Load indicator
                    ServerLoadIndicator(load: server.load, showPercentage: false, barWidth: 40)
                }
            }
            .padding()
            .background(Theme.cardGradient)
            .cornerRadius(Theme.CornerRadius.medium)
            .overlay(
                RoundedRectangle(cornerRadius: Theme.CornerRadius.medium)
                    .stroke(
                        server.isOnline ? Color.clear : Theme.Colors.error.opacity(0.3),
                        lineWidth: server.isOnline ? 0 : 1
                    )
            )
            .opacity(server.isOnline ? 1.0 : 0.6)
        }
        .buttonStyle(.plain)
    }
}

// MARK: - Compact Server Row

/// A compact row for displaying server information in lists.
struct CompactServerRow: View {
    
    let server: VPNServer
    let isFavorite: Bool
    let onTap: () -> Void
    
    var body: some View {
        Button(action: onTap) {
            HStack(spacing: 12) {
                // Online status indicator
                Circle()
                    .fill(server.isOnline ? Theme.Colors.success : Theme.Colors.error)
                    .frame(width: 8, height: 8)
                
                // Flag
                Text(server.flagEmoji)
                    .font(.system(size: 20))
                
                // Server info
                VStack(alignment: .leading, spacing: 2) {
                    HStack(spacing: 4) {
                        Text(server.name)
                            .font(Theme.Fonts.body)
                            .foregroundColor(Theme.Colors.textPrimary)
                        
                        if server.isPremium {
                            Image(systemName: "crown.fill")
                                .font(.system(size: 10))
                                .foregroundColor(.yellow)
                        }
                        
                        if isFavorite {
                            Image(systemName: "heart.fill")
                                .font(.system(size: 10))
                                .foregroundColor(.red)
                        }
                    }
                    
                    Text(server.city)
                        .font(Theme.Fonts.caption)
                        .foregroundColor(Theme.Colors.textSecondary)
                }
                
                Spacer()
                
                // Latency and load
                VStack(alignment: .trailing, spacing: 4) {
                    LatencyBadge(latency: server.latency, isCompact: true)
                    
                    Text("\(server.load)% load")
                        .font(Theme.Fonts.caption2)
                        .foregroundColor(Theme.Colors.textSecondary)
                }
            }
            .padding(.vertical, 8)
            .contentShape(Rectangle())
        }
        .buttonStyle(.plain)
    }
}

// MARK: - Previews

#if DEBUG
#Preview("Buttons") {
    VStack(spacing: 20) {
        CasperButton(title: "Primary Button", style: .primary) {}
        CasperButton(title: "Secondary", style: .secondary) {}
        CasperButton(title: "Danger", style: .danger) {}
        CasperButton(title: "Outline", style: .outline) {}
        CasperButton(title: "Loading...", style: .primary, isLoading: true) {}
    }
    .padding()
}

#Preview("Text Fields") {
    VStack(spacing: 20) {
        CasperTextField(title: "Email", text: .constant(""), icon: "envelope")
        CasperTextField(title: "Password", text: .constant(""), icon: "lock", isSecure: true)
    }
    .padding()
}

#Preview("Latency Badges") {
    VStack(spacing: 16) {
        LatencyBadge(latency: 45)
        LatencyBadge(latency: 150)
        LatencyBadge(latency: 250)
        LatencyBadge(latency: nil)
        LatencyBadge(latency: 80, isCompact: true)
    }
    .padding()
    .background(Theme.backgroundColor)
}

#Preview("Load Indicators") {
    VStack(spacing: 16) {
        ServerLoadIndicator(load: 15)
        ServerLoadIndicator(load: 50)
        ServerLoadIndicator(load: 85)
        ServerLoadIndicator(load: 30, showPercentage: false, barWidth: 40)
    }
    .padding()
    .background(Theme.backgroundColor)
}

#Preview("Feature Badges") {
    VStack(spacing: 16) {
        FeatureBadge(feature: .p2p)
        FeatureBadge(feature: .streaming)
        FeatureBadge(feature: .gaming)
        FeatureBadgeRow(features: [.p2p, .streaming, .gaming, .doublVPN])
    }
    .padding()
    .background(Theme.backgroundColor)
}
#endif
