import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../core/api_service.dart';
import '../../shared/theme/app_theme.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/toast_helper.dart';

class AddEntryScreen extends StatefulWidget {
  final String? periodId;
  const AddEntryScreen({super.key, this.periodId});
  @override State<AddEntryScreen> createState() => _AddEntryScreenState();
}

class _AddEntryScreenState extends State<AddEntryScreen> {
  final _form = GlobalKey<FormState>();
  final _basicCtrl = TextEditingController();
  final _housingCtrl = TextEditingController();
  final _transportCtrl = TextEditingController();
  final _medicalCtrl = TextEditingController();
  final _otherAllowanceCtrl = TextEditingController();
  final _overtimeHoursCtrl = TextEditingController();
  final _overtimeRateCtrl = TextEditingController();
  final _bonusCtrl = TextEditingController();

  String? _selectedEmployee;
  String? _selectedPeriod;
  List<dynamic> _employees = [];
  List<dynamic> _periods = [];
  List<dynamic> _deductionTypes = [];
  Map<String, TextEditingController> _deductionCtrls = {};
  bool _saving = false;
  bool _loadingEmployees = true;
  bool _loadingPeriods = true;
  bool _loadingDeductions = true;
  String? _err;

  @override
  void initState() {
    super.initState();
    _selectedPeriod = widget.periodId;
    _loadData();
  }

  @override
  void dispose() {
    for (final c in [_basicCtrl, _housingCtrl, _transportCtrl, _medicalCtrl, _otherAllowanceCtrl, _overtimeHoursCtrl, _overtimeRateCtrl, _bonusCtrl]) c.dispose();
    for (final c in _deductionCtrls.values) c.dispose();
    super.dispose();
  }

  Future<void> _loadData() async {
    try {
      final results = await Future.wait([
        ApiService.get('/payroll/employees'),
        ApiService.get('/payroll/periods'),
        ApiService.get('/payroll/deductions'),
      ]);
      setState(() {
        _employees = results[0] is List ? results[0] : (results[0]['data'] ?? []);
        _periods = results[1] is List ? results[1] : (results[1]['data'] ?? []);
        _deductionTypes = results[2] is List ? results[2] : (results[2]['data'] ?? []);
        _loadingEmployees = false;
        _loadingPeriods = false;
        _loadingDeductions = false;
        for (final d in _deductionTypes) {
          _deductionCtrls[d['id'].toString()] = TextEditingController();
        }
      });
    } catch (_) {
      setState(() { _loadingEmployees = false; _loadingPeriods = false; _loadingDeductions = false; });
    }
  }

  Future<void> _save() async {
    if (!_form.currentState!.validate()) return;
    if (_selectedEmployee == null) { ToastHelper.error(context, 'Select an employee'); return; }
    if (_selectedPeriod == null) { ToastHelper.error(context, 'Select a period'); return; }
    setState(() { _saving = true; _err = null; });
    final deductions = _deductionTypes.map((d) => {
      'deduction_type_id': d['id'],
      'amount': double.tryParse(_deductionCtrls[d['id'].toString()]?.text ?? '') ?? 0,
    }).where((d) => (d['amount'] as double) > 0).toList();
    try {
      await ApiService.post('/payroll/entries', {
        'employee_id': _selectedEmployee,
        'period_id': _selectedPeriod,
        'basic_salary': double.tryParse(_basicCtrl.text) ?? 0,
        'allowances': {
          'housing': double.tryParse(_housingCtrl.text) ?? 0,
          'transport': double.tryParse(_transportCtrl.text) ?? 0,
          'medical': double.tryParse(_medicalCtrl.text) ?? 0,
          'other': double.tryParse(_otherAllowanceCtrl.text) ?? 0,
        },
        'overtime': {
          'hours': double.tryParse(_overtimeHoursCtrl.text) ?? 0,
          'rate': double.tryParse(_overtimeRateCtrl.text) ?? 0,
        },
        'bonuses': double.tryParse(_bonusCtrl.text) ?? 0,
        'deductions': deductions,
      });
      if (mounted) { ToastHelper.success(context, 'Entry added'); Navigator.pop(context); }
    } on ApiException catch (e) { setState(() { _err = e.message; _saving = false; }); }
    catch (_) { setState(() { _err = 'Save failed'; _saving = false; }); }
  }

  double get _totalAllowances =>
    (double.tryParse(_housingCtrl.text) ?? 0) +
    (double.tryParse(_transportCtrl.text) ?? 0) +
    (double.tryParse(_medicalCtrl.text) ?? 0) +
    (double.tryParse(_otherAllowanceCtrl.text) ?? 0);

  double get _totalOvertime =>
    (double.tryParse(_overtimeHoursCtrl.text) ?? 0) *
    (double.tryParse(_overtimeRateCtrl.text) ?? 0);

  double get _totalDeductions =>
    _deductionTypes.fold(0.0, (s, d) => s + (double.tryParse(_deductionCtrls[d['id'].toString()]?.text ?? '') ?? 0));

  double get _grossPay =>
    (double.tryParse(_basicCtrl.text) ?? 0) +
    _totalAllowances +
    _totalOvertime +
    (double.tryParse(_bonusCtrl.text) ?? 0);

  double get _netPay => _grossPay - _totalDeductions;

  final _fmt = NumberFormat('#,##0.00');
  final _currency = 'TSh';

  String _f(double v) => '$_currency ${_fmt.format(v)}';

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        title: const Text('Add Payroll Entry', style: TextStyle(fontWeight: FontWeight.w800, color: Color(0xFF0F172A), fontSize: 20)),
        backgroundColor: Colors.white, elevation: 0, centerTitle: true,
        leading: IconButton(icon: const Icon(Icons.close), onPressed: () => Navigator.pop(context)),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Form(key: _form, child: Column(crossAxisAlignment: CrossAxisAlignment.stretch, children: [
          if (_err != null) Container(padding: const EdgeInsets.all(12), margin: const EdgeInsets.only(bottom: 16),
            decoration: BoxDecoration(color: AppColors.dangerLt, borderRadius: BorderRadius.circular(10)),
            child: Text(_err!, style: const TextStyle(color: AppColors.danger))),
          GlassCard(child: Column(children: [
            if (_loadingEmployees) const Center(child: Padding(padding: EdgeInsets.all(8), child: CircularProgressIndicator(strokeWidth: 2)))
            else DropdownButtonFormField<String>(value: _selectedEmployee, decoration: const InputDecoration(labelText: 'Employee *'),
              items: _employees.map((e) => DropdownMenuItem(value: e['id'].toString(), child: Text(e['name']?.toString() ?? ''))).toList(),
              onChanged: (v) {
                setState(() {
                  _selectedEmployee = v;
                  final emp = _employees.firstWhere((e) => e['id'].toString() == v, orElse: () => {});
                  if (emp.isNotEmpty && emp['basic_salary'] != null) {
                    _basicCtrl.text = '${emp['basic_salary']}';
                  }
                });
              }),
            const SizedBox(height: 12),
            if (_loadingPeriods) const Center(child: Padding(padding: EdgeInsets.all(8), child: CircularProgressIndicator(strokeWidth: 2)))
            else DropdownButtonFormField<String>(value: _selectedPeriod, decoration: const InputDecoration(labelText: 'Pay Period *'),
              items: _periods.map((p) => DropdownMenuItem(value: p['id'].toString(), child: Text(p['name']?.toString() ?? ''))).toList(),
              onChanged: (v) => setState(() => _selectedPeriod = v)),
          ])),
          const SizedBox(height: 16),
          GlassCard(child: Column(children: [
            const Text('Salary & Allowances', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 14, color: AppColors.textPri)),
            const SizedBox(height: 12),
            TextFormField(controller: _basicCtrl, keyboardType: TextInputType.number, decoration: const InputDecoration(labelText: 'Basic Salary *'), validator: (v) => (v != null && double.tryParse(v) != null) ? null : 'Required'),
            const SizedBox(height: 12),
            Row(children: [
              Expanded(child: TextFormField(controller: _housingCtrl, keyboardType: TextInputType.number, decoration: const InputDecoration(labelText: 'Housing Allowance'))),
              const SizedBox(width: 12),
              Expanded(child: TextFormField(controller: _transportCtrl, keyboardType: TextInputType.number, decoration: const InputDecoration(labelText: 'Transport'))),
            ]),
            const SizedBox(height: 12),
            Row(children: [
              Expanded(child: TextFormField(controller: _medicalCtrl, keyboardType: TextInputType.number, decoration: const InputDecoration(labelText: 'Medical'))),
              const SizedBox(width: 12),
              Expanded(child: TextFormField(controller: _otherAllowanceCtrl, keyboardType: TextInputType.number, decoration: const InputDecoration(labelText: 'Other Allowances'))),
            ]),
          ])),
          const SizedBox(height: 16),
          GlassCard(child: Column(children: [
            const Text('Overtime & Bonuses', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 14, color: AppColors.textPri)),
            const SizedBox(height: 12),
            Row(children: [
              Expanded(child: TextFormField(controller: _overtimeHoursCtrl, keyboardType: TextInputType.number, decoration: const InputDecoration(labelText: 'Overtime Hours'))),
              const SizedBox(width: 12),
              Expanded(child: TextFormField(controller: _overtimeRateCtrl, keyboardType: TextInputType.number, decoration: const InputDecoration(labelText: 'Rate / Hour'))),
            ]),
            const SizedBox(height: 12),
            TextFormField(controller: _bonusCtrl, keyboardType: TextInputType.number, decoration: const InputDecoration(labelText: 'Bonuses')),
          ])),
          const SizedBox(height: 16),
          GlassCard(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            const Text('Deductions', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 14, color: AppColors.textPri)),
            const SizedBox(height: 12),
            if (_loadingDeductions) const Center(child: Padding(padding: EdgeInsets.all(8), child: CircularProgressIndicator(strokeWidth: 2)))
            else ..._deductionTypes.map((d) => Padding(
              padding: const EdgeInsets.only(bottom: 10),
              child: TextFormField(
                controller: _deductionCtrls[d['id'].toString()],
                keyboardType: TextInputType.number,
                decoration: InputDecoration(
                  labelText: d['name']?.toString() ?? 'Deduction',
                  hintText: d['type']?.toString().toLowerCase() == 'percentage' ? '${d['value']}% of basic' : null,
                ),
              ),
            )),
          ])),
          const SizedBox(height: 16),
          GlassCard(
            child: Column(children: [
              _calcRow('Basic Salary', _f(double.tryParse(_basicCtrl.text) ?? 0)),
              _calcRow('Allowances', _f(_totalAllowances)),
              _calcRow('Overtime', _f(_totalOvertime)),
              _calcRow('Bonuses', _f(double.tryParse(_bonusCtrl.text) ?? 0)),
              const Divider(height: 20),
              _calcRow('Gross Pay', _f(_grossPay), bold: true, color: AppColors.textPri),
              _calcRow('Total Deductions', _f(_totalDeductions), color: AppColors.secondary),
              const Divider(height: 20, thickness: 1.5),
              _calcRow('NET PAY', _f(_netPay), bold: true, color: AppColors.success),
            ]),
          ),
          const SizedBox(height: 24),
          SizedBox(height: 54, child: ElevatedButton(
            style: ElevatedButton.styleFrom(
              backgroundColor: AppColors.success, foregroundColor: Colors.white,
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
            ),
            onPressed: _saving ? null : _save,
            child: _saving
                ? const SizedBox(width: 22, height: 22, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5))
                : const Text('Submit Entry', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700)),
          )),
          const SizedBox(height: 40),
        ])),
      ),
    );
  }

  Widget _calcRow(String label, String value, {bool bold = false, Color? color}) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 3),
      child: Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
        Text(label, style: TextStyle(fontSize: bold ? 14 : 13, fontWeight: bold ? FontWeight.w800 : FontWeight.w500, color: AppColors.textSec)),
        Text(value, style: TextStyle(fontSize: bold ? 16 : 13, fontWeight: bold ? FontWeight.w900 : FontWeight.w600, color: color ?? AppColors.textPri)),
      ]),
    );
  }
}
