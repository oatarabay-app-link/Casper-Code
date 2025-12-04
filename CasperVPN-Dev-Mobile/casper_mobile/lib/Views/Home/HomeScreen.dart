// lib/Views/Home/HomeScreen.dart:
import 'dart:async';
import 'dart:math' as math;

import 'package:flutter/material.dart';

import '../../Routes/AppRoutes.dart';
import '../../Services/VPNService.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen>
    with SingleTickerProviderStateMixin {
  late final AnimationController _controller;
  late final Animation<Color?> _colorAnimation;
  final VPNService _vpnService = VPNService.instance;
  bool _isConnecting = false;
  String _status = 'DISCONNECTED';
  Timer? _statusTimer;
  DateTime? _connectedAt;
  bool _initializing = false;
  String? _initializationError;

  @override
  void initState() {
    super.initState();
    _controller =
        AnimationController(vsync: this, duration: const Duration(seconds: 2))
          ..addStatusListener((status) {
            if (status == AnimationStatus.completed) {
              _controller.reverse();
            } else if (status == AnimationStatus.dismissed) {
              _controller.forward();
            }
          });

    final baseColor = Colors.blueAccent;
    _colorAnimation = ColorTween(
      begin: baseColor,
      end: baseColor.withValues(alpha: 0.6),
    ).chain(CurveTween(curve: Curves.easeInOut)).animate(_controller);

    // Auto-initialize VPN service when home screen loads
    _initializeVPNService();
  }

  Future<void> _initializeVPNService() async {
    if (_vpnService.isInitialized) return;

    setState(() {
      _initializing = true;
      _initializationError = null;
    });

    try {
      // Use a device name (you might want to get this from user or device info)
      final deviceName = 'UserDevice-${DateTime.now().millisecondsSinceEpoch}';
      await _vpnService.initialize(deviceName: deviceName);

      if (!mounted) return;

      // After successful initialization, sync status
      await _vpnService.syncStatus();
      _status = _vpnService.currentStatus;
      if (_status == 'CONNECTED') {
        _connectedAt ??= DateTime.now();
      }
      _updateAnimation(_vpnService.isConnected || _status == 'CONNECTING');
    } catch (e) {
      if (!mounted) return;
      setState(() {
        _initializationError = 'Failed to initialize VPN: $e';
      });
    } finally {
      if (mounted) {
        setState(() {
          _initializing = false;
        });
      }
    }
  }

  @override
  void dispose() {
    _statusTimer?.cancel();
    _controller.dispose();
    super.dispose();
  }

  Future<void> _toggleConnection() async {
    // Check if VPN service is initialized
    if (!_vpnService.isInitialized) {
      // Try to initialize first
      await _initializeVPNService();
      if (!_vpnService.isInitialized) {
        // Still not initialized, show error
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(
              _initializationError ?? 'VPN service not initialized',
            ),
            backgroundColor: Colors.red,
          ),
        );
        return;
      }
    }

    if (_vpnService.isBusy) return;

    setState(() {
      _isConnecting = true;
    });

    final bool starting = !_vpnService.isConnected;
    final bool success = starting
        ? await _vpnService.connect()
        : await _vpnService.disconnect();

    _applyServiceState(finalizeConnecting: true);

    if (!success && _status != 'CONNECTING' && _status != 'FAILED') {
      _notifyFailure();
    }
  }

  Future<void> _manualRefreshStatus() async {
    if (_vpnService.isBusy) {
      return;
    }

    final previousStatus = _status;
    await _vpnService.syncStatus();
    _applyServiceState();

    if (!mounted) {
      return;
    }

    if (_status != 'FAILED' && previousStatus == _status) {
      ScaffoldMessenger.of(
        context,
      ).showSnackBar(const SnackBar(content: Text('Status refreshed.')));
    }
  }

  void _applyServiceState({bool finalizeConnecting = false}) {
    if (!mounted) {
      return;
    }

    final previousStatus = _status;
    final nextStatus = _vpnService.currentStatus;
    final isActive = _vpnService.isConnected;

    setState(() {
      _status = nextStatus;
      if (finalizeConnecting || nextStatus != 'CONNECTING') {
        _isConnecting = false;
      }
      if (nextStatus == 'CONNECTED') {
        _connectedAt ??= DateTime.now();
      } else if (nextStatus != 'CONNECTING') {
        _connectedAt = null;
      }
    });

    _updateAnimation(isActive);

    if (nextStatus == 'CONNECTING' || nextStatus == 'CONNECTED') {
      _startPolling();
    } else {
      _stopPolling();
    }

    if (nextStatus == 'FAILED' && previousStatus != 'FAILED') {
      _notifyFailure();
    }
  }

  void _startPolling() {
    _statusTimer ??= Timer.periodic(const Duration(seconds: 1), (_) async {
      if (!mounted) {
        _stopPolling();
        return;
      }

      if (_status == 'CONNECTING') {
        final previousStatus = _status;
        await _vpnService.syncStatus();
        _applyServiceState();
        if (_status != 'CONNECTING') {
          _stopPolling();
        } else if (previousStatus != _status) {
          _updateAnimation(_vpnService.isConnected);
        }
      } else if (_status == 'CONNECTED') {
        setState(() {});
      } else {
        _stopPolling();
      }
    });
  }

  void _stopPolling() {
    _statusTimer?.cancel();
    _statusTimer = null;
  }

  void _updateAnimation(bool isActive) {
    final shouldAnimate = isActive || _status == 'CONNECTING';
    if (shouldAnimate) {
      if (!_controller.isAnimating) {
        _controller.forward();
      }
    } else {
      _controller.stop();
      _controller.reset();
    }
  }

  void _notifyFailure() {
    if (!mounted) {
      return;
    }

    String message;
    if (_vpnService.permissionRequired) {
      message =
          'VPN permission required. Approve the Android prompt then try again.';
    } else if (_vpnService.lastError != null &&
        _vpnService.lastError!.isNotEmpty) {
      message = _vpnService.lastError!;
    } else if (_status == 'FAILED') {
      message =
          'Unable to establish the VPN tunnel. Please verify the configuration.';
    } else {
      message = 'Unable to update VPN state. Please try again.';
    }

    _vpnService.clearPermissionFlag();
    ScaffoldMessenger.of(
      context,
    ).showSnackBar(SnackBar(content: Text(message)));
  }

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    final isConnected = _vpnService.isConnected;
    final statusLabel = _mapStatusToLabel(_status, isConnected);
    final statusColor = _statusColor(theme, _status, isConnected);
    // final configInfo = _parseVpnConfig(_vpnService.config);

    // Show loading indicator while initializing
    if (_initializing) {
      return Scaffold(
        appBar: AppBar(title: const Text('CasperVPN')),
        body: Center(
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              CircularProgressIndicator(),
              SizedBox(height: 20),
              Text('Initializing VPN Service...'),
            ],
          ),
        ),
      );
    }
    // Show error if initialization failed
    if (_initializationError != null) {
      return Scaffold(
        appBar: AppBar(title: const Text('CasperVPN')),
        body: Center(
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Icon(Icons.error_outline, color: Colors.red, size: 64),
              SizedBox(height: 20),
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 32),
                child: Text(
                  _initializationError!,
                  textAlign: TextAlign.center,
                  style: TextStyle(color: Colors.red),
                ),
              ),
              SizedBox(height: 20),
              ElevatedButton(
                onPressed: _initializeVPNService,
                child: Text('Retry Initialization'),
              ),
            ],
          ),
        ),
      );
    }
    return Scaffold(
      appBar: AppBar(
        title: const Text('CasperVPN'),
        actions: [
          IconButton(
            tooltip: 'Logout',
            icon: const Icon(Icons.logout),
            onPressed: () {
              Navigator.of(
                context,
              ).pushNamedAndRemoveUntil(AppRoutes.login, (route) => false);
            },
          ),
        ],
      ),
      body: SafeArea(
        child: LayoutBuilder(
          builder: (context, constraints) {
            final availableWidth = math.min(constraints.maxWidth, 500.0);

            return SingleChildScrollView(
              padding: const EdgeInsets.fromLTRB(24, 24, 24, 32),
              child: Center(
                child: ConstrainedBox(
                  constraints: BoxConstraints(
                    maxWidth: 500,
                    minHeight: math.max(constraints.maxHeight - 56, 0.0),
                  ),
                  child: Column(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      _buildHeroCard(
                        theme,
                        statusLabel,
                        statusColor,
                        isConnected,
                      ),
                      // const SizedBox(height: 24),
                      // _buildConnectionCard(theme, configInfo),
                      // const SizedBox(height: 16),
                      // _buildMetricsRow(theme, configInfo, isConnected, availableWidth),
                      // const SizedBox(height: 32),
                      SizedBox(
                        width: double.infinity,
                        child: ElevatedButton(
                          onPressed: _isConnecting ? null : _toggleConnection,
                          style: ElevatedButton.styleFrom(
                            padding: const EdgeInsets.symmetric(vertical: 18),
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(20),
                            ),
                          ),
                          child: _isConnecting
                              ? const SizedBox(
                                  width: 22,
                                  height: 22,
                                  child: CircularProgressIndicator(
                                    strokeWidth: 2,
                                  ),
                                )
                              : Text(isConnected ? 'Disconnect' : 'Connect'),
                        ),
                      ),
                      if (_status == 'FAILED') ...[
                        const SizedBox(height: 12),
                        SizedBox(
                          width: double.infinity,
                          child: OutlinedButton.icon(
                            onPressed: _isConnecting ? null : _toggleConnection,
                            icon: const Icon(Icons.refresh),
                            label: const Text('Retry'),
                          ),
                        ),
                      ],
                    ],
                  ),
                ),
              ),
            );
          },
        ),
      ),
      floatingActionButton: (_status == 'CONNECTING' || _status == 'CONNECTED')
          ? FloatingActionButton.extended(
              onPressed: _manualRefreshStatus,
              icon: const Icon(Icons.refresh),
              label: const Text('Refresh status'),
            )
          : null,
    );
  }

  Widget _buildHeroCard(
    ThemeData theme,
    String statusLabel,
    Color statusColor,
    bool isConnected,
  ) {
    return Container(
      padding: const EdgeInsets.symmetric(vertical: 32, horizontal: 24),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(28),
        gradient: LinearGradient(
          colors: [
            theme.colorScheme.primaryContainer.withValues(alpha: 0.55),
            theme.colorScheme.surfaceContainerHighest.withValues(alpha: 0.25),
          ],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
      ),
      child: Column(
        children: [
          AnimatedBuilder(
            animation: _controller,
            builder: (context, child) {
              final animatedColor = (isConnected || _status == 'CONNECTING')
                  ? _colorAnimation.value ?? theme.colorScheme.primary
                  : theme.colorScheme.surfaceContainerHigh;
              return Container(
                width: 220,
                height: 220,
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  gradient: RadialGradient(
                    colors: [
                      animatedColor,
                      animatedColor.withValues(alpha: 0.08),
                    ],
                  ),
                ),
                child: child,
              );
            },
            child: Stack(
              alignment: Alignment.center,
              children: [
                if (_status == 'CONNECTING')
                  const SizedBox(
                    width: 140,
                    height: 140,
                    child: CircularProgressIndicator(strokeWidth: 2),
                  ),
                Container(
                  width: 120,
                  height: 120,
                  decoration: BoxDecoration(
                    shape: BoxShape.circle,
                    color: theme.colorScheme.surface.withValues(alpha: 0.9),
                  ),
                  child: Center(
                    child: AnimatedSwitcher(
                      duration: const Duration(milliseconds: 280),
                      child: Icon(
                        isConnected ? Icons.shield : Icons.shield_outlined,
                        key: ValueKey<bool>(isConnected),
                        size: 72,
                        color: statusColor,
                      ),
                    ),
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(height: 24),
          AnimatedSwitcher(
            duration: const Duration(milliseconds: 280),
            child: Text(
              statusLabel,
              key: ValueKey<String>(statusLabel),
              style: theme.textTheme.headlineSmall?.copyWith(
                fontWeight: FontWeight.bold,
                color: statusColor,
              ),
            ),
          ),
          const SizedBox(height: 8),
          AnimatedSwitcher(
            duration: const Duration(milliseconds: 280),
            child: Text(
              _statusDescription(_status, isConnected),
              key: ValueKey<String>(_statusDescription(_status, isConnected)),
              textAlign: TextAlign.center,
              style: theme.textTheme.bodyMedium?.copyWith(
                color: theme.colorScheme.onSurfaceVariant,
              ),
            ),
          ),
          if (_connectedAt != null) ...[
            const SizedBox(height: 12),
            Text(
              'Connected for ${_formatDuration(DateTime.now().difference(_connectedAt!))}',
              style: theme.textTheme.labelMedium?.copyWith(
                color: theme.colorScheme.onSurfaceVariant,
              ),
            ),
          ],
        ],
      ),
    );
  }

  Widget _buildConnectionCard(ThemeData theme, _VpnConfigInfo info) {
    return Card(
      elevation: 0,
      color: theme.colorScheme.surfaceContainerHigh.withValues(alpha: 0.35),
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(24)),
      child: Padding(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              'Connection details',
              style: theme.textTheme.titleMedium?.copyWith(
                fontWeight: FontWeight.bold,
                color: theme.colorScheme.onSurface,
              ),
            ),
            const SizedBox(height: 16),
            // _detailRow(theme, Icons.place_outlined, 'Address', info.addresses.join(', ')),
            // _detailRow(theme, Icons.dns_outlined, 'DNS servers', info.dnsServers.join(', ')),
            // _detailRow(theme, Icons.route_outlined, 'Allowed IPs', info.allowedIps.join(', ')),
          ],
        ),
      ),
    );
  }

  Widget _buildMetricsRow(
    ThemeData theme,
    _VpnConfigInfo info,
    bool isConnected,
    double maxWidth,
  ) {
    final tileWidth = maxWidth >= 420 ? (maxWidth - 12) / 2 : maxWidth;
    final sessionValue = _connectedAt != null
        ? _formatDuration(DateTime.now().difference(_connectedAt!))
        : (isConnected ? 'Just connected' : 'Not connected');

    return Wrap(
      spacing: 12,
      runSpacing: 12,
      children: [
        _metricCard(
          theme,
          Icons.timer_outlined,
          'Session',
          sessionValue,
          tileWidth,
        ),
        _metricCard(
          theme,
          Icons.shield_outlined,
          'Tunnel state',
          _mapStatusToLabel(_status, isConnected),
          tileWidth,
        ),
      ],
    );
  }

  Widget _metricCard(
    ThemeData theme,
    IconData icon,
    String label,
    String value,
    double width,
  ) {
    return SizedBox(
      width: width,
      child: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: theme.colorScheme.surfaceContainerHigh.withValues(alpha: 0.3),
          borderRadius: BorderRadius.circular(20),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Icon(icon, size: 20, color: theme.colorScheme.primary),
            const SizedBox(height: 12),
            Text(
              label,
              style: theme.textTheme.labelMedium?.copyWith(
                color: theme.colorScheme.onSurfaceVariant,
                fontWeight: FontWeight.w600,
              ),
            ),
            const SizedBox(height: 6),
            Text(
              value,
              style: theme.textTheme.bodyLarge?.copyWith(
                color: theme.colorScheme.onSurface,
                fontWeight: FontWeight.w600,
              ),
              maxLines: 2,
              overflow: TextOverflow.ellipsis,
            ),
          ],
        ),
      ),
    );
  }

  Widget _detailRow(
    ThemeData theme,
    IconData icon,
    String label,
    String value,
  ) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 8),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Icon(icon, size: 20, color: theme.colorScheme.primary),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  label,
                  style: theme.textTheme.labelMedium?.copyWith(
                    color: theme.colorScheme.onSurfaceVariant,
                    fontWeight: FontWeight.w600,
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  value,
                  style: theme.textTheme.bodyMedium?.copyWith(
                    color: theme.colorScheme.onSurface,
                  ),
                  maxLines: 2,
                  overflow: TextOverflow.ellipsis,
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class _VpnConfigInfo {
  final List<String> addresses;
  final List<String> dnsServers;
  final List<String> allowedIps;

  const _VpnConfigInfo({
    required this.addresses,
    required this.dnsServers,
    required this.allowedIps,
  });
}

_VpnConfigInfo _parseVpnConfig(String raw) {
  final addresses = <String>[];
  final dnsServers = <String>[];
  final allowedIps = <String>[];
  String currentSection = '';

  for (final line in raw.split('\n')) {
    final trimmed = line.trim();
    if (trimmed.isEmpty || trimmed.startsWith('#')) {
      continue;
    }

    if (trimmed.startsWith('[')) {
      currentSection = trimmed.toLowerCase();
      continue;
    }

    final parts = trimmed.split('=');
    if (parts.length < 2) {
      continue;
    }

    final key = parts.first.trim().toLowerCase();
    final value = parts.sublist(1).join('=').trim();

    if (currentSection == '[interface]') {
      if (key == 'address') {
        addresses.addAll(
          value
              .split(',')
              .map((entry) => entry.trim())
              .where((entry) => entry.isNotEmpty),
        );
      } else if (key == 'dns') {
        dnsServers.addAll(
          value
              .split(',')
              .map((entry) => entry.trim())
              .where((entry) => entry.isNotEmpty),
        );
      }
    } else if (currentSection == '[peer]') {
      if (key == 'allowedips') {
        allowedIps.addAll(
          value
              .split(',')
              .map((entry) => entry.trim())
              .where((entry) => entry.isNotEmpty),
        );
      }
    }
  }

  return _VpnConfigInfo(
    addresses: addresses.isEmpty ? const ['N/A'] : addresses,
    dnsServers: dnsServers.isEmpty ? const ['1.1.1.1'] : dnsServers,
    allowedIps: allowedIps.isEmpty ? const ['0.0.0.0/0'] : allowedIps,
  );
}

String _formatDuration(Duration duration) {
  if (duration.inHours >= 1) {
    final hours = duration.inHours;
    final minutes = duration.inMinutes.remainder(60);
    return '${hours}h ${minutes}m';
  }
  if (duration.inMinutes >= 1) {
    final minutes = duration.inMinutes;
    final seconds = duration.inSeconds.remainder(60);
    return '${minutes}m ${seconds}s';
  }
  final seconds = duration.inSeconds;
  return '${seconds}s';
}

String _mapStatusToLabel(String status, bool isConnected) {
  switch (status) {
    case 'CONNECTING':
      return 'Connecting...';
    case 'CONNECTED':
      return 'Connected';
    case 'FAILED':
      return 'Connection failed';
    default:
      return isConnected ? 'Connected' : 'Disconnected';
  }
}

Color _statusColor(ThemeData theme, String status, bool isConnected) {
  switch (status) {
    case 'CONNECTING':
      return theme.colorScheme.secondary;
    case 'CONNECTED':
      return theme.colorScheme.primary;
    case 'FAILED':
      return theme.colorScheme.error;
    default:
      return isConnected
          ? theme.colorScheme.primary
          : theme.colorScheme.onSurface;
  }
}

String _statusDescription(String status, bool isConnected) {
  if (status == 'CONNECTING') {
    return 'Establishing a secure tunnel...';
  }
  if (status == 'FAILED') {
    return 'We could not establish the VPN tunnel. Review the configuration and try again.';
  }
  return isConnected
      ? 'Your connection is protected through WireGuard.'
      : 'Tap connect to secure your connection.';
}
