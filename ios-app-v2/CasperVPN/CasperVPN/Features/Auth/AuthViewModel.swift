//
//  AuthViewModel.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//

import Foundation
import Combine

@MainActor
final class AuthViewModel: ObservableObject {
    
    // MARK: - Published Properties
    @Published var email: String = ""
    @Published var password: String = ""
    @Published var confirmPassword: String = ""
    @Published var firstName: String = ""
    @Published var lastName: String = ""
    
    @Published private(set) var isLoading: Bool = false
    @Published private(set) var error: String?
    @Published var showError: Bool = false
    @Published var isRegistering: Bool = false
    
    // MARK: - Validation
    var isEmailValid: Bool {
        let emailRegex = "[A-Z0-9a-z._%+-]+@[A-Za-z0-9.-]+\\.[A-Za-z]{2,}"
        let emailPredicate = NSPredicate(format: "SELF MATCHES %@", emailRegex)
        return emailPredicate.evaluate(with: email)
    }
    
    var isPasswordValid: Bool {
        password.count >= 8
    }
    
    var passwordsMatch: Bool {
        password == confirmPassword
    }
    
    var canLogin: Bool {
        isEmailValid && isPasswordValid
    }
    
    var canRegister: Bool {
        isEmailValid && isPasswordValid && passwordsMatch
    }
    
    // MARK: - Dependencies
    private let authService: AuthServiceProtocol
    
    // MARK: - Initialization
    init(authService: AuthServiceProtocol = AuthService.shared) {
        self.authService = authService
    }
    
    // MARK: - Public Methods
    
    func login() async -> User? {
        guard canLogin else {
            error = "Please enter a valid email and password"
            showError = true
            return nil
        }
        
        isLoading = true
        error = nil
        
        do {
            let user = try await authService.login(email: email, password: password)
            clearForm()
            return user
        } catch {
            self.error = error.localizedDescription
            showError = true
            return nil
        }
        
        isLoading = false
    }
    
    func register() async -> User? {
        guard canRegister else {
            if !passwordsMatch {
                error = "Passwords do not match"
            } else {
                error = "Please fill in all required fields"
            }
            showError = true
            return nil
        }
        
        isLoading = true
        error = nil
        
        do {
            let user = try await authService.register(
                email: email,
                password: password,
                firstName: firstName.isEmpty ? nil : firstName,
                lastName: lastName.isEmpty ? nil : lastName
            )
            clearForm()
            return user
        } catch {
            self.error = error.localizedDescription
            showError = true
            return nil
        }
        
        isLoading = false
    }
    
    func toggleMode() {
        isRegistering.toggle()
        error = nil
        showError = false
    }
    
    func clearForm() {
        email = ""
        password = ""
        confirmPassword = ""
        firstName = ""
        lastName = ""
    }
}
