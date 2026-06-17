import 'dart:async';
import 'dart:math' as math;
import 'dart:ui' as ui show TextDirection;
import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:provider/provider.dart';
import '../../core/auth_provider.dart';
import '../../core/api_service.dart';
import '../../shared/theme/app_colors.dart';
import '../../shared/utils/formatters.dart';
import '../../shared/widgets/status_badge.dart';

class HomePage extends StatefulWidget {
  const HomePage({super.key});
  @override
  State<HomePage> createState() => _HomePageState();
}

class _HomePageState extends State<HomePage> {
  Map<String, dynamic>? _stats;
  bool _loading = true;
  bool _refreshing = false;
  String? _error;
  List<dynamic> _recentSales = [];
  bool _isDark = false;

  // Interactive Tour States
  bool _showTour = true;
  int _tourStep = 0;
  final _tourSteps = const [
    ('Live Sales Stats 👋', 'Welcome to Manna! View your real-time today revenue, weekly summaries, and total order counts at a single glance.'),
    ('Performance Grid 📊', 'Track monthly sales revenues, purchases, products, and customer counts. Everything scales beautifully.'),
    ('Quick Actions ⚡', 'Easily register a new completed sale, add products, or add new customers instantly from any screen.'),
    ('Live Cashflow Curve 📈', 'Monitor income trends dynamically with our beautiful smooth curve chart tracking the last 7 days.'),
    ('All Set! 🚀', "You're all set! Start managing and scaling your business with Manna today."),
  ];

  @override
  void initState() {
    super.initState();
    _loadStats();
  }

  Future<void> _loadStats() async {
    if (_loading) {
      setState(() { _loading = true; _error = null; });
    } else {
      setState(() { _refreshing = true; _error = null; });
    }
    try {
      final data = await ApiService.get('/dashboard/stats');
      final sales = await ApiService.get('/sales?status=completed&take=5');
      setState(() {
        _stats = data;
        _recentSales = sales is List ? sales : [];
        _loading = false;
        _refreshing = false;
        _error = null;
      });
    } on ApiException catch (e) {
      setState(() { _error = e.message; _loading = false; _refreshing = false; });
    } catch (_) {
      setState(() { _error = 'Connection error'; _loading = false; _refreshing = false; });
    }
  }

  @override
  Widget build(BuildContext context) {
    final user = context.watch<AuthProvider>().user;
    final double screenWidth = MediaQuery.of(context).size.width;
    final bool isTablet = screenWidth > 600;

    // Theme values
    final Color bgColor = _isDark ? const Color(0xFF111827) : const Color(0xFFF4F5F7);
    final Color cardBg = _isDark ? const Color(0xFF1F2937) : Colors.white;
    final Color borderColor = _isDark ? const Color(0xFF374151) : const Color(0xFFE4E4E7);
    final Color textPrimary = _isDark ? Colors.white : const Color(0xFF111827);
    final Color textSecondary = _isDark ? const Color(0xFF9CA3AF) : const Color(0xFF6B7280);
    final Color iconThemeColor = _isDark ? Colors.white70 : Colors.black87;

    // Responsive horizontal padding to keep dashboard content beautifully centered on wide tablet screens
    final double horizontalPadding = isTablet ? (screenWidth - 720) / 2 : 14.0;

    return Scaffold(
      backgroundColor: bgColor,
      body: Stack(
        children: [
          RefreshIndicator(
            onRefresh: _loadStats,
            color: AppColors.primary,
            child: CustomScrollView(
              physics: const BouncingScrollPhysics(),
              slivers: [
                // Header section spans fully
                SliverToBoxAdapter(child: _buildHeader(user, cardBg, borderColor, textPrimary, textSecondary, iconThemeColor)),

                if (_loading)
                  const SliverFillRemaining(
                    child: Center(
                      child: CircularProgressIndicator(color: AppColors.primary),
                    ),
                  )
                else if (_error != null)
                  SliverFillRemaining(
                    child: Center(
                      child: Padding(
                        padding: const EdgeInsets.all(24),
                        child: Column(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            const Icon(Icons.error_outline_rounded, color: AppColors.error, size: 48),
                            const SizedBox(height: 12),
                            Text(_error!, style: const TextStyle(fontSize: 14, color: AppColors.textSec)),
                            const SizedBox(height: 16),
                            ElevatedButton(
                              onPressed: _loadStats,
                              style: ElevatedButton.styleFrom(backgroundColor: AppColors.primary),
                              child: const Text('Retry', style: TextStyle(color: Colors.white)),
                            ),
                          ],
                        ),
                      ),
                    ),
                  )
                else ...[
                  // Constrained/Responsive layout components
                  SliverPadding(
                    padding: EdgeInsets.symmetric(horizontal: horizontalPadding, vertical: 12),
                    sliver: SliverList(
                      delegate: SliverChildListDelegate([
                        _buildTodayStats(),
                        const SizedBox(height: 14),
                        _buildStatsGrid(isTablet, cardBg, borderColor, textPrimary, textSecondary),
                        const SizedBox(height: 18),
                        _buildQuickActions(isTablet, cardBg, borderColor, textPrimary),
                        const SizedBox(height: 18),
                        _buildInventoryAlerts(),
                        const SizedBox(height: 18),
                        _buildCashflowSection(cardBg, borderColor, textPrimary, textSecondary),
                        const SizedBox(height: 18),
                        _buildRecentSales(cardBg, borderColor, textPrimary, textSecondary),
                      ]),
                    ),
                  ),
                ],
                const SliverToBoxAdapter(child: SizedBox(height: 48)),
              ],
            ),
          ),
          // Interactive Guided Tour Walkthrough
          if (_showTour) _buildTourOverlay(),
        ],
      ),
    );
  }

  Widget _buildHeader(dynamic user, Color cardBg, Color borderColor, Color textPrimary, Color textSecondary, Color iconThemeColor) {
    final name = user?.name ?? 'User';
    final business = user?.businessName ?? 'Manna';

    return Container(
      color: cardBg,
      decoration: BoxDecoration(
        border: Border(bottom: BorderSide(color: borderColor)),
      ),
      child: SafeArea(
        bottom: false,
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 18, vertical: 12),
          child: Row(
            children: [
              // Business Storefront Avatar Box
              Container(
                width: 42,
                height: 42,
                decoration: BoxDecoration(
                  gradient: const LinearGradient(
                    colors: [AppColors.primary, AppColors.primaryDark],
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                  ),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: const Icon(Icons.storefront_rounded, color: Colors.white, size: 22),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(name, style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700, color: textPrimary)),
                    const SizedBox(height: 1),
                    Text(business, style: TextStyle(fontSize: 11, color: textSecondary, fontWeight: FontWeight.w500)),
                  ],
                ),
              ),
              // Live active indicator
              Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Container(
                    width: 7,
                    height: 7,
                    decoration: const BoxDecoration(color: Color(0xFF10B981), shape: BoxShape.circle),
                  ),
                  const SizedBox(width: 5),
                  const Text('LIVE', style: TextStyle(color: Color(0xFF10B981), fontSize: 10, fontWeight: FontWeight.w800)),
                ],
              ),
              const SizedBox(width: 16),
              // Dark Mode Toggle Icon Button
              GestureDetector(
                onTap: () => setState(() => _isDark = !_isDark),
                child: Icon(
                  _isDark ? Icons.wb_sunny_rounded : Icons.dark_mode_rounded,
                  size: 22,
                  color: _isDark ? Colors.amber : const Color(0xFF4B5563),
                ),
              ),
              const SizedBox(width: 16),
              // Refresh Icon
              GestureDetector(
                onTap: _refreshing ? null : _loadStats,
                child: _refreshing
                    ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(color: AppColors.primary, strokeWidth: 2))
                    : const Icon(Icons.refresh_rounded, size: 22, color: AppColors.primary),
              ),
              const SizedBox(width: 16),
              GestureDetector(
                onTap: () => context.push('/settings'),
                child: Icon(Icons.settings_outlined, size: 22, color: iconThemeColor),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildTodayStats() {
    if (_stats == null) return const SizedBox.shrink();
    final todaySales = (_stats!['today_sales'] ?? 0).toDouble();
    final todayOrders = _stats!['today_orders'] ?? 0;
    final weekSales = (_stats!['week_sales'] ?? 0).toDouble();

    return Container(
      decoration: BoxDecoration(
        gradient: const LinearGradient(
          colors: [AppColors.primary, AppColors.primaryDark],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: AppColors.primary.withValues(alpha: 0.15),
            blurRadius: 16,
            offset: const Offset(0, 6),
          ),
        ],
      ),
      padding: const EdgeInsets.all(20),
      child: Row(
        children: [
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text('TODAY\'S REVENUE', style: TextStyle(color: Colors.white70, fontSize: 11, fontWeight: FontWeight.w800, letterSpacing: 0.5)),
                const SizedBox(height: 6),
                Text(
                  'TSh ${fmtCurrency(todaySales)}',
                  style: const TextStyle(color: Colors.white, fontSize: 26, fontWeight: FontWeight.w900, letterSpacing: -0.5),
                ),
                const SizedBox(height: 8),
                Text(
                  '$todayOrders completed orders · TSh ${fmtCurrency(weekSales)} this week',
                  style: const TextStyle(color: Colors.white70, fontSize: 12, fontWeight: FontWeight.w500),
                ),
              ],
            ),
          ),
          Container(
            width: 48,
            height: 40,
            decoration: BoxDecoration(
              color: Colors.white.withValues(alpha: 0.18),
              borderRadius: BorderRadius.circular(10),
            ),
            child: const Icon(Icons.trending_up_rounded, color: Colors.white, size: 24),
          ),
        ],
      ),
    );
  }

  Widget _buildStatsGrid(bool isTablet, Color cardBg, Color borderColor, Color textPrimary, Color textSecondary) {
    if (_stats == null) return const SizedBox.shrink();
    final s = _stats!;

    final salesGrowth = s['sales_growth'] != null ? (s['sales_growth'] as num).toInt() : 0;
    final ordersGrowth = s['orders_growth'] != null ? (s['orders_growth'] as num).toInt() : 0;

    final cards = [
      _StatCard(
        icon: Icons.auto_graph_rounded,
        value: 'TSh ${fmtCurrency((s['total_sales'] ?? 0).toDouble())}',
        label: 'Sales (Month)',
        color: const Color(0xFF0D9488),
        bg: const Color(0xFFF0FDFA),
        subtitle: salesGrowth != 0 ? '${salesGrowth > 0 ? '+' : ''}$salesGrowth% vs last month' : 'No prior sales',
        cardBg: cardBg,
        borderColor: borderColor,
        textPrimary: textPrimary,
        textSecondary: textSecondary,
      ),
      _StatCard(
        icon: Icons.shopping_bag_outlined,
        value: '${s['total_orders'] ?? 0}',
        label: 'Orders Count',
        color: const Color(0xFF2563EB),
        bg: const Color(0xFFEFF6FF),
        subtitle: ordersGrowth != 0 ? '${ordersGrowth > 0 ? '+' : ''}$ordersGrowth% vs last month' : 'No prior orders',
        cardBg: cardBg,
        borderColor: borderColor,
        textPrimary: textPrimary,
        textSecondary: textSecondary,
      ),
      _StatCard(
        icon: Icons.layers_outlined,
        value: '${s['total_products'] ?? 0}',
        label: 'Products',
        color: const Color(0xFF7C3AED),
        bg: const Color(0xFFF5F3FF),
        subtitle: 'In active inventory',
        cardBg: cardBg,
        borderColor: borderColor,
        textPrimary: textPrimary,
        textSecondary: textSecondary,
      ),
      _StatCard(
        icon: Icons.people_alt_outlined,
        value: '${s['total_customers'] ?? 0}',
        label: 'Customers',
        color: const Color(0xFFEA580C),
        bg: const Color(0xFFFFF7ED),
        subtitle: 'Registered buyers',
        cardBg: cardBg,
        borderColor: borderColor,
        textPrimary: textPrimary,
        textSecondary: textSecondary,
      ),
    ];

    if (isTablet) {
      // 4 columns in 1 row on tablet
      return Row(
        children: cards.map((card) => Expanded(child: Padding(padding: const EdgeInsets.symmetric(horizontal: 4), child: card))).toList(),
      );
    } else {
      // 2x2 grid on mobile
      return Column(
        children: [
          Row(
            children: [
              Expanded(child: cards[0]),
              const SizedBox(width: 10),
              Expanded(child: cards[1]),
            ],
          ),
          const SizedBox(height: 10),
          Row(
            children: [
              Expanded(child: cards[2]),
              const SizedBox(width: 10),
              Expanded(child: cards[3]),
            ],
          ),
        ],
      );
    }
  }

  Widget _buildQuickActions(bool isTablet, Color cardBg, Color borderColor, Color textPrimary) {
    final actions = [
      _ActionTile(
        icon: Icons.add_shopping_cart_rounded,
        label: 'New Sale',
        color: const Color(0xFF0D9488),
        onTap: () => context.push('/sales'),
        cardBg: cardBg,
        borderColor: borderColor,
        textPrimary: textPrimary,
      ),
      _ActionTile(
        icon: Icons.add_box_outlined,
        label: 'Add Product',
        color: const Color(0xFF2563EB),
        onTap: () => context.push('/products'),
        cardBg: cardBg,
        borderColor: borderColor,
        textPrimary: textPrimary,
      ),
      _ActionTile(
        icon: Icons.person_add_alt_1_outlined,
        label: 'Add Customer',
        color: const Color(0xFF7C3AED),
        onTap: () => context.push('/customers'),
        cardBg: cardBg,
        borderColor: borderColor,
        textPrimary: textPrimary,
      ),
    ];

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text('Quick Actions', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700, color: Colors.black)),
        const SizedBox(height: 12),
        Row(
          children: actions.map((action) => Expanded(child: Padding(padding: const EdgeInsets.symmetric(horizontal: 4), child: action))).toList(),
        ),
      ],
    );
  }

  Widget _buildInventoryAlerts() {
    if (_stats == null) return const SizedBox.shrink();
    final lowStock = _stats!['low_stock'] ?? 0;
    final outOfStock = _stats!['out_of_stock'] ?? 0;
    if (lowStock == 0 && outOfStock == 0) return const SizedBox.shrink();

    final bool isSevere = outOfStock > 0;

    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: isSevere ? const Color(0xFFFEF2F2) : const Color(0xFFFFFBEB),
        borderRadius: BorderRadius.circular(12),
        border: Border.all(
          color: isSevere ? const Color(0xFFFCA5A5) : const Color(0xFFFDE68A),
        ),
      ),
      child: Row(
        children: [
          Icon(
            isSevere ? Icons.error_outline_rounded : Icons.warning_amber_rounded,
            color: isSevere ? AppColors.danger : AppColors.warning,
            size: 24,
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  isSevere ? 'Inventory Alert' : 'Low Stock Warning',
                  style: TextStyle(fontWeight: FontWeight.w700, fontSize: 14, color: isSevere ? AppColors.danger : AppColors.warning),
                ),
                Text(
                  '$outOfStock out of stock · $lowStock low stock items',
                  style: const TextStyle(color: Color(0xFF71717A), fontSize: 12),
                ),
              ],
            ),
          ),
          TextButton(
            onPressed: () => context.push('/products'),
            child: Text(
              'View',
              style: TextStyle(color: isSevere ? AppColors.danger : AppColors.warning, fontWeight: FontWeight.bold),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildCashflowSection(Color cardBg, Color borderColor, Color textPrimary, Color textSecondary) {
    if (_stats == null) return const SizedBox.shrink();
    final chartData = _stats!['sales_chart'] as List?;
    if (chartData == null || chartData.isEmpty) return const SizedBox.shrink();

    // Map chartData into coordinate points tuple list
    final List<(String, double)> dataList = chartData.map((e) {
      final String label = e['label']?.toString() ?? '';
      final double total = (e['total'] as num?)?.toDouble() ?? 0.0;
      return (label, total);
    }).toList();

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          children: [
            Text('Cashflow ', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700, color: textPrimary)),
            Text('(Last 7 Days)', style: TextStyle(fontSize: 14, color: textSecondary)),
          ],
        ),
        const SizedBox(height: 12),
        Container(
          decoration: BoxDecoration(color: cardBg, borderRadius: BorderRadius.circular(12), border: Border.all(color: borderColor)),
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  _legendDot(color: AppColors.primary),
                  const SizedBox(width: 6),
                  Text('Revenue', style: TextStyle(fontSize: 12, color: textSecondary, fontWeight: FontWeight.w500)),
                ],
              ),
              const SizedBox(height: 18),
              SizedBox(
                height: 160,
                child: _CashflowChart(data: dataList, isDark: _isDark),
              ),
            ],
          ),
        ),
      ],
    );
  }

  Widget _legendDot({required Color color}) {
    return Container(width: 8, height: 8, decoration: BoxDecoration(color: color, shape: BoxShape.circle));
  }

  Widget _buildRecentSales(Color cardBg, Color borderColor, Color textPrimary, Color textSecondary) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          children: [
            Text('Recent Sales', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700, color: textPrimary)),
            const Spacer(),
            GestureDetector(
              onTap: () => context.push('/sales'),
              child: const Row(
                children: [
                  Text('See All', style: TextStyle(fontSize: 13, color: AppColors.primary, fontWeight: FontWeight.w600)),
                  SizedBox(width: 2),
                  Icon(Icons.chevron_right_rounded, size: 16, color: AppColors.primary),
                ],
              ),
            ),
          ],
        ),
        const SizedBox(height: 12),
        if (_recentSales.isEmpty)
          Container(
            padding: const EdgeInsets.all(24),
            decoration: BoxDecoration(color: cardBg, borderRadius: BorderRadius.circular(12), border: Border.all(color: borderColor)),
            child: const Center(child: Text('No sales yet', style: TextStyle(color: AppColors.textSec))),
          )
        else
          Container(
            decoration: BoxDecoration(color: cardBg, borderRadius: BorderRadius.circular(12), border: Border.all(color: borderColor)),
            child: Column(
              children: _recentSales.take(5).map((s) => _buildSaleRow(s, textPrimary, textSecondary)).toList(),
            ),
          ),
      ],
    );
  }

  Widget _buildSaleRow(dynamic s, Color textPrimary, Color textSecondary) {
    final ref = s['reference'] ?? '';
    final total = (s['total'] ?? 0).toDouble();
    final status = s['status'] ?? 'completed';
    final date = fmtDateShort(s['sale_date']);
    final customer = s['customer']?['name'] ?? 'Walk-in';

    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
      child: Row(
        children: [
          Container(
            width: 38,
            height: 38,
            decoration: BoxDecoration(
              color: AppColors.primaryLt,
              borderRadius: BorderRadius.circular(10),
            ),
            child: const Icon(Icons.receipt_long_rounded, size: 18, color: AppColors.primary),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(ref, style: TextStyle(fontSize: 14, fontWeight: FontWeight.w700, color: textPrimary)),
                const SizedBox(height: 2),
                Text('$customer · $date', style: TextStyle(fontSize: 11, color: textSecondary, fontWeight: FontWeight.w500)),
              ],
            ),
          ),
          Text(
            'TSh ${fmtCurrency(total)}',
            style: TextStyle(fontSize: 14, fontWeight: FontWeight.w800, color: textPrimary),
          ),
          const SizedBox(width: 8),
          StatusBadge.fromStatus(status),
        ],
      ),
    );
  }

  Widget _buildTourOverlay() {
    final step = _tourSteps[_tourStep];
    final isLast = _tourStep == _tourSteps.length - 1;
    return Positioned.fill(
      child: Container(
        color: Colors.black.withValues(alpha: 0.65),
        child: Center(
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: 28),
            child: Container(
              padding: const EdgeInsets.all(24),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(20),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withValues(alpha: 0.2),
                    blurRadius: 32,
                    offset: const Offset(0, 10),
                  ),
                ],
              ),
              child: Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(step.$1, style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w800, color: Colors.black)),
                  const SizedBox(height: 10),
                  Text(step.$2, style: const TextStyle(fontSize: 14, color: Color(0xFF4B5563), height: 1.55)),
                  const SizedBox(height: 24),
                  Row(
                    children: [
                      Text(
                        '${_tourStep + 1}/${_tourSteps.length}',
                        style: const TextStyle(fontSize: 13, color: Color(0xFF9CA3AF), fontWeight: FontWeight.bold),
                      ),
                      const Spacer(),
                      if (!isLast)
                        TextButton(
                          onPressed: () => setState(() => _showTour = false),
                          child: const Text('Skip', style: TextStyle(color: Color(0xFF9CA3AF), fontSize: 15)),
                        ),
                      const SizedBox(width: 4),
                      ElevatedButton(
                        onPressed: () {
                          if (isLast) {
                            setState(() => _showTour = false);
                          } else {
                            setState(() => _tourStep++);
                          }
                        },
                        style: ElevatedButton.styleFrom(
                          backgroundColor: AppColors.primary,
                          foregroundColor: Colors.white,
                          elevation: 0,
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                          padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 10),
                        ),
                        child: Text(isLast ? 'Done' : 'Next', style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w700)),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
}

class _StatCard extends StatelessWidget {
  final IconData icon;
  final String value;
  final String label;
  final String subtitle;
  final Color color;
  final Color bg;
  final Color cardBg;
  final Color borderColor;
  final Color textPrimary;
  final Color textSecondary;

  const _StatCard({
    required this.icon,
    required this.value,
    required this.label,
    required this.subtitle,
    required this.color,
    required this.bg,
    required this.cardBg,
    required this.borderColor,
    required this.textPrimary,
    required this.textSecondary,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: cardBg,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: borderColor),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            width: 36,
            height: 36,
            decoration: BoxDecoration(color: bg, borderRadius: BorderRadius.circular(8)),
            child: Icon(icon, color: color, size: 20),
          ),
          const SizedBox(height: 12),
          Text(
            value,
            maxLines: 1,
            overflow: TextOverflow.ellipsis,
            style: TextStyle(fontSize: 15, fontWeight: FontWeight.w800, color: textPrimary, letterSpacing: -0.2),
          ),
          const SizedBox(height: 3),
          Text(
            label,
            style: TextStyle(fontSize: 11, color: textSecondary, fontWeight: FontWeight.w600),
          ),
          const SizedBox(height: 3),
          Text(
            subtitle,
            maxLines: 1,
            overflow: TextOverflow.ellipsis,
            style: TextStyle(fontSize: 10, color: color, fontWeight: FontWeight.w500),
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
  final Color cardBg;
  final Color borderColor;
  final Color textPrimary;

  const _ActionTile({
    required this.icon,
    required this.label,
    required this.color,
    required this.onTap,
    required this.cardBg,
    required this.borderColor,
    required this.textPrimary,
  });

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 16),
        decoration: BoxDecoration(
          color: cardBg,
          borderRadius: BorderRadius.circular(12),
          border: Border.all(color: borderColor),
        ),
        child: Column(
          children: [
            Container(
              width: 44,
              height: 44,
              decoration: BoxDecoration(
                color: color.withValues(alpha: 0.08),
                borderRadius: BorderRadius.circular(10),
              ),
              child: Icon(icon, size: 20, color: color),
            ),
            const SizedBox(height: 8),
            Text(
              label,
              textAlign: TextAlign.center,
              style: TextStyle(fontSize: 11, fontWeight: FontWeight.w600, color: textPrimary),
            ),
          ],
        ),
      ),
    );
  }
}

class _CashflowChart extends StatelessWidget {
  final List<(String, double)> data;
  final bool isDark;
  const _CashflowChart({required this.data, required this.isDark});
  @override
  Widget build(BuildContext context) {
    return CustomPaint(painter: _ChartPainter(data: data, isDark: isDark), child: const SizedBox.expand());
  }
}

class _ChartPainter extends CustomPainter {
  final List<(String, double)> data;
  final bool isDark;
  _ChartPainter({required this.data, required this.isDark});

  @override
  void paint(Canvas canvas, Size size) {
    if (data.isEmpty) return;
    final maxVal = data.map((d) => d.$2).reduce(math.max);
    const labelHeight = 20.0;
    const yAxisWidth = 42.0;
    final chartH = size.height - labelHeight;
    final chartW = size.width - yAxisWidth;
    final spacing = chartW / data.length;

    final yLabelPaint = TextPainter(textDirection: ui.TextDirection.ltr);
    final yLines = [1.0, 0.75, 0.5, 0.25];
    final gridPaint = Paint()
      ..color = isDark ? const Color(0xFF374151) : const Color(0xFFF1F5F9)
      ..strokeWidth = 1;

    for (final frac in yLines) {
      final y = chartH * (1 - frac);
      canvas.drawLine(Offset(yAxisWidth, y), Offset(size.width, y), gridPaint);
      final label = fmtNum(maxVal * frac);
      yLabelPaint.text = TextSpan(
        text: label,
        style: TextStyle(fontSize: 9, color: isDark ? const Color(0xFF6B7280) : const Color(0xFF94A3B8), fontWeight: FontWeight.w500),
      );
      yLabelPaint.layout();
      yLabelPaint.paint(canvas, Offset(0, y - yLabelPaint.height / 2));
    }

    final linePaint = Paint()
      ..color = AppColors.primary
      ..strokeWidth = 2.5
      ..style = PaintingStyle.stroke
      ..strokeJoin = StrokeJoin.round;

    final fillPaint = Paint()
      ..shader = LinearGradient(
        colors: [AppColors.primary.withValues(alpha: 0.25), AppColors.primary.withValues(alpha: 0.0)],
        begin: Alignment.topCenter,
        end: Alignment.bottomCenter,
      ).createShader(Rect.fromLTWH(yAxisWidth, 0, chartW, chartH))
      ..style = PaintingStyle.fill;

    final dotPaint = Paint()..color = AppColors.primary..style = PaintingStyle.fill;
    final points = <Offset>[];

    for (int i = 0; i < data.length; i++) {
      final val = data[i].$2;
      final normH = (val / (maxVal > 0 ? maxVal : 1.0)) * (chartH * 0.85);
      final x = yAxisWidth + i * spacing + spacing / 2;
      final y = chartH - normH;

      points.add(Offset(x, y));

      final xLabel = TextPainter(
        text: TextSpan(
          text: data[i].$1,
          style: TextStyle(fontSize: 9, color: isDark ? const Color(0xFF6B7280) : const Color(0xFF94A3B8), fontWeight: FontWeight.w500),
        ),
        textDirection: ui.TextDirection.ltr,
      )..layout();
      xLabel.paint(canvas, Offset(x - xLabel.width / 2, chartH + 4));
    }

    if (points.length > 1) {
      final path = Path()..moveTo(points[0].dx, points[0].dy);
      final fillPath = Path()..moveTo(points[0].dx, chartH)..lineTo(points[0].dx, points[0].dy);

      for (int i = 1; i < points.length; i++) {
        final cp1 = Offset((points[i - 1].dx + points[i].dx) / 2, points[i - 1].dy);
        final cp2 = Offset((points[i - 1].dx + points[i].dx) / 2, points[i].dy);
        path.cubicTo(cp1.dx, cp1.dy, cp2.dx, cp2.dy, points[i].dx, points[i].dy);
        fillPath.cubicTo(cp1.dx, cp1.dy, cp2.dx, cp2.dy, points[i].dx, points[i].dy);
      }

      fillPath.lineTo(points.last.dx, chartH);
      fillPath.close();
      canvas.drawPath(fillPath, fillPaint);
      canvas.drawPath(path, linePaint);
    }

    for (final pt in points) {
      canvas.drawCircle(pt, 4.0, dotPaint);
      canvas.drawCircle(pt, 4.0, Paint()..color = Colors.white..style = PaintingStyle.stroke..strokeWidth = 2.0);
    }
  }

  @override
  bool shouldRepaint(_ChartPainter old) => old.data != data;
}
