import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../shared/theme/app_colors.dart';
import '../../shared/widgets/app_card.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/stat_card.dart';
import '../../widgets/chart_widgets.dart';
import '../../core/api_service.dart';

class PurchaseReportScreen extends StatefulWidget {
  const PurchaseReportScreen({super.key});
  @override State<PurchaseReportScreen> createState() => _PurchaseReportScreenState();
}

class _PurchaseReportScreenState extends State<PurchaseReportScreen> {
  late TextEditingController _from, _to;
  Map<String, dynamic>? _data;
  bool _loading = false;
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
    setState(() => _loading = true);
    try {
      final d = await ApiService.get('/reports/purchases?from=${_from.text}&to=${_to.text}');
      if (mounted) setState(() { _data = d; _loading = false; });
    } catch (_) { if (mounted) setState(() => _loading = false); }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(title: const Text('Purchase Report')),
      body: Column(children: [
        _dateFilter(),
        Expanded(
          child: _loading
              ? const Center(child: CircularProgressIndicator())
              : _data == null
                  ? const Center(child: Text('No data'))
                  : SingleChildScrollView(
                      padding: const EdgeInsets.all(16),
                      child: Column(children: [
                        Row(children: [
                          Expanded(child: StatCard(icon: Icons.shopping_cart, value: 'TSh ${_fmt.format((_data!['total_purchases'] ?? 0).toDouble())}', label: 'Total Purchases', color: AppColors.primary)),
                          const SizedBox(width: 10),
                          Expanded(child: StatCard(icon: Icons.payment, value: 'TSh ${_fmt.format((_data!['total_paid'] ?? 0).toDouble())}', label: 'Total Paid', color: AppColors.success)),
                        ]),
                        const SizedBox(height: 10),
                        Row(children: [
                          Expanded(child: StatCard(icon: Icons.pending, value: 'TSh ${_fmt.format((_data!['pending'] ?? 0).toDouble())}', label: 'Pending', color: AppColors.warning)),
                          const SizedBox(width: 10),
                          Expanded(child: StatCard(icon: Icons.bar_chart, value: 'TSh ${_fmt.format((_data!['avg_purchase_value'] ?? 0).toDouble())}', label: 'Avg Purchase', color: AppColors.purple)),
                        ]),
                        const SizedBox(height: 20),
                        GlassCard(child: Padding(padding: const EdgeInsets.all(16), child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                          const Text('Monthly Purchase Trend', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 16)),
                          const SizedBox(height: 12),
                          MiniLineChart(data: (_data!['monthly_trend'] as List?)?.map((e) => (e['amount'] ?? 0).toDouble()).toList() ?? []),
                        ]))),
                        const SizedBox(height: 12),
                        GlassCard(child: Padding(padding: const EdgeInsets.all(16), child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                          const Text('Top Suppliers', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 16)),
                          const SizedBox(height: 12),
                          ...((_data!['top_suppliers'] as List?) ?? []).take(5).map((s) => Padding(
                            padding: const EdgeInsets.symmetric(vertical: 4),
                            child: Row(children: [
                              Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                                Text(s['name'] ?? '', style: const TextStyle(fontWeight: FontWeight.w600)),
                                Text('${s['count'] ?? 0} purchases', style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
                              ])),
                              Text('TSh ${_fmt.format((s['total'] ?? 0).toDouble())}', style: const TextStyle(fontWeight: FontWeight.w700)),
                            ]),
                          )),
                        ]))),
                        const SizedBox(height: 12),
                        GlassCard(child: Padding(padding: const EdgeInsets.all(16), child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                          const Text('Purchases by Category', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 16)),
                          const SizedBox(height: 12),
                          BarChartWidget(data: (_data!['by_category'] as List?)?.map((e) => (e['amount'] ?? 0).toDouble()).toList() ?? []),
                        ]))),
                        const SizedBox(height: 30),
                      ]),
                    ),
        ),
      ]),
    );
  }

  Widget _dateFilter() {
    return GlassCard(
      child: Padding(
        padding: const EdgeInsets.fromLTRB(16, 12, 16, 12),
        child: Row(children: [
          Expanded(child: TextFormField(controller: _from, readOnly: true, decoration: const InputDecoration(labelText: 'From', isDense: true, suffixIcon: Icon(Icons.calendar_today, size: 16)),
            onTap: () async { final d = await showDatePicker(context: context, initialDate: DateTime.now().subtract(const Duration(days: 30)), firstDate: DateTime(2020), lastDate: DateTime(2030)); if (d != null) _from.text = DateFormat('yyyy-MM-dd').format(d); })),
          const SizedBox(width: 10),
          Expanded(child: TextFormField(controller: _to, readOnly: true, decoration: const InputDecoration(labelText: 'To', isDense: true, suffixIcon: Icon(Icons.calendar_today, size: 16)),
            onTap: () async { final d = await showDatePicker(context: context, initialDate: DateTime.now(), firstDate: DateTime(2020), lastDate: DateTime(2030)); if (d != null) _to.text = DateFormat('yyyy-MM-dd').format(d); })),
          const SizedBox(width: 10),
          ElevatedButton(onPressed: _load, style: ElevatedButton.styleFrom(padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12)), child: const Text('Filter')),
        ]),
      ),
    );
  }
}
