import 'package:flutter/material.dart';
import '../../shared/theme/app_theme.dart';

class ForgotPasswordPage extends StatefulWidget {
  const ForgotPasswordPage({super.key});
  @override State<ForgotPasswordPage> createState() => _ForgotPasswordPageState();
}

class _ForgotPasswordPageState extends State<ForgotPasswordPage> {
  final _email = TextEditingController();
  bool _sent = false;

  @override Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.bg,
      body: SafeArea(
        child: Padding(
          padding: const EdgeInsets.all(24),
          child: Column(children: [
            Row(children: [IconButton(
              icon: const Icon(Icons.arrow_back, color: AppColors.textPri),
              onPressed: () => Navigator.pop(context),
            )]),
            const SizedBox(height: 32),
            Container(
              padding: const EdgeInsets.all(28),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(16),
                border: Border.all(color: AppColors.line, width: 1),
                boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.03), blurRadius: 20, offset: const Offset(0, 4))],
              ),
              child: _sent ? Column(children: [
                TweenAnimationBuilder<double>(
                  tween: Tween(begin: 0.0, end: 1.0),
                  duration: const Duration(milliseconds: 500),
                  curve: Curves.easeOut,
                  builder: (_, value, child) => Transform.scale(
                    scale: 0.8 + (0.2 * value),
                    child: Opacity(opacity: value, child: child),
                  ),
                  child: const Icon(Icons.mark_email_read_outlined, size: 64, color: AppColors.success),
                ),
                const SizedBox(height: 16),
                const Text('Email Sent!', style: TextStyle(fontSize: 22, fontWeight: FontWeight.w700, color: AppColors.textPri)),
                const SizedBox(height: 8),
                Text('Check ${_email.text} for password reset instructions.', style: const TextStyle(color: AppColors.textSec), textAlign: TextAlign.center),
                const SizedBox(height: 24),
                SizedBox(width: double.infinity, height: 52, child: ElevatedButton(onPressed: () => Navigator.pop(context), child: const Text('Back to Login'))),
              ]) : Column(crossAxisAlignment: CrossAxisAlignment.stretch, children: [
                TweenAnimationBuilder<double>(
                  tween: Tween(begin: 0.0, end: 1.0),
                  duration: const Duration(milliseconds: 500),
                  curve: Curves.easeOut,
                  builder: (_, value, child) => Transform.scale(
                    scale: 0.8 + (0.2 * value),
                    child: Opacity(opacity: value, child: child),
                  ),
                  child: const Icon(Icons.lock_reset, size: 56, color: AppColors.primary),
                ),
                const SizedBox(height: 16),
                const Text('Reset Password', style: TextStyle(fontSize: 22, fontWeight: FontWeight.w700, color: AppColors.textPri), textAlign: TextAlign.center),
                const SizedBox(height: 8),
                const Text('Enter your email to receive reset instructions', style: TextStyle(color: AppColors.textSec, fontSize: 14), textAlign: TextAlign.center),
                const SizedBox(height: 24),
                TextField(
                  controller: _email,
                  keyboardType: TextInputType.emailAddress,
                  decoration: const InputDecoration(labelText: 'Email Address', prefixIcon: Icon(Icons.email_outlined)),
                ),
                const SizedBox(height: 24),
                SizedBox(
                  height: 52,
                  child: ElevatedButton(
                    onPressed: () { if (_email.text.contains('@')) setState(() => _sent = true); },
                    child: const Text('Send Reset Link', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700)),
                  ),
                ),
              ]),
            ),
          ]),
        ),
      ),
    );
  }
}