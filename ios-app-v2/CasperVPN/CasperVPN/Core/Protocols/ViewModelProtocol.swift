//
//  ViewModelProtocol.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//

import Foundation
import Combine

// MARK: - Base ViewModel Protocol
protocol ViewModelProtocol: ObservableObject {
    associatedtype State
    associatedtype Action
    
    var state: State { get }
    func send(_ action: Action)
}

// MARK: - Loading State Protocol
protocol LoadingStateProtocol {
    var isLoading: Bool { get }
    var error: Error? { get }
}

// MARK: - Refreshable Protocol
protocol RefreshableProtocol {
    func refresh() async
}
