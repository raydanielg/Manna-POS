import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../../providers/purchase_provider.dart';
import '../../providers/supplier_provider.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/search_bar_widget.dart';
import '../../widgets/toast_helper.dart';
import '../../constants/app_constants.dart';

class AddPurchaseScreen extends StatefulWidget {
  const AddPurchaseScreen({super.key});
  @override State<AddPurchaseScreen> createState() => _AddPurchaseScreenState();
}

class _AddPurchaseScreenState extends State<AddPurchaseScreen> {
  final _form = GlobalKey<FormState>();
  final _notesCtrl = TextEditingController();
  final _paidCtrl = TextEditingController(text: '0');
  final _discountCtrl = TextEditingController(text: '0');
  final _taxCtrl = TextEditingController(text: '0');
  final _dateCtrl = TextEditingController(text: DateFormat('yyyy-MM-dd').format(DateTime.now()));
  final _productSearchCtrl = TextEditingController();
  bool _saving = false;
  int? _supplierId;
  String _paymentMethod = 'cash';
  List<Map<String, dynamic>> _items = [];
  List<dynamic> _searchResults = [];
  bool _searching = false;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<SupplierProvider>().fetchSuppliers();
    });
  }

  @override
  void dispose() {
    _notesCtrl.dispose();
    _paidCtrl.dispose();
    _discountCtrl.dispose();
    _taxCtrl.dispose();
    _dateCtrl.dispose();
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

  void _addItem(dynamic product) {
    setState(() {
      _items.add({
        'product_id': product['id'],
        'product_name': product['name'],
        'quantity': 1,
        'unit_cost': double.tryParse(product['cost_price']?.toString() ?? '0') ?? 0,
      });
      _searchResults = [];
      _productSearchCtrl.clear();
    });
  }

  double get _subtotal => _items.fold<double>(0, (s, i) => s + (i['quantity'] as num) * (i['unit_cost'] as num));
  double get _discount => double.tryParse(_discountCtrl.text) ?? 0;
  double get _tax => double.tryParse(_taxCtrl.text) ?? 0;
  double get _total => _subtotal - _discount + _tax;

  Future<void> _save() async {
    if (!_form.currentState!.validate()) return;
    if (_supplierId == null) { ToastHelper.showError(context, 'Select a supplier'); return; }
    if (_items.isEmpty) { ToastHelper.showError(context, 'Add at least one item'); return; }
    setState(() => _saving = true);
    try {
      await context.read<PurchaseProvider>().createPurchase({
        'supplier_id': _supplierId,
        'purchase_date': _dateCtrl.text.trim(),
        'payment_method': _paymentMethod,
        'paid_amount': double.tryParse(_paidCtrl.text) ?? 0,
        'discount': _discount,
        'tax': _tax,
        'notes': _notesCtrl.text.trim(),
        'items': _items.map((i) => {
          'product_id': i['product_id'],
          'quantity': i['quantity'],
          'unit_cost': i['unit_cost'],
        }).toList(),
      });
      if (mounted) {
        ToastHelper.showSuccess(context, 'Purchase created');
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
    final suppliers = context.watch<SupplierProvider>().suppliers;

    return Scaffold(
      backgroundColor: theme.scaffoldBackgroundColor,
      appBar: AppBar(
        title: const Text('New Purchase'),
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
              DropdownButtonFormField<int>(
                decoration: const InputDecoration(labelText: 'Supplier *', prefixIcon: Icon(Icons.business, size: 20)),
                items: suppliers.map((s) => DropdownMenuItem(value: s.id, child: Text(s.name))).toList(),
                onChanged: (v) => _supplierId = v,
              ),
              const SizedBox(height: 12),
              TextFormField(
                controller: _productSearchCtrl,
                decoration: InputDecoration(
                  labelText: 'Search & Add Products',
                  prefixIcon: const Icon(Icons.search, size: 20),
                  suffixIcon: _searching ? const SizedBox(width: 20, height: 20, child: Padding(padding: EdgeInsets.all(12), child: CircularProgressIndicator(strokeWidth: 2))) : null,
                ),
                onChanged: _searchProduct,
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
                        subtitle: Text('${AppConstants.currency} ${p['cost_price'] ?? 0}', style: const TextStyle(fontSize: 11)),
                        trailing: IconButton(icon: const Icon(Icons.add_circle_outline, color: AppColors.primary), onPressed: () => _addItem(p)),
                      );
                    },
                  ),
                ),
              const SizedBox(height: 16),
              if (_items.isNotEmpty) ...[
                Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
                  const Text('Items', style: TextStyle(fontSize: 15, fontWeight: FontWeight.w700)),
                  Text('${_items.length} item(s)', style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
                ]),
                const SizedBox(height: 8),
                ..._items.asMap().entries.map((e) => _itemRow(e.key)),
              ],
              const SizedBox(height: 20),
              GlassCard(
                child: Column(
                  children: [
                    Row(children: [
                      const Expanded(child: Text('Subtotal', style: TextStyle(color: AppColors.textSec))),
                      Text('${AppConstants.currency} ${_subtotal.toStringAsFixed(2)}', style: const TextStyle(fontWeight: FontWeight.w600)),
                    ]),
                    const SizedBox(height: 8),
                    Row(children: [
                      const Expanded(child: Text('Discount', style: TextStyle(color: AppColors.textSec))),
                      SizedBox(width: 100, child: TextFormField(controller: _discountCtrl, keyboardType: TextInputType.number, textAlign: TextAlign.end, decoration: const InputDecoration(isDense: true, contentPadding: EdgeInsets.symmetric(vertical: 4, horizontal: 8)))),
                    ]),
                    const SizedBox(height: 8),
                    Row(children: [
                      const Expanded(child: Text('Tax', style: TextStyle(color: AppColors.textSec))),
                      SizedBox(width: 100, child: TextFormField(controller: _taxCtrl, keyboardType: TextInputType.number, textAlign: TextAlign.end, decoration: const InputDecoration(isDense: true, contentPadding: EdgeInsets.symmetric(vertical: 4, horizontal: 8)))),
                    ]),
                    const Divider(height: 24),
                    Row(children: [
                      const Expanded(child: Text('Total', style: TextStyle(fontWeight: FontWeight.w800, fontSize: 16))),
                      Text('${AppConstants.currency} ${_total.toStringAsFixed(2)}', style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 16, color: AppColors.primary)),
                    ]),
                  ],
                ),
              ),
              const SizedBox(height: 16),
              TextFormField(
                controller: _paidCtrl,
                keyboardType: TextInputType.number,
                decoration: const InputDecoration(labelText: 'Paid Amount', prefixIcon: Icon(Icons.payments_outlined, size: 20)),
              ),
              const SizedBox(height: 12),
              DropdownButtonFormField<String>(
                decoration: const InputDecoration(labelText: 'Payment Method', prefixIcon: Icon(Icons.account_balance_wallet_outlined, size: 20)),
                value: _paymentMethod,
                items: ['cash', 'card', 'bank_transfer', 'mobile_money', 'credit'].map((m) => DropdownMenuItem(value: m, child: Text(m.replaceAll('_', ' ').toUpperCase()))).toList(),
                onChanged: (v) => setState(() => _paymentMethod = v!),
              ),
              const SizedBox(height: 12),
              TextFormField(controller: _dateCtrl, decoration: const InputDecoration(labelText: 'Date', prefixIcon: Icon(Icons.calendar_today_outlined, size: 20)),
                onTap: () async {
                  final d = await showDatePicker(context: context, initialDate: DateTime.now(), firstDate: DateTime(2020), lastDate: DateTime(2030));
                  if (d != null) _dateCtrl.text = DateFormat('yyyy-MM-dd').format(d);
                },
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

  Widget _itemRow(int idx) {
    final item = _items[idx];
    return Container(
      margin: const EdgeInsets.only(bottom: 10),
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(color: AppColors.surfaceVariant, borderRadius: BorderRadius.circular(12)),
      child: Column(
        children: [
          Row(
            children: [
              Expanded(
                child: Text(item['product_name'] ?? '', style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13)),
              ),
              IconButton(
                icon: const Icon(Icons.close, size: 18, color: AppColors.danger),
                onPressed: () => setState(() => _items.removeAt(idx)),
                padding: EdgeInsets.zero,
                constraints: const BoxConstraints(),
              ),
            ],
          ),
          const SizedBox(height: 8),
          Row(
            children: [
              Expanded(
                child: TextFormField(
                  initialValue: item['quantity'].toString(),
                  keyboardType: TextInputType.number,
                  decoration: const InputDecoration(labelText: 'Qty', isDense: true),
                  onChanged: (v) => item['quantity'] = double.tryParse(v) ?? 0,
                ),
              ),
              const SizedBox(width: 8),
              Expanded(
                child: TextFormField(
                  initialValue: item['unit_cost'].toString(),
                  keyboardType: TextInputType.number,
                  decoration: const InputDecoration(labelText: 'Unit Cost', isDense: true),
                  onChanged: (v) => item['unit_cost'] = double.tryParse(v) ?? 0,
                ),
              ),
              const SizedBox(width: 8),
              Text('${AppConstants.currency} ${((item['quantity'] as num) * (item['unit_cost'] as num)).toStringAsFixed(2)}',
                  style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 13)),
            ],
          ),
        ],
      ),
    );
  }
}
