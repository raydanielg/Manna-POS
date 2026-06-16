import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:provider/provider.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../shared/theme/app_colors.dart';
import '../core/auth_provider.dart';
import '../features/onboarding/onboarding_page.dart';
import '../features/auth/login_page.dart';
import '../features/auth/register_page.dart';
import '../features/dashboard/main_screen.dart';
import '../features/admin/admin_home_page.dart';
import '../features/admin/users_page.dart';
import '../features/admin/businesses_page.dart';
import '../features/products/products_page.dart';
import '../features/customers/customers_page.dart';
import '../features/sales/sales_page.dart';
import '../features/expenses/expenses_page.dart';
import '../features/reports/reports_page.dart';
import '../features/settings/settings_page.dart';

class AppRouter {
  static final GoRouter router = GoRouter(
    initialLocation: '/splash',
    routes: [
      GoRoute(path: '/splash', builder: (_, __) => const _SplashScreen()),
      GoRoute(path: '/onboarding', builder: (_, __) => const OnboardingPage()),
      GoRoute(path: '/login', builder: (_, __) => const LoginPage()),
      GoRoute(path: '/register', builder: (_, __) => const RegisterPage()),
      GoRoute(path: '/home', builder: (_, __) => const MainScreen()),
      GoRoute(path: '/admin', builder: (_, __) => const AdminHomePage()),
      GoRoute(path: '/admin/users', builder: (_, __) => const UsersPage()),
      GoRoute(path: '/admin/businesses', builder: (_, __) => const BusinessesPage()),
      GoRoute(path: '/products', builder: (_, __) => const ProductsPage()),
      GoRoute(path: '/customers', builder: (_, __) => const CustomersPage()),
      GoRoute(path: '/sales', builder: (_, __) => const SalesPage()),
      GoRoute(path: '/expenses', builder: (_, __) => const ExpensesPage()),
      GoRoute(path: '/reports', builder: (_, __) => const ReportsPage()),
      GoRoute(path: '/settings', builder: (_, __) => const SettingsPage()),
    ],
    errorBuilder: (_, __) => Scaffold(
      body: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            const Icon(Icons.error_outline, size: 64, color: AppColors.error),
            const SizedBox(height: 16),
            Text('Page not found',
              style: TextStyle(fontSize: 18, fontWeight: FontWeight.w600, color: AppColors.textPri)),
          ],
        ),
      ),
    ),
  );
}

class _SplashScreen extends StatefulWidget {
  const _SplashScreen();
  @override
  State<_SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends State<_SplashScreen> with SingleTickerProviderStateMixin {
  late AnimationController _ctrl;
  late Animation<double> _fade;

  @override
  void initState() {
    super.initState();
    _ctrl = AnimationController(vsync: this, duration: const Duration(milliseconds: 1000));
    _fade = CurvedAnimation(parent: _ctrl, curve: Curves.easeIn);
    _ctrl.forward();
    _checkAuth();
  }

  Future<void> _checkAuth() async {
    await Future.delayed(const Duration(seconds: 2));
    if (!mounted) return;
    final auth = context.read<AuthProvider>();
    final prefs = await SharedPreferences.getInstance();
    final onboarded = prefs.getBool('onboarding_done') ?? false;
    if (!onboarded) {
      context.go('/onboarding');
    } else if (await auth.tryAutoLogin()) {
      context.go(auth.user?.role == 'admin' ? '/admin' : '/home');
    } else {
      context.go('/login');
    }
  }

  @override
  void dispose() { _ctrl.dispose(); super.dispose(); }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.primary,
      body: Center(
        child: FadeTransition(
          opacity: _fade,
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Container(
                width: 80, height: 80,
                decoration: BoxDecoration(
                  color: Colors.white, borderRadius: BorderRadius.circular(20),
                ),
                child: const Icon(Icons.store_rounded, size: 44, color: AppColors.primary),
              ),
              const SizedBox(height: 20),
              const Text('MannaPOS',
                style: TextStyle(fontSize: 28, fontWeight: FontWeight.w800, color: Colors.white)),
              const SizedBox(height: 8),
              Text('Point of Sale',
                style: TextStyle(fontSize: 15, color: Colors.white.withValues(alpha: 0.8))),
            ],
          ),
        ),
      ),
    );
  }
}
