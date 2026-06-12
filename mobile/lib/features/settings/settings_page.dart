import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:provider/provider.dart';
import '../../core/auth_provider.dart';
import '../../shared/theme/app_theme.dart';
import '../../shared/models/user.dart';
import '../../shared/widgets/app_card.dart';

class SettingsPage extends StatelessWidget {
  const SettingsPage({super.key});

  @override
  Widget build(BuildContext context) {
    final user = context.watch<AuthProvider>().user;
    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(title: const Text('Settings')),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(children: [
          // ── Profile Hero ────────────────────────────────────────────────
          _ProfileHero(user: user),
          const SizedBox(height: 16),

          // ── Business Info ───────────────────────────────────────────────
          _SectionCard(
            title: 'Business',
            icon: Icons.store_outlined,
            color: AppColors.primary,
            items: [
              _InfoRow(Icons.storefront_outlined, 'Business Name', user?.businessName ?? '—', AppColors.primary),
              _InfoRow(Icons.category_outlined, 'Type', _typeName(user?.businessType), AppColors.secondary),
              _InfoRow(Icons.location_city_outlined, 'City', user?.businessCity ?? '—', AppColors.success),
              _InfoRow(Icons.flag_outlined, 'Country', user?.businessCountry ?? 'Tanzania', AppColors.warning),
            ],
            onEdit: () => _openEditSheet(context, user, _EditMode.business),
          ),
          const SizedBox(height: 12),

          // ── Financial Settings ──────────────────────────────────────────
          _SectionCard(
            title: 'Financial Settings',
            icon: Icons.tune_outlined,
            color: AppColors.success,
            items: [
              _InfoRow(Icons.payments_outlined, 'Currency', '${user?.currency ?? 'TZS'} (${user?.currencySymbol ?? 'TSh'})', AppColors.success),
              _InfoRow(Icons.percent_outlined, 'VAT / Tax Rate', '${user?.taxPercentage.toStringAsFixed(1) ?? '18.0'}%', AppColors.warning),
              _InfoRow(Icons.calendar_month_outlined, 'Fiscal Year Starts', user?.fiscalYearStart ?? 'January', AppColors.secondary),
            ],
            onEdit: () => _openEditSheet(context, user, _EditMode.financial),
          ),
          const SizedBox(height: 12),

          // ── Personal Info ───────────────────────────────────────────────
          _SectionCard(
            title: 'Personal Info',
            icon: Icons.person_outlined,
            color: AppColors.secondary,
            items: [
              _InfoRow(Icons.person_outline, 'Full Name', user?.name ?? '—', AppColors.secondary),
              _InfoRow(Icons.phone_outlined, 'Phone', user?.phone ?? '—', AppColors.success),
              _InfoRow(Icons.email_outlined, 'Email', user?.email ?? '—', AppColors.primary),
            ],
            onEdit: () => _openEditSheet(context, user, _EditMode.personal),
          ),
          const SizedBox(height: 12),

          // ── Security ────────────────────────────────────────────────────
          AppCard(child: Column(children: [
            Padding(
              padding: const EdgeInsets.fromLTRB(16, 14, 16, 8),
              child: Row(children: [
                Container(width: 32, height: 32, decoration: BoxDecoration(color: AppColors.dangerLt, borderRadius: BorderRadius.circular(8)), child: const Icon(Icons.security_outlined, color: AppColors.danger, size: 18)),
                const SizedBox(width: 10),
                const Text('Security', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 15)),
              ]),
            ),
            const Divider(height: 1),
            ListTile(
              leading: const Icon(Icons.lock_outline, color: AppColors.textSec, size: 22),
              title: const Text('Change Password', style: TextStyle(fontWeight: FontWeight.w600, fontSize: 14)),
              subtitle: const Text('Keep your account secure', style: TextStyle(fontSize: 12)),
              trailing: const Icon(Icons.chevron_right, color: AppColors.textSec),
              onTap: () => _openChangePassword(context),
            ),
          ])),
          const SizedBox(height: 12),

          // ── App Info ────────────────────────────────────────────────────
          AppCard(child: Column(children: [
            Padding(padding: const EdgeInsets.fromLTRB(16, 14, 16, 8), child: Row(children: [
              Container(width: 32, height: 32, decoration: BoxDecoration(color: AppColors.primaryLt, borderRadius: BorderRadius.circular(8)), child: const Icon(Icons.info_outline, color: AppColors.primary, size: 18)),
              const SizedBox(width: 10),
              const Text('About', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 15)),
            ])),
            const Divider(height: 1),
            const ListTile(leading: Icon(Icons.phone_android_outlined, color: AppColors.textSec, size: 22), title: Text('App Version', style: TextStyle(fontWeight: FontWeight.w600, fontSize: 14)), trailing: Text('1.0.0', style: TextStyle(color: AppColors.textSec, fontSize: 13))),
            const Divider(height: 1, indent: 56),
            const ListTile(leading: Icon(Icons.location_on_outlined, color: AppColors.textSec, size: 22), title: Text('Region', style: TextStyle(fontWeight: FontWeight.w600, fontSize: 14)), trailing: Text('Tanzania 🇹🇿', style: TextStyle(color: AppColors.textSec, fontSize: 13))),
          ])),
          const SizedBox(height: 20),

          // ── Sign Out ────────────────────────────────────────────────────
          SizedBox(
            width: double.infinity, height: 52,
            child: ElevatedButton.icon(
              onPressed: () => _confirmLogout(context),
              icon: const Icon(Icons.logout),
              label: const Text('Sign Out', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700)),
              style: ElevatedButton.styleFrom(backgroundColor: AppColors.danger),
            ),
          ),
          const SizedBox(height: 32),
        ]),
      ),
    );
  }

  static String _typeName(String? t) {
    const map = {'retail': 'Retail 🏪', 'wholesale': 'Wholesale 🏭', 'restaurant': 'Restaurant 🍽️', 'service': 'Service ⚙️', 'other': 'Other 📦'};
    return map[t] ?? '—';
  }

  static void _openEditSheet(BuildContext ctx, AppUser? user, _EditMode mode) =>
      showModalBottomSheet(context: ctx, isScrollControlled: true, backgroundColor: Colors.transparent,
        builder: (_) => _EditProfileSheet(user: user, mode: mode));

  static void _openChangePassword(BuildContext ctx) =>
      showModalBottomSheet(context: ctx, isScrollControlled: true, backgroundColor: Colors.transparent,
        builder: (_) => const _ChangePasswordSheet());

  static void _confirmLogout(BuildContext context) async {
    final ok = await showDialog<bool>(context: context, builder: (_) => AlertDialog(
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
      title: const Text('Sign Out'),
      content: const Text('Are you sure you want to sign out?'),
      actions: [
        TextButton(onPressed: () => Navigator.pop(context, false), child: const Text('Cancel')),
        ElevatedButton(style: ElevatedButton.styleFrom(backgroundColor: AppColors.danger), onPressed: () => Navigator.pop(context, true), child: const Text('Sign Out')),
      ],
    ));
    if (ok != true) return;
    if (context.mounted) {
      await context.read<AuthProvider>().logout();
      if (context.mounted) Navigator.pushNamedAndRemoveUntil(context, '/login', (_) => false);
    }
  }
}

// ── Profile Hero ─────────────────────────────────────────────────────────────
class _ProfileHero extends StatelessWidget {
  final AppUser? user;
  const _ProfileHero({this.user});
  @override
  Widget build(BuildContext context) => AppCard(child: Container(
    decoration: const BoxDecoration(
      gradient: LinearGradient(colors: [Color(0xFF2563EB), Color(0xFF7C3AED)], begin: Alignment.topLeft, end: Alignment.bottomRight),
      borderRadius: BorderRadius.all(Radius.circular(14)),
    ),
    padding: const EdgeInsets.all(20),
    child: Row(children: [
      Container(width: 64, height: 64, decoration: BoxDecoration(color: Colors.white.withValues(alpha: 0.2), shape: BoxShape.circle, border: Border.all(color: Colors.white54, width: 2)),
        child: Center(child: Text(user?.initials ?? '?', style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w800, fontSize: 24)))),
      const SizedBox(width: 16),
      Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
        Text(user?.name ?? 'User', style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w800, fontSize: 18)),
        Text(user?.displayBusiness ?? 'My Business', style: const TextStyle(color: Colors.white70, fontSize: 13)),
        const SizedBox(height: 6),
        Container(padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4), decoration: BoxDecoration(color: Colors.white.withValues(alpha: 0.2), borderRadius: BorderRadius.circular(20)),
          child: Text('${user?.currency ?? 'TZS'} · ${user?.taxPercentage.toStringAsFixed(0) ?? '18'}% VAT', style: const TextStyle(color: Colors.white, fontSize: 11, fontWeight: FontWeight.w600))),
      ])),
    ]),
  ));
}

// ── Section Card ──────────────────────────────────────────────────────────────
class _SectionCard extends StatelessWidget {
  final String title;
  final IconData icon;
  final Color color;
  final List<Widget> items;
  final VoidCallback onEdit;
  const _SectionCard({required this.title, required this.icon, required this.color, required this.items, required this.onEdit});

  @override
  Widget build(BuildContext context) => AppCard(child: Column(children: [
    Padding(padding: const EdgeInsets.fromLTRB(16, 14, 8, 8), child: Row(children: [
      Container(width: 32, height: 32, decoration: BoxDecoration(color: color.withValues(alpha: 0.12), borderRadius: BorderRadius.circular(8)),
        child: Icon(icon, color: color, size: 18)),
      const SizedBox(width: 10),
      Expanded(child: Text(title, style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 15))),
      TextButton.icon(onPressed: onEdit, icon: Icon(Icons.edit_outlined, size: 16, color: color), label: Text('Edit', style: TextStyle(color: color, fontSize: 13, fontWeight: FontWeight.w600))),
    ])),
    const Divider(height: 1),
    ...items.map((w) => w),
  ]));
}

// ── Info Row ──────────────────────────────────────────────────────────────────
class _InfoRow extends StatelessWidget {
  final IconData icon;
  final String label, value;
  final Color color;
  const _InfoRow(this.icon, this.label, this.value, this.color);
  @override
  Widget build(BuildContext context) => Padding(
    padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
    child: Row(children: [
      Icon(icon, size: 18, color: color),
      const SizedBox(width: 12),
      Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
        Text(label, style: const TextStyle(color: AppColors.textSec, fontSize: 11, fontWeight: FontWeight.w500)),
        Text(value, style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 14)),
      ])),
    ]),
  );
}

// ── Edit modes ────────────────────────────────────────────────────────────────
enum _EditMode { business, financial, personal }

// ── Edit Profile Bottom Sheet ─────────────────────────────────────────────────
class _EditProfileSheet extends StatefulWidget {
  final AppUser? user;
  final _EditMode mode;
  const _EditProfileSheet({this.user, required this.mode});
  @override State<_EditProfileSheet> createState() => _EditProfileSheetState();
}

class _EditProfileSheetState extends State<_EditProfileSheet> {
  // Business
  late TextEditingController _bizName, _bizCity, _bizCountry, _bizAddr;
  late String _bizType;
  // Financial
  late String _currency, _fiscalStart;
  late TextEditingController _taxPct;
  // Personal
  late TextEditingController _name, _email, _phone;

  bool _saving = false;
  String? _err;

  static const _bizTypes = [
    ('retail', '🏪', 'Retail'), ('wholesale', '🏭', 'Wholesale'),
    ('restaurant', '🍽️', 'Restaurant'), ('service', '⚙️', 'Service'), ('other', '📦', 'Other'),
  ];
  static const _currencies = [
    ('TZS', '🇹🇿', 'TZS'), ('USD', '🇺🇸', 'USD'), ('EUR', '🇪🇺', 'EUR'),
    ('KES', '🇰🇪', 'KES'), ('UGX', '🇺🇬', 'UGX'), ('GBP', '🇬🇧', 'GBP'), ('ZAR', '🇿🇦', 'ZAR'),
  ];
  static const _months = ['January','February','March','April','May','June','July','August','September','October','November','December'];

  @override
  void initState() {
    super.initState();
    final u = widget.user;
    _bizName    = TextEditingController(text: u?.businessName);
    _bizCity    = TextEditingController(text: u?.businessCity);
    _bizCountry = TextEditingController(text: u?.businessCountry ?? 'Tanzania');
    _bizAddr    = TextEditingController(text: u?.businessAddress);
    _bizType    = u?.businessType ?? 'retail';
    _currency   = u?.currency ?? 'TZS';
    _fiscalStart = u?.fiscalYearStart ?? 'January';
    _taxPct     = TextEditingController(text: u?.taxPercentage.toStringAsFixed(1) ?? '18.0');
    _name       = TextEditingController(text: u?.name);
    _email      = TextEditingController(text: u?.email);
    _phone      = TextEditingController(text: _stripPrefix(u?.phone));
  }

  String _stripPrefix(String? p) {
    if (p == null) return '';
    if (p.startsWith('+255')) return p.substring(4);
    if (p.startsWith('0')) return p.substring(1);
    return p;
  }

  @override
  void dispose() {
    for (final c in [_bizName, _bizCity, _bizCountry, _bizAddr, _taxPct, _name, _email, _phone]) c.dispose();
    super.dispose();
  }

  Future<void> _save() async {
    setState(() { _saving = true; _err = null; });
    try {
      final u = widget.user;
      final Map<String, dynamic> body = {
        'name'             : u?.name ?? _name.text.trim(),
        'email'            : u?.email ?? _email.text.trim(),
      };
      if (widget.mode == _EditMode.business) {
        body['business_name']    = _bizName.text.trim();
        body['business_type']    = _bizType;
        body['business_city']    = _bizCity.text.trim();
        body['business_country'] = _bizCountry.text.trim().isEmpty ? 'Tanzania' : _bizCountry.text.trim();
        body['business_address'] = _bizAddr.text.trim();
      } else if (widget.mode == _EditMode.financial) {
        body['currency']          = _currency;
        body['tax_percentage']    = double.tryParse(_taxPct.text) ?? 18.0;
        body['fiscal_year_start'] = _fiscalStart;
      } else {
        body['name']  = _name.text.trim();
        body['email'] = _email.text.trim();
        if (_phone.text.trim().isNotEmpty) {
          body['phone'] = '+255${_phone.text.trim()}';
        }
      }
      await context.read<AuthProvider>().updateProfile(body);
      if (mounted) {
        Navigator.pop(context);
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Profile updated'), backgroundColor: AppColors.success, behavior: SnackBarBehavior.floating));
      }
    } catch (e) {
      setState(() { _err = e.toString().replaceAll('Exception:', '').trim(); _saving = false; });
    }
  }

  @override
  Widget build(BuildContext context) {
    final titles = {_EditMode.business: 'Edit Business Details', _EditMode.financial: 'Edit Financial Settings', _EditMode.personal: 'Edit Personal Info'};
    return Container(
      decoration: const BoxDecoration(color: AppColors.surface, borderRadius: BorderRadius.vertical(top: Radius.circular(24))),
      padding: EdgeInsets.only(bottom: MediaQuery.of(context).viewInsets.bottom),
      child: SingleChildScrollView(
        padding: const EdgeInsets.fromLTRB(24, 16, 24, 32),
        child: Column(crossAxisAlignment: CrossAxisAlignment.stretch, children: [
          Center(child: Container(width: 40, height: 4, decoration: BoxDecoration(color: AppColors.border, borderRadius: BorderRadius.circular(4)))),
          const SizedBox(height: 20),
          Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
            Text(titles[widget.mode]!, style: const TextStyle(fontSize: 19, fontWeight: FontWeight.w800)),
            IconButton(icon: const Icon(Icons.close), onPressed: () => Navigator.pop(context)),
          ]),
          if (_err != null) ...[const SizedBox(height: 12), _errBox(_err!)],
          const SizedBox(height: 16),
          if (widget.mode == _EditMode.business) _businessFields(),
          if (widget.mode == _EditMode.financial) _financialFields(),
          if (widget.mode == _EditMode.personal) _personalFields(),
          const SizedBox(height: 24),
          SizedBox(height: 52, child: ElevatedButton(
            onPressed: _saving ? null : _save,
            child: _saving ? const SizedBox(width: 22, height: 22, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5)) : const Text('Save Changes', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700)),
          )),
        ]),
      ),
    );
  }

  Widget _businessFields() => Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
    _lbl('Business Name'),
    TextFormField(controller: _bizName, decoration: const InputDecoration(labelText: 'Business name', prefixIcon: Icon(Icons.storefront_outlined, size: 20))),
    const SizedBox(height: 14),
    _lbl('Business Type'),
    const SizedBox(height: 8),
    Wrap(spacing: 8, runSpacing: 8, children: _bizTypes.map((t) {
      final (val, em, lab) = t;
      final sel = _bizType == val;
      return GestureDetector(
        onTap: () => setState(() => _bizType = val),
        child: AnimatedContainer(
          duration: const Duration(milliseconds: 180),
          padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 9),
          decoration: BoxDecoration(color: sel ? AppColors.primaryLt : Colors.white, borderRadius: BorderRadius.circular(10), border: Border.all(color: sel ? AppColors.primary : AppColors.border, width: sel ? 2 : 1)),
          child: Row(mainAxisSize: MainAxisSize.min, children: [Text(em, style: const TextStyle(fontSize: 15)), const SizedBox(width: 6), Text(lab, style: TextStyle(color: sel ? AppColors.primary : AppColors.textPri, fontWeight: sel ? FontWeight.w700 : FontWeight.w500))]),
        ),
      );
    }).toList()),
    const SizedBox(height: 14),
    Row(children: [
      Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [_lbl('City'), TextFormField(controller: _bizCity, decoration: const InputDecoration(hintText: 'Dar es Salaam'))])),
      const SizedBox(width: 12),
      Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [_lbl('Country'), TextFormField(controller: _bizCountry)])),
    ]),
    const SizedBox(height: 14),
    _lbl('Address (optional)'),
    TextFormField(controller: _bizAddr, decoration: const InputDecoration(hintText: 'Street, building...')),
  ]);

  Widget _financialFields() => Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
    _lbl('Currency'),
    DropdownButtonFormField<String>(
      value: _currency,
      decoration: const InputDecoration(prefixIcon: Icon(Icons.payments_outlined, size: 20)),
      isExpanded: true,
      items: _currencies.map((c) { final (code, flag, _) = c; return DropdownMenuItem(value: code, child: Text('$flag  $code')); }).toList(),
      onChanged: (v) => setState(() => _currency = v!),
    ),
    const SizedBox(height: 14),
    _lbl('VAT / Tax Rate (%)'),
    TextFormField(
      controller: _taxPct,
      keyboardType: const TextInputType.numberWithOptions(decimal: true),
      decoration: const InputDecoration(hintText: '18.0', prefixIcon: Icon(Icons.percent_outlined, size: 20), helperText: 'Tanzania standard VAT is 18%'),
    ),
    const SizedBox(height: 14),
    _lbl('Fiscal Year Starts'),
    DropdownButtonFormField<String>(
      value: _fiscalStart,
      decoration: const InputDecoration(prefixIcon: Icon(Icons.calendar_month_outlined, size: 20)),
      items: _months.map((m) => DropdownMenuItem(value: m, child: Text(m))).toList(),
      onChanged: (v) => setState(() => _fiscalStart = v!),
    ),
  ]);

  Widget _personalFields() => Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
    _lbl('Full Name'),
    TextFormField(controller: _name, decoration: const InputDecoration(prefixIcon: Icon(Icons.person_outline, size: 20))),
    const SizedBox(height: 14),
    _lbl('Phone Number'),
    _phoneField(),
    const SizedBox(height: 4),
    const Text('9 digits  •  e.g. 712 345 678', style: TextStyle(color: AppColors.textSec, fontSize: 11)),
    const SizedBox(height: 14),
    _lbl('Email Address'),
    TextFormField(controller: _email, keyboardType: TextInputType.emailAddress, decoration: const InputDecoration(prefixIcon: Icon(Icons.email_outlined, size: 20))),
  ]);

  Widget _phoneField() => Container(
    decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(12), border: Border.all(color: AppColors.border)),
    child: Row(children: [
      Container(padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 14), decoration: const BoxDecoration(color: AppColors.primaryLt, borderRadius: BorderRadius.only(topLeft: Radius.circular(11), bottomLeft: Radius.circular(11))),
        child: const Row(mainAxisSize: MainAxisSize.min, children: [Text('🇹🇿', style: TextStyle(fontSize: 18)), SizedBox(width: 6), Text('+255', style: TextStyle(fontWeight: FontWeight.w800, color: AppColors.primary, fontSize: 14))])),
      Container(width: 1, height: 28, color: AppColors.border),
      Expanded(child: TextField(controller: _phone, keyboardType: TextInputType.number,
        inputFormatters: [FilteringTextInputFormatter.digitsOnly, LengthLimitingTextInputFormatter(9)],
        style: const TextStyle(fontWeight: FontWeight.w600, letterSpacing: 2),
        decoration: const InputDecoration(hintText: '7XX XXX XXX', border: InputBorder.none, enabledBorder: InputBorder.none, focusedBorder: InputBorder.none, contentPadding: EdgeInsets.symmetric(horizontal: 14, vertical: 14)))),
    ]),
  );

  Widget _lbl(String t) => Padding(padding: const EdgeInsets.only(bottom: 8), child: Text(t, style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w700, color: AppColors.textPri)));
  Widget _errBox(String e) => Container(padding: const EdgeInsets.all(12), decoration: BoxDecoration(color: AppColors.dangerLt, borderRadius: BorderRadius.circular(10), border: Border.all(color: AppColors.danger.withValues(alpha: 0.3))), child: Row(children: [const Icon(Icons.error_outline, color: AppColors.danger, size: 18), const SizedBox(width: 8), Expanded(child: Text(e, style: const TextStyle(color: AppColors.danger, fontSize: 13)))]));
}

// ── Change Password Sheet ─────────────────────────────────────────────────────
class _ChangePasswordSheet extends StatefulWidget {
  const _ChangePasswordSheet();
  @override State<_ChangePasswordSheet> createState() => _ChangePasswordSheetState();
}

class _ChangePasswordSheetState extends State<_ChangePasswordSheet> {
  final _cur     = TextEditingController();
  final _new     = TextEditingController();
  final _confirm = TextEditingController();
  bool _showCur  = false, _showNew = false;
  bool _saving   = false;
  String? _err;

  @override void dispose() { _cur.dispose(); _new.dispose(); _confirm.dispose(); super.dispose(); }

  Future<void> _save() async {
    if (_new.text != _confirm.text) { setState(() => _err = 'Passwords do not match'); return; }
    if (_new.text.length < 8) { setState(() => _err = 'Password must be at least 8 characters'); return; }
    setState(() { _saving = true; _err = null; });
    try {
      final u = context.read<AuthProvider>().user;
      await context.read<AuthProvider>().updateProfile({
        'name': u?.name ?? '', 'email': u?.email ?? '',
        'current_password': _cur.text,
        'password': _new.text,
        'password_confirmation': _confirm.text,
      });
      if (mounted) {
        Navigator.pop(context);
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Password updated'), backgroundColor: AppColors.success, behavior: SnackBarBehavior.floating));
      }
    } catch (e) {
      setState(() { _err = e.toString().replaceAll('Exception:', '').trim(); _saving = false; });
    }
  }

  @override
  Widget build(BuildContext context) => Container(
    decoration: const BoxDecoration(color: AppColors.surface, borderRadius: BorderRadius.vertical(top: Radius.circular(24))),
    padding: EdgeInsets.only(bottom: MediaQuery.of(context).viewInsets.bottom),
    child: Padding(padding: const EdgeInsets.fromLTRB(24, 16, 24, 32), child: Column(mainAxisSize: MainAxisSize.min, crossAxisAlignment: CrossAxisAlignment.stretch, children: [
      Center(child: Container(width: 40, height: 4, decoration: BoxDecoration(color: AppColors.border, borderRadius: BorderRadius.circular(4)))),
      const SizedBox(height: 20),
      Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [const Text('Change Password', style: TextStyle(fontSize: 19, fontWeight: FontWeight.w800)), IconButton(icon: const Icon(Icons.close), onPressed: () => Navigator.pop(context))]),
      if (_err != null) ...[const SizedBox(height: 12), Container(padding: const EdgeInsets.all(12), decoration: BoxDecoration(color: AppColors.dangerLt, borderRadius: BorderRadius.circular(10)), child: Row(children: [const Icon(Icons.error_outline, color: AppColors.danger, size: 18), const SizedBox(width: 8), Expanded(child: Text(_err!, style: const TextStyle(color: AppColors.danger)))]))],
      const SizedBox(height: 16),
      TextFormField(controller: _cur, obscureText: !_showCur, decoration: InputDecoration(labelText: 'Current Password', prefixIcon: const Icon(Icons.lock_outline, size: 20), suffixIcon: IconButton(icon: Icon(_showCur ? Icons.visibility_off_outlined : Icons.visibility_outlined, color: AppColors.textSec), onPressed: () => setState(() => _showCur = !_showCur)))),
      const SizedBox(height: 12),
      TextFormField(controller: _new, obscureText: !_showNew, decoration: InputDecoration(labelText: 'New Password (min. 8 chars)', prefixIcon: const Icon(Icons.lock_outline, size: 20), suffixIcon: IconButton(icon: Icon(_showNew ? Icons.visibility_off_outlined : Icons.visibility_outlined, color: AppColors.textSec), onPressed: () => setState(() => _showNew = !_showNew)))),
      const SizedBox(height: 12),
      TextFormField(controller: _confirm, obscureText: true, decoration: const InputDecoration(labelText: 'Confirm New Password', prefixIcon: Icon(Icons.lock_outline, size: 20))),
      const SizedBox(height: 24),
      SizedBox(height: 52, child: ElevatedButton(onPressed: _saving ? null : _save, child: _saving ? const SizedBox(width: 22, height: 22, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5)) : const Text('Update Password', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700)))),
    ])),
  );
}