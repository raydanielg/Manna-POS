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
  final PageController _pageCtrl = PageController();
  int _step = 0;
  final _name = TextEditingController();
  final _email = TextEditingController();
  final _phone = TextEditingController();
  final _pass = TextEditingController();
  final _confirmPass = TextEditingController();
  bool _showPass = false;
  bool _showConfirm = false;
  String? _error;

  @override
  void dispose() {
    _pageCtrl.dispose();
    _name.dispose();
    _email.dispose();
    _phone.dispose();
    _pass.dispose();
    _confirmPass.dispose();
    super.dispose();
  }

  bool _canNext() {
    if (_step == 0) return _name.text.trim().length >= 2 && _email.text.contains('@');
    if (_step == 1) return _phone.text.trim().length >= 10 && _pass.text.length >= 8 && _pass.text == _confirmPass.text;
    return true;
  }

  void _next() {
    if (!_canNext()) return;
    if (_step < 2) {
      _pageCtrl.nextPage(duration: const Duration(milliseconds: 300), curve: Curves.easeInOut);
      setState(() => _step++);
    } else {
      _register();
    }
  }

  void _prev() {
    if (_step > 0) {
      _pageCtrl.previousPage(duration: const Duration(milliseconds: 300), curve: Curves.easeInOut);
      setState(() => _step--);
    } else {
      Navigator.pop(context);
    }
  }

  Future<void> _register() async {
    if (!mounted) return;
    setState(() => _error = null);
    try {
      await context.read<AuthProvider>().register(_name.text.trim(), _email.text.trim(), _pass.text);
      if (mounted) Navigator.pushReplacementNamed(context, '/dashboard');
    } on ApiException catch (e) {
      if (mounted) setState(() => _error = e.message);
    } catch (e) {
      if (mounted) setState(() => _error = 'Connection error. Check your network.');
    }
  }

  @override
  Widget build(BuildContext context) {
    final loading = context.watch<AuthProvider>().loading;
    return Scaffold(
      backgroundColor: AppColors.bg,
      body: SafeArea(
        child: Column(children: [
          const SizedBox(height: 20),
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 24),
            child: Row(children: [
              IconButton(
                icon: const Icon(Icons.arrow_back, color: AppColors.textPri),
                onPressed: _prev,
              ),
              const Spacer(),
              Text('Step ${_step + 1} of 3', style: const TextStyle(color: AppColors.textSec, fontSize: 14, fontWeight: FontWeight.w600)),
              const Spacer(),
              const SizedBox(width: 48),
            ]),
          ),
          const SizedBox(height: 16),
          _stepIndicator(),
          const SizedBox(height: 24),
          Expanded(
            child: PageView(
              controller: _pageCtrl,
              physics: const NeverScrollableScrollPhysics(),
              children: [
                _step1(),
                _step2(),
                _step3(),
              ],
            ),
          ),
          Padding(
            padding: const EdgeInsets.all(24),
            child: SizedBox(
              height: 52,
              width: double.infinity,
              child: ElevatedButton(
                onPressed: loading ? null : (_canNext() ? _next : null),
                style: ElevatedButton.styleFrom(
                  backgroundColor: _canNext() ? AppColors.primary : AppColors.textSec.withValues(alpha: 0.3),
                ),
                child: loading
                    ? const SizedBox(width: 22, height: 22, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5))
                    : Text(_step == 2 ? 'Create Account' : 'Continue', style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w700)),
              ),
            ),
          ),
        ]),
      ),
    );
  }

  Widget _stepIndicator() {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 24),
      child: Row(children: [
        _dot(0),
        Expanded(child: _line(0)),
        _dot(1),
        Expanded(child: _line(1)),
        _dot(2),
      ]),
    );
  }

  Widget _dot(int i) {
    final active = _step >= i;
    return Container(
      width: 28,
      height: 28,
      decoration: BoxDecoration(
        color: active ? AppColors.primary : AppColors.line,
        shape: BoxShape.circle,
        border: Border.all(color: active ? AppColors.primary : AppColors.line, width: 2),
      ),
      child: Center(
        child: Text('${i + 1}', style: TextStyle(color: active ? Colors.white : AppColors.textSec, fontSize: 12, fontWeight: FontWeight.w700)),
      ),
    );
  }

  Widget _line(int i) {
    final active = _step > i;
    return Container(
      height: 2,
      margin: const EdgeInsets.symmetric(horizontal: 8),
      decoration: BoxDecoration(color: active ? AppColors.primary : AppColors.line),
    );
  }

  Widget _step1() {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 24),
      child: SingleChildScrollView(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
        Center(
          child: Container(
            width: 80, height: 80,
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(16),
              border: Border.all(color: AppColors.line, width: 1),
              boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 15, offset: const Offset(0, 4))],
            ),
            padding: const EdgeInsets.all(12),
            child: Image.asset('assets/icons/app_logo.png', fit: BoxFit.contain),
          ),
        ),
        const SizedBox(height: 24),
        const Text('Let\'s get started', style: TextStyle(fontSize: 28, fontWeight: FontWeight.w800, color: AppColors.textPri)),
        const SizedBox(height: 8),
        const Text('First, tell us your name and email', style: TextStyle(color: AppColors.textSec, fontSize: 15)),
        const SizedBox(height: 32),
        Container(
          padding: const EdgeInsets.all(24),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(16),
            border: Border.all(color: AppColors.line, width: 1),
            boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.03), blurRadius: 20, offset: const Offset(0, 4))],
          ),
          child: Column(children: [
            TextFormField(
              controller: _name,
              decoration: const InputDecoration(labelText: 'Full Name', prefixIcon: Icon(Icons.person_outline)),
              onChanged: (_) => setState(() {}),
            ),
            const SizedBox(height: 16),
            TextFormField(
              controller: _email,
              keyboardType: TextInputType.emailAddress,
              decoration: const InputDecoration(labelText: 'Email Address', prefixIcon: Icon(Icons.email_outlined)),
              onChanged: (_) => setState(() {}),
            ),
          ]),
        ),
      ])),
    );
  }

  Widget _step2() {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 24),
      child: SingleChildScrollView(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
        const Text('Contact Info', style: TextStyle(fontSize: 28, fontWeight: FontWeight.w800, color: AppColors.textPri)),
        const SizedBox(height: 8),
        const Text('Add your phone number and set a password', style: TextStyle(color: AppColors.textSec, fontSize: 15)),
        const SizedBox(height: 32),
        Container(
          padding: const EdgeInsets.all(24),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(16),
            border: Border.all(color: AppColors.line, width: 1),
            boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.03), blurRadius: 20, offset: const Offset(0, 4))],
          ),
          child: Column(children: [
            TextFormField(
              controller: _phone,
              keyboardType: TextInputType.phone,
              decoration: const InputDecoration(labelText: 'Phone Number', prefixIcon: Icon(Icons.phone_outlined)),
              onChanged: (_) => setState(() {}),
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
              onChanged: (_) => setState(() {}),
            ),
            const SizedBox(height: 16),
            TextFormField(
              controller: _confirmPass,
              obscureText: !_showConfirm,
              decoration: InputDecoration(
                labelText: 'Confirm Password',
                prefixIcon: const Icon(Icons.lock_outlined),
                suffixIcon: IconButton(
                  icon: Icon(_showConfirm ? Icons.visibility_off_outlined : Icons.visibility_outlined, color: AppColors.textSec),
                  onPressed: () => setState(() => _showConfirm = !_showConfirm),
                ),
                errorText: _pass.text.isNotEmpty && _pass.text != _confirmPass.text ? 'Passwords do not match' : null,
              ),
              onChanged: (_) => setState(() {}),
            ),
          ]),
        ),
      ])),
    );
  }

  Widget _step3() {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 24),
      child: SingleChildScrollView(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
        const Text('Review & Create', style: TextStyle(fontSize: 28, fontWeight: FontWeight.w800, color: AppColors.textPri)),
        const SizedBox(height: 8),
        const Text('Review your information before creating account', style: TextStyle(color: AppColors.textSec, fontSize: 15)),
        const SizedBox(height: 32),
        if (_error != null) Container(
          padding: const EdgeInsets.all(12),
          margin: const EdgeInsets.only(bottom: 16),
          decoration: BoxDecoration(color: AppColors.dangerLt, borderRadius: BorderRadius.circular(10), border: Border.all(color: AppColors.danger.withValues(alpha: 0.2))),
          child: Row(children: [const Icon(Icons.error_outline, color: AppColors.danger, size: 18), const SizedBox(width: 8), Expanded(child: Text(_error!, style: const TextStyle(color: AppColors.danger, fontSize: 13)))]),
        ),
        Container(
          padding: const EdgeInsets.all(24),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(16),
            border: Border.all(color: AppColors.line, width: 1),
            boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.03), blurRadius: 20, offset: const Offset(0, 4))],
          ),
          child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            _infoRow('Name', _name.text),
            const Divider(height: 24),
            _infoRow('Email', _email.text),
            const Divider(height: 24),
            _infoRow('Phone', _phone.text),
            const Divider(height: 24),
            _infoRow('Password', '•' * _pass.text.length),
          ]),
        ),
        const SizedBox(height: 24),
        Row(mainAxisAlignment: MainAxisAlignment.center, children: [
          const Text('Already have an account? ', style: TextStyle(color: AppColors.textSec)),
          GestureDetector(
            onTap: () => Navigator.pushReplacementNamed(context, '/login'),
            child: const Text('Sign In', style: TextStyle(color: AppColors.primary, fontWeight: FontWeight.w700)),
          ),
        ]),
      ])),
    );
  }

  Widget _infoRow(String label, String value) {
    return Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
      Text(label, style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
      const SizedBox(height: 4),
      Text(value, style: const TextStyle(color: AppColors.textPri, fontSize: 16, fontWeight: FontWeight.w600)),
    ]);
  }
}