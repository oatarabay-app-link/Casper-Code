package com.m7slabs.casper.vpn

import android.app.Notification
import android.app.NotificationChannel
import android.app.NotificationManager
import android.app.PendingIntent
import android.content.Context
import android.content.Intent
import android.net.ConnectivityManager
import android.net.Network
import android.net.NetworkCapabilities
import android.net.NetworkRequest
import android.net.VpnService
import android.os.Build
import android.os.ParcelFileDescriptor
import androidx.core.app.NotificationCompat
import com.m7slabs.casper.MainActivity
import android.content.pm.ServiceInfo // Add this import

/**
 * Placeholder VPN service that keeps the foreground service + notification plumbing ready
 * for the upcoming WireGuard integration.
 */
class WireGuardVpnService : VpnService() {

  companion object {
    const val ACTION_START = "com.m7slabs.casper.vpn.action.START"
    const val ACTION_STOP = "com.m7slabs.casper.vpn.action.STOP"
    const val EXTRA_CONFIG = "com.m7slabs.casper.vpn.extra.CONFIG"

  const val STATUS_DISCONNECTED = "DISCONNECTED"
  const val STATUS_CONNECTING = "CONNECTING"
  const val STATUS_CONNECTED = "CONNECTED"
  const val STATUS_FAILED = "FAILED"

    private const val CHANNEL_ID = "casper_vpn_channel"
    private const val CHANNEL_NAME = "CasperVPN Tunnel"
    private const val NOTIFICATION_ID = 1001

    @Volatile
    var isRunning: Boolean = false
      private set

    @Volatile
    var currentStatus: String = STATUS_DISCONNECTED
      internal set

    @Volatile
    var lastError: String? = null
      internal set

    private const val FALLBACK_CONFIG = """
[Interface]
PrivateKey = WA3/IlsuNPrpJ8SiufdwtMrAuabSkR3I5zBEMBhSTms=
Address = 192.168.30.2/24
DNS = 1.1.1.1

[Peer]
PublicKey = qGvV+DMxgstQzrXOjSfOG80z2Z3RtnDBGmK8CA1YIGQ=
AllowedIPs = 0.0.0.0/0
Endpoint = 51.178.85.84:51820
PersistentKeepalive = 25
"""
  }

  private var tunnelDescriptor: ParcelFileDescriptor? = null
  private var activeConfig: String? = null
  private var connectivityManager: ConnectivityManager? = null
  private var networkCallback: ConnectivityManager.NetworkCallback? = null

  override fun onCreate() {
    super.onCreate()
    ensureNotificationChannel()
  }

  override fun onStartCommand(intent: Intent?, flags: Int, startId: Int): Int {
    when (intent?.action) {
      ACTION_STOP -> {
        tearDownTunnel()
        stopSelf()
        return START_NOT_STICKY
      }
      ACTION_START, null -> {
        val config = intent?.getStringExtra(EXTRA_CONFIG)
        activeConfig = if (!config.isNullOrBlank()) {
          config
        } else {
          FALLBACK_CONFIG
        }
        startTunnel()
        return START_STICKY
      }
    }
    return START_NOT_STICKY
  }

  override fun onDestroy() {
    super.onDestroy()
    val shouldResetStatus = currentStatus != STATUS_FAILED
    tearDownTunnel(setStatus = shouldResetStatus, clearError = shouldResetStatus)
    if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.N) {
      stopForeground(STOP_FOREGROUND_REMOVE)
    } else {
      @Suppress("DEPRECATION")
      stopForeground(true)
    }
  }

  override fun onRevoke() {
    super.onRevoke()
    // The system revoked VPN permissions, shut everything down gracefully.
    stopSelf()
  }


private fun startTunnel() {
    currentStatus = STATUS_CONNECTING
    lastError = null

        // Simple foreground service start without specific type
    startForeground(NOTIFICATION_ID, buildNotification("Connecting..."))

    val parsedConfig = try {
        parseConfig(activeConfig ?: FALLBACK_CONFIG)
    } catch (error: IllegalArgumentException) {
        lastError = error.message
        handleTunnelFailure("Invalid configuration")
        return
    }

    val builder = Builder().apply {
        setSession("CasperVPN")
        setMtu(parsedConfig.mtu)
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.Q) {
            setMetered(false)
        }
        val immutableFlag = if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.M) {
            PendingIntent.FLAG_IMMUTABLE
        } else {
            0
        }
        val configureIntent = PendingIntent.getActivity(
            this@WireGuardVpnService,
            0,
            Intent(this@WireGuardVpnService, MainActivity::class.java),
            PendingIntent.FLAG_UPDATE_CURRENT or immutableFlag
        )
        setConfigureIntent(configureIntent)

        parsedConfig.addresses.forEach { (address, prefix) ->
            addAddress(address, prefix)
        }
        parsedConfig.dnsServers.forEach { server ->
            addDnsServer(server)
        }
        parsedConfig.routes.forEach { (address, prefix) ->
            addRoute(address, prefix)
        }
    }

    val descriptor = try {
        builder.establish()
    } catch (error: Exception) {
        lastError = error.message ?: "Failed to establish VPN interface."
        handleTunnelFailure("Connection failed")
        return
    }

    if (descriptor == null) {
        lastError = "System refused to create the VPN interface."
        handleTunnelFailure("Connection failed")
        return
    }

    tunnelDescriptor?.close()
    tunnelDescriptor = descriptor
    isRunning = true
    currentStatus = STATUS_CONNECTED
    updateNotification("Connected")
    monitorVpnNetwork()
}

  private fun monitorVpnNetwork() {
    if (connectivityManager == null) {
      connectivityManager = getSystemService(Context.CONNECTIVITY_SERVICE) as ConnectivityManager
    }

    unregisterNetworkCallback()

    val request = NetworkRequest.Builder()
      .addTransportType(NetworkCapabilities.TRANSPORT_VPN)
      .build()

    val callback = object : ConnectivityManager.NetworkCallback() {
      override fun onAvailable(network: Network) {
        super.onAvailable(network)
        currentStatus = STATUS_CONNECTED
        isRunning = true
        updateNotification("Connected")
      }

      override fun onLost(network: Network) {
        super.onLost(network)
        if (currentStatus != STATUS_FAILED) {
          currentStatus = STATUS_DISCONNECTED
        }
        isRunning = false
        updateNotification("Disconnected")
        stopSelf()
      }
    }

    connectivityManager?.registerNetworkCallback(request, callback)
    networkCallback = callback
  }

  private fun unregisterNetworkCallback() {
    val callback = networkCallback
    if (callback != null) {
      connectivityManager?.unregisterNetworkCallback(callback)
      networkCallback = null
    }
  }

  private fun handleTunnelFailure(notificationText: String) {
    currentStatus = STATUS_FAILED
    isRunning = false
    if (lastError.isNullOrEmpty()) {
      lastError = "Failed to establish VPN tunnel."
    }
    tearDownTunnel(setStatus = false, clearError = false)
    updateNotification(notificationText)
    stopSelf()
  }

  private fun ensureNotificationChannel() {
    if (Build.VERSION.SDK_INT < Build.VERSION_CODES.O) {
      return
    }
    val notificationManager = getSystemService(Context.NOTIFICATION_SERVICE) as NotificationManager
    val channel = NotificationChannel(
      CHANNEL_ID,
      CHANNEL_NAME,
      NotificationManager.IMPORTANCE_LOW
    )
    notificationManager.createNotificationChannel(channel)
  }

  private fun buildNotification(status: String): Notification {
    val intent = Intent(this, MainActivity::class.java)
    val pendingIntent = if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.M) {
      PendingIntent.getActivity(
        this,
        0,
        intent,
        PendingIntent.FLAG_UPDATE_CURRENT or PendingIntent.FLAG_IMMUTABLE
      )
    } else {
      @Suppress("DEPRECATION")
      PendingIntent.getActivity(
        this,
        0,
        intent,
        PendingIntent.FLAG_UPDATE_CURRENT
      )
    }

    return NotificationCompat.Builder(this, CHANNEL_ID)
      .setSmallIcon(android.R.drawable.stat_sys_download_done)
      .setContentTitle("CasperVPN")
      .setContentText(status)
      .setContentIntent(pendingIntent)
      .setOngoing(true)
      .setPriority(NotificationCompat.PRIORITY_LOW)
      .build()
  }

  private fun updateNotification(status: String) {
    val notificationManager = getSystemService(Context.NOTIFICATION_SERVICE) as NotificationManager
    notificationManager.notify(NOTIFICATION_ID, buildNotification(status))
  }

  private fun tearDownTunnel(setStatus: Boolean = true, clearError: Boolean = true) {
    unregisterNetworkCallback()
    tunnelDescriptor?.close()
    tunnelDescriptor = null
    isRunning = false
    if (setStatus && currentStatus != STATUS_FAILED) {
      currentStatus = STATUS_DISCONNECTED
    }
    if (clearError) {
      lastError = null
    }
  }

  private data class ParsedConfig(
    val addresses: List<Pair<String, Int>>,
    val dnsServers: List<String>,
    val routes: List<Pair<String, Int>>,
    val mtu: Int
  )

  private fun parseConfig(rawConfig: String): ParsedConfig {
    val addresses = mutableListOf<Pair<String, Int>>()
    val dnsServers = mutableListOf<String>()
    val routes = mutableListOf<Pair<String, Int>>()
    var mtu = 1280
    var currentSection = ""

    rawConfig.lineSequence().forEach { line ->
      val trimmed = line.trim()
      if (trimmed.isEmpty() || trimmed.startsWith("#")) {
        return@forEach
      }

      if (trimmed.startsWith("[")) {
        currentSection = trimmed
        return@forEach
      }

      val parts = trimmed.split("=", limit = 2)
      if (parts.size != 2) {
        return@forEach
      }

      val key = parts[0].trim()
      val value = parts[1].trim()

      when (currentSection) {
        "[Interface]" -> when (key.lowercase()) {
          "address" -> value.split(",").map { it.trim() }.filter { it.isNotEmpty() }.forEach { address ->
            val slashIndex = address.indexOf('/')
            if (slashIndex <= 0) {
              throw IllegalArgumentException("Invalid address entry in configuration.")
            }
            val prefix = address.substring(slashIndex + 1).toIntOrNull()
              ?: throw IllegalArgumentException("Invalid address prefix in configuration.")
            addresses.add(address.substring(0, slashIndex) to prefix)
          }
          "dns" -> value.split(",").map { it.trim() }.filter { it.isNotEmpty() }.forEach { dns ->
            dnsServers.add(dns)
          }
          "mtu" -> {
            mtu = value.toIntOrNull() ?: 1280
          }
        }
        "[Peer]" -> when (key.lowercase()) {
          "allowedips" -> value.split(",").map { it.trim() }.filter { it.isNotEmpty() }.forEach { cidr ->
            val slashIndex = cidr.indexOf('/')
            if (slashIndex <= 0) {
              throw IllegalArgumentException("Invalid AllowedIPs entry in configuration.")
            }
            val prefix = cidr.substring(slashIndex + 1).toIntOrNull()
              ?: throw IllegalArgumentException("Invalid AllowedIPs prefix in configuration.")
            routes.add(cidr.substring(0, slashIndex) to prefix)
          }
        }
      }
    }

    if (addresses.isEmpty()) {
      throw IllegalArgumentException("VPN configuration must declare at least one interface address.")
    }
    if (routes.isEmpty()) {
      routes.add("0.0.0.0" to 0)
    }

    return ParsedConfig(
      addresses = addresses,
      dnsServers = if (dnsServers.isEmpty()) listOf("1.1.1.1") else dnsServers,
      routes = routes,
      mtu = mtu
    )
  }
}
