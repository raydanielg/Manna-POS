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

class StockAdjustmentsScreen extends StatefulWidget {
  const StockAdjustmentsScreen({super.key});
  @override State<StockAdjustmentsScreen> createState() => _StockAdjustmentsScreenState();
}

class _StockAdjustmentsScreenState extends State<StockAdjustmentsScreen> {
  String _typeFilter = '';

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
        title: const Text('Stock Adjustments'),
        actions: [IconButton(icon: const Icon(Icons.refresh), onPressed: _refresh)],
      ),
      body: Column(
        children: [
          const SizedBox(height: 12),
          SizedBox(
            height: 40,
            child: FilterChipRow(
              selected: _typeFilter,
              chips: const [
                ChipOption(label: 'All', value: ''),
                ChipOption(label: 'Addition', value: 'addition'),
                ChipOption(label: 'Reduction', value: 'subtraction'),
              ],
              onSelected: (v) => setState(() => _typeFilter = v),
            ),
          ),
          const SizedBox(height: 8),
          Expanded(child: _buildList()),
        ],
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const AddStockAdjustmentScreen())),
        icon: const Icon(Icons.add),
        label: const Text('New Adjustment'),
      ),
    );
  }

  Widget _buildList() {
    return Consumer<PurchaseProvider>(
      builder: (context, provider, _) {
        if (provider.isLoading) return const ShimmerLoading();
        if (provider.purchases.isEmpty) {
          return const EmptyState(icon: Icons.balance_outlined, title: 'No Adjustments', subtitle: 'Stock adjustments will appear here');
        }
        return RefreshIndicator(
          onRefresh: _refresh,
          child: ListView.separated(
            padding: const EdgeInsets.fromLTRB(16, 0, 16, 100),
            itemCount: provider.purchases.length,
            separatorBuilder: (_, __) => const SizedBox(height: 10),
            itemBuilder: (_, i) => _adjustmentCard(provider.purchases[i]),
          ),
        );
      },
    );
  }

  Widget _adjustmentCard(dynamic a) {
    final isAddition = a.status == 'received';
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
                    color: isAddition ? AppColors.successLt : AppColors.dangerLt,
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: Icon(
                    isAddition ? Icons.add_circle_outlined : Icons.remove_circle_outlined,
                    color: isAddition ? AppColors.success : AppColors.danger,
                    size: 22,
                  ),
                ),
                const SizedBox(width: 14),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(a.reference, style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14)),
                      const SizedBox(height: 3),
                      Text('Product: Item #${a.id}', style: const TextStyle(color: AppColors.textSec, fontSize: 13)),
                      if (a.notes != null && a.notes!.isNotEmpty) ...[
                        const SizedBox(height: 2),
                        Text(a.notes!, style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
                      ],
                    ],
                  ),
                ),
                Column(
                  crossAxisAlignment: CrossAxisAlignment.end,
                  children: [
                    Text('${isAddition ? '+' : '-'}1', style: TextStyle(fontWeight: FontWeight.w800, fontSize: 16, color: isAddition ? AppColors.success : AppColors.danger)),
                    const SizedBox(height: 4),
                    StatusBadge(label: isAddition ? 'Addition' : 'Reduction', color: isAddition ? AppColors.success : AppColors.danger, bgColor: isAddition ? AppColors.successLt : AppColors.dangerLt),
                  ],
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }
}
