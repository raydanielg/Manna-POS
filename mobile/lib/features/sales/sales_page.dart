import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../core/api_service.dart';
import '../../shared/theme/app_theme.dart';
import '../../shared/models/sale.dart';
import '../../shared/widgets/app_card.dart';
import '../../shared/widgets/empty_state.dart';
import '../../shared/widgets/search_bar_widget.dart';
import '../../shared/widgets/status_badge.dart';
import '../../shared/constants/app_constants.dart';

class SalesPage extends StatefulWidget {
  const SalesPage({super.key});
  @override State<SalesPage> createState() => _SalesPageState();
}

class _SalesPageState extends State<SalesPage> with SingleTickerProviderStateMixin {
  List<Sale> _sales = [];
  bool _loading = true;
  String? _error;
  String _search = '';
  String _status = '';
  final fmt = NumberFormat('#,##0.00');
  late TabController _tabs;
  DateTime? _selectedDate;
  bool _showFilters = false;

  @override
  void initState() { super.initState(); _tabs = TabController(length: 4, vsync: this); _tabs.addListener(() { if (!_tabs.indexIsChanging) { _status = ['', 'completed', 'draft', 'cancelled'][_tabs.index]; _load(); } }); _load(); }
  @override
  void dispose() { _tabs.dispose(); super.dispose(); }

  Future<void> _load() async {
    setState(() { _loading = true; _error = null; });
    try {
      String apiUrl = '/sales?status=$_status';
      if (_search.isNotEmpty) apiUrl += '&search=${Uri.encodeComponent(_search)}';
      final data = await ApiService.get(apiUrl);
      setState(() { _sales = (data as List).map((e) => Sale.fromJson(e)).toList(); _loading = false; });
    } on ApiException catch (e) { setState(() { _error = e.message; _loading = false; }); }
    catch (_) { setState(() { _error = 'Connection error'; _loading = false; }); }
  }

  void _clearSearch() {
    setState(() { _search = ''; _load(); });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(
        title: const Text('Sales'),
        actions: [
          IconButton(
            icon: Icon(_showFilters ? Icons.filter_alt : Icons.filter_alt_outlined, color: Colors.white),
            onPressed: () => setState(() => _showFilters = !_showFilters),
          ),
          IconButton(icon: const Icon(Icons.refresh, color: Colors.white), onPressed: _load),
        ],
        bottom: TabBar(controller: _tabs, labelColor: Colors.white, unselectedLabelColor: Colors.white70, indicatorColor: Colors.white, indicatorWeight: 3,
          tabs: const [Tab(text: 'All'), Tab(text: 'Completed'), Tab(text: 'Draft'), Tab(text: 'Cancelled')]),
      ),
      body: Column(children: [
        AnimatedContainer(
          duration: const Duration(milliseconds: 300),
          height: _showFilters ? 140 : 0,
          child: _showFilters
              ? Container(color: AppColors.surface, padding: const EdgeInsets.fromLTRB(16, 8, 16, 16),
                  child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                    const Text('Filters', style: TextStyle(fontSize: 14, fontWeight: FontWeight.w600, color: AppColors.textPri)),
                    const SizedBox(height: 8),
                    TextField(
                      decoration: InputDecoration(
                        hintText: 'Search by invoice, customer...',
                        prefixIcon: const Icon(Icons.search, size: 20),
                        suffixIcon: _search.isNotEmpty
                            ? IconButton(icon: const Icon(Icons.clear, size: 18), onPressed: _clearSearch)
                            : null,
                        border: OutlineInputBorder(borderRadius: BorderRadius.circular(10), borderSide: BorderSide(color: AppColors.border)),
                        contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                      ),
                      onChanged: (v) { setState(() => _search = v); _load(); },
                    ),
                    const SizedBox(height: 8),
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                      decoration: BoxDecoration(color: AppColors.bg, borderRadius: BorderRadius.circular(8)),
                      child: Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
                        const Text('Status: ', style: TextStyle(fontSize: 13, color: AppColors.textSec)),
                        Text('${_tabs.index == 0 ? 'All' : _status}', style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w600, color: AppColors.primary)),
                      ]),
                    ),
                  ]))
              : const SizedBox(),
        ),
        Padding(
          padding: const EdgeInsets.fromLTRB(16, 8, 16, 8),
          child: Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
            Text('${_sales.length} ${_sales.length == 1 ? 'Sale' : 'Sales'}', style: const TextStyle(fontSize: 13, color: AppColors.textSec)),
            if (_loading) const Text('Updating...', style: TextStyle(fontSize: 12, color: AppColors.primary))
          ]),
        ),
        Expanded(child: _loading ? const LoadingWidget(message: 'Loading sales...')
          : _error != null ? ErrorWidget2(message: _error!, onRetry: _load)
          : _sales.isEmpty ? const EmptyState(icon: Icons.receipt_long_outlined, title: 'No Sales Found', subtitle: 'Completed sales will appear here')
          : RefreshIndicator(color: AppColors.primary, onRefresh: _load,
              child: ListView.separated(padding: const EdgeInsets.fromLTRB(16, 0, 16, 100), itemCount: _sales.length, separatorBuilder: (_, __) => const SizedBox(height: `${_sales.length}`), itemBuilder: (_, i) => _saleTile(_sales[i])))),
      ]),
    );
  }

  Widget _saleTile(Sale s) {
    return AppCard(
      onTap: () => _showDetail(s),
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          children: [
            Row(
              children: [
                Container(
                  width: 46,
                  height: 46,
                  decoration: BoxDecoration(
                    color: s.status == 'completed' ? AppColors.successLt : s.status == 'draft' ? AppColors.warningLt : AppColors.dangerLt,
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: Icon(
                    s.status == 'completed' ? Icons.check_circle_outline : s.status == 'draft' ? Icons.edit_outlined : Icons.cancel_outlined,
                    color: s.status == 'completed' ? AppColors.success : s.status == 'draft' ? AppColors.warning : AppColors.danger,
                    size: 22,
                  ),
                ),
                const SizedBox(width: 14),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        s.reference,
                        style: const TextStyle(
                          fontWeight: FontWeight.w700,
                          fontSize: 14,
                          color: AppColors.textPri,
                        ),
                      ),
                      const SizedBox(height: 3),
                      Text(
                        s.customerName,
                        style: const TextStyle(
                          color: AppColors.textSec,
                          fontSize: 13,
                        ),
                      ),
                    ],
                  ),
                ),
                Column(
                  crossAxisAlignment: CrossAxisAlignment.end,
                  children: [
                    Text(
                      '${AppConstants.currency} ${fmt.format(s.total)}',
                      style: TextStyle(
                        fontWeight: FontWeight.w800,
                        fontSize: 15,
                        color: AppColors.textPri,
                      ),
        ),
          : RefreshIndicator(color: AppColors.primary, onRefresh: _load,
              child: ListView.separated(padding: const EdgeInsets.fromLTRB(16, 0, 16, 100), itemCount: _sales.length, separatorBuilder: (_, __) => const SizedBox(height: 10), itemBuilder: (_, i) => _saleTile(_sales[i]))),
      ]),
    );
  }

  Widget _enhancedChip(IconData icon, String label, Color color) => Container(
    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
    decoration: BoxDecoration(
      color: color.withValues(alpha: 0.08),
      borderRadius: BorderRadius.circular(12),
    ),
    child: Row(
      mainAxisSize: MainAxisSize.min,
      children: [
        Icon(icon, size: 12, color: color),
        const SizedBox(width: 4),
        Text(
          label,
          style: TextStyle(color: color, fontSize: 11, fontWeight: FontWeight.w600),
        ),
      ],
    ),
  );

  void _showDetail(Sale s) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (_) => _SaleDetail(sale: s),
    );
  }
}

class _SaleDetail extends StatefulWidget {
  final Sale sale;
  const _SaleDetail({required this.sale});
  @override State<_SaleDetail> createState() => _SaleDetailState();
}

class _SaleDetailState extends State<_SaleDetail> {
  Sale? _detail;
  bool _loading = true;
  final fmt = NumberFormat('#,##0.00');

  @override
  void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    try {
      final data = await ApiService.get('/sales/${widget.sale.id}');
      setState(() { _detail = Sale.fromJson(data); _loading = false; });
    } catch (_) { setState(() { _detail = widget.sale; _loading = false; }); }
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: const BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
      ),
      child: _loading
          ? const Padding(
              padding: EdgeInsets.all(60),
              child: LoadingWidget(),
            )
          : SingleChildScrollView(
              padding: const EdgeInsets.fromLTRB(24, 16, 24, 32),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Center(
                    child: Container(
                      width: 40,
                      height: 4,
                      decoration: BoxDecoration(
                        color: AppColors.border,
                        borderRadius: BorderRadius.circular(4),
                      ),
                    ),
                  ),
                  const SizedBox(height: 20),
                  Row(
                    children: [
                      Container(
                        width: 52,
                        height: 52,
                        decoration: BoxDecoration(
                          color: AppColors.primaryLt,
                          borderRadius: BorderRadius.circular(14),
                        ),
                        child: const Icon(Icons.receipt_outlined, color: AppColors.primary, size: 28),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              _detail!.reference,
                              style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w700, color: AppColors.textPri),
                            ),
                            const SizedBox(height: 4),
                            Text(
                              _detail!.customerName,
                              style: const TextStyle(color: AppColors.textSec, fontSize: 13),
                            ),
                          ],
                        ),
                      ),
                      StatusBadge.fromStatus(_detail!.status),
                    ],
                  ),
                  const SizedBox(height: 20),
                  const Divider(),
                  const SizedBox(height: 16),
                  const Text('Items', style: TextStyle(fontSize: 15, fontWeight: FontWeight.w700, color: AppColors.textPri)),
                  const SizedBox(height: 10),
                  ...(_detail!.items ?? []).map(
                    (item) => Padding(
                      padding: const EdgeInsets.only(bottom: 8),
                      child: Container(
                        padding: const EdgeInsets.all(12),
                        decoration: BoxDecoration(
                          color: AppColors.bg,
                          borderRadius: BorderRadius.circular(12),
                          border: Border.all(color: AppColors.border.withValues(alpha: 0.5)),
                        ),
                        child: Row(
                          children: [
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(
                                    item['product_name'] ?? '',
                                    style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w600, color: AppColors.textPri),
                                  ),
                                  const SizedBox(height: 4),
                                  Text(
                                    '${item['quantity']} x TSh ${fmt.format(double.tryParse(item['unit_price']?.toString() ?? '0') ?? 0)}',
                                    style: const TextStyle(color: AppColors.textSec, fontSize: 12),
                                  ),
                                ],
                              ),
                            ),
                            Container(
                              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                              decoration: BoxDecoration(
                                color: AppColors.successLt,
                                borderRadius: BorderRadius.circular(8),
                              ),
                              child: Text(
                                'TSh ${fmt.format(double.tryParse(item['total']?.toString() ?? '0') ?? 0)}',
                                style: const TextStyle(
                                  fontWeight: FontWeight.w800,
                                  fontSize: 14,
                                  color: AppColors.success,
                                ),
                              ),
                            ),
                          ],
                        ),
                      ),
                    ),
                  ),
                  const Divider(),
                  const SizedBox(height: 16),
                  const Text('Financial Summary', style: TextStyle(fontSize: 15, fontWeight: FontWeight.w700, color: AppColors.textPri)),
                  const SizedBox(height: 12),
                  _enhancedRow('Subtotal', fmt.format(_detail!.total)),
                  _enhancedRow('Discount', '0.00'),
                  _enhancedRow('Tax (18%)', fmt.format(_detail!.total * 0.18)),
                  _enhancedRow('Total Amount', fmt.format(_detail!.total), isTotal: true),
                  const SizedBox(height: 12),
                  _enhancedRow('Paid Amount', fmt.format(_detail!.paid), color: AppColors.success),
                  _enhancedRow('Due Amount', fmt.format(_detail!.outstanding), color: _detail!.outstanding > 0 ? AppColors.danger : AppColors.success),
                  const SizedBox(height: 12),
                  _enhancedRow('Payment Method', _detail!.paymentMethod),
                  _enhancedRow('Payment Status', _detail!.paymentStatus),
                  _enhancedRow('Sale Date', _detail!.saleDate),
                ],
              ),
            ),
    );
  }

  Widget _enhancedRow(String label, String value, {bool isTotal = false, Color? color}) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 6),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(
            label,
            style: const TextStyle(
              color: AppColors.textSec,
              fontSize: 14,
            ),
          ),
          Text(
            value,
            style: TextStyle(
              fontWeight: isTotal ? FontWeight.w800 : FontWeight.w600,
              fontSize: isTotal ? 16 : 14,
              color: color ?? AppColors.textPri,
            ),
          ),
        ],
      ),
    );
  }
}