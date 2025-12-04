//lib/Controllers/debug_controller.dart:
import 'dart:developer';
import '../Services/mobile_service.dart';
import '../Services/secure_storage_service.dart';
import '../Utils/crypto_utils.dart';
import '../ViewModels/DebugViewModel.dart';
import '../Resources/strings.dart';
import '../Models/VPNConnectionModel.dart';
import '../Services/VPNService.dart';

class DebugController {
  final DebugViewModel viewModel;
  final MobileService _mobileService;

  DebugController({required this.viewModel, MobileService? mobileService})
    : _mobileService = mobileService ?? MobileService();

  Future<void> registerVpnSilent({required String deviceName}) async {
    log(
      "++++++++++++++++++++++++++++ Debug controller runs ++++++++++++++++++++++++",
    );
    try {
      viewModel.isLoading.value = true;
      viewModel.status.value = 'Generating keys...';

      final keys = await CryptoUtils.generateX25519KeypairBase64();
      final privateKey = keys['privateKey']!;
      final publicKey = keys['publicKey']!;

      await SecureStorageService.savePrivateKey(privateKey);
      log("🔐 PRIVATE KEY STORED SECURELY");

      viewModel.status.value = 'Sending public key to server...';

      final VPNConnectionModel response = await _mobileService.createConnection(
        clientPublicKey: publicKey,
        deviceName: deviceName,
        allowedIPs: '',
        description: 'Created from Flutter debug screen',
      );

      log(
        "📥 SERVER RESPONSE → ${response.serverPublicKey}, ${response.serverEndpoint}, ${response.allowIps}",
      );

      // Build WireGuard config dynamically
      final config =
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

      VPNService.instance.updateConfig(config);
      viewModel.status.value = Strings.success;
    } catch (e, st) {
      viewModel.status.value = '${Strings.failure}: ${e.toString()}';
      log('registerVpnSilent error', error: e, stackTrace: st);
    } finally {
      viewModel.isLoading.value = false;
    }
  }
}
