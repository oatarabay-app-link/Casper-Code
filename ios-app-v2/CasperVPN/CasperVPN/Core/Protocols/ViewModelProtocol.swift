//
//  ViewModelProtocol.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//  Copyright © 2024 CasperVPN. All rights reserved.
//

import Foundation
import Combine

// MARK: - Base ViewModel Protocol

/// Base protocol for all ViewModels in the application.
/// Provides common functionality for state management and error handling.
protocol ViewModelProtocol: ObservableObject {
    
    /// Whether the ViewModel is currently loading data
    var isLoading: Bool { get set }
    
    /// Current error message, if any
    var errorMessage: String? { get set }
    
    /// Whether an error alert should be shown
    var showError: Bool { get set }
    
    /// Set of cancellables for Combine subscriptions
    var cancellables: Set<AnyCancellable> { get set }
}

// MARK: - Default Implementation

extension ViewModelProtocol {
    
    /// Handles an error by setting the error message and showing the error alert
    /// - Parameter error: The error to handle
    func handleError(_ error: Error) {
        DispatchQueue.main.async { [self] in
            if let apiError = error as? APIError {
                self.errorMessage = apiError.errorDescription
            } else if let vpnError = error as? VPNError {
                self.errorMessage = vpnError.errorDescription
            } else {
                self.errorMessage = error.localizedDescription
            }
            self.showError = true
            self.isLoading = false
        }
    }
    
    /// Clears the current error state
    func clearError() {
        errorMessage = nil
        showError = false
    }
}

// MARK: - Loadable Protocol

/// Protocol for ViewModels that load data asynchronously.
protocol LoadableViewModel: ViewModelProtocol {
    
    /// The type of data being loaded
    associatedtype DataType
    
    /// The loaded data
    var data: DataType? { get set }
    
    /// Loads or refreshes the data
    func loadData() async
    
    /// Refreshes the data (typically same as loadData, but can include cache invalidation)
    func refresh() async
}

// MARK: - Paginated Protocol

/// Protocol for ViewModels that handle paginated data.
protocol PaginatedViewModel: LoadableViewModel where DataType == [Item] {
    
    /// The type of items in the list
    associatedtype Item
    
    /// Current page number
    var currentPage: Int { get set }
    
    /// Total number of pages
    var totalPages: Int { get set }
    
    /// Whether more pages are available
    var hasMorePages: Bool { get }
    
    /// Whether currently loading more data
    var isLoadingMore: Bool { get set }
    
    /// Loads the next page of data
    func loadNextPage() async
}

// MARK: - Default Paginated Implementation

extension PaginatedViewModel {
    
    var hasMorePages: Bool {
        currentPage < totalPages
    }
    
    func loadNextPage() async {
        guard !isLoadingMore && hasMorePages else { return }
        
        await MainActor.run {
            isLoadingMore = true
        }
        
        currentPage += 1
        await loadData()
        
        await MainActor.run {
            isLoadingMore = false
        }
    }
}

// MARK: - Searchable Protocol

/// Protocol for ViewModels that support search functionality.
protocol SearchableViewModel: ViewModelProtocol {
    
    /// The search query string
    var searchQuery: String { get set }
    
    /// Whether a search is currently active
    var isSearching: Bool { get }
    
    /// Performs a search with the current query
    func performSearch() async
    
    /// Clears the search and resets to default state
    func clearSearch()
}

// MARK: - Default Searchable Implementation

extension SearchableViewModel {
    
    var isSearching: Bool {
        !searchQuery.isEmpty
    }
    
    func clearSearch() {
        searchQuery = ""
    }
}

// MARK: - Form ViewModel Protocol

/// Protocol for ViewModels that handle form input and validation.
protocol FormViewModel: ViewModelProtocol {
    
    /// Whether the form is valid
    var isValid: Bool { get }
    
    /// Validation errors keyed by field name
    var validationErrors: [String: String] { get set }
    
    /// Validates all form fields
    func validate() -> Bool
    
    /// Submits the form
    func submit() async throws
}

// MARK: - Validation Rule

/// A validation rule for form fields.
struct ValidationRule {
    let field: String
    let validate: (String) -> Bool
    let errorMessage: String
}

// MARK: - Common Validation Rules

enum ValidationRules {
    
    /// Validates that a string is not empty
    static func required(field: String) -> ValidationRule {
        ValidationRule(
            field: field,
            validate: { !$0.trimmingCharacters(in: .whitespacesAndNewlines).isEmpty },
            errorMessage: "\(field) is required"
        )
    }
    
    /// Validates email format
    static func email(field: String) -> ValidationRule {
        ValidationRule(
            field: field,
            validate: { email in
                let emailRegex = #"^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$"#
                return email.range(of: emailRegex, options: .regularExpression) != nil
            },
            errorMessage: "Invalid email format"
        )
    }
    
    /// Validates minimum string length
    static func minLength(field: String, length: Int) -> ValidationRule {
        ValidationRule(
            field: field,
            validate: { $0.count >= length },
            errorMessage: "\(field) must be at least \(length) characters"
        )
    }
    
    /// Validates maximum string length
    static func maxLength(field: String, length: Int) -> ValidationRule {
        ValidationRule(
            field: field,
            validate: { $0.count <= length },
            errorMessage: "\(field) must be at most \(length) characters"
        )
    }
    
    /// Validates password strength
    static func password(field: String) -> ValidationRule {
        ValidationRule(
            field: field,
            validate: { password in
                // At least 8 characters, 1 uppercase, 1 lowercase, 1 digit
                let minLength = password.count >= 8
                let hasUppercase = password.range(of: "[A-Z]", options: .regularExpression) != nil
                let hasLowercase = password.range(of: "[a-z]", options: .regularExpression) != nil
                let hasDigit = password.range(of: "[0-9]", options: .regularExpression) != nil
                return minLength && hasUppercase && hasLowercase && hasDigit
            },
            errorMessage: "Password must be at least 8 characters with uppercase, lowercase, and number"
        )
    }
    
    /// Validates that two fields match
    static func matches(field: String, otherField: String, otherValue: @escaping () -> String) -> ValidationRule {
        ValidationRule(
            field: field,
            validate: { $0 == otherValue() },
            errorMessage: "\(field) must match \(otherField)"
        )
    }
}

// MARK: - View State

/// Represents the state of a view.
enum ViewState<T> {
    case idle
    case loading
    case loaded(T)
    case error(Error)
    
    var isLoading: Bool {
        if case .loading = self { return true }
        return false
    }
    
    var data: T? {
        if case .loaded(let data) = self { return data }
        return nil
    }
    
    var error: Error? {
        if case .error(let error) = self { return error }
        return nil
    }
}

// MARK: - Alert Item

/// Represents an alert to be shown.
struct AlertItem: Identifiable {
    let id = UUID()
    let title: String
    let message: String
    let dismissButtonTitle: String
    var primaryAction: (() -> Void)?
    var primaryButtonTitle: String?
    
    init(
        title: String,
        message: String,
        dismissButtonTitle: String = "OK",
        primaryAction: (() -> Void)? = nil,
        primaryButtonTitle: String? = nil
    ) {
        self.title = title
        self.message = message
        self.dismissButtonTitle = dismissButtonTitle
        self.primaryAction = primaryAction
        self.primaryButtonTitle = primaryButtonTitle
    }
}

// MARK: - Coordinator Protocol

/// Protocol for coordinators that handle navigation.
protocol CoordinatorProtocol: ObservableObject {
    
    /// The navigation path
    associatedtype Route: Hashable
    
    /// Current navigation stack
    var navigationPath: [Route] { get set }
    
    /// Navigates to a route
    func navigate(to route: Route)
    
    /// Pops the current route
    func pop()
    
    /// Pops to the root
    func popToRoot()
}

// MARK: - Default Coordinator Implementation

extension CoordinatorProtocol {
    
    func navigate(to route: Route) {
        navigationPath.append(route)
    }
    
    func pop() {
        guard !navigationPath.isEmpty else { return }
        navigationPath.removeLast()
    }
    
    func popToRoot() {
        navigationPath.removeAll()
    }
}
