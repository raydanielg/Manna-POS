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

class PayrollDashboardScreen extends StatefulWidget {
  const PayrollDashboardScreen({super.key});
  @override State<PayrollDashboardScreen> createState() => _PayrollDashboardScreenState();
}

class _PayrollDashboardScreenState extends State<PayrollDashboardScreen> {
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
      final res = await ApiService.get('/dashboard/payroll');
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
        title: const Text('Payroll', style: TextStyle(fontWeight: FontWeight.w800, color: Color(0xFF0F172A), fontSize: 20)),
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
                      _buildChart(),
                      const SizedBox(height: 20),
                      _buildCurrentPeriod(),
                      const SizedBox(height: 20),
                      _buildQuickActions(),
                      const SizedBox(height: 20),
                      _buildRecentEntries(),
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
          Expanded(child: StatCard(icon: Icons.account_balance_wallet_rounded, value: _f(s['total_payroll'] ?? 0), label: 'Total Payroll (Month)', color: AppColors.primary)),
          const SizedBox(width: 12),
          Expanded(child: StatCard(icon: Icons.people_rounded, value: '${s['active_employees'] ?? 0}', label: 'Active Employees', color: AppColors.success)),
        ]),
        const SizedBox(height: 12),
        Row(children: [
          Expanded(child: StatCard(icon: Icons.pending_actions_rounded, value: _f(s['pending_payments'] ?? 0), label: 'Pending Payments', color: AppColors.warning)),
          const SizedBox(width: 12),
          Expanded(child: StatCard(icon: Icons.money_off_rounded, value: _f(s['total_deductions'] ?? 0), label: 'Total Deductions', color: AppColors.secondary)),
        ]),
      ]),
    );
  }

  Widget _buildChart() {
    final chartData = (_data?['monthly_trend'] as List?) ?? [];
    if (chartData.isEmpty) return const SizedBox.shrink();
    return GlassCard(
      margin: const EdgeInsets.symmetric(horizontal: 16),
      child: Column(children: [
        const SectionHeader(title: 'Monthly Payroll Trend', horizontalPadding: 0),
        const SizedBox(height: 16),
        SizedBox(
          height: 160,
          child: BarChartWidget(
            items: chartData.map((d) => BarChartItem(
              label: d['label']?.toString() ?? '',
              value: (d['value'] as num?)?.toDouble() ?? 0,
              color: AppColors.primary,
            )).toList(),
          ),
        ),
      ]),
    );
  }

  Widget _buildCurrentPeriod() {
    final period = _data?['current_period'];
    if (period == null) return const SizedBox.shrink();
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16),
      child: GlassCard(
        child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
          Row(children: [
            Container(width: 40, height: 40, decoration: BoxDecoration(color: AppColors.primary.withValues(alpha: 0.1), borderRadius: BorderRadius.circular(10)),
              child: const Icon(Icons.calendar_month_rounded, color: AppColors.primary, size: 20)),
            const SizedBox(width: 12),
            Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
              Text('Current Period', style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13, color: AppColors.textPri)),
              Text('${period['name']?.toString() ?? ''}', style: const TextStyle(fontSize: 12, color: AppColors.textSec)),
            ])),
            StatusBadge.fromStatus(period['status']?.toString() ?? 'open'),
          ]),
          const SizedBox(height: 14),
          Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
            _periodStat('Employees', '${period['employee_count'] ?? 0}'),
            _periodStat('Gross Pay', _f(period['gross_pay'] ?? 0)),
            _periodStat('Deductions', _f(period['deductions'] ?? 0)),
            _periodStat('Net Pay', _f(period['net_pay'] ?? 0)),
          ]),
        ]),
      ),
    );
  }

  Widget _periodStat(String label, String value) {
    return Column(children: [
      Text(value, style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 13, color: AppColors.textPri)),
      Text(label, style: const TextStyle(fontSize: 9, color: AppColors.textSec)),
    ]);
  }

  Widget _buildQuickActions() {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16),
      child: Row(children: [
        _actionBtn(Icons.add_circle_outline_rounded, 'New Period', AppColors.primary, () {}),
        const SizedBox(width: 12),
        _actionBtn(Icons.engineering_rounded, 'Process\nPayroll', AppColors.success, () {}),
        const SizedBox(width: 12),
        _actionBtn(Icons.play_circle_outline_rounded, 'Run\nPayroll', AppColors.warning, () {}),
      ]),
    );
  }

  Widget _actionBtn(IconData icon, String label, Color color, VoidCallback onTap) {
    return Expanded(
      child: GlassCard(
        padding: const EdgeInsets.symmetric(vertical: 18),
        onTap: onTap,
        child: Column(children: [
          Container(width: 44, height: 44, decoration: BoxDecoration(color: color.withValues(alpha: 0.15), borderRadius: BorderRadius.circular(14)),
            child: Icon(icon, color: color, size: 22)),
          const SizedBox(height: 8),
          Text(label, textAlign: TextAlign.center, style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w600, color: AppColors.textPri)),
        ]),
      ),
    );
  }

  Widget _buildRecentEntries() {
    final entries = (_data?['recent_entries'] as List?) ?? [];
    return Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
      const Padding(padding: EdgeInsets.symmetric(horizontal: 16), child: SectionHeader(title: 'Recent Payroll Entries', actionLabel: 'View All', horizontalPadding: 0)),
      const SizedBox(height: 12),
      if (entries.isEmpty)
        const Padding(padding: EdgeInsets.symmetric(horizontal: 16), child: EmptyState(icon: Icons.receipt_long_outlined, title: 'No entries'))
      else
        ...entries.map((e) => Padding(padding: const EdgeInsets.fromLTRB(16, 0, 16, 8), child: _entryTile(e))),
    ]);
  }

  Widget _entryTile(Map<String, dynamic> e) {
    return GlassCard(
      padding: const EdgeInsets.all(14),
      child: Row(children: [
        Container(width: 42, height: 42, decoration: BoxDecoration(color: AppColors.primary.withValues(alpha: 0.1), borderRadius: BorderRadius.circular(12)),
          child: Center(child: Text((e['employee_name']?.toString() ?? '?')[0].toUpperCase(), style: const TextStyle(color: AppColors.primary, fontWeight: FontWeight.w800, fontSize: 18)))),
        const SizedBox(width: 12),
        Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
          Text(e['employee_name']?.toString() ?? 'Employee', style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13, color: AppColors.textPri)),
          const SizedBox(height: 2),
          Text(e['period_name']?.toString() ?? '', style: const TextStyle(fontSize: 11, color: AppColors.textSec)),
        ])),
        Column(crossAxisAlignment: CrossAxisAlignment.end, children: [
          Text(_f(e['net_pay'] ?? 0), style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 14, color: AppColors.textPri)),
          const SizedBox(height: 4),
          StatusBadge.fromStatus(e['status']?.toString() ?? 'draft'),
        ]),
      ]),
    );
  }
}
