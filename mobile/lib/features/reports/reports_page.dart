import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../core/api_service.dart';
import '../../shared/theme/app_theme.dart';
import '../../shared/widgets/app_card.dart';
import '../../shared/widgets/empty_state.dart';

class ReportsPage extends StatefulWidget {
  const ReportsPage({super.key});
  @override State<ReportsPage> createState() => _ReportsPageState();
}

class _ReportsPageState extends State<ReportsPage> with SingleTickerProviderStateMixin {
  late TabController _tab;
  @override void initState() { super.initState(); _tab = TabController(length: 3, vsync: this); }
  @override void dispose() { _tab.dispose(); super.dispose(); }

  @override
  Widget build(BuildContext context) => Scaffold(
    backgroundColor: AppColors.bg,
    appBar: AppBar(
      title: const Text('Reports'),
      bottom: TabBar(controller: _tab, indicatorColor: Colors.white, labelColor: Colors.white, unselectedLabelColor: Colors.white70,
        tabs: const [Tab(text: 'Sales'), Tab(text: 'Profit & Loss'), Tab(text: 'Inventory')]),
    ),
    body: TabBarView(controller: _tab, children: const [_SalesReport(), _ProfitLossReport(), _InventoryReport()]),
  );
}

class _DateFilter extends StatelessWidget {
  final TextEditingController from, to;
  final VoidCallback onFilter;
  const _DateFilter({required this.from, required this.to, required this.onFilter});
  @override
  Widget build(BuildContext context) => Container(color: AppColors.surface, padding: const EdgeInsets.fromLTRB(16, 12, 16, 12),
    child: Row(children: [
      Expanded(child: TextFormField(controller: from, readOnly: true, decoration: const InputDecoration(labelText: 'From', isDense: true, suffixIcon: Icon(Icons.calendar_today, size: 16)),
        onTap: () async { final d = await showDatePicker(context: context, initialDate: DateTime.now().subtract(const Duration(days: 30)), firstDate: DateTime(2020), lastDate: DateTime(2030)); if (d != null) from.text = DateFormat('yyyy-MM-dd').format(d); })),
      const SizedBox(width: 10),
      Expanded(child: TextFormField(controller: to, readOnly: true, decoration: const InputDecoration(labelText: 'To', isDense: true, suffixIcon: Icon(Icons.calendar_today, size: 16)),
        onTap: () async { final d = await showDatePicker(context: context, initialDate: DateTime.now(), firstDate: DateTime(2020), lastDate: DateTime(2030)); if (d != null) to.text = DateFormat('yyyy-MM-dd').format(d); })),
      const SizedBox(width: 10),
      ElevatedButton(onPressed: onFilter, style: ElevatedButton.styleFrom(padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12)), child: const Text('Filter')),
    ]));
}

// ─── Sales Report ─────────────────────────────────────────────────────────────
class _SalesReport extends StatefulWidget {
  const _SalesReport();
  @override State<_SalesReport> createState() => _SalesReportState();
}
class _SalesReportState extends State<_SalesReport> {
  Map<String, dynamic>? _data;
  bool _loading = false;
  String? _error;
  late TextEditingController _from, _to;
  final _fmt = NumberFormat('#,##0.00');

  @override
  void initState() {
    super.initState();
    _from = TextEditingController(text: DateFormat('yyyy-MM-dd').format(DateTime.now().subtract(const Duration(days: 30))));
    _to = TextEditingController(text: DateFormat('yyyy-MM-dd').format(DateTime.now()));
    _load();
  }
  @override void dispose() { _from.dispose(); _to.dispose(); super.dispose(); }

  Future<void> _load() async {
    setState(() { _loading = true; _error = null; });
    try {
      final d = await ApiService.get('/reports/sales?from=${_from.text}&to=${_to.text}');
      setState(() { _data = d; _loading = false; });
    } on ApiException catch (e) { setState(() { _error = e.message; _loading = false; }); }
    catch (_) { setState(() { _error = 'Connection error'; _loading = false; }); }
  }

  @override
  Widget build(BuildContext context) => Column(children: [
    _DateFilter(from: _from, to: _to, onFilter: _load),
    Expanded(child: _loading ? _loadingWidget(message: 'Loading report...')
      : _error != null ? _errorWidget(_error!, _load)
      : _data == null ? const EmptyState(icon: Icons.bar_chart_outlined, title: 'No Data', subtitle: 'Select a date range and filter')
      : SingleChildScrollView(padding: const EdgeInsets.all(16), child: Column(children: [
          Row(children: [
            Expanded(child: _statCard('Total Sales', 'TSh ${_fmt.format((_data!['summary']?['total_revenue'] ?? 0).toDouble())}', Icons.trending_up, AppColors.success, AppColors.successLt)),
            const SizedBox(width: 12),
            Expanded(child: _statCard('Orders', '${_data!['summary']?['total_orders'] ?? 0}', Icons.receipt_long_outlined, AppColors.primary, AppColors.primaryLt)),
          ]),
          const SizedBox(height: 12),
          Row(children: [
            Expanded(child: _statCard('Avg Order', 'TSh ${_fmt.format((_data!['summary']?['average_order'] ?? 0).toDouble())}', Icons.bar_chart, AppColors.warning, AppColors.warningLt)),
            const SizedBox(width: 12),
            Expanded(child: _statCard('Paid', 'TSh ${_fmt.format((_data!['summary']?['total_paid'] ?? 0).toDouble())}', Icons.payment, AppColors.secondary, const Color(0xFFF5F3FF))),
          ]),
          const SizedBox(height: 20),
          AppCard(child: Padding(padding: const EdgeInsets.all(16), child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            const Text('Sales Details', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 16)),
            const SizedBox(height: 12),
            ...(_data!['sales'] as List? ?? []).map((s) => Padding(padding: const EdgeInsets.symmetric(vertical: 6), child: Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
              Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [Text(s['reference'] ?? '-', style: const TextStyle(fontWeight: FontWeight.w600)), Text(s['sale_date'] ?? '', style: const TextStyle(color: AppColors.textSec, fontSize: 12))])),
              Text('TSh ${_fmt.format((s['grand_total'] ?? 0).toDouble())}', style: const TextStyle(fontWeight: FontWeight.w700, color: AppColors.success)),
            ]))),
          ]))),
        ]))),
  ]);

  Widget _statCard(String label, String value, IconData icon, Color color, Color bg) => AppCard(child: Padding(padding: const EdgeInsets.all(16), child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
    Container(width: 40, height: 40, decoration: BoxDecoration(color: bg, borderRadius: BorderRadius.circular(10)), child: Icon(icon, color: color, size: 20)),
    const SizedBox(height: 10),
    Text(value, style: TextStyle(fontWeight: FontWeight.w800, fontSize: 18, color: color)),
    Text(label, style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
  ])));
}

// ─── Profit & Loss ────────────────────────────────────────────────────────────
class _ProfitLossReport extends StatefulWidget {
  const _ProfitLossReport();
  @override State<_ProfitLossReport> createState() => _ProfitLossReportState();
}
class _ProfitLossReportState extends State<_ProfitLossReport> {
  Map<String, dynamic>? _data;
  bool _loading = false;
  String? _error;
  late TextEditingController _from, _to;
  final _fmt = NumberFormat('#,##0.00');

  @override
  void initState() {
    super.initState();
    _from = TextEditingController(text: DateFormat('yyyy-MM-dd').format(DateTime.now().subtract(const Duration(days: 30))));
    _to = TextEditingController(text: DateFormat('yyyy-MM-dd').format(DateTime.now()));
    _load();
  }
  @override void dispose() { _from.dispose(); _to.dispose(); super.dispose(); }

  Future<void> _load() async {
    setState(() { _loading = true; _error = null; });
    try {
      final d = await ApiService.get('/reports/profit-loss?from=${_from.text}&to=${_to.text}');
      setState(() { _data = d; _loading = false; });
    } on ApiException catch (e) { setState(() { _error = e.message; _loading = false; }); }
    catch (_) { setState(() { _error = 'Connection error'; _loading = false; }); }
  }

  @override
  Widget build(BuildContext context) => Column(children: [
    _DateFilter(from: _from, to: _to, onFilter: _load),
    Expanded(child: _loading ? _loadingWidget(message: 'Loading report...')
      : _error != null ? _errorWidget(_error!, _load)
      : _data == null ? const EmptyState(icon: Icons.show_chart, title: 'No Data', subtitle: 'Select a date range')
      : SingleChildScrollView(padding: const EdgeInsets.all(16), child: Column(children: [
          _plCard('Revenue', _data!['revenue'] ?? 0, AppColors.success, AppColors.successLt, Icons.trending_up),
          const SizedBox(height: 12),
          _plCard('Cost of Goods', _data!['cost'] ?? 0, AppColors.danger, AppColors.dangerLt, Icons.inventory_2_outlined),
          const SizedBox(height: 12),
          _plCard('Expenses', _data!['expenses'] ?? 0, AppColors.warning, AppColors.warningLt, Icons.receipt_outlined),
          const SizedBox(height: 16),
          AppCard(child: Container(decoration: BoxDecoration(gradient: LinearGradient(colors: [AppColors.primary, AppColors.primaryDk], begin: Alignment.topLeft, end: Alignment.bottomRight), borderRadius: BorderRadius.circular(14)),
            padding: const EdgeInsets.all(20), child: Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
              const Column(crossAxisAlignment: CrossAxisAlignment.start, children: [Text('Net Profit', style: TextStyle(color: Colors.white70, fontSize: 14)), Text('After all deductions', style: TextStyle(color: Colors.white54, fontSize: 12))]),
              Text('TSh ${_fmt.format((_data!['net_profit'] ?? 0).toDouble())}', style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w900, fontSize: 20)),
            ]))),
        ]))),
  ]);

  Widget _plCard(String label, dynamic val, Color color, Color bg, IconData icon) => AppCard(child: Padding(padding: const EdgeInsets.all(16), child: Row(children: [
    Container(width: 44, height: 44, decoration: BoxDecoration(color: bg, borderRadius: BorderRadius.circular(12)), child: Icon(icon, color: color)),
    const SizedBox(width: 14),
    Expanded(child: Text(label, style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 15))),
    Text('TSh ${_fmt.format((val as num).toDouble())}', style: TextStyle(fontWeight: FontWeight.w800, fontSize: 16, color: color)),
  ])));
}

// ─── Inventory Report ─────────────────────────────────────────────────────────
class _InventoryReport extends StatefulWidget {
  const _InventoryReport();
  @override State<_InventoryReport> createState() => _InventoryReportState();
}
class _InventoryReportState extends State<_InventoryReport> {
  List<dynamic> _products = [];
  Map<String, dynamic> _summary = {};
  bool _loading = true;
  String? _error;
  final _fmt = NumberFormat('#,##0.00');

  @override void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    setState(() { _loading = true; _error = null; });
    try {
      final d = await ApiService.get('/reports/inventory') as Map<String, dynamic>;
      setState(() { _summary = d['summary'] ?? {}; _products = d['products'] ?? []; _loading = false; });
    } on ApiException catch (e) { setState(() { _error = e.message; _loading = false; }); }
    catch (_) { setState(() { _error = 'Connection error'; _loading = false; }); }
  }

  @override
  Widget build(BuildContext context) => _loading ? _loadingWidget(message: 'Loading inventory...')
    : _error != null ? _errorWidget(_error!, _load)
    : RefreshIndicator(color: AppColors.primary, onRefresh: _load, child: CustomScrollView(slivers: [
        SliverToBoxAdapter(child: Padding(padding: const EdgeInsets.all(16), child: Column(children: [
          Row(children: [
            Expanded(child: _summCard('Products', '${_summary['total_products'] ?? 0}', Icons.inventory_2_outlined, AppColors.primary, AppColors.primaryLt)),
            const SizedBox(width: 12),
            Expanded(child: _summCard('Stock Value', 'TSh ${_fmt.format((_summary['total_value'] ?? 0).toDouble())}', Icons.attach_money, AppColors.success, AppColors.successLt)),
          ]),
          const SizedBox(height: 12),
          Row(children: [
            Expanded(child: _summCard('Low Stock', '${_summary['low_stock'] ?? 0}', Icons.warning_outlined, AppColors.warning, AppColors.warningLt)),
            const SizedBox(width: 12),
            Expanded(child: _summCard('Out of Stock', '${_summary['out_of_stock'] ?? 0}', Icons.remove_shopping_cart_outlined, AppColors.danger, AppColors.dangerLt)),
          ]),
          const SizedBox(height: 20),
        ]))),
        SliverList(delegate: SliverChildBuilderDelegate((context, i) {
          final p = _products[i];
          return Padding(padding: const EdgeInsets.fromLTRB(16, 0, 16, 10), child: AppCard(child: Padding(padding: const EdgeInsets.all(14), child: Row(children: [
            Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
              Text(p['name'] ?? '', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14)),
              Text('SKU: ${p['sku'] ?? '-'}', style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
            ])),
            Column(crossAxisAlignment: CrossAxisAlignment.end, children: [
              Text('${p['stock_quantity'] ?? 0} units', style: const TextStyle(fontWeight: FontWeight.w700)),
              Text('TSh ${_fmt.format(((p['cost_price'] ?? 0) * (p['stock_quantity'] ?? 0)).toDouble())}', style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
            ]),
          ]))));
        }, childCount: _products.length)),
      ]));

  Widget _summCard(String label, String value, IconData icon, Color color, Color bg) => AppCard(child: Padding(padding: const EdgeInsets.all(14), child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
    Container(width: 36, height: 36, decoration: BoxDecoration(color: bg, borderRadius: BorderRadius.circular(10)), child: Icon(icon, color: color, size: 18)),
    const SizedBox(height: 8),
    Text(value, style: TextStyle(fontWeight: FontWeight.w800, fontSize: 16, color: color)),
    Text(label, style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
  ])));
}

// Loading Widget
Widget _loadingWidget({String message = 'Loading...'}) => Container(
  padding: const EdgeInsets.all(60),
  child: Column(
    mainAxisAlignment: MainAxisAlignment.center,
    children: [
      const CircularProgressIndicator(color: AppColors.primary),
      const SizedBox(height: 16),
      Text(
        message,
        style: const TextStyle(color: AppColors.textSec, fontSize: 14),
      ),
    ],
  ),
);

// Error Widget
Widget _errorWidget(String message, VoidCallback onRetry) => Container(
  padding: const EdgeInsets.all(24),
  child: Column(
    mainAxisAlignment: MainAxisAlignment.center,
    children: [
      Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: AppColors.dangerLt,
          borderRadius: BorderRadius.circular(12),
          border: Border.all(color: AppColors.danger.withValues(alpha: 0.3)),
        ),
        child: Column(
          children: [
            const Icon(Icons.error_outline, color: AppColors.danger, size: 48),
            const SizedBox(height: 12),
            Text(
              'Error',
              style: const TextStyle(
                color: AppColors.danger,
                fontSize: 18,
                fontWeight: FontWeight.w700,
              ),
            ),
            const SizedBox(height: 8),
            Text(
              message,
              style: const TextStyle(
                color: AppColors.danger,
                fontSize: 14,
              ),
              textAlign: TextAlign.center,
            ),
            const SizedBox(height: 20),
            ElevatedButton(
              onPressed: onRetry,
              style: ElevatedButton.styleFrom(
                backgroundColor: AppColors.danger,
                foregroundColor: Colors.white,
                padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(8),
                ),
              ),
              child: const Text('Retry'),
            ),
          ],
        ),
      ),
    ],
  ),
);
