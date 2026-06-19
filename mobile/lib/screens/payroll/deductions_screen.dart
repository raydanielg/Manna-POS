import 'package:flutter/material.dart';
import '../../core/api_service.dart';
import '../../shared/theme/app_theme.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/shimmer_loading.dart';
import '../../widgets/empty_state.dart';
import '../../widgets/toast_helper.dart';
import '../../widgets/app_bottom_sheet.dart';
import '../../widgets/confirm_dialog.dart';

class DeductionsScreen extends StatefulWidget {
  const DeductionsScreen({super.key});
  @override State<DeductionsScreen> createState() => _DeductionsScreenState();
}

class _DeductionsScreenState extends State<DeductionsScreen> {
  bool _loading = true;
  String? _error;
  List<dynamic> _deductions = [];

  @override
  void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    setState(() { _loading = true; _error = null; });
    try {
      final res = await ApiService.get('/payroll/deductions');
      setState(() { _deductions = res is List ? res : (res['data'] ?? []); _loading = false; });
    } on ApiException catch (e) { setState(() { _error = e.message; _loading = false; }); }
    catch (_) { setState(() { _error = 'Connection error'; _loading = false; }); }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        title: const Text('Deduction Types', style: TextStyle(fontWeight: FontWeight.w800, color: Color(0xFF0F172A), fontSize: 20)),
        backgroundColor: Colors.white, elevation: 0, centerTitle: true,
        actions: [IconButton(icon: const Icon(Icons.refresh_rounded, color: AppColors.primary), onPressed: _load)],
      ),
      body: _loading
          ? const ShimmerLoading(itemCount: 5)
          : _error != null
              ? Center(child: Column(mainAxisAlignment: MainAxisAlignment.center, children: [
                  const Icon(Icons.error_outline, size: 48, color: Colors.grey),
                  const SizedBox(height: 12), Text(_error!),
                  const SizedBox(height: 16), ElevatedButton(onPressed: _load, child: const Text('Retry')),
                ]))
              : _deductions.isEmpty
                  ? const EmptyState(icon: Icons.money_off_outlined, title: 'No Deductions', subtitle: 'Add deduction types for payroll')
                  : RefreshIndicator(
                      onRefresh: _load,
                      child: ListView.builder(
                        padding: const EdgeInsets.fromLTRB(16, 12, 16, 100),
                        itemCount: _deductions.length,
                        itemBuilder: (_, i) => _deductionTile(_deductions[i]),
                      ),
                    ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () => _showForm(null),
        icon: const Icon(Icons.add),
        label: const Text('Add Deduction'),
        backgroundColor: AppColors.primary, foregroundColor: Colors.white,
      ),
    );
  }

  Widget _deductionTile(Map<String, dynamic> d) {
    final isPercentage = d['type']?.toString().toLowerCase() == 'percentage';
    return Dismissible(
      key: ValueKey(d['id'] ?? ''),
      direction: DismissDirection.endToStart,
      confirmDismiss: (_) => ConfirmDialog.show(context,
        title: 'Delete Deduction',
        message: 'Delete "${d['name']}"?',
        confirmLabel: 'Delete',
        icon: Icons.delete_outline_rounded,
      ),
      onDismissed: (_) => _delete(d['id']),
      background: Container(alignment: Alignment.centerRight, padding: const EdgeInsets.only(right: 20),
        decoration: BoxDecoration(color: AppColors.danger, borderRadius: BorderRadius.circular(14)),
        child: const Icon(Icons.delete_outline, color: Colors.white)),
      child: Padding(
        padding: const EdgeInsets.only(bottom: 10),
        child: GlassCard(
          onTap: () => _showForm(d),
          padding: const EdgeInsets.all(14),
          child: Row(children: [
            Container(width: 46, height: 46, decoration: BoxDecoration(
              gradient: LinearGradient(colors: [AppColors.secondary, AppColors.secondary.withValues(alpha: 0.7)], begin: Alignment.topLeft, end: Alignment.bottomRight),
              borderRadius: BorderRadius.circular(12)),
              child: const Icon(Icons.money_off_rounded, color: Colors.white, size: 22)),
            const SizedBox(width: 14),
            Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
              Text(d['name']?.toString() ?? '', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 15, color: AppColors.textPri)),
              const SizedBox(height: 2),
              Row(children: [
                Container(padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                  decoration: BoxDecoration(color: AppColors.secondaryLt, borderRadius: BorderRadius.circular(20)),
                  child: Text(d['type']?.toString()?.toUpperCase() ?? 'FIXED', style: TextStyle(fontSize: 9, fontWeight: FontWeight.w800, color: AppColors.secondary))),
                const SizedBox(width: 8),
                Text(isPercentage ? '${d['value']}%' : 'TSh ${d['value'] ?? 0}', style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w700, color: AppColors.textPri)),
              ]),
              if (d['description']?.toString().isNotEmpty == true)
                Padding(padding: const EdgeInsets.only(top: 4), child: Text(d['description']?.toString() ?? '', style: const TextStyle(fontSize: 11, color: AppColors.textSec), maxLines: 1, overflow: TextOverflow.ellipsis)),
            ])),
            const Icon(Icons.chevron_right, size: 18, color: Color(0xFFBBBBBB)),
          ]),
        ),
      ),
    );
  }

  void _showForm(Map<String, dynamic>? deduction) {
    AppBottomSheet.show(context, title: deduction != null ? 'Edit Deduction' : 'Add Deduction', child: _DeductionForm(
      deduction: deduction,
      onSaved: () { _load(); Navigator.pop(context); },
    ));
  }

  Future<void> _delete(dynamic id) async {
    try {
      await ApiService.delete('/payroll/deductions/$id');
      ToastHelper.success(context, 'Deduction deleted');
      _load();
    } catch (_) { ToastHelper.error(context, 'Delete failed'); }
  }
}

class _DeductionForm extends StatefulWidget {
  final Map<String, dynamic>? deduction;
  final VoidCallback onSaved;
  const _DeductionForm({this.deduction, required this.onSaved});
  @override State<_DeductionForm> createState() => _DeductionFormState();
}

class _DeductionFormState extends State<_DeductionForm> {
  final _form = GlobalKey<FormState>();
  late TextEditingController _name, _value, _desc;
  String _type = 'fixed';
  bool _saving = false;
  String? _err;
  final _types = ['fixed', 'percentage'];

  @override
  void initState() {
    super.initState();
    final d = widget.deduction;
    _name = TextEditingController(text: d?['name']?.toString());
    _value = TextEditingController(text: d?['value']?.toString());
    _desc = TextEditingController(text: d?['description']?.toString());
    if (d != null) _type = d['type']?.toString() ?? 'fixed';
  }

  @override void dispose() { for (final c in [_name, _value, _desc]) c.dispose(); super.dispose(); }

  Future<void> _save() async {
    if (!_form.currentState!.validate()) return;
    setState(() { _saving = true; _err = null; });
    final body = {
      'name': _name.text.trim(),
      'type': _type,
      'value': double.tryParse(_value.text) ?? 0,
      'description': _desc.text.trim(),
    };
    try {
      if (widget.deduction != null) await ApiService.put('/payroll/deductions/${widget.deduction!['id']}', body);
      else await ApiService.post('/payroll/deductions', body);
      widget.onSaved();
    } on ApiException catch (e) { setState(() { _err = e.message; _saving = false; }); }
    catch (_) { setState(() { _err = 'Save failed'; _saving = false; }); }
  }

  @override
  Widget build(BuildContext context) => Form(key: _form, child: Column(crossAxisAlignment: CrossAxisAlignment.stretch, children: [
    if (_err != null) Container(padding: const EdgeInsets.all(12), margin: const EdgeInsets.only(bottom: 12),
      decoration: BoxDecoration(color: AppColors.dangerLt, borderRadius: BorderRadius.circular(10)),
      child: Text(_err!, style: const TextStyle(color: AppColors.danger))),
    TextFormField(controller: _name, decoration: const InputDecoration(labelText: 'Deduction Name *'), validator: (v) => v!.isNotEmpty ? null : 'Required'),
    const SizedBox(height: 12),
    DropdownButtonFormField<String>(value: _type, decoration: const InputDecoration(labelText: 'Type'),
      items: _types.map((t) => DropdownMenuItem(value: t, child: Text(t[0].toUpperCase() + t.substring(1)))).toList(),
      onChanged: (v) => setState(() => _type = v!)),
    const SizedBox(height: 12),
    TextFormField(controller: _value, keyboardType: TextInputType.number,
      decoration: InputDecoration(labelText: 'Value *', suffixText: _type == 'percentage' ? '%' : 'TSh'),
      validator: (v) => (v != null && double.tryParse(v) != null) ? null : 'Enter valid value'),
    const SizedBox(height: 12),
    TextFormField(controller: _desc, maxLines: 2, decoration: const InputDecoration(labelText: 'Description')),
    const SizedBox(height: 24),
    SizedBox(height: 52, child: ElevatedButton(onPressed: _saving ? null : _save,
      child: _saving ? const SizedBox(width: 22, height: 22, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5))
          : Text(widget.deduction != null ? 'Update Deduction' : 'Add Deduction', style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w700)))),
  ]));
}
