import 'package:flutter/material.dart';
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

class MicrofinanceDashboardScreen extends StatefulWidget {
  const MicrofinanceDashboardScreen({super.key});
  @override State<MicrofinanceDashboardScreen> createState() => _MicrofinanceDashboardScreenState();
}

class _MicrofinanceDashboardScreenState extends State<MicrofinanceDashboardScreen> {
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
      final res = await ApiService.get('/dashboard/microfinance');
      setState(() { _data = res; _loading = false; });
    } on ApiException catch (e) { setState(() { _error = e.message; _loading = false; }); }
    catch (_) { setState(() { _error = 'Connection error'; _loading = false; }); }
  }

  String _f(dynamic v) => '$_currency ${_fmt.format((v is num ? v.toDouble() : double.tryParse(v?.toString() ?? '0') ?? 0))}';

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        title: const Text('Microfinance', style: TextStyle(fontWeight: FontWeight.w800, color: Color(0xFF0F172A), fontSize: 20)),
        backgroundColor: Colors.white, elevation: 0, centerTitle: true,
        actions: [IconButton(icon: const Icon(Icons.refresh_rounded, color: AppColors.primary), onPressed: _load)],
      ),
      body: _loading
          ? const ShimmerLoading(itemCount: 10)
          : _error != null
              ? Center(child: Column(mainAxisAlignment: MainAxisAlignment.center, children: [
                  const Icon(Icons.error_outline, size: 48, color: Colors.grey),
                  const SizedBox(height: 12), Text(_error!),
                  const SizedBox(height: 16), ElevatedButton(onPressed: _load, child: const Text('Retry')),
                ]))
              : RefreshIndicator(
                  onRefresh: _load,
                  child: SingleChildScrollView(
                    physics: const BouncingScrollPhysics(),
                    padding: const EdgeInsets.only(bottom: 40),
                    child: Column(children: [
                      const SizedBox(height: 8),
                      _buildStatCards(),
                      const SizedBox(height: 20),
                      _buildDonutChart(),
                      const SizedBox(height: 20),
                      _buildBarChart(),
                      const SizedBox(height: 20),
                      _buildQuickActions(),
                      const SizedBox(height: 20),
                      _buildRecentLoans(),
                      const SizedBox(height: 20),
                      _buildRecentRepayments(),
                    ]),
                  ),
                ),
    );
  }

  Widget _buildStatCards() {
    final s = _data?['stats'] ?? {};
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16),
      child: Column(children: [
        Row(children: [
          Expanded(child: StatCard(icon: Icons.credit_card_rounded, value: '${s['active_loans'] ?? 0}', label: 'Active Loans', color: AppColors.primary)),
          const SizedBox(width: 12),
          Expanded(child: StatCard(icon: Icons.payments_rounded, value: _f(s['total_disbursed'] ?? 0), label: 'Total Disbursed', color: AppColors.success)),
        ]),
        const SizedBox(height: 12),
        Row(children: [
          Expanded(child: StatCard(icon: Icons.account_balance_wallet_rounded, value: _f(s['total_repaid'] ?? 0), label: 'Total Repaid', color: AppColors.cyan)),
          const SizedBox(width: 12),
          Expanded(child: StatCard(icon: Icons.pending_actions_rounded, value: _f(s['outstanding'] ?? 0), label: 'Outstanding', color: AppColors.warning)),
        ]),
        const SizedBox(height: 12),
        Row(children: [
          Expanded(child: StatCard(icon: Icons.warning_amber_rounded, value: '${s['default_rate'] ?? 0}%', label: 'Default Rate', color: AppColors.secondary, subtitle: 'of total loans')),
          const SizedBox(width: 12),
          Expanded(child: StatCard(icon: Icons.people_rounded, value: '${s['total_clients'] ?? 0}', label: 'Clients', color: AppColors.purple)),
        ]),
      ]),
    );
  }

  Widget _buildDonutChart() {
    final dist = (_data?['loan_status_distribution'] as List?) ?? [];
    if (dist.isEmpty) return const SizedBox.shrink();
    return GlassCard(
      margin: const EdgeInsets.symmetric(horizontal: 16),
      child: Column(children: [
        const SectionHeader(title: 'Loan Status Distribution', horizontalPadding: 0),
        const SizedBox(height: 12),
        DonutChartWidget(
          items: dist.map<DonutChartItem>((d) => DonutChartItem(
            label: d['label']?.toString() ?? '',
            value: (d['value'] as num?)?.toDouble() ?? 0,
            color: _statusColor(d['label']?.toString() ?? ''),
          )).toList(),
          size: 140,
          strokeWidth: 24,
        ),
      ]),
    );
  }

  Color _statusColor(String s) {
    switch (s.toLowerCase()) {
      case 'active': return AppColors.primary;
      case 'closed': return AppColors.success;
      case 'default': return AppColors.danger;
      case 'pending': return AppColors.warning;
      default: return AppColors.cyan;
    }
  }

  Widget _buildBarChart() {
    final monthly = (_data?['monthly_data'] as List?) ?? [];
    if (monthly.isEmpty) return const SizedBox.shrink();
    return GlassCard(
      margin: const EdgeInsets.symmetric(horizontal: 16),
      child: Column(children: [
        const SectionHeader(title: 'Monthly Disbursements vs Repayments', horizontalPadding: 0),
        const SizedBox(height: 12),
        SizedBox(
          height: 200,
          child: Column(children: [
            Row(mainAxisAlignment: MainAxisAlignment.center, children: [
              _legendDot(AppColors.primary, 'Disbursed'),
              const SizedBox(width: 20),
              _legendDot(AppColors.success, 'Repaid'),
            ]),
            const SizedBox(height: 12),
            Expanded(
              child: Row(crossAxisAlignment: CrossAxisAlignment.end, children: monthly.map((m) {
                final disbursed = (m['disbursed'] as num?)?.toDouble() ?? 0;
                final repaid = (m['repaid'] as num?)?.toDouble() ?? 0;
                final maxVal = monthly.fold(0.0, (s, x) => {
                  final d = (x['disbursed'] as num?)?.toDouble() ?? 0;
                  final r = (x['repaid'] as num?)?.toDouble() ?? 0;
                  return d > s ? d : (r > s ? r : s);
                }()) as double;
                final scale = maxVal > 0 ? maxVal : 1.0;
                return Expanded(child: Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 2),
                  child: Column(mainAxisAlignment: MainAxisAlignment.end, children: [
                    Container(height: (disbursed / scale) * 140, decoration: BoxDecoration(
                      color: AppColors.primary.withValues(alpha: 0.8),
                      borderRadius: const BorderRadius.vertical(top: Radius.circular(4)),
                    )),
                    const SizedBox(height: 2),
                    Container(height: (repaid / scale) * 140, decoration: BoxDecoration(
                      color: AppColors.success.withValues(alpha: 0.8),
                      borderRadius: const BorderRadius.vertical(top: Radius.circular(4)),
                    )),
                    const SizedBox(height: 4),
                    Text(m['label']?.toString() ?? '', style: const TextStyle(fontSize: 9, color: AppColors.textSec)),
                  ]),
                ));
              }).toList()),
            ),
          ]),
        ),
      ]),
    );
  }

  Widget _legendDot(Color color, String label) {
    return Row(mainAxisSize: MainAxisSize.min, children: [
      Container(width: 10, height: 10, decoration: BoxDecoration(color: color, borderRadius: BorderRadius.circular(3))),
      const SizedBox(width: 6),
      Text(label, style: const TextStyle(fontSize: 11, color: AppColors.textSec)),
    ]);
  }

  Widget _buildQuickActions() {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16),
      child: Row(children: [
        _actionBtn(Icons.add_circle_outline_rounded, 'New Loan', AppColors.primary, () {}),
        const SizedBox(width: 12),
        _actionBtn(Icons.payments_rounded, 'Record\nRepayment', AppColors.success, () {}),
      ]),
    );
  }

  Widget _actionBtn(IconData icon, String label, Color color, VoidCallback onTap) {
    return Expanded(
      child: GlassCard(
        padding: const EdgeInsets.symmetric(vertical: 18),
        onTap: onTap,
        child: Column(children: [
          Container(width: 48, height: 48, decoration: BoxDecoration(color: color.withValues(alpha: 0.15), borderRadius: BorderRadius.circular(14)),
            child: Icon(icon, color: color, size: 24)),
          const SizedBox(height: 8),
          Text(label, textAlign: TextAlign.center, style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w600, color: AppColors.textPri)),
        ]),
      ),
    );
  }

  Widget _buildRecentLoans() {
    final loans = (_data?['recent_loans'] as List?) ?? [];
    return Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
      const Padding(padding: EdgeInsets.symmetric(horizontal: 16), child: SectionHeader(title: 'Recent Loans', actionLabel: 'View All', horizontalPadding: 0)),
      const SizedBox(height: 12),
      if (loans.isEmpty)
        const Padding(padding: EdgeInsets.symmetric(horizontal: 16), child: EmptyState(icon: Icons.credit_card_off_outlined, title: 'No loans'))
      else
        ...loans.map((l) => Padding(padding: const EdgeInsets.fromLTRB(16, 0, 16, 8), child: _loanTile(l))),
    ]);
  }

  Widget _loanTile(Map<String, dynamic> l) {
    return GlassCard(
      padding: const EdgeInsets.all(14),
      onTap: () {},
      child: Row(children: [
        Container(width: 44, height: 44, decoration: BoxDecoration(color: AppColors.primary.withValues(alpha: 0.1), borderRadius: BorderRadius.circular(12)),
          child: Text((l['client_name']?.toString() ?? '?')[0].toUpperCase(), style: const TextStyle(color: AppColors.primary, fontWeight: FontWeight.w800, fontSize: 18))),
        const SizedBox(width: 12),
        Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
          Text(l['client_name']?.toString() ?? 'Client', style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13, color: AppColors.textPri)),
          const SizedBox(height: 2),
          Text(l['product_name']?.toString() ?? '', style: const TextStyle(fontSize: 11, color: AppColors.textSec)),
          const SizedBox(height: 2),
          Text('Balance: ${_f(l['balance'] ?? 0)}', style: const TextStyle(fontSize: 11, color: AppColors.textLight)),
        ])),
        Column(crossAxisAlignment: CrossAxisAlignment.end, children: [
          Text(_f(l['amount'] ?? 0), style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 14, color: AppColors.textPri)),
          const SizedBox(height: 4),
          StatusBadge.fromStatus(l['status']?.toString() ?? 'active'),
        ]),
      ]),
    );
  }

  Widget _buildRecentRepayments() {
    final reps = (_data?['recent_repayments'] as List?) ?? [];
    return Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
      const Padding(padding: EdgeInsets.symmetric(horizontal: 16), child: SectionHeader(title: 'Recent Repayments', actionLabel: 'View All', horizontalPadding: 0)),
      const SizedBox(height: 12),
      if (reps.isEmpty)
        const Padding(padding: EdgeInsets.symmetric(horizontal: 16), child: EmptyState(icon: Icons.receipt_long_outlined, title: 'No repayments'))
      else
        ...reps.map((r) => Padding(padding: const EdgeInsets.fromLTRB(16, 0, 16, 8), child: _repaymentTile(r))),
    ]);
  }

  Widget _repaymentTile(Map<String, dynamic> r) {
    return GlassCard(
      padding: const EdgeInsets.all(12),
      child: Row(children: [
        Container(width: 36, height: 36, decoration: BoxDecoration(color: AppColors.success.withValues(alpha: 0.12), borderRadius: BorderRadius.circular(10)),
          child: const Icon(Icons.check_circle_outline_rounded, color: AppColors.success, size: 18)),
        const SizedBox(width: 10),
        Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
          Text(r['client_name']?.toString() ?? '', style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13, color: AppColors.textPri)),
          Text('${r['method']?.toString() ?? ''} \u2022 ${r['date']?.toString() ?? ''}', style: const TextStyle(fontSize: 11, color: AppColors.textSec)),
        ])),
        Text(_f(r['amount'] ?? 0), style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 14, color: AppColors.success)),
      ]),
    );
  }
}
