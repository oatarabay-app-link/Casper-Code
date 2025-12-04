// lib/Utils/crypto_utils.dart
import 'dart:convert';
import 'package:cryptography/cryptography.dart';

class CryptoUtils {
  /// Generates an X25519 key pair and returns base64-encoded privateKey & publicKey.
  static Future<Map<String, String>> generateX25519KeypairBase64() async {
    final algorithm = X25519();
    final keyPair = await algorithm.newKeyPair();

    // Export public key
    final publicKey = await keyPair.extractPublicKey();
    final pubB64 = base64Encode(publicKey.bytes);

    // Export private key
    final kpData = await keyPair.extract();
    final privB64 = base64Encode(kpData.bytes);

    return {'privateKey': privB64, 'publicKey': pubB64};
  }

  /// Derives the public key from a Base64-encoded private key
  static Future<String> getPublicKeyFromPrivateKey(String privateKeyBase64) async {
    final privateBytes = base64Decode(privateKeyBase64);
    final algorithm = X25519();

    // Recreate key pair from private key
    final keyPair = await algorithm.newKeyPairFromSeed(privateBytes);
    final publicKey = await keyPair.extractPublicKey();
    return base64Encode(publicKey.bytes);
  }
}
