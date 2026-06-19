import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../shared/theme/app_colors.dart';
import '../../shared/widgets/status_badge.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/stat_card.dart';
import '../../widgets/chart_widgets.dart';
import '../../core/api_service.dart';

class SuppliersReportScreen extends StatefulWidget {
  const SuppliersReportScreen({super.key});
  @override State<SuppliersReportScreen> createState() => _SuppliersReportScreenState();
}

class _SuppliersReportScreenState extends State<SuppliersReportScreen> {
  Map<String, dynamic>? _data;
  bool _loading = true;
  final _fmt = NumberFormat('#,##0.00');

  @override void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    setState(() => _loading = true);
    try {
      final d = await ApiService.get('/reports/suppliers');
      if (mounted) setState(() { _data = d; _loading = false; });
    } catch (_) { if (mounted) setState(() => _loading = false); }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(title: const Text('Suppliers Report')),
      body: _loading
          ? const Center(child: CircularProgressIndicator())
          : _data == null
              ? const Center(child: Text('No data'))
              : ListView(
                  padding: const EdgeInsets.all(16),
                  children: [
                    Row(children: [
                      Expanded(child: StatCard(icon: Icons.people, value: '${_data!['total_suppliers'] ?? 0}', label: 'Total Suppliers', color: AppColors.primary)),
                      const SizedBox(width: 10),
                      Expanded(child: StatCard(icon: Icons.shopping_cart, value: 'TSh ${_fmt.format((_data!['total_purchases'] ?? 0).toDouble())}', label: 'Total Purchases', color: AppColors.success)),
                    ]),
                    const SizedBox(height: 10),
                    Row(children: [
                      Expanded(child: StatCard(icon: Icons.person, value: 'TSh ${_fmt.format((_data!['avg_per_supplier'] ?? 0).toDouble())}', label: 'Avg per Supplier', color: AppColors.warning)),
                      const SizedBox(width: 10),
                      Expanded(child: StatCard(icon: Icons.star, value: '${_data!['top_supplier'] ?? '-'}', label: 'Top Supplier', color: AppColors.purple)),
                    ]),
                    const SizedBox(height: 20),
                    GlassCard(child: Padding(padding: const EdgeInsets.all(16), child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                      const Text('Top Suppliers', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 16)),
                      const SizedBox(height: 12),
                      ...((_data!['top_suppliers'] as List?) ?? []).take(5).map((s) => Padding(
                        padding: const EdgeInsets.symmetric(vertical: 8),
                        child: Row(children: [
                          Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                            Text(s['name'] ?? '', style: const TextStyle(fontWeight: FontWeight.w600)),
                            const SizedBox(height: 2),
                            Row(children: [
                              Text('${s['purchase_count'] ?? 0} purchases', style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
                              const SizedBox(width: 8),
                              StatusBadge.fromStatus(s['payment_status'] ?? 'active'),
                            ]),
                          ])),
                          Text('TSh ${_fmt.format((s['total'] ?? 0).toDouble())}', style: const TextStyle(fontWeight: FontWeight.w700)),
                        ]),
                      )),
                    ]))),
                    const SizedBox(height: 12),
                    GlassCard(child: Padding(padding: const EdgeInsets.all(16), child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                      const Text('Purchase History by Supplier', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 16)),
                      const SizedBox(height: 12),
                      BarChartWidget(data: (_data!['by_supplier'] as List?)?.map((e) => (e['amount'] ?? 0).toDouble()).toList() ?? []),
                    ]))),
                    const SizedBox(height: 12),
                    GlassCard(child: Padding(padding: const EdgeInsets.all(16), child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                      const Text('Performance Metrics', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 16)),
                      const SizedBox(height: 12),
                      _metricRow('On-Time Delivery', _data!['on_time_rate']?.toString() ?? '0%'),
                      _metricRow('Avg Lead Time', '${_data!['avg_lead_time'] ?? 0} days'),
                      _metricRow('Return Rate', '${_data!['return_rate'] ?? 0}%'),
                    ]))),
                    const SizedBox(height: 30),
                  ],
                ),
    );
  }

  Widget _metricRow(String label, String value) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 8),
      child: Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
        Text(label, style: const TextStyle(color: AppColors.textPri)),
        Text(value, style: const TextStyle(fontWeight: FontWeight.w700, color: AppColors.primary)),
      ]),
    );
  }
}
