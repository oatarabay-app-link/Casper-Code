package com.m7slabs.casper

import android.app.Activity
import android.content.Intent
import android.net.VpnService
import android.os.Build
import androidx.annotation.NonNull
import io.flutter.embedding.android.FlutterActivity
import io.flutter.embedding.engine.FlutterEngine
import io.flutter.plugin.common.MethodCall
import io.flutter.plugin.common.MethodChannel
import com.m7slabs.casper.vpn.WireGuardVpnService

class MainActivity : FlutterActivity() {

  companion object {
    private const val REQUEST_VPN_PERMISSION = 1001
  }

  private val channelName = "com.m7slabs.casper/vpn"

  private var pendingPermissionResult: MethodChannel.Result? = null
  private var pendingConfig: String? = null
  private var methodChannel: MethodChannel? = null

  override fun configureFlutterEngine(@NonNull flutterEngine: FlutterEngine) {
    super.configureFlutterEngine(flutterEngine)

    methodChannel = MethodChannel(flutterEngine.dartExecutor.binaryMessenger, channelName)
      .apply {
        setMethodCallHandler { call: MethodCall, result: MethodChannel.Result ->
        when (call.method) {
          "startVpn" -> {
            val config = call.argument<String>("config")
            startVpn(config, result)
          }
          "stopVpn" -> stopVpn(result)
          "isConnected" -> result.success(WireGuardVpnService.isRunning)
            "getStatus" -> result.success(WireGuardVpnService.currentStatus)
          "getState" -> result.success(
            mapOf(
              "connected" to WireGuardVpnService.isRunning,
              "status" to WireGuardVpnService.currentStatus,
              "error" to WireGuardVpnService.lastError
            )
          )
          "getLastError" -> result.success(WireGuardVpnService.lastError)
          else -> result.notImplemented()
        }
      }
      }
  }

  override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
    super.onActivityResult(requestCode, resultCode, data)
    if (requestCode != REQUEST_VPN_PERMISSION) {
      return
    }

    val result = pendingPermissionResult
    val config = pendingConfig
    pendingPermissionResult = null
    pendingConfig = null

    if (result == null) {
      return
    }

    if (resultCode == Activity.RESULT_OK) {
      startVpnService(config, result)
    } else {
      result.error("VPN_PERMISSION_DENIED", "User denied VPN permission.", null)
    }
  }

  private fun startVpn(config: String?, result: MethodChannel.Result) {
    if (WireGuardVpnService.isRunning) {
      result.success(true)
      return
    }

    val prepareIntent = VpnService.prepare(this)
    if (prepareIntent != null) {
      if (pendingPermissionResult != null) {
        result.error("VPN_PERMISSION_IN_PROGRESS", "VPN permission dialog already shown.", null)
        return
      }
      pendingPermissionResult = result
      pendingConfig = config
      startActivityForResult(prepareIntent, REQUEST_VPN_PERMISSION)
      return
    }

    startVpnService(config, result)
  }

private fun startVpnService(config: String?, result: MethodChannel.Result) {
    WireGuardVpnService.lastError = null
    val serviceIntent = Intent(this, WireGuardVpnService::class.java).apply {
        action = WireGuardVpnService.ACTION_START
        putExtra(WireGuardVpnService.EXTRA_CONFIG, config)
    }

    try {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            startForegroundService(serviceIntent)
        } else {
            @Suppress("DEPRECATION")
            startService(serviceIntent)
        }
        result.success(true)
    } catch (e: Exception) {
        result.error("SERVICE_START_FAILED", "Failed to start VPN service: ${e.message}", null)
    }
}
  private fun stopVpn(result: MethodChannel.Result) {
    val intent = Intent(this, WireGuardVpnService::class.java).apply {
      action = WireGuardVpnService.ACTION_STOP
    }

    if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
      startForegroundService(intent)
    } else {
      @Suppress("DEPRECATION")
      startService(intent)
    }

    result.success(true)
  }
}
