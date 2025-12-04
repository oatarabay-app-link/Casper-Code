//lib/Models/VPNConnectionModel.dart:
class VPNConnectionModel {
  final String clientPublicKey;
  final String serverPublicKey;
  final String serverEndpoint;
  final String allowIps; // NEW FIELD

  VPNConnectionModel({
    required this.clientPublicKey,
    required this.serverPublicKey,
    required this.serverEndpoint,
    required this.allowIps,
  });

  factory VPNConnectionModel.fromJson(Map<String, dynamic> json) {
    return VPNConnectionModel(
      clientPublicKey: json['clientPublicKey'] as String,
      serverPublicKey: json['serverPublicKey'] as String,
      serverEndpoint: json['serverEndpoint'] as String,
      allowIps: json['allowIps'] as String, // NEW FIELD
    );
  }
}
