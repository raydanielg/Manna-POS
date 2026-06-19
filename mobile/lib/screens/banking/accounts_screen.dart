import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../core/api_service.dart';
import '../../shared/theme/app_theme.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/shimmer_loading.dart';
import '../../widgets/empty_state.dart';
import '../../widgets/toast_helper.dart';
import '../../widgets/confirm_dialog.dart';
import '../../widgets/app_bottom_sheet.dart';
import '../../widgets/status_badge.dart';

class AccountsScreen extends StatefulWidget {
  const AccountsScreen({super.key});
  @override State<AccountsScreen> createState() => _AccountsScreenState();
}

class _AccountsScreenState extends State<AccountsScreen> {
  bool _loading = true;
  String? _error;
  List<dynamic> _accounts = [];
  final _fmt = NumberFormat('#,##0.00');
  final _currency = 'TSh';

  @override
  void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    setState(() { _loading = true; _error = null; });
    try {
      final res = await ApiService.get('/dashboard/banking/accounts');
      setState(() { _accounts = res is List ? res : (res['data'] ?? []); _loading = false; });
    } on ApiException catch (e) { setState(() { _error = e.message; _loading = false; }); }
    catch (_) { setState(() { _error = 'Connection error'; _loading = false; }); }
  }

  String _f(dynamic v) => '$_currency ${_fmt.format((v is num ? v.toDouble() : double.tryParse(v?.toString() ?? '0') ?? 0))}';

  double get _totalBalance => _accounts.fold(0.0, (s, a) => s + ((a['balance'] as num?)?.toDouble() ?? 0));

  Color _typeColor(String type) {
    switch (type.toLowerCase()) {
      case 'savings': return AppColors.primary;
      case 'current': return AppColors.success;
      case 'fixed': return AppColors.purple;
      case 'loan': return AppColors.secondary;
      default: return AppColors.cyan;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      body: _loading
          ? const ShimmerLoading(itemCount: 5)
          : _error != null
              ? Center(child: Column(mainAxisAlignment: MainAxisAlignment.center, children: [
                  const Icon(Icons.error_outline, size: 48, color: Colors.grey),
                  const SizedBox(height: 12), Text(_error!),
                  const SizedBox(height: 16), ElevatedButton(onPressed: _load, child: const Text('Retry')),
                ]))
              : RefreshIndicator(
                  onRefresh: _load,
                  child: CustomScrollView(slivers: [
                    SliverAppBar(
                      floating: true, pinned: true, elevation: 0,
                      backgroundColor: const Color(0xFFF8FAFC),
                      title: const Text('Bank Accounts', style: TextStyle(fontWeight: FontWeight.w800, color: Color(0xFF0F172A), fontSize: 20)),
                      actions: [IconButton(icon: const Icon(Icons.refresh_rounded, color: AppColors.primary), onPressed: _load)],
                    ),
                    if (_accounts.isNotEmpty)
                      SliverToBoxAdapter(child: Padding(
                        padding: const EdgeInsets.fromLTRB(16, 0, 16, 12),
                        child: GlassCard(
                          child: Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
                            const Text('Total Balance', style: TextStyle(fontSize: 14, fontWeight: FontWeight.w600, color: AppColors.textPri)),
                            Text(_f(_totalBalance), style: const TextStyle(fontSize: 22, fontWeight: FontWeight.w900, color: AppColors.primary, letterSpacing: -0.5)),
                          ]),
                        ),
                      )),
                    if (_accounts.isEmpty)
                      const SliverFillRemaining(child: EmptyState(icon: Icons.account_balance_outlined, title: 'No Accounts', subtitle: 'Add a bank account to get started'))
                    else
                      SliverPadding(
                        padding: const EdgeInsets.fromLTRB(16, 0, 16, 100),
                        sliver: SliverList(
                          delegate: SliverChildBuilderDelegate((_, i) {
                            final a = _accounts[i];
                            final type = a['type']?.toString() ?? 'savings';
                            final color = _typeColor(type);
                            return Dismissible(
                              key: ValueKey(a['id'] ?? i),
                              direction: DismissDirection.endToStart,
                              confirmDismiss: (_) => ConfirmDialog.show(context,
                                title: 'Delete Account',
                                message: 'Delete "${a['account_name']}"?',
                                confirmLabel: 'Delete',
                                icon: Icons.delete_outline_rounded,
                              ),
                              onDismissed: (_) => _delete(a['id']),
                              background: Container(alignment: Alignment.centerRight, padding: const EdgeInsets.only(right: 20),
                                decoration: BoxDecoration(color: AppColors.danger, borderRadius: BorderRadius.circular(14)),
                                child: const Icon(Icons.delete_outline, color: Colors.white)),
                              child: Padding(
                                padding: const EdgeInsets.only(bottom: 12),
                                child: GestureDetector(
                                  onTap: () => _showForm(a),
                                  child: GlassCard(
                                    child: Row(children: [
                                      Container(width: 48, height: 48, decoration: BoxDecoration(color: color.withValues(alpha: 0.12), borderRadius: BorderRadius.circular(12)),
                                        child: Icon(Icons.account_balance, color: color, size: 22)),
                                      const SizedBox(width: 14),
                                      Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                                        Text(a['account_name']?.toString() ?? '', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14, color: AppColors.textPri)),
                                        const SizedBox(height: 2),
                                        Text(a['account_number']?.toString() ?? '', style: const TextStyle(fontSize: 12, color: AppColors.textSec)),
                                        Row(children: [
                                          StatusBadge.fromStatus(type),
                                          const SizedBox(width: 8),
                                          Text(a['bank_name']?.toString() ?? '', style: const TextStyle(fontSize: 11, color: AppColors.textLight)),
                                        ]),
                                      ])),
                                      Column(crossAxisAlignment: CrossAxisAlignment.end, children: [
                                        Text(_f(a['balance'] ?? 0), style: TextStyle(fontWeight: FontWeight.w800, fontSize: 14, color: color)),
                                        const SizedBox(height: 4),
                                        const Icon(Icons.chevron_right, size: 18, color: Color(0xFFBBBBBB)),
                                      ]),
                                    ]),
                                  ),
                                ),
                              ),
                            );
                          }, childCount: _accounts.length),
                        ),
                      ),
                  ]),
                ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () => _showForm(null),
        icon: const Icon(Icons.add),
        label: const Text('Add Account'),
        backgroundColor: AppColors.primary,
        foregroundColor: Colors.white,
      ),
    );
  }

  void _showForm(Map<String, dynamic>? account) {
    AppBottomSheet.show(context, title: account != null ? 'Edit Account' : 'Add Account', child: _AccountForm(
      account: account,
      onSaved: () { _load(); Navigator.pop(context); },
    ));
  }

  Future<void> _delete(dynamic id) async {
    try {
      await ApiService.delete('/dashboard/banking/accounts/$id');
      ToastHelper.success(context, 'Account deleted');
      _load();
    } catch (_) { ToastHelper.error(context, 'Delete failed'); }
  }
}

class _AccountForm extends StatefulWidget {
  final Map<String, dynamic>? account;
  final VoidCallback onSaved;
  const _AccountForm({this.account, required this.onSaved});
  @override State<_AccountForm> createState() => _AccountFormState();
}

class _AccountFormState extends State<_AccountForm> {
  final _form = GlobalKey<FormState>();
  late TextEditingController _name, _number, _bank, _balance;
  String _type = 'savings';
  bool _saving = false;
  String? _err;

  final _types = ['savings', 'current', 'fixed', 'loan'];

  @override
  void initState() {
    super.initState();
    final a = widget.account;
    _name = TextEditingController(text: a?['account_name']?.toString());
    _number = TextEditingController(text: a?['account_number']?.toString());
    _bank = TextEditingController(text: a?['bank_name']?.toString());
    _balance = TextEditingController(text: a?['balance']?.toString());
    if (a != null) _type = a['type']?.toString() ?? 'savings';
  }

  @override void dispose() { for (final c in [_name, _number, _bank, _balance]) c.dispose(); super.dispose(); }

  Future<void> _save() async {
    if (!_form.currentState!.validate()) return;
    setState(() { _saving = true; _err = null; });
    final body = {
      'account_name': _name.text.trim(),
      'account_number': _number.text.trim(),
      'bank_name': _bank.text.trim(),
      'type': _type,
      'balance': double.tryParse(_balance.text) ?? 0,
    };
    try {
      if (widget.account != null) await ApiService.put('/dashboard/banking/accounts/${widget.account!['id']}', body);
      else await ApiService.post('/dashboard/banking/accounts', body);
      widget.onSaved();
    } on ApiException catch (e) { setState(() { _err = e.message; _saving = false; }); }
    catch (_) { setState(() { _err = 'Save failed'; _saving = false; }); }
  }

  @override
  Widget build(BuildContext context) {
    return Form(key: _form, child: Column(crossAxisAlignment: CrossAxisAlignment.stretch, children: [
      if (_err != null) Container(padding: const EdgeInsets.all(12), margin: const EdgeInsets.only(bottom: 12),
        decoration: BoxDecoration(color: AppColors.dangerLt, borderRadius: BorderRadius.circular(10)),
        child: Text(_err!, style: const TextStyle(color: AppColors.danger))),
      TextFormField(controller: _name, decoration: const InputDecoration(labelText: 'Account Name *'), validator: (v) => v!.isNotEmpty ? null : 'Required'),
      const SizedBox(height: 12),
      TextFormField(controller: _number, decoration: const InputDecoration(labelText: 'Account Number *'), validator: (v) => v!.isNotEmpty ? null : 'Required'),
      const SizedBox(height: 12),
      TextFormField(controller: _bank, decoration: const InputDecoration(labelText: 'Bank Name *'), validator: (v) => v!.isNotEmpty ? null : 'Required'),
      const SizedBox(height: 12),
      DropdownButtonFormField<String>(value: _type, decoration: const InputDecoration(labelText: 'Account Type'),
        items: _types.map((t) => DropdownMenuItem(value: t, child: Text(t[0].toUpperCase() + t.substring(1)))).toList(),
        onChanged: (v) => setState(() => _type = v!)),
      const SizedBox(height: 12),
      TextFormField(controller: _balance, keyboardType: TextInputType.number, decoration: const InputDecoration(labelText: 'Opening Balance')),
      const SizedBox(height: 24),
      SizedBox(height: 52, child: ElevatedButton(onPressed: _saving ? null : _save,
        child: _saving ? const SizedBox(width: 22, height: 22, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5))
            : Text(widget.account != null ? 'Update Account' : 'Add Account', style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w700)))),
    ]));
  }
}
