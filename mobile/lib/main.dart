import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:provider/provider.dart';
import 'core/api_service.dart';
import 'core/auth_provider.dart';
import 'shared/theme/app_theme.dart';
import 'features/auth/login_page.dart';
import 'features/auth/register_page.dart';
import 'features/auth/forgot_password_page.dart';
import 'features/dashboard/dashboard_page.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  SystemChrome.setPreferredOrientations([DeviceOrientation.portraitUp, DeviceOrientation.portraitDown]);
  await ApiService.init();
  runApp(ChangeNotifierProvider(create: (_) => AuthProvider(), child: const MannaApp()));
}

class MannaApp extends StatelessWidget {
  const MannaApp({super.key});
  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'MannaPOS',
      debugShowCheckedModeBanner: false,
      theme: AppTheme.light,
      initialRoute: '/splash',
      routes: {
        '/splash':          (_) => const SplashScreen(),
        '/login':           (_) => const LoginPage(),
        '/register':        (_) => const RegisterPage(),
        '/forgot-password': (_) => const ForgotPasswordPage(),
        '/dashboard':       (_) => const DashboardPage(),
      },
    );
  }
}

class SplashScreen extends StatefulWidget {
  const SplashScreen({super.key});
  @override State<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen> with SingleTickerProviderStateMixin {
  late AnimationController _ctrl;
  late Animation<double> _fade;

  @override
  void initState() {
    super.initState();
    _ctrl = AnimationController(vsync: this, duration: const Duration(milliseconds: 800));
    _fade = CurvedAnimation(parent: _ctrl, curve: Curves.easeOut);
    _ctrl.forward();
    _navigate();
  }

  Future<void> _navigate() async {
    await Future.delayed(const Duration(milliseconds: 2000));
    if (!mounted) return;
    final auth = context.read<AuthProvider>();
    final ok = await auth.tryAutoLogin();
    if (!mounted) return;
    Navigator.pushReplacementNamed(context, ok ? '/dashboard' : '/login');
  }

  @override
  void dispose() { _ctrl.dispose(); super.dispose(); }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.bg,
      body: FadeTransition(
        opacity: _fade,
        child: Center(
          child: Column(mainAxisSize: MainAxisSize.min, children: [
            TweenAnimationBuilder<double>(
              tween: Tween(begin: 0.0, end: 1.0),
              duration: const Duration(milliseconds: 1000),
              curve: Curves.easeOut,
              builder: (_, value, child) => Transform.scale(
                scale: 0.5 + (0.5 * value),
                child: Opacity(opacity: value, child: child),
              ),
              child: SizedBox(
                width: 140, height: 140,
                child: Image.asset('assets/icons/app_logo.png', fit: BoxFit.contain),
              ),
            ),
            const SizedBox(height: 32),
            TweenAnimationBuilder<double>(
              tween: Tween(begin: 0.0, end: 1.0),
              duration: const Duration(milliseconds: 800),
              curve: Curves.easeOut,
              builder: (_, value, child) => Opacity(opacity: value, child: child),
              child: const Text('MannaPOS', style: TextStyle(fontSize: 42, fontWeight: FontWeight.w800, color: AppColors.textPri, letterSpacing: -0.5)),
            ),
            const SizedBox(height: 12),
            TweenAnimationBuilder<double>(
              tween: Tween(begin: 0.0, end: 1.0),
              duration: const Duration(milliseconds: 800),
              curve: Curves.easeOut,
              builder: (_, value, child) => Opacity(opacity: value, child: child),
              child: Text('Smart Point of Sale', style: TextStyle(fontSize: 18, color: AppColors.textSec, fontWeight: FontWeight.w400)),
            ),
            const SizedBox(height: 80),
            TweenAnimationBuilder<double>(
              tween: Tween(begin: 0.0, end: 1.0),
              duration: const Duration(milliseconds: 600),
              curve: Curves.easeOut,
              builder: (_, value, child) => Opacity(opacity: value, child: child),
              child: SizedBox(width: 40, height: 40, child: CircularProgressIndicator(color: AppColors.primary, strokeWidth: 3)),
            ),
          ]),
        ),
      ),
    );
  }
}