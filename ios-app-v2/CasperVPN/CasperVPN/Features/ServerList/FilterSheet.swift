//
//  FilterSheet.swift
//  CasperVPN
//
//  Created by CasperVPN Team
//

import SwiftUI

// MARK: - Sort Option

/// Options for sorting the server list
enum ServerSortOption: String, CaseIterable, Identifiable {
    case name = "Name"
    case latency = "Latency"
    case load = "Load"
    case country = "Country"
    
    var id: String { rawValue }
    
    /// SF Symbol icon for the sort option
    var icon: String {
        switch self {
        case .name:
            return "textformat"
        case .latency:
            return "speedometer"
        case .load:
            return "chart.bar.fill"
        case .country:
            return "globe"
        }
    }
}

// MARK: - Filter Options

/// Options for filtering the server list
struct ServerFilterOptions: Equatable {
    /// Show only premium servers
    var premiumOnly: Bool = false
    
    /// Show only online servers
    var onlineOnly: Bool = true
    
    /// Filter by specific features
    var selectedFeatures: Set<ServerFeature> = []
    
    /// Filter by country codes
    var selectedCountries: Set<String> = []
    
    /// Whether any filters are active
    var hasActiveFilters: Bool {
        premiumOnly || !onlineOnly || !selectedFeatures.isEmpty || !selectedCountries.isEmpty
    }
    
    /// Reset all filters to defaults
    mutating func reset() {
        premiumOnly = false
        onlineOnly = true
        selectedFeatures.removeAll()
        selectedCountries.removeAll()
    }
}

// MARK: - Filter Sheet View

/// A sheet view for filtering and sorting server lists.
struct FilterSheet: View {
    
    @Binding var sortOption: ServerSortOption
    @Binding var sortAscending: Bool
    @Binding var filterOptions: ServerFilterOptions
    
    /// Available countries for filtering (passed from parent)
    let availableCountries: [String]
    
    @Environment(\.dismiss) private var dismiss
    
    var body: some View {
        NavigationStack {
            ZStack {
                Theme.backgroundGradient
                    .ignoresSafeArea()
                
                ScrollView {
                    VStack(spacing: 24) {
                        // Sort section
                        sortSection
                        
                        // Filter section
                        filterSection
                        
                        // Features section
                        featuresSection
                        
                        // Countries section (if available)
                        if !availableCountries.isEmpty {
                            countriesSection
                        }
                        
                        // Reset button
                        resetButton
                    }
                    .padding()
                }
            }
            .navigationTitle("Filter & Sort")
            .navigationBarTitleDisplayMode(.inline)
            .toolbar {
                ToolbarItem(placement: .navigationBarLeading) {
                    Button("Cancel") {
                        dismiss()
                    }
                    .foregroundColor(Theme.Colors.textSecondary)
                }
                
                ToolbarItem(placement: .navigationBarTrailing) {
                    Button("Apply") {
                        dismiss()
                    }
                    .fontWeight(.semibold)
                    .foregroundColor(Theme.Colors.primary)
                }
            }
        }
    }
    
    // MARK: - Sort Section
    
    private var sortSection: some View {
        VStack(alignment: .leading, spacing: 12) {
            SectionHeader(title: "Sort By")
            
            VStack(spacing: 8) {
                ForEach(ServerSortOption.allCases) { option in
                    SortOptionRow(
                        option: option,
                        isSelected: sortOption == option,
                        isAscending: sortAscending
                    ) {
                        if sortOption == option {
                            sortAscending.toggle()
                        } else {
                            sortOption = option
                            sortAscending = true
                        }
                    }
                }
            }
            .padding()
            .background(Theme.cardGradient)
            .cornerRadius(Theme.CornerRadius.medium)
        }
    }
    
    // MARK: - Filter Section
    
    private var filterSection: some View {
        VStack(alignment: .leading, spacing: 12) {
            SectionHeader(title: "Filters")
            
            VStack(spacing: 0) {
                FilterToggleRow(
                    title: "Premium Only",
                    icon: "crown.fill",
                    iconColor: .yellow,
                    isOn: $filterOptions.premiumOnly
                )
                
                Divider()
                    .background(Color.white.opacity(0.1))
                
                FilterToggleRow(
                    title: "Online Only",
                    icon: "circle.fill",
                    iconColor: Theme.Colors.success,
                    isOn: $filterOptions.onlineOnly
                )
            }
            .padding()
            .background(Theme.cardGradient)
            .cornerRadius(Theme.CornerRadius.medium)
        }
    }
    
    // MARK: - Features Section
    
    private var featuresSection: some View {
        VStack(alignment: .leading, spacing: 12) {
            SectionHeader(title: "Features")
            
            LazyVGrid(columns: [GridItem(.flexible()), GridItem(.flexible())], spacing: 8) {
                ForEach(ServerFeature.allCases, id: \.self) { feature in
                    FeatureFilterChip(
                        feature: feature,
                        isSelected: filterOptions.selectedFeatures.contains(feature)
                    ) {
                        if filterOptions.selectedFeatures.contains(feature) {
                            filterOptions.selectedFeatures.remove(feature)
                        } else {
                            filterOptions.selectedFeatures.insert(feature)
                        }
                    }
                }
            }
        }
    }
    
    // MARK: - Countries Section
    
    private var countriesSection: some View {
        VStack(alignment: .leading, spacing: 12) {
            HStack {
                SectionHeader(title: "Countries")
                
                if !filterOptions.selectedCountries.isEmpty {
                    Text("(\(filterOptions.selectedCountries.count) selected)")
                        .font(Theme.Fonts.caption)
                        .foregroundColor(Theme.Colors.textSecondary)
                }
            }
            
            ScrollView(.horizontal, showsIndicators: false) {
                HStack(spacing: 8) {
                    ForEach(availableCountries.sorted(), id: \.self) { country in
                        CountryFilterChip(
                            country: country,
                            isSelected: filterOptions.selectedCountries.contains(country)
                        ) {
                            if filterOptions.selectedCountries.contains(country) {
                                filterOptions.selectedCountries.remove(country)
                            } else {
                                filterOptions.selectedCountries.insert(country)
                            }
                        }
                    }
                }
            }
        }
    }
    
    // MARK: - Reset Button
    
    private var resetButton: some View {
        Button {
            sortOption = .name
            sortAscending = true
            filterOptions.reset()
        } label: {
            HStack {
                Image(systemName: "arrow.counterclockwise")
                Text("Reset All")
            }
            .font(Theme.Fonts.callout)
            .foregroundColor(Theme.Colors.error)
            .frame(maxWidth: .infinity)
            .padding()
            .background(Theme.Colors.error.opacity(0.15))
            .cornerRadius(Theme.CornerRadius.medium)
        }
    }
}

// MARK: - Sort Option Row

/// A row for selecting a sort option.
private struct SortOptionRow: View {
    
    let option: ServerSortOption
    let isSelected: Bool
    let isAscending: Bool
    let onTap: () -> Void
    
    var body: some View {
        Button(action: onTap) {
            HStack(spacing: 12) {
                Image(systemName: option.icon)
                    .font(.system(size: 16))
                    .foregroundColor(isSelected ? Theme.Colors.primary : Theme.Colors.textSecondary)
                    .frame(width: 24)
                
                Text(option.rawValue)
                    .font(Theme.Fonts.body)
                    .foregroundColor(isSelected ? Theme.Colors.textPrimary : Theme.Colors.textSecondary)
                
                Spacer()
                
                if isSelected {
                    Image(systemName: isAscending ? "chevron.up" : "chevron.down")
                        .font(.system(size: 12, weight: .semibold))
                        .foregroundColor(Theme.Colors.primary)
                }
                
                Image(systemName: isSelected ? "checkmark.circle.fill" : "circle")
                    .foregroundColor(isSelected ? Theme.Colors.primary : Theme.Colors.textSecondary.opacity(0.5))
            }
            .padding(.vertical, 8)
            .contentShape(Rectangle())
        }
        .buttonStyle(.plain)
    }
}

// MARK: - Filter Toggle Row

/// A row with a toggle for filter options.
private struct FilterToggleRow: View {
    
    let title: String
    let icon: String
    let iconColor: Color
    @Binding var isOn: Bool
    
    var body: some View {
        HStack(spacing: 12) {
            Image(systemName: icon)
                .font(.system(size: 14))
                .foregroundColor(iconColor)
                .frame(width: 24)
            
            Text(title)
                .font(Theme.Fonts.body)
                .foregroundColor(Theme.Colors.textPrimary)
            
            Spacer()
            
            Toggle("", isOn: $isOn)
                .toggleStyle(SwitchToggleStyle(tint: Theme.Colors.primary))
                .labelsHidden()
        }
        .padding(.vertical, 4)
    }
}

// MARK: - Feature Filter Chip

/// A selectable chip for feature filtering.
private struct FeatureFilterChip: View {
    
    let feature: ServerFeature
    let isSelected: Bool
    let onTap: () -> Void
    
    var body: some View {
        Button(action: onTap) {
            HStack(spacing: 6) {
                Image(systemName: featureIcon)
                    .font(.system(size: 12))
                
                Text(feature.rawValue)
                    .font(Theme.Fonts.caption)
            }
            .foregroundColor(isSelected ? Theme.Colors.primary : Theme.Colors.textSecondary)
            .padding(.horizontal, 12)
            .padding(.vertical, 8)
            .background(isSelected ? Theme.Colors.primary.opacity(0.2) : Color.gray.opacity(0.1))
            .cornerRadius(Theme.CornerRadius.small)
            .overlay(
                RoundedRectangle(cornerRadius: Theme.CornerRadius.small)
                    .stroke(isSelected ? Theme.Colors.primary : Color.clear, lineWidth: 1)
            )
        }
        .buttonStyle(.plain)
    }
    
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
}

// MARK: - Country Filter Chip

/// A selectable chip for country filtering.
private struct CountryFilterChip: View {
    
    let country: String
    let isSelected: Bool
    let onTap: () -> Void
    
    var body: some View {
        Button(action: onTap) {
            Text(country)
                .font(Theme.Fonts.caption)
                .foregroundColor(isSelected ? Theme.Colors.primary : Theme.Colors.textSecondary)
                .padding(.horizontal, 12)
                .padding(.vertical, 8)
                .background(isSelected ? Theme.Colors.primary.opacity(0.2) : Color.gray.opacity(0.1))
                .cornerRadius(Theme.CornerRadius.small)
                .overlay(
                    RoundedRectangle(cornerRadius: Theme.CornerRadius.small)
                        .stroke(isSelected ? Theme.Colors.primary : Color.clear, lineWidth: 1)
                )
        }
        .buttonStyle(.plain)
    }
}

// MARK: - Server Feature Extension

extension ServerFeature: CaseIterable {
    static var allCases: [ServerFeature] {
        [.p2p, .streaming, .gaming, .doublVPN, .obfuscated, .dedicatedIP]
    }
}

// MARK: - Previews

#if DEBUG
#Preview("Filter Sheet") {
    FilterSheet(
        sortOption: .constant(.name),
        sortAscending: .constant(true),
        filterOptions: .constant(ServerFilterOptions()),
        availableCountries: ["United States", "Germany", "Japan", "United Kingdom", "Canada"]
    )
}
#endif
