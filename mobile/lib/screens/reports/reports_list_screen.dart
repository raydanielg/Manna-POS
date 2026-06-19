import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../shared/theme/app_colors.dart';
import '../../shared/widgets/app_card.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/stat_card.dart';
import '../../core/api_service.dart';

class ReportsListScreen extends StatefulWidget {
  const ReportsListScreen({super.key});
  @override State<ReportsListScreen> createState() => _ReportsListScreenState();
}

class _ReportsListScreenState extends State<ReportsListScreen> {
  Map<String, dynamic>? _stats;
  bool _loading = true;

  @override void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    try {
      final d = await ApiService.get('/reports/quick-stats');
      if (mounted) setState(() { _stats = d; _loading = false; });
    } catch (_) { if (mounted) setState(() => _loading = false); }
  }

  static const _reports = [
    {'title': 'Profit & Loss', 'desc': 'Revenue, expenses & net profit overview', 'icon': Icons.assessment, 'color': AppColors.primary, 'route': 'profit_loss'},
    {'title': 'Sales Report', 'desc': 'Sales, orders, payments & trends', 'icon': Icons.trending_up, 'color': AppColors.success, 'route': 'sales'},
    {'title': 'Purchase Report', 'desc': 'Purchases, suppliers & costs', 'icon': Icons.shopping_cart, 'color': AppColors.orange, 'route': 'purchases'},
    {'title': 'Expense Report', 'desc': 'Expense breakdown & categories', 'icon': Icons.money_off, 'color': AppColors.danger, 'route': 'expenses'},
    {'title': 'Inventory Report', 'desc': 'Stock levels, value & alerts', 'icon': Icons.inventory, 'color': AppColors.purple, 'route': 'inventory'},
    {'title': 'Expiry Report', 'desc': 'Products nearing expiration', 'icon': Icons.calendar_today, 'color': Color(0xFFFFB300), 'route': 'expiry'},
    {'title': 'Product Trends', 'desc': 'Top sellers & product performance', 'icon': Icons.show_chart, 'color': Color(0xFF009688), 'route': 'product_trends'},
    {'title': 'Suppliers Report', 'desc': 'Supplier performance & history', 'icon': Icons.people, 'color': Color(0xFF3F51B5), 'route': 'suppliers'},
    {'title': 'Price Comparison', 'desc': 'Compare supplier prices', 'icon': Icons.compare_arrows, 'color': Color(0xFFE91E63), 'route': 'price_comparison'},
  ];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(title: const Text('Reports'), centerTitle: true),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          if (_loading)
            const Padding(padding: EdgeInsets.all(40), child: Center(child: CircularProgressIndicator()))
          else ...[
            GlassCard(
              child: Padding(
                padding: const EdgeInsets.all(16),
                child: Column(
                  children: [
                    Row(children: [
                      _statQuick('Total Revenue', _stats?['total_revenue']?.toString() ?? '0', Icons.trending_up, AppColors.success),
                      const SizedBox(width: 10),
                      _statQuick('Expenses', _stats?['total_expenses']?.toString() ?? '0', Icons.money_off, AppColors.danger),
                      const SizedBox(width: 10),
                      _statQuick('Net Profit', _stats?['net_profit']?.toString() ?? '0', Icons.account_balance, AppColors.primary),
                    ]),
                  ],
                ),
              ),
            ),
            const SizedBox(height: 20),
          ],
          ..._reports.map((r) => Padding(
            padding: const EdgeInsets.only(bottom: 12),
            child: GlassCard(
              child: InkWell(
                borderRadius: BorderRadius.circular(14),
                onTap: () => Navigator.pushNamed(context, '/reports/${r['route']}'),
                child: Padding(
                  padding: const EdgeInsets.all(16),
                  child: Row(children: [
                    Container(
                      width: 48, height: 48,
                      decoration: BoxDecoration(
                        gradient: LinearGradient(colors: [(r['color'] as Color).withValues(alpha: 0.2), (r['color'] as Color).withValues(alpha: 0.05)]),
                        borderRadius: BorderRadius.circular(12),
                      ),
                      child: Icon(r['icon'] as IconData, color: r['color'] as Color, size: 24),
                    ),
                    const SizedBox(width: 14),
                    Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                      Text(r['title'] as String, style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 15)),
                      const SizedBox(height: 2),
                      Text(r['desc'] as String, style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
                    ])),
                    const Icon(Icons.chevron_right, color: AppColors.textSec),
                  ]),
                ),
              ),
            ),
          )),
        ],
      ),
    );
  }

  Widget _statQuick(String label, String value, IconData icon, Color color) {
    return Expanded(
      child: Column(
        children: [
          Icon(icon, color: color, size: 20),
          const SizedBox(height: 6),
          Text(value, style: TextStyle(fontWeight: FontWeight.w800, fontSize: 16, color: color)),
          Text(label, style: const TextStyle(color: AppColors.textSec, fontSize: 11)),
        ],
      ),
    );
  }
}
