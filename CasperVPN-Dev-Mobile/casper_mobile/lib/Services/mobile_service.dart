// lib/Services/mobile_service.dart
import 'dart:convert';
import 'dart:developer';
import 'package:http/http.dart' as http;
import '../Constants/ApiConstants.dart';
import '../Models/VPNConnectionModel.dart';

class MobileService {
  final http.Client client;

  MobileService({http.Client? client}) : client = client ?? http.Client();

  Future<VPNConnectionModel> createConnection({
    required String clientPublicKey,
    required String deviceName,
    String allowedIPs = '',
    String description = '',
  }) async {
    final uri = Uri.parse(ApiConstants.baseUrl + ApiConstants.createConnection);

    final body = {
      "clientPublicKey": clientPublicKey,
      "deviceName": deviceName,
      "allowedIPs": allowedIPs,
      "description": description,
    };

    final headers = {'Content-Type': 'application/json'};

    log("🌐 REQUEST URL → $uri");
    log("📦 REQUEST BODY → ${jsonEncode(body)}");

    final resp = await client
        .post(uri, headers: headers, body: jsonEncode(body))
        .timeout(const Duration(seconds: 20));

    log("🔁 RESPONSE STATUS: ${resp.statusCode}");
    log("🔁 RESPONSE BODY: ${resp.body}");

    if (resp.statusCode >= 200 && resp.statusCode < 300) {
      return VPNConnectionModel.fromJson(jsonDecode(resp.body));
    } else {
      throw Exception("HTTP ${resp.statusCode}: ${resp.body}");
    }
  }
}
