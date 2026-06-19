import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../core/api_service.dart';
import '../../shared/theme/app_theme.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/shimmer_loading.dart';
import '../../widgets/empty_state.dart';
import '../../widgets/status_badge.dart';
import '../../widgets/toast_helper.dart';
import '../../widgets/app_bottom_sheet.dart';
import 'period_detail_screen.dart';

class PeriodsScreen extends StatefulWidget {
  const PeriodsScreen({super.key});
  @override State<PeriodsScreen> createState() => _PeriodsScreenState();
}

class _PeriodsScreenState extends State<PeriodsScreen> {
  bool _loading = true;
  String? _error;
  List<dynamic> _periods = [];
  final _fmt = NumberFormat('#,##0.00');
  final _currency = 'TSh';

  @override
  void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    setState(() { _loading = true; _error = null; });
    try {
      final res = await ApiService.get('/payroll/periods');
      setState(() { _periods = res is List ? res : (res['data'] ?? []); _loading = false; });
    } on ApiException catch (e) { setState(() { _error = e.message; _loading = false; }); }
    catch (_) { setState(() { _error = 'Connection error'; _loading = false; }); }
  }

  String _f(dynamic v) => '$_currency ${_fmt.format((v is num ? v.toDouble() : double.tryParse(v?.toString() ?? '0') ?? 0))}';

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        title: const Text('Payroll Periods', style: TextStyle(fontWeight: FontWeight.w800, color: Color(0xFF0F172A), fontSize: 20)),
        backgroundColor: Colors.white, elevation: 0, centerTitle: true,
        actions: [IconButton(icon: const Icon(Icons.refresh_rounded, color: AppColors.primary), onPressed: _load)],
      ),
      body: _loading
          ? const ShimmerLoading(itemCount: 5)
          : _error != null
              ? Center(child: Column(mainAxisAlignment: MainAxisAlignment.center, children: [
                  const Icon(Icons.error_outline, size: 48, color: Colors.grey),
                  const SizedBox(height: 12), Text(_error!),
                  const SizedBox(height: 16), ElevatedButton(onPressed: _load, child: const Text('Retry')),
                ]))
              : _periods.isEmpty
                  ? const EmptyState(icon: Icons.calendar_month_outlined, title: 'No Periods', subtitle: 'Create your first payroll period')
                  : RefreshIndicator(
                      onRefresh: _load,
                      child: ListView.builder(
                        padding: const EdgeInsets.fromLTRB(16, 12, 16, 100),
                        itemCount: _periods.length,
                        itemBuilder: (_, i) => _periodCard(_periods[i]),
                      ),
                    ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () => _showForm(),
        icon: const Icon(Icons.add),
        label: const Text('New Period'),
        backgroundColor: AppColors.primary, foregroundColor: Colors.white,
      ),
    );
  }

  Widget _periodCard(Map<String, dynamic> p) {
    final statusColors = {
      'open': AppColors.success,
      'closed': AppColors.textSec,
      'processing': AppColors.warning,
    };
    return Padding(
      padding: const EdgeInsets.only(bottom: 12),
      child: GlassCard(
        onTap: () {
          Navigator.push(context, MaterialPageRoute(builder: (_) => PeriodDetailScreen(periodId: p['id']?.toString() ?? '', period: p)));
        },
        child: Row(children: [
          Container(width: 48, height: 48, decoration: BoxDecoration(
            gradient: LinearGradient(colors: [AppColors.primary, AppColors.primaryDark], begin: Alignment.topLeft, end: Alignment.bottomRight),
            borderRadius: BorderRadius.circular(12)),
            child: const Icon(Icons.calendar_month_rounded, color: Colors.white, size: 22)),
          const SizedBox(width: 14),
          Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            Text(p['name']?.toString() ?? 'Period', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 15, color: AppColors.textPri)),
            const SizedBox(height: 2),
            Text('${p['start_date']?.toString() ?? ''} \u2192 ${p['end_date']?.toString() ?? ''}', style: const TextStyle(fontSize: 12, color: AppColors.textSec)),
            const SizedBox(height: 4),
            Row(children: [
              StatusBadge(label: p['status']?.toString()?.toUpperCase() ?? 'OPEN',
                color: statusColors[p['status']?.toString()?.toLowerCase()] ?? AppColors.textSec,
                bgColor: (statusColors[p['status']?.toString()?.toLowerCase()] ?? AppColors.textSec).withValues(alpha: 0.1)),
              const SizedBox(width: 8),
              Text('${p['employee_count'] ?? 0} employees', style: const TextStyle(fontSize: 11, color: AppColors.textLight)),
            ]),
          ])),
          Column(crossAxisAlignment: CrossAxisAlignment.end, children: [
            Text(_f(p['total_amount'] ?? 0), style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 14, color: AppColors.textPri)),
            const SizedBox(height: 4),
            const Icon(Icons.chevron_right, size: 18, color: Color(0xFFBBBBBB)),
          ]),
        ]),
      ),
    );
  }

  void _showForm() {
    AppBottomSheet.show(context, title: 'New Payroll Period', child: _PeriodForm(
      onSaved: () { _load(); Navigator.pop(context); },
    ));
  }
}

class _PeriodForm extends StatefulWidget {
  final VoidCallback onSaved;
  const _PeriodForm({required this.onSaved});
  @override State<_PeriodForm> createState() => _PeriodFormState();
}

class _PeriodFormState extends State<_PeriodForm> {
  final _form = GlobalKey<FormState>();
  final _nameCtrl = TextEditingController();
  DateTime _startDate = DateTime.now();
  DateTime _endDate = DateTime.now().add(const Duration(days: 30));
  bool _saving = false;
  String? _err;

  @override void dispose() { _nameCtrl.dispose(); super.dispose(); }

  Future<void> _save() async {
    if (!_form.currentState!.validate()) return;
    setState(() { _saving = true; _err = null; });
    try {
      await ApiService.post('/payroll/periods', {
        'name': _nameCtrl.text.trim(),
        'start_date': DateFormat('yyyy-MM-dd').format(_startDate),
        'end_date': DateFormat('yyyy-MM-dd').format(_endDate),
      });
      widget.onSaved();
    } on ApiException catch (e) { setState(() { _err = e.message; _saving = false; }); }
    catch (_) { setState(() { _err = 'Save failed'; _saving = false; }); }
  }

  @override
  Widget build(BuildContext context) => Form(key: _form, child: Column(crossAxisAlignment: CrossAxisAlignment.stretch, children: [
    if (_err != null) Container(padding: const EdgeInsets.all(12), margin: const EdgeInsets.only(bottom: 12),
      decoration: BoxDecoration(color: AppColors.dangerLt, borderRadius: BorderRadius.circular(10)),
      child: Text(_err!, style: const TextStyle(color: AppColors.danger))),
    TextFormField(controller: _nameCtrl, decoration: const InputDecoration(labelText: 'Period Name *'), validator: (v) => v!.isNotEmpty ? null : 'Required'),
    const SizedBox(height: 12),
    GestureDetector(
      onTap: () async {
        final d = await showDatePicker(context: context, initialDate: _startDate, firstDate: DateTime(2020), lastDate: DateTime(2030));
        if (d != null) setState(() => _startDate = d);
      },
      child: AbsorbPointer(
        child: TextFormField(decoration: InputDecoration(labelText: 'Start Date', hintText: DateFormat('dd MMM yyyy').format(_startDate), suffixIcon: const Icon(Icons.calendar_today, size: 18))),
      ),
    ),
    const SizedBox(height: 12),
    GestureDetector(
      onTap: () async {
        final d = await showDatePicker(context: context, initialDate: _endDate, firstDate: DateTime(2020), lastDate: DateTime(2030));
        if (d != null) setState(() => _endDate = d);
      },
      child: AbsorbPointer(
        child: TextFormField(decoration: InputDecoration(labelText: 'End Date', hintText: DateFormat('dd MMM yyyy').format(_endDate), suffixIcon: const Icon(Icons.calendar_today, size: 18))),
      ),
    ),
    const SizedBox(height: 24),
    SizedBox(height: 52, child: ElevatedButton(onPressed: _saving ? null : _save,
      child: _saving ? const SizedBox(width: 22, height: 22, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5))
          : const Text('Create Period', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700)))),
  ]));
}
