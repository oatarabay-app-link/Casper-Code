//
//  SettingsView.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//  Copyright © 2024 CasperVPN. All rights reserved.
//

import SwiftUI

/// Settings screen for managing account, preferences, and app options.
struct SettingsView: View {
    
    // MARK: - Properties
    
    @StateObject private var viewModel = SettingsViewModel()
    @EnvironmentObject private var authViewModel: AuthViewModel
    
    @State private var showingLogoutConfirmation = false
    @State private var showingDeleteAccountConfirmation = false
    
    // MARK: - Body
    
    var body: some View {
        NavigationStack {
            List {
                // Account Section
                accountSection
                
                // Subscription Section
                subscriptionSection
                
                // VPN Settings Section
                vpnSettingsSection
                
                // App Settings Section
                appSettingsSection
                
                // Support Section
                supportSection
                
                // About Section
                aboutSection
                
                // Sign Out Section
                signOutSection
            }
            .listStyle(.insetGrouped)
            .navigationTitle("Settings")
            .alert("Sign Out", isPresented: $showingLogoutConfirmation) {
                Button("Cancel", role: .cancel) {}
                Button("Sign Out", role: .destructive) {
                    Task {
                        await authViewModel.logout()
                    }
                }
            } message: {
                Text("Are you sure you want to sign out?")
            }
            .alert("Delete Account", isPresented: $showingDeleteAccountConfirmation) {
                Button("Cancel", role: .cancel) {}
                Button("Delete", role: .destructive) {
                    Task {
                        await viewModel.deleteAccount()
                    }
                }
            } message: {
                Text("This action cannot be undone. All your data will be permanently deleted.")
            }
        }
    }
    
    // MARK: - Account Section
    
    private var accountSection: some View {
        Section("Account") {
            if let user = authViewModel.currentUser {
                // User info
                HStack(spacing: 12) {
                    Image(systemName: "person.circle.fill")
                        .font(.system(size: 50))
                        .foregroundColor(Theme.Colors.primary)
                    
                    VStack(alignment: .leading, spacing: 4) {
                        Text(user.fullName)
                            .font(Theme.Fonts.headline)
                            .foregroundColor(Theme.Colors.textPrimary)
                        
                        Text(user.email)
                            .font(Theme.Fonts.caption)
                            .foregroundColor(Theme.Colors.textSecondary)
                        
                        HStack(spacing: 4) {
                            Image(systemName: user.role.iconName)
                            Text(user.role.displayName)
                        }
                        .font(Theme.Fonts.caption)
                        .foregroundColor(Theme.Colors.primary)
                    }
                }
                .padding(.vertical, 8)
                
                // Edit Profile
                NavigationLink {
                    EditProfileView()
                } label: {
                    Label("Edit Profile", systemImage: "pencil")
                }
                
                // Change Password
                NavigationLink {
                    ChangePasswordView()
                } label: {
                    Label("Change Password", systemImage: "lock")
                }
            }
        }
    }
    
    // MARK: - Subscription Section
    
    private var subscriptionSection: some View {
        Section("Subscription") {
            if let user = authViewModel.currentUser {
                // Current plan
                HStack {
                    Label("Current Plan", systemImage: "star")
                    Spacer()
                    Text(user.planName ?? "Free")
                        .foregroundColor(Theme.Colors.textSecondary)
                }
                
                // Data usage
                if !user.hasUnlimitedData {
                    VStack(alignment: .leading, spacing: 8) {
                        HStack {
                            Label("Data Usage", systemImage: "chart.bar")
                            Spacer()
                            Text("\(user.formattedDataUsage) / \(user.formattedDataLimit)")
                                .font(Theme.Fonts.caption)
                                .foregroundColor(Theme.Colors.textSecondary)
                        }
                        
                        ProgressView(value: user.dataUsagePercentage, total: 100)
                            .tint(user.dataUsagePercentage > 80 ? Theme.Colors.error : Theme.Colors.primary)
                    }
                } else {
                    HStack {
                        Label("Data Usage", systemImage: "chart.bar")
                        Spacer()
                        Text("Unlimited")
                            .foregroundColor(Theme.Colors.textSecondary)
                    }
                }
                
                // Upgrade button (if not premium)
                if !user.isPremium {
                    NavigationLink {
                        UpgradeView()
                    } label: {
                        Label("Upgrade to Premium", systemImage: "crown")
                            .foregroundColor(Theme.Colors.primary)
                    }
                }
                
                // Manage subscription
                Button {
                    Task {
                        await viewModel.openBillingPortal()
                    }
                } label: {
                    Label("Manage Subscription", systemImage: "creditcard")
                }
            }
        }
    }
    
    // MARK: - VPN Settings Section
    
    private var vpnSettingsSection: some View {
        Section("VPN Settings") {
            // Protocol selection
            Picker(selection: $viewModel.preferredProtocol) {
                ForEach(VPNProtocolType.allCases, id: \.self) { proto in
                    Text(proto.displayName).tag(proto)
                }
            } label: {
                Label("Protocol", systemImage: "network")
            }
            
            // Auto-connect
            Toggle(isOn: $viewModel.autoConnect) {
                Label("Auto-Connect", systemImage: "bolt")
            }
            
            // Kill switch
            Toggle(isOn: $viewModel.killSwitch) {
                Label("Kill Switch", systemImage: "shield.slash")
            }
            
            // DNS settings
            NavigationLink {
                DNSSettingsView()
            } label: {
                Label("DNS Settings", systemImage: "server.rack")
            }
        }
    }
    
    // MARK: - App Settings Section
    
    private var appSettingsSection: some View {
        Section("App Settings") {
            // Notifications
            Toggle(isOn: $viewModel.notificationsEnabled) {
                Label("Notifications", systemImage: "bell")
            }
            
            // App appearance
            Picker(selection: $viewModel.appearance) {
                Text("System").tag(AppAppearance.system)
                Text("Light").tag(AppAppearance.light)
                Text("Dark").tag(AppAppearance.dark)
            } label: {
                Label("Appearance", systemImage: "paintbrush")
            }
            
            // Language
            NavigationLink {
                LanguageSettingsView()
            } label: {
                HStack {
                    Label("Language", systemImage: "globe")
                    Spacer()
                    Text("English")
                        .foregroundColor(Theme.Colors.textSecondary)
                }
            }
        }
    }
    
    // MARK: - Support Section
    
    private var supportSection: some View {
        Section("Support") {
            // Help Center
            Link(destination: URL(string: "https://help.caspervpn.com")!) {
                Label("Help Center", systemImage: "questionmark.circle")
            }
            
            // Contact Support
            Link(destination: URL(string: "mailto:support@caspervpn.com")!) {
                Label("Contact Support", systemImage: "envelope")
            }
            
            // Report a Bug
            NavigationLink {
                BugReportView()
            } label: {
                Label("Report a Bug", systemImage: "ant")
            }
        }
    }
    
    // MARK: - About Section
    
    private var aboutSection: some View {
        Section("About") {
            // Version
            HStack {
                Label("Version", systemImage: "info.circle")
                Spacer()
                Text(AppConfig.appVersion)
                    .foregroundColor(Theme.Colors.textSecondary)
            }
            
            // Privacy Policy
            Link(destination: URL(string: "https://caspervpn.com/privacy")!) {
                Label("Privacy Policy", systemImage: "hand.raised")
            }
            
            // Terms of Service
            Link(destination: URL(string: "https://caspervpn.com/terms")!) {
                Label("Terms of Service", systemImage: "doc.text")
            }
            
            // Licenses
            NavigationLink {
                LicensesView()
            } label: {
                Label("Open Source Licenses", systemImage: "doc.plaintext")
            }
        }
    }
    
    // MARK: - Sign Out Section
    
    private var signOutSection: some View {
        Section {
            Button(role: .destructive) {
                showingLogoutConfirmation = true
            } label: {
                Label("Sign Out", systemImage: "rectangle.portrait.and.arrow.right")
            }
            
            Button(role: .destructive) {
                showingDeleteAccountConfirmation = true
            } label: {
                Label("Delete Account", systemImage: "trash")
            }
        }
    }
}

// MARK: - Supporting Views

struct EditProfileView: View {
    @Environment(\.dismiss) private var dismiss
    @EnvironmentObject private var authViewModel: AuthViewModel
    
    @State private var firstName = ""
    @State private var lastName = ""
    @State private var email = ""
    
    var body: some View {
        Form {
            Section("Personal Information") {
                TextField("First Name", text: $firstName)
                TextField("Last Name", text: $lastName)
            }
            
            Section("Email") {
                TextField("Email", text: $email)
                    .keyboardType(.emailAddress)
                    .autocapitalization(.none)
            }
            
            Section {
                Button("Save Changes") {
                    // Save changes
                    dismiss()
                }
                .frame(maxWidth: .infinity)
            }
        }
        .navigationTitle("Edit Profile")
        .onAppear {
            if let user = authViewModel.currentUser {
                firstName = user.firstName ?? ""
                lastName = user.lastName ?? ""
                email = user.email
            }
        }
    }
}

struct ChangePasswordView: View {
    @State private var currentPassword = ""
    @State private var newPassword = ""
    @State private var confirmPassword = ""
    
    var body: some View {
        Form {
            Section("Current Password") {
                SecureField("Current Password", text: $currentPassword)
            }
            
            Section("New Password") {
                SecureField("New Password", text: $newPassword)
                SecureField("Confirm Password", text: $confirmPassword)
            }
            
            Section {
                Button("Change Password") {
                    // Change password
                }
                .frame(maxWidth: .infinity)
                .disabled(newPassword.isEmpty || newPassword != confirmPassword)
            }
        }
        .navigationTitle("Change Password")
    }
}

struct UpgradeView: View {
    var body: some View {
        Text("Upgrade View")
            .navigationTitle("Upgrade")
    }
}

struct DNSSettingsView: View {
    var body: some View {
        Text("DNS Settings")
            .navigationTitle("DNS Settings")
    }
}

struct LanguageSettingsView: View {
    var body: some View {
        Text("Language Settings")
            .navigationTitle("Language")
    }
}

struct BugReportView: View {
    var body: some View {
        Text("Bug Report")
            .navigationTitle("Report a Bug")
    }
}

struct LicensesView: View {
    var body: some View {
        Text("Open Source Licenses")
            .navigationTitle("Licenses")
    }
}

// MARK: - App Appearance

enum AppAppearance: String, CaseIterable {
    case system
    case light
    case dark
}

// MARK: - Preview

#if DEBUG
#Preview {
    SettingsView()
        .environmentObject(AuthViewModel())
}
#endif
