import 'package:flutter/material.dart';
import '../../core/api_service.dart';
import '../../shared/theme/app_colors.dart';
import '../../shared/widgets/app_card.dart';
import '../../shared/widgets/empty_state.dart';
import '../../shared/widgets/status_badge.dart';

class TaxRatesPage extends StatefulWidget {
  const TaxRatesPage({super.key});
  @override State<TaxRatesPage> createState() => _TaxRatesPageState();
}

class _TaxRatesPageState extends State<TaxRatesPage> {
  List<dynamic> _taxes = [];
  bool _loading = true;
  String? _error;

  @override void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    setState(() { _loading = true; _error = null; });
    try {
      final data = await ApiService.get('/tax-rates');
      setState(() { _taxes = (data as List); _loading = false; });
    } on ApiException catch (e) { setState(() { _error = e.message; _loading = false; }); }
    catch (_) { setState(() { _error = 'Connection error'; _loading = false; }); }
  }

  void _showForm([dynamic t]) => showModalBottomSheet(context: context, isScrollControlled: true, backgroundColor: Colors.transparent, builder: (_) => _TaxRateForm(tax: t, onSaved: _load));

  Future<void> _delete(dynamic t) async {
    final ok = await showDialog<bool>(context: context, builder: (_) => AlertDialog(
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
      title: const Text('Delete Tax Rate'),
      content: Text('Delete "${t['name']}"?'),
      actions: [TextButton(onPressed: () => Navigator.pop(context, false), child: const Text('Cancel')),
        ElevatedButton(style: ElevatedButton.styleFrom(backgroundColor: AppColors.danger), onPressed: () => Navigator.pop(context, true), child: const Text('Delete'))],
    ));
    if (ok != true) return;
    try { await ApiService.delete('/tax-rates/${t['id']}'); _load(); } on ApiException catch (e) { _snack(e.message, error: true); }
  }

  void _snack(String msg, {bool error = false}) => ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(msg), backgroundColor: error ? AppColors.danger : AppColors.success, behavior: SnackBarBehavior.floating, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10))));

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(title: const Text('Tax Rates'), actions: [IconButton(icon: const Icon(Icons.refresh, color: Colors.white), onPressed: _load)]),
      body: _loading ? const LoadingWidget(message: 'Loading...')
        : _error != null ? ErrorWidget2(message: _error!, onRetry: _load)
        : _taxes.isEmpty ? const EmptyState(icon: Icons.account_balance_outlined, title: 'No Tax Rates', subtitle: 'Add your first tax rate')
        : RefreshIndicator(color: AppColors.primary, onRefresh: _load,
            child: ListView.separated(padding: const EdgeInsets.fromLTRB(16, 0, 16, 100), itemCount: _taxes.length, separatorBuilder: (_, __) => const SizedBox(height: 10), itemBuilder: (_, i) => _tile(_taxes[i]))),
      floatingActionButton: FloatingActionButton.extended(onPressed: () => _showForm(), icon: const Icon(Icons.add), label: const Text('Add Tax Rate')),
    );
  }

  Widget _tile(dynamic t) => AppCard(child: Padding(padding: const EdgeInsets.all(16), child: Row(children: [
    Container(width: 46, height: 46, decoration: BoxDecoration(color: AppColors.warningLt, borderRadius: BorderRadius.circular(12)),
      child: const Icon(Icons.account_balance_outlined, color: AppColors.warning, size: 22)),
    const SizedBox(width: 14),
    Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
      Text(t['name'] ?? '', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 15)),
      const SizedBox(height: 2),
      Text('${t['rate']}% - ${t['type'] ?? ''}', style: const TextStyle(color: AppColors.textSec, fontSize: 13)),
    ])),
    StatusBadge.fromStatus(t['status'] ?? 'active'),
    const SizedBox(width: 8),
    PopupMenuButton<String>(shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)), onSelected: (v) { if (v == 'edit') _showForm(t); else _delete(t); },
      itemBuilder: (_) => [const PopupMenuItem(value: 'edit', child: Row(children: [Icon(Icons.edit_outlined, size: 18, color: AppColors.primary), SizedBox(width: 8), Text('Edit')])), const PopupMenuItem(value: 'delete', child: Row(children: [Icon(Icons.delete_outline, size: 18, color: AppColors.danger), SizedBox(width: 8), Text('Delete', style: TextStyle(color: AppColors.danger))]))]),
  ])));
}

class _TaxRateForm extends StatefulWidget {
  final dynamic tax;
  final VoidCallback onSaved;
  const _TaxRateForm({this.tax, required this.onSaved});
  @override State<_TaxRateForm> createState() => _TaxRateFormState();
}

class _TaxRateFormState extends State<_TaxRateForm> {
  final _form = GlobalKey<FormState>();
  late TextEditingController _name, _rate;
  bool _saving = false;
  String? _err;
  String _type = 'exclusive';

  @override
  void initState() {
    super.initState();
    final t = widget.tax;
    _name = TextEditingController(text: t?['name']);
    _rate = TextEditingController(text: t?['rate']?.toString());
    if (t != null) _type = t['type'] ?? 'exclusive';
  }

  @override void dispose() { _name.dispose(); _rate.dispose(); super.dispose(); }

  Future<void> _save() async {
    if (!_form.currentState!.validate()) return;
    setState(() { _saving = true; _err = null; });
    final body = {'name': _name.text.trim(), 'rate': double.tryParse(_rate.text) ?? 0, 'type': _type};
    try {
      if (widget.tax != null) await ApiService.put('/tax-rates/${widget.tax!['id']}', body);
      else await ApiService.post('/tax-rates', body);
      widget.onSaved();
      if (mounted) Navigator.pop(context);
    } on ApiException catch (e) { setState(() { _err = e.message; _saving = false; }); }
    catch (_) { setState(() { _err = 'Save failed'; _saving = false; }); }
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: const BoxDecoration(color: AppColors.surface, borderRadius: BorderRadius.vertical(top: Radius.circular(24))),
      padding: EdgeInsets.only(bottom: MediaQuery.of(context).viewInsets.bottom),
      child: SingleChildScrollView(padding: const EdgeInsets.fromLTRB(24, 16, 24, 32), child: Form(key: _form, child: Column(crossAxisAlignment: CrossAxisAlignment.stretch, children: [
        Center(child: Container(width: 40, height: 4, decoration: BoxDecoration(color: AppColors.border, borderRadius: BorderRadius.circular(4)))),
        const SizedBox(height: 20),
        Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [Text(widget.tax != null ? 'Edit Tax Rate' : 'Add Tax Rate', style: const TextStyle(fontSize: 20, fontWeight: FontWeight.w700)), IconButton(icon: const Icon(Icons.close), onPressed: () => Navigator.pop(context))]),
        if (_err != null) ...[const SizedBox(height: 12), Container(padding: const EdgeInsets.all(12), decoration: BoxDecoration(color: AppColors.dangerLt, borderRadius: BorderRadius.circular(10)), child: Text(_err!, style: const TextStyle(color: AppColors.danger)))],
        const SizedBox(height: 16),
        TextFormField(controller: _name, decoration: const InputDecoration(labelText: 'Tax Name *'), validator: (v) => v!.isNotEmpty ? null : 'Required'),
        const SizedBox(height: 12),
        TextFormField(controller: _rate, keyboardType: TextInputType.number, decoration: const InputDecoration(labelText: 'Rate (%) *', suffixText: '%'), validator: (v) => v!.isNotEmpty ? null : 'Required'),
        const SizedBox(height: 12),
        SegmentedButton<String>(segments: const [ButtonSegment(value: 'exclusive', label: Text('Exclusive')), ButtonSegment(value: 'inclusive', label: Text('Inclusive'))], selected: {_type}, onSelectionChanged: (v) => setState(() => _type = v.first)),
        const SizedBox(height: 24),
        SizedBox(height: 52, child: ElevatedButton(onPressed: _saving ? null : _save, child: _saving ? const SizedBox(width: 22, height: 22, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5)) : Text(widget.tax != null ? 'Update Tax Rate' : 'Add Tax Rate', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700)))),
      ]))),
    );
  }
}
