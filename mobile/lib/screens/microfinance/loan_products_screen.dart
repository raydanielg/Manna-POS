import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../core/api_service.dart';
import '../../shared/theme/app_theme.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/shimmer_loading.dart';
import '../../widgets/empty_state.dart';
import '../../widgets/status_badge.dart';
import '../../widgets/toast_helper.dart';
import '../../widgets/app_bottom_sheet.dart';
import '../../widgets/confirm_dialog.dart';

class LoanProductsScreen extends StatefulWidget {
  const LoanProductsScreen({super.key});
  @override State<LoanProductsScreen> createState() => _LoanProductsScreenState();
}

class _LoanProductsScreenState extends State<LoanProductsScreen> {
  bool _loading = true;
  String? _error;
  List<dynamic> _products = [];
  final _fmt = NumberFormat('#,##0.00');
  final _currency = 'TSh';

  @override
  void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    setState(() { _loading = true; _error = null; });
    try {
      final res = await ApiService.get('/microfinance/loan-products');
      setState(() { _products = res is List ? res : (res['data'] ?? []); _loading = false; });
    } on ApiException catch (e) { setState(() { _error = e.message; _loading = false; }); }
    catch (_) { setState(() { _error = 'Connection error'; _loading = false; }); }
  }

  String _f(dynamic v) => '$_currency ${_fmt.format((v is num ? v.toDouble() : double.tryParse(v?.toString() ?? '0') ?? 0))}';
  Color _typeColor(String type) {
    switch (type.toLowerCase()) {
      case 'personal': return AppColors.primary;
      case 'business': return AppColors.success;
      case 'group': return AppColors.purple;
      case 'emergency': return AppColors.secondary;
      default: return AppColors.cyan;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        title: const Text('Loan Products', style: TextStyle(fontWeight: FontWeight.w800, color: Color(0xFF0F172A), fontSize: 20)),
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
              : _products.isEmpty
                  ? const EmptyState(icon: Icons.inventory_2_outlined, title: 'No Products', subtitle: 'Add loan products to get started')
                  : RefreshIndicator(
                      onRefresh: _load,
                      child: ListView.builder(
                        padding: const EdgeInsets.fromLTRB(16, 12, 16, 100),
                        itemCount: _products.length,
                        itemBuilder: (_, i) => _productCard(_products[i]),
                      ),
                    ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () => _showForm(null),
        icon: const Icon(Icons.add),
        label: const Text('Add Product'),
        backgroundColor: AppColors.primary, foregroundColor: Colors.white,
      ),
    );
  }

  Widget _productCard(Map<String, dynamic> p) {
    final type = p['type']?.toString() ?? 'personal';
    final color = _typeColor(type);
    return Padding(
      padding: const EdgeInsets.only(bottom: 12),
      child: GlassCard(
        onTap: () => _showForm(p),
        child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
          Row(children: [
            Container(width: 44, height: 44, decoration: BoxDecoration(color: color.withValues(alpha: 0.12), borderRadius: BorderRadius.circular(12)),
              child: Icon(Icons.monetization_on_outlined, color: color, size: 22)),
            const SizedBox(width: 14),
            Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
              Text(p['name']?.toString() ?? '', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 15, color: AppColors.textPri)),
              Row(children: [
                StatusBadge.fromStatus(p['status']?.toString() ?? 'active'),
                const SizedBox(width: 8),
                Container(padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                  decoration: BoxDecoration(color: color.withValues(alpha: 0.1), borderRadius: BorderRadius.circular(20)),
                  child: Text(type[0].toUpperCase() + type.substring(1), style: TextStyle(fontSize: 9, fontWeight: FontWeight.w800, color: color))),
              ]),
            ])),
            const Icon(Icons.chevron_right, size: 18, color: Color(0xFFBBBBBB)),
          ]),
          const SizedBox(height: 12),
          Row(children: [
            _detailChip('Min', _f(p['min_amount'] ?? 0)),
            const SizedBox(width: 16),
            _detailChip('Max', _f(p['max_amount'] ?? 0)),
            const Spacer(),
            _detailChip('Rate', '${p['interest_rate'] ?? 0}%'),
            const SizedBox(width: 16),
            _detailChip('Duration', '${p['duration'] ?? 0}m'),
          ]),
          if (p['description']?.toString().isNotEmpty == true) ...[
            const SizedBox(height: 8),
            Text(p['description']?.toString() ?? '', style: const TextStyle(fontSize: 12, color: AppColors.textSec), maxLines: 2, overflow: TextOverflow.ellipsis),
          ],
        ]),
      ),
    );
  }

  Widget _detailChip(String label, String value) {
    return Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
      Text(label, style: const TextStyle(fontSize: 10, color: AppColors.textLight)),
      Text(value, style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w600, color: AppColors.textPri)),
    ]);
  }

  void _showForm(Map<String, dynamic>? product) {
    AppBottomSheet.show(context, title: product != null ? 'Edit Product' : 'Add Product', child: _ProductForm(
      product: product,
      onSaved: () { _load(); Navigator.pop(context); },
    ));
  }
}

class _ProductForm extends StatefulWidget {
  final Map<String, dynamic>? product;
  final VoidCallback onSaved;
  const _ProductForm({this.product, required this.onSaved});
  @override State<_ProductForm> createState() => _ProductFormState();
}

class _ProductFormState extends State<_ProductForm> {
  final _form = GlobalKey<FormState>();
  late TextEditingController _name, _minAmount, _maxAmount, _interest, _duration, _desc;
  String _type = 'personal';
  bool _saving = false;
  String? _err;
  final _types = ['personal', 'business', 'group', 'emergency'];

  @override
  void initState() {
    super.initState();
    final p = widget.product;
    _name = TextEditingController(text: p?['name']?.toString());
    _minAmount = TextEditingController(text: p?['min_amount']?.toString());
    _maxAmount = TextEditingController(text: p?['max_amount']?.toString());
    _interest = TextEditingController(text: p?['interest_rate']?.toString());
    _duration = TextEditingController(text: p?['duration']?.toString());
    _desc = TextEditingController(text: p?['description']?.toString());
    if (p != null) _type = p['type']?.toString() ?? 'personal';
  }

  @override void dispose() { for (final c in [_name, _minAmount, _maxAmount, _interest, _duration, _desc]) c.dispose(); super.dispose(); }

  Future<void> _save() async {
    if (!_form.currentState!.validate()) return;
    setState(() { _saving = true; _err = null; });
    final body = {
      'name': _name.text.trim(),
      'min_amount': double.tryParse(_minAmount.text) ?? 0,
      'max_amount': double.tryParse(_maxAmount.text) ?? 0,
      'interest_rate': double.tryParse(_interest.text) ?? 0,
      'duration': int.tryParse(_duration.text) ?? 0,
      'type': _type,
      'description': _desc.text.trim(),
    };
    try {
      if (widget.product != null) await ApiService.put('/microfinance/loan-products/${widget.product!['id']}', body);
      else await ApiService.post('/microfinance/loan-products', body);
      widget.onSaved();
    } on ApiException catch (e) { setState(() { _err = e.message; _saving = false; }); }
    catch (_) { setState(() { _err = 'Save failed'; _saving = false; }); }
  }

  @override
  Widget build(BuildContext context) => Form(key: _form, child: Column(crossAxisAlignment: CrossAxisAlignment.stretch, children: [
    if (_err != null) Container(padding: const EdgeInsets.all(12), margin: const EdgeInsets.only(bottom: 12),
      decoration: BoxDecoration(color: AppColors.dangerLt, borderRadius: BorderRadius.circular(10)),
      child: Text(_err!, style: const TextStyle(color: AppColors.danger))),
    TextFormField(controller: _name, decoration: const InputDecoration(labelText: 'Product Name *'), validator: (v) => v!.isNotEmpty ? null : 'Required'),
    const SizedBox(height: 12),
    Row(children: [
      Expanded(child: TextFormField(controller: _minAmount, keyboardType: TextInputType.number, decoration: const InputDecoration(labelText: 'Min Amount'))),
      const SizedBox(width: 12),
      Expanded(child: TextFormField(controller: _maxAmount, keyboardType: TextInputType.number, decoration: const InputDecoration(labelText: 'Max Amount'))),
    ]),
    const SizedBox(height: 12),
    Row(children: [
      Expanded(child: TextFormField(controller: _interest, keyboardType: TextInputType.number, decoration: const InputDecoration(labelText: 'Interest Rate (%)'))),
      const SizedBox(width: 12),
      Expanded(child: TextFormField(controller: _duration, keyboardType: TextInputType.number, decoration: const InputDecoration(labelText: 'Duration (months)'))),
    ]),
    const SizedBox(height: 12),
    DropdownButtonFormField<String>(value: _type, decoration: const InputDecoration(labelText: 'Type'),
      items: _types.map((t) => DropdownMenuItem(value: t, child: Text(t[0].toUpperCase() + t.substring(1)))).toList(),
      onChanged: (v) => setState(() => _type = v!)),
    const SizedBox(height: 12),
    TextFormField(controller: _desc, maxLines: 3, decoration: const InputDecoration(labelText: 'Description')),
    const SizedBox(height: 24),
    SizedBox(height: 52, child: ElevatedButton(onPressed: _saving ? null : _save,
      child: _saving ? const SizedBox(width: 22, height: 22, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5))
          : Text(widget.product != null ? 'Update Product' : 'Add Product', style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w700)))),
  ]));
}
