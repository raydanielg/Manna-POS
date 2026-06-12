import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../core/auth_provider.dart';
import '../../core/api_service.dart';
import '../../shared/theme/app_theme.dart';

class RegisterPage extends StatefulWidget {
  const RegisterPage({super.key});
  @override State<RegisterPage> createState() => _RegisterPageState();
}

class _RegisterPageState extends State<RegisterPage> {
  final _form = GlobalKey<FormState>();
  final _name = TextEditingController();
  final _email = TextEditingController();
  final _pass = TextEditingController();
  bool _showPass = false;
  String? _error;

  @override
  void dispose() { _name.dispose(); _email.dispose(); _pass.dispose(); super.dispose(); }

  Future<void> _register() async {
    if (!_form.currentState!.validate()) return;
    setState(() => _error = null);
    try {
      await context.read<AuthProvider>().register(_name.text.trim(), _email.text.trim(), _pass.text);
      if (mounted) Navigator.pushReplacementNamed(context, '/dashboard');
    } on ApiException catch (e) {
      setState(() => _error = e.message);
    } catch (e) {
      setState(() => _error = 'Connection error. Check your network.');
    }
  }

  @override
  Widget build(BuildContext context) {
    final loading = context.watch<AuthProvider>().loading;
    return Scaffold(
      backgroundColor: AppColors.bg,
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(24),
          child: Column(children: [
            const SizedBox(height: 20),
            Row(children: [IconButton(
              icon: const Icon(Icons.arrow_back, color: AppColors.textPri),
              onPressed: () => Navigator.pop(context),
            )]),
            const SizedBox(height: 24),
            Center(
              child: TweenAnimationBuilder<double>(
                tween: Tween(begin: 0.0, end: 1.0),
                duration: const Duration(milliseconds: 600),
                curve: Curves.easeOut,
                builder: (_, value, child) => Transform.scale(
                  scale: 0.8 + (0.2 * value),
                  child: Opacity(opacity: value, child: child),
                ),
                child: Container(
                  width: 72, height: 72,
                  decoration: BoxDecoration(
                    color: AppColors.primaryLt,
                    borderRadius: BorderRadius.circular(20),
                    border: Border.all(color: AppColors.line, width: 1),
                  ),
                  child: const Icon(Icons.point_of_sale, size: 36, color: AppColors.primary),
                ),
              ),
            ),
            const SizedBox(height: 20),
            const Text('Create Account', style: TextStyle(fontSize: 26, fontWeight: FontWeight.w800, color: AppColors.textPri)),
            const SizedBox(height: 32),
            Container(
              padding: const EdgeInsets.all(28),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(16),
                border: Border.all(color: AppColors.line, width: 1),
                boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.03), blurRadius: 20, offset: const Offset(0, 4))],
              ),
              child: Form(key: _form, child: Column(crossAxisAlignment: CrossAxisAlignment.stretch, children: [
                if (_error != null) ...[
                  Container(
                    padding: const EdgeInsets.all(12),
                    decoration: BoxDecoration(color: AppColors.dangerLt, borderRadius: BorderRadius.circular(10), border: Border.all(color: AppColors.danger.withValues(alpha: 0.2))),
                    child: Text(_error!, style: const TextStyle(color: AppColors.danger, fontSize: 13)),
                  ),
                  const SizedBox(height: 16),
                ],
                TextFormField(
                  controller: _name,
                  decoration: const InputDecoration(labelText: 'Full Name', prefixIcon: Icon(Icons.person_outline)),
                  validator: (v) => v!.length >= 2 ? null : 'Enter your name',
                ),
                const SizedBox(height: 16),
                TextFormField(
                  controller: _email,
                  keyboardType: TextInputType.emailAddress,
                  decoration: const InputDecoration(labelText: 'Email Address', prefixIcon: Icon(Icons.email_outlined)),
                  validator: (v) => v!.contains('@') ? null : 'Enter a valid email',
                ),
                const SizedBox(height: 16),
                TextFormField(
                  controller: _pass,
                  obscureText: !_showPass,
                  decoration: InputDecoration(
                    labelText: 'Password',
                    prefixIcon: const Icon(Icons.lock_outlined),
                    suffixIcon: IconButton(
                      icon: Icon(_showPass ? Icons.visibility_off_outlined : Icons.visibility_outlined, color: AppColors.textSec),
                      onPressed: () => setState(() => _showPass = !_showPass),
                    ),
                  ),
                  validator: (v) => v!.length >= 8 ? null : 'Min 8 characters',
                ),
                const SizedBox(height: 24),
                SizedBox(
                  height: 52,
                  child: ElevatedButton(
                    onPressed: loading ? null : _register,
                    child: loading ? const SizedBox(width: 22, height: 22, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5))
                        : const Text('Create Account', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700)),
                  ),
                ),
                const SizedBox(height: 20),
                Row(mainAxisAlignment: MainAxisAlignment.center, children: [
                  const Text('Already have an account? ', style: TextStyle(color: AppColors.textSec)),
                  GestureDetector(
                    onTap: () => Navigator.pushReplacementNamed(context, '/login'),
                    child: const Text('Sign In', style: TextStyle(color: AppColors.primary, fontWeight: FontWeight.w700)),
                  ),
                ]),
              ])),
            ),
            const SizedBox(height: 32),
          ]),
        ),
      ),
    );
  }
}