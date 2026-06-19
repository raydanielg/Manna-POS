import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/purchase_provider.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/toast_helper.dart';
import '../../constants/app_constants.dart';

class AddStockAdjustmentScreen extends StatefulWidget {
  const AddStockAdjustmentScreen({super.key});
  @override State<AddStockAdjustmentScreen> createState() => _AddStockAdjustmentScreenState();
}

class _AddStockAdjustmentScreenState extends State<AddStockAdjustmentScreen> {
  final _form = GlobalKey<FormState>();
  final _qtyCtrl = TextEditingController(text: '1');
  final _reasonCtrl = TextEditingController();
  final _notesCtrl = TextEditingController();
  final _productSearchCtrl = TextEditingController();
  bool _saving = false;
  bool _isAddition = true;
  int? _productId;
  String? _productName;
  List<dynamic> _searchResults = [];
  bool _searching = false;

  @override
  void dispose() {
    _qtyCtrl.dispose();
    _reasonCtrl.dispose();
    _notesCtrl.dispose();
    _productSearchCtrl.dispose();
    super.dispose();
  }

  void _searchProduct(String q) async {
    if (q.trim().isEmpty) { setState(() => _searchResults = []); return; }
    setState(() => _searching = true);
    try {
      final provider = context.read<PurchaseProvider>();
      final results = await provider.searchProducts(q);
      setState(() => _searchResults = results);
    } catch (_) {
      setState(() => _searchResults = []);
    } finally {
      setState(() => _searching = false);
    }
  }

  void _selectProduct(dynamic p) {
    setState(() {
      _productId = p['id'];
      _productName = p['name'];
      _productSearchCtrl.text = p['name'];
      _searchResults = [];
    });
  }

  Future<void> _save() async {
    if (!_form.currentState!.validate()) return;
    if (_productId == null) { ToastHelper.showError(context, 'Select a product'); return; }
    setState(() => _saving = true);
    try {
      await context.read<PurchaseProvider>().createPurchase({
        'product_id': _productId,
        'type': _isAddition ? 'addition' : 'subtraction',
        'quantity': double.tryParse(_qtyCtrl.text) ?? 1,
        'reason': _reasonCtrl.text.trim(),
        'notes': _notesCtrl.text.trim(),
      });
      if (mounted) {
        ToastHelper.showSuccess(context, 'Adjustment saved');
        Navigator.pop(context, true);
      }
    } catch (e) {
      if (mounted) ToastHelper.showError(context, e.toString());
      setState(() => _saving = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    return Scaffold(
      backgroundColor: theme.scaffoldBackgroundColor,
      appBar: AppBar(
        title: const Text('Stock Adjustment'),
        actions: [
          TextButton(
            onPressed: _saving ? null : _save,
            child: _saving
                ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(strokeWidth: 2))
                : const Text('Submit', style: TextStyle(fontWeight: FontWeight.w700)),
          ),
        ],
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.fromLTRB(16, 8, 16, 100),
        child: Form(
          key: _form,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              TextFormField(
                controller: _productSearchCtrl,
                decoration: InputDecoration(
                  labelText: 'Search & Select Product *',
                  prefixIcon: const Icon(Icons.search, size: 20),
                  suffixIcon: _searching
                      ? const SizedBox(width: 20, height: 20, child: Padding(padding: EdgeInsets.all(12), child: CircularProgressIndicator(strokeWidth: 2)))
                      : _productId != null
                          ? IconButton(icon: const Icon(Icons.close, size: 18), onPressed: () => setState(() { _productId = null; _productName = null; _productSearchCtrl.clear(); }))
                          : null,
                ),
                onChanged: (v) {
                  if (_productId != null) setState(() { _productId = null; _productName = null; });
                  _searchProduct(v);
                },
                validator: (_) => _productId == null ? 'Select a product' : null,
              ),
              if (_searchResults.isNotEmpty)
                Container(
                  constraints: const BoxConstraints(maxHeight: 200),
                  decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(12), boxShadow: [BoxShadow(color: Colors.black.withValues(alpha: 0.08), blurRadius: 12)]),
                  child: ListView.separated(
                    shrinkWrap: true,
                    itemCount: _searchResults.length,
                    separatorBuilder: (_, __) => const Divider(height: 1),
                    itemBuilder: (_, i) {
                      final p = _searchResults[i];
                      return ListTile(
                        dense: true,
                        leading: Container(
                          width: 36, height: 36,
                          decoration: BoxDecoration(color: AppColors.primaryLt, borderRadius: BorderRadius.circular(8)),
                          child: const Icon(Icons.inventory_2_outlined, size: 18, color: AppColors.primary),
                        ),
                        title: Text(p['name'] ?? '', style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13)),
                        subtitle: Text('Stock: ${p['stock_quantity'] ?? 0}', style: const TextStyle(fontSize: 11)),
                        onTap: () => _selectProduct(p),
                      );
                    },
                  ),
                ),
              const SizedBox(height: 20),
              const Text('Adjustment Type', style: TextStyle(fontSize: 13, fontWeight: FontWeight.w600, color: AppColors.textSec)),
              const SizedBox(height: 8),
              SegmentedButton<bool>(
                segments: const [
                  ButtonSegment(value: true, label: Text('Addition'), icon: Icon(Icons.add_circle_outline)),
                  ButtonSegment(value: false, label: Text('Reduction'), icon: Icon(Icons.remove_circle_outline)),
                ],
                selected: {_isAddition},
                onSelectionChanged: (v) => setState(() => _isAddition = v.first),
              ),
              const SizedBox(height: 20),
              TextFormField(
                controller: _qtyCtrl,
                keyboardType: TextInputType.number,
                decoration: const InputDecoration(
                  labelText: 'Quantity *',
                  prefixIcon: Icon(Icons.numbers, size: 20),
                  helperText: 'Enter the quantity to adjust',
                  helperStyle: TextStyle(fontSize: 11),
                ),
                validator: (v) => (v == null || v.trim().isEmpty || double.tryParse(v) == null || double.parse(v) <= 0) ? 'Enter a valid quantity' : null,
              ),
              const SizedBox(height: 12),
              TextFormField(
                controller: _reasonCtrl,
                decoration: const InputDecoration(labelText: 'Reason *', prefixIcon: Icon(Icons.assignment_outlined, size: 20)),
                validator: (v) => v!.trim().isEmpty ? 'Required' : null,
              ),
              const SizedBox(height: 12),
              TextFormField(controller: _notesCtrl, maxLines: 2, decoration: const InputDecoration(labelText: 'Notes', prefixIcon: Icon(Icons.notes_outlined, size: 20), alignLabelWithHint: true)),
              const SizedBox(height: 32),
            ],
          ),
        ),
      ),
    );
  }
}
