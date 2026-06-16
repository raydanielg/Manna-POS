import 'package:flutter/material.dart';
import '../theme/app_colors.dart';

class EmptyState extends StatelessWidget {
  final IconData icon;
  final String title;
  final String? subtitle;
  final String? actionLabel;
  final VoidCallback? onAction;
  const EmptyState({super.key, required this.icon, required this.title, this.subtitle, this.actionLabel, this.onAction});

  @override
  Widget build(BuildContext context) {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(32),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Container(
              width: 72, height: 72,
              decoration: BoxDecoration(color: AppColors.primaryLt, shape: BoxShape.circle),
              child: Icon(icon, size: 32, color: AppColors.primary),
            ),
            const SizedBox(height: 16),
            Text(title, style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w600, color: AppColors.textPri)),
            if (subtitle != null) ...[
              const SizedBox(height: 8),
              Text(subtitle!, textAlign: TextAlign.center, style: const TextStyle(fontSize: 13, color: AppColors.textSec)),
            ],
            if (actionLabel != null && onAction != null) ...[
              const SizedBox(height: 20),
              ElevatedButton(onPressed: onAction, child: Text(actionLabel!)),
            ],
          ],
        ),
      ),
    );
  }
}

class LoadingWidget extends StatelessWidget {
  final String? message;
  const LoadingWidget({super.key, this.message});
  @override
  Widget build(BuildContext context) {
    return Center(
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          const CircularProgressIndicator(color: AppColors.primary),
          if (message != null) ...[const SizedBox(height: 12), Text(message!, style: const TextStyle(color: AppColors.textSec))],
        ],
      ),
    );
  }
}

class ErrorWidget2 extends StatelessWidget {
  final String message;
  final VoidCallback? onRetry;
  const ErrorWidget2({super.key, required this.message, this.onRetry});
  @override
  Widget build(BuildContext context) {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(32),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            const Icon(Icons.error_outline, size: 48, color: AppColors.error),
            const SizedBox(height: 12),
            Text(message, textAlign: TextAlign.center, style: const TextStyle(color: AppColors.textSec)),
            if (onRetry != null) ...[const SizedBox(height: 16), ElevatedButton(onPressed: onRetry, child: const Text('Retry'))],
          ],
        ),
      ),
    );
  }
}
