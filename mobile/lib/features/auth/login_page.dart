import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:provider/provider.dart';
import '../../core/auth_provider.dart';
import '../../core/api_service.dart';
import '../../shared/theme/app_colors.dart';

class LoginPage extends StatefulWidget {
  const LoginPage({super.key});
  @override
  State<LoginPage> createState() => _LoginPageState();
}

class _LoginPageState extends State<LoginPage> {
  final _emailCtrl = TextEditingController();
  final _passCtrl = TextEditingController();
  bool _obscure = true;
  bool _remember = false;
  String? _error;
  bool _isSwahili = false;
  bool _isDark = true;
  bool _success = false;

  @override
  void initState() {
    super.initState();
    final auth = context.read<AuthProvider>();
    if (auth.rememberMe && auth.savedEmail != null) {
      _emailCtrl.text = auth.savedEmail!;
      _remember = true;
    }
  }

  @override
  void dispose() {
    _emailCtrl.dispose();
    _passCtrl.dispose();
    super.dispose();
  }

  Future<void> _login() async {
    if (_emailCtrl.text.trim().isEmpty || _passCtrl.text.isEmpty) {
      setState(() => _error = _isSwahili ? 'Tafadhali jaza nafasi zote' : 'Please fill in all fields');
      return;
    }
    setState(() => _error = null);
    try {
      await context.read<AuthProvider>().login(
        _emailCtrl.text.trim(), _passCtrl.text,
        remember: _remember,
      );
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
    }
  }

  @override
  Widget build(BuildContext context) {
    final loading = context.watch<AuthProvider>().loading;

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
    final String welcomeTitle = _isSwahili ? 'Karibu tena 👋' : 'Welcome back 👋';
    final String welcomeSubtitle = _isSwahili ? 'Ingia ili kuendelea na akaunti yako' : 'Sign in to continue to your account';
    final String emailLabel = _isSwahili ? 'Barua pepe' : 'Email address';
    final String passwordLabel = _isSwahili ? 'Nenosiri' : 'Password';
    final String rememberMeLabel = _isSwahili ? 'Nikumbuke' : 'Remember me';
    final String forgotPasswordLabel = _isSwahili ? 'Umesahau nenosiri?' : 'Forgot password?';
    final String signInButtonText = _isSwahili ? 'Ingia' : 'Sign In';
    final String noAccountText = _isSwahili ? 'Huna akaunti?' : "Don't have an account?";
    final String createAccountText = _isSwahili ? 'Fungua Akaunti' : 'Create Account';

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
                      const SizedBox(height: 36),

                      // Header Texts
                      Text(
                        welcomeTitle,
                        style: TextStyle(
                          fontSize: 32,
                          fontWeight: FontWeight.w800,
                          color: textPrimary,
                          letterSpacing: -0.5,
                        ),
                      ),
                      const SizedBox(height: 8),
                      Text(
                        welcomeSubtitle,
                        style: TextStyle(
                          fontSize: 15,
                          color: textSecondary,
                        ),
                      ),
                      const SizedBox(height: 36),

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

                      // Email Input
                      _label(emailLabel, labelColor),
                      const SizedBox(height: 8),
                      TextField(
                        controller: _emailCtrl,
                        keyboardType: TextInputType.emailAddress,
                        style: _ts(inputTextColor),
                        decoration: _inputDeco('name@company.com', Icons.mail_outline_rounded, cardBg, borderColor),
                      ),
                      const SizedBox(height: 20),

                      // Password Input
                      _label(passwordLabel, labelColor),
                      const SizedBox(height: 8),
                      TextField(
                        controller: _passCtrl,
                        obscureText: _obscure,
                        style: _ts(inputTextColor),
                        onSubmitted: (_) => loading ? null : _login(),
                        decoration: _inputDeco('••••••••', Icons.lock_outline_rounded, cardBg, borderColor).copyWith(
                          suffixIcon: IconButton(
                            icon: Icon(
                              _obscure ? Icons.visibility_outlined : Icons.visibility_off_outlined,
                              color: textSecondary,
                              size: 20,
                            ),
                            onPressed: () => setState(() => _obscure = !_obscure),
                          ),
                        ),
                      ),
                      const SizedBox(height: 16),

                      // Remember me and Forgot password Row
                      Row(
                        children: [
                          GestureDetector(
                            onTap: () => setState(() => _remember = !_remember),
                            child: Row(
                              children: [
                                Container(
                                  width: 20,
                                  height: 20,
                                  decoration: BoxDecoration(
                                    color: Colors.transparent,
                                    border: Border.all(
                                      color: _remember ? AppColors.primary : borderColor,
                                      width: 2,
                                    ),
                                    borderRadius: BorderRadius.circular(5),
                                  ),
                                  child: _remember
                                      ? const Icon(Icons.check, size: 14, color: AppColors.primary)
                                      : null,
                                ),
                                const SizedBox(width: 8),
                                Text(
                                  rememberMeLabel,
                                  style: TextStyle(fontSize: 13, color: textSecondary),
                                ),
                              ],
                            ),
                          ),
                          const Spacer(),
                          TextButton(
                            onPressed: () => context.push('/forgot-password'),
                            style: TextButton.styleFrom(
                              padding: EdgeInsets.zero,
                              minimumSize: const Size(0, 0),
                            ),
                            child: Text(
                              forgotPasswordLabel,
                              style: const TextStyle(
                                  fontSize: 13,
                                  color: AppColors.primary,
                                  fontWeight: FontWeight.w600),
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 32),

                      // Sign In Button
                      SizedBox(
                        width: double.infinity,
                        height: 54,
                        child: ElevatedButton(
                          onPressed: loading ? null : _login,
                          style: ElevatedButton.styleFrom(
                            backgroundColor: AppColors.primary,
                            foregroundColor: Colors.white,
                            disabledBackgroundColor: AppColors.primary.withValues(alpha: 0.5),
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                            elevation: 0,
                          ),
                          child: Text(
                            signInButtonText,
                            style: const TextStyle(
                              fontSize: 16,
                              fontWeight: FontWeight.w700,
                              color: Colors.white,
                            ),
                          ),
                        ),
                      ),
                      const SizedBox(height: 28),

                      // Footer: Don't have an account?
                      Row(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Text(
                            noAccountText,
                            style: TextStyle(fontSize: 14, color: textSecondary),
                          ),
                          const SizedBox(width: 6),
                          TextButton(
                            onPressed: () => context.push('/register'),
                            style: TextButton.styleFrom(
                              padding: EdgeInsets.zero,
                              minimumSize: const Size(0, 0),
                            ),
                            child: Text(
                              createAccountText,
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
          if (loading && !_success)
            Container(
              color: Colors.black.withValues(alpha: 0.75),
              child: Center(
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    const CircularProgressIndicator(color: AppColors.primary, strokeWidth: 3.5),
                    const SizedBox(height: 20),
                    Text(
                      _isSwahili ? 'Inaingia...' : 'Signing in...',
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
                        _isSwahili ? 'Umeingia Kikamilifu!' : 'Success!',
                        style: TextStyle(
                          fontSize: 22,
                          fontWeight: FontWeight.w800,
                          color: textPrimary,
                        ),
                      ),
                      const SizedBox(height: 10),
                      Text(
                        _isSwahili ? 'Karibu tena Manna...' : 'Welcome back to Manna...',
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

  Widget _label(String text, Color color) => Text(
        text,
        style: TextStyle(fontSize: 14, fontWeight: FontWeight.w500, color: color),
      );

  TextStyle _ts(Color color) => TextStyle(fontSize: 15, color: color);

  InputDecoration _inputDeco(String hint, IconData icon, Color fill, Color border) => InputDecoration(
        hintText: hint,
        hintStyle: const TextStyle(color: Color(0xFF71717A), fontSize: 14),
        prefixIcon: Icon(icon, color: AppColors.primary, size: 20),
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
