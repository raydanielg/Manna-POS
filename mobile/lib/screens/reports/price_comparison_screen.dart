import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../shared/theme/app_colors.dart';
import '../../shared/widgets/search_bar_widget.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/chart_widgets.dart';
import '../../core/api_service.dart';

class PriceComparisonScreen extends StatefulWidget {
  const PriceComparisonScreen({super.key});
  @override State<PriceComparisonScreen> createState() => _PriceComparisonScreenState();
}

class _PriceComparisonScreenState extends State<PriceComparisonScreen> {
  List<dynamic> _products = [];
  List<dynamic> _prices = [];
  bool _loading = false;
  bool _loadingProducts = true;
  String? _selectedProduct;
  bool _sortAsc = true;
  final _fmt = NumberFormat('#,##0.00');

  @override void initState() { super.initState(); _loadProducts(); }

  Future<void> _loadProducts() async {
    try {
      final d = await ApiService.get('/products?limit=100');
      if (mounted) setState(() { _products = d['data'] ?? d as List? ?? []; _loadingProducts = false; });
    } catch (_) { if (mounted) setState(() => _loadingProducts = false); }
  }

  Future<void> _loadPrices() async {
    if (_selectedProduct == null) return;
    setState(() => _loading = true);
    try {
      final d = await ApiService.get('/purchases/price-comparison?product_id=$_selectedProduct');
      if (mounted) setState(() { _prices = (d['prices'] as List?) ?? []; _loading = false; });
    } catch (_) { if (mounted) setState(() => _loading = false); }
  }

  @override
  Widget build(BuildContext context) {
    final sorted = List<dynamic>.from(_prices);
    sorted.sort((a, b) => _sortAsc
        ? (a['price'] ?? 0).compareTo(b['price'] ?? 0)
        : (b['price'] ?? 0).compareTo(a['price'] ?? 0));
    final bestPrice = sorted.isNotEmpty ? sorted.first['price'] : null;

    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(title: const Text('Price Comparison')),
      body: Column(children: [
        GlassCard(
          child: Padding(
            padding: const EdgeInsets.all(12),
            child: DropdownButtonFormField<String>(
              value: _selectedProduct,
              decoration: const InputDecoration(labelText: 'Select Product', prefixIcon: Icon(Icons.search)),
              isExpanded: true,
              items: _loadingProducts
                  ? [const DropdownMenuItem(value: null, child: Text('Loading...'))]
                  : [const DropdownMenuItem(value: null, child: Text('Choose product'))],
              onChanged: (v) => setState(() { _selectedProduct = v; _loadPrices(); }),
            ),
          ),
        ),
        Expanded(
          child: _loading
              ? const Center(child: CircularProgressIndicator())
              : _prices.isEmpty
                  ? const Center(child: Text('Select a product to compare prices'))
                  : ListView(
                      padding: const EdgeInsets.all(16),
                      children: [
                        Row(children: [
                          const Text('Sort: ', style: TextStyle(fontWeight: FontWeight.w600)),
                          ChoiceChip(label: Text('Low to High'), selected: _sortAsc, onSelected: (v) => setState(() => _sortAsc = v)),
                          const SizedBox(width: 8),
                          ChoiceChip(label: Text('High to Low'), selected: !_sortAsc, onSelected: (v) => setState(() => _sortAsc = !v)),
                        ]),
                        const SizedBox(height: 16),
                        GlassCard(child: Padding(padding: const EdgeInsets.all(16), child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                          const Text('Price Chart', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 16)),
                          const SizedBox(height: 12),
                          BarChartWidget(data: sorted.map((e) => (e['price'] ?? 0).toDouble()).toList()),
                        ]))),
                        const SizedBox(height: 12),
                        ...sorted.map((p) {
                          final isBest = p['price'] == bestPrice;
                          return Padding(
                            padding: const EdgeInsets.only(bottom: 8),
                            child: GlassCard(
                              child: Container(
                                decoration: BoxDecoration(
                                  border: isBest ? Border.all(color: AppColors.success, width: 2) : null,
                                  borderRadius: BorderRadius.circular(14),
                                ),
                                padding: const EdgeInsets.all(14),
                                child: Row(children: [
                                  Container(
                                    width: 44, height: 44,
                                    decoration: BoxDecoration(
                                      color: isBest ? AppColors.successLt : AppColors.primaryLt,
                                      borderRadius: BorderRadius.circular(10),
                                    ),
                                    child: Icon(Icons.store, color: isBest ? AppColors.success : AppColors.primary, size: 22),
                                  ),
                                  const SizedBox(width: 12),
                                  Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                                    Text(p['supplier'] ?? '', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 15)),
                                    const SizedBox(height: 2),
                                    Text('Min Qty: ${p['min_quantity'] ?? 1}', style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
                                  ])),
                                  Column(crossAxisAlignment: CrossAxisAlignment.end, children: [
                                    Text('TSh ${_fmt.format((p['price'] ?? 0).toDouble())}', style: TextStyle(fontWeight: FontWeight.w800, fontSize: 16, color: isBest ? AppColors.success : AppColors.textPri)),
                                    if (isBest) Container(
                                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                                      decoration: BoxDecoration(color: AppColors.successLt, borderRadius: BorderRadius.circular(10)),
                                      child: const Text('Best Price', style: TextStyle(color: AppColors.success, fontSize: 10, fontWeight: FontWeight.w700)),
                                    ),
                                  ]),
                                ]),
                              ),
                            ),
                          );
                        }),
                        const SizedBox(height: 30),
                      ],
                    ),
        ),
      ]),
    );
  }
}
