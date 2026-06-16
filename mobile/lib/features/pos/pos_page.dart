import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:mobile_scanner/mobile_scanner.dart';
import 'package:provider/provider.dart';
import '../../core/api_service.dart';
import '../../core/auth_provider.dart';
import '../../shared/theme/app_theme.dart';
import '../../shared/models/product.dart';

class CartItem {
  final Product product;
  int qty;
  CartItem({required this.product, this.qty = 1});
  double get total => product.sellingPrice * qty;
}

class PosPage extends StatefulWidget {
  const PosPage({super.key});
  @override State<PosPage> createState() => _PosPageState();
}

class _PosPageState extends State<PosPage> with SingleTickerProviderStateMixin {
  late TabController _tab;
  List<Product> _products = [];
  List<Product> _filtered = [];
  final Map<int, CartItem> _cart = {};
  bool _loading = true;
  String _payMethod = 'cash';
  final _paidCtrl = TextEditingController();
  final _searchCtrl = TextEditingController();
  bool _processing = false;
  final _fmt = NumberFormat('#,##0');

  @override
  void initState() {
    super.initState();
    _tab = TabController(length: 2, vsync: this);
    _load();
  }

  @override
  void dispose() { _tab.dispose(); _paidCtrl.dispose(); _searchCtrl.dispose(); super.dispose(); }

  Future<void> _load() async {
    setState(() => _loading = true);
    try {
      final data = await ApiService.get('/products?status=active') as List;
      setState(() { _products = data.map((e) => Product.fromJson(e)).toList(); _filtered = _products; _loading = false; });
    } catch (_) { setState(() => _loading = false); }
  }

  void _filter(String q) {
    setState(() {
      _filtered = q.isEmpty ? _products : _products.where((p) =>
        p.name.toLowerCase().contains(q.toLowerCase()) ||
        (p.sku ?? '').toLowerCase().contains(q.toLowerCase()) ||
        (p.barcode ?? '').contains(q)).toList();
    });
  }

  void _addToCart(Product p) {
    if (p.isOutOfStock) { _snack('${p.name} is out of stock', error: true); return; }
    setState(() {
      if (_cart.containsKey(p.id)) {
        if (_cart[p.id]!.qty >= p.stockQuantity) { _snack('Max stock reached', error: true); return; }
        _cart[p.id]!.qty++;
      } else {
        _cart[p.id] = CartItem(product: p);
      }
    });
    if (_cart.length == 1) {
      Future.delayed(const Duration(milliseconds: 200), () { if (mounted) _tab.animateTo(1); });
    }
  }

  void _updateQty(int id, int delta) {
    setState(() {
      if (!_cart.containsKey(id)) return;
      final newQty = _cart[id]!.qty + delta;
      if (newQty <= 0) { _cart.remove(id); return; }
      if (newQty > _cart[id]!.product.stockQuantity) return;
      _cart[id]!.qty = newQty;
    });
  }

  void _removeFromCart(int id) => setState(() => _cart.remove(id));

  double get _subtotal => _cart.values.fold(0, (s, i) => s + i.total);
  double get _tax => _subtotal * 0.18;
  double get _total => _subtotal + _tax;
  double get _paid => double.tryParse(_paidCtrl.text) ?? 0;
  double get _change => _paid - _total;
  int get _itemCount => _cart.values.fold(0, (s, i) => s + i.qty);

  Future<void> _completeSale() async {
    if (_cart.isEmpty) { _snack('Cart is empty', error: true); return; }
    if (_payMethod == 'cash' && _paid < _total) { _snack('Insufficient payment amount', error: true); return; }
    setState(() => _processing = true);
    try {
      final user = context.read<AuthProvider>().user;
      final body = {
        'status': 'completed',
        'payment_method': _payMethod,
        'paid_amount': _payMethod == 'cash' ? _paid : _total,
        'tax_amount': _tax,
        'items': _cart.values.map((i) => {
          'product_id': i.product.id,
          'product_name': i.product.name,
          'quantity': i.qty,
          'unit_price': i.product.sellingPrice,
          'total': i.total,
        }).toList(),
      };
      final sale = await ApiService.post('/sales', body);
      if (mounted) {
        setState(() { _processing = false; });
        await _showReceipt(sale, user?.displayBusiness ?? 'MannaPOS');
        setState(() { _cart.clear(); _paidCtrl.clear(); _tab.animateTo(0); });
        _load();
      }
    } on ApiException catch (e) {
      setState(() => _processing = false);
      _snack(e.message, error: true);
    } catch (_) {
      setState(() => _processing = false);
      _snack('Sale failed. Try again.', error: true);
    }
  }

  Future<void> _scanBarcode() async {
    final result = await Navigator.push<String>(context, MaterialPageRoute(builder: (_) => const _BarcodeScannerPage()));
    if (result != null && mounted) {
      final found = _products.where((p) => p.barcode == result || p.sku == result).firstOrNull;
      if (found != null) { _addToCart(found); _snack('Added: ${found.name}'); }
      else { _searchCtrl.text = result; _filter(result); _snack('No product found for: $result', error: true); }
    }
  }

  Future<void> _showReceipt(Map<String, dynamic> sale, String bizName) async {
    await showModalBottomSheet(
      context: context, isScrollControlled: true, backgroundColor: Colors.transparent,
      builder: (_) => _ReceiptSheet(sale: sale, bizName: bizName),
    );
  }

  void _snack(String msg, {bool error = false}) => ScaffoldMessenger.of(context).showSnackBar(SnackBar(
    content: Text(msg), backgroundColor: error ? AppColors.danger : AppColors.success,
    behavior: SnackBarBehavior.floating, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10))));

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(
        backgroundColor: AppColors.primary,
        foregroundColor: Colors.white,
        title: const Text('Point of Sale', style: TextStyle(color: Colors.white, fontWeight: FontWeight.w700)),
        actions: [
          if (_itemCount > 0)
            Padding(padding: const EdgeInsets.only(right: 8),
              child: GestureDetector(
                onTap: () => _tab.animateTo(1),
                child: Stack(children: [
                  const Padding(padding: EdgeInsets.all(8), child: Icon(Icons.shopping_cart_outlined, color: Colors.white)),
                  Positioned(top: 4, right: 4, child: Container(
                    width: 16, height: 16, decoration: const BoxDecoration(color: AppColors.danger, shape: BoxShape.circle),
                    child: Center(child: Text('$_itemCount', style: const TextStyle(color: Colors.white, fontSize: 9, fontWeight: FontWeight.w800))))),
                ])),
            ),
          IconButton(icon: const Icon(Icons.refresh, color: Colors.white), onPressed: _load),
        ],
        bottom: TabBar(
          controller: _tab,
          indicatorColor: Colors.white, labelColor: Colors.white, unselectedLabelColor: Colors.white70,
          tabs: [
            const Tab(icon: Icon(Icons.grid_view, size: 18), text: 'Products'),
            Tab(icon: const Icon(Icons.shopping_cart, size: 18), text: _cart.isEmpty ? 'Cart' : 'Cart ($_itemCount)'),
          ],
        ),
      ),
      body: TabBarView(
        controller: _tab,
        children: [_productsTab(), _cartTab()],
      ),
    );
  }

  // ── Products Tab ──────────────────────────────────────────
  Widget _productsTab() => Column(children: [
    Container(color: Colors.white, padding: const EdgeInsets.all(12),
      child: Row(children: [
        Expanded(child: Container(
          decoration: BoxDecoration(color: AppColors.bg, borderRadius: BorderRadius.circular(12), border: Border.all(color: AppColors.border)),
          child: TextField(
            controller: _searchCtrl,
            decoration: const InputDecoration(
              hintText: 'Search products or SKU...', hintStyle: TextStyle(color: AppColors.textSec, fontSize: 14),
              border: InputBorder.none, enabledBorder: InputBorder.none, focusedBorder: InputBorder.none,
              prefixIcon: Icon(Icons.search, color: AppColors.textSec, size: 20),
              contentPadding: EdgeInsets.symmetric(vertical: 12),
            ),
            onChanged: _filter,
          ),
        )),
        const SizedBox(width: 10),
        GestureDetector(
          onTap: _scanBarcode,
          child: Container(width: 46, height: 46, decoration: BoxDecoration(color: AppColors.primaryLt, borderRadius: BorderRadius.circular(12), border: Border.all(color: AppColors.primary.withValues(alpha: 0.4))),
            child: const Icon(Icons.qr_code_scanner, color: AppColors.primary, size: 22)),
        ),
      ]),
    ),
    Expanded(child: _loading
      ? const Center(child: CircularProgressIndicator(color: AppColors.primary))
      : _filtered.isEmpty
        ? const Center(child: Column(mainAxisSize: MainAxisSize.min, children: [
            Icon(Icons.inventory_2_outlined, size: 48, color: AppColors.textSec),
            SizedBox(height: 8),
            Text('No products found', style: TextStyle(color: AppColors.textSec)),
          ]))
        : GridView.builder(
            padding: const EdgeInsets.all(12),
            gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(crossAxisCount: 2, crossAxisSpacing: 10, mainAxisSpacing: 10, childAspectRatio: 0.78),
            itemCount: _filtered.length,
            itemBuilder: (_, i) => _productCard(_filtered[i]),
          )),
  ]);

  Widget _productCard(Product p) {
    final inCart = _cart.containsKey(p.id);
    final cartQty = inCart ? _cart[p.id]!.qty : 0;
    return GestureDetector(
      onTap: () => _addToCart(p),
      child: Container(
        decoration: BoxDecoration(
          color: p.isOutOfStock ? AppColors.bg : Colors.white,
          borderRadius: BorderRadius.circular(14),
          border: Border.all(color: inCart ? AppColors.primary : AppColors.border, width: inCart ? 2 : 1),
          boxShadow: const [BoxShadow(color: Color(0x06000000), blurRadius: 10, offset: Offset(0, 2))],
        ),
        child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
          // Image
          ClipRRect(
            borderRadius: const BorderRadius.vertical(top: Radius.circular(13)),
            child: SizedBox(
              height: 90, width: double.infinity,
              child: p.imageUrl != null
                ? Image.network(p.imageUrl!, fit: BoxFit.cover,
                    errorBuilder: (_, __, ___) => _imagePlaceholder(p))
                : _imagePlaceholder(p),
            ),
          ),
          Padding(padding: const EdgeInsets.all(10), child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            Text(p.name, style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 13), maxLines: 2, overflow: TextOverflow.ellipsis),
            const SizedBox(height: 4),
            Text('TSh ${_fmt.format(p.sellingPrice)}', style: const TextStyle(color: AppColors.primary, fontWeight: FontWeight.w800, fontSize: 14)),
            const SizedBox(height: 4),
            Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
              Text(p.isOutOfStock ? 'Out of stock' : '${p.stockQuantity} ${p.unitShort}',
                style: TextStyle(color: p.isOutOfStock ? AppColors.danger : p.isLowStock ? AppColors.warning : AppColors.textSec, fontSize: 11, fontWeight: FontWeight.w500)),
              if (inCart) Container(padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                decoration: BoxDecoration(color: AppColors.primaryLt, borderRadius: BorderRadius.circular(8)),
                child: Text('x$cartQty', style: const TextStyle(color: AppColors.primary, fontSize: 11, fontWeight: FontWeight.w800))),
            ]),
          ])),
        ]),
      ),
    );
  }

  Widget _imagePlaceholder(Product p) => Container(
    color: AppColors.primaryLt,
    child: Center(child: Text(p.name.substring(0, p.name.length > 2 ? 2 : 1).toUpperCase(),
      style: const TextStyle(color: AppColors.primary, fontWeight: FontWeight.w800, fontSize: 22))),
  );

  // ── Cart Tab ──────────────────────────────────────────────
  Widget _cartTab() {
    final items = _cart.values.toList();
    if (items.isEmpty) return Center(child: Column(mainAxisSize: MainAxisSize.min, children: [
      const Icon(Icons.shopping_cart_outlined, size: 64, color: AppColors.textSec),
      const SizedBox(height: 12),
      const Text('Cart is empty', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 16, color: AppColors.textSec)),
      const SizedBox(height: 8),
      const Text('Tap a product to add it here', style: TextStyle(color: AppColors.textSec, fontSize: 13)),
      const SizedBox(height: 24),
      ElevatedButton(onPressed: () => _tab.animateTo(0), child: const Text('Browse Products')),
    ]));

    return Column(children: [
      Expanded(child: ListView.separated(
        padding: const EdgeInsets.fromLTRB(16, 12, 16, 0),
        itemCount: items.length,
        separatorBuilder: (_, __) => const SizedBox(height: 8),
        itemBuilder: (_, i) => _cartItemWidget(items[i]),
      )),
      // Order summary + checkout
      Container(
        decoration: const BoxDecoration(color: Colors.white, borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
          boxShadow: [BoxShadow(color: Color(0x10000000), blurRadius: 20, offset: Offset(0, -4))]),
        padding: const EdgeInsets.fromLTRB(20, 20, 20, 32),
        child: Column(crossAxisAlignment: CrossAxisAlignment.stretch, children: [
          // Totals
          _totalRow('Subtotal', _subtotal),
          const SizedBox(height: 6),
          _totalRow('VAT (18%)', _tax),
          const Divider(height: 20),
          _totalRow('Total', _total, bold: true, large: true),
          const SizedBox(height: 16),
          // Payment method
          Row(children: [
            Expanded(child: _payChip('cash', Icons.payments_outlined, 'Cash')),
            const SizedBox(width: 8),
            Expanded(child: _payChip('card', Icons.credit_card, 'Card')),
            const SizedBox(width: 8),
            Expanded(child: _payChip('mobile', Icons.phone_android, 'Mobile')),
          ]),
          if (_payMethod == 'cash') ...[
            const SizedBox(height: 12),
            TextField(
              controller: _paidCtrl,
              keyboardType: TextInputType.number,
              onChanged: (_) => setState(() {}),
              decoration: InputDecoration(
                labelText: 'Amount Paid',
                prefixText: 'TSh ',
                helperText: _paid >= _total && _paidCtrl.text.isNotEmpty ? 'Change: TSh ${_fmt.format(_change)}' : null,
                helperStyle: const TextStyle(color: AppColors.success, fontWeight: FontWeight.w700),
                errorText: _paidCtrl.text.isNotEmpty && _paid < _total ? 'Insufficient (need TSh ${_fmt.format(_total - _paid)} more)' : null,
              ),
            ),
          ],
          const SizedBox(height: 16),
          SizedBox(height: 54, child: ElevatedButton.icon(
            onPressed: _processing ? null : _completeSale,
            icon: _processing ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5)) : const Icon(Icons.check_circle_outline),
            label: Text(_processing ? 'Processing...' : 'Complete Sale · TSh ${_fmt.format(_total)}', style: const TextStyle(fontSize: 15, fontWeight: FontWeight.w700)),
          )),
          const SizedBox(height: 8),
          TextButton(onPressed: () { setState(() => _cart.clear()); _tab.animateTo(0); }, child: const Text('Clear Cart', style: TextStyle(color: AppColors.danger))),
        ]),
      ),
    ]);
  }

  Widget _cartItemWidget(CartItem item) => Container(
    decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(12), border: Border.all(color: AppColors.border)),
    padding: const EdgeInsets.all(12),
    child: Row(children: [
      // Image
      ClipRRect(borderRadius: BorderRadius.circular(8), child: SizedBox(width: 52, height: 52, child: item.product.imageUrl != null
        ? Image.network(item.product.imageUrl!, fit: BoxFit.cover, errorBuilder: (_, __, ___) => _imgThumb(item.product))
        : _imgThumb(item.product))),
      const SizedBox(width: 12),
      Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
        Text(item.product.name, style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14), maxLines: 1, overflow: TextOverflow.ellipsis),
        Text('TSh ${_fmt.format(item.product.sellingPrice)} each', style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
        Text('TSh ${_fmt.format(item.total)}', style: const TextStyle(color: AppColors.primary, fontWeight: FontWeight.w800, fontSize: 14)),
      ])),
      Column(children: [
        Row(children: [
          _qtyBtn(Icons.remove, () => _updateQty(item.product.id, -1)),
          Container(width: 32, alignment: Alignment.center, child: Text('${item.qty}', style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 16))),
          _qtyBtn(Icons.add, () => _updateQty(item.product.id, 1)),
        ]),
        const SizedBox(height: 4),
        GestureDetector(onTap: () => _removeFromCart(item.product.id), child: const Text('Remove', style: TextStyle(color: AppColors.danger, fontSize: 11, fontWeight: FontWeight.w600))),
      ]),
    ]),
  );

  Widget _imgThumb(Product p) => Container(color: AppColors.primaryLt, child: Center(child: Text(p.name[0].toUpperCase(), style: const TextStyle(color: AppColors.primary, fontWeight: FontWeight.w800, fontSize: 18))));

  Widget _qtyBtn(IconData icon, VoidCallback onTap) => GestureDetector(
    onTap: onTap,
    child: Container(width: 28, height: 28, decoration: BoxDecoration(color: AppColors.bg, borderRadius: BorderRadius.circular(8), border: Border.all(color: AppColors.border)),
      child: Icon(icon, size: 16, color: AppColors.textPri)),
  );

  Widget _payChip(String val, IconData icon, String label) {
    final sel = _payMethod == val;
    return GestureDetector(
      onTap: () => setState(() { _payMethod = val; _paidCtrl.clear(); }),
      child: AnimatedContainer(duration: const Duration(milliseconds: 180),
        padding: const EdgeInsets.symmetric(vertical: 10),
        decoration: BoxDecoration(color: sel ? AppColors.primaryLt : Colors.white, borderRadius: BorderRadius.circular(10), border: Border.all(color: sel ? AppColors.primary : AppColors.border, width: sel ? 2 : 1)),
        child: Column(mainAxisSize: MainAxisSize.min, children: [
          Icon(icon, size: 18, color: sel ? AppColors.primary : AppColors.textSec),
          const SizedBox(height: 3),
          Text(label, style: TextStyle(fontSize: 11, fontWeight: FontWeight.w700, color: sel ? AppColors.primary : AppColors.textSec)),
        ])),
    );
  }

  Widget _totalRow(String label, double amount, {bool bold = false, bool large = false}) => Row(
    mainAxisAlignment: MainAxisAlignment.spaceBetween,
    children: [
      Text(label, style: TextStyle(fontWeight: bold ? FontWeight.w700 : FontWeight.w500, fontSize: large ? 16 : 14, color: bold ? AppColors.textPri : AppColors.textSec)),
      Text('TSh ${_fmt.format(amount)}', style: TextStyle(fontWeight: bold ? FontWeight.w800 : FontWeight.w600, fontSize: large ? 18 : 14, color: bold ? AppColors.primary : AppColors.textPri)),
    ],
  );
}

// ── Barcode Scanner Page ──────────────────────────────────
class _BarcodeScannerPage extends StatefulWidget {
  const _BarcodeScannerPage();
  @override State<_BarcodeScannerPage> createState() => _BarcodeScannerPageState();
}
class _BarcodeScannerPageState extends State<_BarcodeScannerPage> {
  final _ctrl = MobileScannerController();
  bool _scanned = false;
  @override void dispose() { _ctrl.dispose(); super.dispose(); }

  @override
  Widget build(BuildContext context) => Scaffold(
    backgroundColor: Colors.black,
    appBar: AppBar(
      backgroundColor: Colors.black, foregroundColor: Colors.white,
      title: const Text('Scan Barcode'),
      actions: [IconButton(icon: const Icon(Icons.flash_on, color: Colors.white), onPressed: () => _ctrl.toggleTorch())],
    ),
    body: Stack(children: [
      MobileScanner(controller: _ctrl, onDetect: (capture) {
        if (_scanned) return;
        final barcode = capture.barcodes.firstOrNull?.rawValue;
        if (barcode != null) {
          _scanned = true;
          Navigator.pop(context, barcode);
        }
      }),
      // Scanner overlay
      Center(child: Container(
        width: 260, height: 160,
        decoration: BoxDecoration(
          border: Border.all(color: AppColors.primary, width: 3),
          borderRadius: BorderRadius.circular(16),
        ),
        child: const Center(child: Text('Point camera at barcode', style: TextStyle(color: Colors.white70, fontSize: 14))),
      )),
    ]),
  );
}

// ── Receipt Sheet ─────────────────────────────────────────
class _ReceiptSheet extends StatelessWidget {
  final Map<String, dynamic> sale;
  final String bizName;
  const _ReceiptSheet({required this.sale, required this.bizName});

  @override
  Widget build(BuildContext context) {
    final fmt = NumberFormat('#,##0.00');
    final items = (sale['items'] as List? ?? []);
    final ref = sale['reference'] ?? 'N/A';
    final date = sale['sale_date'] ?? DateTime.now().toString().substring(0, 10);
    final total = double.tryParse(sale['total']?.toString() ?? '0') ?? 0;
    final paid = double.tryParse(sale['paid']?.toString() ?? '0') ?? 0;
    final payMethod = (sale['payment_method'] ?? 'cash').toString().toUpperCase();

    return Container(
      height: MediaQuery.of(context).size.height * 0.85,
      decoration: const BoxDecoration(color: Colors.white, borderRadius: BorderRadius.vertical(top: Radius.circular(24))),
      child: Column(children: [
        // Handle
        Center(child: Container(margin: const EdgeInsets.only(top: 12), width: 40, height: 4, decoration: BoxDecoration(color: AppColors.border, borderRadius: BorderRadius.circular(4)))),
        // Header
        Padding(padding: const EdgeInsets.all(20), child: Row(children: [
          const Expanded(child: Text('Receipt', style: TextStyle(fontWeight: FontWeight.w800, fontSize: 20))),
          IconButton(icon: const Icon(Icons.close), onPressed: () => Navigator.pop(context)),
        ])),
        // Receipt content
        Expanded(child: SingleChildScrollView(
          padding: const EdgeInsets.symmetric(horizontal: 24),
          child: Column(children: [
            // Success icon
            Container(width: 64, height: 64, decoration: const BoxDecoration(color: AppColors.successLt, shape: BoxShape.circle),
              child: const Icon(Icons.check_circle, color: AppColors.success, size: 36)),
            const SizedBox(height: 12),
            const Text('Sale Completed!', style: TextStyle(fontWeight: FontWeight.w800, fontSize: 18, color: AppColors.success)),
            const SizedBox(height: 20),
            // Receipt box
            Container(
              decoration: BoxDecoration(color: AppColors.bg, borderRadius: BorderRadius.circular(16), border: Border.all(color: AppColors.border)),
              padding: const EdgeInsets.all(20),
              child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                // Business
                Center(child: Text(bizName, style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 16))),
                const Center(child: Text('MannaPOS Receipt', style: TextStyle(color: AppColors.textSec, fontSize: 12))),
                const Divider(height: 24),
                // Ref + date
                _row('Reference:', ref),
                _row('Date:', date),
                _row('Payment:', payMethod),
                const Divider(height: 24),
                // Items
                const Text('Items', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 14)),
                const SizedBox(height: 8),
                ...items.map((item) => Padding(
                  padding: const EdgeInsets.only(bottom: 8),
                  child: Row(children: [
                    Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                      Text(item['product_name'] ?? '', style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13)),
                      Text('${item['quantity']} x TSh ${fmt.format(double.tryParse(item['unit_price']?.toString() ?? '0') ?? 0)}', style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
                    ])),
                    Text('TSh ${fmt.format(double.tryParse(item['total']?.toString() ?? '0') ?? 0)}', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 13)),
                  ]),
                )),
                const Divider(height: 24),
                // Totals
                _row('Total:', 'TSh ${fmt.format(total)}', bold: true),
                _row('Paid:', 'TSh ${fmt.format(paid)}'),
                if (paid > total) _row('Change:', 'TSh ${fmt.format(paid - total)}', color: AppColors.success),
              ]),
            ),
            const SizedBox(height: 24),
          ]),
        )),
        // Actions
        Padding(padding: const EdgeInsets.fromLTRB(20, 0, 20, 32), child: Column(children: [
          SizedBox(width: double.infinity, height: 52, child: ElevatedButton.icon(
            onPressed: () => Navigator.pop(context),
            icon: const Icon(Icons.check),
            label: const Text('Done', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700)),
          )),
        ])),
      ]),
    );
  }

  Widget _row(String label, String value, {bool bold = false, Color? color}) => Padding(
    padding: const EdgeInsets.only(bottom: 6),
    child: Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
      Text(label, style: const TextStyle(color: AppColors.textSec, fontSize: 13)),
      Text(value, style: TextStyle(fontWeight: bold ? FontWeight.w800 : FontWeight.w600, fontSize: 13, color: color ?? AppColors.textPri)),
    ]),
  );
}