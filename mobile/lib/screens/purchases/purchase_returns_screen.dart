import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../../providers/purchase_provider.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/status_badge.dart';
import '../../widgets/search_bar_widget.dart';
import '../../widgets/filter_chip_row.dart';
import '../../widgets/shimmer_loading.dart';
import '../../widgets/empty_state.dart';
import '../../widgets/confirm_dialog.dart';
import '../../widgets/toast_helper.dart';
import '../../constants/app_constants.dart';

class PurchaseReturnsScreen extends StatefulWidget {
  const PurchaseReturnsScreen({super.key});
  @override State<PurchaseReturnsScreen> createState() => _PurchaseReturnsScreenState();
}

class _PurchaseReturnsScreenState extends State<PurchaseReturnsScreen> {
  final _searchCtrl = TextEditingController();
  final _fmt = NumberFormat('#,##0.00');
  String _search = '';
  String _statusFilter = '';

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<PurchaseProvider>().fetchPurchases();
    });
  }

  @override
  void dispose() { _searchCtrl.dispose(); super.dispose(); }

  Future<void> _refresh() async {
    await context.read<PurchaseProvider>().fetchPurchases();
  }

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    return Scaffold(
      backgroundColor: theme.scaffoldBackgroundColor,
      appBar: AppBar(
        title: const Text('Purchase Returns'),
        actions: [IconButton(icon: const Icon(Icons.refresh), onPressed: _refresh)],
      ),
      body: Column(
        children: [
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 12, 16, 0),
            child: AppSearchBar(
              hint: 'Search returns...',
              controller: _searchCtrl,
              onChanged: (v) { _search = v; _refresh(); },
            ),
          ),
          const SizedBox(height: 12),
          SizedBox(
            height: 40,
            child: FilterChipRow(
              selected: _statusFilter,
              chips: const [
                ChipOption(label: 'All', value: ''),
                ChipOption(label: 'Pending', value: 'pending'),
                ChipOption(label: 'Approved', value: 'approved'),
                ChipOption(label: 'Rejected', value: 'rejected'),
              ],
              onSelected: (v) { setState(() => _statusFilter = v); _refresh(); },
            ),
          ),
          const SizedBox(height: 8),
          Expanded(child: _buildList()),
        ],
      ),
    );
  }

  Widget _buildList() {
    return Consumer<PurchaseProvider>(
      builder: (context, provider, _) {
        if (provider.isLoading) return const ShimmerLoading();
        if (provider.purchases.isEmpty) {
          return const EmptyState(icon: Icons.assignment_return_outlined, title: 'No Returns', subtitle: 'Purchase returns will appear here');
        }
        return RefreshIndicator(
          onRefresh: _refresh,
          child: ListView.separated(
            padding: const EdgeInsets.fromLTRB(16, 0, 16, 100),
            itemCount: provider.purchases.length,
            separatorBuilder: (_, __) => const SizedBox(height: 10),
            itemBuilder: (_, i) => _returnCard(provider.purchases[i]),
          ),
        );
      },
    );
  }

  Widget _returnCard(dynamic p) {
    return GlassCard(
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Row(
          children: [
            Container(
              width: 46, height: 46,
              decoration: BoxDecoration(color: AppColors.warningLt, borderRadius: BorderRadius.circular(12)),
              child: const Icon(Icons.assignment_return_outlined, color: AppColors.warning, size: 22),
            ),
            const SizedBox(width: 14),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(p.reference, style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14)),
                  const SizedBox(height: 3),
                  Text(p.supplierName, style: const TextStyle(color: AppColors.textSec, fontSize: 13)),
                  const SizedBox(height: 2),
                  Row(
                    children: [
                      StatusBadge.fromStatus(p.status),
                      const SizedBox(width: 8),
                      Text('${AppConstants.currency} ${_fmt.format(p.totalAmount)}', style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
                    ],
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}
