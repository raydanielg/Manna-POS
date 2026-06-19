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

class AddQuotationScreen extends StatefulWidget {
  const AddQuotationScreen({super.key});
  @override State<AddQuotationScreen> createState() => _AddQuotationScreenState();
}

class _AddQuotationScreenState extends State<AddQuotationScreen> {
  final _searchCtrl = TextEditingController();
  final _notesCtrl = TextEditingController();
  final _termsCtrl = TextEditingController();
  final _discountCtrl = TextEditingController();

  List<Product> _products = [];
  List<Product> _filtered = [];
  final Map<int, CartItem> _cart = {};
  Customer? _selectedCustomer;
  DateTime _validUntil = DateTime.now().add(const Duration(days: 30));
  String _discountType = 'percentage';
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
    _termsCtrl.dispose();
    _discountCtrl.dispose();
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
      if (_cart.containsKey(p.id)) { _cart[p.id]!.qty++; }
      else { _cart[p.id] = CartItem(product: p); }
    });
  }

  void _removeFromCart(int id) => setState(() => _cart.remove(id));

  double get _subtotal => _cart.values.fold(0, (s, i) => s + i.total);
  double get _discountValue => double.tryParse(_discountCtrl.text) ?? 0;
  double get _discountAmount => _discountType == 'percentage' ? _subtotal * (_discountValue / 100) : _discountValue;
  double get _total => _subtotal - _discountAmount;

  Future<void> _pickValidUntil() async {
    final date = await showDatePicker(
      context: context,
      initialDate: _validUntil,
      firstDate: DateTime.now(),
      lastDate: DateTime.now().add(const Duration(days: 365)),
    );
    if (date != null) setState(() => _validUntil = date);
  }

  Future<void> _submit() async {
    if (_cart.isEmpty) { ToastHelper.warning(context, 'Add at least one product'); return; }
    setState(() => _submitting = true);
    try {
      final now = DateTime.now().toIso8601String().split('T')[0];
      await context.read<SaleProvider>().createSale({
        'sale_date': now,
        'status': 'quotation',
        'payment_method': 'cash',
        'paid': 0,
        'discount': _discountAmount,
        'notes': _notesCtrl.text,
        'terms': _termsCtrl.text,
        'valid_until': _validUntil.toIso8601String().split('T')[0],
        'customer_id': _selectedCustomer?.id,
        'items': _cart.values.map((i) => ({
          'product_id': i.product.id,
          'quantity': i.qty,
          'unit_price': i.product.sellingPrice,
        })).toList(),
      });
      if (mounted) {
        ToastHelper.success(context, 'Quotation created');
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
        title: const Text('New Quotation', style: TextStyle(fontWeight: FontWeight.w700)),
      ),
      body: _loading
          ? const ShimmerLoading(child: ShimmerCard())
          : SingleChildScrollView(
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  SectionHeader(title: 'Customer'),
                  const SizedBox(height: 8),
                  InkWell(
                    onTap: () async {
                      final customers = context.read<CustomerProvider>().customers;
                      final result = await showModalBottomSheet<Customer>(
                        context: context,
                        isScrollControlled: true,
                        backgroundColor: Colors.transparent,
                        builder: (_) => _QuotationCustomerPicker(customers: customers),
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
                            Expanded(child: Text(_selectedCustomer?.name ?? 'Select Customer',
                              style: TextStyle(fontWeight: _selectedCustomer != null ? FontWeight.w700 : FontWeight.w400,
                                color: _selectedCustomer != null ? theme.colorScheme.onSurface : theme.colorScheme.onSurface.withValues(alpha: 0.5)))),
                            const Icon(Icons.chevron_down),
                          ],
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(height: 20),
                  SectionHeader(title: 'Valid Until'),
                  const SizedBox(height: 8),
                  InkWell(
                    onTap: _pickValidUntil,
                    child: GlassCard(
                      child: Container(
                        padding: const EdgeInsets.all(14),
                        child: Row(
                          children: [
                            Icon(Icons.calendar_today, color: theme.colorScheme.primary),
                            const SizedBox(width: 12),
                            Text(DateFormat('dd MMM yyyy').format(_validUntil),
                              style: const TextStyle(fontWeight: FontWeight.w600)),
                            const Spacer(),
                            const Icon(Icons.edit_calendar, size: 18),
                          ],
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(height: 20),
                  SectionHeader(title: 'Products'),
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
                                border: Border.all(color: inCart ? theme.colorScheme.primary : theme.colorScheme.outline.withValues(alpha: 0.2)),
                              ),
                              child: Column(
                                mainAxisAlignment: MainAxisAlignment.center,
                                children: [
                                  Text(p.name, textAlign: TextAlign.center, maxLines: 2, overflow: TextOverflow.ellipsis,
                                    style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 12)),
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
                  if (_cart.isNotEmpty) ...[
                    SectionHeader(title: 'Items (${_cart.length})'),
                    const SizedBox(height: 8),
                    ..._cart.values.map((item) => GlassCard(
                      child: Padding(
                        padding: const EdgeInsets.all(12),
                        child: Row(
                          children: [
                            Expanded(child: Text(item.product.name, style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14))),
                            Text('TSh ${_fmt.format(item.total)}',
                              style: TextStyle(color: theme.colorScheme.primary, fontWeight: FontWeight.w800, fontSize: 14)),
                            const SizedBox(width: 8),
                            GestureDetector(onTap: () => _removeFromCart(item.product.id),
                              child: Icon(Icons.close, size: 18, color: theme.colorScheme.error)),
                          ],
                        ),
                      ),
                    )),
                  ],
                  const SizedBox(height: 20),
                  SectionHeader(title: 'Discount'),
                  const SizedBox(height: 8),
                  Row(children: [
                    Expanded(child: _discountChip('percentage', '%', theme)),
                    const SizedBox(width: 8),
                    Expanded(child: _discountChip('fixed', 'Fixed', theme)),
                  ]),
                  const SizedBox(height: 8),
                  TextField(
                    controller: _discountCtrl,
                    keyboardType: TextInputType.number,
                    onChanged: (_) => setState(() {}),
                    decoration: InputDecoration(
                      labelText: _discountType == 'percentage' ? 'Discount %' : 'Discount Amount',
                      suffixText: _discountType == 'percentage' ? '%' : 'TSh',
                    ),
                  ),
                  const SizedBox(height: 20),
                  SectionHeader(title: 'Terms & Conditions'),
                  const SizedBox(height: 8),
                  TextField(
                    controller: _termsCtrl,
                    maxLines: 3,
                    decoration: const InputDecoration(hintText: 'Payment terms, delivery terms...'),
                  ),
                  const SizedBox(height: 12),
                  SectionHeader(title: 'Notes'),
                  const SizedBox(height: 8),
                  TextField(
                    controller: _notesCtrl,
                    maxLines: 2,
                    decoration: const InputDecoration(hintText: 'Internal notes...'),
                  ),
                  const SizedBox(height: 20),
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
                  const SizedBox(height: 24),
                  SizedBox(
                    height: 54,
                    child: ElevatedButton.icon(
                      onPressed: _submitting ? null : _submit,
                      icon: _submitting
                          ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5))
                          : const Icon(Icons.description_outlined),
                      label: Text(_submitting ? 'Creating...' : 'Create Quotation \u00b7 TSh ${_fmt.format(_total)}',
                        style: const TextStyle(fontSize: 15, fontWeight: FontWeight.w700)),
                    ),
                  ),
                  const SizedBox(height: 32),
                ],
              ),
            ),
    );
  }

  Widget _discountChip(String type, String label, ThemeData theme) {
    final sel = _discountType == type;
    return GestureDetector(
      onTap: () => setState(() => _discountType = type),
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 10),
        decoration: BoxDecoration(
          color: sel ? theme.colorScheme.primary.withValues(alpha: 0.15) : Colors.white,
          borderRadius: BorderRadius.circular(8),
          border: Border.all(color: sel ? theme.colorScheme.primary : theme.colorScheme.outline.withValues(alpha: 0.3), width: sel ? 2 : 1),
        ),
        child: Text(label, textAlign: TextAlign.center,
          style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700, color: sel ? theme.colorScheme.primary : Colors.grey)),
      ),
    );
  }
}

class _QuotationCustomerPicker extends StatelessWidget {
  final List<Customer> customers;
  const _QuotationCustomerPicker({required this.customers});

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
