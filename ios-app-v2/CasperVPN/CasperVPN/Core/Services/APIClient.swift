//
//  APIClient.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//  Copyright © 2024 CasperVPN. All rights reserved.
//

import Foundation

/// HTTP client for communicating with the CasperVPN backend API.
/// Implements JWT authentication with automatic token refresh.
final class APIClient: APIClientProtocol {
    
    // MARK: - Singleton
    
    static let shared = APIClient()
    
    // MARK: - Properties
    
    private let session: URLSession
    private let decoder: JSONDecoder
    private let encoder: JSONEncoder
    private let keychainService: KeychainServiceProtocol
    
    /// Current access token
    private var accessToken: String?
    
    /// Whether token refresh is in progress
    private var isRefreshing = false
    
    /// Queue for pending requests during token refresh
    private var pendingRequests: [(Result<Data, Error>) -> Void] = []
    
    /// Lock for thread-safe token refresh
    private let refreshLock = NSLock()
    
    // MARK: - Initialization
    
    init(
        session: URLSession = .shared,
        keychainService: KeychainServiceProtocol = KeychainService.shared
    ) {
        self.session = session
        self.keychainService = keychainService
        
        // Configure JSON decoder
        self.decoder = JSONDecoder()
        self.decoder.dateDecodingStrategy = .custom { decoder in
            let container = try decoder.singleValueContainer()
            let dateString = try container.decode(String.self)
            
            // Try ISO8601 with fractional seconds
            let formatter = ISO8601DateFormatter()
            formatter.formatOptions = [.withInternetDateTime, .withFractionalSeconds]
            if let date = formatter.date(from: dateString) {
                return date
            }
            
            // Try ISO8601 without fractional seconds
            formatter.formatOptions = [.withInternetDateTime]
            if let date = formatter.date(from: dateString) {
                return date
            }
            
            throw DecodingError.dataCorruptedError(
                in: container,
                debugDescription: "Cannot decode date: \(dateString)"
            )
        }
        
        // Configure JSON encoder
        self.encoder = JSONEncoder()
        self.encoder.dateEncodingStrategy = .iso8601
        
        // Load stored access token
        loadStoredToken()
    }
    
    // MARK: - Public Methods
    
    func get<T: Decodable>(endpoint: String, queryItems: [URLQueryItem]? = nil) async throws -> T {
        let request = try buildRequest(endpoint: endpoint, method: "GET", queryItems: queryItems)
        return try await performRequest(request)
    }
    
    func post<T: Decodable, U: Encodable>(endpoint: String, body: U?) async throws -> T {
        var request = try buildRequest(endpoint: endpoint, method: "POST")
        if let body = body {
            request.httpBody = try encoder.encode(body)
            request.setValue("application/json", forHTTPHeaderField: "Content-Type")
        }
        return try await performRequest(request)
    }
    
    func put<T: Decodable, U: Encodable>(endpoint: String, body: U?) async throws -> T {
        var request = try buildRequest(endpoint: endpoint, method: "PUT")
        if let body = body {
            request.httpBody = try encoder.encode(body)
            request.setValue("application/json", forHTTPHeaderField: "Content-Type")
        }
        return try await performRequest(request)
    }
    
    func delete<T: Decodable>(endpoint: String) async throws -> T {
        let request = try buildRequest(endpoint: endpoint, method: "DELETE")
        return try await performRequest(request)
    }
    
    func registerDeviceToken(_ token: String) async throws {
        struct DeviceTokenRequest: Encodable {
            let deviceToken: String
            let platform: String
        }
        
        let body = DeviceTokenRequest(deviceToken: token, platform: "ios")
        let _: APIResponse<EmptyResponse> = try await post(endpoint: "/api/users/device-token", body: body)
    }
    
    func setAuthToken(_ token: String?) {
        self.accessToken = token
        if let token = token {
            try? keychainService.save(token, forKey: KeychainKeys.accessToken)
        } else {
            try? keychainService.delete(forKey: KeychainKeys.accessToken)
        }
    }
    
    func refreshAuthToken() async throws -> AuthResponse {
        guard let refreshToken = try? keychainService.get(forKey: KeychainKeys.refreshToken) else {
            throw APIError.unauthorized
        }
        
        struct RefreshRequest: Encodable {
            let refreshToken: String
        }
        
        let request = RefreshRequest(refreshToken: refreshToken)
        
        // Temporarily clear access token to avoid using expired token
        let oldToken = accessToken
        accessToken = nil
        
        do {
            let response: APIResponse<AuthResponse> = try await post(
                endpoint: "/api/auth/refresh",
                body: request
            )
            
            guard let authResponse = response.data else {
                accessToken = oldToken
                throw APIError.noData
            }
            
            // Store new tokens
            setAuthToken(authResponse.accessToken)
            try? keychainService.save(authResponse.refreshToken, forKey: KeychainKeys.refreshToken)
            
            return authResponse
        } catch {
            accessToken = oldToken
            throw error
        }
    }
    
    // MARK: - Private Methods
    
    private func loadStoredToken() {
        accessToken = try? keychainService.get(forKey: KeychainKeys.accessToken)
    }
    
    private func buildRequest(
        endpoint: String,
        method: String,
        queryItems: [URLQueryItem]? = nil
    ) throws -> URLRequest {
        var components = URLComponents(string: AppConfig.apiBaseURL + endpoint)
        
        if let queryItems = queryItems, !queryItems.isEmpty {
            components?.queryItems = queryItems
        }
        
        guard let url = components?.url else {
            throw APIError.invalidURL
        }
        
        var request = URLRequest(url: url)
        request.httpMethod = method
        request.setValue("application/json", forHTTPHeaderField: "Accept")
        request.timeoutInterval = AppConfig.requestTimeout
        
        // Add auth header if we have a token
        if let token = accessToken {
            request.setValue("Bearer \(token)", forHTTPHeaderField: "Authorization")
        }
        
        // Add common headers
        request.setValue(AppConfig.userAgent, forHTTPHeaderField: "User-Agent")
        request.setValue(AppConfig.appVersion, forHTTPHeaderField: "X-App-Version")
        request.setValue("ios", forHTTPHeaderField: "X-Platform")
        
        return request
    }
    
    private func performRequest<T: Decodable>(_ request: URLRequest) async throws -> T {
        do {
            let (data, response) = try await session.data(for: request)
            
            guard let httpResponse = response as? HTTPURLResponse else {
                throw APIError.invalidResponse
            }
            
            // Handle different status codes
            switch httpResponse.statusCode {
            case 200...299:
                return try decoder.decode(T.self, from: data)
                
            case 401:
                // Try to refresh token and retry
                return try await handleUnauthorized(originalRequest: request)
                
            case 403:
                throw APIError.forbidden
                
            case 404:
                throw APIError.notFound
                
            case 400...499:
                let errorResponse = try? decoder.decode(APIResponse<EmptyResponse>.self, from: data)
                throw APIError.httpError(
                    statusCode: httpResponse.statusCode,
                    message: errorResponse?.message
                )
                
            case 500...599:
                let errorResponse = try? decoder.decode(APIResponse<EmptyResponse>.self, from: data)
                throw APIError.serverError(errorResponse?.message)
                
            default:
                throw APIError.httpError(statusCode: httpResponse.statusCode, message: nil)
            }
        } catch let error as APIError {
            throw error
        } catch let error as DecodingError {
            AppLogger.shared.error("Decoding error: \(error)")
            throw APIError.decodingError(error)
        } catch {
            throw APIError.networkError(error)
        }
    }
    
    private func handleUnauthorized<T: Decodable>(originalRequest: URLRequest) async throws -> T {
        refreshLock.lock()
        
        // Check if refresh is already in progress
        if isRefreshing {
            refreshLock.unlock()
            
            // Wait for refresh to complete
            return try await withCheckedThrowingContinuation { continuation in
                pendingRequests.append { result in
                    switch result {
                    case .success(let data):
                        do {
                            let decoded = try self.decoder.decode(T.self, from: data)
                            continuation.resume(returning: decoded)
                        } catch {
                            continuation.resume(throwing: error)
                        }
                    case .failure(let error):
                        continuation.resume(throwing: error)
                    }
                }
            }
        }
        
        isRefreshing = true
        refreshLock.unlock()
        
        do {
            // Refresh the token
            _ = try await refreshAuthToken()
            
            isRefreshing = false
            
            // Retry the original request
            var newRequest = originalRequest
            if let token = accessToken {
                newRequest.setValue("Bearer \(token)", forHTTPHeaderField: "Authorization")
            }
            
            let result: T = try await performRequest(newRequest)
            
            // Process pending requests
            processPendingRequests(with: .success(Data()))
            
            return result
        } catch {
            isRefreshing = false
            
            // Clear tokens on refresh failure
            setAuthToken(nil)
            try? keychainService.delete(forKey: KeychainKeys.refreshToken)
            
            // Process pending requests with failure
            processPendingRequests(with: .failure(error))
            
            throw APIError.tokenExpired
        }
    }
    
    private func processPendingRequests(with result: Result<Data, Error>) {
        let requests = pendingRequests
        pendingRequests.removeAll()
        
        for completion in requests {
            completion(result)
        }
    }
}

// MARK: - Keychain Keys

enum KeychainKeys {
    static let accessToken = "com.caspervpn.accessToken"
    static let refreshToken = "com.caspervpn.refreshToken"
    static let userCredentials = "com.caspervpn.userCredentials"
    static let vpnConfig = "com.caspervpn.vpnConfig"
}

// MARK: - Empty Response

/// Empty response type for endpoints that don't return data
struct EmptyResponse: Codable {}

// MARK: - Request Extensions

extension URLRequest {
    /// Creates a description for logging
    var debugDescription: String {
        var desc = "\(httpMethod ?? "GET") \(url?.absoluteString ?? "nil")"
        if let headers = allHTTPHeaderFields {
            desc += "\nHeaders: \(headers)"
        }
        if let body = httpBody, let bodyString = String(data: body, encoding: .utf8) {
            desc += "\nBody: \(bodyString)"
        }
        return desc
    }
}
