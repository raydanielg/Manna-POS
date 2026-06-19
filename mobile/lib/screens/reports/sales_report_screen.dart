import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../shared/theme/app_colors.dart';
import '../../shared/widgets/app_card.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/stat_card.dart';
import '../../widgets/chart_widgets.dart';
import '../../core/api_service.dart';

class SalesReportScreen extends StatefulWidget {
  const SalesReportScreen({super.key});
  @override State<SalesReportScreen> createState() => _SalesReportScreenState();
}

class _SalesReportScreenState extends State<SalesReportScreen> {
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
      final d = await ApiService.get('/reports/sales?from=${_from.text}&to=${_to.text}');
      if (mounted) setState(() { _data = d; _loading = false; });
    } catch (_) { if (mounted) setState(() => _loading = false); }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(title: const Text('Sales Report')),
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
                          Expanded(child: StatCard(icon: Icons.trending_up, value: 'TSh ${_fmt.format((_data!['total_sales'] ?? 0).toDouble())}', label: 'Total Sales', color: AppColors.success)),
                          const SizedBox(width: 10),
                          Expanded(child: StatCard(icon: Icons.receipt_long, value: '${_data!['orders_count'] ?? 0}', label: 'Orders', color: AppColors.primary)),
                        ]),
                        const SizedBox(height: 10),
                        Row(children: [
                          Expanded(child: StatCard(icon: Icons.shopping_cart, value: 'TSh ${_fmt.format((_data!['avg_order_value'] ?? 0).toDouble())}', label: 'Avg Order', color: AppColors.warning)),
                          const SizedBox(width: 10),
                          Expanded(child: StatCard(icon: Icons.assignment_return, value: '${_data!['returns'] ?? 0}', label: 'Returns', color: AppColors.danger)),
                        ]),
                        const SizedBox(height: 20),
                        GlassCard(child: Padding(padding: const EdgeInsets.all(16), child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                          const Text('Daily Sales', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 16)),
                          const SizedBox(height: 12),
                          BarChartWidget(data: (_data!['daily_sales'] as List?)?.map((e) => (e['amount'] ?? 0).toDouble()).toList() ?? []),
                        ]))),
                        const SizedBox(height: 12),
                        GlassCard(child: Padding(padding: const EdgeInsets.all(16), child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                          const Text('Payment Methods', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 16)),
                          const SizedBox(height: 12),
                          DonutChartWidget(data: (_data!['payment_methods'] as List?)?.map((e) => (e['amount'] ?? 0).toDouble()).toList() ?? [],
                            labels: (_data!['payment_methods'] as List?)?.map((e) => e['method'] ?? '').toList() ?? []),
                        ]))),
                        const SizedBox(height: 12),
                        GlassCard(child: Padding(padding: const EdgeInsets.all(16), child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                          const Text('Top Products', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 16)),
                          const SizedBox(height: 12),
                          ...((_data!['top_products'] as List?) ?? []).take(5).map((p) => Padding(
                            padding: const EdgeInsets.symmetric(vertical: 4),
                            child: Row(children: [
                              Expanded(child: Text(p['name'] ?? '', style: const TextStyle(fontWeight: FontWeight.w500))),
                              Text('x${p['quantity'] ?? 0}', style: const TextStyle(color: AppColors.textSec)),
                              const SizedBox(width: 10),
                              Text('TSh ${_fmt.format((p['revenue'] ?? 0).toDouble())}', style: const TextStyle(fontWeight: FontWeight.w700)),
                            ]),
                          )),
                        ]))),
                        const SizedBox(height: 12),
                        GlassCard(child: Padding(padding: const EdgeInsets.all(16), child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                          const Text('Sales by Customer', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 16)),
                          const SizedBox(height: 12),
                          ...((_data!['by_customer'] as List?) ?? []).take(5).map((c) => Padding(
                            padding: const EdgeInsets.symmetric(vertical: 4),
                            child: Row(children: [
                              Expanded(child: Text(c['name'] ?? '', style: const TextStyle(fontWeight: FontWeight.w500))),
                              Text('${c['orders'] ?? 0} orders', style: const TextStyle(color: AppColors.textSec)),
                              const SizedBox(width: 10),
                              Text('TSh ${_fmt.format((c['total'] ?? 0).toDouble())}', style: const TextStyle(fontWeight: FontWeight.w700)),
                            ]),
                          )),
                        ]))),
                        const SizedBox(height: 20),
                        SizedBox(
                          width: double.infinity,
                          child: ElevatedButton.icon(
                            onPressed: () => ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Export feature coming soon'))),
                            icon: const Icon(Icons.download),
                            label: const Text('Export Report'),
                          ),
                        ),
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
