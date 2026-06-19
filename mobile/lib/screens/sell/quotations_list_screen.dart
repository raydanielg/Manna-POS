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
import '../../providers/sale_provider.dart';
import '../../models/sale.dart';
import 'add_quotation_screen.dart';

class QuotationsListScreen extends StatefulWidget {
  const QuotationsListScreen({super.key});
  @override State<QuotationsListScreen> createState() => _QuotationsListScreenState();
}

class _QuotationsListScreenState extends State<QuotationsListScreen> {
  List<Sale> _quotations = [];
  bool _loading = true;
  String _statusFilter = '';
  final _fmt = NumberFormat('#,##0');

  final _statuses = ['All', 'Pending', 'Approved', 'Expired'];
  final _statusVals = ['', 'pending', 'approved', 'expired'];

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() => _loading = true);
    try {
      await context.read<SaleProvider>().fetchSales(
        status: 'quotation',
        search: '',
      );
      if (mounted) {
        setState(() {
          _quotations = context.read<SaleProvider>().sales;
          _loading = false;
        });
      }
    } catch (_) {
      if (mounted) setState(() => _loading = false);
    }
  }

  List<Sale> get _filtered {
    if (_statusFilter.isEmpty) return _quotations;
    return _quotations.where((q) => q.status == _statusFilter).toList();
  }

  Future<void> _convertToSale(Sale quotation) async {
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (_) => ConfirmDialog(
        title: 'Convert to Sale',
        message: 'Convert ${quotation.reference} to a completed sale?',
        confirmLabel: 'Convert',
      ),
    );
    if (confirmed == true && mounted) {
      try {
        await context.read<SaleProvider>().createSale({
          'sale_date': DateTime.now().toIso8601String().split('T')[0],
          'status': 'completed',
          'payment_method': 'cash',
          'paid': quotation.total,
          'discount': 0,
          'notes': 'Converted from quotation ${quotation.reference}',
          'items': (quotation.items ?? []).map((item) => ({
            'product_id': item['product_id'] ?? item['id'],
            'quantity': item['quantity'],
            'unit_price': item['unit_price'],
          })).toList(),
        });
        if (mounted) {
          ToastHelper.success(context, 'Quotation converted to sale');
          _load();
        }
      } catch (e) {
        if (mounted) ToastHelper.error(context, 'Failed: $e');
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    final filtered = _filtered;
    return Scaffold(
      backgroundColor: theme.scaffoldBackgroundColor,
      appBar: AppBar(
        backgroundColor: theme.colorScheme.primary,
        foregroundColor: Colors.white,
        elevation: 0,
        title: const Text('Quotations',
          style: TextStyle(fontWeight: FontWeight.w700)),
        actions: [
          IconButton(icon: const Icon(Icons.refresh), onPressed: _load),
        ],
      ),
      body: Column(
        children: [
          // Filter chips
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
            child: SingleChildScrollView(
              scrollDirection: Axis.horizontal,
              child: Row(
                children: List.generate(_statuses.length, (i) {
                  final sel = _statusFilter == _statusVals[i];
                  return Padding(
                    padding: const EdgeInsets.only(right: 8),
                    child: FilterChip(
                      label: Text(_statuses[i]),
                      selected: sel,
                      onSelected: (_) {
                        setState(() => _statusFilter = _statusVals[i]);
                      },
                    ),
                  );
                }),
              ),
            ),
          ),
          Expanded(
            child: _loading
                ? const ShimmerLoading(child: ShimmerCard())
                : filtered.isEmpty
                    ? EmptyState(
                        icon: Icons.description_outlined,
                        title: 'No Quotations',
                        subtitle: 'Create quotations for your customers',
                      )
                    : RefreshIndicator(
                        onRefresh: _load,
                        child: ListView.separated(
                          padding: const EdgeInsets.fromLTRB(16, 0, 16, 100),
                          itemCount: filtered.length,
                          separatorBuilder: (_, __) => const SizedBox(height: 10),
                          itemBuilder: (_, i) => _quotationCard(filtered[i], theme),
                        ),
                      ),
          ),
        ],
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () {
          Navigator.push(
            context,
            MaterialPageRoute(builder: (_) => const AddQuotationScreen()),
          ).then((_) => _load());
        },
        backgroundColor: theme.colorScheme.primary,
        foregroundColor: Colors.white,
        icon: const Icon(Icons.add),
        label: const Text('New Quotation'),
      ),
    );
  }

  Widget _quotationCard(Sale q, ThemeData theme) {
    return GlassCard(
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          children: [
            Row(
              children: [
                Container(
                  width: 46, height: 46,
                  decoration: BoxDecoration(
                    color: Colors.purple.withValues(alpha: 0.15),
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: const Icon(Icons.description_outlined,
                    color: Colors.purple, size: 22),
                ),
                const SizedBox(width: 14),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(q.reference,
                        style: const TextStyle(
                          fontWeight: FontWeight.w700, fontSize: 14)),
                      const SizedBox(height: 3),
                      Text(q.customerName,
                        style: TextStyle(
                          color: theme.colorScheme.onSurface.withValues(alpha: 0.6),
                          fontSize: 13)),
                    ],
                  ),
                ),
                Text('TSh ${_fmt.format(q.total)}',
                  style: const TextStyle(
                    fontWeight: FontWeight.w800, fontSize: 15)),
              ],
            ),
            const SizedBox(height: 12),
            const Divider(height: 1),
            const SizedBox(height: 12),
            Row(
              children: [
                StatusBadge(
                  label: 'Quotation',
                  color: Colors.purple,
                  bgColor: Colors.purple.withValues(alpha: 0.15),
                ),
                const Spacer(),
                TextButton.icon(
                  onPressed: () => _convertToSale(q),
                  icon: const Icon(Icons.shopping_cart_checkout, size: 16),
                  label: const Text('Convert to Sale',
                    style: TextStyle(fontSize: 12)),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }
}
