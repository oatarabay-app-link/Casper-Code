//lib/main.dart:
import 'package:flutter/material.dart';
import 'Routes/AppRoutes.dart';
import 'Views/Auth/Login/LoginScreen.dart';
import 'Views/Home/HomeScreen.dart';
import 'Views/Debug/DebugScreen.dart';

void main() {
  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'CasperVPN',
      debugShowCheckedModeBanner: false,
      theme: ThemeData(
        colorScheme: ColorScheme.fromSeed(seedColor: Colors.blueAccent),
        scaffoldBackgroundColor: Colors.grey.shade100,
        useMaterial3: true,
      ),
      initialRoute: AppRoutes.login,
      routes: {
        AppRoutes.login: (_) => const LoginScreen(),
        AppRoutes.home: (_) => const HomeScreen(),
        AppRoutes.debug: (_) => const DebugScreen(), // new debug route
      },
    );
  }
}
