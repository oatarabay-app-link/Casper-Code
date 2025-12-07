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
#endif
