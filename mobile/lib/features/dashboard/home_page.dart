import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:provider/provider.dart';
import '../../core/auth_provider.dart';
import '../../core/api_service.dart';
import '../../shared/theme/app_colors.dart';
import '../../shared/utils/formatters.dart';
import '../../shared/widgets/app_card.dart';
import '../../shared/widgets/empty_state.dart';
import '../../shared/widgets/status_badge.dart';

class HomePage extends StatefulWidget {
  const HomePage({super.key});
  @override
  State<HomePage> createState() => _HomePageState();
}

class _HomePageState extends State<HomePage> {
  Map<String, dynamic>? _stats;
  bool _loading = true;
  String? _error;
  List<dynamic> _recentSales = [];

  @override
  void initState() {
    super.initState();
    _loadStats();
  }

  Future<void> _loadStats() async {
    setState(() => _loading = true);
    try {
      final data = await ApiService.get('/dashboard/stats');
      final sales = await ApiService.get('/sales?status=completed&take=5');
      setState(() {
        _stats = data;
        _recentSales = sales is List ? sales : [];
        _loading = false;
        _error = null;
      });
    } catch (e) {
      setState(() { _error = e.toString(); _loading = false; });
    }
  }

  @override
  Widget build(BuildContext context) {
    final user = context.watch<AuthProvider>().user;
    return Scaffold(
      backgroundColor: AppColors.background,
      body: RefreshIndicator(
        onRefresh: _loadStats,
        child: CustomScrollView(
          physics: const BouncingScrollPhysics(),
          slivers: [
            SliverToBoxAdapter(child: _buildHeader(user)),
            if (_loading)
              const SliverFillRemaining(child: Center(child: CircularProgressIndicator()))
            else if (_error != null)
              SliverFillRemaining(child: ErrorWidget2(message: _error!, onRetry: _loadStats))
            else ...[
              SliverToBoxAdapter(child: _buildTodayStats()),
              SliverToBoxAdapter(child: _buildStatsGrid()),
              SliverToBoxAdapter(child: _buildQuickActions()),
              SliverToBoxAdapter(child: _buildTopProducts()),
              SliverToBoxAdapter(child: _buildRecentSales()),
              SliverToBoxAdapter(child: _buildInventoryAlerts()),
            ],
            const SliverToBoxAdapter(child: SizedBox(height: 32)),
          ],
        ),
      ),
    );
  }

  Widget _buildTodayStats() {
    if (_stats == null) return const SizedBox.shrink();
    return Padding(
      padding: const EdgeInsets.fromLTRB(12, 12, 12, 0),
      child: Container(
        decoration: BoxDecoration(
          gradient: const LinearGradient(colors: [AppColors.primary, AppColors.primaryDark], begin: Alignment.topLeft, end: Alignment.bottomRight),
          borderRadius: BorderRadius.circular(16),
        ),
        padding: const EdgeInsets.all(16),
        child: Row(children: [
          Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            const Text('Today', style: TextStyle(color: Colors.white70, fontSize: 12, fontWeight: FontWeight.w500)),
            const SizedBox(height: 4),
            Text('TSh ${fmtCurrency((_stats!['today_sales'] ?? 0).toDouble())}', style: const TextStyle(color: Colors.white, fontSize: 22, fontWeight: FontWeight.w900)),
            const SizedBox(height: 4),
            Text('${_stats!['today_orders'] ?? 0} orders · ${fmtCurrency((_stats!['week_sales'] ?? 0).toDouble())} this week', style: const TextStyle(color: Colors.white70, fontSize: 11)),
          ])),
          Container(
            width: 50, height: 50,
            decoration: BoxDecoration(color: Colors.white.withValues(alpha: 0.2), borderRadius: BorderRadius.circular(14)),
            child: const Icon(Icons.trending_up_rounded, color: Colors.white, size: 26),
          ),
        ]),
      ),
    );
  }

  Widget _buildHeader(dynamic user) {
    final name = user?.name ?? 'User';
    final business = user?.businessName ?? 'MannaPOS';
    final initials = name.isNotEmpty ? name.split(' ').map((w) => w[0]).take(2).join().toUpperCase() : 'U';
    return Container(
      color: Colors.white,
      child: SafeArea(
        bottom: false,
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
          child: Row(
            children: [
              Container(
                width: 38, height: 38,
                decoration: BoxDecoration(
                  color: AppColors.primaryLt, borderRadius: BorderRadius.circular(19),
                ),
                child: Center(child: Text(initials, style: const TextStyle(
                  fontSize: 13, fontWeight: FontWeight.w700, color: AppColors.primary))),
              ),
              const SizedBox(width: 10),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(name, style: const TextStyle(fontSize: 15, fontWeight: FontWeight.w600, color: Colors.black)),
                    Text(business, style: const TextStyle(fontSize: 11, color: AppColors.textSec)),
                  ],
                ),
              ),
              if (user?.role == 'admin')
                GestureDetector(
                  onTap: () => context.push('/admin'),
                  child: Container(
                    padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
                    decoration: BoxDecoration(
                      color: AppColors.orange.withValues(alpha: 0.1),
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: const Text('Admin', style: TextStyle(fontSize: 12, color: AppColors.orange, fontWeight: FontWeight.w600)),
                  ),
                ),
              const SizedBox(width: 8),
              GestureDetector(
                onTap: () => context.push('/settings'),
                child: const Icon(Icons.settings_outlined, size: 22, color: Colors.black),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildStatsGrid() {
    if (_stats == null) return const SizedBox.shrink();
    final s = _stats!;
    return Padding(
      padding: const EdgeInsets.fromLTRB(12, 12, 12, 0),
      child: Column(children: [
        Row(children: [
          Expanded(child: StatCard(
            icon: Icons.shopping_cart_rounded, value: fmtCurrency((s['total_sales'] ?? 0).toDouble()),
            label: 'Sales (Month)', color: AppColors.primary, bg: const Color(0xFFDFF7EE),
            subtitle: s['sales_growth'] != null ? '${s['sales_growth'] > 0 ? '+' : ''}${s['sales_growth']}% vs last month' : null,
          )),
          const SizedBox(width: 10),
          Expanded(child: StatCard(
            icon: Icons.receipt_long_rounded, value: '${s['total_orders'] ?? 0}',
            label: 'Orders', color: AppColors.accent, bg: const Color(0xFFE8F0FE),
            subtitle: s['orders_growth'] != null ? '${s['orders_growth'] > 0 ? '+' : ''}${s['orders_growth']}% vs last month' : null,
          )),
        ]),
        const SizedBox(height: 10),
        Row(children: [
          Expanded(child: StatCard(
            icon: Icons.inventory_2_rounded, value: '${s['total_products'] ?? 0}',
            label: 'Products', color: AppColors.purple, bg: const Color(0xFFEEF0FF),
          )),
          const SizedBox(width: 10),
          Expanded(child: StatCard(
            icon: Icons.people_rounded, value: '${s['total_customers'] ?? 0}',
            label: 'Customers', color: AppColors.pink, bg: const Color(0xFFFFE8ED),
          )),
        ]),
      ]),
    );
  }

  Widget _buildTopProducts() {
    if (_stats == null || (_stats!['top_products'] as List?)?.isEmpty == true) return const SizedBox.shrink();
    final products = _stats!['top_products'] as List;
    return Padding(
      padding: const EdgeInsets.fromLTRB(12, 16, 12, 0),
      child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
        const Text('Top Products', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700, color: Colors.black)),
        const SizedBox(height: 12),
        Container(
          decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(12)),
          child: Column(children: products.asMap().entries.map((e) => Padding(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
            child: Row(children: [
              Container(width: 24, height: 24, decoration: BoxDecoration(color: AppColors.primaryLt, borderRadius: BorderRadius.circular(6)),
                child: Center(child: Text('${e.key + 1}', style: const TextStyle(color: AppColors.primary, fontSize: 11, fontWeight: FontWeight.w800)))),
              const SizedBox(width: 12),
              Expanded(child: Text(e.value['name'] ?? '', style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 14))),
              Text('${e.value['total_qty'] ?? 0} sold', style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
              const SizedBox(width: 8),
              Text('TSh ${fmtCurrency((e.value['total_revenue'] ?? 0).toDouble())}', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 13, color: AppColors.primary)),
            ]),
          )).toList()),
        ),
      ]),
    );
  }

  Widget _buildInventoryAlerts() {
    if (_stats == null) return const SizedBox.shrink();
    final lowStock = _stats!['low_stock'] ?? 0;
    final outOfStock = _stats!['out_of_stock'] ?? 0;
    if (lowStock == 0 && outOfStock == 0) return const SizedBox.shrink();
    return Padding(
      padding: const EdgeInsets.fromLTRB(12, 16, 12, 0),
      child: Container(
        padding: const EdgeInsets.all(14),
        decoration: BoxDecoration(
          color: (outOfStock as int) > 0 ? AppColors.dangerLt : AppColors.warningLt,
          borderRadius: BorderRadius.circular(12),
          border: Border.all(color: (outOfStock as int) > 0 ? AppColors.danger.withValues(alpha: 0.3) : AppColors.warning.withValues(alpha: 0.3)),
        ),
        child: Row(children: [
          Icon((outOfStock as int) > 0 ? Icons.error_outline : Icons.warning_amber_outlined, color: (outOfStock as int) > 0 ? AppColors.danger : AppColors.warning, size: 24),
          const SizedBox(width: 12),
          Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            Text((outOfStock as int) > 0 ? 'Inventory Alert' : 'Low Stock Warning', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 14, color: (outOfStock as int) > 0 ? AppColors.danger : AppColors.warning)),
            Text('${outOfStock} out of stock · ${lowStock} low stock items', style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
          ])),
          TextButton(onPressed: () => context.push('/products'), child: const Text('View', style: TextStyle(fontSize: 13))),
        ]),
      ),
    );
  }

  Widget _buildQuickActions() {
    return Padding(
      padding: const EdgeInsets.fromLTRB(12, 16, 12, 0),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text('Quick Actions', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700, color: Colors.black)),
          const SizedBox(height: 12),
          Row(children: [
            Expanded(child: _ActionTile(
              icon: Icons.add_circle_outline_rounded, label: 'New Sale',
              color: AppColors.primary, onTap: () => context.push('/sales'),
            )),
            const SizedBox(width: 10),
            Expanded(child: _ActionTile(
              icon: Icons.add_box_outlined, label: 'Add Product',
              color: AppColors.accent, onTap: () => context.push('/products'),
            )),
            const SizedBox(width: 10),
            Expanded(child: _ActionTile(
              icon: Icons.people_alt_outlined, label: 'Add Customer',
              color: AppColors.purple, onTap: () => context.push('/customers'),
            )),
          ]),
        ],
      ),
    );
  }

  Widget _buildRecentSales() {
    return Padding(
      padding: const EdgeInsets.fromLTRB(12, 16, 12, 0),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              const Text('Recent Sales', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700, color: Colors.black)),
              const Spacer(),
              GestureDetector(
                onTap: () => context.push('/sales'),
                child: Row(children: [
                  Text('See All', style: TextStyle(fontSize: 13, color: AppColors.primary, fontWeight: FontWeight.w500)),
                  const SizedBox(width: 2),
                  Icon(Icons.chevron_right, size: 16, color: AppColors.primary),
                ]),
              ),
            ],
          ),
          const SizedBox(height: 12),
          if (_recentSales.isEmpty)
            Container(
              padding: const EdgeInsets.all(24),
              decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(12)),
              child: const Center(child: Text('No sales yet', style: TextStyle(color: AppColors.textSec))),
            )
          else
            Container(
              decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(12)),
              child: Column(
                children: _recentSales.take(5).map((s) => _SaleRow(sale: s)).toList(),
              ),
            ),
        ],
      ),
    );
  }
}

class _ActionTile extends StatelessWidget {
  final IconData icon;
  final String label;
  final Color color;
  final VoidCallback onTap;
  const _ActionTile({required this.icon, required this.label, required this.color, required this.onTap});

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 16),
        decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(12)),
        child: Column(
          children: [
            Container(
              width: 46, height: 46,
              decoration: BoxDecoration(color: color.withValues(alpha: 0.12), borderRadius: BorderRadius.circular(12)),
              child: Icon(icon, size: 22, color: color),
            ),
            const SizedBox(height: 8),
            Text(label, textAlign: TextAlign.center, style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w500, color: Colors.black)),
          ],
        ),
      ),
    );
  }
}

class _SaleRow extends StatelessWidget {
  final dynamic sale;
  const _SaleRow({required this.sale});

  @override
  Widget build(BuildContext context) {
    final ref = sale['reference'] ?? '';
    final total = (sale['total'] ?? 0).toDouble();
    final status = sale['status'] ?? 'completed';
    final date = fmtDateShort(sale['sale_date']);
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
      child: Row(
        children: [
          Container(
            width: 36, height: 36,
            decoration: BoxDecoration(color: AppColors.primaryLt, borderRadius: BorderRadius.circular(10)),
            child: const Icon(Icons.receipt_rounded, size: 18, color: AppColors.primary),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(ref, style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w600, color: Colors.black)),
                Text(date, style: const TextStyle(fontSize: 11, color: AppColors.textSec)),
              ],
            ),
          ),
          Text('TSh ${fmtCurrency(total)}', style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w700, color: Colors.black)),
          const SizedBox(width: 8),
          StatusBadge.fromStatus(status),
        ],
      ),
    );
  }
}
