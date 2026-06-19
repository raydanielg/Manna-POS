import 'package:flutter/material.dart';
import 'dart:async';
import 'package:intl/intl.dart';
import '../../core/api_service.dart';
import '../../shared/theme/app_theme.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/stat_card.dart';
import '../../widgets/shimmer_loading.dart';
import '../../widgets/empty_state.dart';
import '../../widgets/section_header.dart';
import '../../widgets/status_badge.dart';
import '../../widgets/chart_widgets.dart';

class BankingDashboardScreen extends StatefulWidget {
  const BankingDashboardScreen({super.key});
  @override State<BankingDashboardScreen> createState() => _BankingDashboardScreenState();
}

class _BankingDashboardScreenState extends State<BankingDashboardScreen> {
  bool _loading = true;
  String? _error;
  Map<String, dynamic>? _data;
  final _fmt = NumberFormat('#,##0.00');
  final _currency = 'TSh';

  @override
  void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    setState(() { _loading = true; _error = null; });
    try {
      final res = await ApiService.get('/dashboard/banking');
      setState(() { _data = res; _loading = false; });
    } on ApiException catch (e) { setState(() { _error = e.message; _loading = false; }); }
    catch (_) { setState(() { _error = 'Connection error'; _loading = false; }); }
  }

  String _f(dynamic v) => '$_currency ${_fmt.format((v is num ? v.toDouble() : double.tryParse(v?.toString() ?? '0') ?? 0))}';

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF0F172A),
      body: _loading
          ? const ShimmerLoading(itemCount: 8)
          : _error != null
              ? Center(child: Column(mainAxisAlignment: MainAxisAlignment.center, children: [
                  const Icon(Icons.error_outline, size: 48, color: Colors.white38),
                  const SizedBox(height: 12),
                  Text(_error!, style: const TextStyle(color: Colors.white54)),
                  const SizedBox(height: 16),
                  ElevatedButton(onPressed: _load, child: const Text('Retry')),
                ]))
              : RefreshIndicator(
                  onRefresh: _load,
                  color: AppColors.primary,
                  child: SingleChildScrollView(
                    physics: const BouncingScrollPhysics(),
                    child: Column(children: [
                      _buildHeader(),
                      _buildStatCards(),
                      const SizedBox(height: 20),
                      _buildDonutChart(),
                      const SizedBox(height: 20),
                      _buildQuickActions(),
                      const SizedBox(height: 20),
                      _buildAccounts(),
                      const SizedBox(height: 20),
                      _buildRecentTransactions(),
                      const SizedBox(height: 40),
                    ]),
                  ),
                ),
    );
  }

  Widget _buildHeader() {
    final totalBalance = _data?['total_balance'] ?? 0;
    return GradientHeader(
      title: 'Total Balance',
      amount: _f(totalBalance),
      subtitle: 'Banking Overview',
      colors: const [Color(0xFF1A3A5C), Color(0xFF0F1B2D)],
      height: 220,
      trailing: Container(
        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
        decoration: BoxDecoration(color: Colors.white.withValues(alpha: 0.15), borderRadius: BorderRadius.circular(20)),
        child: const Row(mainAxisSize: MainAxisSize.min, children: [
          Icon(Icons.account_balance, color: Colors.white, size: 14),
          SizedBox(width: 6),
          Text('ALL ACCOUNTS', style: TextStyle(color: Colors.white, fontSize: 11, fontWeight: FontWeight.w700)),
        ]),
      ),
    );
  }

  Widget _buildStatCards() {
    final stats = _data?['stats'] ?? {};
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16),
      child: Column(children: [
        Row(children: [
          Expanded(child: StatCard(icon: Icons.account_balance_wallet_rounded, value: _f(stats['total_balance'] ?? 0), label: 'Total Balance', color: AppColors.primary)),
          const SizedBox(width: 12),
          Expanded(child: StatCard(icon: Icons.trending_up_rounded, value: _f(stats['money_in'] ?? 0), label: 'Money In (Month)', color: AppColors.success)),
        ]),
        const SizedBox(height: 12),
        Row(children: [
          Expanded(child: StatCard(icon: Icons.trending_down_rounded, value: _f(stats['money_out'] ?? 0), label: 'Money Out', color: AppColors.secondary)),
          const SizedBox(width: 12),
          Expanded(child: StatCard(icon: Icons.pending_actions_rounded, value: _f(stats['pending'] ?? 0), label: 'Pending', color: AppColors.warning)),
        ]),
      ]),
    );
  }

  Widget _buildDonutChart() {
    final dist = (_data?['payment_distribution'] as List?) ?? [];
    if (dist.isEmpty) return const SizedBox.shrink();
    return GlassCard(
      margin: const EdgeInsets.symmetric(horizontal: 16),
      child: Column(children: [
        const SectionHeader(title: 'Payment Distribution', horizontalPadding: 0),
        const SizedBox(height: 12),
        DonutChartWidget(
          items: dist.map<DonutChartItem>((d) => DonutChartItem(
            label: d['label']?.toString() ?? '',
            value: (d['value'] as num?)?.toDouble() ?? 0,
            color: _chartColor(d['label']?.toString() ?? ''),
          )).toList(),
          centerText: '${dist.length}',
          centerSubText: 'Categories',
        ),
      ]),
    );
  }

  Color _chartColor(String label) {
    switch (label.toLowerCase()) {
      case 'transfer': return AppColors.primary;
      case 'deposit': return AppColors.success;
      case 'withdrawal': case 'withdraw': return AppColors.warning;
      case 'payment': return AppColors.purple;
      case 'fee': return AppColors.secondary;
      default: return AppColors.cyan;
    }
  }

  Widget _buildQuickActions() {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16),
      child: Row(children: [
        _actionBtn(Icons.swap_horiz_rounded, 'Transfer', AppColors.primary, () {}),
        const SizedBox(width: 12),
        _actionBtn(Icons.add_card_rounded, 'Deposit', AppColors.success, () {}),
        const SizedBox(width: 12),
        _actionBtn(Icons.remove_circle_outline_rounded, 'Withdraw', AppColors.warning, () {}),
      ]),
    );
  }

  Widget _actionBtn(IconData icon, String label, Color color, VoidCallback onTap) {
    return Expanded(
      child: GestureDetector(
        onTap: onTap,
        child: GlassCard(
          padding: const EdgeInsets.symmetric(vertical: 20),
          child: Column(children: [
            Container(
              width: 48, height: 48,
              decoration: BoxDecoration(color: color.withValues(alpha: 0.15), borderRadius: BorderRadius.circular(14)),
              child: Icon(icon, color: color, size: 24),
            ),
            const SizedBox(height: 8),
            Text(label, style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w600, color: AppColors.textPri)),
          ]),
        ),
      ),
    );
  }

  Widget _buildAccounts() {
    final accounts = (_data?['accounts'] as List?) ?? [];
    return Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
      const Padding(
        padding: EdgeInsets.symmetric(horizontal: 16),
        child: SectionHeader(title: 'Accounts', actionLabel: 'View All', horizontalPadding: 0),
      ),
      const SizedBox(height: 12),
      SizedBox(
        height: 150,
        child: ListView.separated(
          scrollDirection: Axis.horizontal,
          padding: const EdgeInsets.symmetric(horizontal: 16),
          itemCount: accounts.length,
          separatorBuilder: (_, __) => const SizedBox(width: 12),
          itemBuilder: (_, i) => _accountCard(accounts[i]),
        ),
      ),
    ]);
  }

  Widget _accountCard(Map<String, dynamic> acct) {
    final type = acct['type']?.toString() ?? 'savings';
    final typeColors = {
      'savings': AppColors.primary,
      'current': AppColors.success,
      'fixed': AppColors.purple,
      'loan': AppColors.secondary,
    };
    final color = typeColors[type.toLowerCase()] ?? AppColors.cyan;
    return GlassCard(
      width: 220,
      onTap: () {},
      child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
        Row(children: [
          Container(width: 36, height: 36, decoration: BoxDecoration(color: color.withValues(alpha: 0.15), borderRadius: BorderRadius.circular(10)),
            child: Icon(Icons.account_balance, color: color, size: 18)),
          const Spacer(),
          Container(padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3), decoration: BoxDecoration(color: color.withValues(alpha: 0.1), borderRadius: BorderRadius.circular(20)),
            child: Text(type.toUpperCase(), style: TextStyle(fontSize: 9, fontWeight: FontWeight.w800, color: color))),
        ]),
        const Spacer(),
        Text(acct['account_name']?.toString() ?? 'Account', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14, color: AppColors.textPri)),
        const SizedBox(height: 2),
        Text(acct['account_number']?.toString() ?? '', style: TextStyle(fontSize: 11, color: Colors.grey.shade400)),
        const SizedBox(height: 6),
        Text(_f(acct['balance'] ?? 0), style: TextStyle(fontSize: 18, fontWeight: FontWeight.w900, color: color, letterSpacing: -0.5)),
      ]),
    );
  }

  Widget _buildRecentTransactions() {
    final txns = (_data?['recent_transactions'] as List?) ?? [];
    return Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
      const Padding(
        padding: EdgeInsets.symmetric(horizontal: 16),
        child: SectionHeader(title: 'Recent Transactions', actionLabel: 'View All', horizontalPadding: 0),
      ),
      const SizedBox(height: 12),
      if (txns.isEmpty)
        const Padding(padding: EdgeInsets.symmetric(horizontal: 16), child: EmptyState(icon: Icons.receipt_long_outlined, title: 'No transactions'))
      else
        ...txns.map((t) => Padding(padding: const EdgeInsets.fromLTRB(16, 0, 16, 8), child: _txnTile(t))),
    ]);
  }

  Widget _txnTile(Map<String, dynamic> t) {
    final isCredit = t['type']?.toString().toLowerCase() == 'income' || t['type']?.toString().toLowerCase() == 'deposit';
    final txnColor = isCredit ? AppColors.success : AppColors.secondary;
    return GlassCard(
      padding: const EdgeInsets.all(14),
      onTap: () {},
      child: Row(children: [
        Container(width: 42, height: 42, decoration: BoxDecoration(color: txnColor.withValues(alpha: 0.12), borderRadius: BorderRadius.circular(12)),
          child: Icon(isCredit ? Icons.arrow_upward_rounded : Icons.arrow_downward_rounded, color: txnColor, size: 20)),
        const SizedBox(width: 12),
        Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
          Text(t['description']?.toString() ?? 'Transaction', style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13, color: AppColors.textPri)),
          const SizedBox(height: 2),
          Text(t['category']?.toString() ?? '', style: const TextStyle(fontSize: 11, color: AppColors.textSec)),
        ])),
        Column(crossAxisAlignment: CrossAxisAlignment.end, children: [
          Text('${isCredit ? '+' : '-'} ${_f(t['amount'] ?? 0)}', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 13, color: txnColor)),
          const SizedBox(height: 4),
          StatusBadge.fromStatus(t['status']?.toString() ?? 'completed'),
        ]),
      ]),
    );
  }
}
