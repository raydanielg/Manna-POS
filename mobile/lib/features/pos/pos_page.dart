import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../core/api_service.dart';
import '../../shared/theme/app_theme.dart';
import '../../shared/models/product.dart';
import '../../shared/widgets/empty_state.dart';
import '../../shared/widgets/search_bar_widget.dart';
import '../../shared/constants/app_constants.dart';

class CartItem {
  final Product product;
  int quantity;
  CartItem({required this.product, this.quantity = 1});
  double get total => product.sellingPrice * quantity;
}

class PosPage extends StatefulWidget {
  const PosPage({super.key});
  @override State<PosPage> createState() => _PosPageState();
}

class _PosPageState extends State<PosPage> {
  List<Product> _products = [];
  List<Product> _filtered = [];
  final Map<int, CartItem> _cart = {};
  bool _loading = true;
  String _payMethod = 'cash';
  final _paidCtrl = TextEditingController();
  final _searchCtrl = TextEditingController();
  bool _processing = false;
  final fmt = NumberFormat('#,##0.00');

  @override
  void initState() { super.initState(); _load(); }
  @override
  void dispose() { _paidCtrl.dispose(); _searchCtrl.dispose(); super.dispose(); }

  Future<void> _load() async {
    setState(() => _loading = true);
    try {
      final data = await ApiService.get('/products?status=active');
      setState(() { _products = (data as List).map((e) => Product.fromJson(e)).toList(); _filtered = _products; _loading = false; });
    } catch (_) { setState(() => _loading = false); }
  }

  void _filter(String q) {
    setState(() { _filtered = q.isEmpty ? _products : _products.where((p) => p.name.toLowerCase().contains(q.toLowerCase()) || (p.sku ?? '').toLowerCase().contains(q.toLowerCase())).toList(); });
  }

  void _addToCart(Product p) {
    if (p.isOutOfStock) { _showSnack('${p.name} is out of stock', error: true); return; }
    setState(() { if (_cart.containsKey(p.id)) _cart[p.id]!.quantity++; else _cart[p.id] = CartItem(product: p); });
  }

  void _removeFromCart(int id) => setState(() => _cart.remove(id));
  void _updateQty(int id, int qty) => setState(() { if (qty <= 0) _cart.remove(id); else _cart[id]?.quantity = qty; });

  double get _subtotal => _cart.values.fold(0, (a, b) => a + b.total);
  double get _total => _subtotal;
  double get _change => (double.tryParse(_paidCtrl.text) ?? 0) - _total;

  Future<void> _completeSale() async {
    if (_cart.isEmpty) { _showSnack('Cart is empty', error: true); return; }
    setState(() => _processing = true);
    final paid = double.tryParse(_paidCtrl.text) ?? _total;
    final body = {
      'sale_date': DateTime.now().toIso8601String().split('T')[0],
      'status': 'completed',
      'payment_method': _payMethod,
      'subtotal': _subtotal,
      'discount': 0,
      'tax': 0,
      'total': _total,
      'paid': paid,
      'items': _cart.values.map((c) => {'product_id': c.product.id, 'quantity': c.quantity, 'unit_price': c.product.sellingPrice, 'discount': 0}).toList(),
    };
    try {
      await ApiService.post('/sales', body);
      setState(() { _cart.clear(); _paidCtrl.clear(); _processing = false; });
      _showSnack('Sale completed successfully!');
      _load();
    } on ApiException catch (e) { setState(() => _processing = false); _showSnack(e.message, error: true); }
    catch (_) { setState(() => _processing = false); _showSnack('Sale failed', error: true); }
  }

  void _showSnack(String msg, {bool error = false}) {
    ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(msg), backgroundColor: error ? AppColors.danger : AppColors.success,
      behavior: SnackBarBehavior.floating, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10))));
  }

  @override
  Widget build(BuildContext context) {
    final cartCount = _cart.values.fold(0, (a, b) => a + b.quantity);
    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(title: const Text('Point of Sale'), actions: [
        if (cartCount > 0) IconButton(icon: const Icon(Icons.delete_outline, color: Colors.white), onPressed: () => setState(() => _cart.clear())),
        IconButton(icon: const Icon(Icons.refresh, color: Colors.white), onPressed: _load),
      ]),
      body: _loading ? const LoadingWidget(message: 'Loading products...')
        : Row(crossAxisAlignment: CrossAxisAlignment.start, children: [
            // Products
            Expanded(flex: 6, child: Column(children: [
              Padding(padding: const EdgeInsets.all(12), child: SearchBarWidget(hint: 'Search or scan barcode...', controller: _searchCtrl, onChanged: _filter)),
              Expanded(child: _filtered.isEmpty ? const EmptyState(icon: Icons.search_off, title: 'No products found')
                : GridView.builder(padding: const EdgeInsets.fromLTRB(12, 0, 12, 12), gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(crossAxisCount: 2, childAspectRatio: 0.8, crossAxisSpacing: 10, mainAxisSpacing: 10),
                    itemCount: _filtered.length, itemBuilder: (_, i) => _productCard(_filtered[i]))),
            ])),
            // Cart
            Container(width: 320, height: double.infinity, decoration: const BoxDecoration(color: Colors.white, border: Border(left: BorderSide(color: AppColors.border))),
              child: Column(children: [
                Container(padding: const EdgeInsets.all(16), decoration: const BoxDecoration(border: Border(bottom: BorderSide(color: AppColors.border))),
                  child: Row(children: [
                    const Icon(Icons.shopping_cart_outlined, color: AppColors.primary, size: 22),
                    const SizedBox(width: 8),
                    Text('Cart', style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w700)),
                    const Spacer(),
                    if (cartCount > 0) Container(padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4), decoration: BoxDecoration(color: AppColors.primary, borderRadius: BorderRadius.circular(12)),
                      child: Text('$cartCount', style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w700, fontSize: 13))),
                  ])),
                Expanded(child: _cart.isEmpty ? const EmptyState(icon: Icons.shopping_cart_outlined, title: 'Cart empty', subtitle: 'Tap products to add')
                  : ListView(padding: const EdgeInsets.all(12), children: _cart.values.map(_cartTile).toList())),
                Container(padding: const EdgeInsets.all(16), decoration: const BoxDecoration(border: Border(top: BorderSide(color: AppColors.border))), child: Column(children: [
                  _row('Subtotal', '${AppConstants.currency} ${fmt.format(_subtotal)}'),
                  const Divider(height: 16),
                  _row('TOTAL', '${AppConstants.currency} ${fmt.format(_total)}', big: true),
                  const SizedBox(height: 12),
                  DropdownButtonFormField<String>(value: _payMethod, decoration: const InputDecoration(labelText: 'Payment Method', contentPadding: EdgeInsets.symmetric(horizontal: 12, vertical: 10)),
                    items: const [DropdownMenuItem(value: 'cash', child: Text('Cash')), DropdownMenuItem(value: 'card', child: Text('Card')), DropdownMenuItem(value: 'mobile_money', child: Text('Mobile Money')), DropdownMenuItem(value: 'credit', child: Text('Credit'))],
                    onChanged: (v) => setState(() => _payMethod = v!)),
                  const SizedBox(height: 10),
                  Row(children: [
                    Expanded(child: TextField(controller: _paidCtrl, keyboardType: TextInputType.number, onChanged: (_) => setState(() {}),
                      decoration: const InputDecoration(labelText: 'Amount Paid', contentPadding: EdgeInsets.symmetric(horizontal: 12, vertical: 10)))),
                    const SizedBox(width: 10),
                    Column(crossAxisAlignment: CrossAxisAlignment.end, children: [
                      const Text('Change', style: TextStyle(fontSize: 11, color: AppColors.textSec)),
                      Text('${AppConstants.currency} ${fmt.format(_change > 0 ? _change : 0)}', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 14, color: _change >= 0 ? AppColors.success : AppColors.danger)),
                    ]),
                  ]),
                  const SizedBox(height: 14),
                  SizedBox(width: double.infinity, height: 52, child: ElevatedButton(onPressed: _cart.isEmpty || _processing ? null : _completeSale,
                    style: ElevatedButton.styleFrom(backgroundColor: AppColors.success),
                    child: _processing ? const SizedBox(width: 22, height: 22, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5)) : Row(mainAxisAlignment: MainAxisAlignment.center, children: [const Icon(Icons.check_circle_outline), const SizedBox(width: 8), Text('Complete Sale (${AppConstants.currency} ${fmt.format(_total)})', style: const TextStyle(fontWeight: FontWeight.w700))]))),
                ])),
              ])),
          ]),
    );
  }

  Widget _productCard(Product p) {
    final inCart = _cart.containsKey(p.id);
    return GestureDetector(onTap: () => _addToCart(p), child: Container(decoration: BoxDecoration(
      color: AppColors.surface,
      borderRadius: BorderRadius.circular(14),
      border: Border.all(color: inCart ? AppColors.primary : AppColors.border, width: inCart ? 2 : 1),
      boxShadow: [BoxShadow(color: Colors.black.withValues(alpha: 0.05), blurRadius: 8, offset: const Offset(0, 3))],
    ), child: Padding(padding: const EdgeInsets.all(12), child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
      Row(children: [
        Container(width: 36, height: 36, decoration: BoxDecoration(color: p.isOutOfStock ? AppColors.dangerLt : AppColors.primaryLt, borderRadius: BorderRadius.circular(10)),
          child: Icon(Icons.inventory_2_outlined, color: p.isOutOfStock ? AppColors.danger : AppColors.primary, size: 18)),
        const Spacer(),
        if (inCart) Container(padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2), decoration: BoxDecoration(color: AppColors.primary, borderRadius: BorderRadius.circular(8)),
          child: Text('${_cart[p.id]?.quantity}', style: const TextStyle(color: Colors.white, fontSize: 11, fontWeight: FontWeight.w700))),
      ]),
      const Spacer(),
      Text(p.name, style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 13, color: AppColors.textPri), maxLines: 2, overflow: TextOverflow.ellipsis),
      const SizedBox(height: 4),
      Text('${AppConstants.currency} ${fmt.format(p.sellingPrice)}', style: const TextStyle(color: AppColors.primary, fontWeight: FontWeight.w700, fontSize: 14)),
      const SizedBox(height: 4),
      Text(p.isOutOfStock ? 'Out of stock' : 'Stock: ${p.stockQuantity.toInt()}', style: TextStyle(fontSize: 11, color: p.isOutOfStock ? AppColors.danger : AppColors.textSec, fontWeight: FontWeight.w600)),
    ]))));
  }

  Widget _cartTile(CartItem c) {
    return Padding(padding: const EdgeInsets.only(bottom: 10), child: Row(children: [
      Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
        Text(c.product.name, style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13), maxLines: 1, overflow: TextOverflow.ellipsis),
        Text('${AppConstants.currency} ${fmt.format(c.product.sellingPrice)} each', style: const TextStyle(color: AppColors.textSec, fontSize: 11)),
      ])),
      Row(children: [
        _qtyBtn(Icons.remove, () => _updateQty(c.product.id, c.quantity - 1)),
        Container(width: 32, alignment: Alignment.center, child: Text('${c.quantity}', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14))),
        _qtyBtn(Icons.add, () => _updateQty(c.product.id, c.quantity + 1)),
        const SizedBox(width: 6),
        IconButton(icon: const Icon(Icons.close, size: 18, color: AppColors.danger), onPressed: () => _removeFromCart(c.product.id), padding: EdgeInsets.zero, constraints: const BoxConstraints(minWidth: 28, minHeight: 28)),
      ]),
    ]));
  }

  Widget _qtyBtn(IconData icon, VoidCallback onTap) => GestureDetector(onTap: onTap, child: Container(width: 28, height: 28, decoration: BoxDecoration(color: AppColors.primaryLt, borderRadius: BorderRadius.circular(8)), child: Icon(icon, size: 16, color: AppColors.primary)));
  Widget _row(String l, String v, {bool big = false}) => Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [Text(l, style: TextStyle(color: big ? AppColors.textPri : AppColors.textSec, fontWeight: big ? FontWeight.w700 : FontWeight.w400, fontSize: big ? 16 : 13)), Text(v, style: TextStyle(fontWeight: FontWeight.w700, fontSize: big ? 18 : 13, color: big ? AppColors.primary : AppColors.textPri))]);
}