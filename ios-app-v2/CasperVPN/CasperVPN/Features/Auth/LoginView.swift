//
//  LoginView.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//

import SwiftUI

struct LoginView: View {
    @StateObject private var viewModel = AuthViewModel()
    @EnvironmentObject var coordinator: AppCoordinator
    
    var body: some View {
        ZStack {
            Theme.backgroundGradient
                .ignoresSafeArea()
            
            ScrollView {
                VStack(spacing: 30) {
                    // Logo
                    VStack(spacing: 16) {
                        Image(systemName: "shield.checkered")
                            .font(.system(size: 80))
                            .foregroundStyle(
                                LinearGradient(
                                    colors: [Theme.primaryColor, Theme.secondaryColor],
                                    startPoint: .topLeading,
                                    endPoint: .bottomTrailing
                                )
                            )
                        
                        Text("CasperVPN")
                            .font(.largeTitle)
                            .fontWeight(.bold)
                            .foregroundColor(.white)
                        
                        Text(viewModel.isRegistering ? "Create your account" : "Welcome back")
                            .font(.subheadline)
                            .foregroundColor(.gray)
                    }
                    .padding(.top, 60)
                    
                    // Form
                    VStack(spacing: 16) {
                        if viewModel.isRegistering {
                            HStack(spacing: 12) {
                                CustomTextField(
                                    placeholder: "First Name",
                                    text: $viewModel.firstName,
                                    icon: "person"
                                )
                                
                                CustomTextField(
                                    placeholder: "Last Name",
                                    text: $viewModel.lastName,
                                    icon: "person"
                                )
                            }
                        }
                        
                        CustomTextField(
                            placeholder: "Email",
                            text: $viewModel.email,
                            icon: "envelope",
                            keyboardType: .emailAddress,
                            autocapitalization: .never
                        )
                        
                        CustomTextField(
                            placeholder: "Password",
                            text: $viewModel.password,
                            icon: "lock",
                            isSecure: true
                        )
                        
                        if viewModel.isRegistering {
                            CustomTextField(
                                placeholder: "Confirm Password",
                                text: $viewModel.confirmPassword,
                                icon: "lock",
                                isSecure: true
                            )
                        }
                    }
                    .padding(.horizontal)
                    
                    // Action Button
                    Button {
                        Task {
                            if viewModel.isRegistering {
                                if let user = await viewModel.register() {
                                    coordinator.currentUser = user
                                    coordinator.isAuthenticated = true
                                }
                            } else {
                                if let user = await viewModel.login() {
                                    coordinator.currentUser = user
                                    coordinator.isAuthenticated = true
                                }
                            }
                        }
                    } label: {
                        HStack {
                            if viewModel.isLoading {
                                ProgressView()
                                    .tint(.white)
                            } else {
                                Text(viewModel.isRegistering ? "Create Account" : "Sign In")
                                    .fontWeight(.semibold)
                            }
                        }
                        .frame(maxWidth: .infinity)
                        .frame(height: 50)
                        .background(
                            LinearGradient(
                                colors: [Theme.primaryColor, Theme.secondaryColor],
                                startPoint: .leading,
                                endPoint: .trailing
                            )
                        )
                        .cornerRadius(12)
                        .foregroundColor(.white)
                    }
                    .disabled(viewModel.isLoading)
                    .padding(.horizontal)
                    
                    // Toggle Mode
                    Button {
                        withAnimation {
                            viewModel.toggleMode()
                        }
                    } label: {
                        Text(viewModel.isRegistering ? "Already have an account? Sign In" : "Don't have an account? Sign Up")
                            .font(.subheadline)
                            .foregroundColor(Theme.primaryColor)
                    }
                    
                    Spacer()
                }
            }
        }
        .alert("Error", isPresented: $viewModel.showError) {
            Button("OK", role: .cancel) {}
        } message: {
            if let error = viewModel.error {
                Text(error)
            }
        }
    }
}

// MARK: - Custom Text Field
struct CustomTextField: View {
    let placeholder: String
    @Binding var text: String
    let icon: String
    var keyboardType: UIKeyboardType = .default
    var autocapitalization: TextInputAutocapitalization = .sentences
    var isSecure: Bool = false
    
    @State private var showPassword: Bool = false
    
    var body: some View {
        HStack(spacing: 12) {
            Image(systemName: icon)
                .foregroundColor(.gray)
                .frame(width: 20)
            
            if isSecure && !showPassword {
                SecureField(placeholder, text: $text)
                    .foregroundColor(.white)
            } else {
                TextField(placeholder, text: $text)
                    .foregroundColor(.white)
                    .keyboardType(keyboardType)
                    .textInputAutocapitalization(autocapitalization)
            }
            
            if isSecure {
                Button {
                    showPassword.toggle()
                } label: {
                    Image(systemName: showPassword ? "eye.slash" : "eye")
                        .foregroundColor(.gray)
                }
            }
        }
        .padding()
        .background(Color.white.opacity(0.1))
        .cornerRadius(12)
    }
}

// MARK: - Preview
#Preview {
    LoginView()
        .environmentObject(AppCoordinator())
}
