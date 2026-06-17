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
  bool _isSwahili = false;
  bool _isDark = true;
  bool _success = false;

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
    if (_firstCtrl.text.trim().isEmpty) { _showError(_isSwahili ? 'Jina la kwanza linahitajika' : 'First name is required'); return false; }
    if (_lastCtrl.text.trim().isEmpty) { _showError(_isSwahili ? 'Jina la mwisho linahitajika' : 'Last name is required'); return false; }
    if (_phoneCtrl.text.trim().isEmpty) { _showError(_isSwahili ? 'Namba ya simu inahitajika' : 'Phone number is required'); return false; }
    if (_emailCtrl.text.trim().isEmpty) { _showError(_isSwahili ? 'Barua pepe inahitajika' : 'Email is required'); return false; }
    if (!_emailCtrl.text.contains('@')) { _showError(_isSwahili ? 'Weka barua pepe sahihi' : 'Enter a valid email address'); return false; }
    if (_passCtrl.text.length < 8) { _showError(_isSwahili ? 'Nenosiri lazima liwe na herufi 8 au zaidi' : 'Password must be at least 8 characters'); return false; }
    if (_passCtrl.text != _confirmCtrl.text) { _showError(_isSwahili ? 'Nenosiri hailingani' : 'Passwords do not match'); return false; }
    return true;
  }

  bool _validateStep2() {
    if (_businessCtrl.text.trim().isEmpty) { _showError(_isSwahili ? 'Jina la biashara linahitajika' : 'Business name is required'); return false; }
    if (_businessType == null) { _showError(_isSwahili ? 'Tafadhali chagua aina ya biashara' : 'Please select a business type'); return false; }
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
      setState(() { _success = true; });
      await Future.delayed(const Duration(milliseconds: 1600));
      if (!mounted) return;
      final user = context.read<AuthProvider>().user;
      context.go(user?.role == 'admin' ? '/admin' : '/home');
    } on ApiException catch (e) {
      setState(() => _error = e.message);
    } catch (e) {
      setState(() => _error = _isSwahili ? 'Hitilafu ya muunganisho. Angalia mtandao wako.' : 'Connection error. Check your network.');
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
    if (_pwdStrength <= 0.25) return _isSwahili ? 'Dhaifu' : 'Weak';
    if (_pwdStrength <= 0.5) return _isSwahili ? 'Wastani' : 'Fair';
    if (_pwdStrength <= 0.75) return _isSwahili ? 'Nzuri' : 'Good';
    return _isSwahili ? 'Imara' : 'Strong';
  }

  @override
  Widget build(BuildContext context) {
    // Theme values
    final Color bgColor = _isDark ? const Color(0xFF171717) : const Color(0xFFF9FAFB);
    final Color cardBg = _isDark ? const Color(0xFF262626) : Colors.white;
    final Color borderColor = _isDark ? const Color(0xFF3F3F46) : const Color(0xFFE4E4E7);
    final Color textPrimary = _isDark ? Colors.white : const Color(0xFF111827);
    final Color textSecondary = _isDark ? const Color(0xFFA1A1AA) : const Color(0xFF4B5563);
    final Color labelColor = _isDark ? const Color(0xFFE4E4E7) : const Color(0xFF374151);
    final Color inputTextColor = _isDark ? Colors.white : const Color(0xFF111827);
    final IconData themeIcon = _isDark ? Icons.wb_sunny_outlined : Icons.dark_mode_outlined;

    // Language values
    final String flagText = _isSwahili ? '🇹🇿' : '🇬🇧';
    final String langText = _isSwahili ? 'SW' : 'EN';
    final String welcomeTitle = _isSwahili ? 'Fungua Akaunti 👋' : 'Create Account 👋';
    final String welcomeSubtitle = _isSwahili ? 'Fungua akaunti yako ya bure ya Manna' : 'Create your free Manna account';
    final String alreadyHaveAccountText = _isSwahili ? 'Umeshakuwa na akaunti?' : 'Already have an account?';
    final String signInText = _isSwahili ? 'Ingia' : 'Sign In';

    return Scaffold(
      backgroundColor: bgColor,
      body: Stack(
        children: [
          SafeArea(
            child: Align(
              alignment: Alignment.topCenter,
              child: SingleChildScrollView(
                padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 16),
                child: Container(
                  constraints: const BoxConstraints(maxWidth: 420),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      // Top buttons Row
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          // Language Selector Capsule Toggle
                          GestureDetector(
                            onTap: () => setState(() => _isSwahili = !_isSwahili),
                            child: Container(
                              padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
                              decoration: BoxDecoration(
                                color: cardBg,
                                borderRadius: BorderRadius.circular(20),
                                border: Border.all(color: borderColor),
                              ),
                              child: Row(
                                mainAxisSize: MainAxisSize.min,
                                children: [
                                  Text(flagText, style: const TextStyle(fontSize: 14)),
                                  const SizedBox(width: 8),
                                  Text(
                                    langText,
                                    style: TextStyle(color: textPrimary, fontSize: 13, fontWeight: FontWeight.bold),
                                  ),
                                ],
                              ),
                            ),
                          ),
                          // Theme Toggle Square
                          GestureDetector(
                            onTap: () => setState(() => _isDark = !_isDark),
                            child: Container(
                              width: 40,
                              height: 40,
                              decoration: BoxDecoration(
                                color: cardBg,
                                borderRadius: BorderRadius.circular(12),
                                border: Border.all(color: borderColor),
                              ),
                              child: Icon(themeIcon, color: textPrimary, size: 20),
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 24),

                      // Step indicator Row
                      Row(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          _stepCircle(1, _isSwahili ? 'Akaunti' : 'Account', _step >= 0, textPrimary, textSecondary),
                          Container(
                            width: 40,
                            height: 2,
                            margin: const EdgeInsets.symmetric(horizontal: 8),
                            color: _step >= 1 ? AppColors.primary : borderColor,
                          ),
                          _stepCircle(2, _isSwahili ? 'Biashara' : 'Business', _step >= 1, textPrimary, textSecondary),
                        ],
                      ),
                      const SizedBox(height: 28),

                      // Header Texts
                      Text(
                        welcomeTitle,
                        style: TextStyle(
                          fontSize: 28,
                          fontWeight: FontWeight.w800,
                          color: textPrimary,
                          letterSpacing: -0.5,
                        ),
                      ),
                      const SizedBox(height: 6),
                      Text(
                        welcomeSubtitle,
                        style: TextStyle(
                          fontSize: 14,
                          color: textSecondary,
                        ),
                      ),
                      const SizedBox(height: 24),

                      // Error Banner
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

                      // Form content wrapped in AnimatedSwitcher
                      AnimatedSwitcher(
                        duration: const Duration(milliseconds: 200),
                        child: _step == 0
                            ? _buildStep1(key: const ValueKey(0), labelColor: labelColor, textColor: inputTextColor, cardBg: cardBg, borderColor: borderColor)
                            : _buildStep2(key: const ValueKey(1), labelColor: labelColor, textColor: inputTextColor, cardBg: cardBg, borderColor: borderColor),
                      ),
                      const SizedBox(height: 28),

                      // Buttons Row
                      Row(
                        children: [
                          if (_step > 0) ...[
                            Expanded(
                              child: SizedBox(
                                height: 52,
                                child: OutlinedButton(
                                  onPressed: _goBack,
                                  style: OutlinedButton.styleFrom(
                                    side: BorderSide(color: borderColor, width: 1.5),
                                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                                  ),
                                  child: Text(
                                    _isSwahili ? '← Nyuma' : '← Back',
                                    style: TextStyle(color: textPrimary, fontWeight: FontWeight.w600, fontSize: 14),
                                  ),
                                ),
                              ),
                            ),
                            const SizedBox(width: 12),
                          ],
                          Expanded(
                            flex: 2,
                            child: SizedBox(
                              height: 52,
                              child: ElevatedButton(
                                onPressed: _loading ? null : (_step == 0 ? _goNext : _register),
                                style: ElevatedButton.styleFrom(
                                  backgroundColor: AppColors.primary,
                                  foregroundColor: Colors.white,
                                  disabledBackgroundColor: AppColors.primary.withValues(alpha: 0.5),
                                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                                  elevation: 0,
                                ),
                                child: Text(
                                  _step == 0
                                      ? (_isSwahili ? 'Endelea →' : 'Continue →')
                                      : (_isSwahili ? 'Fungua Akaunti' : 'Create Account'),
                                  style: const TextStyle(fontSize: 15, fontWeight: FontWeight.w700),
                                ),
                              ),
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 24),

                      // Footer already have account?
                      Row(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Text(
                            alreadyHaveAccountText,
                            style: TextStyle(fontSize: 14, color: textSecondary),
                          ),
                          const SizedBox(width: 6),
                          TextButton(
                            onPressed: () => context.go('/login'),
                            style: TextButton.styleFrom(
                              padding: EdgeInsets.zero,
                              minimumSize: const Size(0, 0),
                            ),
                            child: Text(
                              signInText,
                              style: const TextStyle(
                                fontSize: 14,
                                fontWeight: FontWeight.w700,
                                color: AppColors.primary,
                              ),
                            ),
                          ),
                        ],
                      ),
                    ],
                  ),
                ),
              ),
            ),
          ),
          // Full-Page loading overlay
          if (_loading && !_success)
            Container(
              color: Colors.black.withValues(alpha: 0.75),
              child: Center(
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    const CircularProgressIndicator(color: AppColors.primary, strokeWidth: 3.5),
                    const SizedBox(height: 20),
                    Text(
                      _isSwahili ? 'Inasajili...' : 'Registering...',
                      style: const TextStyle(
                        color: Colors.white,
                        fontSize: 16,
                        fontWeight: FontWeight.w600,
                        letterSpacing: 0.2,
                      ),
                    ),
                  ],
                ),
              ),
            ),
          // SweetAlert style Success alert popup overlay
          if (_success)
            Container(
              color: Colors.black.withValues(alpha: 0.8),
              child: Center(
                child: AnimatedContainer(
                  duration: const Duration(milliseconds: 300),
                  curve: Curves.easeOutBack,
                  padding: const EdgeInsets.all(32),
                  margin: const EdgeInsets.symmetric(horizontal: 40),
                  decoration: BoxDecoration(
                    color: _isDark ? const Color(0xFF262626) : Colors.white,
                    borderRadius: BorderRadius.circular(20),
                    boxShadow: [
                      BoxShadow(
                        color: Colors.black.withValues(alpha: 0.3),
                        blurRadius: 30,
                        offset: const Offset(0, 15),
                      ),
                    ],
                  ),
                  child: Column(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Container(
                        width: 72,
                        height: 72,
                        decoration: const BoxDecoration(
                          color: Color(0xFF10B981),
                          shape: BoxShape.circle,
                        ),
                        child: const Icon(
                          Icons.check_rounded,
                          color: Colors.white,
                          size: 44,
                        ),
                      ),
                      const SizedBox(height: 24),
                      Text(
                        _isSwahili ? 'Umefanikiwa Kusajili!' : 'Success!',
                        style: TextStyle(
                          fontSize: 22,
                          fontWeight: FontWeight.w800,
                          color: textPrimary,
                        ),
                      ),
                      const SizedBox(height: 10),
                      Text(
                        _isSwahili ? 'Biashara yako imewekwa tayari...' : 'Your business has been set up...',
                        textAlign: TextAlign.center,
                        style: TextStyle(
                          fontSize: 14,
                          color: textSecondary,
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ),
        ],
      ),
    );
  }

  Widget _stepCircle(int number, String label, bool active, Color textPrimary, Color textSecondary) {
    return Row(
      mainAxisSize: MainAxisSize.min,
      children: [
        Container(
          width: 24,
          height: 24,
          decoration: BoxDecoration(
            color: active ? AppColors.primary : Colors.transparent,
            border: Border.all(color: active ? AppColors.primary : textSecondary, width: 2),
            shape: BoxShape.circle,
          ),
          child: Center(
            child: Text(
              '$number',
              style: TextStyle(
                fontSize: 12,
                fontWeight: FontWeight.bold,
                color: active ? Colors.white : textSecondary,
              ),
            ),
          ),
        ),
        const SizedBox(width: 8),
        Text(
          label,
          style: TextStyle(
            fontSize: 13,
            fontWeight: active ? FontWeight.bold : FontWeight.normal,
            color: active ? textPrimary : textSecondary,
          ),
        ),
      ],
    );
  }

  Widget _buildStep1({required Key key, required Color labelColor, required Color textColor, required Color cardBg, required Color borderColor}) {
    return Column(
      key: key,
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          children: [
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  _label(_isSwahili ? 'Jina la Kwanza *' : 'First Name *', labelColor),
                  const SizedBox(height: 8),
                  TextField(
                    controller: _firstCtrl,
                    style: _ts(textColor),
                    decoration: _inputDeco(_isSwahili ? 'Mf. John' : 'John', Icons.person_outline, cardBg, borderColor),
                  ),
                ],
              ),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  _label(_isSwahili ? 'Jina la Mwisho *' : 'Last Name *', labelColor),
                  const SizedBox(height: 8),
                  TextField(
                    controller: _lastCtrl,
                    style: _ts(textColor),
                    decoration: _inputDeco(_isSwahili ? 'Mf. Doe' : 'Doe', null, cardBg, borderColor),
                  ),
                ],
              ),
            ),
          ],
        ),
        const SizedBox(height: 16),

        _label(_isSwahili ? 'Namba ya Simu *' : 'Phone Number *', labelColor),
        const SizedBox(height: 8),
        TextField(
          controller: _phoneCtrl,
          keyboardType: TextInputType.phone,
          style: _ts(textColor),
          decoration: _inputDeco('+255 7XX XXX XXX', Icons.phone_outlined, cardBg, borderColor),
        ),
        const SizedBox(height: 16),

        _label(_isSwahili ? 'Anwani ya Barua Pepe *' : 'Email Address *', labelColor),
        const SizedBox(height: 8),
        TextField(
          controller: _emailCtrl,
          keyboardType: TextInputType.emailAddress,
          style: _ts(textColor),
          decoration: _inputDeco('name@company.com', Icons.email_outlined, cardBg, borderColor),
        ),
        const SizedBox(height: 16),

        _label(_isSwahili ? 'Nenosiri *' : 'Password *', labelColor),
        const SizedBox(height: 8),
        TextField(
          controller: _passCtrl,
          obscureText: _obscure,
          style: _ts(textColor),
          onChanged: _calcStrength,
          decoration: _inputDeco(_isSwahili ? 'Herufi 8 au zaidi' : 'Min. 8 characters', Icons.lock_outlined, cardBg, borderColor).copyWith(
            suffixIcon: IconButton(
              icon: Icon(
                _obscure ? Icons.visibility_outlined : Icons.visibility_off_outlined,
                color: textSecondaryColor,
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
                    backgroundColor: borderColor,
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
        const SizedBox(height: 16),

        _label(_isSwahili ? 'Thibitisha Nenosiri *' : 'Confirm Password *', labelColor),
        const SizedBox(height: 8),
        TextField(
          controller: _confirmCtrl,
          obscureText: _obscure2,
          style: _ts(textColor),
          decoration: _inputDeco(_isSwahili ? 'Rudia nenosiri' : 'Re-enter password', Icons.lock_outlined, cardBg, borderColor).copyWith(
            suffixIcon: IconButton(
              icon: Icon(
                _obscure2 ? Icons.visibility_outlined : Icons.visibility_off_outlined,
                color: textSecondaryColor,
                size: 20,
              ),
              onPressed: () => setState(() => _obscure2 = !_obscure2),
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildStep2({required Key key, required Color labelColor, required Color textColor, required Color cardBg, required Color borderColor}) {
    return Column(
      key: key,
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        // Free trial badge
        Container(
          padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
          decoration: BoxDecoration(
            color: AppColors.primaryLt,
            borderRadius: BorderRadius.circular(12),
            border: Border.all(color: AppColors.primary.withValues(alpha: 0.3)),
          ),
          child: Row(
            children: [
              const Icon(Icons.star_rounded, color: AppColors.primary, size: 18),
              const SizedBox(width: 8),
              Expanded(
                child: Text(
                  _isSwahili ? 'Majaribio ya siku 14 ya bure — hakuna kadi inayohitajika' : '14-day free trial — no credit card required',
                  style: const TextStyle(fontSize: 12, color: AppColors.primary, fontWeight: FontWeight.w600),
                ),
              ),
            ],
          ),
        ),
        const SizedBox(height: 18),

        _label(_isSwahili ? 'Jina la Biashara *' : 'Business Name *', labelColor),
        const SizedBox(height: 8),
        TextField(
          controller: _businessCtrl,
          style: _ts(textColor),
          decoration: _inputDeco(_isSwahili ? 'Mf. Duka Langu' : 'My Store', Icons.store_outlined, cardBg, borderColor),
        ),
        const SizedBox(height: 16),

        _label(_isSwahili ? 'Aina ya Biashara *' : 'Business Type *', labelColor),
        const SizedBox(height: 10),
        Wrap(
          spacing: 8,
          runSpacing: 8,
          children: ['retail', 'wholesale', 'restaurant', 'service', 'other'].map((t) {
            final sel = _businessType == t;
            String label = t[0].toUpperCase() + t.substring(1);
            if (_isSwahili) {
              if (t == 'retail') label = 'Rejareja';
              if (t == 'wholesale') label = 'Jumla';
              if (t == 'restaurant') label = 'Mgahawa';
              if (t == 'service') label = 'Huduma';
              if (t == 'other') label = 'Nyingine';
            }
            return GestureDetector(
              onTap: () => setState(() => _businessType = t),
              child: Container(
                padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
                decoration: BoxDecoration(
                  color: sel ? AppColors.primary : cardBg,
                  borderRadius: BorderRadius.circular(20),
                  border: Border.all(color: sel ? AppColors.primary : borderColor),
                ),
                child: Text(
                  label,
                  style: TextStyle(
                    fontSize: 13,
                    color: sel ? Colors.white : textSecondaryColor,
                    fontWeight: sel ? FontWeight.w700 : FontWeight.w400,
                  ),
                ),
              ),
            );
          }).toList(),
        ),
        const SizedBox(height: 16),

        _label(_isSwahili ? 'Nchi' : 'Country', labelColor),
        const SizedBox(height: 8),
        DropdownButtonFormField<String>(
          value: _country,
          dropdownColor: cardBg,
          style: _ts(textColor),
          decoration: _inputDeco(_isSwahili ? 'Chagua nchi' : 'Select country', Icons.location_on_outlined, cardBg, borderColor),
          items: ['Tanzania', 'Kenya', 'Uganda', 'Rwanda', 'Ethiopia', 'Nigeria', 'Ghana', 'Other']
              .map((c) => DropdownMenuItem(value: c, child: Text(c, style: TextStyle(color: textColor))))
              .toList(),
          onChanged: (v) => setState(() => _country = v ?? 'Tanzania'),
        ),
        const SizedBox(height: 16),

        _label(_isSwahili ? 'Sarafu' : 'Currency', labelColor),
        const SizedBox(height: 8),
        DropdownButtonFormField<String>(
          value: _currency,
          dropdownColor: cardBg,
          style: _ts(textColor),
          decoration: _inputDeco(_isSwahili ? 'Chagua sarafu' : 'Select currency', Icons.monetization_on_outlined, cardBg, borderColor),
          items: ['TZS', 'USD', 'EUR', 'KES', 'UGX', 'RWF']
              .map((c) => DropdownMenuItem(value: c, child: Text(c, style: TextStyle(color: textColor))))
              .toList(),
          onChanged: (v) => setState(() => _currency = v ?? 'TZS'),
        ),
      ],
    );
  }

  Widget _label(String text, Color color) => Text(
        text,
        style: TextStyle(fontSize: 14, fontWeight: FontWeight.w500, color: color),
      );

  TextStyle _ts(Color color) => TextStyle(fontSize: 15, color: color);

  Color get textSecondaryColor => _isDark ? const Color(0xFFA1A1AA) : const Color(0xFF4B5563);

  InputDecoration _inputDeco(String hint, IconData? icon, Color fill, Color border) => InputDecoration(
        hintText: hint,
        hintStyle: const TextStyle(color: Color(0xFF71717A), fontSize: 14),
        prefixIcon: icon != null ? Icon(icon, color: AppColors.primary, size: 20) : null,
        filled: true,
        fillColor: fill,
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(10),
          borderSide: BorderSide(color: border),
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(10),
          borderSide: BorderSide(color: border),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(10),
          borderSide: const BorderSide(color: AppColors.primary, width: 1.5),
        ),
        contentPadding: const EdgeInsets.symmetric(vertical: 16, horizontal: 16),
      );
}
