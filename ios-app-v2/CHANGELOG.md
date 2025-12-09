# Changelog

All notable changes to the CasperVPN iOS application will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Phase 2.2 - Server Management (2024-12-07)

#### Added

##### New Services & Managers
- **LatencyService.swift** - TCP-based latency measurement service
  - NWConnection-based TCP socket latency testing (ICMP ping not available on iOS)
  - Multi-sample averaging (3 samples with 0.1s delay between samples)
  - Batch concurrent latency testing for multiple servers
  - 60-second result caching with automatic expiration
  - 5-second timeout per measurement
  - LatencyStatus enum with quality levels: excellent (<50ms), good (50-100ms), fair (100-200ms), poor (>200ms)
  - Thread-safe connection handling with NSLock
  - Comprehensive logging integration

- **FavoritesManager.swift** - Favorite servers persistence and management
  - UserDefaults-based persistence for favorite server IDs
  - Combine publisher for reactive UI updates
  - Add/remove/toggle favorite operations
  - Batch operations for getting favorite servers from server list
  - Clear all favorites functionality
  - Singleton pattern with dependency injection support

- **RecentServersManager.swift** - Recent connection history tracking
  - Tracks last 5 connected servers with most-recent-first ordering
  - UserDefaults persistence with automatic list trimming
  - Combine publisher for reactive updates
  - Automatic deduplication (moves existing server to front)
  - Helper methods for checking recency and retrieving recent servers
  - Clear history functionality

##### New UI Components
- **FilterSheet.swift** - Advanced filtering and sorting interface
  - Country-based filtering with multi-select
  - Feature-based filtering (streaming, P2P, Tor, double VPN, dedicated IP)
  - Premium/free server filtering
  - Online status filtering
  - Sort options: name, latency, load, country
  - Ascending/descending sort direction toggle
  - Reset filters functionality
  - SwiftUI sheet presentation with dismiss action

- **ServerDetailView.swift** - Comprehensive server information view
  - Full server details display (name, country, city, hostname, port)
  - Real-time latency display with color-coded status badges
  - Server load indicator with visual progress bar
  - Feature badges (streaming, P2P, Tor, double VPN, dedicated IP)
  - Premium status badge
  - Online/offline status indicator
  - Connect button with VPN service integration
  - Favorite toggle button with heart icon
  - Responsive layout with proper spacing and styling

##### Enhanced UI Components (Components.swift)
- **LatencyBadge** - Color-coded latency display
  - Green for <100ms (excellent/good)
  - Yellow for 100-200ms (fair)
  - Red for >200ms (poor)
  - Loading state with animated ellipsis
  - Compact badge design with rounded corners

- **ServerLoadIndicator** - Visual server load representation
  - Horizontal progress bar with percentage
  - Color-coded: green (<50%), yellow (50-80%), red (>80%)
  - Compact design suitable for cards and detail views

- **FeatureBadge** - Server feature indicators
  - Icon + text layout for features
  - Consistent styling with theme colors
  - Support for streaming, P2P, Tor, double VPN, dedicated IP

- **PremiumBadge** - Premium server indicator
  - Crown icon with "Premium" text
  - Gold/yellow accent color
  - Compact badge design

- **OnlineStatusBadge** - Server availability indicator
  - Green dot for online servers
  - Red dot for offline servers
  - Text label with status

- **Enhanced ServerCard** - Improved server list card
  - Integrated latency badge display
  - Server load indicator
  - Feature badges row
  - Premium badge
  - Online status indicator
  - Favorite button with heart icon
  - Connect button
  - Responsive layout with proper spacing
  - Tap gesture for navigation to detail view

##### Theme Enhancements (Theme.swift)
- **Colors namespace** - Organized color definitions
  - Primary, secondary, accent colors
  - Background colors (primary, secondary, tertiary)
  - Text colors (primary, secondary, tertiary)
  - Status colors (success, warning, error, info)
  - VPN state colors (connected, connecting, disconnected)
  - Card and border colors

- **Fonts namespace** - Typography system
  - Title fonts (large, medium, small)
  - Body fonts (regular, medium, small)
  - Caption and footnote styles
  - Consistent font weights and sizes

##### Protocol Extensions (ServiceProtocols.swift)
- **LatencyServiceProtocol** - Latency measurement interface
  - Single server latency measurement
  - Batch latency testing
  - Host/port-based latency measurement

- **FavoritesManagerProtocol** - Favorites management interface
  - Combine publisher for reactive updates
  - CRUD operations for favorites
  - Batch retrieval methods

- **RecentServersManagerProtocol** - Recent servers interface
  - Combine publisher for reactive updates
  - Add/remove/clear operations
  - Recent server retrieval methods

#### Changed

##### ServerListViewModel.swift - Major Enhancements
- Integrated LatencyService for real-time latency testing
- Integrated FavoritesManager for favorites management
- Integrated RecentServersManager for connection history
- Added `serverLatencies` published property for latency display
- Added `favoriteServers` published property for favorites section
- Added `recentServers` published property for recent connections section
- Added `isTestingLatency` state for latency testing UI feedback
- Added `sortOption`, `sortAscending`, `filterOptions` for advanced filtering
- Implemented `testLatency()` method for batch latency testing
- Implemented `selectQuickConnectServer()` with smart algorithm:
  - Latency weight: 40%
  - Load weight: 30%
  - Online status weight: 20%
  - Premium status weight: 10%
  - Automatic fallback to first available server
- Implemented `applyFiltersAndSort()` for dynamic filtering and sorting
- Added Combine subscriptions for reactive favorites and recent servers updates
- Enhanced error handling with user-friendly messages
- Added automatic latency testing on server list load

##### ServerListView.swift - UI Overhaul
- Added "Quick Connect" button at top with smart server selection
- Added "Favorites" section with horizontal scroll
- Added "Recent" section with horizontal scroll
- Added "All Servers" section with country grouping
- Integrated FilterSheet with filter/sort button
- Added latency testing button with loading state
- Enhanced search functionality
- Improved empty states for favorites and recent sections
- Added pull-to-refresh for server list
- Improved navigation with ServerDetailView integration
- Enhanced visual hierarchy with sections and dividers

#### Technical Improvements
- Implemented proper dependency injection for all new services
- Added comprehensive logging throughout new services
- Implemented thread-safe operations in LatencyService
- Used Combine for reactive state management
- Proper error handling and user feedback
- Efficient caching strategies (60s for latency, persistent for favorites/recent)
- Memory-efficient batch operations with TaskGroup
- Proper UserDefaults key namespacing
- Clean separation of concerns with protocols

#### Performance Optimizations
- Concurrent latency testing with Swift Concurrency
- Result caching to minimize redundant network calls
- Efficient filtering and sorting algorithms
- Lazy loading of server details
- Optimized UI updates with @Published properties

---

### Phase 2.1 - Core VPN Connection (2024-12-06)

#### Added
- **WireGuardManager.swift** - WireGuard VPN tunnel management
  - NetworkExtension integration for VPN tunnel control
  - Automatic tunnel configuration generation
  - Connection state monitoring with Combine publishers
  - Reconnection logic with exponential backoff
  - Comprehensive error handling and logging

- **VPNService.swift** - High-level VPN service orchestration
  - Connect/disconnect operations
  - Server selection and configuration
  - Connection state management
  - Integration with WireGuardManager and KillSwitchManager
  - User-friendly error messages

- **KillSwitchManager.swift** - Network kill switch implementation
  - Automatic network blocking on VPN disconnect
  - NEOnDemandRule-based implementation
  - Configurable enable/disable functionality
  - Integration with VPN connection lifecycle

- **ConnectionLogger.swift** - Centralized logging system
  - Multiple log levels (debug, info, warning, error)
  - File-based log persistence
  - Console output for debugging
  - Log rotation and size management

- **ConnectionView.swift** - Main VPN connection interface
  - Large connect/disconnect button with animations
  - Real-time connection status display
  - Server information display
  - Connection timer
  - Data transfer statistics
  - Quick server change functionality

- **ConnectionViewModel.swift** - Connection screen business logic
  - VPN service integration
  - Connection state management
  - Timer management for connection duration
  - Data transfer tracking
  - Error handling and user feedback

#### Changed
- Enhanced VPNError enum with more specific error cases
- Updated ConnectionState enum with detailed substates
- Improved error messages for better user experience

---

### Phase 1 - App Foundation (2024-12-05)

#### Added

##### Architecture & Project Structure
- Clean Architecture implementation with clear layer separation
- MVVM-C (Model-View-ViewModel-Coordinator) pattern
- SwiftUI-based UI layer
- Combine framework for reactive programming
- Dependency injection support

##### Core Models
- **User.swift** - User account model with subscription info
- **VPNServer.swift** - VPN server model with location and features
- **VPNConfig.swift** - WireGuard configuration model
- **ConnectionState.swift** - VPN connection state enum
- **VPNError.swift** - Comprehensive error handling
- **Subscription.swift** - User subscription model

##### Core Services
- **APIClient.swift** - RESTful API client with async/await
  - JWT token management
  - Automatic token refresh
  - Request/response logging
  - Error handling and retry logic
  - Multipart form data support

- **KeychainService.swift** - Secure credential storage
  - JWT token storage
  - User credentials management
  - Secure enclave integration
  - Error handling

- **ServerService.swift** - Server management
  - Server list fetching and caching
  - Server search and filtering
  - Optimal server selection

##### Features - Authentication
- **AuthView.swift** - Login/register interface
- **AuthViewModel.swift** - Authentication business logic
- **AuthService.swift** - Authentication API integration
- Email/password authentication
- Form validation
- Error handling and user feedback

##### Features - Server List
- **ServerListView.swift** - Server browsing interface
- **ServerListViewModel.swift** - Server list business logic
- Search functionality
- Country grouping
- Server cards with details

##### Features - Settings
- **SettingsView.swift** - App settings interface
- **SettingsViewModel.swift** - Settings business logic
- User profile display
- Subscription information
- Kill switch toggle
- Auto-reconnect toggle
- Logout functionality

##### UI System
- **Theme.swift** - Centralized design system
  - Color palette
  - Typography
  - Spacing and sizing constants
  - Consistent styling

- **Components.swift** - Reusable UI components
  - PrimaryButton
  - SecondaryButton
  - TextFieldStyle
  - ServerCard
  - LoadingView
  - ErrorView

##### App Infrastructure
- **CasperVPNApp.swift** - App entry point
- **AppCoordinator.swift** - Navigation coordination
- **AppDelegate.swift** - App lifecycle management
- **Config.swift** - Environment configuration

##### Network Extension
- **PacketTunnelProvider.swift** - VPN tunnel provider
- **TunnelConfiguration.swift** - Tunnel configuration management

##### Extensions
- **Extensions.swift** - Swift standard library extensions
  - Date formatting
  - String validation
  - Color extensions
  - View modifiers

#### Project Configuration
- Xcode project setup with proper targets
- Main app target: CasperVPN
- Network extension target: CasperVPNTunnel
- Proper entitlements configuration
- Info.plist configuration
- Build settings optimization

---

## Project History

### Sprint 2 - React Admin Panel (2024-12-04)
- Complete React-based admin dashboard
- User management interface
- Server management interface
- Analytics and monitoring
- Material-UI integration

### Sprint 1 - Backend API (2024-12-03)
- 60+ RESTful API endpoints
- JWT authentication
- User management
- Server management
- Subscription management
- Payment processing (Stripe)
- Admin panel APIs
- Comprehensive validation and error handling

### Phase 1 - DevOps Infrastructure (2024-12-02)
- Docker containerization
- Docker Compose orchestration
- Nginx reverse proxy
- SSL/TLS configuration
- CI/CD pipeline setup
- Production deployment configuration

---

## Notes

### Version Numbering
- Phase numbers correspond to major feature sets
- Each phase may have multiple sub-versions (e.g., 2.1, 2.2)
- Production releases will follow semantic versioning (1.0.0, 1.1.0, etc.)

### Development Status
- ✅ Completed and merged
- 🟡 Completed but pending review/merge
- 🔄 In progress
- ⏳ Planned

### Next Steps
- Phase 2.3: Subscription management and in-app purchases
- Phase 3: Testing and quality assurance
- Phase 4: App Store preparation and submission
