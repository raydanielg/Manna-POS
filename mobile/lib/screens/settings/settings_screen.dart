import 'package:flutter/material.dart';
import '../../shared/theme/app_colors.dart';
import '../../shared/widgets/app_card.dart';
import '../../widgets/glass_card.dart';

class SettingsScreen extends StatelessWidget {
  const SettingsScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(title: const Text('Settings'), centerTitle: true),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          _sectionHeader('Business'),
          _settingTile(context, Icons.store_outlined, 'Business Settings', AppColors.primary, '/settings/business'),
          _settingTile(context, Icons.location_on_outlined, 'Business Locations', AppColors.success, '/settings/locations'),
          const SizedBox(height: 20),
          _sectionHeader('Invoice'),
          _settingTile(context, Icons.receipt_outlined, 'Invoice Settings', AppColors.warning, '/settings/invoice'),
          _settingTile(context, Icons.qr_code_outlined, 'Barcode Settings', AppColors.purple, '/settings/barcode'),
          const SizedBox(height: 20),
          _sectionHeader('Finance'),
          _settingTile(context, Icons.percent_outlined, 'Tax Rates', AppColors.danger, '/settings/tax-rates'),
          _settingTile(context, Icons.monetization_on_outlined, 'Currency', AppColors.orange, '/settings/currency'),
          const SizedBox(height: 20),
          _sectionHeader('Notifications'),
          _settingTile(context, Icons.notifications_outlined, 'Notification Templates', AppColors.cyan, '/settings/notifications'),
          const SizedBox(height: 20),
          _sectionHeader('System'),
          _settingTile(context, Icons.info_outline, 'About', AppColors.textSec, '/settings/about'),
          ListTile(
            leading: const Icon(Icons.phone_android_outlined, color: AppColors.textSec),
            title: const Text('Version', style: TextStyle(fontWeight: FontWeight.w600, fontSize: 14)),
            trailing: const Text('1.0.0', style: TextStyle(color: AppColors.textSec)),
          ),
          const SizedBox(height: 30),
        ],
      ),
    );
  }

  Widget _sectionHeader(String title) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 8, left: 4),
      child: Text(title, style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 13, color: AppColors.textSec, letterSpacing: 0.5)),
    );
  }

  Widget _settingTile(BuildContext context, IconData icon, String title, Color color, String route) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 8),
      child: GlassCard(
        child: ListTile(
          leading: Container(
            width: 40, height: 40,
            decoration: BoxDecoration(color: color.withValues(alpha: 0.12), borderRadius: BorderRadius.circular(10)),
            child: Icon(icon, color: color, size: 20),
          ),
          title: Text(title, style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 15)),
          trailing: const Icon(Icons.chevron_right, color: AppColors.textSec),
          onTap: () => Navigator.pushNamed(context, route),
        ),
      ),
    );
  }
}
