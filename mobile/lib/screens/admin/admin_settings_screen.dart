import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../core/api_service.dart';
import '../../shared/theme/app_colors.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/toast_helper.dart';

class AdminSettingsScreen extends StatefulWidget {
  const AdminSettingsScreen({super.key});
  @override State<AdminSettingsScreen> createState() => _AdminSettingsScreenState();
}

class _AdminSettingsScreenState extends State<AdminSettingsScreen> {
  bool _maintenanceMode = false;
  bool _loading = true;

  @override
  void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    try {
      final data = await ApiService.get('/api/admin/settings');
      if (data is Map) setState(() { _maintenanceMode = data['maintenance_mode'] == true; _loading = false; });
    } catch (_) { setState(() => _loading = false); }
  }

  void _toggleMaintenance() async {
    try {
      final newVal = !_maintenanceMode;
      await ApiService.post('/api/admin/settings/maintenance', {'enabled': newVal});
      setState(() => _maintenanceMode = newVal);
      if (mounted) ToastHelper.show(context, message: newVal ? 'Maintenance mode enabled' : 'Maintenance mode disabled');
    } catch (e) { if (mounted) ToastHelper.show(context, message: 'Failed to update', error: true); }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(title: const Text('System Settings')),
      body: _loading
          ? const Center(child: CircularProgressIndicator())
          : ListView(
              padding: const EdgeInsets.fromLTRB(16, 12, 16, 100),
              children: [
                _sectionHeader('General'),
                _settingsTile(Icons.settings_rounded, 'System Configuration', 'API keys, security, general config', AppColors.primary, () {}),
                _settingsTile(Icons.shield_rounded, 'Security', 'Password policy, 2FA, IP whitelist', AppColors.danger, () {}),
                const SizedBox(height: 16),
                _sectionHeader('Communication'),
                _settingsTile(Icons.email_rounded, 'Email Configuration', 'SMTP, templates, test email', AppColors.warning, () {}),
                _settingsTile(Icons.sms_rounded, 'SMS Configuration', 'SMS gateway, templates, test SMS', AppColors.purple, () {}),
                const SizedBox(height: 16),
                _sectionHeader('Payments'),
                _settingsTile(Icons.payment_rounded, 'Payment Gateways', 'Configure Stripe, PayPal, etc.', AppColors.success, () {}),
                _settingsTile(Icons.currency_exchange_rounded, 'Currency & Tax', 'Default currency, tax rates', AppColors.info, () {}),
                const SizedBox(height: 16),
                _sectionHeader('Data & Maintenance'),
                _settingsTile(Icons.backup_rounded, 'Backup & Restore', 'Database backup, restore, schedule', AppColors.orange, () {}),
                _settingsTile(Icons.cache_rounded, 'Cache Management', 'Clear cache, optimize performance', AppColors.cyan, () {}),
                _settingsTile(Icons.bug_report_rounded, 'Logs & Error Logs', 'System logs, error tracking, debug', AppColors.danger, () {}),
                const SizedBox(height: 16),
                _sectionHeader('System'),
                SwitchListTile(
                  title: const Text('Maintenance Mode'),
                  subtitle: const Text('Disable user access for maintenance'),
                  value: _maintenanceMode,
                  onChanged: (_) => _toggleMaintenance(),
                  secondary: Container(
                    width: 40, height: 40,
                    decoration: BoxDecoration(color: AppColors.warning.withValues(alpha: 0.12), borderRadius: BorderRadius.circular(10)),
                    child: const Icon(Icons.engineering_rounded, color: AppColors.warning, size: 20),
                  ),
                  activeColor: AppColors.warning,
                ),
              ],
            ),
    );
  }

  Widget _sectionHeader(String title) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 8, left: 4),
      child: Text(title, style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w700, color: AppColors.textSec, letterSpacing: 0.5)),
    );
  }

  Widget _settingsTile(IconData icon, String title, String subtitle, Color color, VoidCallback onTap) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 8),
      child: GlassCard(onTap: onTap, padding: const EdgeInsets.symmetric(vertical: 4),
        child: ListTile(
          leading: Container(
            width: 40, height: 40,
            decoration: BoxDecoration(color: color.withValues(alpha: 0.12), borderRadius: BorderRadius.circular(10)),
            child: Icon(icon, color: color, size: 20),
          ),
          title: Text(title, style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 15)),
          subtitle: Text(subtitle, style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
          trailing: const Icon(Icons.chevron_right, color: AppColors.textLight),
        ),
      ),
    );
  }
}
