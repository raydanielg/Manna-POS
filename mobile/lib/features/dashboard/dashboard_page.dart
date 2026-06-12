import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../../core/api_service.dart';
import '../../core/auth_provider.dart';
import '../../shared/theme/app_theme.dart';
import '../../shared/widgets/app_card.dart';
import '../../shared/widgets/empty_state.dart';
import '../../shared/widgets/status_badge.dart';
import '../../shared/constants/app_constants.dart';
import '../products/products_page.dart';
import '../pos/pos_page.dart';
import '../sales/sales_page.dart';
import '../more/more_page.dart';

class DashboardPage extends StatefulWidget {
  const DashboardPage({super.key});
  @override State<DashboardPage> createState() => _DashboardPageState();
}

class _DashboardPageState extends State<DashboardPage> {
  int _tab = 0;
  final _tabs = const [HomeTab(), ProductsPage(), PosPage(), SalesPage(), MorePage()];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: IndexedStack(index: _tab, children: _tabs),
      bottomNavigationBar: BottomNavigationBar(
        currentIndex: _tab,
        onTap: (i) => setState(() => _tab = i),
        items: const [
          BottomNavigationBarItem(icon: Icon(Icons.home_outlined), activeIcon: Icon(Icons.home), label: 'Home'),
          BottomNavigationBarItem(icon: Icon(Icons.inventory_2_outlined), activeIcon: Icon(Icons.inventory_2), label: 'Products'),
          BottomNavigationBarItem(icon: Icon(Icons.point_of_sale_outlined), activeIcon: Icon(Icons.point_of_sale), label: 'POS'),
          BottomNavigationBarItem(icon: Icon(Icons.receipt_long_outlined), activeIcon: Icon(Icons.receipt_long), label: 'Sales'),
          BottomNavigationBarItem(icon: Icon(Icons.grid_view_outlined), activeIcon: Icon(Icons.grid_view), label: 'More'),
        ],
      ),
    );
  }
}

class HomeTab extends StatefulWidget {
  const HomeTab({super.key});
  @override State<HomeTab> createState() => _HomeTabState();
}

class _HomeTabState extends State<HomeTab> {
  Map<String, dynamic>? _stats;
  bool _loading = true;
  String? _error;
  final fmt = NumberFormat('#,##0.00');

  @override
  void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    setState(() { _loading = true; _error = null; });
    try {
      final data = await ApiService.get('/dashboard/stats');
      setState(() { _stats = data; _loading = false; });
    } on ApiException catch (e) {
      setState(() { _error = e.message; _loading = false; });
    } catch (_) {
      setState(() { _error = 'Connection error'; _loading = false; });
    }
  }

  String _f(dynamic v) => fmt.format(double.tryParse(v?.toString() ?? '0') ?? 0);

  @override
  Widget build(BuildContext context) {
    final user = context.watch<AuthProvider>().user;
    return Scaffold(
      backgroundColor: AppColors.bg,
      body: CustomScrollView(slivers: [
        SliverAppBar(
          expandedHeight: 140,
          pinned: true,
          backgroundColor: Colors.white,
          elevation: 0,
          actions: [
            IconButton(icon: const Icon(Icons.refresh, color: AppColors.textPri), onPressed: _load),
            IconButton(icon: const Icon(Icons.notifications_outlined, color: AppColors.textPri), onPressed: () {}),
            const SizedBox(width: 8),
          ],
          flexibleSpace: FlexibleSpaceBar(
            background: Container(
              color: Colors.white,
              child: SafeArea(child: Padding(padding: const EdgeInsets.fromLTRB(20, 16, 20, 0), child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                const SizedBox(height: 48),
                Text('Good ${_greeting()}, ${user?.name.split(' ').first ?? ''}! 👋', style: const TextStyle(color: AppColors.textPri, fontSize: 18, fontWeight: FontWeight.w700)),
                const SizedBox(height: 4),
                Text(DateFormat('EEEE, MMM d yyyy').format(DateTime.now()), style: const TextStyle(color: AppColors.textSec, fontSize: 13)),
              ]))),
            ),
          ),
        ),
        SliverPadding(
          padding: const EdgeInsets.all(16),
          sliver: _loading ? const SliverFillRemaining(child: LoadingWidget(message: 'Loading dashboard...'))
              : _error != null ? SliverFillRemaining(child: ErrorWidget2(message: _error!, onRetry: _load))
              : SliverList(delegate: SliverChildListDelegate([
                  _section('Today\'s Overview'),
                  const SizedBox(height: 12),
                  Row(children: [
                    Expanded(child: StatCard(label: 'Sales Revenue', value: '${AppConstants.currency} ${_f(_stats!['today_sales'])}', icon: Icons.trending_up, color: AppColors.success)),
                    const SizedBox(width: 12),
                    Expanded(child: StatCard(label: 'Orders', value: '${_stats!['today_orders']}', icon: Icons.receipt_outlined, color: AppColors.primary)),
                  ]),
                  const SizedBox(height: 12),
                  Row(children: [
                    Expanded(child: StatCard(label: 'Products', value: '${_stats!['total_products']}', icon: Icons.inventory_2_outlined, color: AppColors.secondary)),
                    const SizedBox(width: 12),
                    Expanded(child: StatCard(label: 'Customers', value: '${_stats!['total_customers']}', icon: Icons.people_outlined, color: AppColors.warning)),
                  ]),
                  const SizedBox(height: 20),
                  _section('This Month'),
                  const SizedBox(height: 12),
                  Row(children: [
                    Expanded(child: StatCard(label: 'Revenue', value: '${AppConstants.currency} ${_f(_stats!['month_revenue'])}', icon: Icons.account_balance_wallet_outlined, color: AppColors.success)),
                    const SizedBox(width: 12),
                    Expanded(child: StatCard(label: 'Expenses', value: '${AppConstants.currency} ${_f(_stats!['month_expenses'])}', icon: Icons.money_off_outlined, color: AppColors.danger)),
                  ]),
                  const SizedBox(height: 12),
                  Row(children: [
                    Expanded(child: StatCard(label: 'Low Stock', value: '${_stats!['low_stock']}', icon: Icons.warning_amber_outlined, color: AppColors.warning, subtitle: 'items need reorder')),
                    const SizedBox(width: 12),
                    Expanded(child: StatCard(label: 'Out of Stock', value: '${_stats!['out_of_stock']}', icon: Icons.remove_shopping_cart_outlined, color: AppColors.danger, subtitle: 'items unavailable')),
                  ]),
                  const SizedBox(height: 20),
                  _section('Sales - Last 7 Days'),
                  const SizedBox(height: 12),
                  AppCard(padding: const EdgeInsets.all(20), child: _buildChart()),
                  const SizedBox(height: 20),
                  _section('Recent Sales'),
                  const SizedBox(height: 12),
                  ...(_stats!['recent_sales'] as List? ?? []).map((s) => _saleTile(s)),
                  const SizedBox(height: 20),
                ])),
        ),
      ]),
    );
  }

  Widget _section(String t) => Text(t, style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w700, color: AppColors.textPri));

  Widget _buildChart() {
    final chart = (_stats!['sales_chart'] as List? ?? []);
    if (chart.isEmpty) return const SizedBox(height: 80, child: Center(child: Text('No data')));
    final maxVal = chart.map((e) => (e['total'] as num).toDouble()).fold(0.0, (a, b) => a > b ? a : b);
    return SizedBox(
      height: 140,
      child: Row(crossAxisAlignment: CrossAxisAlignment.end, children: chart.map((e) {
        final val = (e['total'] as num).toDouble();
        final pct = maxVal > 0 ? val / maxVal : 0.0;
        return Expanded(child: Padding(padding: const EdgeInsets.symmetric(horizontal: 3), child: Column(mainAxisAlignment: MainAxisAlignment.end, children: [
          if (val > 0) Text(val >= 1000 ? '${(val/1000).toStringAsFixed(1)}K' : val.toStringAsFixed(0), style: const TextStyle(fontSize: 9, color: AppColors.textSec)),
          const SizedBox(height: 4),
          Container(height: 100 * pct + 8, decoration: BoxDecoration(color: pct > 0 ? AppColors.primary : AppColors.border, borderRadius: BorderRadius.circular(6))),
          const SizedBox(height: 6),
          Text(e['label'], style: const TextStyle(fontSize: 10, color: AppColors.textSec, fontWeight: FontWeight.w600)),
        ])));
      }).toList()),
    );
  }

  Widget _saleTile(Map<String, dynamic> s) {
    return Padding(padding: const EdgeInsets.only(bottom: 8), child: AppCard(padding: const EdgeInsets.all(14), child: Row(children: [
      Container(width: 42, height: 42, decoration: BoxDecoration(color: AppColors.primaryLt, borderRadius: BorderRadius.circular(12)),
        child: const Icon(Icons.receipt_outlined, color: AppColors.primary, size: 20)),
      const SizedBox(width: 12),
      Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
        Text(s['reference'] ?? '', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 13)),
        const SizedBox(height: 2),
        Text(s['customer']?['name'] ?? 'Walk-in', style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
      ])),
      Column(crossAxisAlignment: CrossAxisAlignment.end, children: [
        Text('${AppConstants.currency} ${_f(s['total'])}', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 13, color: AppColors.textPri)),
        const SizedBox(height: 4),
        StatusBadge.fromStatus(s['status'] ?? 'completed'),
      ]),
    ])));
  }

  String _greeting() {
    final h = DateTime.now().hour;
    if (h < 12) return 'Morning';
    if (h < 17) return 'Afternoon';
    return 'Evening';
  }
}