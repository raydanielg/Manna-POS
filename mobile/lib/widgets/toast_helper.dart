import 'package:flutter/material.dart';
import '../shared/theme/app_colors.dart';

class ToastHelper {
  static void show(BuildContext context, String message, {bool error = false, int seconds = 3}) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Row(
          children: [
            Icon(
              error ? Icons.error_outline_rounded : Icons.check_circle_outline_rounded,
              color: Colors.white,
              size: 20,
            ),
            const SizedBox(width: 10),
            Expanded(child: Text(message, style: const TextStyle(color: Colors.white, fontSize: 14))),
          ],
        ),
        backgroundColor: error ? AppColors.danger : AppColors.success,
        behavior: SnackBarBehavior.floating,
        margin: const EdgeInsets.fromLTRB(16, 0, 16, 20),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
        duration: Duration(seconds: seconds),
      ),
    );
  }

  static void success(BuildContext context, String message) => show(context, message, error: false);

  static void error(BuildContext context, String message) => show(context, message, error: true);
}
