import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../shared/theme/app_colors.dart';
import '../../shared/widgets/app_card.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/stat_card.dart';
import '../../widgets/chart_widgets.dart';
import '../../core/api_service.dart';

class ProfitLossScreen extends StatefulWidget {
  const ProfitLossScreen({super.key});
  @override State<ProfitLossScreen> createState() => _ProfitLossScreenState();
}

class _ProfitLossScreenState extends State<ProfitLossScreen> {
  late TextEditingController _from, _to;
  Map<String, dynamic>? _data;
  bool _loading = false;
  final _fmt = NumberFormat('#,##0.00');

  @override
  void initState() {
    super.initState();
    _from = TextEditingController(text: DateFormat('yyyy-MM-dd').format(DateTime.now().subtract(const Duration(days: 30))));
    _to = TextEditingController(text: DateFormat('yyyy-MM-dd').format(DateTime.now()));
    _load();
  }

  @override void dispose() { _from.dispose(); _to.dispose(); super.dispose(); }

  Future<void> _load() async {
    setState(() => _loading = true);
    try {
      final d = await ApiService.get('/reports/profit-loss?from=${_from.text}&to=${_to.text}');
      if (mounted) setState(() { _data = d; _loading = false; });
    } catch (_) { if (mounted) setState(() => _loading = false); }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(title: const Text('Profit & Loss')),
      body: Column(children: [
        _dateFilter(),
        Expanded(
          child: _loading
              ? const Center(child: CircularProgressIndicator())
              : _data == null
                  ? const Center(child: Text('No data'))
                  : SingleChildScrollView(
                      padding: const EdgeInsets.all(16),
                      child: Column(children: [
                        Row(children: [
                          Expanded(child: StatCard(icon: Icons.trending_up, value: 'TSh ${_fmt.format((_data!['total_revenue'] ?? 0).toDouble())}', label: 'Total Revenue', color: AppColors.success)),
                          const SizedBox(width: 10),
                          Expanded(child: StatCard(icon: Icons.money_off, value: 'TSh ${_fmt.format((_data!['total_expenses'] ?? 0).toDouble())}', label: 'Total Expenses', color: AppColors.danger)),
                        ]),
                        const SizedBox(height: 10),
                        Row(children: [
                          Expanded(child: StatCard(icon: Icons.account_balance, value: 'TSh ${_fmt.format((_data!['gross_profit'] ?? 0).toDouble())}', label: 'Gross Profit', color: AppColors.warning)),
                          const SizedBox(width: 10),
                          Expanded(child: StatCard(icon: Icons.savings, value: 'TSh ${_fmt.format((_data!['net_profit'] ?? 0).toDouble())}', label: 'Net Profit', color: AppColors.primary)),
                        ]),
                        const SizedBox(height: 20),
                        GlassCard(child: Padding(padding: const EdgeInsets.all(16), child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                          const Text('Revenue Breakdown', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 16)),
                          const SizedBox(height: 12),
                          ...((_data!['revenue_breakdown'] as List?) ?? []).map((item) => Padding(
                            padding: const EdgeInsets.symmetric(vertical: 4),
                            child: Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
                              Text(item['category'] ?? '', style: const TextStyle(fontWeight: FontWeight.w500)),
                              Text('TSh ${_fmt.format((item['amount'] ?? 0).toDouble())}', style: const TextStyle(fontWeight: FontWeight.w700, color: AppColors.success)),
                            ]),
                          )),
                        ]))),
                        const SizedBox(height: 12),
                        GlassCard(child: Padding(padding: const EdgeInsets.all(16), child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                          const Text('Expense Breakdown', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 16)),
                          const SizedBox(height: 12),
                          ...((_data!['expense_breakdown'] as List?) ?? []).map((item) => Padding(
                            padding: const EdgeInsets.symmetric(vertical: 4),
                            child: Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
                              Text(item['category'] ?? '', style: const TextStyle(fontWeight: FontWeight.w500)),
                              Text('TSh ${_fmt.format((item['amount'] ?? 0).toDouble())}', style: const TextStyle(fontWeight: FontWeight.w700, color: AppColors.danger)),
                            ]),
                          )),
                        ]))),
                        const SizedBox(height: 12),
                        GlassCard(child: Padding(padding: const EdgeInsets.all(16), child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                          const Text('Profit Trend', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 16)),
                          const SizedBox(height: 12),
                          MiniLineChart(data: (_data!['profit_trend'] as List?)?.map((e) => (e['profit'] ?? 0).toDouble()).toList() ?? []),
                        ]))),
                        const SizedBox(height: 20),
                        SizedBox(
                          width: double.infinity,
                          child: ElevatedButton.icon(
                            onPressed: () => ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Export feature coming soon'))),
                            icon: const Icon(Icons.download),
                            label: const Text('Export Report'),
                          ),
                        ),
                        const SizedBox(height: 30),
                      ]),
                    ),
        ),
      ]),
    );
  }

  Widget _dateFilter() {
    return GlassCard(
      child: Padding(
        padding: const EdgeInsets.fromLTRB(16, 12, 16, 12),
        child: Row(children: [
          Expanded(child: TextFormField(controller: _from, readOnly: true, decoration: const InputDecoration(labelText: 'From', isDense: true, suffixIcon: Icon(Icons.calendar_today, size: 16)),
            onTap: () async { final d = await showDatePicker(context: context, initialDate: DateTime.now().subtract(const Duration(days: 30)), firstDate: DateTime(2020), lastDate: DateTime(2030)); if (d != null) _from.text = DateFormat('yyyy-MM-dd').format(d); })),
          const SizedBox(width: 10),
          Expanded(child: TextFormField(controller: _to, readOnly: true, decoration: const InputDecoration(labelText: 'To', isDense: true, suffixIcon: Icon(Icons.calendar_today, size: 16)),
            onTap: () async { final d = await showDatePicker(context: context, initialDate: DateTime.now(), firstDate: DateTime(2020), lastDate: DateTime(2030)); if (d != null) _to.text = DateFormat('yyyy-MM-dd').format(d); })),
          const SizedBox(width: 10),
          ElevatedButton(onPressed: _load, style: ElevatedButton.styleFrom(padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12)), child: const Text('Filter')),
        ]),
      ),
    );
  }
}
