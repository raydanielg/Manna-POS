import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../../providers/purchase_provider.dart';
import '../../models/purchase.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/status_badge.dart';
import '../../widgets/search_bar_widget.dart';
import '../../widgets/filter_chip_row.dart';
import '../../widgets/shimmer_loading.dart';
import '../../widgets/empty_state.dart';
import '../../widgets/section_header.dart';
import '../../widgets/stat_card.dart';
import '../../widgets/toast_helper.dart';
import '../../constants/app_constants.dart';

class PurchasesListScreen extends StatefulWidget {
  const PurchasesListScreen({super.key});
  @override State<PurchasesListScreen> createState() => _PurchasesListScreenState();
}

class _PurchasesListScreenState extends State<PurchasesListScreen> {
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
  void dispose() {
    _searchCtrl.dispose();
    super.dispose();
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
        title: const Text('Purchases'),
        actions: [IconButton(icon: const Icon(Icons.refresh), onPressed: _refresh)],
      ),
      body: Column(
        children: [
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 12, 16, 0),
            child: AppSearchBar(
              hint: 'Search by reference...',
              controller: _searchCtrl,
              onChanged: (v) {
                _search = v;
                _refresh();
              },
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
                ChipOption(label: 'Received', value: 'received'),
                ChipOption(label: 'Cancelled', value: 'cancelled'),
              ],
              onSelected: (v) {
                setState(() => _statusFilter = v);
                _refresh();
              },
            ),
          ),
          const SizedBox(height: 8),
          _buildStatsRow(),
          Expanded(child: _buildList()),
        ],
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const AddPurchaseScreen())),
        icon: const Icon(Icons.add),
        label: const Text('New Purchase'),
      ),
    );
  }

  Widget _buildStatsRow() {
    return Consumer<PurchaseProvider>(
      builder: (context, provider, _) {
        if (provider.purchases.isEmpty) return const SizedBox.shrink();
        final total = provider.purchases.fold<double>(0, (s, p) => s + p.totalAmount);
        final pending = provider.purchases.where((p) => p.status == 'pending').length;
        return Padding(
          padding: const EdgeInsets.symmetric(horizontal: 16),
          child: Row(
            children: [
              Expanded(child: StatCard(icon: Icons.shopping_cart_outlined, value: '${provider.purchases.length}', label: 'Total', color: AppColors.primary)),
              const SizedBox(width: 10),
              Expanded(child: StatCard(icon: Icons.attach_money, value: '${AppConstants.currency} ${_fmt.format(total)}', label: 'Amount', color: AppColors.success)),
              const SizedBox(width: 10),
              Expanded(child: StatCard(icon: Icons.pending_outlined, value: '$pending', label: 'Pending', color: AppColors.warning)),
            ],
          ),
        );
      },
    );
  }

  Widget _buildList() {
    return Consumer<PurchaseProvider>(
      builder: (context, provider, _) {
        if (provider.isLoading) return const ShimmerLoading();
        if (provider.error != null) {
          return Center(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                const Icon(Icons.error_outline, size: 48, color: AppColors.error),
                const SizedBox(height: 12),
                Text(provider.error!, style: const TextStyle(color: AppColors.textSec)),
                const SizedBox(height: 16),
                ElevatedButton(onPressed: _refresh, child: const Text('Retry')),
              ],
            ),
          );
        }
        final purchases = provider.purchases;
        if (purchases.isEmpty) {
          return const EmptyState(
            icon: Icons.shopping_cart_outlined,
            title: 'No Purchases',
            subtitle: 'Purchase orders will appear here',
          );
        }
        return RefreshIndicator(
          onRefresh: _refresh,
          child: ListView.separated(
            padding: const EdgeInsets.fromLTRB(16, 0, 16, 100),
            itemCount: purchases.length,
            separatorBuilder: (_, __) => const SizedBox(height: 10),
            itemBuilder: (_, i) => _purchaseCard(purchases[i]),
          ),
        );
      },
    );
  }

  Widget _purchaseCard(Purchase p) {
    return GlassCard(
      onTap: () => _showDetail(p),
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          children: [
            Row(
              children: [
                Container(
                  width: 46, height: 46,
                  decoration: BoxDecoration(color: AppColors.primaryLt, borderRadius: BorderRadius.circular(12)),
                  child: const Icon(Icons.shopping_cart_outlined, color: AppColors.primary, size: 22),
                ),
                const SizedBox(width: 14),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(p.reference, style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14)),
                      const SizedBox(height: 3),
                      Text(p.supplierName, style: const TextStyle(color: AppColors.textSec, fontSize: 13)),
                    ],
                  ),
                ),
                Column(
                  crossAxisAlignment: CrossAxisAlignment.end,
                  children: [
                    Text('${AppConstants.currency} ${_fmt.format(p.totalAmount)}', style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 15)),
                    const SizedBox(height: 4),
                    StatusBadge.fromStatus(p.status),
                  ],
                ),
              ],
            ),
            const SizedBox(height: 10),
            const Divider(height: 1),
            const SizedBox(height: 10),
            Row(
              children: [
                Row(
                  children: [
                    Icon(Icons.calendar_today_outlined, size: 13, color: AppColors.textSec),
                    const SizedBox(width: 4),
                    Text(_fmtDate(p.createdAt), style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
                  ],
                ),
                const Spacer(),
                Text('${p.items?.length ?? 0} items', style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
              ],
            ),
          ],
        ),
      ),
    );
  }

  void _showDetail(Purchase p) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (_) => _PurchaseDetailSheet(purchase: p),
    );
  }

  String _fmtDate(String? d) {
    if (d == null) return '';
    try { return DateFormat('dd MMM yyyy').format(DateTime.parse(d)); } catch (_) { return d; }
  }
}

class _PurchaseDetailSheet extends StatelessWidget {
  final Purchase purchase;
  const _PurchaseDetailSheet({required this.purchase});
  @override
  Widget build(BuildContext context) {
    final fmt = NumberFormat('#,##0.00');
    return Container(
      decoration: const BoxDecoration(color: Colors.white, borderRadius: BorderRadius.vertical(top: Radius.circular(24))),
      child: SingleChildScrollView(
        padding: const EdgeInsets.fromLTRB(24, 16, 24, 32),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Center(child: Container(width: 40, height: 4, decoration: BoxDecoration(color: AppColors.border, borderRadius: BorderRadius.circular(4)))),
            const SizedBox(height: 20),
            Row(
              children: [
                const Icon(Icons.shopping_cart_outlined, color: AppColors.primary, size: 28),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(purchase.reference, style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w700)),
                      Text(purchase.supplierName, style: const TextStyle(color: AppColors.textSec, fontSize: 13)),
                    ],
                  ),
                ),
                StatusBadge.fromStatus(purchase.status),
              ],
            ),
            const SizedBox(height: 20),
            const Divider(),
            const SizedBox(height: 16),
            const Text('Items', style: TextStyle(fontSize: 15, fontWeight: FontWeight.w700)),
            const SizedBox(height: 10),
            ...(purchase.items ?? []).map<Widget>((item) => Padding(
              padding: const EdgeInsets.only(bottom: 8),
              child: Row(
                children: [
                  Expanded(child: Text(item['product_name'] ?? '', style: const TextStyle(fontSize: 14))),
                  Text('${item['quantity']} x ${fmt.format((item['unit_cost'] ?? 0).toDouble())}', style: const TextStyle(color: AppColors.textSec, fontSize: 13)),
                  const SizedBox(width: 12),
                  Text('${fmt.format((item['total'] ?? 0).toDouble())}', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 13)),
                ],
              ),
            )),
            const Divider(),
            const SizedBox(height: 10),
            _row('Total', '${AppConstants.currency} ${fmt.format(purchase.totalAmount)}', bold: true),
            _row('Paid', '${AppConstants.currency} ${fmt.format(purchase.paidAmount)}'),
            _row('Status', purchase.status),
          ],
        ),
      ),
    );
  }

  Widget _row(String l, String v, {bool bold = false}) => Padding(
    padding: const EdgeInsets.only(bottom: 6),
    child: Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
      Text(l, style: const TextStyle(color: AppColors.textSec, fontSize: 14)),
      Text(v, style: TextStyle(fontWeight: bold ? FontWeight.w700 : FontWeight.w600, fontSize: 14)),
    ]),
  );
}
