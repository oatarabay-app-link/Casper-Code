// // lib/Services/VPNService.dart:
// import 'package:flutter/services.dart';

// class VPNService {
//   VPNService._internal();

//   static final VPNService instance = VPNService._internal();

//   static const String _channelName = 'com.m7slabs.casper/vpn';
//   static const MethodChannel _channel = MethodChannel(_channelName);

//   static const String _defaultConfig = '''
// [Interface]
// PrivateKey = WA3/IlsuNPrpJ8SiufdwtMrAuabSkR3I5zBEMBhSTms=
// Address = 192.168.30.2/24
// DNS = 1.1.1.1

// [Peer]
// PublicKey = qGvV+DMxgstQzrXOjSfOG80z2Z3RtnDBGmK8CA1YIGQ=
// AllowedIPs = 0.0.0.0/0
// Endpoint = 51.178.85.84:51820
// PersistentKeepalive = 25
// ''';

//   String _config = _defaultConfig;
//   bool _isConnected = false;
//   bool _isBusy = false;
//   bool _permissionRequired = false;
//   String? _lastError;

//   String get config => _config;
//   bool get isConnected => _isConnected;
//   bool get isBusy => _isBusy;
//   bool get permissionRequired => _permissionRequired;
//   String? get lastError => _lastError;
//   String _currentStatus = 'DISCONNECTED';
//   String get currentStatus => _currentStatus;
//   bool get hasFailure => _currentStatus == 'FAILED';

//   void updateConfig(String config) {
//     if (config.trim().isEmpty) {
//       return;
//     }
//     _config = config.trim();
//   }

//   Future<bool> connect() async {
//     if (_isConnected || _isBusy) {
//       return _isConnected;
//     }

//     _setBusy(true);
//     try {
//       await _channel.invokeMethod('startVpn', {'config': _config});
//       await _postCommandSync();
//       _permissionRequired = false;

//       if (_currentStatus == 'FAILED') {
//         if (_lastError == null || _lastError!.isEmpty) {
//           _lastError =
//               'VPN tunnel negotiation failed. Check Android 14+ compatibility.';
//         }
//         return false;
//       }

//       return _isConnected || _currentStatus == 'CONNECTING';
//     } on PlatformException catch (error) {
//       _handlePlatformException(error);

//       // Handle Android 14+ specific errors
//       if (error.code.contains('foreground') || error.code.contains('type')) {
//         _lastError = 'Android 14 compatibility issue: ${error.message}';
//         _currentStatus = 'FAILED';
//       }

//       return false;
//     } finally {
//       _setBusy(false);
//     }
//   }

//   Future<bool> disconnect() async {
//     if (!_isConnected || _isBusy) {
//       return !_isConnected;
//     }

//     _setBusy(true);
//     try {
//       await _channel.invokeMethod('stopVpn');
//       await _postCommandSync();
//       _permissionRequired = false;
//       return !_isConnected;
//     } on PlatformException catch (error) {
//       _handlePlatformException(error);
//       return false;
//     } finally {
//       _setBusy(false);
//     }
//   }

//   Future<bool> syncStatus() async {
//     try {
//       final state = await _channel.invokeMapMethod<String, dynamic>('getState');
//       if (state != null) {
//         _isConnected = state['connected'] == true;
//         final status = state['status'] as String?;
//         if (status != null && status.isNotEmpty) {
//           _currentStatus = status;
//         }
//         final error = state['error'] as String?;
//         _lastError = error;
//       } else {
//         final result = await _channel.invokeMethod<bool>('isConnected');
//         final status = await _channel.invokeMethod<String>('getStatus');
//         _isConnected = result ?? false;
//         if (status != null) {
//           _currentStatus = status;
//         }
//       }
//       if (_isConnected && _currentStatus == 'CONNECTED') {
//         _lastError = null;
//       }
//       if (_currentStatus == 'FAILED' &&
//           (_lastError == null || _lastError!.isEmpty)) {
//         _lastError = 'VPN tunnel negotiation failed.';
//       }
//       return _isConnected;
//     } on PlatformException catch (error) {
//       _handlePlatformException(error);
//       return _isConnected;
//     }
//   }

//   Future<void> _postCommandSync() async {
//     await Future.delayed(const Duration(milliseconds: 150));
//     await syncStatus();
//   }

//   void clearPermissionFlag() {
//     _permissionRequired = false;
//   }

//   void _setBusy(bool value) {
//     _isBusy = value;
//   }

//   void _handlePlatformException(PlatformException error) {
//     _lastError = error.message ?? error.code;
//     _permissionRequired = error.code == 'VPN_PERMISSION_REQUIRED';
//     if (error.code == 'VPN_OPERATION_FAILED') {
//       _currentStatus = 'FAILED';
//     }
//   }
// }

// //lib/Services/VPNService.dart:
// import 'dart:developer';
// import 'package:casper_mobile/Services/secure_storage_service.dart';
// import 'package:flutter/services.dart';
// import 'mobile_service.dart';
// import 'secure_storage_service.dart';
// import '../Models/VPNConnectionModel.dart';
// import '../Utils/crypto_utils.dart';

// class VPNService {
//   VPNService._internal();
//   static final VPNService instance = VPNService._internal();

//   static const String _channelName = 'com.m7slabs.casper/vpn';
//   static const MethodChannel _channel = MethodChannel(_channelName);

//   final MobileService _mobileService = MobileService();

//   String _config = '';
//   bool _isConnected = false;
//   bool _isBusy = false;
//   bool _permissionRequired = false;
//   String? _lastError;

//   String get config => _config;
//   bool get isConnected => _isConnected;
//   bool get isBusy => _isBusy;
//   bool get permissionRequired => _permissionRequired;
//   String? get lastError => _lastError;

//   String _currentStatus = 'DISCONNECTED';
//   String get currentStatus => _currentStatus;

//   bool _initialized = false;
//   bool get isInitialized => _initialized;

//   Future<void> initialize({required String deviceName}) async {
//     if (_initialized) return;

//     _setBusy(true);
//     try {
//       log("+++++++++++++++++++Initializing VPN Service");
//       String? privateKey = await SecureStorageService.getPrivateKey();
//       String publicKey;

//       if (privateKey == null || privateKey.isEmpty) {
//         log("+++++++++++++++++++Generating new keypair in if block");
//         final keys = await CryptoUtils.generateX25519KeypairBase64();
//         privateKey = keys['privateKey']!;
//         publicKey = keys['publicKey']!;
//         await SecureStorageService.savePrivateKey(privateKey);
//       } else {
//         log("+++++++++++++++++++Using existing private key");
//         publicKey = await CryptoUtils.getPublicKeyFromPrivateKey(privateKey);
//       }

//       final VPNConnectionModel response = await _mobileService.createConnection(
//         clientPublicKey: publicKey,
//         deviceName: deviceName,
//         allowedIPs: '',
//         description: 'Created from Flutter VPNService',
//       );

//       _config =
//           '''
// [Interface]
// PrivateKey = $privateKey
// Address = ${response.allowIps}
// DNS = 1.1.1.1

// [Peer]
// PublicKey = ${response.serverPublicKey}
// AllowedIPs = 0.0.0.0/0
// Endpoint = ${response.serverEndpoint}
// PersistentKeepalive = 25
// ''';

//       _initialized = true;
//     } catch (e, st) {
//       _lastError = e.toString();
//       _initialized = false;
//       log("❌ VPNService initialize error", error: e, stackTrace: st);
//       rethrow;
//     } finally {
//       _setBusy(false);
//     }
//   }

//   void updateConfig(String config) {
//     if (config.trim().isNotEmpty) {
//       _config = config.trim();
//     }
//   }

//   Future<bool> connect() async {
//     if (!_initialized) {
//       _lastError = 'VPN configuration is empty. Call initialize() first.';
//       return false;
//     }

//     if (_isConnected || _isBusy) return _isConnected;

//     _setBusy(true);
//     try {
//       await _channel.invokeMethod('startVpn', {'config': _config});
//       await _postCommandSync();
//       _permissionRequired = false;

//       if (_currentStatus == 'FAILED') return false;
//       return _isConnected || _currentStatus == 'CONNECTING';
//     } on PlatformException catch (error) {
//       _handlePlatformException(error);
//       return false;
//     } finally {
//       _setBusy(false);
//     }
//   }

//   Future<bool> disconnect() async {
//     if (!_isConnected || _isBusy) return !_isConnected;

//     _setBusy(true);
//     try {
//       await _channel.invokeMethod('stopVpn');
//       await _postCommandSync();
//       return !_isConnected;
//     } catch (_) {
//       return false;
//     } finally {
//       log("+++++++++++++++++++Disconnecting VPN Service");
//         await SecureStorageService.deletePrivateKey();
//         log("+++++++++++++++++++Deleted private key from secure storage");
//       _setBusy(false);
//     }
//   }

//   Future<bool> syncStatus() async {
//     try {
//       final state = await _channel.invokeMapMethod<String, dynamic>('getState');
//       if (state != null) {
//         _isConnected = state['connected'] == true;
//         _currentStatus = state['status'] ?? _currentStatus;
//         _lastError = state['error'];
//       }
//       return _isConnected;
//     } catch (_) {
//       return false;
//     }
//   }

//   Future<void> _postCommandSync() async {
//     await Future.delayed(const Duration(milliseconds: 150));
//     await syncStatus();
//   }

//   void _handlePlatformException(PlatformException error) {
//     _lastError = error.message ?? error.code;
//     _permissionRequired = error.code == 'VPN_PERMISSION_REQUIRED';
//     if (error.code == 'VPN_OPERATION_FAILED') {
//       _currentStatus = 'FAILED';
//     }
//   }

//   void clearPermissionFlag() => _permissionRequired = false;

//   void _setBusy(bool value) {
//     _isBusy = value;
//   }
// }

// lib/Services/VPNService.dart:

import 'dart:developer';
import 'package:flutter/services.dart';
import 'package:casper_mobile/Services/secure_storage_service.dart';
import '../Models/VPNConnectionModel.dart';
import '../Utils/crypto_utils.dart';
import 'mobile_service.dart';

class VPNService {
  VPNService._internal();
  static final VPNService instance = VPNService._internal();

  static const String _channelName = 'com.m7slabs.casper/vpn';
  static const MethodChannel _channel = MethodChannel(_channelName);

  final MobileService _mobileService = MobileService();

  String _config = '';
  bool _isConnected = false;
  bool _isBusy = false;
  bool _permissionRequired = false;
  String? _lastError;

  String _currentStatus = 'DISCONNECTED';

  bool _initialized = false;

  String get config => _config;
  bool get isConnected => _isConnected;
  bool get isBusy => _isBusy;
  bool get permissionRequired => _permissionRequired;
  String? get lastError => _lastError;
  String get currentStatus => _currentStatus;
  bool get isInitialized => _initialized;

  // ------------------------
  // Initialize VPN instance
  // ------------------------
  Future<void> initialize({required String deviceName}) async {
    if (_initialized) {
      log("VPN already initialized");
      return;
    }

    _setBusy(true);
    try {
      log("++++++++ Initializing VPN Service");

      // Load existing private key
      String? privateKey = await SecureStorageService.getPrivateKey();
      String publicKey;

      if (privateKey == null || privateKey.isEmpty) {
        log("++++++++ No private key found → generating new keypair");

        final keys = await CryptoUtils.generateX25519KeypairBase64();
        privateKey = keys['privateKey']!;
        publicKey = keys['publicKey']!;

        await SecureStorageService.savePrivateKey(privateKey);
        await SecureStorageService.savePublicKey(publicKey);
      } else {
        log("++++++++ Using stored private key");
        publicKey = await CryptoUtils.getPublicKeyFromPrivateKey(privateKey);
      }

      // Create connection on backend
      final VPNConnectionModel response = await _mobileService.createConnection(
        clientPublicKey: publicKey,
        deviceName: deviceName,
        allowedIPs: '',
        description: 'Created from Flutter VPNService',
      );

      // Save allowIps to secure storage
      await SecureStorageService.saveAllowIps(response.allowIps);

      _config =
          '''
[Interface]
PrivateKey = $privateKey
Address = ${response.allowIps}
DNS = 1.1.1.1

[Peer]
PublicKey = ${response.serverPublicKey}
AllowedIPs = 0.0.0.0/0
Endpoint = ${response.serverEndpoint}
PersistentKeepalive = 25
''';

      _initialized = true;
    } catch (e, st) {
      _lastError = e.toString();
      _initialized = false;
      log("❌ initialize() error", error: e, stackTrace: st);
      rethrow;
    } finally {
      _setBusy(false);
    }
  }

  void updateConfig(String config) {
    if (config.trim().isNotEmpty) {
      _config = config.trim();
    }
  }

  // ------------------------
  // Connect
  // ------------------------
  Future<bool> connect() async {
    if (!_initialized) {
      _lastError = 'VPN configuration missing. Call initialize() first.';
      return false;
    }

    if (_isConnected || _isBusy) return _isConnected;

    _setBusy(true);
    try {
      await _channel.invokeMethod('startVpn', {'config': _config});
      await _postCommandSync();

      _permissionRequired = false;

      if (_currentStatus == 'FAILED') {
        return false;
      }

      return _isConnected || _currentStatus == 'CONNECTING';
    } catch (error) {
      if (error is PlatformException) _handlePlatformException(error);
      return false;
    } finally {
      _setBusy(false);
    }
  }

  // ------------------------
  // Disconnect (CLEAR ALL STORAGE)
  // ------------------------
  Future<bool> disconnect() async {
    if (!_isConnected || _isBusy) return !_isConnected;

    _setBusy(true);
    try {
      await _channel.invokeMethod('stopVpn');
      await _postCommandSync();

      return !_isConnected;
    } catch (_) {
      return false;
    } finally {
      log("++++++++ Disconnecting VPN → clearing secure storage");
      await SecureStorageService.clearAll(); // 🔥 DELETE EVERYTHING
      log("++++++++ Secure storage cleared successfully");
      _setBusy(false);
    }
  }

  // ------------------------
  // Sync VPN Status
  // ------------------------
  Future<bool> syncStatus() async {
    try {
      final state = await _channel.invokeMapMethod<String, dynamic>('getState');

      if (state != null) {
        _isConnected = state['connected'] == true;
        _currentStatus = state['status'] ?? _currentStatus;
        _lastError = state['error'];
      }

      return _isConnected;
    } catch (_) {
      return false;
    }
  }

  Future<void> _postCommandSync() async {
    await Future.delayed(const Duration(milliseconds: 150));
    await syncStatus();
  }

  void _handlePlatformException(PlatformException error) {
    _lastError = error.message ?? error.code;
    _permissionRequired = error.code == 'VPN_PERMISSION_REQUIRED';

    if (error.code == 'VPN_OPERATION_FAILED') {
      _currentStatus = 'FAILED';
    }
  }

  void clearPermissionFlag() => _permissionRequired = false;

  void _setBusy(bool value) {
    _isBusy = value;
  }
}
