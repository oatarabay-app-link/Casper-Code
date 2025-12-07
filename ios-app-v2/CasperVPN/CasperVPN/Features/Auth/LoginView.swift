//
//  LoginView.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//  Copyright © 2024 CasperVPN. All rights reserved.
//

import SwiftUI

/// Login and registration view for CasperVPN.
/// Handles user authentication with email/password.
struct LoginView: View {
    
    // MARK: - Properties
    
    @EnvironmentObject private var authViewModel: AuthViewModel
    
    @State private var isShowingRegistration = false
    @State private var email = ""
    @State private var password = ""
    @State private var confirmPassword = ""
    @State private var firstName = ""
    @State private var lastName = ""
    @State private var rememberMe = true
    @State private var isShowingForgotPassword = false
    
    @FocusState private var focusedField: Field?
    
    enum Field {
        case email, password, confirmPassword, firstName, lastName
    }
    
    // MARK: - Body
    
    var body: some View {
        NavigationStack {
            ScrollView {
                VStack(spacing: 32) {
                    // Logo and Title
                    logoSection
                    
                    // Form
                    formSection
                    
                    // Submit Button
                    submitButton
                    
                    // Toggle Login/Register
                    toggleSection
                    
                    // Forgot Password
                    if !isShowingRegistration {
                        forgotPasswordButton
                    }
                }
                .padding(24)
            }
            .background(Theme.Colors.background.ignoresSafeArea())
            .navigationBarTitleDisplayMode(.inline)
            .alert("Error", isPresented: $authViewModel.showError) {
                Button("OK", role: .cancel) {}
            } message: {
                Text(authViewModel.errorMessage ?? "An error occurred")
            }
            .sheet(isPresented: $isShowingForgotPassword) {
                ForgotPasswordView()
            }
        }
    }
    
    // MARK: - Logo Section
    
    private var logoSection: some View {
        VStack(spacing: 16) {
            Image(systemName: "shield.checkered")
                .font(.system(size: 80))
                .foregroundColor(Theme.Colors.primary)
            
            Text("CasperVPN")
                .font(Theme.Fonts.largeTitle)
                .foregroundColor(Theme.Colors.textPrimary)
            
            Text(isShowingRegistration ? "Create your account" : "Secure your connection")
                .font(Theme.Fonts.body)
                .foregroundColor(Theme.Colors.textSecondary)
        }
        .padding(.top, 40)
    }
    
    // MARK: - Form Section
    
    private var formSection: some View {
        VStack(spacing: 16) {
            // Registration-only fields
            if isShowingRegistration {
                HStack(spacing: 12) {
                    CasperTextField(
                        title: "First Name",
                        text: $firstName,
                        icon: "person"
                    )
                    .focused($focusedField, equals: .firstName)
                    
                    CasperTextField(
                        title: "Last Name",
                        text: $lastName,
                        icon: "person"
                    )
                    .focused($focusedField, equals: .lastName)
                }
            }
            
            // Email field
            CasperTextField(
                title: "Email",
                text: $email,
                icon: "envelope",
                keyboardType: .emailAddress,
                autocapitalization: .never
            )
            .focused($focusedField, equals: .email)
            
            // Password field
            CasperTextField(
                title: "Password",
                text: $password,
                icon: "lock",
                isSecure: true
            )
            .focused($focusedField, equals: .password)
            
            // Confirm password (registration only)
            if isShowingRegistration {
                CasperTextField(
                    title: "Confirm Password",
                    text: $confirmPassword,
                    icon: "lock",
                    isSecure: true
                )
                .focused($focusedField, equals: .confirmPassword)
            }
            
            // Remember me toggle (login only)
            if !isShowingRegistration {
                HStack {
                    Toggle("Remember me", isOn: $rememberMe)
                        .toggleStyle(SwitchToggleStyle(tint: Theme.Colors.primary))
                        .font(Theme.Fonts.callout)
                        .foregroundColor(Theme.Colors.textSecondary)
                    
                    Spacer()
                }
            }
        }
    }
    
    // MARK: - Submit Button
    
    private var submitButton: some View {
        CasperButton(
            title: isShowingRegistration ? "Create Account" : "Sign In",
            style: .primary,
            isLoading: authViewModel.isLoading
        ) {
            submitForm()
        }
        .disabled(!isFormValid)
    }
    
    // MARK: - Toggle Section
    
    private var toggleSection: some View {
        HStack {
            Text(isShowingRegistration ? "Already have an account?" : "Don't have an account?")
                .font(Theme.Fonts.callout)
                .foregroundColor(Theme.Colors.textSecondary)
            
            Button(isShowingRegistration ? "Sign In" : "Create Account") {
                withAnimation(.easeInOut(duration: 0.3)) {
                    isShowingRegistration.toggle()
                    clearForm()
                }
            }
            .font(Theme.Fonts.callout.bold())
            .foregroundColor(Theme.Colors.primary)
        }
    }
    
    // MARK: - Forgot Password Button
    
    private var forgotPasswordButton: some View {
        Button("Forgot Password?") {
            isShowingForgotPassword = true
        }
        .font(Theme.Fonts.callout)
        .foregroundColor(Theme.Colors.textSecondary)
    }
    
    // MARK: - Computed Properties
    
    private var isFormValid: Bool {
        if isShowingRegistration {
            return !email.isEmpty &&
                   !password.isEmpty &&
                   !confirmPassword.isEmpty &&
                   password == confirmPassword &&
                   isValidEmail(email) &&
                   password.count >= 8
        } else {
            return !email.isEmpty && !password.isEmpty
        }
    }
    
    // MARK: - Methods
    
    private func submitForm() {
        focusedField = nil
        
        Task {
            if isShowingRegistration {
                await authViewModel.register(
                    email: email,
                    password: password,
                    firstName: firstName.isEmpty ? nil : firstName,
                    lastName: lastName.isEmpty ? nil : lastName
                )
            } else {
                await authViewModel.login(
                    email: email,
                    password: password,
                    rememberMe: rememberMe
                )
            }
        }
    }
    
    private func clearForm() {
        email = ""
        password = ""
        confirmPassword = ""
        firstName = ""
        lastName = ""
    }
    
    private func isValidEmail(_ email: String) -> Bool {
        let emailRegex = #"^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$"#
        return email.range(of: emailRegex, options: .regularExpression) != nil
    }
}

// MARK: - Forgot Password View

struct ForgotPasswordView: View {
    
    @Environment(\.dismiss) private var dismiss
    @EnvironmentObject private var authViewModel: AuthViewModel
    
    @State private var email = ""
    @State private var isSubmitted = false
    
    var body: some View {
        NavigationStack {
            VStack(spacing: 24) {
                // Icon
                Image(systemName: "key.fill")
                    .font(.system(size: 60))
                    .foregroundColor(Theme.Colors.primary)
                    .padding(.top, 40)
                
                // Title
                Text("Reset Password")
                    .font(Theme.Fonts.title)
                    .foregroundColor(Theme.Colors.textPrimary)
                
                // Description
                Text("Enter your email address and we'll send you a link to reset your password.")
                    .font(Theme.Fonts.body)
                    .foregroundColor(Theme.Colors.textSecondary)
                    .multilineTextAlignment(.center)
                    .padding(.horizontal)
                
                if isSubmitted {
                    // Success message
                    VStack(spacing: 16) {
                        Image(systemName: "checkmark.circle.fill")
                            .font(.system(size: 48))
                            .foregroundColor(Theme.Colors.success)
                        
                        Text("Check your email")
                            .font(Theme.Fonts.headline)
                            .foregroundColor(Theme.Colors.textPrimary)
                        
                        Text("We've sent password reset instructions to your email.")
                            .font(Theme.Fonts.body)
                            .foregroundColor(Theme.Colors.textSecondary)
                            .multilineTextAlignment(.center)
                    }
                    .padding()
                    .background(Theme.Colors.success.opacity(0.1))
                    .cornerRadius(12)
                } else {
                    // Email input
                    CasperTextField(
                        title: "Email",
                        text: $email,
                        icon: "envelope",
                        keyboardType: .emailAddress,
                        autocapitalization: .never
                    )
                    
                    // Submit button
                    CasperButton(
                        title: "Send Reset Link",
                        style: .primary,
                        isLoading: authViewModel.isLoading
                    ) {
                        submitForgotPassword()
                    }
                    .disabled(email.isEmpty)
                }
                
                Spacer()
            }
            .padding(24)
            .background(Theme.Colors.background.ignoresSafeArea())
            .navigationTitle("Reset Password")
            .navigationBarTitleDisplayMode(.inline)
            .toolbar {
                ToolbarItem(placement: .navigationBarLeading) {
                    Button("Cancel") {
                        dismiss()
                    }
                }
            }
        }
    }
    
    private func submitForgotPassword() {
        Task {
            await authViewModel.forgotPassword(email: email)
            await MainActor.run {
                if authViewModel.errorMessage == nil {
                    isSubmitted = true
                }
            }
        }
    }
}

// MARK: - Preview

#if DEBUG
#Preview {
    LoginView()
        .environmentObject(AuthViewModel())
}
#endif
