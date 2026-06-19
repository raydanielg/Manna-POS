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

class ReturnsScreen extends StatefulWidget {
  const ReturnsScreen({super.key});
  @override State<ReturnsScreen> createState() => _ReturnsScreenState();
}

class _ReturnsScreenState extends State<ReturnsScreen> {
  List<Sale> _returns = [];
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
      await context.read<SaleProvider>().fetchSales(status: 'returns');
      if (mounted) {
        setState(() {
          _returns = context.read<SaleProvider>().sales;
          _loading = false;
        });
      }
    } catch (_) {
      if (mounted) setState(() => _loading = false);
    }
  }

  double get _totalRefunded =>
      _returns.fold(0, (s, r) => s + r.total);

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    return Scaffold(
      backgroundColor: theme.scaffoldBackgroundColor,
      appBar: AppBar(
        backgroundColor: theme.colorScheme.primary,
        foregroundColor: Colors.white,
        elevation: 0,
        title: const Text('Returns',
          style: TextStyle(fontWeight: FontWeight.w700)),
        actions: [
          IconButton(icon: const Icon(Icons.refresh), onPressed: _load),
        ],
      ),
      body: _loading
          ? const ShimmerLoading(child: ShimmerCard())
          : _returns.isEmpty
              ? EmptyState(
                  icon: Icons.assignment_return_outlined,
                  title: 'No Returns',
                  subtitle: 'Returned sales will appear here',
                )
              : RefreshIndicator(
                  onRefresh: _load,
                  child: ListView(
                    padding: const EdgeInsets.fromLTRB(16, 16, 16, 100),
                    children: [
                      // Summary card
                      GlassCard(
                        child: Padding(
                          padding: const EdgeInsets.all(16),
                          child: Row(
                            children: [
                              Container(
                                width: 48, height: 48,
                                decoration: BoxDecoration(
                                  color: Colors.red.withValues(alpha: 0.15),
                                  borderRadius: BorderRadius.circular(12),
                                ),
                                child: const Icon(Icons.money_off,
                                  color: Colors.red, size: 24),
                              ),
                              const SizedBox(width: 16),
                              Expanded(
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    const Text('Total Refunded',
                                      style: TextStyle(
                                        color: Colors.grey, fontSize: 13)),
                                    Text('TSh ${_fmt.format(_totalRefunded)}',
                                      style: const TextStyle(
                                        fontWeight: FontWeight.w800,
                                        fontSize: 20,
                                        color: Colors.red)),
                                  ],
                                ),
                              ),
                              Text('${_returns.length} returns',
                                style: const TextStyle(
                                  color: Colors.grey, fontSize: 12)),
                            ],
                          ),
                        ),
                      ),
                      const SizedBox(height: 16),
                      const SectionHeader(title: 'Return Items'),
                      const SizedBox(height: 8),
                      ..._returns.map((r) => _returnCard(r, theme)),
                    ],
                  ),
                ),
    );
  }

  Widget _returnCard(Sale r, ThemeData theme) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 10),
      child: GlassCard(
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  Icon(Icons.assignment_return, size: 20,
                    color: theme.colorScheme.error),
                  const SizedBox(width: 8),
                  Text(r.reference,
                    style: const TextStyle(
                      fontWeight: FontWeight.w700, fontSize: 14)),
                  const Spacer(),
                  StatusBadge.fromStatus(r.status),
                ],
              ),
              const SizedBox(height: 8),
              Row(
                children: [
                  Text(r.customerName,
                    style: TextStyle(
                      color: theme.colorScheme.onSurface.withValues(alpha: 0.6),
                      fontSize: 13)),
                  const Spacer(),
                  Text('TSh ${_fmt.format(r.total)}',
                    style: TextStyle(
                      fontWeight: FontWeight.w800,
                      color: theme.colorScheme.error)),
                ],
              ),
              const SizedBox(height: 8),
              // Items
              ...(r.items ?? []).map((item) => Container(
                margin: const EdgeInsets.only(bottom: 4),
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                decoration: BoxDecoration(
                  color: theme.colorScheme.surfaceVariant,
                  borderRadius: BorderRadius.circular(8),
                ),
                child: Row(
                  children: [
                    Expanded(
                      child: Text(item['product_name'] ?? '',
                        style: const TextStyle(fontSize: 12)),
                    ),
                    Text('x${item['quantity']}',
                      style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 12)),
                  ],
                ),
              )),
            ],
          ),
        ),
      ),
    );
  }
}
