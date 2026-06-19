import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/status_badge.dart';
import '../../widgets/search_bar_widget.dart';
import '../../widgets/shimmer_loading.dart';
import '../../widgets/empty_state.dart';
import '../../widgets/section_header.dart';
import '../../widgets/toast_helper.dart';
import '../../widgets/loading_overlay.dart';
import '../../providers/sale_provider.dart';
import '../../providers/product_provider.dart';
import '../../providers/customer_provider.dart';
import '../../models/product.dart';
import '../../models/customer.dart';

class AddDraftScreen extends StatefulWidget {
  const AddDraftScreen({super.key});
  @override State<AddDraftScreen> createState() => _AddDraftScreenState();
}

class _AddDraftScreenState extends State<AddDraftScreen> {
  final _searchCtrl = TextEditingController();
  final _notesCtrl = TextEditingController();
  List<Product> _products = [];
  List<Product> _filtered = [];
  final Map<int, CartItem> _cart = {};
  Customer? _selectedCustomer;
  bool _loading = true;
  bool _submitting = false;
  final _fmt = NumberFormat('#,##0');

  @override
  void initState() {
    super.initState();
    _load();
  }

  @override
  void dispose() {
    _searchCtrl.dispose();
    _notesCtrl.dispose();
    super.dispose();
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
          _filtered = _products;
          _loading = false;
        });
      }
    } catch (_) {
      if (mounted) setState(() => _loading = false);
    }
  }

  void _filter(String q) {
    setState(() {
      _filtered = _products.where((p) =>
        p.name.toLowerCase().contains(q.toLowerCase())).toList();
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

  void _removeFromCart(int id) => setState(() => _cart.remove(id));

  double get _total => _cart.values.fold(0, (s, i) => s + i.total);

  Future<void> _saveDraft() async {
    if (_cart.isEmpty) {
      ToastHelper.warning(context, 'Add at least one product');
      return;
    }
    setState(() => _submitting = true);
    try {
      final now = DateTime.now().toIso8601String().split('T')[0];
      await context.read<SaleProvider>().createSale({
        'sale_date': now,
        'status': 'draft',
        'payment_method': 'cash',
        'paid': 0,
        'discount': 0,
        'notes': _notesCtrl.text,
        'customer_id': _selectedCustomer?.id,
        'items': _cart.values.map((i) => ({
          'product_id': i.product.id,
          'quantity': i.qty,
          'unit_price': i.product.sellingPrice,
        })).toList(),
      });
      if (mounted) {
        ToastHelper.success(context, 'Draft saved');
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
        title: const Text('New Draft', style: TextStyle(fontWeight: FontWeight.w700)),
        actions: [
          TextButton(
            onPressed: _submitting ? null : _saveDraft,
            child: _submitting
                ? const SizedBox(width: 18, height: 18, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2))
                : const Text('Save Draft', style: TextStyle(color: Colors.white, fontWeight: FontWeight.w700)),
          ),
        ],
      ),
      body: _loading
          ? const ShimmerLoading(child: ShimmerCard())
          : SingleChildScrollView(
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  // Customer
                  SectionHeader(title: 'Customer'),
                  const SizedBox(height: 8),
                  InkWell(
                    onTap: () async {
                      final customers = context.read<CustomerProvider>().customers;
                      final result = await showModalBottomSheet<Customer>(
                        context: context,
                        isScrollControlled: true,
                        backgroundColor: Colors.transparent,
                        builder: (_) => _DraftCustomerPicker(customers: customers),
                      );
                      if (result != null) setState(() => _selectedCustomer = result);
                    },
                    child: GlassCard(
                      child: Container(
                        padding: const EdgeInsets.all(14),
                        child: Row(
                          children: [
                            Icon(Icons.person_outline, color: theme.colorScheme.primary),
                            const SizedBox(width: 12),
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
                            const Icon(Icons.chevron_down),
                          ],
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(height: 20),
                  // Products
                  SectionHeader(title: 'Add Products'),
                  const SizedBox(height: 8),
                  AppSearchBar(hint: 'Search products...', onChanged: _filter, controller: _searchCtrl),
                  const SizedBox(height: 8),
                  if (_filtered.isNotEmpty)
                    SizedBox(
                      height: 100,
                      child: ListView.separated(
                        scrollDirection: Axis.horizontal,
                        itemCount: _filtered.length,
                        separatorBuilder: (_, __) => const SizedBox(width: 8),
                        itemBuilder: (_, i) {
                          final p = _filtered[i];
                          final inCart = _cart.containsKey(p.id);
                          return GestureDetector(
                            onTap: () => _addToCart(p),
                            child: Container(
                              width: 90,
                              decoration: BoxDecoration(
                                color: theme.cardColor,
                                borderRadius: BorderRadius.circular(12),
                                border: Border.all(
                                  color: inCart ? theme.colorScheme.primary : theme.colorScheme.outline.withValues(alpha: 0.2),
                                ),
                              ),
                              child: Column(
                                mainAxisAlignment: MainAxisAlignment.center,
                                children: [
                                  Text(p.name, textAlign: TextAlign.center, maxLines: 2, overflow: TextOverflow.ellipsis,
                                    style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 12)),
                                  const SizedBox(height: 4),
                                  Text('TSh ${_fmt.format(p.sellingPrice)}',
                                    style: TextStyle(color: theme.colorScheme.primary, fontWeight: FontWeight.w800, fontSize: 12)),
                                  if (inCart)
                                    Container(
                                      margin: const EdgeInsets.only(top: 4),
                                      padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                                      decoration: BoxDecoration(color: theme.colorScheme.primary.withValues(alpha: 0.15), borderRadius: BorderRadius.circular(8)),
                                      child: Text('x${_cart[p.id]!.qty}',
                                        style: TextStyle(color: theme.colorScheme.primary, fontSize: 11, fontWeight: FontWeight.w800)),
                                    ),
                                ],
                              ),
                            ),
                          );
                        },
                      ),
                    ),
                  const SizedBox(height: 20),
                  // Cart
                  if (_cart.isNotEmpty) ...[
                    SectionHeader(title: 'Cart (${_cart.length} items)'),
                    const SizedBox(height: 8),
                    ..._cart.values.map((item) => GlassCard(
                      child: Padding(
                        padding: const EdgeInsets.all(12),
                        child: Row(
                          children: [
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(item.product.name, style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14)),
                                  Text('TSh ${_fmt.format(item.product.sellingPrice)} each',
                                    style: TextStyle(color: theme.colorScheme.onSurface.withValues(alpha: 0.5), fontSize: 12)),
                                ],
                              ),
                            ),
                            Text('TSh ${_fmt.format(item.total)}',
                              style: TextStyle(color: theme.colorScheme.primary, fontWeight: FontWeight.w800, fontSize: 14)),
                            const SizedBox(width: 8),
                            GestureDetector(
                              onTap: () => _removeFromCart(item.product.id),
                              child: Icon(Icons.close, size: 18, color: theme.colorScheme.error),
                            ),
                          ],
                        ),
                      ),
                    )),
                    const SizedBox(height: 12),
                    GlassCard(
                      child: Padding(
                        padding: const EdgeInsets.all(16),
                        child: Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            const Text('Total', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 16)),
                            Text('TSh ${_fmt.format(_total)}',
                              style: TextStyle(fontWeight: FontWeight.w800, fontSize: 18, color: theme.colorScheme.primary)),
                          ],
                        ),
                      ),
                    ),
                  ],
                  const SizedBox(height: 20),
                  // Notes
                  SectionHeader(title: 'Notes (Optional)'),
                  const SizedBox(height: 8),
                  TextField(
                    controller: _notesCtrl,
                    maxLines: 3,
                    decoration: const InputDecoration(hintText: 'Internal notes...'),
                  ),
                  const SizedBox(height: 32),
                ],
              ),
            ),
    );
  }
}

class _DraftCustomerPicker extends StatelessWidget {
  final List<Customer> customers;
  const _DraftCustomerPicker({required this.customers});

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    return Container(
      height: 300,
      decoration: BoxDecoration(
        color: theme.scaffoldBackgroundColor,
        borderRadius: const BorderRadius.vertical(top: Radius.circular(24)),
      ),
      child: Column(
        children: [
          Center(child: Container(
            margin: const EdgeInsets.only(top: 12),
            width: 40, height: 4,
            decoration: BoxDecoration(color: theme.colorScheme.outline.withValues(alpha: 0.3), borderRadius: BorderRadius.circular(4)))),
          Padding(
            padding: const EdgeInsets.all(20),
            child: Text('Select Customer', style: TextStyle(fontSize: 18, fontWeight: FontWeight.w700, color: theme.colorScheme.onSurface)),
          ),
          Expanded(
            child: ListView.separated(
              padding: const EdgeInsets.symmetric(horizontal: 20),
              itemCount: customers.length,
              separatorBuilder: (_, __) => const SizedBox(height: 6),
              itemBuilder: (_, i) {
                final c = customers[i];
                return ListTile(
                  leading: CircleAvatar(
                    backgroundColor: theme.colorScheme.primary.withValues(alpha: 0.15),
                    child: Text(c.initials, style: TextStyle(color: theme.colorScheme.primary, fontWeight: FontWeight.w800)),
                  ),
                  title: Text(c.name),
                  subtitle: Text(c.phone ?? ''),
                  onTap: () => Navigator.pop(context, c),
                );
              },
            ),
          ),
        ],
      ),
    );
  }
}
