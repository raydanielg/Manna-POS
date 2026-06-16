import 'package:flutter/material.dart';
import '../../core/api_service.dart';
import '../../shared/theme/app_colors.dart';
import '../../shared/widgets/app_card.dart';
import '../../shared/widgets/empty_state.dart';
import '../../shared/widgets/status_badge.dart';
class StockAdjustmentsPage extends StatefulWidget {
  const StockAdjustmentsPage({super.key});
  @override State<StockAdjustmentsPage> createState() => _StockAdjustmentsPageState();
}

class _StockAdjustmentsPageState extends State<StockAdjustmentsPage> {
  List<dynamic> _adjustments = [];
  bool _loading = true;
  String? _error;

  @override void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    setState(() { _loading = true; _error = null; });
    try {
      final data = await ApiService.get('/stock-adjustments');
      setState(() { _adjustments = (data as List); _loading = false; });
    } on ApiException catch (e) { setState(() { _error = e.message; _loading = false; }); }
    catch (_) { setState(() { _error = 'Connection error'; _loading = false; }); }
  }

  void _showForm() => showModalBottomSheet(context: context, isScrollControlled: true, backgroundColor: Colors.transparent, builder: (_) => _AdjustmentForm(onSaved: _load));

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(title: const Text('Stock Adjustments'), actions: [IconButton(icon: const Icon(Icons.refresh, color: Colors.white), onPressed: _load)]),
      body: _loading ? const LoadingWidget(message: 'Loading...')
        : _error != null ? ErrorWidget2(message: _error!, onRetry: _load)
        : _adjustments.isEmpty ? const EmptyState(icon: Icons.balance_outlined, title: 'No Adjustments', subtitle: 'Stock adjustments will appear here')
        : RefreshIndicator(color: AppColors.primary, onRefresh: _load,
            child: ListView.separated(padding: const EdgeInsets.fromLTRB(16, 0, 16, 100), itemCount: _adjustments.length, separatorBuilder: (_, __) => const SizedBox(height: 10), itemBuilder: (_, i) => _tile(_adjustments[i]))),
      floatingActionButton: FloatingActionButton.extended(onPressed: _showForm, icon: const Icon(Icons.add), label: const Text('New Adjustment')),
    );
  }

  Widget _tile(dynamic a) => AppCard(child: Padding(padding: const EdgeInsets.all(16), child: Column(children: [
    Row(children: [
      Container(width: 46, height: 46, decoration: BoxDecoration(
        color: a['type'] == 'addition' ? AppColors.successLt : AppColors.dangerLt, borderRadius: BorderRadius.circular(12)),
        child: Icon(a['type'] == 'addition' ? Icons.add_circle_outlined : Icons.remove_circle_outlined, color: a['type'] == 'addition' ? AppColors.success : AppColors.danger, size: 22)),
      const SizedBox(width: 14),
      Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
        Text(a['reference'] ?? '', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14)),
        const SizedBox(height: 3),
        Text(a['product'] != null ? a['product']['name'] ?? '' : 'Unknown Product', style: const TextStyle(color: AppColors.textSec, fontSize: 13)),
        if (a['reason'] != null) ...[const SizedBox(height: 2), Text(a['reason'], style: const TextStyle(color: AppColors.textSec, fontSize: 12))],
      ])),
      Column(crossAxisAlignment: CrossAxisAlignment.end, children: [
        Text('${a['type'] == 'addition' ? '+' : '-'}${a['quantity'] ?? 0}', style: TextStyle(fontWeight: FontWeight.w800, fontSize: 16, color: a['type'] == 'addition' ? AppColors.success : AppColors.danger)),
        const SizedBox(height: 4),
        StatusBadge(label: a['type'] ?? '', color: AppColors.textSec, bgColor: AppColors.border),
      ]),
    ]),
    if (a['notes'] != null && a['notes'] != '') ...[const SizedBox(height: 8), Container(width: double.infinity, padding: const EdgeInsets.all(8), decoration: BoxDecoration(color: AppColors.surfaceVariant, borderRadius: BorderRadius.circular(8)), child: Text(a['notes'], style: const TextStyle(color: AppColors.textSec, fontSize: 12)))],
  ])));
}

class _AdjustmentForm extends StatefulWidget {
  final VoidCallback onSaved;
  const _AdjustmentForm({required this.onSaved});
  @override State<_AdjustmentForm> createState() => _AdjustmentFormState();
}

class _AdjustmentFormState extends State<_AdjustmentForm> {
  final _form = GlobalKey<FormState>();
  final _qtyCtrl = TextEditingController(text: '1');
  final _costCtrl = TextEditingController(text: '0');
  final _reasonCtrl = TextEditingController();
  final _notesCtrl = TextEditingController();
  final _dateCtrl = TextEditingController(text: DateTime.now().toIso8601String().split('T')[0]);
  bool _saving = false;
  String? _err;
  int? _productId;
  String _type = 'addition';
  List<dynamic> _products = [];

  @override void initState() { super.initState(); _loadProducts(); }
  @override void dispose() { _qtyCtrl.dispose(); _costCtrl.dispose(); _reasonCtrl.dispose(); _notesCtrl.dispose(); _dateCtrl.dispose(); super.dispose(); }

  Future<void> _loadProducts() async {
    try { final data = await ApiService.get('/products'); setState(() { _products = (data as List); }); } catch (_) {}
  }

  Future<void> _save() async {
    if (!_form.currentState!.validate() || _productId == null) { _snack('Select a product', error: true); return; }
    setState(() { _saving = true; _err = null; });
    try {
      await ApiService.post('/stock-adjustments', {
        'product_id': _productId,
        'adjustment_date': _dateCtrl.text.trim(),
        'type': _type,
        'quantity': double.tryParse(_qtyCtrl.text) ?? 1,
        'unit_cost': double.tryParse(_costCtrl.text) ?? 0,
        'reason': _reasonCtrl.text.trim(),
        'notes': _notesCtrl.text.trim(),
      });
      widget.onSaved();
      if (mounted) Navigator.pop(context);
    } on ApiException catch (e) { setState(() { _err = e.message; _saving = false; }); }
    catch (_) { setState(() { _err = 'Save failed'; _saving = false; }); }
  }

  void _snack(String msg, {bool error = false}) => ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(msg), backgroundColor: error ? AppColors.danger : AppColors.success, behavior: SnackBarBehavior.floating, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10))));

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: const BoxDecoration(color: AppColors.surface, borderRadius: BorderRadius.vertical(top: Radius.circular(24))),
      padding: EdgeInsets.only(bottom: MediaQuery.of(context).viewInsets.bottom),
      child: SingleChildScrollView(padding: const EdgeInsets.fromLTRB(24, 16, 24, 32), child: Form(key: _form, child: Column(crossAxisAlignment: CrossAxisAlignment.stretch, children: [
        Center(child: Container(width: 40, height: 4, decoration: BoxDecoration(color: AppColors.border, borderRadius: BorderRadius.circular(4)))),
        const SizedBox(height: 20),
        Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [const Text('Stock Adjustment', style: TextStyle(fontSize: 20, fontWeight: FontWeight.w700)), IconButton(icon: const Icon(Icons.close), onPressed: () => Navigator.pop(context))]),
        if (_err != null) ...[const SizedBox(height: 12), Container(padding: const EdgeInsets.all(12), decoration: BoxDecoration(color: AppColors.dangerLt, borderRadius: BorderRadius.circular(10)), child: Text(_err!, style: const TextStyle(color: AppColors.danger)))],
        const SizedBox(height: 16),
        DropdownButtonFormField<int>(decoration: const InputDecoration(labelText: 'Product *'), items: _products.map((p) => DropdownMenuItem(value: p['id'], child: Text(p['name'] ?? ''))).toList(), onChanged: (v) => _productId = v),
        const SizedBox(height: 12),
        SegmentedButton<String>(segments: const [ButtonSegment(value: 'addition', label: Text('Addition'), icon: Icon(Icons.add)), ButtonSegment(value: 'subtraction', label: Text('Subtraction'), icon: Icon(Icons.remove))], selected: {_type}, onSelectionChanged: (v) => setState(() => _type = v.first)),
        const SizedBox(height: 12),
        Row(children: [Expanded(child: TextFormField(controller: _qtyCtrl, keyboardType: TextInputType.number, decoration: const InputDecoration(labelText: 'Quantity *'))), const SizedBox(width: 12), Expanded(child: TextFormField(controller: _costCtrl, keyboardType: TextInputType.number, decoration: const InputDecoration(labelText: 'Unit Cost')))]),
        const SizedBox(height: 12),
        TextFormField(controller: _dateCtrl, decoration: const InputDecoration(labelText: 'Date', prefixIcon: Icon(Icons.calendar_today_outlined, size: 18))),
        const SizedBox(height: 12),
        TextFormField(controller: _reasonCtrl, decoration: const InputDecoration(labelText: 'Reason')),
        const SizedBox(height: 12),
        TextFormField(controller: _notesCtrl, maxLines: 2, decoration: const InputDecoration(labelText: 'Notes')),
        const SizedBox(height: 24),
        SizedBox(height: 52, child: ElevatedButton(onPressed: _saving ? null : _save, child: _saving ? const SizedBox(width: 22, height: 22, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5)) : const Text('Save Adjustment', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700)))),
      ]))),
    );
  }
}
