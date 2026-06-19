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

class AddSaleScreen extends StatefulWidget {
  final Map<String, dynamic>? draftData;
  const AddSaleScreen({super.key, this.draftData});

  @override
  State<AddSaleScreen> createState() => _AddSaleScreenState();
}

class _AddSaleScreenState extends State<AddSaleScreen> {
  final _searchCtrl = TextEditingController();
  final _paidCtrl = TextEditingController();
  final _discountCtrl = TextEditingController();
  final _notesCtrl = TextEditingController();

  List<Product> _products = [];
  List<Product> _filteredProducts = [];
  List<Customer> _customers = [];
  final Map<int, CartItem> _cart = {};
  Customer? _selectedCustomer;
  String _payMethod = 'cash';
  String _discountType = 'percentage';
  bool _loading = true;
  bool _submitting = false;
  final _fmt = NumberFormat('#,##0');

  @override
  void initState() {
    super.initState();
    _load();
    if (widget.draftData != null) {
      _populateDraft(widget.draftData!);
    }
  }

  @override
  void dispose() {
    _searchCtrl.dispose();
    _paidCtrl.dispose();
    _discountCtrl.dispose();
    _notesCtrl.dispose();
    super.dispose();
  }

  void _populateDraft(Map<String, dynamic> draft) {
    if (draft['notes'] != null) _notesCtrl.text = draft['notes'];
  }

  Future<void> _load() async {
    setState(() => _loading = true);
    try {
      await Future.wait([
        context.read<ProductProvider>().fetchProducts(),
        context.read<CustomerProvider>().fetchCustomers(),
      ]);
      if (mounted) {
        setState(() {
          _products = context.read<ProductProvider>().products;
          _filteredProducts = _products;
          _customers = context.read<CustomerProvider>().customers;
          _loading = false;
        });
      }
    } catch (_) {
      if (mounted) setState(() => _loading = false);
    }
  }

  void _filterProducts(String q) {
    setState(() {
      _filteredProducts = _products.where((p) =>
        p.name.toLowerCase().contains(q.toLowerCase()) ||
        (p.sku ?? '').toLowerCase().contains(q.toLowerCase())).toList();
    });
  }

  void _addToCart(Product p) {
    setState(() {
      if (_cart.containsKey(p.id)) {
        _cart[p.id]!.qty++;
      } else {
        _cart[p.id] = CartItem(product: p);
      }
    });
  }

  void _updateQty(int id, int delta) {
    setState(() {
      if (!_cart.containsKey(id)) return;
      final newQty = _cart[id]!.qty + delta;
      if (newQty <= 0) { _cart.remove(id); return; }
      _cart[id]!.qty = newQty;
    });
  }

  void _removeFromCart(int id) => setState(() => _cart.remove(id));

  double get _subtotal => _cart.values.fold(0, (s, i) => s + i.total);
  double get _discountValue => double.tryParse(_discountCtrl.text) ?? 0;
  double get _discountAmount => _discountType == 'percentage'
      ? _subtotal * (_discountValue / 100)
      : _discountValue;
  double get _total => _subtotal - _discountAmount;
  double get _paid => double.tryParse(_paidCtrl.text) ?? 0;

  Future<void> _pickCustomer() async {
    final result = await showModalBottomSheet<Customer>(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (_) => _PickCustomerSheet(customers: _customers),
    );
    if (result != null) setState(() => _selectedCustomer = result);
  }

  Future<void> _submit() async {
    if (_cart.isEmpty) {
      ToastHelper.warning(context, 'Add at least one product');
      return;
    }
    if (_paid < _total) {
      ToastHelper.warning(context, 'Amount paid must equal or exceed total');
      return;
    }
    setState(() => _submitting = true);
    try {
      final now = DateTime.now().toIso8601String().split('T')[0];
      await context.read<SaleProvider>().createSale({
        'sale_date': now,
        'status': 'completed',
        'payment_method': _payMethod,
        'paid': _paid,
        'discount': _discountAmount,
        'notes': _notesCtrl.text,
        'customer_id': _selectedCustomer?.id,
        'items': _cart.values.map((i) => ({
          'product_id': i.product.id,
          'quantity': i.qty,
          'unit_price': i.product.sellingPrice,
        })).toList(),
      });
      if (mounted) {
        ToastHelper.success(context, 'Sale created successfully');
        Navigator.pop(context, true);
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
        backgroundColor: theme.colorScheme.primary,
        foregroundColor: Colors.white,
        elevation: 0,
        title: const Text('New Sale', style: TextStyle(fontWeight: FontWeight.w700)),
      ),
      body: _loading
          ? const ShimmerLoading(child: ShimmerCard())
          : _buildForm(theme),
    );
  }

  Widget _buildForm(ThemeData theme) {
    return SingleChildScrollView(
      padding: const EdgeInsets.all(16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          // Customer picker
          SectionHeader(title: 'Customer'),
          const SizedBox(height: 8),
          InkWell(
            onTap: _pickCustomer,
            child: GlassCard(
              child: Container(
                padding: const EdgeInsets.all(14),
                child: Row(
                  children: [
                    Icon(Icons.person_outline, color: theme.colorScheme.primary),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            _selectedCustomer?.name ?? 'Select Customer',
                            style: TextStyle(
                              fontWeight: _selectedCustomer != null ? FontWeight.w700 : FontWeight.w400,
                              color: _selectedCustomer != null
                                  ? theme.colorScheme.onSurface
                                  : theme.colorScheme.onSurface.withValues(alpha: 0.5),
                            ),
                          ),
                          if (_selectedCustomer != null)
                            Text(_selectedCustomer!.phone ?? '',
                              style: TextStyle(
                                color: theme.colorScheme.onSurface.withValues(alpha: 0.6),
                                fontSize: 12)),
                        ],
                      ),
                    ),
                    const Icon(Icons.chevron_down),
                  ],
                ),
              ),
            ),
          ),
          const SizedBox(height: 20),
          // Product search + add
          SectionHeader(title: 'Add Products'),
          const SizedBox(height: 8),
          AppSearchBar(
            hint: 'Search products...',
            onChanged: _filterProducts,
            controller: _searchCtrl,
          ),
          const SizedBox(height: 8),
          if (_filteredProducts.isNotEmpty)
            SizedBox(
              height: 120,
              child: ListView.separated(
                scrollDirection: Axis.horizontal,
                itemCount: _filteredProducts.length,
                separatorBuilder: (_, __) => const SizedBox(width: 8),
                itemBuilder: (_, i) {
                  final p = _filteredProducts[i];
                  return GestureDetector(
                    onTap: () => _addToCart(p),
                    child: Container(
                      width: 100,
                      decoration: BoxDecoration(
                        color: theme.cardColor,
                        borderRadius: BorderRadius.circular(12),
                        border: Border.all(
                          color: _cart.containsKey(p.id)
                              ? theme.colorScheme.primary
                              : theme.colorScheme.outline.withValues(alpha: 0.2),
                        ),
                      ),
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Text(p.name, textAlign: TextAlign.center,
                            maxLines: 2, overflow: TextOverflow.ellipsis,
                            style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 12)),
                          const SizedBox(height: 4),
                          Text('TSh ${_fmt.format(p.sellingPrice)}',
                            style: TextStyle(
                              color: theme.colorScheme.primary,
                              fontWeight: FontWeight.w800, fontSize: 13)),
                          if (_cart.containsKey(p.id))
                            Container(
                              margin: const EdgeInsets.only(top: 4),
                              padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                              decoration: BoxDecoration(
                                color: theme.colorScheme.primary.withValues(alpha: 0.15),
                                borderRadius: BorderRadius.circular(8),
                              ),
                              child: Text('x${_cart[p.id]!.qty}',
                                style: TextStyle(
                                  color: theme.colorScheme.primary,
                                  fontSize: 11, fontWeight: FontWeight.w800)),
                            ),
                        ],
                      ),
                    ),
                  );
                },
              ),
            ),
          const SizedBox(height: 20),
          // Cart items
          if (_cart.isNotEmpty) ...[
            SectionHeader(title: 'Cart (${_cart.length} items)'),
            const SizedBox(height: 8),
            ..._cart.values.map((item) => _cartItemCard(item, theme)),
            const SizedBox(height: 20),
          ],
          // Discount
          SectionHeader(title: 'Discount'),
          const SizedBox(height: 8),
          Row(
            children: [
              Expanded(
                child: _discountType == 'percentage'
                    ? _buildDiscountChip('percentage', '%', theme)
                    : GestureDetector(
                        onTap: () => setState(() => _discountType = 'percentage'),
                        child: Container(
                          padding: const EdgeInsets.symmetric(vertical: 10),
                          decoration: BoxDecoration(
                            borderRadius: BorderRadius.circular(8),
                            border: Border.all(color: theme.colorScheme.outline.withValues(alpha: 0.3)),
                          ),
                          child: const Text('Percentage',
                            textAlign: TextAlign.center,
                            style: TextStyle(fontSize: 12, color: Colors.grey)),
                        ),
                      ),
              ),
              const SizedBox(width: 8),
              Expanded(
                child: _discountType == 'fixed'
                    ? _buildDiscountChip('fixed', 'Fixed', theme)
                    : GestureDetector(
                        onTap: () => setState(() => _discountType = 'fixed'),
                        child: Container(
                          padding: const EdgeInsets.symmetric(vertical: 10),
                          decoration: BoxDecoration(
                            borderRadius: BorderRadius.circular(8),
                            border: Border.all(color: theme.colorScheme.outline.withValues(alpha: 0.3)),
                          ),
                          child: const Text('Fixed Amount',
                            textAlign: TextAlign.center,
                            style: TextStyle(fontSize: 12, color: Colors.grey)),
                        ),
                      ),
              ),
            ],
          ),
          const SizedBox(height: 8),
          TextField(
            controller: _discountCtrl,
            keyboardType: TextInputType.number,
            onChanged: (_) => setState(() {}),
            decoration: InputDecoration(
              labelText: _discountType == 'percentage' ? 'Discount %' : 'Discount Amount (TSh)',
              suffixText: _discountType == 'percentage' ? '%' : 'TSh',
            ),
          ),
          const SizedBox(height: 20),
          // Payment method
          SectionHeader(title: 'Payment Method'),
          const SizedBox(height: 8),
          Row(
            children: [
              _payMethodChip('cash', Icons.payments_outlined, 'Cash', theme),
              const SizedBox(width: 8),
              _payMethodChip('card', Icons.credit_card, 'Card', theme),
              const SizedBox(width: 8),
              _payMethodChip('mobile', Icons.phone_android, 'Mobile', theme),
              const SizedBox(width: 8),
              _payMethodChip('credit', Icons.account_balance, 'Credit', theme),
            ],
          ),
          const SizedBox(height: 12),
          TextField(
            controller: _paidCtrl,
            keyboardType: TextInputType.number,
            decoration: InputDecoration(
              labelText: 'Amount Paid',
              prefixText: 'TSh ',
            ),
          ),
          const SizedBox(height: 20),
          // Notes
          SectionHeader(title: 'Notes (Optional)'),
          const SizedBox(height: 8),
          TextField(
            controller: _notesCtrl,
            maxLines: 3,
            decoration: const InputDecoration(
              hintText: 'Add notes...',
              border: OutlineInputBorder(),
            ),
          ),
          const SizedBox(height: 24),
          // Summary
          GlassCard(
            child: Padding(
              padding: const EdgeInsets.all(16),
              child: Column(
                children: [
                  _summaryRow('Subtotal', 'TSh ${_fmt.format(_subtotal)}'),
                  _summaryRow('Discount', '-TSh ${_fmt.format(_discountAmount)}',
                    color: Colors.red),
                  const Divider(),
                  _summaryRow('Total', 'TSh ${_fmt.format(_total)}',
                    bold: true, color: theme.colorScheme.primary),
                  _summaryRow('Paid', 'TSh ${_fmt.format(_paid)}',
                    color: Colors.green),
                ],
              ),
            ),
          ),
          const SizedBox(height: 24),
          SizedBox(
            height: 54,
            child: ElevatedButton.icon(
              onPressed: _submitting ? null : _submit,
              icon: _submitting
                  ? const SizedBox(width: 20, height: 20,
                      child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5))
                  : const Icon(Icons.check_circle_outline),
              label: Text(
                _submitting
                    ? 'Processing...'
                    : 'Complete Sale \u00b7 TSh ${_fmt.format(_total)}',
                style: const TextStyle(fontSize: 15, fontWeight: FontWeight.w700)),
            ),
          ),
          const SizedBox(height: 32),
        ],
      ),
    );
  }

  Widget _buildDiscountChip(String type, String label, ThemeData theme) {
    return Container(
      padding: const EdgeInsets.symmetric(vertical: 10),
      decoration: BoxDecoration(
        color: theme.colorScheme.primary.withValues(alpha: 0.15),
        borderRadius: BorderRadius.circular(8),
        border: Border.all(color: theme.colorScheme.primary, width: 2),
      ),
      child: Text(label,
        textAlign: TextAlign.center,
        style: TextStyle(
          fontSize: 12, fontWeight: FontWeight.w700,
          color: theme.colorScheme.primary)),
    );
  }

  Widget _payMethodChip(String val, IconData icon, String label, ThemeData theme) {
    final sel = _payMethod == val;
    return Expanded(
      child: GestureDetector(
        onTap: () => setState(() => _payMethod = val),
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
                color: sel ? theme.colorScheme.primary : Colors.grey),
              const SizedBox(height: 3),
              Text(label,
                style: TextStyle(
                  fontSize: 11, fontWeight: FontWeight.w700,
                  color: sel ? theme.colorScheme.primary : Colors.grey)),
            ],
          ),
        ),
      ),
    );
  }

  Widget _cartItemCard(CartItem item, ThemeData theme) {
    return GlassCard(
      child: Padding(
        padding: const EdgeInsets.all(12),
        child: Row(
          children: [
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(item.product.name,
                    style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14)),
                  Text('TSh ${_fmt.format(item.product.sellingPrice)} each',
                    style: TextStyle(
                      color: theme.colorScheme.onSurface.withValues(alpha: 0.5),
                      fontSize: 12)),
                ],
              ),
            ),
            Row(
              children: [
                _qtyBtn(Icons.remove, () => _updateQty(item.product.id, -1), theme),
                Container(
                  width: 32, alignment: Alignment.center,
                  child: Text('${item.qty}',
                    style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 16))),
                _qtyBtn(Icons.add, () => _updateQty(item.product.id, 1), theme),
              ],
            ),
            const SizedBox(width: 8),
            Text('TSh ${_fmt.format(item.total)}',
              style: TextStyle(
                color: theme.colorScheme.primary,
                fontWeight: FontWeight.w800, fontSize: 14)),
            const SizedBox(width: 4),
            GestureDetector(
              onTap: () => _removeFromCart(item.product.id),
              child: Icon(Icons.close, size: 18,
                color: theme.colorScheme.error)),
          ],
        ),
      ),
    );
  }

  Widget _qtyBtn(IconData icon, VoidCallback onTap, ThemeData theme) {
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

  Widget _summaryRow(String label, String value, {bool bold = false, Color? color}) {
    final theme = Theme.of(context);
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 4),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(label,
            style: TextStyle(
              fontWeight: bold ? FontWeight.w700 : FontWeight.w500,
              color: theme.colorScheme.onSurface.withValues(alpha: 0.6))),
          Text(value,
            style: TextStyle(
              fontWeight: bold ? FontWeight.w800 : FontWeight.w600,
              color: color ?? theme.colorScheme.onSurface)),
        ],
      ),
    );
  }
}

class _PickCustomerSheet extends StatefulWidget {
  final List<Customer> customers;
  const _PickCustomerSheet({required this.customers});
  @override State<_PickCustomerSheet> createState() => _PickCustomerSheetState();
}

class _PickCustomerSheetState extends State<_PickCustomerSheet> {
  String _q = '';
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
          Center(child: Container(
            margin: const EdgeInsets.only(top: 12),
            width: 40, height: 4,
            decoration: BoxDecoration(
              color: theme.colorScheme.outline.withValues(alpha: 0.3),
              borderRadius: BorderRadius.circular(4)))),
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
                    final result = await Navigator.push<String>(
                      context,
                      MaterialPageRoute(builder: (_) => const _QuickAddCustomer()),
                    );
                    if (result != null && mounted) {
                      await context.read<CustomerProvider>().fetchCustomers();
                      setState(() {
                        _filtered = context.read<CustomerProvider>().customers;
                      });
                    }
                  },
                  child: const Text('+ Add'),
                ),
              ],
            ),
          ),
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 20),
            child: TextField(
              decoration: const InputDecoration(
                hintText: 'Search...',
                prefixIcon: Icon(Icons.search),
              ),
              onChanged: (v) {
                setState(() {
                  _q = v;
                  _filtered = widget.customers.where((c) =>
                    c.name.toLowerCase().contains(v.toLowerCase())).toList();
                });
              },
            ),
          ),
          const SizedBox(height: 8),
          Expanded(
            child: _filtered.isEmpty
                ? const EmptyState(icon: Icons.people_outline, title: 'No Customers')
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

class _QuickAddCustomer extends StatefulWidget {
  const _QuickAddCustomer();
  @override State<_QuickAddCustomer> createState() => _QuickAddCustomerState();
}

class _QuickAddCustomerState extends State<_QuickAddCustomer> {
  final _nameCtrl = TextEditingController();
  final _phoneCtrl = TextEditingController();
  bool _busy = false;

  @override
  void dispose() {
    _nameCtrl.dispose();
    _phoneCtrl.dispose();
    super.dispose();
  }

  Future<void> _save() async {
    if (_nameCtrl.text.isEmpty) {
      ToastHelper.warning(context, 'Name required');
      return;
    }
    setState(() => _busy = true);
    try {
      await context.read<CustomerProvider>().createCustomer({
        'name': _nameCtrl.text,
        'phone': _phoneCtrl.text,
      });
      if (mounted) {
        ToastHelper.success(context, 'Customer added');
        Navigator.pop(context, _nameCtrl.text);
      }
    } catch (e) {
      if (mounted) ToastHelper.error(context, 'Failed: $e');
    } finally {
      if (mounted) setState(() => _busy = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Add Customer')),
      body: Padding(
        padding: const EdgeInsets.all(20),
        child: Column(
          children: [
            TextField(controller: _nameCtrl,
              decoration: const InputDecoration(labelText: 'Name *')),
            const SizedBox(height: 12),
            TextField(controller: _phoneCtrl,
              keyboardType: TextInputType.phone,
              decoration: const InputDecoration(labelText: 'Phone')),
            const SizedBox(height: 24),
            ElevatedButton(
              onPressed: _busy ? null : _save,
              style: ElevatedButton.styleFrom(minimumSize: const Size(double.infinity, 52)),
              child: _busy
                  ? const SizedBox(width: 20, height: 20,
                      child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2))
                  : const Text('Save', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700)),
            ),
          ],
        ),
      ),
    );
  }
}
