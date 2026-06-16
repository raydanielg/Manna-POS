import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:provider/provider.dart';
import '../../core/auth_provider.dart';
import '../../shared/theme/app_colors.dart';


class AdminHomePage extends StatelessWidget {
  const AdminHomePage({super.key});

  @override
  Widget build(BuildContext context) {
    final user = context.watch<AuthProvider>().user;
    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        title: const Text('Admin Panel'),
        actions: [
          IconButton(
            icon: const Icon(Icons.home_outlined),
            onPressed: () => context.go('/home'),
          ),
        ],
      ),
      body: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                gradient: const LinearGradient(colors: [AppColors.primary, AppColors.primaryDark]),
                borderRadius: BorderRadius.circular(16),
              ),
              child: Row(
                children: [
                  Container(
                    width: 48, height: 48,
                    decoration: BoxDecoration(color: Colors.white.withValues(alpha: 0.2), borderRadius: BorderRadius.circular(12)),
                    child: const Icon(Icons.admin_panel_settings, color: Colors.white, size: 28),
                  ),
                  const SizedBox(width: 14),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Text('Admin Dashboard', style: TextStyle(fontSize: 18, fontWeight: FontWeight.w700, color: Colors.white)),
                        Text(user?.name ?? 'Admin', style: TextStyle(fontSize: 13, color: Colors.white.withValues(alpha: 0.85))),
                      ],
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 20),
            const Text('Management', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700, color: Colors.black)),
            const SizedBox(height: 12),
            Expanded(
              child: GridView.count(
                crossAxisCount: 2,
                mainAxisSpacing: 12,
                crossAxisSpacing: 12,
                childAspectRatio: 1.2,
                children: [
                  _AdminTile(icon: Icons.people_rounded, label: 'Users', color: AppColors.accent, onTap: () => context.push('/admin/users')),
                  _AdminTile(icon: Icons.business_rounded, label: 'Businesses', color: AppColors.purple, onTap: () => context.push('/admin/businesses')),
                  _AdminTile(icon: Icons.settings_rounded, label: 'Settings', color: AppColors.primary, onTap: () => context.push('/settings')),
                  _AdminTile(icon: Icons.assessment_rounded, label: 'Reports', color: AppColors.orange, onTap: () => context.push('/reports')),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _AdminTile extends StatelessWidget {
  final IconData icon;
  final String label;
  final Color color;
  final VoidCallback onTap;
  const _AdminTile({required this.icon, required this.label, required this.color, required this.onTap});

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(14)),
        padding: const EdgeInsets.all(16),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Container(
              width: 48, height: 48,
              decoration: BoxDecoration(color: color.withValues(alpha: 0.12), borderRadius: BorderRadius.circular(12)),
              child: Icon(icon, color: color, size: 26),
            ),
            const SizedBox(height: 10),
            Text(label, style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w600, color: Colors.black)),
          ],
        ),
      ),
    );
  }
}
