import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/purchase_provider.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/toast_helper.dart';

class AddStockTransferScreen extends StatefulWidget {
  const AddStockTransferScreen({super.key});
  @override State<AddStockTransferScreen> createState() => _AddStockTransferScreenState();
}

class _AddStockTransferScreenState extends State<AddStockTransferScreen> {
  final _form = GlobalKey<FormState>();
  final _fromCtrl = TextEditingController();
  final _toCtrl = TextEditingController();
  final _notesCtrl = TextEditingController();
  final _productSearchCtrl = TextEditingController();
  final _qtyCtrl = TextEditingController(text: '1');
  bool _saving = false;
  List<Map<String, dynamic>> _items = [];
  List<dynamic> _searchResults = [];
  bool _searching = false;

  @override
  void dispose() {
    _fromCtrl.dispose();
    _toCtrl.dispose();
    _notesCtrl.dispose();
    _productSearchCtrl.dispose();
    _qtyCtrl.dispose();
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

  void _addItem(dynamic product) {
    setState(() {
      _items.add({
        'product_id': product['id'],
        'product_name': product['name'],
        'quantity': int.tryParse(_qtyCtrl.text) ?? 1,
      });
      _searchResults = [];
      _productSearchCtrl.clear();
    });
  }

  Future<void> _save() async {
    if (!_form.currentState!.validate()) return;
    if (_items.isEmpty) { ToastHelper.showError(context, 'Add at least one product'); return; }
    setState(() => _saving = true);
    try {
      await context.read<PurchaseProvider>().createPurchase({
        'from_location': _fromCtrl.text.trim(),
        'to_location': _toCtrl.text.trim(),
        'status': 'draft',
        'notes': _notesCtrl.text.trim(),
        'items': _items.map((i) => {'product_id': i['product_id'], 'quantity': i['quantity']}).toList(),
      });
      if (mounted) {
        ToastHelper.showSuccess(context, 'Transfer created');
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
        title: const Text('New Stock Transfer'),
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
              Row(children: [
                Expanded(child: TextFormField(controller: _fromCtrl, decoration: const InputDecoration(labelText: 'From Location *', prefixIcon: Icon(Icons.location_on_outlined, size: 20)), validator: (v) => v!.trim().isEmpty ? 'Required' : null)),
                const SizedBox(width: 12),
                Expanded(child: TextFormField(controller: _toCtrl, decoration: const InputDecoration(labelText: 'To Location *', prefixIcon: Icon(Icons.location_on, size: 20)), validator: (v) => v!.trim().isEmpty ? 'Required' : null)),
              ]),
              const SizedBox(height: 20),
              const Text('Products', style: TextStyle(fontSize: 15, fontWeight: FontWeight.w700)),
              const SizedBox(height: 8),
              TextFormField(
                controller: _productSearchCtrl,
                decoration: InputDecoration(
                  labelText: 'Search Product',
                  prefixIcon: const Icon(Icons.search, size: 20),
                  suffixIcon: _searching ? const SizedBox(width: 20, height: 20, child: Padding(padding: EdgeInsets.all(12), child: CircularProgressIndicator(strokeWidth: 2))) : null,
                ),
                onChanged: _searchProduct,
              ),
              if (_searchResults.isNotEmpty)
                Container(
                  constraints: const BoxConstraints(maxHeight: 180),
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
                        trailing: IconButton(icon: const Icon(Icons.add_circle_outline, color: AppColors.primary), onPressed: () => _addItem(p)),
                      );
                    },
                  ),
                ),
              if (_items.isNotEmpty) ...[
                const SizedBox(height: 12),
                Row(children: [
                  const Text('Added Items:', style: TextStyle(fontWeight: FontWeight.w600)),
                  const Spacer(),
                  Text('${_items.length} item(s)', style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
                ]),
                const SizedBox(height: 8),
                ..._items.asMap().entries.map((e) => Container(
                  margin: const EdgeInsets.only(bottom: 8),
                  padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                  decoration: BoxDecoration(color: AppColors.surfaceVariant, borderRadius: BorderRadius.circular(10)),
                  child: Row(
                    children: [
                      Expanded(child: Text(e.value['product_name'] ?? '', style: const TextStyle(fontWeight: FontWeight.w500, fontSize: 13))),
                      Text('x${e.value['quantity']}', style: const TextStyle(fontWeight: FontWeight.w700)),
                      const SizedBox(width: 8),
                      IconButton(icon: const Icon(Icons.close, size: 16, color: AppColors.danger), padding: EdgeInsets.zero, constraints: const BoxConstraints(), onPressed: () => setState(() => _items.removeAt(e.key))),
                    ],
                  ),
                )),
              ],
              const SizedBox(height: 16),
              TextFormField(controller: _notesCtrl, maxLines: 2, decoration: const InputDecoration(labelText: 'Notes', prefixIcon: Icon(Icons.notes_outlined, size: 20), alignLabelWithHint: true)),
              const SizedBox(height: 32),
            ],
          ),
        ),
      ),
    );
  }
}
