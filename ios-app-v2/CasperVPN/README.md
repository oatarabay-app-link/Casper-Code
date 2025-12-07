# CasperVPN iOS App

A modern VPN client for iOS built with SwiftUI and Network Extension framework.

## 🚀 Features

- **WireGuard Protocol Support**: Fast and secure VPN connections using WireGuard
- **Modern UI**: Clean, intuitive interface built with SwiftUI
- **Server Selection**: Browse and connect to servers worldwide
- **Quick Connect**: One-tap connection to the best available server
- **Kill Switch**: Protect your data if the VPN disconnects
- **Auto-Connect**: Automatically connect when the app launches

## 📋 Requirements

- iOS 16.0+
- Xcode 15.0+
- Swift 5.9+
- Apple Developer Account (for Network Extension entitlements)

## 🏗️ Architecture

The app follows the **MVVM-C (Model-View-ViewModel-Coordinator)** architecture pattern:

```
CasperVPN/
├── App/                      # App entry point and configuration
│   ├── CasperVPNApp.swift    # SwiftUI app entry
│   ├── AppDelegate.swift     # App lifecycle & push notifications
│   └── Config.swift          # App configuration & constants
├── Core/
│   ├── Models/               # Data models
│   │   ├── User.swift
│   │   ├── VPNServer.swift
│   │   ├── VPNConfig.swift
│   │   └── Subscription.swift
│   ├── Services/             # Business logic services
│   │   ├── APIClient.swift
│   │   ├── AuthService.swift
│   │   ├── VPNService.swift
│   │   ├── KeychainService.swift
│   │   └── ServerService.swift
│   ├── Protocols/            # Protocol definitions
│   │   ├── ServiceProtocols.swift
│   │   └── ViewModelProtocol.swift
│   └── Extensions/           # Swift extensions
│       └── Extensions.swift
├── Features/                 # Feature modules
│   ├── Auth/
│   │   ├── LoginView.swift
│   │   └── AuthViewModel.swift
│   ├── ServerList/
│   │   ├── ServerListView.swift
│   │   └── ServerListViewModel.swift
│   ├── Connection/
│   │   ├── ConnectionView.swift
│   │   └── ConnectionViewModel.swift
│   └── Settings/
│       ├── SettingsView.swift
│       └── SettingsViewModel.swift
├── UI/
│   ├── Components/           # Reusable UI components
│   │   └── Components.swift
│   └── Theme/                # Colors, fonts, styling
│       └── Theme.swift
└── Resources/                # Assets, localization
```

### Network Extension (CasperVPNTunnel)

```
CasperVPNTunnel/
├── PacketTunnelProvider.swift   # Main tunnel provider
├── TunnelConfiguration.swift    # WireGuard config parsing
└── Info.plist                   # Extension configuration
```

## 🔧 Setup Instructions

### 1. Clone the Repository

```bash
git clone https://github.com/oatarabay-app-link/Casper-Code.git
cd Casper-Code/ios-app-v2/CasperVPN
```

### 2. Open in Xcode

```bash
open CasperVPN.xcodeproj
```

### 3. Configure Signing

1. Select the `CasperVPN` target
2. Go to "Signing & Capabilities"
3. Select your Development Team
4. Update the Bundle Identifier if needed

5. Repeat for the `CasperVPNTunnel` target

### 4. Configure Entitlements

The app requires the following entitlements:

**CasperVPN (Main App):**
- App Groups: `group.com.caspervpn.shared`
- Keychain Sharing: `com.caspervpn.keychain`
- Network Extensions

**CasperVPNTunnel (Extension):**
- App Groups: `group.com.caspervpn.shared`
- Keychain Sharing: `com.caspervpn.keychain`
- Network Extensions (Packet Tunnel Provider)

### 5. Update Configuration

Edit `CasperVPN/App/Config.swift` to configure:
- API base URL
- Bundle identifiers
- App group identifier

### 6. Build and Run

1. Select your target device (physical device required for VPN testing)
2. Build and run (⌘R)

## 🔐 Network Extension Setup

The Network Extension is required for VPN functionality. To enable it:

1. **Apple Developer Portal:**
   - Go to Certificates, Identifiers & Profiles
   - Create an App ID for the main app
   - Create an App ID for the tunnel extension (suffix: `.tunnel`)
   - Enable "Network Extensions" capability for both

2. **Xcode Capabilities:**
   - Add "Network Extensions" capability
   - Enable "Packet Tunnel Provider"

## 📱 API Integration

The app communicates with the CasperVPN backend API:

**Base URLs:**
- Development: `http://localhost:5000`
- Production: `https://api.caspervpn.com`

**Authentication:**
- JWT tokens stored securely in Keychain
- Automatic token refresh on expiration

**Main Endpoints:**
- `POST /api/auth/login` - User login
- `POST /api/auth/register` - User registration
- `GET /api/servers` - Get server list
- `GET /api/servers/{id}/config` - Get VPN configuration
- `POST /api/servers/{id}/connect` - Log connection
- `POST /api/servers/{id}/disconnect` - Log disconnection

## 🎨 Theming

The app uses a centralized theme system defined in `Theme.swift`:

```swift
// Colors
Theme.Colors.primary
Theme.Colors.success
Theme.Colors.background

// Fonts
Theme.Fonts.headline
Theme.Fonts.body

// Spacing
Theme.Spacing.md
Theme.Spacing.lg
```

## 🧪 Testing

### Unit Tests

```bash
xcodebuild test -scheme CasperVPN -destination 'platform=iOS Simulator,name=iPhone 15'
```

### UI Tests

```bash
xcodebuild test -scheme CasperVPNUITests -destination 'platform=iOS Simulator,name=iPhone 15'
```

## 📦 Dependencies

The app is built with minimal external dependencies:

- **SwiftUI** - UI framework
- **Combine** - Reactive programming
- **NetworkExtension** - VPN functionality
- **Security** - Keychain access

## 🔒 Security Considerations

1. **Keychain Storage**: All sensitive data (tokens, VPN credentials) stored in Keychain
2. **Certificate Pinning**: Consider implementing for production
3. **No Logging of Sensitive Data**: Private keys never logged
4. **Secure Communication**: HTTPS for all API calls

## 📄 License

Copyright © 2024 CasperVPN. All rights reserved.

This is proprietary software. Unauthorized copying, modification, or distribution is prohibited.

## 👥 Contributing

This is a private repository. For contribution guidelines, contact the development team.

## 📞 Support

- Email: support@caspervpn.com
- Help Center: https://help.caspervpn.com
