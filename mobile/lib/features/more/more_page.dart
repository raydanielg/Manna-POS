import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:provider/provider.dart';
import '../../core/auth_provider.dart';
import '../../shared/theme/app_colors.dart';
import '../../shared/utils/formatters.dart';
import '../more/staff_management_page.dart';

class MorePage extends StatelessWidget {
  const MorePage({super.key});

  @override
  Widget build(BuildContext context) {
    final user = context.watch<AuthProvider>().user;
    final name = user?.name ?? 'User';
    final initials = name.split(' ').map((w) => w[0]).take(2).join().toUpperCase();
    final business = user?.businessName ?? 'MannaPOS';

    return Scaffold(
      backgroundColor: AppColors.background,
      body: CustomScrollView(
        physics: const BouncingScrollPhysics(),
        slivers: [
          SliverToBoxAdapter(child: _buildProfile(name, initials, business)),
          SliverToBoxAdapter(child: _buildSection('Sales & Purchases', [
            _MenuItem(Icons.receipt_long_rounded, 'Receipts', AppColors.primary, () => context.push('/receipts')),
            _MenuItem(Icons.shopping_cart_outlined, 'Purchases', AppColors.primaryDark, () => context.push('/purchases')),
            _MenuItem(Icons.business_outlined, 'Suppliers', AppColors.accent, () => context.push('/suppliers')),
            _MenuItem(Icons.receipt_rounded, 'Expenses', AppColors.secondary, () => context.push('/expenses')),
            _MenuItem(Icons.discount_outlined, 'Discounts', AppColors.warning, () => context.push('/discounts')),
          ])),
          SliverToBoxAdapter(child: _buildSection('Products & Stock', [
            _MenuItem(Icons.category_rounded, 'Categories', AppColors.cyan, () => context.push('/categories')),
            _MenuItem(Icons.trademark_rounded, 'Brands', AppColors.purple, () => context.push('/brands')),
            _MenuItem(Icons.straighten_outlined, 'Units', AppColors.info, () => context.push('/units')),
            _MenuItem(Icons.account_balance_outlined, 'Tax Rates', AppColors.orange, () => context.push('/tax-rates')),
            _MenuItem(Icons.verified_outlined, 'Warranties', AppColors.pink, () => context.push('/warranties')),
            _MenuItem(Icons.balance_rounded, 'Stock Adjustments', AppColors.warning, () => context.push('/stock-adjustments')),
            _MenuItem(Icons.swap_horiz_outlined, 'Stock Transfers', AppColors.primary, () => context.push('/stock-transfers')),
          ])),
          SliverToBoxAdapter(child: _buildSection('Management', [
            _MenuItem(Icons.category_outlined, 'Expense Categories', AppColors.secondary, () => context.push('/expense-categories')),
            _MenuItem(Icons.group_outlined, 'Customer Groups', AppColors.info, () => context.push('/customer-groups')),
            _MenuItem(Icons.people_rounded, 'Staff Management', AppColors.accent, () => _push(context, const StaffManagementPage())),
            _MenuItem(Icons.people_rounded, 'Staff Management', AppColors.accent, () => _push(context, const StaffManagementPage())),
            _MenuItem(Icons.assessment_rounded, 'Reports', AppColors.primary, () => context.push('/reports')),
          ])),
          SliverToBoxAdapter(child: _buildSection('System', [
            _MenuItem(Icons.settings_rounded, 'Settings', AppColors.textSec, () => context.push('/settings')),
            if (user?.role == 'admin')
              _MenuItem(Icons.admin_panel_settings, 'Admin Panel', AppColors.orange, () => context.push('/admin')),
            _MenuItem(Icons.help_outline_rounded, 'Help & Support', AppColors.purple, () => _showHelp(context)),
            _MenuItem(Icons.info_outline_rounded, 'About', AppColors.textSec, () => _showAbout(context)),
          ])),
          const SliverToBoxAdapter(child: SizedBox(height: 32)),
        ],
      ),
    );
  }

  Widget _buildProfile(String name, String initials, String business) {
    return Container(
      color: Colors.white,
      child: SafeArea(
        bottom: false,
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Row(
            children: [
              Container(
                width: 48, height: 48,
                decoration: BoxDecoration(
                  gradient: const LinearGradient(colors: [AppColors.primary, AppColors.primaryDark]),
                  borderRadius: BorderRadius.circular(14),
                ),
                child: Center(child: Text(initials, style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w700, color: Colors.white))),
              ),
              const SizedBox(width: 14),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(name, style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w700, color: Colors.black)),
                    Text(business, style: const TextStyle(fontSize: 12, color: AppColors.textSec)),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildSection(String title, List<_MenuItem> items) {
    return Padding(
      padding: const EdgeInsets.fromLTRB(16, 20, 16, 0),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(title, style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w700, color: Colors.black)),
          const SizedBox(height: 10),
          Container(
            decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(12)),
            child: Column(
              children: items.map((item) => _MenuRow(item: item)).toList(),
            ),
          ),
        ],
      ),
    );
  }

  void _push(BuildContext context, Widget page) {
    Navigator.push(context, MaterialPageRoute(builder: (_) => page));
  }

  void _showHelp(BuildContext context) {
    showModalBottomSheet(
      context: context, isScrollControlled: true, backgroundColor: Colors.white,
      shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(20))),
      builder: (_) => Padding(
        padding: const EdgeInsets.all(24),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Center(child: Container(width: 40, height: 4, decoration: BoxDecoration(color: AppColors.border, borderRadius: BorderRadius.circular(2)))),
            const SizedBox(height: 16),
            const Text('Help & Support', style: TextStyle(fontSize: 18, fontWeight: FontWeight.w700)),
            const SizedBox(height: 16),
            _helpItem(Icons.email_outlined, 'Email', 'support@mannapos.com'),
            _helpItem(Icons.phone_outlined, 'Phone', '+255 123 456 789'),
            _helpItem(Icons.language_outlined, 'Website', 'www.mannapos.com'),
            const SizedBox(height: 16),
          ],
        ),
      ),
    );
  }

  Widget _helpItem(IconData icon, String label, String value) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 8),
      child: Row(
        children: [
          Container(
            width: 36, height: 36,
            decoration: BoxDecoration(color: AppColors.primaryLt, borderRadius: BorderRadius.circular(8)),
            child: Icon(icon, size: 18, color: AppColors.primary),
          ),
          const SizedBox(width: 12),
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(label, style: const TextStyle(fontSize: 13, color: AppColors.textSec)),
              Text(value, style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w600, color: Colors.black)),
            ],
          ),
        ],
      ),
    );
  }

  void _showAbout(BuildContext context) {
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        content: const Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Text('MannaPOS', style: TextStyle(fontSize: 22, fontWeight: FontWeight.w800)),
            SizedBox(height: 8),
            Text('Version 1.0.0', style: TextStyle(color: AppColors.textSec)),
            SizedBox(height: 8),
            Text('Point of Sale System', textAlign: TextAlign.center, style: TextStyle(color: AppColors.textSec)),
          ],
        ),
        actions: [TextButton(onPressed: () => Navigator.pop(ctx), child: const Text('Close'))],
      ),
    );
  }
}

class _MenuItem {
  final IconData icon;
  final String label;
  final Color color;
  final VoidCallback onTap;
  const _MenuItem(this.icon, this.label, this.color, this.onTap);
}

class _MenuRow extends StatelessWidget {
  final _MenuItem item;
  const _MenuRow({required this.item});

  @override
  Widget build(BuildContext context) {
    return InkWell(
      onTap: item.onTap,
      borderRadius: BorderRadius.circular(12),
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
        child: Row(
          children: [
            Container(
              width: 36, height: 36,
              decoration: BoxDecoration(color: item.color.withValues(alpha: 0.12), borderRadius: BorderRadius.circular(8)),
              child: Icon(item.icon, size: 18, color: item.color),
            ),
            const SizedBox(width: 12),
            Expanded(child: Text(item.label, style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w500, color: Colors.black))),
            const Icon(Icons.chevron_right, size: 18, color: AppColors.textLight),
          ],
        ),
      ),
    );
  }
}
