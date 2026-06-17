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
  bool _showBanner = true;

  // ── Tour states ────────────────────────────────────────────────
  bool _showTour = true;
  int _tourStep = 0;
  final _tourSteps = const [
    ('Dashboard Overview', 'Track your business in real-time. View receivables, payables, sales, purchases, and cashflow at a glance.'),
    ('Financial Cards', 'Monitor To Receive and To Give amounts. Keep your cash position healthy.'),
    ('Quick Actions', 'Use shortcuts for Sales Invoice, Purchase, Expense, and more — all in one tap.'),
    ('Cashflow Analytics', 'Visualize your last 7 days revenue trends with an interactive chart.'),
    ('All Set!', 'You are ready to run your business like a pro with Manna POS.'),
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
                        _summaryCards(),
                        const SizedBox(height: 20),
                        _financialGrid(isTablet),
                        const SizedBox(height: 24),
                        _exploreApp(),
                        const SizedBox(height: 24),
                        if (_showBanner) _tutorialBanner(),
                        if (_showBanner) const SizedBox(height: 24),
                        _shortcuts(),
                        const SizedBox(height: 24),
                        _cashflowChart(),
                        const SizedBox(height: 24),
                        _recentTransactions(),
                        const SizedBox(height: 40),
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
      decoration: BoxDecoration(
        color: cardBg,
        border: Border(bottom: BorderSide(color: borderColor)),
      ),
      child: SafeArea(
        bottom: false,
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 18, vertical: 12),
          child: Row(
            children: [
              // Business Storefront Avatar Box
              GestureDetector(
                onTap: () => context.push('/my-business'),
                child: Container(
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

  Widget _summaryCards() {
    if (_stats == null) return const SizedBox.shrink();
    final receive = (_stats!['to_receive'] ?? 0).toDouble();
    final give = (_stats!['to_give'] ?? 0).toDouble();

    return Row(
      children: [
        Expanded(
          child: _summaryCard(
            label: 'To Receive',
            amount: receive,
            icon: Icons.arrow_downward_rounded,
            iconColor: const Color(0xFF10B981),
            bgColor: _isDark ? _card : const Color(0xFFECFDF5),
            borderColor: _isDark ? _border : const Color(0xFFA7F3D0),
          ),
        ),
        const SizedBox(width: 12),
        Expanded(
          child: _summaryCard(
            label: 'To Give',
            amount: give,
            icon: Icons.arrow_upward_rounded,
            iconColor: const Color(0xFFEF4444),
            bgColor: _isDark ? _card : const Color(0xFFFEF2F2),
            borderColor: _isDark ? _border : const Color(0xFFFECACA),
          ),
        ),
      ],
    );
  }

  Widget _summaryCard({
    required String label,
    required double amount,
    required IconData icon,
    required Color iconColor,
    required Color bgColor,
    required Color borderColor,
  }) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: bgColor,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: borderColor),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                width: 32,
                height: 32,
                decoration: BoxDecoration(
                  color: iconColor.withValues(alpha: 0.12),
                  borderRadius: BorderRadius.circular(8),
                ),
                child: Icon(icon, size: 16, color: iconColor),
              ),
              const Spacer(),
              const Icon(Icons.chevron_right_rounded, size: 18, color: Color(0xFF94A3B8)),
            ],
          ),
          const SizedBox(height: 12),
          Text(
            '${_currencySymbol} ${fmtCurrency(amount)}',
            style: TextStyle(fontSize: 18, fontWeight: FontWeight.w800, color: _txt, letterSpacing: -0.5),
          ),
          const SizedBox(height: 4),
          Text(label, style: TextStyle(fontSize: 12, color: _txt2, fontWeight: FontWeight.w500)),
        ],
      ),
    );
  }

  Widget _financialGrid(bool isTablet) {
    if (_stats == null) return const SizedBox.shrink();
    final s = _stats!;

    final items = [
      _finItem('Sales', (s['total_sales'] ?? 0).toDouble(), Icons.show_chart_rounded, const Color(0xFF10B981)),
      _finItem('Purchase', (s['total_purchases'] ?? 0).toDouble(), Icons.shopping_cart_outlined, const Color(0xFF3B82F6)),
      _finItem('Expense', (s['total_expenses'] ?? 0).toDouble(), Icons.receipt_long_outlined, const Color(0xFFF59E0B)),
      _finItem('Total Balance', (s['total_balance'] ?? 0).toDouble(), Icons.account_balance_wallet_outlined, const Color(0xFF8B5CF6)),
    ];

    if (isTablet) {
      return Row(
        children: items.map((i) => Expanded(child: Padding(padding: const EdgeInsets.symmetric(horizontal: 6), child: i))).toList(),
      );
    }

    return Column(
      children: [
        Row(children: [Expanded(child: items[0]), const SizedBox(width: 12), Expanded(child: items[1])]),
        const SizedBox(height: 12),
        Row(children: [Expanded(child: items[2]), const SizedBox(width: 12), Expanded(child: items[3])]),
      ],
    );
  }

  Widget _finItem(String label, double val, IconData icon, Color color) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: _card,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: _border),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                width: 36,
                height: 36,
                decoration: BoxDecoration(color: color.withValues(alpha: 0.08), borderRadius: BorderRadius.circular(10)),
                child: Icon(icon, size: 18, color: color),
              ),
              const Spacer(),
              const Icon(Icons.chevron_right_rounded, size: 16, color: Color(0xFF94A3B8)),
            ],
          ),
          const SizedBox(height: 14),
          Text('${_currencySymbol} ${fmtCurrency(val)}', style: TextStyle(fontSize: 15, fontWeight: FontWeight.w800, color: _txt)),
          const SizedBox(height: 4),
          Text(label, style: TextStyle(fontSize: 12, color: _txt2, fontWeight: FontWeight.w500)),
        ],
      ),
    );
  }

  Widget _exploreApp() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text('Explore App', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700, color: _txt)),
        const SizedBox(height: 14),
        Row(
          children: [
            _exploreTile(Icons.grid_view_rounded, 'Quick Entry', const Color(0xFF10B981)),
            const SizedBox(width: 12),
            _exploreTile(Icons.point_of_sale_rounded, 'Quick POS', const Color(0xFF3B82F6)),
            const SizedBox(width: 12),
            _exploreTile(Icons.bar_chart_rounded, 'View Reports', const Color(0xFFF59E0B)),
          ],
        ),
      ],
    );
  }

  Widget _exploreTile(IconData icon, String label, Color color) {
    return Expanded(
      child: GestureDetector(
        onTap: () {
          if (label == 'Quick POS') context.push('/sales');
          if (label == 'View Reports') context.push('/reports');
        },
        child: Container(
          padding: const EdgeInsets.symmetric(vertical: 18),
          decoration: BoxDecoration(
            color: _card,
            borderRadius: BorderRadius.circular(14),
            border: Border.all(color: _border),
          ),
          child: Column(
            children: [
              Icon(icon, size: 24, color: color),
              const SizedBox(height: 8),
              Text(label, style: TextStyle(fontSize: 12, fontWeight: FontWeight.w600, color: _txt)),
            ],
          ),
        ),
      ),
    );
  }

  Widget _tutorialBanner() {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: _card,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: _border),
      ),
      child: Row(
        children: [
          Container(
            width: 44,
            height: 44,
            decoration: BoxDecoration(
              color: const Color(0xFFFEE2E2),
              borderRadius: BorderRadius.circular(12),
            ),
            child: const Icon(Icons.play_circle_fill_rounded, color: Color(0xFFEF4444), size: 22),
          ),
          const SizedBox(width: 14),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text('How to use the app?', style: TextStyle(fontSize: 14, fontWeight: FontWeight.w700, color: _txt)),
                const SizedBox(height: 2),
                Text('To manage & grow your business', style: TextStyle(fontSize: 12, color: _txt2)),
              ],
            ),
          ),
          GestureDetector(
            onTap: () => setState(() => _showBanner = false),
            child: Icon(Icons.close_rounded, size: 20, color: _txt2),
          ),
        ],
      ),
    );
  }

  Widget _shortcuts() {
    final shortcuts = [
      (Icons.person_add_alt_1_outlined, 'Add Party', '/customers'),
      (Icons.receipt_outlined, 'Sales Invoice', '/sales'),
      (Icons.payments_outlined, 'Payment In', '/sales'),
      (Icons.payment_outlined, 'Payment Out', '/expenses'),
      (Icons.shopping_bag_outlined, 'Purchase', '/purchases'),
      (Icons.add_box_outlined, 'Add Item', '/products'),
      (Icons.receipt_long_outlined, 'Expense', '/expenses'),
      (Icons.note_add_outlined, 'Add Note', '/settings'),
    ];

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          children: [
            Text('Shortcuts', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700, color: _txt)),
            const Spacer(),
            GestureDetector(
              onTap: () => context.push('/settings'),
              child: Row(
                children: [
                  Icon(Icons.edit_outlined, size: 16, color: const Color(0xFF10B981)),
                  const SizedBox(width: 4),
                  Text('Edit Menu', style: TextStyle(fontSize: 13, fontWeight: FontWeight.w600, color: const Color(0xFF10B981))),
                ],
              ),
            ),
          ],
        ),
        const SizedBox(height: 14),
        GridView.count(
          shrinkWrap: true,
          physics: const NeverScrollableScrollPhysics(),
          crossAxisCount: 4,
          mainAxisSpacing: 12,
          crossAxisSpacing: 12,
          childAspectRatio: 0.85,
          children: shortcuts.map((s) => _shortcutTile(s.$1, s.$2, s.$3)).toList(),
        ),
      ],
    );
  }

  Widget _shortcutTile(IconData icon, String label, String route) {
    return GestureDetector(
      onTap: () => context.push(route),
      child: Container(
        decoration: BoxDecoration(
          color: _card,
          borderRadius: BorderRadius.circular(12),
          border: Border.all(color: _border),
        ),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Container(
              width: 44,
              height: 44,
              decoration: BoxDecoration(
                color: const Color(0xFF10B981).withValues(alpha: 0.08),
                shape: BoxShape.circle,
              ),
              child: Icon(icon, size: 20, color: const Color(0xFF10B981)),
            ),
            const SizedBox(height: 8),
            Text(label, textAlign: TextAlign.center, style: TextStyle(fontSize: 11, fontWeight: FontWeight.w500, color: _txt)),
          ],
        ),
      ),
    );
  }

  Widget _cashflowChart() {
    if (_stats == null) return const SizedBox.shrink();
    final chartData = _stats!['sales_chart'] as List?;
    if (chartData == null || chartData.isEmpty) return const SizedBox.shrink();

    final data = chartData.map((e) {
      return (e['label']?.toString() ?? '', (e['total'] as num?)?.toDouble() ?? 0.0);
    }).toList();

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          children: [
            Text('Cashflow ', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700, color: _txt)),
            Text('(Last 7 Days)', style: TextStyle(fontSize: 14, color: _txt2)),
            const Spacer(),
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
              decoration: BoxDecoration(color: _isDark ? const Color(0xFF334155) : const Color(0xFFF1F5F9), borderRadius: BorderRadius.circular(20)),
              child: Row(
                children: [
                  const Icon(Icons.calendar_today_rounded, size: 12, color: Color(0xFF64748B)),
                  const SizedBox(width: 4),
                  Text('Daily', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w600, color: _txt2)),
                  const Icon(Icons.keyboard_arrow_down_rounded, size: 14, color: Color(0xFF64748B)),
                ],
              ),
            ),
          ],
        ),
        const SizedBox(height: 14),
        Container(
          decoration: BoxDecoration(color: _card, borderRadius: BorderRadius.circular(14), border: Border.all(color: _border)),
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  Container(width: 8, height: 8, decoration: const BoxDecoration(color: Color(0xFF10B981), shape: BoxShape.circle)),
                  const SizedBox(width: 6),
                  Text('Revenue', style: TextStyle(fontSize: 12, color: _txt2, fontWeight: FontWeight.w500)),
                ],
              ),
              const SizedBox(height: 16),
              SizedBox(height: 160, child: _CashflowChart(data: data, isDark: _isDark)),
            ],
          ),
        ),
      ],
    );
  }

  Widget _recentTransactions() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          children: [
            Text('Recent Transactions', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700, color: _txt)),
            const Spacer(),
            GestureDetector(
              onTap: () => context.push('/sales'),
              child: Row(
                children: [
                  Text('See All', style: TextStyle(fontSize: 13, fontWeight: FontWeight.w600, color: const Color(0xFF10B981))),
                  const SizedBox(width: 2),
                  const Icon(Icons.chevron_right_rounded, size: 16, color: Color(0xFF10B981)),
                ],
              ),
            ),
          ],
        ),
        const SizedBox(height: 14),
        if (_recentSales.isEmpty)
          Container(
            padding: const EdgeInsets.all(24),
            decoration: BoxDecoration(color: _card, borderRadius: BorderRadius.circular(14), border: Border.all(color: _border)),
            child: Center(child: Text('No transactions yet', style: TextStyle(color: _txt2, fontSize: 14))),
          )
        else
          Container(
            decoration: BoxDecoration(color: _card, borderRadius: BorderRadius.circular(14), border: Border.all(color: _border)),
            child: Column(
              children: _recentSales.take(5).map((s) => _txRow(s)).toList(),
            ),
          ),
      ],
    );
  }

  Widget _txRow(dynamic s) {
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
            width: 40,
            height: 40,
            decoration: BoxDecoration(color: const Color(0xFFECFDF5), borderRadius: BorderRadius.circular(10)),
            child: const Icon(Icons.receipt_long_rounded, size: 18, color: Color(0xFF10B981)),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(ref, style: TextStyle(fontSize: 14, fontWeight: FontWeight.w600, color: _txt)),
                const SizedBox(height: 2),
                Text('$customer · $date', style: TextStyle(fontSize: 11, color: _txt2)),
              ],
            ),
          ),
          Text('${_currencySymbol} ${fmtCurrency(total)}', style: TextStyle(fontSize: 14, fontWeight: FontWeight.w700, color: _txt)),
          const SizedBox(width: 8),
          StatusBadge.fromStatus(status),
        ],
      ),
    );
  }

  Widget _tourOverlay() {
    final step = _tourSteps[_tourStep];
    final isLast = _tourStep == _tourSteps.length - 1;
    return Positioned.fill(
      child: Container(
        color: Colors.black.withValues(alpha: 0.6),
        child: Center(
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: 28),
            child: Container(
              padding: const EdgeInsets.all(24),
              decoration: BoxDecoration(
                color: _isDark ? const Color(0xFF1E293B) : Colors.white,
                borderRadius: BorderRadius.circular(20),
                boxShadow: [BoxShadow(color: Colors.black.withValues(alpha: 0.2), blurRadius: 32, offset: const Offset(0, 10))],
              ),
              child: Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(step.$1, style: TextStyle(fontSize: 18, fontWeight: FontWeight.w800, color: _txt)),
                  const SizedBox(height: 10),
                  Text(step.$2, style: TextStyle(fontSize: 14, color: _txt2, height: 1.55)),
                  const SizedBox(height: 24),
                  Row(
                    children: [
                      Text('${_tourStep + 1}/${_tourSteps.length}', style: TextStyle(fontSize: 13, color: _txt2, fontWeight: FontWeight.bold)),
                      const Spacer(),
                      if (!isLast)
                        TextButton(
                          onPressed: () => setState(() => _showTour = false),
                          child: Text('Skip', style: TextStyle(color: _txt2, fontSize: 15)),
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
                          backgroundColor: const Color(0xFF10B981),
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

  String get _currencySymbol {
    final user = context.read<AuthProvider>().user;
    final map = {'TZS': 'TSh', 'USD': '\$', 'EUR': '€', 'KES': 'KSh', 'UGX': 'USh', 'GBP': '£'};
    return map[user?.currency] ?? user?.currency ?? 'TSh';
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
