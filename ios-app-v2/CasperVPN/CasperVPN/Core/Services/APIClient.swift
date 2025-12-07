//
//  APIClient.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//

import Foundation

/// HTTP API client for communicating with the CasperVPN backend
final class APIClient: APIClientProtocol {
    
    // MARK: - Singleton
    static let shared = APIClient()
    
    // MARK: - Properties
    private let session: URLSession
    private var authToken: String?
    private let decoder: JSONDecoder
    private let encoder: JSONEncoder
    private let logger = ConnectionLogger.shared
    
    // MARK: - Initialization
    private init() {
        let configuration = URLSessionConfiguration.default
        configuration.timeoutIntervalForRequest = Config.requestTimeout
        configuration.timeoutIntervalForResource = Config.connectionTimeout
        configuration.waitsForConnectivity = true
        
        self.session = URLSession(configuration: configuration)
        
        self.decoder = JSONDecoder()
        self.decoder.dateDecodingStrategy = .iso8601
        self.decoder.keyDecodingStrategy = .convertFromSnakeCase
        
        self.encoder = JSONEncoder()
        self.encoder.dateEncodingStrategy = .iso8601
        self.encoder.keyEncodingStrategy = .convertToSnakeCase
    }
    
    // MARK: - Auth Token
    
    func setAuthToken(_ token: String?) {
        self.authToken = token
    }
    
    // MARK: - Request Methods
    
    func request<T: Decodable>(_ endpoint: String,
                               method: HTTPMethod = .GET,
                               body: Encodable? = nil,
                               headers: [String: String]? = nil) async throws -> T {
        let url = try buildURL(for: endpoint)
        var request = URLRequest(url: url)
        request.httpMethod = method.rawValue
        
        // Set default headers
        request.setValue("application/json", forHTTPHeaderField: "Content-Type")
        request.setValue("application/json", forHTTPHeaderField: "Accept")
        
        // Set auth token if available
        if let token = authToken {
            request.setValue("Bearer \(token)", forHTTPHeaderField: "Authorization")
        }
        
        // Set custom headers
        headers?.forEach { key, value in
            request.setValue(value, forHTTPHeaderField: key)
        }
        
        // Set body if provided
        if let body = body {
            request.httpBody = try encoder.encode(body)
        }
        
        logger.log("API Request: \(method.rawValue) \(endpoint)", level: .debug)
        
        do {
            let (data, response) = try await session.data(for: request)
            
            guard let httpResponse = response as? HTTPURLResponse else {
                throw APIError.invalidResponse
            }
            
            logger.log("API Response: \(httpResponse.statusCode)", level: .debug)
            
            // Handle HTTP errors
            try handleHTTPStatus(httpResponse.statusCode, data: data)
            
            // Decode response
            return try decoder.decode(T.self, from: data)
            
        } catch let error as APIError {
            logger.log("API Error: \(error.localizedDescription)", level: .error)
            throw error
        } catch let error as DecodingError {
            logger.log("Decoding Error: \(error.localizedDescription)", level: .error)
            throw APIError.decodingFailed(error.localizedDescription)
        } catch {
            logger.log("Network Error: \(error.localizedDescription)", level: .error)
            throw APIError.networkError(error.localizedDescription)
        }
    }
    
    // MARK: - Convenience Methods
    
    func get<T: Decodable>(_ endpoint: String, headers: [String: String]? = nil) async throws -> T {
        return try await request(endpoint, method: .GET, body: nil as Empty?, headers: headers)
    }
    
    func post<T: Decodable, B: Encodable>(_ endpoint: String, body: B, headers: [String: String]? = nil) async throws -> T {
        return try await request(endpoint, method: .POST, body: body, headers: headers)
    }
    
    func put<T: Decodable, B: Encodable>(_ endpoint: String, body: B, headers: [String: String]? = nil) async throws -> T {
        return try await request(endpoint, method: .PUT, body: body, headers: headers)
    }
    
    func delete<T: Decodable>(_ endpoint: String, headers: [String: String]? = nil) async throws -> T {
        return try await request(endpoint, method: .DELETE, body: nil as Empty?, headers: headers)
    }
    
    // MARK: - Private Methods
    
    private func buildURL(for endpoint: String) throws -> URL {
        let baseURL = Config.fullAPIURL
        let fullPath = endpoint.hasPrefix("/") ? "\(baseURL)\(endpoint)" : "\(baseURL)/\(endpoint)"
        
        guard let url = URL(string: fullPath) else {
            throw APIError.invalidURL
        }
        
        return url
    }
    
    private func handleHTTPStatus(_ statusCode: Int, data: Data) throws {
        switch statusCode {
        case 200...299:
            return // Success
        case 401:
            throw APIError.unauthorized
        case 403:
            throw APIError.forbidden
        case 404:
            throw APIError.notFound
        case 422:
            // Try to parse validation errors
            if let errorResponse = try? decoder.decode(ErrorResponse.self, from: data) {
                throw APIError.validationError(errorResponse.message ?? "Validation failed")
            }
            throw APIError.validationError("Validation failed")
        case 429:
            throw APIError.rateLimited
        case 500...599:
            throw APIError.serverError(statusCode)
        default:
            throw APIError.httpError(statusCode)
        }
    }
}

// MARK: - API Error

enum APIError: LocalizedError {
    case invalidURL
    case invalidResponse
    case unauthorized
    case forbidden
    case notFound
    case validationError(String)
    case rateLimited
    case serverError(Int)
    case httpError(Int)
    case networkError(String)
    case decodingFailed(String)
    case encodingFailed
    
    var errorDescription: String? {
        switch self {
        case .invalidURL:
            return "Invalid URL"
        case .invalidResponse:
            return "Invalid response from server"
        case .unauthorized:
            return "Authentication required"
        case .forbidden:
            return "Access denied"
        case .notFound:
            return "Resource not found"
        case .validationError(let message):
            return message
        case .rateLimited:
            return "Too many requests. Please try again later."
        case .serverError(let code):
            return "Server error (code: \(code))"
        case .httpError(let code):
            return "HTTP error (code: \(code))"
        case .networkError(let message):
            return "Network error: \(message)"
        case .decodingFailed(let message):
            return "Failed to parse response: \(message)"
        case .encodingFailed:
            return "Failed to encode request"
        }
    }
}

// MARK: - Helper Types

private struct Empty: Encodable {}

struct ErrorResponse: Codable {
    let success: Bool
    let message: String?
    let errors: [String: [String]]?
}
