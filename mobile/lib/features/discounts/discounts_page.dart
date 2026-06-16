import 'package:flutter/material.dart';
import '../../core/api_service.dart';
import '../../shared/theme/app_colors.dart';
import '../../shared/widgets/app_card.dart';
import '../../shared/widgets/empty_state.dart';
import '../../shared/widgets/status_badge.dart';
import '../../shared/constants/app_constants.dart';

class DiscountsPage extends StatefulWidget {
  const DiscountsPage({super.key});
  @override State<DiscountsPage> createState() => _DiscountsPageState();
}

class _DiscountsPageState extends State<DiscountsPage> {
  List<dynamic> _discounts = [];
  bool _loading = true;
  String? _error;

  @override void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    setState(() { _loading = true; _error = null; });
    try {
      final data = await ApiService.get('/discounts');
      setState(() { _discounts = (data as List); _loading = false; });
    } on ApiException catch (e) { setState(() { _error = e.message; _loading = false; }); }
    catch (_) { setState(() { _error = 'Connection error'; _loading = false; }); }
  }

  void _showForm([dynamic d]) => showModalBottomSheet(context: context, isScrollControlled: true, backgroundColor: Colors.transparent, builder: (_) => _DiscountForm(discount: d, onSaved: _load));

  Future<void> _delete(dynamic d) async {
    final ok = await showDialog<bool>(context: context, builder: (_) => AlertDialog(
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
      title: const Text('Delete Discount'),
      content: Text('Delete "${d['name']}"?'),
      actions: [TextButton(onPressed: () => Navigator.pop(context, false), child: const Text('Cancel')),
        ElevatedButton(style: ElevatedButton.styleFrom(backgroundColor: AppColors.danger), onPressed: () => Navigator.pop(context, true), child: const Text('Delete'))],
    ));
    if (ok != true) return;
    try { await ApiService.delete('/discounts/${d['id']}'); _load(); } on ApiException catch (e) { _snack(e.message, error: true); }
  }

  void _snack(String msg, {bool error = false}) => ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(msg), backgroundColor: error ? AppColors.danger : AppColors.success, behavior: SnackBarBehavior.floating, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10))));

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(title: const Text('Discounts'), actions: [IconButton(icon: const Icon(Icons.refresh, color: Colors.white), onPressed: _load)]),
      body: _loading ? const LoadingWidget(message: 'Loading...')
        : _error != null ? ErrorWidget2(message: _error!, onRetry: _load)
        : _discounts.isEmpty ? const EmptyState(icon: Icons.discount_outlined, title: 'No Discounts', subtitle: 'Add your first discount')
        : RefreshIndicator(color: AppColors.primary, onRefresh: _load,
            child: ListView.separated(padding: const EdgeInsets.fromLTRB(16, 0, 16, 100), itemCount: _discounts.length, separatorBuilder: (_, __) => const SizedBox(height: 10), itemBuilder: (_, i) => _tile(_discounts[i]))),
      floatingActionButton: FloatingActionButton.extended(onPressed: () => _showForm(), icon: const Icon(Icons.add), label: const Text('Add Discount')),
    );
  }

  Widget _tile(dynamic d) => AppCard(child: Padding(padding: const EdgeInsets.all(16), child: Row(children: [
    Container(width: 46, height: 46, decoration: BoxDecoration(color: AppColors.warningLt, borderRadius: BorderRadius.circular(12)),
      child: const Icon(Icons.percent_outlined, color: AppColors.warning, size: 22)),
    const SizedBox(width: 14),
    Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
      Text(d['name'] ?? '', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 15)),
      const SizedBox(height: 3),
      Text('${d['type'] == 'percentage' ? '${d['amount']}%' : '${AppConstants.currency} ${d['amount']}'}', style: const TextStyle(color: AppColors.textSec, fontSize: 13)),
    ])),
    Column(crossAxisAlignment: CrossAxisAlignment.end, children: [
      StatusBadge.fromStatus(d['status'] ?? 'active'),
      const SizedBox(height: 6),
      PopupMenuButton<String>(shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)), onSelected: (v) { if (v == 'edit') _showForm(d); else _delete(d); },
        itemBuilder: (_) => [const PopupMenuItem(value: 'edit', child: Row(children: [Icon(Icons.edit_outlined, size: 18, color: AppColors.primary), SizedBox(width: 8), Text('Edit')])), const PopupMenuItem(value: 'delete', child: Row(children: [Icon(Icons.delete_outline, size: 18, color: AppColors.danger), SizedBox(width: 8), Text('Delete', style: TextStyle(color: AppColors.danger))]))]),
    ]),
  ])));
}

class _DiscountForm extends StatefulWidget {
  final dynamic discount;
  final VoidCallback onSaved;
  const _DiscountForm({this.discount, required this.onSaved});
  @override State<_DiscountForm> createState() => _DiscountFormState();
}

class _DiscountFormState extends State<_DiscountForm> {
  final _form = GlobalKey<FormState>();
  late TextEditingController _name, _amount, _start, _end;
  bool _saving = false;
  String? _err;
  String _type = 'percentage';

  @override
  void initState() {
    super.initState();
    final d = widget.discount;
    _name = TextEditingController(text: d?['name']);
    _amount = TextEditingController(text: d?['amount']?.toString());
    _start = TextEditingController(text: d?['starts_at']);
    _end = TextEditingController(text: d?['ends_at']);
    if (d != null) _type = d['type'] ?? 'percentage';
  }

  @override void dispose() { _name.dispose(); _amount.dispose(); _start.dispose(); _end.dispose(); super.dispose(); }

  Future<void> _save() async {
    if (!_form.currentState!.validate()) return;
    setState(() { _saving = true; _err = null; });
    final body = {'name': _name.text.trim(), 'amount': double.tryParse(_amount.text) ?? 0, 'type': _type};
    if (_start.text.isNotEmpty) body['starts_at'] = _start.text.trim();
    if (_end.text.isNotEmpty) body['ends_at'] = _end.text.trim();
    try {
      if (widget.discount != null) await ApiService.put('/discounts/${widget.discount!['id']}', body);
      else await ApiService.post('/discounts', body);
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
        Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [Text(widget.discount != null ? 'Edit Discount' : 'Add Discount', style: const TextStyle(fontSize: 20, fontWeight: FontWeight.w700)), IconButton(icon: const Icon(Icons.close), onPressed: () => Navigator.pop(context))]),
        if (_err != null) ...[const SizedBox(height: 12), Container(padding: const EdgeInsets.all(12), decoration: BoxDecoration(color: AppColors.dangerLt, borderRadius: BorderRadius.circular(10)), child: Text(_err!, style: const TextStyle(color: AppColors.danger)))],
        const SizedBox(height: 16),
        TextFormField(controller: _name, decoration: const InputDecoration(labelText: 'Discount Name *'), validator: (v) => v!.isNotEmpty ? null : 'Required'),
        const SizedBox(height: 12),
        TextFormField(controller: _amount, keyboardType: TextInputType.number, decoration: const InputDecoration(labelText: 'Amount *', prefixText: _type == 'percentage' ? '% ' : '$AppConstants.currency '), validator: (v) => v!.isNotEmpty ? null : 'Required'),
        const SizedBox(height: 12),
        SegmentedButton<String>(segments: const [ButtonSegment(value: 'percentage', label: Text('Percentage'), icon: Icon(Icons.percent)), ButtonSegment(value: 'fixed', label: Text('Fixed'), icon: Icon(Icons.monetization_on_outlined))], selected: {_type}, onSelectionChanged: (v) => setState(() => _type = v.first)),
        const SizedBox(height: 12),
        Row(children: [Expanded(child: TextFormField(controller: _start, decoration: const InputDecoration(labelText: 'Start Date'))), const SizedBox(width: 12), Expanded(child: TextFormField(controller: _end, decoration: const InputDecoration(labelText: 'End Date')))]),
        const SizedBox(height: 24),
        SizedBox(height: 52, child: ElevatedButton(onPressed: _saving ? null : _save, child: _saving ? const SizedBox(width: 22, height: 22, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5)) : Text(widget.discount != null ? 'Update Discount' : 'Add Discount', style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w700)))),
      ]))),
    );
  }
}
