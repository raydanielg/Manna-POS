import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:provider/provider.dart';
import '../../core/auth_provider.dart';
import '../../core/api_service.dart';
import '../../shared/theme/app_colors.dart';

class RegisterPage extends StatefulWidget {
  const RegisterPage({super.key});
  @override
  State<RegisterPage> createState() => _RegisterPageState();
}

class _RegisterPageState extends State<RegisterPage> {
  int _step = 0;
  String? _error;
  bool _loading = false;

  // Step 1 — Account Info
  final _firstCtrl = TextEditingController();
  final _lastCtrl = TextEditingController();
  final _phoneCtrl = TextEditingController();
  final _emailCtrl = TextEditingController();
  final _passCtrl = TextEditingController();
  final _confirmCtrl = TextEditingController();
  bool _obscure = true;
  bool _obscure2 = true;
  double _pwdStrength = 0;

  // Step 2 — Business Info
  final _businessCtrl = TextEditingController();
  String? _businessType;
  String _country = 'Tanzania';
  String _currency = 'TZS';

  @override
  void dispose() {
    _firstCtrl.dispose(); _lastCtrl.dispose(); _phoneCtrl.dispose();
    _emailCtrl.dispose(); _passCtrl.dispose(); _confirmCtrl.dispose();
    _businessCtrl.dispose();
    super.dispose();
  }

  bool _validateStep1() {
    if (_firstCtrl.text.trim().isEmpty) { _showError('First name is required'); return false; }
    if (_lastCtrl.text.trim().isEmpty) { _showError('Last name is required'); return false; }
    if (_phoneCtrl.text.trim().isEmpty) { _showError('Phone number is required'); return false; }
    if (_emailCtrl.text.trim().isEmpty) { _showError('Email is required'); return false; }
    if (!_emailCtrl.text.contains('@')) { _showError('Enter a valid email address'); return false; }
    if (_passCtrl.text.length < 8) { _showError('Password must be at least 8 characters'); return false; }
    if (_passCtrl.text != _confirmCtrl.text) { _showError('Passwords do not match'); return false; }
    return true;
  }

  bool _validateStep2() {
    if (_businessCtrl.text.trim().isEmpty) { _showError('Business name is required'); return false; }
    if (_businessType == null) { _showError('Please select a business type'); return false; }
    return true;
  }

  void _showError(String msg) => setState(() => _error = msg);
  void _clearError() => setState(() => _error = null);

  void _goNext() {
    _clearError();
    if (!_validateStep1()) return;
    setState(() => _step = 1);
  }

  void _goBack() {
    _clearError();
    setState(() => _step = 0);
  }

  Future<void> _register() async {
    _clearError();
    if (!_validateStep2()) return;
    setState(() => _loading = true);
    try {
      await context.read<AuthProvider>().register({
        'name': '${_firstCtrl.text.trim()} ${_lastCtrl.text.trim()}',
        'email': _emailCtrl.text.trim(),
        'password': _passCtrl.text,
        'phone': _phoneCtrl.text.trim(),
        'business_name': _businessCtrl.text.trim(),
        'business_type': _businessType,
        'business_country': _country,
        'currency': _currency,
        'tax_percentage': 18,
        'fiscal_year_start': 'January',
      });
      if (!mounted) return;
      final user = context.read<AuthProvider>().user;
      context.go(user?.role == 'admin' ? '/admin' : '/home');
    } on ApiException catch (e) {
      setState(() => _error = e.message);
    } catch (e) {
      setState(() => _error = 'Connection error. Check your network.');
    } finally {
      setState(() => _loading = false);
    }
  }

  void _calcStrength(String v) {
    double s = 0;
    if (v.length >= 8) s += 0.25;
    if (v.contains(RegExp(r'[A-Z]'))) s += 0.25;
    if (v.contains(RegExp(r'[0-9]'))) s += 0.25;
    if (v.contains(RegExp(r'[!@#\$%^&*]'))) s += 0.25;
    setState(() => _pwdStrength = s);
  }

  Color get _pwdColor {
    if (_pwdStrength <= 0.25) return AppColors.error;
    if (_pwdStrength <= 0.5) return AppColors.warning;
    if (_pwdStrength <= 0.75) return Colors.lightGreen;
    return AppColors.success;
  }

  String get _pwdLabel {
    if (_pwdStrength <= 0.25) return 'Weak';
    if (_pwdStrength <= 0.5) return 'Fair';
    if (_pwdStrength <= 0.75) return 'Good';
    return 'Strong';
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Container(
        decoration: const BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
            colors: [Color(0xFF063d2a), Color(0xFF064e3b), Color(0xFF065f46), Color(0xFF047857)],
          ),
        ),
        child: SafeArea(
          child: Center(
            child: SingleChildScrollView(
              padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 28),
              child: Column(
                children: [
                  // Logo + Brand
                  Container(
                    width: 76,
                    height: 76,
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(20),
                      boxShadow: [
                        BoxShadow(
                          color: Colors.black.withValues(alpha: 0.25),
                          blurRadius: 20,
                          offset: const Offset(0, 8),
                        ),
                      ],
                    ),
                    child: ClipRRect(
                      borderRadius: BorderRadius.circular(20),
                      child: Image.asset('assets/icons/app_logo.png', fit: BoxFit.cover),
                    ),
                  ),
                  const SizedBox(height: 12),
                  const Text(
                    'Manna',
                    style: TextStyle(fontSize: 24, fontWeight: FontWeight.w800, color: Colors.white),
                  ),
                  const SizedBox(height: 2),
                  Text(
                    'Create your free account',
                    style: TextStyle(fontSize: 13, color: Colors.white.withValues(alpha: 0.7)),
                  ),
                  const SizedBox(height: 24),

                  // Card
                  Container(
                    width: double.infinity,
                    constraints: const BoxConstraints(maxWidth: 420),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(24),
                      boxShadow: [
                        BoxShadow(
                          color: Colors.black.withValues(alpha: 0.15),
                          blurRadius: 30,
                          offset: const Offset(0, 10),
                        ),
                      ],
                    ),
                    child: Column(
                      children: [
                        // Green step header
                        Container(
                          width: double.infinity,
                          padding: const EdgeInsets.fromLTRB(24, 20, 24, 20),
                          decoration: const BoxDecoration(
                            gradient: LinearGradient(
                              colors: [Color(0xFF047857), Color(0xFF065f46)],
                              begin: Alignment.topLeft,
                              end: Alignment.bottomRight,
                            ),
                            borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
                          ),
                          child: Column(
                            children: [
                              Row(
                                mainAxisAlignment: MainAxisAlignment.center,
                                children: [
                                  _dot(0),
                                  _line(0),
                                  _dot(1),
                                ],
                              ),
                              const SizedBox(height: 10),
                              Row(
                                mainAxisAlignment: MainAxisAlignment.center,
                                children: [
                                  SizedBox(
                                    width: 90,
                                    child: Text(
                                      'Account\nInfo',
                                      textAlign: TextAlign.center,
                                      style: TextStyle(
                                        fontSize: 11,
                                        color: Colors.white.withValues(alpha: _step == 0 ? 1.0 : 0.55),
                                        fontWeight: _step == 0 ? FontWeight.w700 : FontWeight.w400,
                                      ),
                                    ),
                                  ),
                                  const SizedBox(width: 36),
                                  SizedBox(
                                    width: 90,
                                    child: Text(
                                      'Business\nInfo',
                                      textAlign: TextAlign.center,
                                      style: TextStyle(
                                        fontSize: 11,
                                        color: Colors.white.withValues(alpha: _step == 1 ? 1.0 : 0.55),
                                        fontWeight: _step == 1 ? FontWeight.w700 : FontWeight.w400,
                                      ),
                                    ),
                                  ),
                                ],
                              ),
                            ],
                          ),
                        ),

                        // Form body
                        Padding(
                          padding: const EdgeInsets.all(24),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              if (_error != null) ...[
                                Container(
                                  width: double.infinity,
                                  padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
                                  decoration: BoxDecoration(
                                    color: AppColors.error.withValues(alpha: 0.08),
                                    borderRadius: BorderRadius.circular(12),
                                    border: Border.all(color: AppColors.error.withValues(alpha: 0.3)),
                                  ),
                                  child: Row(
                                    children: [
                                      const Icon(Icons.error_outline, color: AppColors.error, size: 18),
                                      const SizedBox(width: 8),
                                      Expanded(
                                        child: Text(
                                          _error!,
                                          style: const TextStyle(color: AppColors.error, fontSize: 13, fontWeight: FontWeight.w500),
                                        ),
                                      ),
                                    ],
                                  ),
                                ),
                                const SizedBox(height: 16),
                              ],

                              AnimatedSwitcher(
                                duration: const Duration(milliseconds: 220),
                                child: _step == 0
                                    ? _buildStep1(key: const ValueKey(0))
                                    : _buildStep2(key: const ValueKey(1)),
                              ),

                              const SizedBox(height: 20),

                              Row(
                                children: [
                                  if (_step > 0) ...[
                                    Expanded(
                                      child: SizedBox(
                                        height: 50,
                                        child: OutlinedButton(
                                          onPressed: _goBack,
                                          style: OutlinedButton.styleFrom(
                                            side: const BorderSide(color: AppColors.border, width: 1.5),
                                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                                          ),
                                          child: const Text('← Back', style: TextStyle(color: AppColors.textPri, fontWeight: FontWeight.w600)),
                                        ),
                                      ),
                                    ),
                                    const SizedBox(width: 12),
                                  ],
                                  Expanded(
                                    flex: 2,
                                    child: SizedBox(
                                      height: 50,
                                      child: ElevatedButton(
                                        onPressed: _loading ? null : (_step == 0 ? _goNext : _register),
                                        style: ElevatedButton.styleFrom(
                                          backgroundColor: AppColors.primary,
                                          foregroundColor: Colors.white,
                                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                                          elevation: 0,
                                        ),
                                        child: _loading
                                            ? const SizedBox(
                                                width: 20,
                                                height: 20,
                                                child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white),
                                              )
                                            : Text(
                                                _step == 0 ? 'Continue →' : 'Create Account',
                                                style: const TextStyle(fontSize: 15, fontWeight: FontWeight.w700),
                                              ),
                                      ),
                                    ),
                                  ),
                                ],
                              ),

                              const SizedBox(height: 16),
                              Row(
                                mainAxisAlignment: MainAxisAlignment.center,
                                children: [
                                  const Text('Already have an account?', style: TextStyle(fontSize: 13, color: AppColors.textSec)),
                                  const SizedBox(width: 4),
                                  TextButton(
                                    onPressed: () => context.go('/login'),
                                    style: TextButton.styleFrom(padding: EdgeInsets.zero, minimumSize: const Size(0, 0)),
                                    child: const Text(
                                      'Sign In',
                                      style: TextStyle(fontSize: 13, fontWeight: FontWeight.w700, color: AppColors.primary),
                                    ),
                                  ),
                                ],
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),
                  ),

                  const SizedBox(height: 24),
                  Text(
                    '© 2024 MannaPOS. All rights reserved.',
                    style: TextStyle(fontSize: 11, color: Colors.white.withValues(alpha: 0.4)),
                  ),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }

  Widget _dot(int index) {
    final active = _step >= index;
    return Container(
      width: 32,
      height: 32,
      decoration: BoxDecoration(
        color: active ? Colors.white : Colors.white.withValues(alpha: 0.25),
        shape: BoxShape.circle,
      ),
      child: Center(
        child: Text(
          '${index + 1}',
          style: TextStyle(
            fontSize: 14,
            fontWeight: FontWeight.w700,
            color: active ? const Color(0xFF047857) : Colors.white,
          ),
        ),
      ),
    );
  }

  Widget _line(int afterIndex) {
    final done = _step > afterIndex;
    return Container(width: 60, height: 2, color: done ? Colors.white : Colors.white.withValues(alpha: 0.3));
  }

  Widget _buildStep1({Key? key}) {
    return Column(
      key: key,
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text('Account Information', style: TextStyle(fontSize: 18, fontWeight: FontWeight.w800, color: AppColors.textPri)),
        const SizedBox(height: 4),
        const Text('Set up your owner credentials', style: TextStyle(fontSize: 13, color: AppColors.textSec)),
        const SizedBox(height: 20),

        Row(
          children: [
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  _label('First Name *'),
                  const SizedBox(height: 6),
                  TextField(controller: _firstCtrl, style: _ts, decoration: _inputDeco('John', Icons.person_outline)),
                ],
              ),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  _label('Last Name *'),
                  const SizedBox(height: 6),
                  TextField(controller: _lastCtrl, style: _ts, decoration: _inputDeco('Doe', null)),
                ],
              ),
            ),
          ],
        ),
        const SizedBox(height: 14),

        _label('Phone Number *'),
        const SizedBox(height: 6),
        TextField(
          controller: _phoneCtrl,
          keyboardType: TextInputType.phone,
          style: _ts,
          decoration: _inputDeco('+255 7XX XXX XXX', Icons.phone_outlined),
        ),
        const SizedBox(height: 14),

        _label('Email Address *'),
        const SizedBox(height: 6),
        TextField(
          controller: _emailCtrl,
          keyboardType: TextInputType.emailAddress,
          style: _ts,
          decoration: _inputDeco('name@company.com', Icons.email_outlined),
        ),
        const SizedBox(height: 14),

        _label('Password *'),
        const SizedBox(height: 6),
        TextField(
          controller: _passCtrl,
          obscureText: _obscure,
          style: _ts,
          onChanged: _calcStrength,
          decoration: _inputDeco('Min. 8 characters', Icons.lock_outlined).copyWith(
            suffixIcon: IconButton(
              icon: Icon(
                _obscure ? Icons.visibility_outlined : Icons.visibility_off_outlined,
                color: AppColors.textSec,
                size: 20,
              ),
              onPressed: () => setState(() => _obscure = !_obscure),
            ),
          ),
        ),
        if (_passCtrl.text.isNotEmpty) ...[
          const SizedBox(height: 8),
          Row(
            children: [
              Expanded(
                child: ClipRRect(
                  borderRadius: BorderRadius.circular(4),
                  child: LinearProgressIndicator(
                    value: _pwdStrength,
                    backgroundColor: AppColors.border,
                    color: _pwdColor,
                    minHeight: 4,
                  ),
                ),
              ),
              const SizedBox(width: 8),
              Text(_pwdLabel, style: TextStyle(fontSize: 11, color: _pwdColor, fontWeight: FontWeight.w600)),
            ],
          ),
        ],
        const SizedBox(height: 14),

        _label('Confirm Password *'),
        const SizedBox(height: 6),
        TextField(
          controller: _confirmCtrl,
          obscureText: _obscure2,
          style: _ts,
          decoration: _inputDeco('Re-enter password', Icons.lock_outlined).copyWith(
            suffixIcon: IconButton(
              icon: Icon(
                _obscure2 ? Icons.visibility_outlined : Icons.visibility_off_outlined,
                color: AppColors.textSec,
                size: 20,
              ),
              onPressed: () => setState(() => _obscure2 = !_obscure2),
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildStep2({Key? key}) {
    return Column(
      key: key,
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text('Business Information', style: TextStyle(fontSize: 18, fontWeight: FontWeight.w800, color: AppColors.textPri)),
        const SizedBox(height: 4),
        const Text('Tell us about your business', style: TextStyle(fontSize: 13, color: AppColors.textSec)),
        const SizedBox(height: 16),

        // Free trial badge
        Container(
          padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
          decoration: BoxDecoration(
            color: AppColors.primaryLt,
            borderRadius: BorderRadius.circular(12),
            border: Border.all(color: AppColors.primary.withValues(alpha: 0.3)),
          ),
          child: const Row(
            children: [
              Icon(Icons.star_rounded, color: AppColors.primary, size: 18),
              SizedBox(width: 8),
              Expanded(
                child: Text(
                  '14-day free trial — no credit card required',
                  style: TextStyle(fontSize: 12, color: AppColors.primary, fontWeight: FontWeight.w600),
                ),
              ),
            ],
          ),
        ),
        const SizedBox(height: 16),

        _label('Business Name *'),
        const SizedBox(height: 6),
        TextField(
          controller: _businessCtrl,
          style: _ts,
          decoration: _inputDeco('My Store', Icons.store_outlined),
        ),
        const SizedBox(height: 14),

        _label('Business Type *'),
        const SizedBox(height: 8),
        Wrap(
          spacing: 8,
          runSpacing: 8,
          children: ['retail', 'wholesale', 'restaurant', 'service', 'other'].map((t) {
            final sel = _businessType == t;
            return GestureDetector(
              onTap: () => setState(() => _businessType = t),
              child: Container(
                padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
                decoration: BoxDecoration(
                  color: sel ? AppColors.primary : Colors.white,
                  borderRadius: BorderRadius.circular(20),
                  border: Border.all(color: sel ? AppColors.primary : AppColors.border),
                ),
                child: Text(
                  t[0].toUpperCase() + t.substring(1),
                  style: TextStyle(
                    fontSize: 13,
                    color: sel ? Colors.white : AppColors.textSec,
                    fontWeight: sel ? FontWeight.w700 : FontWeight.w400,
                  ),
                ),
              ),
            );
          }).toList(),
        ),
        const SizedBox(height: 14),

        _label('Country'),
        const SizedBox(height: 6),
        DropdownButtonFormField<String>(
          value: _country,
          style: _ts,
          decoration: _inputDeco('Select country', Icons.location_on_outlined),
          items: ['Tanzania', 'Kenya', 'Uganda', 'Rwanda', 'Ethiopia', 'Nigeria', 'Ghana', 'Other']
              .map((c) => DropdownMenuItem(value: c, child: Text(c)))
              .toList(),
          onChanged: (v) => setState(() => _country = v ?? 'Tanzania'),
        ),
        const SizedBox(height: 14),

        _label('Currency'),
        const SizedBox(height: 6),
        DropdownButtonFormField<String>(
          value: _currency,
          style: _ts,
          decoration: _inputDeco('Select currency', Icons.monetization_on_outlined),
          items: ['TZS', 'USD', 'EUR', 'KES', 'UGX', 'RWF']
              .map((c) => DropdownMenuItem(value: c, child: Text(c)))
              .toList(),
          onChanged: (v) => setState(() => _currency = v ?? 'TZS'),
        ),
      ],
    );
  }

  Widget _label(String text) => Text(
        text,
        style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w600, color: AppColors.textPri),
      );

  TextStyle get _ts => const TextStyle(fontSize: 15, color: AppColors.textPri);

  InputDecoration _inputDeco(String hint, IconData? icon) => InputDecoration(
        hintText: hint,
        hintStyle: const TextStyle(color: AppColors.textLight, fontSize: 14),
        prefixIcon: icon != null ? Icon(icon, color: AppColors.textSec, size: 20) : null,
        filled: true,
        fillColor: AppColors.background,
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: AppColors.border),
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: AppColors.border),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: AppColors.primary, width: 2),
        ),
        contentPadding: const EdgeInsets.symmetric(vertical: 14, horizontal: 16),
      );
}
