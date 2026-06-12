import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../core/api_service.dart';
import '../../core/auth_provider.dart';
import '../../shared/theme/app_theme.dart';
import '../../shared/widgets/app_card.dart';

class SettingsPage extends StatefulWidget {
  const SettingsPage({super.key});
  @override State<SettingsPage> createState() => _SettingsPageState();
}

class _SettingsPageState extends State<SettingsPage> {
  @override
  Widget build(BuildContext context) {
    final auth = context.watch<AuthProvider>();
    final user = auth.user;
    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(title: const Text('Settings')),
      body: SingleChildScrollView(padding: const EdgeInsets.all(16), child: Column(children: [
        // Profile card
        AppCard(child: Padding(padding: const EdgeInsets.all(20), child: Column(children: [
          Container(width: 72, height: 72, decoration: const BoxDecoration(color: AppColors.primaryLt, shape: BoxShape.circle),
            child: Center(child: Text(user?.initials ?? '?', style: const TextStyle(color: AppColors.primary, fontWeight: FontWeight.w800, fontSize: 26)))),
          const SizedBox(height: 12),
          Text(user?.name ?? 'User', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 18)),
          Text(user?.email ?? '', style: const TextStyle(color: AppColors.textSec, fontSize: 13)),
          const SizedBox(height: 16),
          SizedBox(width: double.infinity, child: OutlinedButton.icon(onPressed: () => _showProfileForm(context, auth), icon: const Icon(Icons.edit_outlined, size: 18), label: const Text('Edit Profile'))),
        ]))),

        const SizedBox(height: 16),

        // App settings
        AppCard(child: Column(children: [
          _section('General'),
          _settingTile(Icons.currency_exchange, 'Currency', 'Tanzanian Shilling (TSh)', () {}),
          _divider(),
          _settingTile(Icons.language, 'Language', 'English', () {}),
          _divider(),
          _settingTile(Icons.notifications_outlined, 'Notifications', 'Enabled', () {}),
        ])),

        const SizedBox(height: 16),

        AppCard(child: Column(children: [
          _section('Security'),
          _settingTile(Icons.lock_outline, 'Change Password', 'Update your password', () => _showChangePassword(context)),
          _divider(),
          _settingTile(Icons.fingerprint, 'Biometric Login', 'Not configured', () {}),
        ])),

        const SizedBox(height: 16),

        AppCard(child: Column(children: [
          _section('About'),
          _settingTile(Icons.info_outline, 'App Version', '1.0.0', () {}),
          _divider(),
          _settingTile(Icons.help_outline, 'Help & Support', 'Get help', () {}),
          _divider(),
          _settingTile(Icons.privacy_tip_outlined, 'Privacy Policy', 'Read our policy', () {}),
        ])),

        const SizedBox(height: 16),

        SizedBox(width: double.infinity, height: 52, child: ElevatedButton.icon(
          onPressed: () => _confirmLogout(context, auth),
          icon: const Icon(Icons.logout),
          label: const Text('Sign Out', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700)),
          style: ElevatedButton.styleFrom(backgroundColor: AppColors.danger),
        )),
        const SizedBox(height: 32),
      ])),
    );
  }

  Widget _section(String t) => Padding(padding: const EdgeInsets.fromLTRB(16, 16, 16, 4), child: Row(children: [Text(t, style: const TextStyle(color: AppColors.textSec, fontSize: 12, fontWeight: FontWeight.w600, letterSpacing: 0.8))]));
  Widget _divider() => const Divider(height: 1, indent: 56);
  Widget _settingTile(IconData icon, String title, String sub, VoidCallback onTap) => ListTile(leading: Container(width: 36, height: 36, decoration: BoxDecoration(color: AppColors.primaryLt, borderRadius: BorderRadius.circular(9)), child: Icon(icon, color: AppColors.primary, size: 20)), title: Text(title, style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 14)), subtitle: Text(sub, style: const TextStyle(color: AppColors.textSec, fontSize: 12)), trailing: const Icon(Icons.chevron_right, color: AppColors.textSec), onTap: onTap);

  void _confirmLogout(BuildContext context, AuthProvider auth) async {
    final ok = await showDialog<bool>(context: context, builder: (_) => AlertDialog(
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
      title: const Text('Sign Out'),
      content: const Text('Are you sure you want to sign out?'),
      actions: [TextButton(onPressed: () => Navigator.pop(context, false), child: const Text('Cancel')), ElevatedButton(style: ElevatedButton.styleFrom(backgroundColor: AppColors.danger), onPressed: () => Navigator.pop(context, true), child: const Text('Sign Out'))],
    ));
    if (ok != true) return;
    await auth.logout();
    if (mounted) Navigator.pushNamedAndRemoveUntil(context, '/login', (_) => false);
  }

  void _showProfileForm(BuildContext context, AuthProvider auth) => showModalBottomSheet(context: context, isScrollControlled: true, backgroundColor: Colors.transparent,
    builder: (_) => _ProfileForm(user: auth.user, onSaved: () { setState(() {}); }));

  void _showChangePassword(BuildContext context) => showModalBottomSheet(context: context, isScrollControlled: true, backgroundColor: Colors.transparent, builder: (_) => const _ChangePasswordForm());
}

class _ProfileForm extends StatefulWidget {
  final dynamic user;
  final VoidCallback onSaved;
  const _ProfileForm({this.user, required this.onSaved});
  @override State<_ProfileForm> createState() => _ProfileFormState();
}
class _ProfileFormState extends State<_ProfileForm> {
  final _form = GlobalKey<FormState>();
  late TextEditingController _name, _email;
  bool _saving = false;
  String? _err;

  @override
  void initState() {
    super.initState();
    _name = TextEditingController(text: widget.user?.name);
    _email = TextEditingController(text: widget.user?.email);
  }
  @override void dispose() { _name.dispose(); _email.dispose(); super.dispose(); }

  Future<void> _save() async {
    if (!_form.currentState!.validate()) return;
    setState(() { _saving = true; _err = null; });
    try {
      await ApiService.put('/auth/profile', {'name': _name.text.trim(), 'email': _email.text.trim()});
      if (mounted) { widget.onSaved(); Navigator.pop(context); ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Profile updated'), backgroundColor: AppColors.success, behavior: SnackBarBehavior.floating)); }
    } on ApiException catch (e) { setState(() { _err = e.message; _saving = false; }); }
    catch (_) { setState(() { _err = 'Update failed'; _saving = false; }); }
  }

  @override
  Widget build(BuildContext context) => Container(
    decoration: const BoxDecoration(color: AppColors.surface, borderRadius: BorderRadius.vertical(top: Radius.circular(24))),
    padding: EdgeInsets.only(bottom: MediaQuery.of(context).viewInsets.bottom),
    child: SingleChildScrollView(padding: const EdgeInsets.fromLTRB(24, 16, 24, 32), child: Form(key: _form, child: Column(crossAxisAlignment: CrossAxisAlignment.stretch, children: [
      Center(child: Container(width: 40, height: 4, decoration: BoxDecoration(color: AppColors.border, borderRadius: BorderRadius.circular(4)))),
      const SizedBox(height: 20),
      Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [const Text('Edit Profile', style: TextStyle(fontSize: 20, fontWeight: FontWeight.w700)), IconButton(icon: const Icon(Icons.close), onPressed: () => Navigator.pop(context))]),
      if (_err != null) ...[const SizedBox(height: 12), Container(padding: const EdgeInsets.all(12), decoration: BoxDecoration(color: AppColors.dangerLt, borderRadius: BorderRadius.circular(10)), child: Text(_err!, style: const TextStyle(color: AppColors.danger)))],
      const SizedBox(height: 16),
      TextFormField(controller: _name, decoration: const InputDecoration(labelText: 'Full Name *'), validator: (v) => v!.isNotEmpty ? null : 'Required'),
      const SizedBox(height: 12),
      TextFormField(controller: _email, keyboardType: TextInputType.emailAddress, decoration: const InputDecoration(labelText: 'Email *'), validator: (v) => v!.contains('@') ? null : 'Invalid email'),
      const SizedBox(height: 24),
      SizedBox(height: 52, child: ElevatedButton(onPressed: _saving ? null : _save, child: _saving ? const SizedBox(width: 22, height: 22, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5)) : const Text('Save Changes', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700)))),
    ]))),
  );
}

class _ChangePasswordForm extends StatefulWidget {
  const _ChangePasswordForm();
  @override State<_ChangePasswordForm> createState() => _ChangePasswordFormState();
}
class _ChangePasswordFormState extends State<_ChangePasswordForm> {
  final _form = GlobalKey<FormState>();
  final _cur = TextEditingController();
  final _new = TextEditingController();
  final _confirm = TextEditingController();
  bool _saving = false;
  String? _err;
  bool _showCur = false, _showNew = false;

  @override void dispose() { _cur.dispose(); _new.dispose(); _confirm.dispose(); super.dispose(); }

  Future<void> _save() async {
    if (!_form.currentState!.validate()) return;
    setState(() { _saving = true; _err = null; });
    try {
      await ApiService.put('/auth/profile', {'current_password': _cur.text, 'password': _new.text, 'password_confirmation': _confirm.text});
      if (mounted) { Navigator.pop(context); ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Password updated'), backgroundColor: AppColors.success, behavior: SnackBarBehavior.floating)); }
    } on ApiException catch (e) { setState(() { _err = e.message; _saving = false; }); }
    catch (_) { setState(() { _err = 'Update failed'; _saving = false; }); }
  }

  @override
  Widget build(BuildContext context) => Container(
    decoration: const BoxDecoration(color: AppColors.surface, borderRadius: BorderRadius.vertical(top: Radius.circular(24))),
    padding: EdgeInsets.only(bottom: MediaQuery.of(context).viewInsets.bottom),
    child: SingleChildScrollView(padding: const EdgeInsets.fromLTRB(24, 16, 24, 32), child: Form(key: _form, child: Column(crossAxisAlignment: CrossAxisAlignment.stretch, children: [
      Center(child: Container(width: 40, height: 4, decoration: BoxDecoration(color: AppColors.border, borderRadius: BorderRadius.circular(4)))),
      const SizedBox(height: 20),
      Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [const Text('Change Password', style: TextStyle(fontSize: 20, fontWeight: FontWeight.w700)), IconButton(icon: const Icon(Icons.close), onPressed: () => Navigator.pop(context))]),
      if (_err != null) ...[const SizedBox(height: 12), Container(padding: const EdgeInsets.all(12), decoration: BoxDecoration(color: AppColors.dangerLt, borderRadius: BorderRadius.circular(10)), child: Text(_err!, style: const TextStyle(color: AppColors.danger)))],
      const SizedBox(height: 16),
      TextFormField(controller: _cur, obscureText: !_showCur, decoration: InputDecoration(labelText: 'Current Password', suffixIcon: IconButton(icon: Icon(_showCur ? Icons.visibility_off : Icons.visibility), onPressed: () => setState(() => _showCur = !_showCur))), validator: (v) => v!.isNotEmpty ? null : 'Required'),
      const SizedBox(height: 12),
      TextFormField(controller: _new, obscureText: !_showNew, decoration: InputDecoration(labelText: 'New Password', suffixIcon: IconButton(icon: Icon(_showNew ? Icons.visibility_off : Icons.visibility), onPressed: () => setState(() => _showNew = !_showNew))), validator: (v) => v!.length >= 8 ? null : 'Min 8 characters'),
      const SizedBox(height: 12),
      TextFormField(controller: _confirm, obscureText: true, decoration: const InputDecoration(labelText: 'Confirm Password'), validator: (v) => v == _new.text ? null : 'Passwords do not match'),
      const SizedBox(height: 24),
      SizedBox(height: 52, child: ElevatedButton(onPressed: _saving ? null : _save, child: _saving ? const SizedBox(width: 22, height: 22, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5)) : const Text('Update Password', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700)))),
    ]))),
  );
}