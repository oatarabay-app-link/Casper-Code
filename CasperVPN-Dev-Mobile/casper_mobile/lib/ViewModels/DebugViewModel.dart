// lib/ViewModels/DebugViewModel.dart:
import 'package:flutter/foundation.dart';

class DebugViewModel {
  final ValueNotifier<String> status = ValueNotifier<String>('Idle');
  final ValueNotifier<bool> isLoading = ValueNotifier<bool>(false);

  void dispose() {
    status.dispose();
    isLoading.dispose();
  }
}
