import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../shared/theme/app_colors.dart';
import '../../shared/widgets/app_card.dart';
import '../../shared/widgets/search_bar_widget.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/chart_widgets.dart';
import '../../core/api_service.dart';

class ProductTrendsScreen extends StatefulWidget {
  const ProductTrendsScreen({super.key});
  @override State<ProductTrendsScreen> createState() => _ProductTrendsScreenState();
}

class _ProductTrendsScreenState extends State<ProductTrendsScreen> {
  Map<String, dynamic>? _data;
  bool _loading = true;
  String? _selectedProduct;
  final _search = TextEditingController();
  final _fmt = NumberFormat('#,##0.00');

  @override void initState() { super.initState(); _load(); }
  @override void dispose() { _search.dispose(); super.dispose(); }

  Future<void> _load() async {
    setState(() => _loading = true);
    try {
      final params = _selectedProduct != null ? '?product_id=$_selectedProduct' : '';
      final d = await ApiService.get('/reports/product-trends$params');
      if (mounted) setState(() { _data = d; _loading = false; });
    } catch (_) { if (mounted) setState(() => _loading = false); }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(title: const Text('Product Trends')),
      body: _loading
          ? const Center(child: CircularProgressIndicator())
          : _data == null
              ? const Center(child: Text('No data'))
              : SingleChildScrollView(
                  padding: const EdgeInsets.all(16),
                  child: Column(children: [
                    SearchBarWidget(hint: 'Search products...', onChanged: (v) {}),
                    const SizedBox(height: 12),
                    GlassCard(
                      child: Padding(
                        padding: const EdgeInsets.all(12),
                        child: Row(children: [
                          Expanded(
                            child: DropdownButtonFormField<String>(
                              value: _selectedProduct,
                              decoration: const InputDecoration(labelText: 'Select Product', isDense: true, contentPadding: EdgeInsets.symmetric(horizontal: 12, vertical: 10)),
                              isExpanded: true,
                              items: [const DropdownMenuItem(value: null, child: Text('All Products'))],
                              onChanged: (v) => setState(() { _selectedProduct = v; _load(); }),
                            ),
                          ),
                        ]),
                      ),
                    ),
                    const SizedBox(height: 16),
                    ...((_data!['top_selling'] as List?) ?? []).take(5).map((p) => Padding(
                      padding: const EdgeInsets.only(bottom: 8),
                      child: GlassCard(
                        child: Padding(
                          padding: const EdgeInsets.all(12),
                          child: Row(children: [
                            Container(
                              width: 40, height: 40,
                              decoration: BoxDecoration(color: AppColors.primaryLt, borderRadius: BorderRadius.circular(10)),
                              child: const Icon(Icons.inventory_2, color: AppColors.primary, size: 20),
                            ),
                            const SizedBox(width: 12),
                            Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                              Text(p['name'] ?? '', style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 14)),
                              Text('${p['quantity_sold'] ?? 0} sold', style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
                            ])),
                            Text('TSh ${_fmt.format((p['revenue'] ?? 0).toDouble())}', style: const TextStyle(fontWeight: FontWeight.w700, color: AppColors.success)),
                          ]),
                        ),
                      ),
                    )),
                    if (_selectedProduct != null) ...[
                      const SizedBox(height: 16),
                      GlassCard(child: Padding(padding: const EdgeInsets.all(16), child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                        const Text('Sales Trend', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 16)),
                        const SizedBox(height: 12),
                        MiniLineChart(data: (_data!['trend'] as List?)?.map((e) => (e['quantity'] ?? 0).toDouble()).toList() ?? []),
                      ]))),
                      const SizedBox(height: 12),
                      GlassCard(child: Padding(padding: const EdgeInsets.all(16), child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                        const Text('Monthly Comparison', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 16)),
                        const SizedBox(height: 12),
                        BarChartWidget(data: (_data!['monthly'] as List?)?.map((e) => (e['revenue'] ?? 0).toDouble()).toList() ?? []),
                      ]))),
                    ],
                    const SizedBox(height: 30),
                  ]),
                ),
    );
  }
}
