import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../shared/theme/app_colors.dart';
import '../../shared/widgets/status_badge.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/stat_card.dart';
import '../../widgets/chart_widgets.dart';
import '../../core/api_service.dart';

class InventoryReportScreen extends StatefulWidget {
  const InventoryReportScreen({super.key});
  @override State<InventoryReportScreen> createState() => _InventoryReportScreenState();
}

class _InventoryReportScreenState extends State<InventoryReportScreen> {
  Map<String, dynamic>? _data;
  bool _loading = true;
  final _fmt = NumberFormat('#,##0.00');

  @override void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    setState(() => _loading = true);
    try {
      final d = await ApiService.get('/reports/inventory');
      if (mounted) setState(() { _data = d; _loading = false; });
    } catch (_) { if (mounted) setState(() => _loading = false); }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(title: const Text('Inventory Report')),
      body: _loading
          ? const Center(child: CircularProgressIndicator())
          : _data == null
              ? const Center(child: Text('No data'))
              : RefreshIndicator(
                  onRefresh: _load,
                  child: ListView(
                    padding: const EdgeInsets.all(16),
                    children: [
                      Row(children: [
                        Expanded(child: StatCard(icon: Icons.inventory, value: '${_data!['total_products'] ?? 0}', label: 'Total Products', color: AppColors.primary)),
                        const SizedBox(width: 10),
                        Expanded(child: StatCard(icon: Icons.attach_money, value: 'TSh ${_fmt.format((_data!['total_stock_value'] ?? 0).toDouble())}', label: 'Stock Value', color: AppColors.success)),
                      ]),
                      const SizedBox(height: 10),
                      Row(children: [
                        Expanded(child: StatCard(icon: Icons.warning_amber, value: '${_data!['low_stock'] ?? 0}', label: 'Low Stock Items', color: AppColors.warning)),
                        const SizedBox(width: 10),
                        Expanded(child: StatCard(icon: Icons.remove_shopping_cart, value: '${_data!['out_of_stock'] ?? 0}', label: 'Out of Stock', color: AppColors.danger)),
                      ]),
                      const SizedBox(height: 20),
                      GlassCard(child: Padding(padding: const EdgeInsets.all(16), child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                        const Text('Stock Value by Category', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 16)),
                        const SizedBox(height: 12),
                        BarChartWidget(data: (_data!['stock_by_category'] as List?)?.map((e) => (e['value'] ?? 0).toDouble()).toList() ?? []),
                      ]))),
                      const SizedBox(height: 16),
                      const Text('Low Stock Products', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 16)),
                      const SizedBox(height: 8),
                      ...((_data!['low_stock_products'] as List?) ?? []).map((p) => Padding(
                        padding: const EdgeInsets.only(bottom: 10),
                        child: GlassCard(
                          child: Padding(
                            padding: const EdgeInsets.all(14),
                            child: Row(children: [
                              Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                                Text(p['name'] ?? '', style: const TextStyle(fontWeight: FontWeight.w700)),
                                const SizedBox(height: 4),
                                Text('Stock: ${p['current_stock'] ?? 0} | Reorder: ${p['reorder_level'] ?? 0}', style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
                              ])),
                              TextButton(
                                onPressed: () => ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Reorder action'))),
                                child: const Text('Order'),
                              ),
                            ]),
                          ),
                        ),
                      )),
                      const SizedBox(height: 16),
                      GlassCard(child: Padding(padding: const EdgeInsets.all(16), child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                        const Text('Recently Added', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 16)),
                        const SizedBox(height: 12),
                        ...((_data!['recently_added'] as List?) ?? []).take(5).map((p) => Padding(
                          padding: const EdgeInsets.symmetric(vertical: 4),
                          child: Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
                            Text(p['name'] ?? '', style: const TextStyle(fontWeight: FontWeight.w500)),
                            Text('+${p['quantity'] ?? 0}', style: const TextStyle(color: AppColors.success, fontWeight: FontWeight.w700)),
                          ]),
                        )),
                      ]))),
                      const SizedBox(height: 16),
                      GlassCard(child: Padding(padding: const EdgeInsets.all(16), child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                        const Text('Stock Movement', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 16)),
                        const SizedBox(height: 8),
                        Row(children: [
                          _movementChip('In: ${_data!['stock_in'] ?? 0}', AppColors.success),
                          const SizedBox(width: 8),
                          _movementChip('Out: ${_data!['stock_out'] ?? 0}', AppColors.danger),
                          const SizedBox(width: 8),
                          _movementChip('Adjustments: ${_data!['adjustments'] ?? 0}', AppColors.warning),
                        ]),
                      ]))),
                      const SizedBox(height: 30),
                    ],
                  ),
                ),
    );
  }

  Widget _movementChip(String label, Color color) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
      decoration: BoxDecoration(color: color.withValues(alpha: 0.1), borderRadius: BorderRadius.circular(20)),
      child: Text(label, style: TextStyle(color: color, fontWeight: FontWeight.w600, fontSize: 12)),
    );
  }
}
