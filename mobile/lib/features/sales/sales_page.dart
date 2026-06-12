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

  @override
  void initState() { super.initState(); _tabs = TabController(length: 4, vsync: this); _tabs.addListener(() { if (!_tabs.indexIsChanging) { _status = ['', 'completed', 'draft', 'cancelled'][_tabs.index]; _load(); } }); _load(); }
  @override
  void dispose() { _tabs.dispose(); super.dispose(); }

  Future<void> _load() async {
    setState(() { _loading = true; _error = null; });
    try {
      final data = await ApiService.get('/sales?search=${Uri.encodeComponent(_search)}&status=$_status');
      setState(() { _sales = (data as List).map((e) => Sale.fromJson(e)).toList(); _loading = false; });
    } on ApiException catch (e) { setState(() { _error = e.message; _loading = false; }); }
    catch (_) { setState(() { _error = 'Connection error'; _loading = false; }); }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(
        title: const Text('Sales'),
        actions: [IconButton(icon: const Icon(Icons.refresh, color: Colors.white), onPressed: _load)],
        bottom: TabBar(controller: _tabs, labelColor: Colors.white, unselectedLabelColor: Colors.white70, indicatorColor: Colors.white, indicatorWeight: 3,
          tabs: const [Tab(text: 'All'), Tab(text: 'Completed'), Tab(text: 'Draft'), Tab(text: 'Cancelled')]),
      ),
      body: Column(children: [
        Padding(padding: const EdgeInsets.all(16), child: SearchBarWidget(hint: 'Search by invoice...', onChanged: (v) { _search = v; _load(); })),
        Expanded(child: _loading ? const LoadingWidget(message: 'Loading sales...')
          : _error != null ? ErrorWidget2(message: _error!, onRetry: _load)
          : _sales.isEmpty ? const EmptyState(icon: Icons.receipt_long_outlined, title: 'No Sales Found', subtitle: 'Completed sales will appear here')
          : RefreshIndicator(color: AppColors.primary, onRefresh: _load,
              child: ListView.separated(padding: const EdgeInsets.fromLTRB(16, 0, 16, 100), itemCount: _sales.length, separatorBuilder: (_, __) => const SizedBox(height: 10), itemBuilder: (_, i) => _saleTile(_sales[i])))),
      ]),
    );
  }

  Widget _saleTile(Sale s) {
    return AppCard(
      onTap: () => _showDetail(s),
      child: Padding(padding: const EdgeInsets.all(16), child: Column(children: [
        Row(children: [
          Container(width: 46, height: 46, decoration: BoxDecoration(color: AppColors.primaryLt, borderRadius: BorderRadius.circular(12)),
            child: const Icon(Icons.receipt_outlined, color: AppColors.primary, size: 22)),
          const SizedBox(width: 14),
          Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            Text(s.reference, style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14)),
            const SizedBox(height: 3),
            Text(s.customerName, style: const TextStyle(color: AppColors.textSec, fontSize: 13)),
          ])),
          Column(crossAxisAlignment: CrossAxisAlignment.end, children: [
            Text('${AppConstants.currency} ${fmt.format(s.total)}', style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 15, color: AppColors.textPri)),
            const SizedBox(height: 4),
            StatusBadge.fromStatus(s.status),
          ]),
        ]),
        const SizedBox(height: 10),
        const Divider(height: 1),
        const SizedBox(height: 10),
        Row(children: [
          _chip(Icons.calendar_today_outlined, s.saleDate, AppColors.textSec),
          const SizedBox(width: 12),
          _chip(Icons.payment_outlined, s.paymentMethod, AppColors.textSec),
          const Spacer(),
          StatusBadge.fromStatus(s.paymentStatus),
        ]),
      ])),
    );
  }

  Widget _chip(IconData icon, String label, Color color) => Row(children: [Icon(icon, size: 13, color: color), const SizedBox(width: 4), Text(label, style: TextStyle(color: color, fontSize: 12))]);

  void _showDetail(Sale s) {
    showModalBottomSheet(context: context, isScrollControlled: true, backgroundColor: Colors.transparent,
      builder: (_) => _SaleDetail(sale: s));
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
      decoration: const BoxDecoration(color: AppColors.surface, borderRadius: BorderRadius.vertical(top: Radius.circular(24))),
      child: _loading ? const Padding(padding: EdgeInsets.all(60), child: LoadingWidget())
        : SingleChildScrollView(padding: const EdgeInsets.fromLTRB(24, 16, 24, 32), child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            Center(child: Container(width: 40, height: 4, decoration: BoxDecoration(color: AppColors.border, borderRadius: BorderRadius.circular(4)))),
            const SizedBox(height: 20),
            Row(children: [
              const Icon(Icons.receipt_outlined, color: AppColors.primary, size: 28),
              const SizedBox(width: 12),
              Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                Text(_detail!.reference, style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w700)),
                Text(_detail!.customerName, style: const TextStyle(color: AppColors.textSec, fontSize: 13)),
              ])),
              StatusBadge.fromStatus(_detail!.status),
            ]),
            const SizedBox(height: 20),
            const Divider(),
            const SizedBox(height: 16),
            const Text('Items', style: TextStyle(fontSize: 15, fontWeight: FontWeight.w700)),
            const SizedBox(height: 10),
            ...(_detail!.items ?? []).map((item) => Padding(padding: const EdgeInsets.only(bottom: 8), child: Row(children: [
              Expanded(child: Text(item['product_name'] ?? '', style: const TextStyle(fontSize: 14))),
              Text('${item['quantity']} x ${fmt.format(double.tryParse(item['unit_price']?.toString() ?? '0') ?? 0)}', style: const TextStyle(color: AppColors.textSec, fontSize: 13)),
              const SizedBox(width: 12),
              Text('${AppConstants.currency} ${fmt.format(double.tryParse(item['total']?.toString() ?? '0') ?? 0)}', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 13)),
            ]))),
            const Divider(),
            const SizedBox(height: 10),
            _row('Subtotal', fmt.format(_detail!.total)),
            const SizedBox(height: 6),
            _row('Paid', fmt.format(_detail!.paid), color: AppColors.success),
            _row('Outstanding', fmt.format(_detail!.outstanding), color: _detail!.outstanding > 0 ? AppColors.danger : AppColors.success),
            const SizedBox(height: 6),
            _row('Payment', _detail!.paymentMethod),
            _row('Date', _detail!.saleDate),
          ])),
    );
  }

  Widget _row(String l, String v, {Color? color}) => Padding(padding: const EdgeInsets.only(bottom: 6), child: Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [Text(l, style: const TextStyle(color: AppColors.textSec, fontSize: 14)), Text(v, style: TextStyle(fontWeight: FontWeight.w600, fontSize: 14, color: color ?? AppColors.textPri))]));
}