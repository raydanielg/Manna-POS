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

class ReceiptsScreen extends StatefulWidget {
  const ReceiptsScreen({super.key});
  @override State<ReceiptsScreen> createState() => _ReceiptsScreenState();
}

class _ReceiptsScreenState extends State<ReceiptsScreen> {
  List<Sale> _receipts = [];
  bool _loading = true;
  final _fmt = NumberFormat('#,##0');

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() => _loading = true);
    try {
      await context.read<SaleProvider>().fetchSales(status: 'completed');
      if (mounted) {
        setState(() {
          _receipts = context.read<SaleProvider>().sales;
          _loading = false;
        });
      }
    } catch (_) {
      if (mounted) setState(() => _loading = false);
    }
  }

  void _viewReceipt(Sale sale) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (_) => _ReceiptViewSheet(sale: sale),
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
        title: const Text('Receipts',
          style: TextStyle(fontWeight: FontWeight.w700)),
        actions: [
          IconButton(icon: const Icon(Icons.refresh), onPressed: _load),
        ],
      ),
      body: _loading
          ? const ShimmerLoading(child: ShimmerCard())
          : _receipts.isEmpty
              ? EmptyState(
                  icon: Icons.receipt_outlined,
                  title: 'No Receipts',
                  subtitle: 'Completed sales receipts appear here',
                )
              : RefreshIndicator(
                  onRefresh: _load,
                  child: ListView.separated(
                    padding: const EdgeInsets.fromLTRB(16, 16, 16, 100),
                    itemCount: _receipts.length,
                    separatorBuilder: (_, __) => const SizedBox(height: 10),
                    itemBuilder: (_, i) => _receiptCard(_receipts[i], theme),
                  ),
                ),
    );
  }

  Widget _receiptCard(Sale sale, ThemeData theme) {
    return GlassCard(
      child: InkWell(
        onTap: () => _viewReceipt(sale),
        borderRadius: BorderRadius.circular(14),
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Row(
            children: [
              Container(
                width: 46, height: 46,
                decoration: BoxDecoration(
                  color: theme.colorScheme.primary.withValues(alpha: 0.15),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Icon(Icons.receipt,
                  color: theme.colorScheme.primary, size: 22),
              ),
              const SizedBox(width: 14),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(sale.reference,
                      style: const TextStyle(
                        fontWeight: FontWeight.w700, fontSize: 14)),
                    const SizedBox(height: 3),
                    Text(sale.customerName,
                      style: TextStyle(
                        color: theme.colorScheme.onSurface.withValues(alpha: 0.6),
                        fontSize: 13)),
                    const SizedBox(height: 4),
                    Text(sale.saleDate,
                      style: TextStyle(
                        fontSize: 11,
                        color: theme.colorScheme.onSurface.withValues(alpha: 0.5))),
                  ],
                ),
              ),
              Column(
                crossAxisAlignment: CrossAxisAlignment.end,
                children: [
                  Text('TSh ${_fmt.format(sale.total)}',
                    style: const TextStyle(
                      fontWeight: FontWeight.w800, fontSize: 15)),
                  const SizedBox(height: 8),
                  Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      IconButton(
                        constraints: const BoxConstraints(),
                        padding: EdgeInsets.zero,
                        iconSize: 20,
                        icon: const Icon(Icons.print_outlined),
                        onPressed: () {
                          ToastHelper.success(context, 'Print triggered');
                        },
                        color: theme.colorScheme.primary,
                      ),
                      const SizedBox(width: 8),
                      const Icon(Icons.chevron_right, size: 18, color: Colors.grey),
                    ],
                  ),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class _ReceiptViewSheet extends StatelessWidget {
  final Sale sale;
  const _ReceiptViewSheet({required this.sale});

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    final fmt = NumberFormat('#,##0');
    final items = sale.items ?? [];
    return Container(
      height: MediaQuery.of(context).size.height * 0.85,
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
          Expanded(
            child: SingleChildScrollView(
              padding: const EdgeInsets.all(24),
              child: Column(
                children: [
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
                                const Text('Official Receipt',
                                  style: TextStyle(
                                    color: Colors.grey, fontSize: 12)),
                              ],
                            ),
                          ),
                          const Divider(height: 24),
                          _receiptLine('Receipt #:', sale.reference),
                          _receiptLine('Date:', sale.saleDate),
                          _receiptLine('Customer:', sale.customerName),
                          _receiptLine('Payment:', sale.paymentMethod.toUpperCase()),
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
                                      Text(item['product_name'] ?? '',
                                        style: const TextStyle(
                                          fontWeight: FontWeight.w600,
                                          fontSize: 13)),
                                      Text(
                                        '${item['quantity']} x TSh ${fmt.format(double.tryParse(item['unit_price']?.toString() ?? '0') ?? 0)}',
                                        style: const TextStyle(
                                          color: Colors.grey, fontSize: 12)),
                                    ],
                                  ),
                                ),
                                Text(
                                  'TSh ${fmt.format(double.tryParse(item['total']?.toString() ?? '0') ?? 0)}',
                                  style: const TextStyle(
                                    fontWeight: FontWeight.w700, fontSize: 13)),
                              ],
                            ),
                          )),
                          const Divider(height: 24),
                          _receiptLine('Total:', 'TSh ${fmt.format(sale.total)}',
                            bold: true),
                          _receiptLine('Paid:', 'TSh ${fmt.format(sale.paid)}'),
                          if (sale.paid > sale.total)
                            _receiptLine('Change:',
                              'TSh ${fmt.format(sale.paid - sale.total)}',
                              color: Colors.green),
                          if (sale.outstanding > 0)
                            _receiptLine('Due:',
                              'TSh ${fmt.format(sale.outstanding)}',
                              color: Colors.red),
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
            child: Row(
              children: [
                Expanded(
                  child: SizedBox(
                    height: 52,
                    child: ElevatedButton.icon(
                      onPressed: () => ToastHelper.success(context, 'Printing...'),
                      icon: const Icon(Icons.print),
                      label: const Text('Print',
                        style: TextStyle(
                          fontSize: 16, fontWeight: FontWeight.w700)),
                    ),
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: SizedBox(
                    height: 52,
                    child: OutlinedButton(
                      onPressed: () => Navigator.pop(context),
                      child: const Text('Close',
                        style: TextStyle(
                          fontSize: 16, fontWeight: FontWeight.w700)),
                    ),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _receiptLine(String label, String value,
      {bool bold = false, Color? color}) {
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
