import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/status_badge.dart';
import '../../widgets/search_bar_widget.dart';
import '../../widgets/shimmer_loading.dart';
import '../../widgets/empty_state.dart';
import '../../widgets/section_header.dart';
import '../../widgets/confirm_dialog.dart';
import '../../widgets/toast_helper.dart';
import '../../widgets/app_bottom_sheet.dart';
import '../../widgets/loading_overlay.dart';
import '../../providers/sale_provider.dart';
import '../../providers/product_provider.dart';
import '../../providers/customer_provider.dart';
import '../../models/sale.dart';
import '../../models/product.dart';
import '../../models/customer.dart';

class PosScreen extends StatefulWidget {
  const PosScreen({super.key});
  @override State<PosScreen> createState() => _PosScreenState();
}

class _PosScreenState extends State<PosScreen> with SingleTickerProviderStateMixin {
  final _searchCtrl = TextEditingController();
  final _paidCtrl = TextEditingController();
  final _mobilePhoneCtrl = TextEditingController();
  final _mobileRefCtrl = TextEditingController();
  final _mobileNetworkCtrl = TextEditingController();
  late TabController _tabCtrl;

  List<Product> _products = [];
  List<Product> _filtered = [];
  String _selectedCategory = 'All';
  List<String> _categories = ['All'];
  bool _loading = true;
  bool _processing = false;
  String _payMethod = 'cash';
  String _mobileNetwork = 'Airtel';
  Customer? _selectedCustomer;

  final Map<int, CartItem> _cart = {};
  final _fmt = NumberFormat('#,##0');

  @override
  void initState() {
    super.initState();
    _tabCtrl = TabController(length: 2, vsync: this);
    _load();
  }

  @override
  void dispose() {
    _searchCtrl.dispose();
    _paidCtrl.dispose();
    _mobilePhoneCtrl.dispose();
    _mobileRefCtrl.dispose();
    _mobileNetworkCtrl.dispose();
    _tabCtrl.dispose();
    super.dispose();
  }

  Future<void> _load() async {
    setState(() => _loading = true);
    try {
      await context.read<ProductProvider>().fetchProducts();
      final products = context.read<ProductProvider>().products;
      final cats = {'All', ...products.map((p) => p.categoryName)};
      setState(() {
        _products = products;
        _filtered = products;
        _categories = cats.toList()..sort();
        _loading = false;
      });
    } catch (_) {
      setState(() => _loading = false);
      if (mounted) ToastHelper.error(context, 'Failed to load products');
    }
  }

  void _filter(String q) {
    setState(() {
      _filtered = _products.where((p) {
        final matchesSearch = q.isEmpty ||
            p.name.toLowerCase().contains(q.toLowerCase()) ||
            (p.sku ?? '').toLowerCase().contains(q.toLowerCase());
        final matchesCategory = _selectedCategory == 'All' || p.categoryName == _selectedCategory;
        return matchesSearch && matchesCategory;
      }).toList();
    });
  }

  void _addToCart(Product p) {
    if (p.isOutOfStock) {
      ToastHelper.warning(context, '${p.name} is out of stock');
      return;
    }
    setState(() {
      if (_cart.containsKey(p.id)) {
        if (_cart[p.id]!.qty >= p.stockQuantity) {
          ToastHelper.warning(context, 'Max stock reached');
          return;
        }
        _cart[p.id]!.qty++;
      } else {
        _cart[p.id] = CartItem(product: p);
      }
    });
    if (_cart.length == 1) {
      Future.delayed(const Duration(milliseconds: 300), () {
        if (mounted) _tabCtrl.animateTo(1);
      });
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
  double get _total => _subtotal;
  double get _paid => double.tryParse(_paidCtrl.text) ?? 0;
  double get _change => _paid - _total;
  int get _itemCount => _cart.values.fold(0, (s, i) => s + i.qty);

  Future<void> _selectCustomer() async {
    try {
      await context.read<CustomerProvider>().fetchCustomers();
      if (!mounted) return;
      final customers = context.read<CustomerProvider>().customers;
      final result = await showModalBottomSheet<Customer>(
        context: context,
        isScrollControlled: true,
        backgroundColor: Colors.transparent,
        builder: (_) => _CustomerPickerSheet(customers: customers),
      );
      if (result != null) setState(() => _selectedCustomer = result);
    } catch (_) {
      ToastHelper.error(context, 'Failed to load customers');
    }
  }

  Future<void> _completeSale() async {
    if (_cart.isEmpty) { ToastHelper.warning(context, 'Cart is empty'); return; }

    if (_payMethod == 'cash') {
      if (_paid < _total) {
        ToastHelper.warning(context, 'Insufficient payment');
        return;
      }
    } else if (_payMethod == 'mobile') {
      if (_mobilePhoneCtrl.text.isEmpty || _mobileRefCtrl.text.isEmpty) {
        ToastHelper.warning(context, 'Fill in mobile payment details');
        return;
      }
    }

    setState(() => _processing = true);
    try {
      final now = DateTime.now().toIso8601String().split('T')[0];
      final saleData = {
        'sale_date': now,
        'status': 'completed',
        'payment_method': _payMethod,
        'paid': _payMethod == 'cash' ? _paid : _total,
        'discount': 0,
        'notes': null,
        'customer_id': _selectedCustomer?.id,
        'items': _cart.values.map((i) => {
          'product_id': i.product.id,
          'quantity': i.qty,
          'unit_price': i.product.sellingPrice,
        }).toList(),
      };
      await context.read<SaleProvider>().createSale(saleData);
      if (mounted) {
        setState(() => _processing = false);
        await _showReceipt();
        setState(() {
          _cart.clear();
          _paidCtrl.clear();
          _mobilePhoneCtrl.clear();
          _mobileRefCtrl.clear();
          _selectedCustomer = null;
          _tabCtrl.animateTo(0);
        });
        _load();
      }
    } catch (e) {
      setState(() => _processing = false);
      if (mounted) ToastHelper.error(context, 'Sale failed: $e');
    }
  }

  Future<void> _showReceipt() async {
    await showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (_) => _ReceiptSheet(
        items: _cart.values.toList(),
        total: _total,
        paid: _payMethod == 'cash' ? _paid : _total,
        payMethod: _payMethod,
        customerName: _selectedCustomer?.name ?? 'Walk-in',
        reference: 'POS-${DateTime.now().millisecondsSinceEpoch}',
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    return Scaffold(
      backgroundColor: theme.scaffoldBackgroundColor,
      appBar: AppBar(
        backgroundColor: theme.colorScheme.primary,
        foregroundColor: Colors.white,
        elevation: 0,
        title: const Text('Point of Sale', style: TextStyle(fontWeight: FontWeight.w700)),
        leading: IconButton(icon: const Icon(Icons.menu), onPressed: () {}),
        actions: [
          if (_itemCount > 0)
            Padding(
              padding: const EdgeInsets.only(right: 8),
              child: Stack(
                children: [
                  IconButton(
                    icon: const Icon(Icons.shopping_cart_outlined),
                    onPressed: () => _tabCtrl.animateTo(1),
                  ),
                  Positioned(
                    top: 6, right: 6,
                    child: Container(
                      width: 18, height: 18,
                      decoration: BoxDecoration(color: theme.colorScheme.error, shape: BoxShape.circle),
                      child: Center(
                        child: Text('$_itemCount',
                          style: const TextStyle(color: Colors.white, fontSize: 10, fontWeight: FontWeight.w800)),
                      ),
                    ),
                  ),
                ],
              ),
            ),
          IconButton(icon: const Icon(Icons.refresh), onPressed: _load),
        ],
        bottom: TabBar(
          controller: _tabCtrl,
          indicatorColor: Colors.white,
          labelColor: Colors.white,
          unselectedLabelColor: Colors.white70,
          tabs: [
            const Tab(icon: Icon(Icons.grid_view, size: 18), text: 'Products'),
            Tab(
              icon: const Icon(Icons.shopping_cart, size: 18),
              text: _cart.isEmpty ? 'Cart' : 'Cart ($_itemCount)',
            ),
          ],
        ),
      ),
      body: TabBarView(
        controller: _tabCtrl,
        children: [_productsTab(), _cartTab()],
      ),
    );
  }

  Widget _productsTab() {
    return Column(
      children: [
        Container(
          color: Theme.of(context).cardColor,
          padding: const EdgeInsets.fromLTRB(12, 12, 12, 0),
          child: AppSearchBar(
            hint: 'Search products or SKU...',
            onChanged: _filter,
            controller: _searchCtrl,
          ),
        ),
        Container(
          color: Theme.of(context).cardColor,
          padding: const EdgeInsets.fromLTRB(12, 8, 12, 12),
          child: FilterChipRow(
            items: _categories,
            selected: _selectedCategory,
            onSelected: (cat) {
              setState(() => _selectedCategory = cat);
              _filter(_searchCtrl.text);
            },
          ),
        ),
        Expanded(
          child: _loading
              ? const ShimmerLoading(child: ShimmerCard())
              : _filtered.isEmpty
                  ? EmptyState(
                      icon: Icons.inventory_2_outlined,
                      title: 'No Products Found',
                      subtitle: 'Try a different search or category',
                    )
                  : RefreshIndicator(
                      onRefresh: _load,
                      child: GridView.builder(
                        padding: const EdgeInsets.all(12),
                        gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                          crossAxisCount: 2,
                          crossAxisSpacing: 10,
                          mainAxisSpacing: 10,
                          childAspectRatio: 0.78,
                        ),
                        itemCount: _filtered.length,
                        itemBuilder: (_, i) => _productCard(_filtered[i]),
                      ),
                    ),
        ),
      ],
    );
  }

  Widget _productCard(Product p) {
    final inCart = _cart.containsKey(p.id);
    final cartQty = inCart ? _cart[p.id]!.qty : 0;
    final theme = Theme.of(context);
    return GestureDetector(
      onTap: () => _addToCart(p),
      child: GlassCard(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            ClipRRect(
              borderRadius: const BorderRadius.vertical(top: Radius.circular(12)),
              child: Container(
                height: 90,
                width: double.infinity,
                color: theme.colorScheme.primary.withValues(alpha: 0.1),
                child: p.imageUrl != null
                    ? Image.network(p.imageUrl!, fit: BoxFit.cover,
                        errorBuilder: (_, __, ___) => _imagePlaceholder(p))
                    : _imagePlaceholder(p),
              ),
            ),
            Padding(
              padding: const EdgeInsets.all(10),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(p.name,
                    style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 13),
                    maxLines: 2, overflow: TextOverflow.ellipsis),
                  const SizedBox(height: 4),
                  Text('TSh ${_fmt.format(p.sellingPrice)}',
                    style: TextStyle(
                      color: theme.colorScheme.primary,
                      fontWeight: FontWeight.w800, fontSize: 14)),
                  const SizedBox(height: 4),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text(
                        p.isOutOfStock
                            ? 'Out of stock'
                            : '${p.stockQuantity} pcs',
                        style: TextStyle(
                          color: p.isOutOfStock
                              ? theme.colorScheme.error
                              : p.isLowStock
                                  ? Colors.orange
                                  : theme.colorScheme.onSurface.withValues(alpha: 0.6),
                          fontSize: 11,
                          fontWeight: FontWeight.w500,
                        ),
                      ),
                      if (inCart)
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                          decoration: BoxDecoration(
                            color: theme.colorScheme.primary.withValues(alpha: 0.15),
                            borderRadius: BorderRadius.circular(8),
                          ),
                          child: Text('x$cartQty',
                            style: TextStyle(
                              color: theme.colorScheme.primary,
                              fontSize: 11,
                              fontWeight: FontWeight.w800)),
                        ),
                    ],
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _imagePlaceholder(Product p) {
    final theme = Theme.of(context);
    return Container(
      color: theme.colorScheme.primary.withValues(alpha: 0.1),
      child: Center(
        child: Text(
          p.name.substring(0, p.name.length > 2 ? 2 : 1).toUpperCase(),
          style: TextStyle(
            color: theme.colorScheme.primary,
            fontWeight: FontWeight.w800,
            fontSize: 22,
          ),
        ),
      ),
    );
  }

  Widget _cartTab() {
    final items = _cart.values.toList();
    final theme = Theme.of(context);

    if (items.isEmpty) {
      return Center(
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(Icons.shopping_cart_outlined, size: 64,
              color: theme.colorScheme.onSurface.withValues(alpha: 0.4)),
            const SizedBox(height: 12),
            Text('Cart is empty',
              style: TextStyle(
                fontWeight: FontWeight.w700, fontSize: 16,
                color: theme.colorScheme.onSurface.withValues(alpha: 0.6))),
            const SizedBox(height: 8),
            Text('Tap a product to add it here',
              style: TextStyle(
                color: theme.colorScheme.onSurface.withValues(alpha: 0.4),
                fontSize: 13)),
            const SizedBox(height: 24),
            ElevatedButton(
              onPressed: () => _tabCtrl.animateTo(0),
              child: const Text('Browse Products'),
            ),
          ],
        ),
      );
    }

    return Column(
      children: [
        Expanded(
          child: ListView.separated(
            padding: const EdgeInsets.fromLTRB(16, 12, 16, 0),
            itemCount: items.length,
            separatorBuilder: (_, __) => const SizedBox(height: 8),
            itemBuilder: (_, i) => _cartItemWidget(items[i]),
          ),
        ),
        // Bottom cart panel
        GlassCard(
          child: Padding(
            padding: const EdgeInsets.fromLTRB(20, 20, 20, 32),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                // Customer selector
                InkWell(
                  onTap: _selectCustomer,
                  child: Container(
                    padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                    decoration: BoxDecoration(
                      borderRadius: BorderRadius.circular(10),
                      border: Border.all(color: theme.colorScheme.outline.withValues(alpha: 0.3)),
                    ),
                    child: Row(
                      children: [
                        Icon(Icons.person_outline, size: 18,
                          color: theme.colorScheme.onSurface.withValues(alpha: 0.5)),
                        const SizedBox(width: 8),
                        Expanded(
                          child: Text(
                            _selectedCustomer?.name ?? 'Walk-in Customer',
                            style: TextStyle(
                              fontWeight: _selectedCustomer != null ? FontWeight.w700 : FontWeight.w400,
                              color: _selectedCustomer != null
                                  ? theme.colorScheme.onSurface
                                  : theme.colorScheme.onSurface.withValues(alpha: 0.5),
                            ),
                          ),
                        ),
                        Icon(Icons.chevron_down, size: 18,
                          color: theme.colorScheme.onSurface.withValues(alpha: 0.5)),
                      ],
                    ),
                  ),
                ),
                const SizedBox(height: 12),
                // Totals
                _totalRow('Subtotal', _subtotal),
                const Divider(height: 20),
                _totalRow('Total', _total, bold: true, large: true),
                const SizedBox(height: 16),
                // Payment method selector
                Row(
                  children: [
                    Expanded(child: _payChip('cash', Icons.payments_outlined, 'Cash')),
                    const SizedBox(width: 8),
                    Expanded(child: _payChip('card', Icons.credit_card, 'Card')),
                    const SizedBox(width: 8),
                    Expanded(child: _payChip('mobile', Icons.phone_android, 'Mobile')),
                    const SizedBox(width: 8),
                    Expanded(child: _payChip('credit', Icons.account_balance, 'Credit')),
                  ],
                ),
                // Payment details
                if (_payMethod == 'cash') ...[
                  const SizedBox(height: 12),
                  TextField(
                    controller: _paidCtrl,
                    keyboardType: TextInputType.number,
                    onChanged: (_) => setState(() {}),
                    decoration: InputDecoration(
                      labelText: 'Amount Paid',
                      prefixText: 'TSh ',
                      helperText: _paid >= _total && _paidCtrl.text.isNotEmpty
                          ? 'Change: TSh ${_fmt.format(_change)}'
                          : null,
                      helperStyle: const TextStyle(color: Colors.green, fontWeight: FontWeight.w700),
                      errorText: _paidCtrl.text.isNotEmpty && _paid < _total
                          ? 'Insufficient (need TSh ${_fmt.format(_total - _paid)} more)'
                          : null,
                    ),
                  ),
                ],
                if (_payMethod == 'mobile') ...[
                  const SizedBox(height: 12),
                  Row(
                    children: ['Airtel', 'Vodacom', 'Tigo', 'Halotel'].map((n) {
                      final sel = _mobileNetwork == n;
                      return Expanded(
                        child: GestureDetector(
                          onTap: () => setState(() => _mobileNetwork = n),
                          child: AnimatedContainer(
                            duration: const Duration(milliseconds: 180),
                            padding: const EdgeInsets.symmetric(vertical: 8),
                            margin: const EdgeInsets.symmetric(horizontal: 2),
                            decoration: BoxDecoration(
                              color: sel
                                  ? theme.colorScheme.primary.withValues(alpha: 0.15)
                                  : Colors.white,
                              borderRadius: BorderRadius.circular(8),
                              border: Border.all(
                                color: sel ? theme.colorScheme.primary : Colors.grey.shade200,
                                width: sel ? 2 : 1,
                              ),
                            ),
                            child: Text(n,
                              textAlign: TextAlign.center,
                              style: TextStyle(
                                fontSize: 11,
                                fontWeight: FontWeight.w700,
                                color: sel ? theme.colorScheme.primary : Colors.grey,
                              )),
                          ),
                        ),
                      );
                    }).toList(),
                  ),
                  const SizedBox(height: 12),
                  TextField(
                    controller: _mobilePhoneCtrl,
                    keyboardType: TextInputType.phone,
                    decoration: const InputDecoration(
                      labelText: 'Phone Number',
                      prefixText: '+255 ',
                    ),
                  ),
                  const SizedBox(height: 8),
                  TextField(
                    controller: _mobileRefCtrl,
                    decoration: const InputDecoration(
                      labelText: 'Transaction Reference',
                    ),
                  ),
                  if (_mobileRefCtrl.text.isNotEmpty)
                    Padding(
                      padding: const EdgeInsets.only(top: 8),
                      child: Row(
                        children: [
                          const SizedBox(width: 16, height: 16,
                            child: CircularProgressIndicator(strokeWidth: 2)),
                          const SizedBox(width: 8),
                          Text('Processing payment...',
                            style: TextStyle(
                              color: theme.colorScheme.primary, fontSize: 12)),
                        ],
                      ),
                    ),
                ],
                const SizedBox(height: 16),
                AnimatedButton(
                  onPressed: _processing ? null : _completeSale,
                  loading: _processing,
                  child: Text(
                    _processing
                        ? 'Processing...'
                        : 'Complete Sale \u00b7 TSh ${_fmt.format(_total)}',
                    style: const TextStyle(fontSize: 15, fontWeight: FontWeight.w700),
                  ),
                ),
                const SizedBox(height: 8),
                TextButton(
                  onPressed: () {
                    setState(() => _cart.clear());
                    _tabCtrl.animateTo(0);
                  },
                  child: Text('Clear Cart',
                    style: TextStyle(color: theme.colorScheme.error)),
                ),
              ],
            ),
          ),
        ),
      ],
    );
  }

  Widget _cartItemWidget(CartItem item) {
    final theme = Theme.of(context);
    return GlassCard(
      child: Padding(
        padding: const EdgeInsets.all(12),
        child: Row(
          children: [
            ClipRRect(
              borderRadius: BorderRadius.circular(8),
              child: Container(
                width: 52, height: 52,
                color: theme.colorScheme.primary.withValues(alpha: 0.1),
                child: item.product.imageUrl != null
                    ? Image.network(item.product.imageUrl!, fit: BoxFit.cover,
                        errorBuilder: (_, __, ___) => _imgThumb(item.product))
                    : _imgThumb(item.product),
              ),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(item.product.name,
                    style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14),
                    maxLines: 1, overflow: TextOverflow.ellipsis),
                  Text('TSh ${_fmt.format(item.product.sellingPrice)} each',
                    style: TextStyle(
                      color: theme.colorScheme.onSurface.withValues(alpha: 0.5),
                      fontSize: 12)),
                  Text('TSh ${_fmt.format(item.total)}',
                    style: TextStyle(
                      color: theme.colorScheme.primary,
                      fontWeight: FontWeight.w800, fontSize: 14)),
                ],
              ),
            ),
            Column(
              children: [
                Row(
                  children: [
                    _qtyBtn(Icons.remove, () => _updateQty(item.product.id, -1)),
                    Container(
                      width: 32,
                      alignment: Alignment.center,
                      child: Text('${item.qty}',
                        style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 16)),
                    ),
                    _qtyBtn(Icons.add, () => _updateQty(item.product.id, 1)),
                  ],
                ),
                const SizedBox(height: 4),
                GestureDetector(
                  onTap: () => _removeFromCart(item.product.id),
                  child: Text('Remove',
                    style: TextStyle(
                      color: theme.colorScheme.error,
                      fontSize: 11,
                      fontWeight: FontWeight.w600)),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _imgThumb(Product p) {
    final theme = Theme.of(context);
    return Container(
      color: theme.colorScheme.primary.withValues(alpha: 0.1),
      child: Center(
        child: Text(p.name[0].toUpperCase(),
          style: TextStyle(
            color: theme.colorScheme.primary,
            fontWeight: FontWeight.w800,
            fontSize: 18)),
      ),
    );
  }

  Widget _qtyBtn(IconData icon, VoidCallback onTap) {
    final theme = Theme.of(context);
    return GestureDetector(
      onTap: onTap,
      child: Container(
        width: 28, height: 28,
        decoration: BoxDecoration(
          color: theme.colorScheme.surfaceVariant,
          borderRadius: BorderRadius.circular(8),
          border: Border.all(color: theme.colorScheme.outline.withValues(alpha: 0.3)),
        ),
        child: Icon(icon, size: 16, color: theme.colorScheme.onSurface),
      ),
    );
  }

  Widget _payChip(String val, IconData icon, String label) {
    final sel = _payMethod == val;
    final theme = Theme.of(context);
    return GestureDetector(
      onTap: () => setState(() { _payMethod = val; _paidCtrl.clear(); }),
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 180),
        padding: const EdgeInsets.symmetric(vertical: 10),
        decoration: BoxDecoration(
          color: sel ? theme.colorScheme.primary.withValues(alpha: 0.15) : Colors.white,
          borderRadius: BorderRadius.circular(10),
          border: Border.all(
            color: sel ? theme.colorScheme.primary : theme.colorScheme.outline.withValues(alpha: 0.3),
            width: sel ? 2 : 1,
          ),
        ),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(icon, size: 18,
              color: sel ? theme.colorScheme.primary : theme.colorScheme.onSurface.withValues(alpha: 0.5)),
            const SizedBox(height: 3),
            Text(label,
              style: TextStyle(
                fontSize: 11,
                fontWeight: FontWeight.w700,
                color: sel ? theme.colorScheme.primary : theme.colorScheme.onSurface.withValues(alpha: 0.5))),
          ],
        ),
      ),
    );
  }

  Widget _totalRow(String label, double amount, {bool bold = false, bool large = false}) {
    final theme = Theme.of(context);
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 2),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(label,
            style: TextStyle(
              fontWeight: bold ? FontWeight.w700 : FontWeight.w500,
              fontSize: large ? 16 : 14,
              color: bold ? theme.colorScheme.onSurface : theme.colorScheme.onSurface.withValues(alpha: 0.6))),
          Text('TSh ${_fmt.format(amount)}',
            style: TextStyle(
              fontWeight: bold ? FontWeight.w800 : FontWeight.w600,
              fontSize: large ? 18 : 14,
              color: bold ? theme.colorScheme.primary : theme.colorScheme.onSurface)),
        ],
      ),
    );
  }
}

class CartItem {
  final Product product;
  int qty;
  CartItem({required this.product, this.qty = 1});
  double get total => product.sellingPrice * qty;
}

// Customer picker bottom sheet
class _CustomerPickerSheet extends StatefulWidget {
  final List<Customer> customers;
  const _CustomerPickerSheet({required this.customers});
  @override State<_CustomerPickerSheet> createState() => _CustomerPickerSheetState();
}

class _CustomerPickerSheetState extends State<_CustomerPickerSheet> {
  String _search = '';
  late List<Customer> _filtered;

  @override
  void initState() {
    super.initState();
    _filtered = widget.customers;
  }

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    return Container(
      height: MediaQuery.of(context).size.height * 0.7,
      decoration: BoxDecoration(
        color: theme.scaffoldBackgroundColor,
        borderRadius: const BorderRadius.vertical(top: Radius.circular(24)),
      ),
      child: Column(
        children: [
          Center(
            child: Container(
              margin: const EdgeInsets.only(top: 12),
              width: 40, height: 4,
              decoration: BoxDecoration(
                color: theme.colorScheme.outline.withValues(alpha: 0.3),
                borderRadius: BorderRadius.circular(4))),
          ),
          Padding(
            padding: const EdgeInsets.fromLTRB(20, 16, 20, 8),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text('Select Customer',
                  style: TextStyle(
                    fontSize: 18, fontWeight: FontWeight.w700,
                    color: theme.colorScheme.onSurface)),
                TextButton(
                  onPressed: () async {
                    final newCust = await Navigator.push<String>(
                      context,
                      MaterialPageRoute(builder: (_) => const _AddCustomerSheet()),
                    );
                    if (newCust != null && mounted) {
                      ToastHelper.success(context, 'Customer added: $newCust');
                    }
                  },
                  child: const Text('+ Add New'),
                ),
              ],
            ),
          ),
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 20),
            child: TextField(
              decoration: const InputDecoration(
                hintText: 'Search customers...',
                prefixIcon: Icon(Icons.search),
              ),
              onChanged: (v) {
                setState(() {
                  _search = v;
                  _filtered = widget.customers.where((c) =>
                    c.name.toLowerCase().contains(v.toLowerCase()) ||
                    (c.phone ?? '').contains(v)).toList();
                });
              },
            ),
          ),
          const SizedBox(height: 8),
          Expanded(
            child: _filtered.isEmpty
                ? EmptyState(
                    icon: Icons.people_outline,
                    title: 'No Customers Found',
                  )
                : ListView.separated(
                    padding: const EdgeInsets.symmetric(horizontal: 20),
                    itemCount: _filtered.length,
                    separatorBuilder: (_, __) => const SizedBox(height: 6),
                    itemBuilder: (_, i) {
                      final c = _filtered[i];
                      return GlassCard(
                        child: ListTile(
                          leading: CircleAvatar(
                            backgroundColor: theme.colorScheme.primary.withValues(alpha: 0.15),
                            child: Text(c.initials,
                              style: TextStyle(
                                color: theme.colorScheme.primary,
                                fontWeight: FontWeight.w800)),
                          ),
                          title: Text(c.name,
                            style: const TextStyle(fontWeight: FontWeight.w600)),
                          subtitle: Text(c.phone ?? c.email ?? ''),
                          trailing: const Icon(Icons.chevron_right),
                          onTap: () => Navigator.pop(context, c),
                        ),
                      );
                    },
                  ),
          ),
        ],
      ),
    );
  }
}

class _AddCustomerSheet extends StatefulWidget {
  const _AddCustomerSheet();
  @override State<_AddCustomerSheet> createState() => _AddCustomerSheetState();
}

class _AddCustomerSheetState extends State<_AddCustomerSheet> {
  final _nameCtrl = TextEditingController();
  final _phoneCtrl = TextEditingController();
  final _emailCtrl = TextEditingController();
  bool _submitting = false;

  @override
  void dispose() {
    _nameCtrl.dispose();
    _phoneCtrl.dispose();
    _emailCtrl.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    if (_nameCtrl.text.isEmpty) {
      ToastHelper.warning(context, 'Customer name is required');
      return;
    }
    setState(() => _submitting = true);
    try {
      await context.read<CustomerProvider>().createCustomer({
        'name': _nameCtrl.text,
        'phone': _phoneCtrl.text,
        'email': _emailCtrl.text,
      });
      if (mounted) {
        ToastHelper.success(context, 'Customer created');
        Navigator.pop(context, _nameCtrl.text);
      }
    } catch (e) {
      if (mounted) ToastHelper.error(context, 'Failed: $e');
    } finally {
      if (mounted) setState(() => _submitting = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    return Scaffold(
      backgroundColor: theme.scaffoldBackgroundColor,
      appBar: AppBar(
        title: const Text('Add Customer'),
      ),
      body: Padding(
        padding: const EdgeInsets.all(20),
        child: Column(
          children: [
            TextField(
              controller: _nameCtrl,
              decoration: const InputDecoration(labelText: 'Customer Name *'),
            ),
            const SizedBox(height: 12),
            TextField(
              controller: _phoneCtrl,
              keyboardType: TextInputType.phone,
              decoration: const InputDecoration(labelText: 'Phone'),
            ),
            const SizedBox(height: 12),
            TextField(
              controller: _emailCtrl,
              keyboardType: TextInputType.emailAddress,
              decoration: const InputDecoration(labelText: 'Email'),
            ),
            const SizedBox(height: 24),
            ElevatedButton(
              onPressed: _submitting ? null : _submit,
              style: ElevatedButton.styleFrom(
                minimumSize: const Size(double.infinity, 52),
              ),
              child: _submitting
                  ? const SizedBox(width: 20, height: 20,
                      child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2))
                  : const Text('Save Customer',
                      style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700)),
            ),
          ],
        ),
      ),
    );
  }
}

// Receipt bottom sheet
class _ReceiptSheet extends StatelessWidget {
  final List<CartItem> items;
  final double total;
  final double paid;
  final String payMethod;
  final String customerName;
  final String reference;

  const _ReceiptSheet({
    required this.items,
    required this.total,
    required this.paid,
    required this.payMethod,
    required this.customerName,
    required this.reference,
  });

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    final fmt = NumberFormat('#,##0');
    final change = paid - total;
    return Container(
      height: MediaQuery.of(context).size.height * 0.85,
      decoration: BoxDecoration(
        color: theme.scaffoldBackgroundColor,
        borderRadius: const BorderRadius.vertical(top: Radius.circular(24)),
      ),
      child: Column(
        children: [
          Center(
            child: Container(
              margin: const EdgeInsets.only(top: 12),
              width: 40, height: 4,
              decoration: BoxDecoration(
                color: theme.colorScheme.outline.withValues(alpha: 0.3),
                borderRadius: BorderRadius.circular(4))),
          ),
          Expanded(
            child: SingleChildScrollView(
              padding: const EdgeInsets.all(24),
              child: Column(
                children: [
                  Container(
                    width: 64, height: 64,
                    decoration: BoxDecoration(
                      color: Colors.green.withValues(alpha: 0.15),
                      shape: BoxShape.circle),
                    child: const Icon(Icons.check_circle, color: Colors.green, size: 36),
                  ),
                  const SizedBox(height: 12),
                  const Text('Sale Completed!',
                    style: TextStyle(
                      fontWeight: FontWeight.w800, fontSize: 18, color: Colors.green)),
                  const SizedBox(height: 20),
                  GlassCard(
                    child: Padding(
                      padding: const EdgeInsets.all(20),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Center(
                            child: Column(
                              children: [
                                Text('MANNAPOS',
                                  style: TextStyle(
                                    fontWeight: FontWeight.w800, fontSize: 16,
                                    color: theme.colorScheme.primary)),
                                const SizedBox(height: 4),
                                const Text('Receipt',
                                  style: TextStyle(
                                    color: Colors.grey, fontSize: 12)),
                              ],
                            ),
                          ),
                          const Divider(height: 24),
                          _receiptRow('Reference:', reference),
                          _receiptRow('Customer:', customerName),
                          _receiptRow('Payment:', payMethod.toUpperCase()),
                          const Divider(height: 24),
                          const Text('Items',
                            style: TextStyle(
                              fontWeight: FontWeight.w700, fontSize: 14)),
                          const SizedBox(height: 8),
                          ...items.map((item) => Padding(
                            padding: const EdgeInsets.only(bottom: 8),
                            child: Row(
                              children: [
                                Expanded(
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      Text(item.product.name,
                                        style: const TextStyle(
                                          fontWeight: FontWeight.w600, fontSize: 13)),
                                      Text('${item.qty} x TSh ${fmt.format(item.product.sellingPrice)}',
                                        style: const TextStyle(
                                          color: Colors.grey, fontSize: 12)),
                                    ],
                                  ),
                                ),
                                Text('TSh ${fmt.format(item.total)}',
                                  style: const TextStyle(
                                    fontWeight: FontWeight.w700, fontSize: 13)),
                              ],
                            ),
                          )),
                          const Divider(height: 24),
                          _receiptRow('Total:', 'TSh ${fmt.format(total)}', bold: true),
                          _receiptRow('Paid:', 'TSh ${fmt.format(paid)}'),
                          if (change > 0)
                            _receiptRow('Change:', 'TSh ${fmt.format(change)}',
                              color: Colors.green),
                        ],
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ),
          Padding(
            padding: const EdgeInsets.fromLTRB(20, 0, 20, 32),
            child: SizedBox(
              width: double.infinity, height: 52,
              child: ElevatedButton.icon(
                onPressed: () => Navigator.pop(context),
                icon: const Icon(Icons.check),
                label: const Text('Done',
                  style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700)),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _receiptRow(String label, String value, {bool bold = false, Color? color}) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 6),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(label, style: const TextStyle(color: Colors.grey, fontSize: 13)),
          Text(value,
            style: TextStyle(
              fontWeight: bold ? FontWeight.w800 : FontWeight.w600,
              fontSize: 13,
              color: color ?? Theme.of(context).colorScheme.onSurface)),
        ],
      ),
    );
  }
}
