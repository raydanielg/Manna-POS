import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../core/auth_provider.dart';
import '../../core/api_service.dart';
import '../../shared/theme/app_theme.dart';

class LoginPage extends StatefulWidget {
  const LoginPage({super.key});
  @override State<LoginPage> createState() => _LoginPageState();
}

class _LoginPageState extends State<LoginPage> {
  final _form = GlobalKey<FormState>();
  final _email = TextEditingController();
  final _pass = TextEditingController();
  bool _showPass = false;
  bool _remember = false;
  String? _error;

  @override
  void initState() {
    super.initState();
    final auth = context.read<AuthProvider>();
    _remember = auth.rememberMe;
    if (auth.savedEmail != null) {
      _email.text = auth.savedEmail!;
    }
  }

  @override
  void dispose() { _email.dispose(); _pass.dispose(); super.dispose(); }

  Future<void> _login() async {
    if (!_form.currentState!.validate()) return;
    setState(() => _error = null);
    try {
      await context.read<AuthProvider>().login(_email.text.trim(), _pass.text, remember: _remember);
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
      body: Container(
        decoration: const BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
            colors: [Color(0xFFF8FAFC), Color(0xFFF1F5F9)],
          ),
        ),
        child: Stack(
          children: [
            Positioned.fill(
              child: CustomPaint(
                painter: _FintechBackgroundPainter(),
              ),
            ),
            SafeArea(
              child: SingleChildScrollView(
                padding: const EdgeInsets.all(24),
                child: Column(crossAxisAlignment: CrossAxisAlignment.stretch, children: [
            const SizedBox(height: 60),
            const Text('👋 Welcome back', style: TextStyle(fontSize: 32, fontWeight: FontWeight.w800, color: AppColors.textPri)),
            const SizedBox(height: 8),
            const Text('Sign in to your account to continue your journey with us.', style: TextStyle(color: AppColors.textSec, fontSize: 15, height: 1.5)),
            const SizedBox(height: 48),
            if (_error != null) Container(
              padding: const EdgeInsets.all(14),
              margin: const EdgeInsets.only(bottom: 20),
              decoration: BoxDecoration(color: AppColors.dangerLt, borderRadius: BorderRadius.circular(10), border: Border.all(color: AppColors.danger.withValues(alpha: 0.2))),
              child: Row(children: [const Icon(Icons.error_outline, color: AppColors.danger, size: 18), const SizedBox(width: 8), Expanded(child: Text(_error!, style: const TextStyle(color: AppColors.danger, fontSize: 13)))]),
            ),
            Form(key: _form, child: Column(crossAxisAlignment: CrossAxisAlignment.stretch, children: [
              const Text('Email address', style: TextStyle(color: AppColors.textPri, fontSize: 14, fontWeight: FontWeight.w600)),
              const SizedBox(height: 8),
              TextFormField(
                controller: _email,
                keyboardType: TextInputType.emailAddress,
                decoration: InputDecoration(
                  hintText: 'Enter your email',
                  hintStyle: TextStyle(color: AppColors.textSec.withValues(alpha: 0.5)),
                  prefixIcon: const Icon(Icons.email_outlined, color: AppColors.primary, size: 20),
                  filled: true,
                  fillColor: Colors.white,
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: AppColors.line)),
                  enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: AppColors.line)),
                  focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: AppColors.primary, width: 2)),
                  errorBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: AppColors.danger)),
                  focusedErrorBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: AppColors.danger, width: 2)),
                  contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 18),
                ),
                validator: (v) => v!.contains('@') ? null : 'Enter a valid email',
              ),
              const SizedBox(height: 20),
              const Text('Password', style: TextStyle(color: AppColors.textPri, fontSize: 14, fontWeight: FontWeight.w600)),
              const SizedBox(height: 8),
              TextFormField(
                controller: _pass,
                obscureText: !_showPass,
                decoration: InputDecoration(
                  hintText: 'Enter your password',
                  hintStyle: TextStyle(color: AppColors.textSec.withValues(alpha: 0.5)),
                  prefixIcon: const Icon(Icons.lock_outlined, color: AppColors.primary, size: 20),
                  suffixIcon: IconButton(
                    icon: Icon(_showPass ? Icons.visibility_off_outlined : Icons.visibility_outlined, color: AppColors.textSec, size: 20),
                    onPressed: () => setState(() => _showPass = !_showPass),
                  ),
                  filled: true,
                  fillColor: Colors.white,
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: AppColors.line)),
                  enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: AppColors.line)),
                  focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: AppColors.primary, width: 2)),
                  errorBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: AppColors.danger)),
                  focusedErrorBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: AppColors.danger, width: 2)),
                  contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 18),
                ),
                validator: (v) => v!.length >= 1 ? null : 'Enter your password',
              ),
              const SizedBox(height: 16),
              Row(
                children: [
                  SizedBox(
                    width: 20,
                    height: 20,
                    child: Checkbox(
                      value: _remember,
                      onChanged: (v) => setState(() => _remember = v ?? false),
                      activeColor: AppColors.primary,
                      materialTapTargetSize: MaterialTapTargetSize.shrinkWrap,
                      visualDensity: VisualDensity.compact,
                    ),
                  ),
                  const SizedBox(width: 8),
                  const Text('Remember me', style: TextStyle(color: AppColors.textSec, fontSize: 14)),
                ],
              ),
              const SizedBox(height: 24),
              SizedBox(
                height: 52,
                child: ElevatedButton(
                  onPressed: loading ? null : _login,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: AppColors.primary,
                    disabledBackgroundColor: AppColors.textSec.withValues(alpha: 0.3),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                  ),
                  child: loading ? const SizedBox(width: 22, height: 22, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5))
                      : const Text('Sign in', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700, color: Colors.white)),
                ),
              ),
              const SizedBox(height: 16),
              Center(
                child: TextButton(
                  onPressed: () => Navigator.pushNamed(context, '/forgot-password'),
                  child: const Text('Forgot Your Password?', style: TextStyle(color: AppColors.primary, fontWeight: FontWeight.w600)),
                ),
              ),
            ])),
            const SizedBox(height: 32),
            Row(mainAxisAlignment: MainAxisAlignment.center, children: [
              const Text('Don\'t have an account? ', style: TextStyle(color: AppColors.textSec)),
              GestureDetector(
                onTap: () => Navigator.pushReplacementNamed(context, '/register'),
                child: const Text('Create account', style: TextStyle(color: AppColors.primary, fontWeight: FontWeight.w700)),
              ),
            ]),
          ]),
        ),
            ),
          ],
        ),
      ),
    );
  }
}

class _FintechBackgroundPainter extends CustomPainter {
  @override
  void paint(Canvas canvas, Size size) {
    final paint = Paint()
      ..color = const Color(0xFFE2E8F0).withOpacity(0.3)
      ..strokeWidth = 1.0
      ..style = PaintingStyle.stroke;

    final paint2 = Paint()
      ..color = const Color(0xFFCBD5E1).withOpacity(0.2)
      ..strokeWidth = 0.5
      ..style = PaintingStyle.stroke;

    final paint3 = Paint()
      ..color = const Color(0xFF94A3B8).withOpacity(0.1)
      ..strokeWidth = 0.3
      ..style = PaintingStyle.stroke;

    for (int i = 0; i < size.width; i += 40) {
      canvas.drawLine(Offset(i.toDouble(), 0), Offset(i.toDouble(), size.height), paint);
    }

    for (int i = 0; i < size.height; i += 40) {
      canvas.drawLine(Offset(0, i.toDouble()), Offset(size.width, i.toDouble()), paint);
    }

    for (int i = 0; i < size.width; i += 80) {
      canvas.drawLine(Offset(i.toDouble(), 0), Offset(i.toDouble() + 40, size.height), paint2);
    }

    for (int i = 0; i < size.height; i += 80) {
      canvas.drawLine(Offset(0, i.toDouble()), Offset(size.width, i.toDouble() + 40), paint2);
    }

    for (int i = 0; i < size.width; i += 120) {
      canvas.drawLine(Offset(i.toDouble(), 0), Offset(i.toDouble() - 60, size.height), paint3);
    }

    for (int i = 0; i < size.height; i += 120) {
      canvas.drawLine(Offset(0, i.toDouble()), Offset(size.width, i.toDouble() - 60), paint3);
    }
  }

  @override
  bool shouldRepaint(covariant CustomPainter oldDelegate) => false;
}