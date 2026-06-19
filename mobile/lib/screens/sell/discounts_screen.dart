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
import '../../models/product.dart';

class DiscountsScreen extends StatefulWidget {
  const DiscountsScreen({super.key});
  @override State<DiscountsScreen> createState() => _DiscountsScreenState();
}

class _DiscountsScreenState extends State<DiscountsScreen> {
  List<Map<String, dynamic>> _discounts = [];
  bool _loading = true;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() => _loading = true);
    try {
      final provider = context.read<SaleProvider>();
      await provider.fetchSales();
      setState(() {
        _discounts = [
          {
            'id': 1, 'name': 'New Customer 10%',
            'type': 'percentage', 'value': 10,
            'active': true, 'products': 'All Products',
          },
          {
            'id': 2, 'name': 'Holiday Special',
            'type': 'fixed', 'value': 5000,
            'active': true, 'products': 'Selected',
          },
          {
            'id': 3, 'name': 'Clearance Sale',
            'type': 'percentage', 'value': 25,
            'active': false, 'products': 'Old Stock',
          },
        ];
        _loading = false;
      });
    } catch (_) {
      if (mounted) setState(() => _loading = false);
    }
  }

  void _toggleActive(int index) {
    setState(() {
      _discounts[index]['active'] = !_discounts[index]['active'];
    });
    ToastHelper.success(
      context,
      _discounts[index]['active'] ? 'Discount activated' : 'Discount deactivated',
    );
  }

  void _showAddDiscountSheet() {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (_) => const _AddDiscountSheet(),
    ).then((added) {
      if (added == true) _load();
    });
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
        title: const Text('Discounts',
          style: TextStyle(fontWeight: FontWeight.w700)),
        actions: [
          IconButton(icon: const Icon(Icons.refresh), onPressed: _load),
        ],
      ),
      body: _loading
          ? const ShimmerLoading(child: ShimmerCard())
          : _discounts.isEmpty
              ? EmptyState(
                  icon: Icons.local_offer_outlined,
                  title: 'No Discounts',
                  subtitle: 'Add discounts to attract more sales',
                  actionLabel: 'Add Discount',
                  onAction: _showAddDiscountSheet,
                )
              : RefreshIndicator(
                  onRefresh: _load,
                  child: ListView.separated(
                    padding: const EdgeInsets.fromLTRB(16, 16, 16, 100),
                    itemCount: _discounts.length,
                    separatorBuilder: (_, __) => const SizedBox(height: 10),
                    itemBuilder: (_, i) => _discountCard(i, theme),
                  ),
                ),
      floatingActionButton: FloatingActionButton(
        onPressed: _showAddDiscountSheet,
        backgroundColor: theme.colorScheme.primary,
        foregroundColor: Colors.white,
        child: const Icon(Icons.add),
      ),
    );
  }

  Widget _discountCard(int index, ThemeData theme) {
    final d = _discounts[index];
    return GlassCard(
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Container(
                  width: 46, height: 46,
                  decoration: BoxDecoration(
                    color: Colors.orange.withValues(alpha: 0.15),
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: const Icon(Icons.local_offer,
                    color: Colors.orange, size: 22),
                ),
                const SizedBox(width: 14),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(d['name'],
                        style: const TextStyle(
                          fontWeight: FontWeight.w700, fontSize: 14)),
                      const SizedBox(height: 3),
                      Text(
                        d['type'] == 'percentage'
                            ? '${d['value']}% off'
                            : 'TSh ${NumberFormat('#,##0').format(d['value'])} off',
                        style: TextStyle(
                          color: theme.colorScheme.onSurface.withValues(alpha: 0.6),
                          fontSize: 13)),
                    ],
                  ),
                ),
                Switch(
                  value: d['active'],
                  onChanged: (_) => _toggleActive(index),
                  activeColor: theme.colorScheme.primary,
                ),
              ],
            ),
            const SizedBox(height: 8),
            Row(
              children: [
                Icon(Icons.category_outlined, size: 14,
                  color: theme.colorScheme.onSurface.withValues(alpha: 0.5)),
                const SizedBox(width: 4),
                Text('Applies to: ${d['products']}',
                  style: TextStyle(
                    fontSize: 12,
                    color: theme.colorScheme.onSurface.withValues(alpha: 0.6))),
                const Spacer(),
                StatusBadge(
                  label: d['active'] ? 'Active' : 'Inactive',
                  color: d['active'] ? Colors.green : Colors.grey,
                  bgColor: d['active']
                      ? Colors.green.withValues(alpha: 0.15)
                      : Colors.grey.withValues(alpha: 0.15),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }
}

class _AddDiscountSheet extends StatefulWidget {
  const _AddDiscountSheet();
  @override State<_AddDiscountSheet> createState() => _AddDiscountSheetState();
}

class _AddDiscountSheetState extends State<_AddDiscountSheet> {
  final _nameCtrl = TextEditingController();
  final _valueCtrl = TextEditingController();
  String _type = 'percentage';
  bool _submitting = false;

  @override
  void dispose() {
    _nameCtrl.dispose();
    _valueCtrl.dispose();
    super.dispose();
  }

  void _submit() {
    if (_nameCtrl.text.isEmpty || _valueCtrl.text.isEmpty) {
      ToastHelper.warning(context, 'Fill all fields');
      return;
    }
    ToastHelper.success(context, 'Discount added');
    Navigator.pop(context, true);
  }

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    return Container(
      padding: EdgeInsets.only(
        bottom: MediaQuery.of(context).viewInsets.bottom,
      ),
      decoration: BoxDecoration(
        color: theme.scaffoldBackgroundColor,
        borderRadius: const BorderRadius.vertical(top: Radius.circular(24)),
      ),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          Center(child: Container(
            margin: const EdgeInsets.only(top: 12),
            width: 40, height: 4,
            decoration: BoxDecoration(
              color: theme.colorScheme.outline.withValues(alpha: 0.3),
              borderRadius: BorderRadius.circular(4)))),
          Padding(
            padding: const EdgeInsets.fromLTRB(20, 16, 20, 8),
            child: Text('Add Discount',
              style: TextStyle(
                fontSize: 18, fontWeight: FontWeight.w700,
                color: theme.colorScheme.onSurface)),
          ),
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 20),
            child: Column(
              children: [
                TextField(
                  controller: _nameCtrl,
                  decoration: const InputDecoration(labelText: 'Discount Name'),
                ),
                const SizedBox(height: 12),
                Row(
                  children: [
                    Expanded(
                      child: GestureDetector(
                        onTap: () => setState(() => _type = 'percentage'),
                        child: Container(
                          padding: const EdgeInsets.symmetric(vertical: 10),
                          decoration: BoxDecoration(
                            color: _type == 'percentage'
                                ? theme.colorScheme.primary.withValues(alpha: 0.15)
                                : Colors.white,
                            borderRadius: BorderRadius.circular(8),
                            border: Border.all(
                              color: _type == 'percentage'
                                  ? theme.colorScheme.primary
                                  : theme.colorScheme.outline.withValues(alpha: 0.3),
                              width: _type == 'percentage' ? 2 : 1,
                            ),
                          ),
                          child: Text('Percentage',
                            textAlign: TextAlign.center,
                            style: TextStyle(
                              fontWeight: FontWeight.w700,
                              color: _type == 'percentage'
                                  ? theme.colorScheme.primary
                                  : Colors.grey,
                              fontSize: 13)),
                        ),
                      ),
                    ),
                    const SizedBox(width: 8),
                    Expanded(
                      child: GestureDetector(
                        onTap: () => setState(() => _type = 'fixed'),
                        child: Container(
                          padding: const EdgeInsets.symmetric(vertical: 10),
                          decoration: BoxDecoration(
                            color: _type == 'fixed'
                                ? theme.colorScheme.primary.withValues(alpha: 0.15)
                                : Colors.white,
                            borderRadius: BorderRadius.circular(8),
                            border: Border.all(
                              color: _type == 'fixed'
                                  ? theme.colorScheme.primary
                                  : theme.colorScheme.outline.withValues(alpha: 0.3),
                              width: _type == 'fixed' ? 2 : 1,
                            ),
                          ),
                          child: Text('Fixed Amount',
                            textAlign: TextAlign.center,
                            style: TextStyle(
                              fontWeight: FontWeight.w700,
                              color: _type == 'fixed'
                                  ? theme.colorScheme.primary
                                  : Colors.grey,
                              fontSize: 13)),
                        ),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 12),
                TextField(
                  controller: _valueCtrl,
                  keyboardType: TextInputType.number,
                  decoration: InputDecoration(
                    labelText: _type == 'percentage' ? 'Percentage (%)' : 'Amount (TSh)',
                    suffixText: _type == 'percentage' ? '%' : 'TSh',
                  ),
                ),
                const SizedBox(height: 20),
                SizedBox(
                  width: double.infinity, height: 52,
                  child: ElevatedButton(
                    onPressed: _submitting ? null : _submit,
                    child: _submitting
                        ? const SizedBox(width: 20, height: 20,
                            child: CircularProgressIndicator(
                              color: Colors.white, strokeWidth: 2))
                        : const Text('Add Discount',
                            style: TextStyle(
                              fontSize: 16, fontWeight: FontWeight.w700)),
                  ),
                ),
                const SizedBox(height: 24),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
