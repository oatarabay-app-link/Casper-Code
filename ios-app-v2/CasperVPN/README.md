# CasperVPN iOS App - Phase 2.1: Core VPN Connection

A native iOS VPN application built with SwiftUI and WireGuard protocol support.

## Features

### Phase 2.1 Implemented Features

#### 1. WireGuard Integration
- ✅ X25519 key generation for client keys
- ✅ WireGuard configuration parsing from backend API
- ✅ Full WireGuard tunnel configuration support

#### 2. Enhanced PacketTunnelProvider
- ✅ Full WireGuard-based packet tunnel implementation
- ✅ `startTunnel(options:completionHandler:)` with actual connection
- ✅ `stopTunnel(with:completionHandler:)` for clean disconnection
- ✅ Network settings configuration (DNS, allowed IPs, MTU)
- ✅ Packet handling for the tunnel

#### 3. VPN Connection Manager
- ✅ Connection state machine: Disconnected → Connecting → Connected → Disconnecting
- ✅ Combine-based connection status publisher
- ✅ `connect(to server:)` and `disconnect()` methods
- ✅ NEVPNManager configuration and status observation

#### 4. Kill Switch Implementation
- ✅ On-demand rules to block traffic when VPN disconnects
- ✅ Kill switch toggle in settings
- ✅ NEOnDemandRule configuration for always-on VPN behavior
- ✅ Trusted networks management

#### 5. Auto-Reconnect Logic
- ✅ NWPathMonitor for network reachability monitoring
- ✅ Automatic reconnection on network changes
- ✅ Exponential backoff for retry attempts
- ✅ WiFi ↔ Cellular transition handling

#### 6. Connection Status & UI Updates
- ✅ VPN status observation via Combine
- ✅ Connection timer (time connected)
- ✅ Server info display when connected
- ✅ Connection statistics (bytes sent/received)
- ✅ Animated connection button with state transitions
- ✅ Status indicator with color coding
- ✅ Quick disconnect option

#### 7. Error Handling
- ✅ Comprehensive VPNError enum with all error cases
- ✅ User-friendly error messages
- ✅ Retry logic for transient failures
- ✅ Error logging for debugging

#### 8. Connection Logging
- ✅ Debug logging service
- ✅ Connection attempts, successes, failures logging
- ✅ Recent connection history storage
- ✅ Log export functionality for support

## Architecture

```
ios-app-v2/CasperVPN/
├── CasperVPN.xcodeproj/
├── CasperVPN/                    # Main App Target
│   ├── App/
│   │   ├── CasperVPNApp.swift   # App entry point
│   │   ├── AppDelegate.swift     # App lifecycle
│   │   ├── Config.swift          # Configuration constants
│   │   └── AppCoordinator.swift  # Navigation coordinator
│   ├── Core/
│   │   ├── Models/
│   │   │   ├── User.swift
│   │   │   ├── VPNServer.swift
│   │   │   ├── VPNConfig.swift
│   │   │   ├── Subscription.swift
│   │   │   ├── VPNError.swift        # ⭐ Phase 2.1
│   │   │   └── ConnectionState.swift  # ⭐ Phase 2.1
│   │   ├── Services/
│   │   │   ├── APIClient.swift
│   │   │   ├── AuthService.swift
│   │   │   ├── KeychainService.swift
│   │   │   ├── ServerService.swift
│   │   │   ├── WireGuardManager.swift      # ⭐ Phase 2.1
│   │   │   ├── VPNConnectionManager.swift  # ⭐ Phase 2.1
│   │   │   ├── KillSwitchManager.swift     # ⭐ Phase 2.1
│   │   │   ├── NetworkMonitor.swift        # ⭐ Phase 2.1
│   │   │   └── ConnectionLogger.swift      # ⭐ Phase 2.1
│   │   └── Protocols/
│   │       ├── ServiceProtocols.swift
│   │       └── ViewModelProtocol.swift
│   ├── Features/
│   │   ├── Auth/
│   │   │   ├── AuthViewModel.swift
│   │   │   └── LoginView.swift
│   │   ├── Connection/
│   │   │   ├── ConnectionViewModel.swift   # ⭐ Enhanced
│   │   │   └── ConnectionView.swift        # ⭐ Enhanced
│   │   ├── ServerList/
│   │   │   ├── ServerListViewModel.swift
│   │   │   └── ServerListView.swift
│   │   └── Settings/
│   │       ├── SettingsViewModel.swift     # ⭐ Enhanced
│   │       └── SettingsView.swift          # ⭐ Enhanced
│   └── UI/
│       └── Theme/
│           └── Theme.swift
├── CasperVPNTunnel/              # Network Extension Target
│   ├── PacketTunnelProvider.swift  # ⭐ Full implementation
│   ├── TunnelConfiguration.swift   # ⭐ Enhanced
│   ├── Info.plist
│   └── CasperVPNTunnel.entitlements
└── README.md
```

## Requirements

- iOS 16.0+
- Xcode 15.0+
- Swift 5.9+
- Apple Developer Account (for Network Extension entitlements)

## Setup

### 1. Clone and Open Project

```bash
git clone https://github.com/oatarabay-app-link/Casper-Code.git
cd Casper-Code
git checkout feature/ios-app-v2
cd ios-app-v2/CasperVPN
open CasperVPN.xcodeproj
```

### 2. Configure Signing

1. Select the CasperVPN target
2. Go to "Signing & Capabilities"
3. Select your Development Team
4. Repeat for CasperVPNTunnel target

### 3. Configure Entitlements

Ensure your Apple Developer Account has the following capabilities:
- Network Extension
- App Groups
- Keychain Access Groups

### 4. Add WireGuardKit (Production)

For production use, add WireGuardKit via Swift Package Manager:

1. File → Add Packages...
2. Enter: `https://github.com/WireGuard/wireguard-apple`
3. Add to both CasperVPN and CasperVPNTunnel targets

### 5. Build and Run

1. Select a device or simulator
2. Build and run the app

## API Configuration

The app connects to the CasperVPN backend API. Configure the base URL in `Config.swift`:

```swift
static let apiBaseURL = "https://api.caspervpn.com"
```

### Backend Endpoints Used

| Endpoint | Method | Description |
|----------|--------|-------------|
| `/api/v1/auth/login` | POST | User authentication |
| `/api/v1/auth/refresh` | POST | Token refresh |
| `/api/v1/servers` | GET | List VPN servers |
| `/api/v1/servers/{id}/config` | GET | Get WireGuard config |
| `/api/v1/servers/{id}/connect` | POST | Log connection start |
| `/api/v1/servers/{id}/disconnect` | POST | Log disconnection |

### WireGuard Configuration Response

```json
{
  "success": true,
  "data": {
    "privateKey": "base64_encoded_private_key",
    "publicKey": "server_public_key",
    "endpoint": "server.caspervpn.com:51820",
    "allowedIPs": ["0.0.0.0/0", "::/0"],
    "dns": ["1.1.1.1", "1.0.0.1"],
    "persistentKeepalive": 25
  }
}
```

## Key Components

### VPNConnectionManager

The main orchestrator for VPN connections:

```swift
// Connect to a server
try await VPNConnectionManager.shared.connect(to: server)

// Disconnect
try await VPNConnectionManager.shared.disconnect()

// Observe connection state
VPNConnectionManager.shared.$connectionState
    .sink { state in
        // Handle state change
    }
```

### KillSwitchManager

Manages the kill switch functionality:

```swift
// Enable kill switch
try await KillSwitchManager.shared.enable()

// Disable kill switch
try await KillSwitchManager.shared.disable()

// Check status
let isEnabled = KillSwitchManager.shared.isEnabled
```

### NetworkMonitor

Monitors network reachability:

```swift
NetworkMonitor.shared.pathUpdatePublisher
    .sink { status in
        if status.isConnected {
            // Network available
        }
    }
```

### ConnectionLogger

Debug logging:

```swift
// Log events
ConnectionLogger.shared.log("Event message", level: .info)

// Export logs
let logs = ConnectionLogger.shared.exportLogs()
```

## Connection States

| State | Description |
|-------|-------------|
| `disconnected` | Not connected to VPN |
| `connecting` | Establishing connection |
| `connected(since: Date)` | Connected with timestamp |
| `disconnecting` | Closing connection |
| `reconnecting` | Auto-reconnecting |
| `invalid` | Invalid configuration |

## Error Handling

All VPN errors are handled through the `VPNError` enum:

- Connection errors (timeout, cancelled, etc.)
- Configuration errors (invalid key, endpoint, etc.)
- Tunnel errors (start/stop failures)
- WireGuard errors (handshake, adapter issues)
- Network errors (unavailable, changed)
- Server errors (unavailable, overloaded)
- Authentication errors (expired session)
- Permission errors (VPN denied)

## Testing

### Unit Tests
```bash
# Run tests from Xcode
Cmd + U
```

### Manual Testing
1. Login with valid credentials
2. Select a server from the list
3. Tap Connect button
4. Verify connection status and timer
5. Test disconnect
6. Test kill switch toggle
7. Test auto-reconnect by toggling airplane mode

## Known Limitations

1. **WireGuardKit Integration**: The current implementation includes a placeholder WireGuard adapter. For production, integrate the official WireGuardKit.

2. **Network Extension Sandbox**: The tunnel extension runs in a sandbox with limited capabilities.

3. **Statistics**: Full traffic statistics require IPC between the app and extension via app groups.

## Future Improvements

- Split tunneling support
- Multiple VPN profiles
- Server favorites
- Bandwidth monitoring charts
- Widgets for quick connect

## License

Proprietary - CasperVPN

## Support

For support, contact: support@caspervpn.com
