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
      body: Container(
        decoration: const BoxDecoration(gradient: LinearGradient(begin: Alignment.topLeft, end: Alignment.bottomRight, colors: [Color(0xFF1D4ED8), Color(0xFF2563EB), Color(0xFF7C3AED)])),
        child: SafeArea(child: Padding(padding: const EdgeInsets.all(24), child: Column(children: [
          Row(children: [IconButton(icon: const Icon(Icons.arrow_back, color: Colors.white), onPressed: () => Navigator.pop(context))]),
          const SizedBox(height: 32),
          Container(padding: const EdgeInsets.all(28), decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(24)),
            child: _sent ? Column(children: [
              const Icon(Icons.mark_email_read_outlined, size: 64, color: AppColors.success),
              const SizedBox(height: 16),
              const Text('Email Sent!', style: TextStyle(fontSize: 22, fontWeight: FontWeight.w700)),
              const SizedBox(height: 8),
              Text('Check ${_email.text} for password reset instructions.', style: const TextStyle(color: AppColors.textSec), textAlign: TextAlign.center),
              const SizedBox(height: 24),
              SizedBox(width: double.infinity, height: 52, child: ElevatedButton(onPressed: () => Navigator.pop(context), child: const Text('Back to Login'))),
            ]) : Column(crossAxisAlignment: CrossAxisAlignment.stretch, children: [
              const Icon(Icons.lock_reset, size: 56, color: AppColors.primary),
              const SizedBox(height: 16),
              const Text('Reset Password', style: TextStyle(fontSize: 22, fontWeight: FontWeight.w700), textAlign: TextAlign.center),
              const SizedBox(height: 8),
              const Text('Enter your email to receive reset instructions', style: TextStyle(color: AppColors.textSec, fontSize: 14), textAlign: TextAlign.center),
              const SizedBox(height: 24),
              TextField(controller: _email, keyboardType: TextInputType.emailAddress, decoration: const InputDecoration(labelText: 'Email Address', prefixIcon: Icon(Icons.email_outlined))),
              const SizedBox(height: 24),
              SizedBox(height: 52, child: ElevatedButton(onPressed: () { if (_email.text.contains('@')) setState(() => _sent = true); }, child: const Text('Send Reset Link', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700)))),
            ]),
          ),
        ]))),
      ),
    );
  }
}