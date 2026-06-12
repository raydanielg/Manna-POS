import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import '../../core/api_service.dart';
import '../../shared/theme/app_theme.dart';

class StaffManagementPage extends StatefulWidget {
  const StaffManagementPage({super.key});
  @override State<StaffManagementPage> createState() => _StaffManagementPageState();
}

class _StaffManagementPageState extends State<StaffManagementPage> {
  List<Map<String, dynamic>> _staff = [];
  bool _loading = true;
  String? _error;

  @override void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    setState(() { _loading = true; _error = null; });
    try {
      final data = await ApiService.get('/staff');
      setState(() { _staff = (data as List).map((e) => Map<String,dynamic>.from(e)).toList(); _loading = false; });
    } catch (e) { setState(() { _error = e.toString(); _loading = false; }); }
  }

  Future<void> _delete(Map<String, dynamic> member) async {
    final ok = await showDialog<bool>(context: context, builder: (_) => AlertDialog(
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
      title: const Text('Remove Staff Member'),
      content: Text('Remove "${member['name']}"?\nThey will no longer be able to log in.'),
      actions: [
        TextButton(onPressed: () => Navigator.pop(context, false), child: const Text('Cancel')),
        ElevatedButton(
          style: ElevatedButton.styleFrom(backgroundColor: AppColors.danger),
          onPressed: () => Navigator.pop(context, true),
          child: const Text('Remove')),
      ],
    ));
    if (ok != true || !mounted) return;
    try {
      await ApiService.delete('/staff/${member['id']}');
      _snack('${member['name']} removed');
      _load();
    } on ApiException catch (e) { _snack(e.message, error: true); }
  }

  void _showForm([Map<String, dynamic>? member]) {
    showModalBottomSheet(
      context: context, isScrollControlled: true, backgroundColor: Colors.transparent,
      builder: (_) => _StaffForm(member: member, onSaved: _load),
    );
  }

  void _snack(String msg, {bool error = false}) {
    if (!mounted) return;
    ScaffoldMessenger.of(context).showSnackBar(SnackBar(
      content: Text(msg),
      backgroundColor: error ? AppColors.danger : AppColors.success,
      behavior: SnackBarBehavior.floating,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
    ));
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(
        backgroundColor: AppColors.primary,
        foregroundColor: Colors.white,
        title: const Text('Staff Management', style: TextStyle(color: Colors.white, fontWeight: FontWeight.w700)),
        actions: [
          IconButton(icon: const Icon(Icons.refresh, color: Colors.white), onPressed: _load),
        ],
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () => _showForm(),
        backgroundColor: AppColors.primary,
        icon: const Icon(Icons.person_add_outlined),
        label: const Text('Add Staff'),
      ),
      body: _loading
        ? const Center(child: CircularProgressIndicator(color: AppColors.primary))
        : _error != null
          ? Center(child: Column(mainAxisSize: MainAxisSize.min, children: [
              const Icon(Icons.wifi_off_rounded, size: 56, color: AppColors.textSec),
              const SizedBox(height: 16),
              const Text('Failed to load staff', style: TextStyle(fontWeight: FontWeight.w600, color: AppColors.textSec)),
              const SizedBox(height: 12),
              ElevatedButton(onPressed: _load, child: const Text('Retry')),
            ]))
          : _staff.isEmpty
            ? Center(child: Column(mainAxisSize: MainAxisSize.min, children: [
                Container(width: 80, height: 80, decoration: BoxDecoration(color: AppColors.primaryLt, shape: BoxShape.circle),
                  child: const Icon(Icons.group_outlined, size: 40, color: AppColors.primary)),
                const SizedBox(height: 20),
                const Text('No staff yet', style: TextStyle(fontSize: 18, fontWeight: FontWeight.w700, color: AppColors.textPri)),
                const SizedBox(height: 8),
                const Text('Add your first staff member\nto get started', textAlign: TextAlign.center, style: TextStyle(color: AppColors.textSec, height: 1.5)),
                const SizedBox(height: 24),
                ElevatedButton.icon(
                  onPressed: () => _showForm(),
                  icon: const Icon(Icons.person_add_outlined),
                  label: const Text('Add Staff Member'),
                  style: ElevatedButton.styleFrom(padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 14)),
                ),
              ]))
            : Column(children: [
                // Summary bar
                Container(
                  color: Colors.white,
                  padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 14),
                  child: Row(children: [
                    Container(width: 40, height: 40, decoration: BoxDecoration(color: AppColors.primaryLt, borderRadius: BorderRadius.circular(12)),
                      child: const Icon(Icons.group, color: AppColors.primary, size: 20)),
                    const SizedBox(width: 12),
                    Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                      Text('${_staff.length} Staff Member${_staff.length != 1 ? 's' : ''}',
                        style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 16, color: AppColors.textPri)),
                      Text('${_staff.where((s) => s['role'] == 'admin').length} Admin · '
                          '${_staff.where((s) => s['role'] == 'manager').length} Manager · '
                          '${_staff.where((s) => s['role'] == 'cashier').length} Cashier',
                        style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
                    ]),
                  ]),
                ),
                Expanded(child: RefreshIndicator(
                  color: AppColors.primary,
                  onRefresh: _load,
                  child: ListView.separated(
                    padding: const EdgeInsets.fromLTRB(16, 16, 16, 100),
                    itemCount: _staff.length,
                    separatorBuilder: (_, __) => const SizedBox(height: 10),
                    itemBuilder: (_, i) => _staffCard(_staff[i]),
                  ),
                )),
              ]),
    );
  }

  Widget _staffCard(Map<String, dynamic> member) {
    final role = member['role'] ?? 'cashier';
    final name = member['name'] ?? 'Unknown';
    final initials = name.trim().split(' ').where((w) => (w as String).isNotEmpty).map((w) => (w as String)[0]).take(2).join().toUpperCase();

    final roleData = _roleInfo(role);

    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppColors.border),
        boxShadow: const [BoxShadow(color: Color(0x06000000), blurRadius: 10, offset: Offset(0, 2))],
      ),
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Row(children: [
          // Avatar
          Container(
            width: 52, height: 52,
            decoration: BoxDecoration(
              gradient: LinearGradient(colors: [roleData.$1, roleData.$1.withValues(alpha: 0.6)], begin: Alignment.topLeft, end: Alignment.bottomRight),
              borderRadius: BorderRadius.circular(16),
            ),
            child: Center(child: Text(initials, style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w800, fontSize: 18))),
          ),
          const SizedBox(width: 14),
          // Info
          Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            Text(name, style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 16, color: AppColors.textPri)),
            const SizedBox(height: 3),
            Text(member['email'] ?? '', style: const TextStyle(color: AppColors.textSec, fontSize: 13)),
            if ((member['phone'] ?? '').toString().isNotEmpty) ...[
              const SizedBox(height: 2),
              Text(member['phone'], style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
            ],
            const SizedBox(height: 8),
            Row(children: [
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                decoration: BoxDecoration(color: roleData.$2, borderRadius: BorderRadius.circular(20)),
                child: Row(mainAxisSize: MainAxisSize.min, children: [
                  Icon(roleData.$3, size: 12, color: roleData.$1),
                  const SizedBox(width: 4),
                  Text(roleData.$4, style: TextStyle(color: roleData.$1, fontSize: 11, fontWeight: FontWeight.w800)),
                ]),
              ),
              if (member['created_at'] != null) ...[
                const SizedBox(width: 8),
                Text('Since ${member['created_at']}', style: const TextStyle(color: AppColors.textSec, fontSize: 11)),
              ],
            ]),
          ])),
          // Actions
          Column(mainAxisSize: MainAxisSize.min, children: [
            GestureDetector(
              onTap: () => _showForm(member),
              child: Container(width: 36, height: 36, decoration: BoxDecoration(color: AppColors.primaryLt, borderRadius: BorderRadius.circular(10)),
                child: const Icon(Icons.edit_outlined, size: 18, color: AppColors.primary)),
            ),
            const SizedBox(height: 6),
            GestureDetector(
              onTap: () => _delete(member),
              child: Container(width: 36, height: 36, decoration: BoxDecoration(color: AppColors.dangerLt, borderRadius: BorderRadius.circular(10)),
                child: const Icon(Icons.person_remove_outlined, size: 18, color: AppColors.danger)),
            ),
          ]),
        ]),
      ),
    );
  }

  (Color, Color, IconData, String) _roleInfo(String role) => switch (role) {
    'admin'   => (AppColors.primary,   AppColors.primaryLt,           Icons.admin_panel_settings_outlined, 'ADMIN'),
    'manager' => (AppColors.secondary, const Color(0xFFF5F3FF),        Icons.manage_accounts_outlined,      'MANAGER'),
    'cashier' => (AppColors.success,   AppColors.successLt,            Icons.point_of_sale_outlined,        'CASHIER'),
    _         => (AppColors.textSec,   AppColors.border,               Icons.visibility_outlined,           'VIEWER'),
  };
}

// -- Staff Form ---------------------------------------------------------
class _StaffForm extends StatefulWidget {
  final Map<String, dynamic>? member;
  final VoidCallback onSaved;
  const _StaffForm({this.member, required this.onSaved});
  @override State<_StaffForm> createState() => _StaffFormState();
}

class _StaffFormState extends State<_StaffForm> {
  final _form = GlobalKey<FormState>();
  late TextEditingController _name, _email, _phone, _password;
  String _role = 'cashier';
  bool _saving = false;
  bool _showPass = false;
  String? _err;

  bool get _isEdit => widget.member != null;

  @override
  void initState() {
    super.initState();
    _name     = TextEditingController(text: widget.member?['name']);
    _email    = TextEditingController(text: widget.member?['email']);
    _phone    = TextEditingController(text: widget.member?['phone']);
    _password = TextEditingController();
    _role     = widget.member?['role'] ?? 'cashier';
  }

  @override
  void dispose() {
    for (final c in [_name, _email, _phone, _password]) c.dispose();
    super.dispose();
  }

  Future<void> _save() async {
    if (!_form.currentState!.validate()) return;
    setState(() { _saving = true; _err = null; });
    try {
      final body = <String, dynamic>{
        'name'  : _name.text.trim(),
        'email' : _email.text.trim(),
        'phone' : _phone.text.trim(),
        'role'  : _role,
      };
      if (!_isEdit || _password.text.isNotEmpty) {
        body['password'] = _password.text;
      }
      if (_isEdit) {
        await ApiService.put('/staff/${widget.member!['id']}', body);
      } else {
        await ApiService.post('/staff', body);
      }
      widget.onSaved();
      if (mounted) Navigator.pop(context);
    } on ApiException catch (e) {
      setState(() { _err = e.message; _saving = false; });
    } catch (_) {
      setState(() { _err = 'Save failed'; _saving = false; });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: const BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
      ),
      padding: EdgeInsets.only(bottom: MediaQuery.of(context).viewInsets.bottom),
      child: SingleChildScrollView(
        padding: const EdgeInsets.fromLTRB(24, 16, 24, 32),
        child: Form(key: _form, child: Column(crossAxisAlignment: CrossAxisAlignment.stretch, children: [
          Center(child: Container(width: 40, height: 4, decoration: BoxDecoration(color: AppColors.border, borderRadius: BorderRadius.circular(4)))),
          const SizedBox(height: 20),
          Row(children: [
            Container(width: 44, height: 44, decoration: BoxDecoration(color: AppColors.primaryLt, borderRadius: BorderRadius.circular(12)),
              child: const Icon(Icons.person_outlined, color: AppColors.primary, size: 22)),
            const SizedBox(width: 12),
            Text(_isEdit ? 'Edit Staff Member' : 'Add Staff Member', style: const TextStyle(fontSize: 20, fontWeight: FontWeight.w800)),
            const Spacer(),
            IconButton(icon: const Icon(Icons.close), onPressed: () => Navigator.pop(context)),
          ]),
          if (_err != null) ...[
            const SizedBox(height: 12),
            Container(padding: const EdgeInsets.all(12), decoration: BoxDecoration(color: AppColors.dangerLt, borderRadius: BorderRadius.circular(10)),
              child: Row(children: [const Icon(Icons.error_outline, color: AppColors.danger, size: 18), const SizedBox(width: 8), Expanded(child: Text(_err!, style: const TextStyle(color: AppColors.danger)))])),
          ],
          const SizedBox(height: 20),
          // Role selector first (most important)
          const Text('Role', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 13, color: AppColors.textSec)),
          const SizedBox(height: 8),
          Row(children: [
            _roleChip('admin',   'Admin',   Icons.admin_panel_settings_outlined, AppColors.primary),
            const SizedBox(width: 8),
            _roleChip('manager', 'Manager', Icons.manage_accounts_outlined,      AppColors.secondary),
            const SizedBox(width: 8),
            _roleChip('cashier', 'Cashier', Icons.point_of_sale_outlined,        AppColors.success),
            const SizedBox(width: 8),
            _roleChip('viewer',  'Viewer',  Icons.visibility_outlined,           AppColors.textSec),
          ]),
          const SizedBox(height: 8),
          // Role description
          Container(padding: const EdgeInsets.all(10), decoration: BoxDecoration(color: AppColors.bg, borderRadius: BorderRadius.circular(10), border: Border.all(color: AppColors.border)),
            child: Text(_roleDescription(_role), style: const TextStyle(color: AppColors.textSec, fontSize: 12, height: 1.4))),
          const SizedBox(height: 16),
          TextFormField(controller: _name, decoration: const InputDecoration(labelText: 'Full Name *', prefixIcon: Icon(Icons.person_outline, size: 20)), validator: (v) => v!.trim().isNotEmpty ? null : 'Name is required'),
          const SizedBox(height: 12),
          TextFormField(controller: _email, keyboardType: TextInputType.emailAddress, decoration: const InputDecoration(labelText: 'Email Address *', prefixIcon: Icon(Icons.email_outlined, size: 20)), validator: (v) => v!.contains('@') ? null : 'Valid email required'),
          const SizedBox(height: 12),
          TextFormField(controller: _phone,
            keyboardType: TextInputType.phone,
            inputFormatters: [FilteringTextInputFormatter.allow(RegExp(r'[0-9+\-\s]'))],
            decoration: const InputDecoration(labelText: 'Phone Number', prefixIcon: Icon(Icons.phone_outlined, size: 20))),
          const SizedBox(height: 12),
          TextFormField(
            controller: _password,
            obscureText: !_showPass,
            decoration: InputDecoration(
              labelText: _isEdit ? 'New Password (leave blank to keep)' : 'Password *',
              prefixIcon: const Icon(Icons.lock_outline, size: 20),
              suffixIcon: IconButton(icon: Icon(_showPass ? Icons.visibility_off_outlined : Icons.visibility_outlined, size: 20), onPressed: () => setState(() => _showPass = !_showPass)),
            ),
            validator: (v) {
              if (!_isEdit && (v == null || v.length < 6)) return 'Minimum 6 characters';
              if (_isEdit && v != null && v.isNotEmpty && v.length < 6) return 'Minimum 6 characters';
              return null;
            },
          ),
          const SizedBox(height: 28),
          SizedBox(height: 54, child: ElevatedButton.icon(
            onPressed: _saving ? null : _save,
            icon: _saving ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5)) : Icon(_isEdit ? Icons.save_outlined : Icons.person_add_outlined),
            label: Text(_saving ? 'Saving...' : (_isEdit ? 'Update Staff Member' : 'Add Staff Member'), style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w700)),
          )),
        ])),
      ),
    );
  }

  Widget _roleChip(String val, String label, IconData icon, Color color) {
    final sel = _role == val;
    return Expanded(child: GestureDetector(
      onTap: () => setState(() => _role = val),
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 180),
        padding: const EdgeInsets.symmetric(vertical: 10),
        decoration: BoxDecoration(
          color: sel ? color.withValues(alpha: 0.12) : AppColors.bg,
          borderRadius: BorderRadius.circular(10),
          border: Border.all(color: sel ? color : AppColors.border, width: sel ? 2 : 1),
        ),
        child: Column(mainAxisSize: MainAxisSize.min, children: [
          Icon(icon, size: 18, color: sel ? color : AppColors.textSec),
          const SizedBox(height: 3),
          Text(label, style: TextStyle(fontSize: 10, fontWeight: FontWeight.w700, color: sel ? color : AppColors.textSec)),
        ]),
      ),
    ));
  }

  String _roleDescription(String role) => switch (role) {
    'admin'   => 'Full access -- can manage products, sales, staff, and all settings',
    'manager' => 'Can manage products, sales, expenses, and view reports',
    'cashier' => 'Can make sales (POS) and view their own transactions only',
    _         => 'Read-only access -- can view data but cannot make changes',
  };
}