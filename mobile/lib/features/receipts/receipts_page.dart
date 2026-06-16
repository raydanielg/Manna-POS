import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../core/api_service.dart';
import '../../shared/theme/app_colors.dart';
import '../../shared/widgets/app_card.dart';
import '../../shared/widgets/empty_state.dart';
import '../../shared/widgets/search_bar_widget.dart';
import '../../shared/constants/app_constants.dart';

class ReceiptsPage extends StatefulWidget {
  const ReceiptsPage({super.key});
  @override State<ReceiptsPage> createState() => _ReceiptsPageState();
}

class _ReceiptsPageState extends State<ReceiptsPage> {
  List<dynamic> _sales = [];
  bool _loading = true;
  String? _error;
  String _search = '';
  final fmt = NumberFormat('#,##0.00');

  @override void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    setState(() { _loading = true; _error = null; });
    try {
      final data = await ApiService.get('/sales?search=${Uri.encodeComponent(_search)}&status=completed');
      setState(() { _sales = (data as List); _loading = false; });
    } on ApiException catch (e) { setState(() { _error = e.message; _loading = false; }); }
    catch (_) { setState(() { _error = 'Connection error'; _loading = false; }); }
  }

  void _showReceipt(dynamic s) {
    showModalBottomSheet(context: context, isScrollControlled: true, backgroundColor: Colors.transparent,
      builder: (_) => _ReceiptView(sale: s));
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(title: const Text('Receipts'), actions: [IconButton(icon: const Icon(Icons.refresh, color: Colors.white), onPressed: _load)]),
      body: Column(children: [
        Padding(padding: const EdgeInsets.all(16), child: SearchBarWidget(hint: 'Search by invoice...', onChanged: (v) { _search = v; _load(); })),
        Expanded(child: _loading ? const LoadingWidget(message: 'Loading receipts...')
          : _error != null ? ErrorWidget2(message: _error!, onRetry: _load)
          : _sales.isEmpty ? const EmptyState(icon: Icons.receipt_long_outlined, title: 'No Receipts', subtitle: 'Completed sales will appear here')
          : RefreshIndicator(color: AppColors.primary, onRefresh: _load,
              child: ListView.separated(padding: const EdgeInsets.fromLTRB(16, 0, 16, 100), itemCount: _sales.length, separatorBuilder: (_, __) => const SizedBox(height: 10), itemBuilder: (_, i) => _tile(_sales[i])))),
      ]),
    );
  }

  Widget _tile(dynamic s) => AppCard(onTap: () => _showReceipt(s), child: Padding(padding: const EdgeInsets.all(16), child: Row(children: [
    Container(width: 46, height: 46, decoration: BoxDecoration(color: AppColors.primaryLt, borderRadius: BorderRadius.circular(12)),
      child: const Icon(Icons.receipt_outlined, color: AppColors.primary, size: 22)),
    const SizedBox(width: 14),
    Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
      Text(s['reference'] ?? '', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14)),
      const SizedBox(height: 3),
      Text(s['customer'] != null ? s['customer']['name'] ?? '' : 'Walk-in Customer', style: const TextStyle(color: AppColors.textSec, fontSize: 13)),
    ])),
    Column(crossAxisAlignment: CrossAxisAlignment.end, children: [
      Text('${AppConstants.currency} ${fmt.format((s['total'] ?? 0).toDouble())}', style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 15, color: AppColors.textPri)),
      const SizedBox(height: 4),
      Text(_fmtDate(s['sale_date']), style: const TextStyle(color: AppColors.textSec, fontSize: 11)),
    ]),
  ])));

  String _fmtDate(String? d) {
    if (d == null) return '';
    try { return DateFormat('dd MMM yyyy').format(DateTime.parse(d)); } catch (_) { return d; }
  }
}

class _ReceiptView extends StatefulWidget {
  final dynamic sale;
  const _ReceiptView({required this.sale});
  @override State<_ReceiptView> createState() => _ReceiptViewState();
}

class _ReceiptViewState extends State<_ReceiptView> {
  dynamic _detail;
  bool _loading = true;
  final fmt = NumberFormat('#,##0.00');

  @override void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    try {
      final data = await ApiService.get('/sales/${widget.sale['id']}');
      setState(() { _detail = data; _loading = false; });
    } catch (_) { setState(() { _detail = widget.sale; _loading = false; }); }
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: const BoxDecoration(color: AppColors.surface, borderRadius: BorderRadius.vertical(top: Radius.circular(24))),
      child: _loading ? const Padding(padding: EdgeInsets.all(60), child: LoadingWidget())
        : SingleChildScrollView(padding: const EdgeInsets.fromLTRB(24, 16, 24, 32), child: Column(children: [
            Center(child: Container(width: 40, height: 4, decoration: BoxDecoration(color: AppColors.border, borderRadius: BorderRadius.circular(4)))),
            const SizedBox(height: 20),
            // Receipt Header
            Container(width: double.infinity, padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(color: AppColors.primaryLt, borderRadius: BorderRadius.circular(16)),
              child: Column(children: [
                Container(width: 56, height: 56, decoration: BoxDecoration(color: AppColors.primary, shape: BoxShape.circle),
                  child: const Icon(Icons.receipt_long_rounded, color: Colors.white, size: 28)),
                const SizedBox(height: 12),
                Text(_detail!['reference'] ?? '', style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w800, color: AppColors.primary)),
                const SizedBox(height: 4),
                Text('Date: ${_fmtDate(_detail!['sale_date'])}', style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
              ]),
            ),
            const SizedBox(height: 20),
            // Customer Info
            Container(width: double.infinity, padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(color: AppColors.surfaceVariant, borderRadius: BorderRadius.circular(12)),
              child: Row(children: [
                const Icon(Icons.person_outlined, size: 16, color: AppColors.textSec),
                const SizedBox(width: 8),
                Text(_detail!['customer'] != null ? _detail!['customer']['name'] ?? '' : 'Walk-in Customer', style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 14)),
                const Spacer(),
                Text(_detail!['status'] ?? '', style: const TextStyle(color: AppColors.success, fontWeight: FontWeight.w600, fontSize: 12)),
              ]),
            ),
            const SizedBox(height: 20),
            // Items
            const Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
              Text('Item', style: TextStyle(fontSize: 13, fontWeight: FontWeight.w700, color: AppColors.textSec)),
              Text('Qty', style: TextStyle(fontSize: 13, fontWeight: FontWeight.w700, color: AppColors.textSec)),
              Text('Price', style: TextStyle(fontSize: 13, fontWeight: FontWeight.w700, color: AppColors.textSec)),
              Text('Total', style: TextStyle(fontSize: 13, fontWeight: FontWeight.w700, color: AppColors.textSec)),
            ]),
            const Divider(height: 20),
            ...(_detail!['items'] ?? []).map<Widget>((item) => Padding(padding: const EdgeInsets.only(bottom: 10), child: Row(children: [
              Expanded(flex: 3, child: Text(item['product_name'] ?? '', style: const TextStyle(fontSize: 13))),
              Expanded(child: Text('${item['quantity']}', textAlign: TextAlign.center, style: const TextStyle(fontSize: 13))),
              Expanded(child: Text('${AppConstants.currency}${fmt.format((item['unit_price'] ?? 0).toDouble())}', textAlign: TextAlign.center, style: const TextStyle(fontSize: 12, color: AppColors.textSec))),
              Expanded(child: Text('${AppConstants.currency}${fmt.format((item['total'] ?? 0).toDouble())}', textAlign: TextAlign.end, style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w700))),
            ]))),
            const Divider(),
            // Totals
            _row('Subtotal', fmt.format((_detail!['subtotal'] ?? _detail!['total'] ?? 0).toDouble())),
            if ((_detail!['discount'] ?? 0) > 0) _row('Discount', '-${fmt.format((_detail!['discount']).toDouble())}', color: AppColors.danger),
            _row('Total', fmt.format((_detail!['total'] ?? 0).toDouble()), bold: true),
            const SizedBox(height: 6),
            _row('Paid', fmt.format((_detail!['paid'] ?? 0).toDouble()), color: AppColors.success),
            _row('Balance', fmt.format(((_detail!['total'] ?? 0) - (_detail!['paid'] ?? 0)).toDouble()), color: (_detail!['total'] ?? 0) > (_detail!['paid'] ?? 0) ? AppColors.danger : AppColors.success),
            const SizedBox(height: 6),
            _row('Payment', _detail!['payment_method'] ?? ''),
            const SizedBox(height: 24),
            // Print button
            SizedBox(width: double.infinity, height: 52,
              child: ElevatedButton.icon(
                icon: const Icon(Icons.print_outlined),
                onPressed: () => _snack('Print feature coming soon'),
                label: const Text('Print Receipt', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700)),
              ),
            ),
            const SizedBox(height: 12),
            SizedBox(width: double.infinity, height: 52,
              child: OutlinedButton.icon(
                icon: const Icon(Icons.share_outlined),
                onPressed: () => _snack('Share feature coming soon'),
                label: const Text('Share Receipt', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700)),
              ),
            ),
          ])),
    );
  }

  Widget _row(String l, String v, {bool bold = false, Color? color}) => Padding(padding: const EdgeInsets.only(bottom: 6), child: Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [Text(l, style: const TextStyle(color: AppColors.textSec, fontSize: 14)), Text(v, style: TextStyle(fontWeight: bold ? FontWeight.w700 : FontWeight.w600, fontSize: 14, color: color ?? AppColors.textPri))]));

  void _snack(String msg) => ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(msg), backgroundColor: AppColors.success, behavior: SnackBarBehavior.floating, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10))));

  String _fmtDate(String? d) {
    if (d == null) return '';
    try { return DateFormat('dd MMM yyyy, HH:mm').format(DateTime.parse(d)); } catch (_) { return d; }
  }
}
