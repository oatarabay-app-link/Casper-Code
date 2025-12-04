// //services/secure_storage_service.dart:
// import 'dart:math';
// import 'package:flutter_secure_storage/flutter_secure_storage.dart';

// class SecureStorageService {
//   static const _storage = FlutterSecureStorage();

//   static const String _privateKeyKey = "client_private_key";

//   /// Save private key securely
//   static Future<void> savePrivateKey(String privateKey) async {
//     print("+++++++++++++++++++Saving private key to secure storage");
//     await _storage.write(key: _privateKeyKey, value: privateKey);
//   }

//   /// Read private key
//   static Future<String?> getPrivateKey() async {
//     return await _storage.read(key: _privateKeyKey);
//   }

//   /// Delete private key
//   static Future<void> deletePrivateKey() async {
//     await _storage.delete(key: _privateKeyKey);
//   }
// }

// lib/Services/secure_storage_service.dart

import 'package:flutter_secure_storage/flutter_secure_storage.dart';

class SecureStorageService {
  static const _storage = FlutterSecureStorage();

  /// Keys to store VPN related values
  static const String _privateKeyKey = "client_private_key";
  static const String _publicKeyKey = "client_public_key";
  static const String _serverConfigKey = "server_config";
  static const String _allowIpsKey = "allow_ips";

  /// Save private key
  static Future<void> savePrivateKey(String privateKey) async {
    print("++++++++ Saving private key");
    await _storage.write(key: _privateKeyKey, value: privateKey);
  }

  /// Get private key
  static Future<String?> getPrivateKey() async {
    return await _storage.read(key: _privateKeyKey);
  }

  /// Delete private key
  static Future<void> deletePrivateKey() async {
    await _storage.delete(key: _privateKeyKey);
  }

  /// Save public key
  static Future<void> savePublicKey(String publicKey) async {
    await _storage.write(key: _publicKeyKey, value: publicKey);
  }

  /// Save allowIps
  static Future<void> saveAllowIps(String allowIps) async {
    await _storage.write(key: _allowIpsKey, value: allowIps);
  }

  /// Delete EVERYTHING related to VPN
  static Future<void> clearAll() async {
    print("++++++++ Clearing all VPN keys from secure storage");
    await _storage.deleteAll();
  }
}
