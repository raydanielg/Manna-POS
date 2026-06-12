import 'dart:io';
import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:image_picker/image_picker.dart';
import '../../core/api_service.dart';
import '../../shared/theme/app_theme.dart';
import '../../shared/models/product.dart';
import '../../shared/widgets/app_card.dart';
import '../../shared/widgets/empty_state.dart';
import '../../shared/widgets/search_bar_widget.dart';
import '../../shared/constants/app_constants.dart';

class ProductsPage extends StatefulWidget {
  const ProductsPage({super.key});
  @override State<ProductsPage> createState() => _ProductsPageState();
}

class _ProductsPageState extends State<ProductsPage> {
  List<Product> _products = [];
  bool _loading = true;
  String? _error;
  String _search = '';
  final _searchCtrl = TextEditingController();
  final fmt = NumberFormat('#,##0.00');

  @override void initState() { super.initState(); _load(); }
  @override void dispose() { _searchCtrl.dispose(); super.dispose(); }

  Future<void> _load() async {
    setState(() { _loading = true; _error = null; });
    try {
      final data = await ApiService.get('/products?search=${Uri.encodeComponent(_search)}');
      setState(() { _products = (data as List).map((e) => Product.fromJson(e)).toList(); _loading = false; });
    } on ApiException catch (e) { setState(() { _error = e.message; _loading = false; }); }
    catch (_) { setState(() { _error = 'Connection error'; _loading = false; }); }
  }

  void _showForm([Product? product]) {
    showModalBottomSheet(context: context, isScrollControlled: true, backgroundColor: Colors.transparent,
      builder: (_) => _ProductForm(product: product, onSaved: _load));
  }

  Future<void> _delete(Product p) async {
    final ok = await showDialog<bool>(context: context, builder: (_) => AlertDialog(
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
      title: const Text('Delete Product'),
      content: Text('Delete "${p.name}"? This cannot be undone.'),
      actions: [TextButton(onPressed: () => Navigator.pop(context, false), child: const Text('Cancel')),
        ElevatedButton(style: ElevatedButton.styleFrom(backgroundColor: AppColors.danger), onPressed: () => Navigator.pop(context, true), child: const Text('Delete'))],
    ));
    if (ok != true) return;
    try { await ApiService.delete('/products/${p.id}'); _load(); } on ApiException catch (e) { _showSnack(e.message, error: true); }
  }

  void _showSnack(String msg, {bool error = false}) {
    ScaffoldMessenger.of(context).showSnackBar(SnackBar(
      content: Text(msg), backgroundColor: error ? AppColors.danger : AppColors.success,
      behavior: SnackBarBehavior.floating, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
    ));
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(title: const Text('Products'), actions: [
        IconButton(icon: const Icon(Icons.refresh, color: Colors.white), onPressed: _load),
      ]),
      body: Column(children: [
        Padding(padding: const EdgeInsets.all(16), child: SearchBarWidget(hint: 'Search products...', controller: _searchCtrl, onChanged: (v) { _search = v; _load(); })),
        Expanded(child: _loading ? const LoadingWidget(message: 'Loading products...')
          : _error != null ? ErrorWidget2(message: _error!, onRetry: _load)
          : _products.isEmpty ? EmptyState(icon: Icons.inventory_2_outlined, title: 'No Products', subtitle: 'Add your first product to get started', actionLabel: 'Add Product', onAction: () => _showForm())
          : RefreshIndicator(color: AppColors.primary, onRefresh: _load,
              child: ListView.separated(padding: const EdgeInsets.fromLTRB(16, 0, 16, 100), itemCount: _products.length,
                separatorBuilder: (_, __) => const SizedBox(height: 10),
                itemBuilder: (_, i) => _productTile(_products[i])))),
      ]),
      floatingActionButton: FloatingActionButton.extended(onPressed: () => _showForm(), icon: const Icon(Icons.add), label: const Text('Add Product')),
    );
  }

  Widget _productTile(Product p) {
    final isLow = p.isLowStock;
    final isOut = p.isOutOfStock;
    return AppCard(
      onTap: () => _showForm(p),
      child: Padding(padding: const EdgeInsets.all(16), child: Row(children: [
        Container(width: 52, height: 52, decoration: BoxDecoration(color: isOut ? AppColors.dangerLt : isLow ? AppColors.warningLt : AppColors.primaryLt, borderRadius: BorderRadius.circular(14)),
          child: Icon(Icons.inventory_2_outlined, color: isOut ? AppColors.danger : isLow ? AppColors.warning : AppColors.primary, size: 24)),
        const SizedBox(width: 14),
        Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
          Text(p.name, style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 15, color: AppColors.textPri)),
          const SizedBox(height: 3),
          Text(p.categoryName.isNotEmpty ? p.categoryName : 'No category', style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
          const SizedBox(height: 6),
          Row(children: [
            Text('${AppConstants.currency} ${fmt.format(p.sellingPrice)}', style: const TextStyle(color: AppColors.primary, fontWeight: FontWeight.w700, fontSize: 14)),
            const Spacer(),
            Container(padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3), decoration: BoxDecoration(color: isOut ? AppColors.dangerLt : isLow ? AppColors.warningLt : AppColors.successLt, borderRadius: BorderRadius.circular(8)),
              child: Text('Stock: ${p.stockQuantity.toInt()}', style: TextStyle(color: isOut ? AppColors.danger : isLow ? AppColors.warning : AppColors.success, fontSize: 11, fontWeight: FontWeight.w700))),
          ]),
        ])),
        const SizedBox(width: 8),
        PopupMenuButton<String>(shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
          onSelected: (v) { if (v == 'edit') _showForm(p); else _delete(p); },
          itemBuilder: (_) => [
            const PopupMenuItem(value: 'edit', child: Row(children: [Icon(Icons.edit_outlined, size: 18, color: AppColors.primary), SizedBox(width: 8), Text('Edit')])),
            const PopupMenuItem(value: 'delete', child: Row(children: [Icon(Icons.delete_outline, size: 18, color: AppColors.danger), SizedBox(width: 8), Text('Delete', style: TextStyle(color: AppColors.danger))])),
          ]),
      ])),
    );
  }
}

class _ProductForm extends StatefulWidget {
  final Product? product;
  final VoidCallback onSaved;
  const _ProductForm({this.product, required this.onSaved});
  @override State<_ProductForm> createState() => _ProductFormState();
}

class _ProductFormState extends State<_ProductForm> {
  final _form = GlobalKey<FormState>();
  late TextEditingController _name, _price, _costPrice, _stock, _reorder, _desc;
  List<Map<String, dynamic>> _categories = [];
  int? _catId;
  bool _saving = false;
  String? _err;

  @override
  void initState() {
    super.initState();
    final p = widget.product;
    _name = TextEditingController(text: p?.name);
    _price = TextEditingController(text: p?.sellingPrice.toString());
    _costPrice = TextEditingController(text: p?.purchasePrice.toString());
    _stock = TextEditingController(text: p?.stockQuantity.toString() ?? '0');
    _reorder = TextEditingController(text: p?.reorderLevel.toString() ?? '5');
    _desc = TextEditingController(text: p?.description);
    _catId = p?.category?['id'];
    _loadCategories();
  }

  @override
  void dispose() {
    for (final c in [_name, _price, _costPrice, _stock, _reorder, _desc]) c.dispose();
    super.dispose();
  }

  Future<void> _loadCategories() async {
    try {
      final data = await ApiService.get('/categories');
      setState(() => _categories = (data as List).map((e) => Map<String, dynamic>.from(e)).toList());
    } catch (_) {}
  }

  Future<void> _save() async {
    if (!_form.currentState!.validate()) return;
    setState(() { _saving = true; _err = null; });
    final body = {
      'name': _name.text.trim(), 'selling_price': _price.text, 'purchase_price': _costPrice.text,
      'stock_quantity': _stock.text, 'reorder_level': _reorder.text,
      if (_desc.text.isNotEmpty) 'description': _desc.text,
      if (_catId != null) 'category_id': _catId,
    };
    try {
      if (widget.product != null) await ApiService.put('/products/${widget.product!.id}', body);
      else await ApiService.post('/products', body);
      widget.onSaved();
      if (mounted) Navigator.pop(context);
    } on ApiException catch (e) { setState(() { _err = e.message; _saving = false; }); }
    catch (_) { setState(() { _err = 'Save failed'; _saving = false; }); }
  }

  @override
  Widget build(BuildContext context) {
    final isEdit = widget.product != null;
    return Container(
      decoration: const BoxDecoration(color: AppColors.surface, borderRadius: BorderRadius.vertical(top: Radius.circular(24))),
      padding: EdgeInsets.only(bottom: MediaQuery.of(context).viewInsets.bottom),
      child: SingleChildScrollView(padding: const EdgeInsets.fromLTRB(24, 16, 24, 32), child: Form(key: _form, child: Column(crossAxisAlignment: CrossAxisAlignment.stretch, children: [
        Center(child: Container(width: 40, height: 4, decoration: BoxDecoration(color: AppColors.border, borderRadius: BorderRadius.circular(4)))),
        const SizedBox(height: 20),
        Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
          Text(isEdit ? 'Edit Product' : 'Add Product', style: const TextStyle(fontSize: 20, fontWeight: FontWeight.w700)),
          IconButton(icon: const Icon(Icons.close), onPressed: () => Navigator.pop(context)),
        ]),
        if (_err != null) ...[const SizedBox(height: 12), Container(padding: const EdgeInsets.all(12), decoration: BoxDecoration(color: AppColors.dangerLt, borderRadius: BorderRadius.circular(10)), child: Text(_err!, style: const TextStyle(color: AppColors.danger)))],
        const SizedBox(height: 16),
        TextFormField(controller: _name, decoration: const InputDecoration(labelText: 'Product Name *'), validator: (v) => v!.isNotEmpty ? null : 'Required'),
        const SizedBox(height: 12),
        if (_categories.isNotEmpty) DropdownButtonFormField<int>(value: _catId, decoration: const InputDecoration(labelText: 'Category'),
          items: _categories.map((c) => DropdownMenuItem(value: c['id'] as int, child: Text(c['name']))).toList(),
          onChanged: (v) => setState(() => _catId = v)),
        if (_categories.isNotEmpty) const SizedBox(height: 12),
        Row(children: [
          Expanded(child: TextFormField(controller: _price, keyboardType: TextInputType.number, decoration: const InputDecoration(labelText: 'Selling Price *'), validator: (v) => v!.isNotEmpty ? null : 'Required')),
          const SizedBox(width: 12),
          Expanded(child: TextFormField(controller: _costPrice, keyboardType: TextInputType.number, decoration: const InputDecoration(labelText: 'Cost Price'))),
        ]),
        const SizedBox(height: 12),
        Row(children: [
          Expanded(child: TextFormField(controller: _stock, keyboardType: TextInputType.number, decoration: const InputDecoration(labelText: 'Stock Qty'))),
          const SizedBox(width: 12),
          Expanded(child: TextFormField(controller: _reorder, keyboardType: TextInputType.number, decoration: const InputDecoration(labelText: 'Reorder Level'))),
        ]),
        const SizedBox(height: 12),
        TextFormField(controller: _desc, decoration: const InputDecoration(labelText: 'Description'), maxLines: 2),
        const SizedBox(height: 24),
        SizedBox(height: 52, child: ElevatedButton(onPressed: _saving ? null : _save,
          child: _saving ? const SizedBox(width: 22, height: 22, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5)) : Text(isEdit ? 'Update Product' : 'Add Product', style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w700)))),
      ]))),
    );
  }
}