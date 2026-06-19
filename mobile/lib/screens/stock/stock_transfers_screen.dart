import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/purchase_provider.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/status_badge.dart';
import '../../widgets/filter_chip_row.dart';
import '../../widgets/shimmer_loading.dart';
import '../../widgets/empty_state.dart';
import '../../widgets/toast_helper.dart';
import '../../constants/app_constants.dart';

class StockTransfersScreen extends StatefulWidget {
  const StockTransfersScreen({super.key});
  @override State<StockTransfersScreen> createState() => _StockTransfersScreenState();
}

class _StockTransfersScreenState extends State<StockTransfersScreen> {
  String _statusFilter = '';

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<PurchaseProvider>().fetchPurchases();
    });
  }

  Future<void> _refresh() async {
    await context.read<PurchaseProvider>().fetchPurchases();
  }

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    return Scaffold(
      backgroundColor: theme.scaffoldBackgroundColor,
      appBar: AppBar(
        title: const Text('Stock Transfers'),
        actions: [IconButton(icon: const Icon(Icons.refresh), onPressed: _refresh)],
      ),
      body: Column(
        children: [
          const SizedBox(height: 12),
          SizedBox(
            height: 40,
            child: FilterChipRow(
              selected: _statusFilter,
              chips: const [
                ChipOption(label: 'All', value: ''),
                ChipOption(label: 'Completed', value: 'completed'),
                ChipOption(label: 'Pending', value: 'pending'),
                ChipOption(label: 'Cancelled', value: 'cancelled'),
              ],
              onSelected: (v) => setState(() => _statusFilter = v),
            ),
          ),
          const SizedBox(height: 8),
          Expanded(child: _buildList()),
        ],
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const AddStockTransferScreen())),
        icon: const Icon(Icons.add),
        label: const Text('New Transfer'),
      ),
    );
  }

  Widget _buildList() {
    return Consumer<PurchaseProvider>(
      builder: (context, provider, _) {
        if (provider.isLoading) return const ShimmerLoading();
        if (provider.purchases.isEmpty) {
          return const EmptyState(icon: Icons.swap_horiz_outlined, title: 'No Transfers', subtitle: 'Stock transfers will appear here');
        }
        return RefreshIndicator(
          onRefresh: _refresh,
          child: ListView.separated(
            padding: const EdgeInsets.fromLTRB(16, 0, 16, 100),
            itemCount: provider.purchases.length,
            separatorBuilder: (_, __) => const SizedBox(height: 10),
            itemBuilder: (_, i) => _transferCard(provider.purchases[i]),
          ),
        );
      },
    );
  }

  Widget _transferCard(dynamic t) {
    return GlassCard(
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          children: [
            Row(
              children: [
                Container(
                  width: 46, height: 46,
                  decoration: BoxDecoration(color: AppColors.primaryLt, borderRadius: BorderRadius.circular(12)),
                  child: const Icon(Icons.swap_horiz_outlined, color: AppColors.primary, size: 22),
                ),
                const SizedBox(width: 14),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(t.reference, style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14)),
                      const SizedBox(height: 3),
                      Text('Warehouse A → Store B', style: const TextStyle(color: AppColors.textSec, fontSize: 13)),
                    ],
                  ),
                ),
                StatusBadge.fromStatus(t.status),
              ],
            ),
            const SizedBox(height: 8),
            Row(
              children: [
                Icon(Icons.calendar_today_outlined, size: 12, color: AppColors.textSec),
                const SizedBox(width: 4),
                Text(_fmtDate(t.createdAt), style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
                const Spacer(),
                Text('${t.items?.length ?? 0} items', style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
              ],
            ),
          ],
        ),
      ),
    );
  }

  String _fmtDate(String? d) {
    if (d == null) return '';
    try { return '${DateTime.parse(d).day}/${DateTime.parse(d).month}/${DateTime.parse(d).year}'; } catch (_) { return d; }
  }
}
