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
import '../../widgets/stat_card.dart';
import '../../providers/sale_provider.dart';
import '../../providers/product_provider.dart';
import '../../providers/customer_provider.dart';
import '../../models/sale.dart';
import '../../models/product.dart';
import '../../models/customer.dart';
import 'pos_screen.dart';

class SalesScreen extends StatefulWidget {
  const SalesScreen({super.key});
  @override State<SalesScreen> createState() => _SalesScreenState();
}

class _SalesScreenState extends State<SalesScreen> with SingleTickerProviderStateMixin {
  late TabController _tabCtrl;
  final _searchCtrl = TextEditingController();
  List<Sale> _sales = [];
  bool _loading = true;
  String? _error;
  String _search = '';
  String _statusFilter = '';
  final _fmt = NumberFormat('#,##0');
  Map<String, dynamic>? _stats;

  final _tabs = ['All', 'Completed', 'Pending', 'Draft', 'Returns'];
  final _statusValues = ['', 'completed', 'pending', 'draft', 'returns'];

  @override
  void initState() {
    super.initState();
    _tabCtrl = TabController(length: _tabs.length, vsync: this);
    _tabCtrl.addListener(() {
      if (!_tabCtrl.indexIsChanging) {
        setState(() => _statusFilter = _statusValues[_tabCtrl.index]);
        _load();
      }
    });
    _load();
  }

  @override
  void dispose() {
    _tabCtrl.dispose();
    _searchCtrl.dispose();
    super.dispose();
  }

  Future<void> _load() async {
    setState(() { _loading = true; _error = null; });
    try {
      final provider = context.read<SaleProvider>();
      await provider.fetchSales(status: _statusFilter, search: _search);
      if (mounted) {
        setState(() {
          _sales = provider.sales;
          _stats = provider.stats;
          _loading = false;
        });
      }
    } catch (e) {
      if (mounted) setState(() { _error = e.toString(); _loading = false; });
    }
  }

  void _onSearch(String q) {
    setState(() => _search = q);
    _load();
  }

  Future<void> _showSaleDetail(Sale sale) async {
    await showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (_) => _SaleDetailSheet(sale: sale),
    );
  }

  Future<void> _deleteSale(Sale sale) async {
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (_) => ConfirmDialog(
        title: 'Delete Sale',
        message: 'Delete ${sale.reference}? This cannot be undone.',
        confirmLabel: 'Delete',
        isDestructive: true,
      ),
    );
    if (confirmed == true && mounted) {
      try {
        await context.read<SaleProvider>().deleteSale(sale.id);
        if (mounted) {
          ToastHelper.success(context, 'Sale deleted');
          _load();
        }
      } catch (e) {
        if (mounted) ToastHelper.error(context, 'Failed to delete');
      }
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
        title: const Text('Sales', style: TextStyle(fontWeight: FontWeight.w700)),
        actions: [
          IconButton(icon: const Icon(Icons.search), onPressed: () {}),
          IconButton(icon: const Icon(Icons.refresh), onPressed: _load),
        ],
        bottom: TabBar(
          controller: _tabCtrl,
          isScrollable: true,
          indicatorColor: Colors.white,
          labelColor: Colors.white,
          unselectedLabelColor: Colors.white70,
          tabs: _tabs.map((t) => Tab(text: t)).toList(),
        ),
      ),
      body: Column(
        children: [
          // Search bar
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 12, 16, 0),
            child: AppSearchBar(
              hint: 'Search by reference or customer...',
              onChanged: _onSearch,
              controller: _searchCtrl,
            ),
          ),
          // Stats row
          if (_stats != null && _loading == false)
            Padding(
              padding: const EdgeInsets.fromLTRB(16, 12, 16, 0),
              child: Row(
                children: [
                  Expanded(
                    child: StatCard(
                      icon: Icons.today,
                      value: '${AppConstants.currency} ${_fmt.format(double.tryParse(_stats!['today']?.toString() ?? '0') ?? 0)}',
                      label: 'Today',
                      color: theme.colorScheme.primary,
                    ),
                  ),
                  const SizedBox(width: 8),
                  Expanded(
                    child: StatCard(
                      icon: Icons.calendar_view_week,
                      value: '${AppConstants.currency} ${_fmt.format(double.tryParse(_stats!['week']?.toString() ?? '0') ?? 0)}',
                      label: 'This Week',
                      color: Colors.green,
                    ),
                  ),
                  const SizedBox(width: 8),
                  Expanded(
                    child: StatCard(
                      icon: Icons.calendar_month,
                      value: '${AppConstants.currency} ${_fmt.format(double.tryParse(_stats!['month']?.toString() ?? '0') ?? 0)}',
                      label: 'This Month',
                      color: Colors.orange,
                    ),
                  ),
                ],
              ),
            ),
          // Count
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 12, 16, 0),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text('${_sales.length} ${_sales.length == 1 ? 'Sale' : 'Sales'}',
                  style: TextStyle(
                    color: theme.colorScheme.onSurface.withValues(alpha: 0.6),
                    fontSize: 13)),
                if (_loading)
                  SizedBox(
                    width: 16, height: 16,
                    child: CircularProgressIndicator(
                      strokeWidth: 2,
                      color: theme.colorScheme.primary)),
              ],
            ),
          ),
          // Content
          Expanded(
            child: _loading
                ? const ShimmerLoading(child: ShimmerCard())
                : _error != null
                    ? EmptyState(
                        icon: Icons.error_outline,
                        title: 'Error Loading',
                        subtitle: _error,
                        actionLabel: 'Retry',
                        onAction: _load,
                      )
                    : _sales.isEmpty
                        ? EmptyState(
                            icon: Icons.receipt_long_outlined,
                            title: 'No Sales Found',
                            subtitle: 'Sales will appear here once created',
                          )
                        : RefreshIndicator(
                            onRefresh: _load,
                            child: ListView.separated(
                              padding: const EdgeInsets.fromLTRB(16, 8, 16, 100),
                              itemCount: _sales.length,
                              separatorBuilder: (_, __) => const SizedBox(height: 10),
                              itemBuilder: (_, i) => _saleCard(_sales[i]),
                            ),
                          ),
          ),
        ],
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () {
          Navigator.push(
            context,
            MaterialPageRoute(builder: (_) => const AddSaleScreen()),
          );
        },
        backgroundColor: theme.colorScheme.primary,
        foregroundColor: Colors.white,
        icon: const Icon(Icons.add),
        label: const Text('New Sale'),
      ),
    );
  }

  Widget _saleCard(Sale s) {
    final theme = Theme.of(context);
    return GlassCard(
      child: InkWell(
        onTap: () => _showSaleDetail(s),
        borderRadius: BorderRadius.circular(14),
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Column(
            children: [
              Row(
                children: [
                  Container(
                    width: 46, height: 46,
                    decoration: BoxDecoration(
                      color: s.status == 'completed'
                          ? Colors.green.withValues(alpha: 0.15)
                          : s.status == 'draft'
                              ? Colors.orange.withValues(alpha: 0.15)
                              : s.status == 'pending'
                                  ? Colors.blue.withValues(alpha: 0.15)
                                  : Colors.red.withValues(alpha: 0.15),
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: Icon(
                      s.status == 'completed'
                          ? Icons.check_circle_outline
                          : s.status == 'draft'
                              ? Icons.edit_outlined
                              : s.status == 'pending'
                                  ? Icons.schedule
                                  : Icons.cancel_outlined,
                      color: s.status == 'completed'
                          ? Colors.green
                          : s.status == 'draft'
                              ? Colors.orange
                              : s.status == 'pending'
                                  ? Colors.blue
                                  : Colors.red,
                      size: 22,
                    ),
                  ),
                  const SizedBox(width: 14),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(s.reference,
                          style: const TextStyle(
                            fontWeight: FontWeight.w700, fontSize: 14)),
                        const SizedBox(height: 3),
                        Text(s.customerName,
                          style: TextStyle(
                            color: theme.colorScheme.onSurface.withValues(alpha: 0.6),
                            fontSize: 13)),
                      ],
                    ),
                  ),
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.end,
                    children: [
                      Text('TSh ${_fmt.format(s.total)}',
                        style: const TextStyle(
                          fontWeight: FontWeight.w800, fontSize: 15)),
                      const SizedBox(height: 4),
                      StatusBadge.fromStatus(s.paymentStatus),
                    ],
                  ),
                ],
              ),
              const SizedBox(height: 12),
              const Divider(height: 1),
              const SizedBox(height: 12),
              Row(
                children: [
                  _infoChip(Icons.calendar_today_outlined, s.saleDate, theme),
                  const SizedBox(width: 8),
                  _infoChip(Icons.payment_outlined, s.paymentMethod, theme),
                  const Spacer(),
                  StatusBadge.fromStatus(s.status),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _infoChip(IconData icon, String label, ThemeData theme) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
      decoration: BoxDecoration(
        color: theme.colorScheme.surfaceVariant,
        borderRadius: BorderRadius.circular(12),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, size: 12,
            color: theme.colorScheme.onSurface.withValues(alpha: 0.6)),
          const SizedBox(width: 4),
          Text(label,
            style: TextStyle(
              color: theme.colorScheme.onSurface.withValues(alpha: 0.6),
              fontSize: 11,
              fontWeight: FontWeight.w600)),
        ],
      ),
    );
  }
}

// Sale detail bottom sheet
class _SaleDetailSheet extends StatelessWidget {
  final Sale sale;
  const _SaleDetailSheet({required this.sale});

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    final fmt = NumberFormat('#,##0');
    final items = sale.items ?? [];
    return Container(
      decoration: BoxDecoration(
        color: theme.scaffoldBackgroundColor,
        borderRadius: const BorderRadius.vertical(top: Radius.circular(24)),
      ),
      child: SingleChildScrollView(
        padding: const EdgeInsets.fromLTRB(24, 16, 24, 32),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Center(
              child: Container(
                width: 40, height: 4,
                decoration: BoxDecoration(
                  color: theme.colorScheme.outline.withValues(alpha: 0.3),
                  borderRadius: BorderRadius.circular(4))),
            ),
            const SizedBox(height: 20),
            Row(
              children: [
                Container(
                  width: 52, height: 52,
                  decoration: BoxDecoration(
                    color: theme.colorScheme.primary.withValues(alpha: 0.15),
                    borderRadius: BorderRadius.circular(14),
                  ),
                  child: Icon(Icons.receipt_outlined,
                    color: theme.colorScheme.primary, size: 28),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(sale.reference,
                        style: const TextStyle(
                          fontSize: 18, fontWeight: FontWeight.w700)),
                      const SizedBox(height: 4),
                      Text(sale.customerName,
                        style: TextStyle(
                          color: theme.colorScheme.onSurface.withValues(alpha: 0.6),
                          fontSize: 13)),
                    ],
                  ),
                ),
                StatusBadge.fromStatus(sale.status),
              ],
            ),
            const SizedBox(height: 20),
            const Divider(),
            const SizedBox(height: 16),
            const Text('Items',
              style: TextStyle(
                fontSize: 15, fontWeight: FontWeight.w700)),
            const SizedBox(height: 10),
            ...items.map((item) => Padding(
              padding: const EdgeInsets.only(bottom: 8),
              child: Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: theme.colorScheme.surfaceVariant,
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Row(
                  children: [
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(item['product_name'] ?? '',
                            style: const TextStyle(
                              fontSize: 14, fontWeight: FontWeight.w600)),
                          const SizedBox(height: 4),
                          Text('${item['quantity']} x TSh ${fmt.format(double.tryParse(item['unit_price']?.toString() ?? '0') ?? 0)}',
                            style: TextStyle(
                              color: theme.colorScheme.onSurface.withValues(alpha: 0.6),
                              fontSize: 12)),
                        ],
                      ),
                    ),
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                      decoration: BoxDecoration(
                        color: Colors.green.withValues(alpha: 0.1),
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: Text('TSh ${fmt.format(double.tryParse(item['total']?.toString() ?? '0') ?? 0)}',
                        style: const TextStyle(
                          fontWeight: FontWeight.w800, fontSize: 14,
                          color: Colors.green)),
                    ),
                  ],
                ),
              ),
            )),
            const Divider(),
            const SizedBox(height: 16),
            const Text('Financial Summary',
              style: TextStyle(
                fontSize: 15, fontWeight: FontWeight.w700)),
            const SizedBox(height: 12),
            _detailRow('Subtotal', fmt.format(sale.total)),
            _detailRow('Discount', fmt.format(sale.discountAmount)),
            _detailRow('Tax', fmt.format(sale.taxAmount)),
            _detailRow('Total', fmt.format(sale.totalAmount), isTotal: true),
            const SizedBox(height: 12),
            _detailRow('Paid', fmt.format(sale.paidAmount),
              color: Colors.green),
            _detailRow('Due', fmt.format(sale.totalAmount - sale.paidAmount),
              color: sale.totalAmount > sale.paidAmount ? Colors.red : Colors.green),
            const SizedBox(height: 12),
            _detailRow('Payment Method', sale.paymentMethod),
            _detailRow('Status', sale.status),
            _detailRow('Date', sale.saleDate),
          ],
        ),
      ),
    );
  }

  Widget _detailRow(String label, String value,
      {bool isTotal = false, Color? color}) {
    final theme = Theme.of(context);
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 6),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(label,
            style: TextStyle(
              color: theme.colorScheme.onSurface.withValues(alpha: 0.6),
              fontSize: 14)),
          Text(value,
            style: TextStyle(
              fontWeight: isTotal ? FontWeight.w800 : FontWeight.w600,
              fontSize: isTotal ? 16 : 14,
              color: color ?? theme.colorScheme.onSurface)),
        ],
      ),
    );
  }
}
