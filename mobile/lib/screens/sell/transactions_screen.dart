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
import '../../providers/sale_provider.dart';
import '../../models/sale.dart';

class TransactionsScreen extends StatefulWidget {
  const TransactionsScreen({super.key});
  @override State<TransactionsScreen> createState() => _TransactionsScreenState();
}

class _TransactionsScreenState extends State<TransactionsScreen> {
  List<Sale> _transactions = [];
  bool _loading = true;
  String _payMethodFilter = '';
  final _fmt = NumberFormat('#,##0');
  DateTimeRange? _dateRange;

  final _methods = ['All', 'Cash', 'Card', 'Mobile', 'Credit'];
  final _methodVals = ['', 'cash', 'card', 'mobile', 'credit'];

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() => _loading = true);
    try {
      await context.read<SaleProvider>().fetchSales();
      if (mounted) {
        setState(() {
          _transactions = context.read<SaleProvider>().sales;
          _loading = false;
        });
      }
    } catch (_) {
      if (mounted) setState(() => _loading = false);
    }
  }

  List<Sale> get _filtered {
    var result = _transactions;
    if (_payMethodFilter.isNotEmpty) {
      result = result.where((t) => t.paymentMethod.toLowerCase() == _payMethodFilter).toList();
    }
    if (_dateRange != null) {
      result = result.where((t) {
        try {
          final dt = DateTime.parse(t.saleDate);
          return dt.isAfter(_dateRange!.start.subtract(const Duration(days: 1))) &&
              dt.isBefore(_dateRange!.end.add(const Duration(days: 1)));
        } catch (_) {
          return true;
        }
      }).toList();
    }
    return result;
  }

  Future<void> _pickDateRange() async {
    final now = DateTime.now();
    final range = await showDateRangePicker(
      context: context,
      firstDate: DateTime(2020),
      lastDate: now,
      initialDateRange: _dateRange ?? DateTimeRange(
        start: now.subtract(const Duration(days: 30)),
        end: now,
      ),
    );
    if (range != null) setState(() => _dateRange = range);
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
        title: const Text('Transactions', style: TextStyle(fontWeight: FontWeight.w700)),
        actions: [
          IconButton(icon: const Icon(Icons.date_range), onPressed: _pickDateRange),
          IconButton(icon: const Icon(Icons.refresh), onPressed: _load),
        ],
      ),
      body: Column(
        children: [
          Container(
            color: theme.cardColor,
            padding: const EdgeInsets.symmetric(vertical: 12, horizontal: 16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                SingleChildScrollView(
                  scrollDirection: Axis.horizontal,
                  child: Row(
                    children: List.generate(_methods.length, (i) {
                      final sel = _payMethodFilter == _methodVals[i];
                      return Padding(
                        padding: const EdgeInsets.only(right: 8),
                        child: FilterChip(
                          label: Text(_methods[i], style: TextStyle(fontSize: 12, color: sel ? Colors.white : null)),
                          selected: sel,
                          onSelected: (_) => setState(() => _payMethodFilter = _methodVals[i]),
                          selectedColor: theme.colorScheme.primary,
                        ),
                      );
                    }),
                  ),
                ),
                if (_dateRange != null) ...[
                  const SizedBox(height: 8),
                  Row(
                    children: [
                      Text('${DateFormat('dd MMM').format(_dateRange!.start)} - ${DateFormat('dd MMM yyyy').format(_dateRange!.end)}',
                        style: TextStyle(fontSize: 12, color: theme.colorScheme.primary, fontWeight: FontWeight.w600)),
                      const SizedBox(width: 8),
                      GestureDetector(
                        onTap: () => setState(() => _dateRange = null),
                        child: Icon(Icons.close, size: 16, color: theme.colorScheme.error),
                      ),
                    ],
                  ),
                ],
              ],
            ),
          ),
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 12, 16, 8),
            child: Text('${filtered.length} transactions',
              style: TextStyle(color: theme.colorScheme.onSurface.withValues(alpha: 0.6), fontSize: 13)),
          ),
          Expanded(
            child: _loading
                ? const ShimmerLoading(child: ShimmerCard())
                : filtered.isEmpty
                    ? EmptyState(icon: Icons.payment_outlined, title: 'No Transactions', subtitle: 'Try changing filters')
                    : RefreshIndicator(
                        onRefresh: _load,
                        child: ListView.separated(
                          padding: const EdgeInsets.fromLTRB(16, 0, 16, 100),
                          itemCount: filtered.length,
                          separatorBuilder: (_, __) => const SizedBox(height: 8),
                          itemBuilder: (_, i) => _transactionCard(filtered[i], theme),
                        ),
                      ),
          ),
        ],
      ),
    );
  }

  Widget _transactionCard(Sale t, ThemeData theme) {
    final isCash = t.paymentMethod == 'cash';
    final isCard = t.paymentMethod == 'card';
    final isMobile = t.paymentMethod == 'mobile';
    final icon = isCash ? Icons.payments_outlined : isCard ? Icons.credit_card : isMobile ? Icons.phone_android : Icons.account_balance;
    final color = isCash ? Colors.green : isCard ? Colors.blue : isMobile ? Colors.purple : Colors.orange;
    return GlassCard(
      child: Padding(
        padding: const EdgeInsets.all(14),
        child: Row(
          children: [
            Container(
              width: 42, height: 42,
              decoration: BoxDecoration(color: color.withValues(alpha: 0.15), borderRadius: BorderRadius.circular(10)),
              child: Icon(icon, size: 20, color: color),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(t.reference, style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14)),
                  const SizedBox(height: 3),
                  Text(t.customerName, style: TextStyle(color: theme.colorScheme.onSurface.withValues(alpha: 0.6), fontSize: 12)),
                ],
              ),
            ),
            Column(
              crossAxisAlignment: CrossAxisAlignment.end,
              children: [
                Text('TSh ${_fmt.format(t.total)}', style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 14)),
                const SizedBox(height: 4),
                StatusBadge.fromStatus(t.paymentStatus),
              ],
            ),
          ],
        ),
      ),
    );
  }
}
