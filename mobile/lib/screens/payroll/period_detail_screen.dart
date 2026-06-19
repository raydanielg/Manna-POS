import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../core/api_service.dart';
import '../../shared/theme/app_theme.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/shimmer_loading.dart';
import '../../widgets/status_badge.dart';
import '../../widgets/section_header.dart';
import '../../widgets/toast_helper.dart';
import '../../widgets/confirm_dialog.dart';

class PeriodDetailScreen extends StatefulWidget {
  final String periodId;
  final Map<String, dynamic>? period;
  const PeriodDetailScreen({super.key, required this.periodId, this.period});
  @override State<PeriodDetailScreen> createState() => _PeriodDetailScreenState();
}

class _PeriodDetailScreenState extends State<PeriodDetailScreen> {
  bool _loading = true;
  String? _error;
  Map<String, dynamic>? _period;
  List<dynamic> _entries = [];
  final _fmt = NumberFormat('#,##0.00');
  final _currency = 'TSh';

  @override
  void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    if (widget.period != null && widget.period!.isNotEmpty) {
      setState(() { _period = widget.period; _loading = false; });
      _loadEntries();
      return;
    }
    setState(() { _loading = true; _error = null; });
    try {
      final res = await ApiService.get('/payroll/periods/${widget.periodId}');
      setState(() { _period = res; _loading = false; });
      _loadEntries();
    } on ApiException catch (e) { setState(() { _error = e.message; _loading = false; }); }
    catch (_) { setState(() { _error = 'Connection error'; _loading = false; }); }
  }

  Future<void> _loadEntries() async {
    try {
      final res = await ApiService.get('/payroll/periods/${widget.periodId}/entries');
      setState(() { _entries = res is List ? res : (res['data'] ?? []); });
    } catch (_) {}
  }

  String _f(dynamic v) => '$_currency ${_fmt.format((v is num ? v.toDouble() : double.tryParse(v?.toString() ?? '0') ?? 0))}';

  double get _totalGross => _entries.fold(0.0, (s, e) => s + ((e['gross_pay'] as num?)?.toDouble() ?? 0));
  double get _totalDeductions => _entries.fold(0.0, (s, e) => s + ((e['deductions'] as num?)?.toDouble() ?? 0));
  double get _totalNet => _entries.fold(0.0, (s, e) => s + ((e['net_pay'] as num?)?.toDouble() ?? 0));

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      body: _loading
          ? const ShimmerLoading(itemCount: 6)
          : _error != null
              ? Center(child: Column(mainAxisAlignment: MainAxisAlignment.center, children: [
                  const Icon(Icons.error_outline, size: 48, color: Colors.grey),
                  const SizedBox(height: 12), Text(_error!),
                  const SizedBox(height: 16), ElevatedButton(onPressed: _load, child: const Text('Retry')),
                ]))
              : RefreshIndicator(
                  onRefresh: _load,
                  child: CustomScrollView(slivers: [
                    SliverToBoxAdapter(child: _buildHeader()),
                    SliverToBoxAdapter(child: _buildActions()),
                    if (_entries.isNotEmpty)
                      SliverToBoxAdapter(child: _buildTotalsFooter()),
                    SliverPadding(
                      padding: const EdgeInsets.fromLTRB(16, 8, 16, 100),
                      sliver: SliverList(
                        delegate: SliverChildBuilderDelegate((_, i) => Padding(
                          padding: const EdgeInsets.only(bottom: 10),
                          child: _entryCard(_entries[i]),
                        ), childCount: _entries.length),
                      ),
                    ),
                  ]),
                ),
    );
  }

  Widget _buildHeader() {
    final p = _period!;
    final statusColors = {
      'open': AppColors.success, 'closed': AppColors.textSec, 'processing': AppColors.warning,
    };
    return Container(
      padding: const EdgeInsets.fromLTRB(20, 48, 20, 24),
      decoration: BoxDecoration(
        gradient: const LinearGradient(colors: [Color(0xFF1A3A5C), Color(0xFF0F1B2D)], begin: Alignment.topLeft, end: Alignment.bottomRight),
        borderRadius: const BorderRadius.vertical(bottom: Radius.circular(28)),
      ),
      child: SafeArea(bottom: false, child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
        Row(children: [
          GestureDetector(onTap: () => Navigator.pop(context), child: Container(padding: const EdgeInsets.all(8),
            decoration: BoxDecoration(color: Colors.white.withValues(alpha: 0.15), borderRadius: BorderRadius.circular(12)),
            child: const Icon(Icons.arrow_back_rounded, color: Colors.white, size: 22))),
          const Spacer(),
          StatusBadge(label: p['status']?.toString()?.toUpperCase() ?? 'OPEN',
            color: statusColors[p['status']?.toString()?.toLowerCase()] ?? AppColors.textSec,
            bgColor: (statusColors[p['status']?.toString()?.toLowerCase()] ?? AppColors.textSec).withValues(alpha: 0.2)),
        ]),
        const SizedBox(height: 20),
        Text(p['name']?.toString() ?? 'Period', style: const TextStyle(color: Colors.white, fontSize: 22, fontWeight: FontWeight.w800)),
        const SizedBox(height: 4),
        Text('${p['start_date']?.toString() ?? ''} \u2192 ${p['end_date']?.toString() ?? ''}', style: TextStyle(color: Colors.white.withValues(alpha: 0.7), fontSize: 13)),
        const SizedBox(height: 12),
        Row(children: [
          _headerStat('Employees', '${_entries.length}'),
          const SizedBox(width: 24),
          _headerStat('Gross Pay', _f(_totalGross)),
          const SizedBox(width: 24),
          _headerStat('Net Pay', _f(_totalNet)),
        ]),
      ])),
    );
  }

  Widget _headerStat(String label, String value) {
    return Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
      Text(value, style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w800, fontSize: 15)),
      Text(label, style: TextStyle(color: Colors.white.withValues(alpha: 0.6), fontSize: 11)),
    ]);
  }

  Widget _buildActions() {
    final status = _period?['status']?.toString().toLowerCase() ?? '';
    return Padding(
      padding: const EdgeInsets.fromLTRB(16, 16, 16, 0),
      child: Row(children: [
        if (status == 'open' || status == 'processing')
          Expanded(child: _actionChip(Icons.payments_rounded, 'Process Payments', AppColors.primary, () {})),
        if (status == 'open')
          Padding(
            padding: const EdgeInsets.only(left: 10),
            child: _actionChip(Icons.lock_outline_rounded, 'Close Period', AppColors.warning, () async {
              final ok = await ConfirmDialog.show(context, title: 'Close Period',
                message: 'Close this payroll period?', confirmLabel: 'Close', icon: Icons.lock_outline_rounded, confirmColor: AppColors.warning);
              if (ok) ToastHelper.success(context, 'Period closed');
            }),
          ),
      ]),
    );
  }

  Widget _actionChip(IconData icon, String label, Color color, VoidCallback onTap) {
    return Expanded(
      child: GestureDetector(
        onTap: onTap,
        child: Container(
          padding: const EdgeInsets.symmetric(vertical: 12),
          decoration: BoxDecoration(color: color.withValues(alpha: 0.1), borderRadius: BorderRadius.circular(12)),
          child: Row(mainAxisAlignment: MainAxisAlignment.center, children: [
            Icon(icon, size: 16, color: color),
            const SizedBox(width: 6),
            Text(label, style: TextStyle(fontSize: 12, fontWeight: FontWeight.w600, color: color)),
          ]),
        ),
      ),
    );
  }

  Widget _buildTotalsFooter() {
    return Padding(
      padding: const EdgeInsets.fromLTRB(16, 16, 16, 8),
      child: GlassCard(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
        child: Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
          _totalCol('Gross Pay', _f(_totalGross), AppColors.textPri),
          _totalCol('Deductions', _f(_totalDeductions), AppColors.secondary),
          _totalCol('Net Pay', _f(_totalNet), AppColors.success),
        ]),
      ),
    );
  }

  Widget _totalCol(String label, String value, Color color) {
    return Column(children: [
      Text(value, style: TextStyle(fontWeight: FontWeight.w800, fontSize: 14, color: color)),
      Text(label, style: const TextStyle(fontSize: 10, color: AppColors.textSec)),
    ]);
  }

  Widget _entryCard(Map<String, dynamic> e) {
    return GlassCard(
      padding: const EdgeInsets.all(14),
      onTap: () {},
      child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
        Row(children: [
          Container(width: 40, height: 40, decoration: BoxDecoration(color: AppColors.primary.withValues(alpha: 0.1), borderRadius: BorderRadius.circular(10)),
            child: Center(child: Text((e['employee_name']?.toString() ?? '?')[0].toUpperCase(), style: const TextStyle(color: AppColors.primary, fontWeight: FontWeight.w800, fontSize: 16)))),
          const SizedBox(width: 12),
          Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            Text(e['employee_name']?.toString() ?? 'Employee', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14, color: AppColors.textPri)),
            const SizedBox(height: 2),
            Text(e['position']?.toString() ?? '', style: const TextStyle(fontSize: 11, color: AppColors.textSec)),
          ])),
          StatusBadge.fromStatus(e['status']?.toString() ?? 'draft'),
        ]),
        const SizedBox(height: 12),
        Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
          _entryStat('Gross', _f(e['gross_pay'] ?? 0)),
          _entryStat('Deductions', _f(e['deductions'] ?? 0)),
          _entryStat('Net', _f(e['net_pay'] ?? 0), color: AppColors.success),
        ]),
      ]),
    );
  }

  Widget _entryStat(String label, String value, {Color? color}) {
    return Column(children: [
      Text(value, style: TextStyle(fontWeight: FontWeight.w800, fontSize: 13, color: color ?? AppColors.textPri)),
      Text(label, style: const TextStyle(fontSize: 10, color: AppColors.textSec)),
    ]);
  }
}
