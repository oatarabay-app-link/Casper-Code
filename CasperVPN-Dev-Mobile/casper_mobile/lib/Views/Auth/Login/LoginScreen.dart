// //lib/Views/Auth/Login/LoginScreen.dart:
// import 'package:flutter/material.dart';

// import '../../../Routes/AppRoutes.dart';

// class LoginScreen extends StatefulWidget {
// 	const LoginScreen({super.key});

// 	@override
// 	State<LoginScreen> createState() => _LoginScreenState();
// }

// class _LoginScreenState extends State<LoginScreen> {
// 	final _formKey = GlobalKey<FormState>();
// 	final _usernameController = TextEditingController();
// 	final _passwordController = TextEditingController();

// 	bool _isSubmitting = false;
// 	String? _errorMessage;

// 	@override
// 	void dispose() {
// 		_usernameController.dispose();
// 		_passwordController.dispose();
// 		super.dispose();
// 	}

// 	Future<void> _handleLogin() async {
// 		if (!_formKey.currentState!.validate()) {
// 			return;
// 		}

// 		setState(() {
// 			_isSubmitting = true;
// 			_errorMessage = null;
// 		});

// 		await Future.delayed(const Duration(milliseconds: 500));

// 		if (_usernameController.text.trim().toLowerCase() == 'admin' &&
// 				_passwordController.text == 'Casper@123') {
// 			if (!mounted) {
// 				return;
// 			}
// 					Navigator.of(context).pushReplacementNamed(AppRoutes.home);
// 		} else {
// 			setState(() {
// 				_errorMessage = 'Invalid username or password.';
// 			});
// 		}

// 		if (mounted) {
// 			setState(() {
// 				_isSubmitting = false;
// 			});
// 		}
// 	}

// 	@override
// 	Widget build(BuildContext context) {
// 		final theme = Theme.of(context);

// 		return Scaffold(
// 			body: Center(
// 				child: SingleChildScrollView(
// 					padding: const EdgeInsets.all(24),
// 					child: Card(
// 						elevation: 4,
// 						shape: RoundedRectangleBorder(
// 							borderRadius: BorderRadius.circular(16),
// 						),
// 						child: Padding(
// 							padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 32),
// 							child: Form(
// 								key: _formKey,
// 								child: Column(
// 									mainAxisSize: MainAxisSize.min,
// 									children: [
// 										Icon(Icons.shield, size: 72, color: theme.colorScheme.primary),
// 										const SizedBox(height: 16),
// 										Text(
// 											'CasperVPN',
// 											style: theme.textTheme.headlineSmall?.copyWith(
// 												fontWeight: FontWeight.bold,
// 											),
// 										),
// 										const SizedBox(height: 24),
// 										TextFormField(
// 											controller: _usernameController,
// 											textInputAction: TextInputAction.next,
// 											decoration: const InputDecoration(
// 												labelText: 'Username',
// 												prefixIcon: Icon(Icons.person_outline),
// 											),
// 											validator: (value) {
// 												if (value == null || value.trim().isEmpty) {
// 													return 'Enter a username';
// 												}
// 												return null;
// 											},
// 										),
// 										const SizedBox(height: 16),
// 										TextFormField(
// 											controller: _passwordController,
// 											textInputAction: TextInputAction.done,
// 											obscureText: true,
// 											decoration: const InputDecoration(
// 												labelText: 'Password',
// 												prefixIcon: Icon(Icons.lock_outline),
// 											),
// 											validator: (value) {
// 												if (value == null || value.isEmpty) {
// 													return 'Enter a password';
// 												}
// 												return null;
// 											},
// 											onFieldSubmitted: (_) => _handleLogin(),
// 										),
// 										const SizedBox(height: 24),
// 										if (_errorMessage != null)
// 											Padding(
// 												padding: const EdgeInsets.only(bottom: 12),
// 												child: Text(
// 													_errorMessage!,
// 													style: theme.textTheme.bodyMedium?.copyWith(
// 														color: theme.colorScheme.error,
// 													),
// 												),
// 											),
// 										SizedBox(
// 											width: double.infinity,
// 											child: ElevatedButton(
// 												onPressed: _isSubmitting ? null : _handleLogin,
// 												style: ElevatedButton.styleFrom(
// 													padding: const EdgeInsets.symmetric(vertical: 14),
// 												),
// 												child: _isSubmitting
// 														? const SizedBox(
// 																height: 20,
// 																width: 20,
// 																child: CircularProgressIndicator(strokeWidth: 2),
// 															)
// 														: const Text('Login'),
// 											),
// 										),
// 									],
// 								),
// 							),
// 						),
// 					),
// 				),
// 			),
// 		);
// 	}
// }

// lib/Views/Auth/Login/LoginScreen.dart
import 'package:flutter/material.dart';
import '../../../Routes/AppRoutes.dart';
import '../../Debug/DebugScreen.dart';
import '../../../ViewModels/DebugViewModel.dart';
import '../../../Services/VPNService.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final _formKey = GlobalKey<FormState>();
  final _usernameController = TextEditingController();
  final _passwordController = TextEditingController();

  bool _isSubmitting = false;
  String? _errorMessage;

  @override
  void dispose() {
    _usernameController.dispose();
    _passwordController.dispose();
    super.dispose();
  }

  // lib/Views/Auth/Login/LoginScreen.dart
  Future<void> _handleLogin() async {
    if (!_formKey.currentState!.validate()) return;

    setState(() {
      _isSubmitting = true;
      _errorMessage = null;
    });

    await Future.delayed(const Duration(milliseconds: 500));

    if (_usernameController.text.trim().toLowerCase() == 'admin' &&
        _passwordController.text == 'Casper@123') {
      try {
        // Initialize VPN service immediately after successful login
        final deviceName =
            'UserDevice-${DateTime.now().millisecondsSinceEpoch}';
        await VPNService.instance.initialize(deviceName: deviceName);

        if (!mounted) return;
        Navigator.of(context).pushReplacementNamed(AppRoutes.home);
      } catch (e) {
        if (!mounted) return;
        // Even if VPN initialization fails, still go to home screen
        // but show an error message
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('VPN initialization failed: $e'),
            backgroundColor: Colors.orange,
          ),
        );
        Navigator.of(context).pushReplacementNamed(AppRoutes.home);
      }
    } else {
      setState(() {
        _errorMessage = 'Invalid username or password.';
        _isSubmitting = false;
      });
    }
  }

  void _goToDebug() {
    final debugVM = DebugViewModel();
    Navigator.of(
      context,
    ).push(MaterialPageRoute(builder: (_) => DebugScreen()));
  }

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);

    return Scaffold(
      body: Center(
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(24),
          child: Card(
            elevation: 4,
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(16),
            ),
            child: Padding(
              padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 32),
              child: Form(
                key: _formKey,
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Icon(
                      Icons.shield,
                      size: 72,
                      color: theme.colorScheme.primary,
                    ),
                    const SizedBox(height: 16),
                    Text(
                      'CasperVPN',
                      style: theme.textTheme.headlineSmall?.copyWith(
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    const SizedBox(height: 24),
                    TextFormField(
                      controller: _usernameController,
                      textInputAction: TextInputAction.next,
                      decoration: const InputDecoration(
                        labelText: 'Username',
                        prefixIcon: Icon(Icons.person_outline),
                      ),
                      validator: (value) {
                        if (value == null || value.trim().isEmpty) {
                          return 'Enter a username';
                        }
                        return null;
                      },
                    ),
                    const SizedBox(height: 16),
                    TextFormField(
                      controller: _passwordController,
                      textInputAction: TextInputAction.done,
                      obscureText: true,
                      decoration: const InputDecoration(
                        labelText: 'Password',
                        prefixIcon: Icon(Icons.lock_outline),
                      ),
                      validator: (value) {
                        if (value == null || value.isEmpty) {
                          return 'Enter a password';
                        }
                        return null;
                      },
                      onFieldSubmitted: (_) => _handleLogin(),
                    ),
                    const SizedBox(height: 24),
                    if (_errorMessage != null)
                      Padding(
                        padding: const EdgeInsets.only(bottom: 12),
                        child: Text(
                          _errorMessage!,
                          style: theme.textTheme.bodyMedium?.copyWith(
                            color: theme.colorScheme.error,
                          ),
                        ),
                      ),
                    Row(
                      children: [
                        Expanded(
                          child: ElevatedButton(
                            onPressed: _isSubmitting ? null : _handleLogin,
                            style: ElevatedButton.styleFrom(
                              padding: const EdgeInsets.symmetric(vertical: 14),
                            ),
                            child: _isSubmitting
                                ? const SizedBox(
                                    height: 20,
                                    width: 20,
                                    child: CircularProgressIndicator(
                                      strokeWidth: 2,
                                    ),
                                  )
                                : const Text('Login'),
                          ),
                        ),
                        const SizedBox(width: 12),
                        Expanded(
                          child: OutlinedButton(
                            onPressed: _goToDebug,
                            style: OutlinedButton.styleFrom(
                              padding: const EdgeInsets.symmetric(vertical: 14),
                            ),
                            child: const Text('Debug'),
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
            ),
          ),
        ),
      ),
    );
  }
}
