import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../core/api_service.dart';
import '../../shared/theme/app_theme.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/shimmer_loading.dart';
import '../../widgets/toast_helper.dart';

class PayslipScreen extends StatefulWidget {
  const PayslipScreen({super.key});
  @override State<PayslipScreen> createState() => _PayslipScreenState();
}

class _PayslipScreenState extends State<PayslipScreen> {
  bool _loading = false;
  bool _loaded = false;
  String? _error;
  Map<String, dynamic>? _payslip;
  String? _selectedEmployee;
  String? _selectedPeriod;
  List<dynamic> _employees = [];
  List<dynamic> _periods = [];
  bool _loadingEmployees = true;
  bool _loadingPeriods = true;
  final _fmt = NumberFormat('#,##0.00');
  final _currency = 'TSh';

  @override
  void initState() { super.initState(); _loadData(); }

  Future<void> _loadData() async {
    try {
      final results = await Future.wait([
        ApiService.get('/payroll/employees'),
        ApiService.get('/payroll/periods'),
      ]);
      setState(() {
        _employees = results[0] is List ? results[0] : (results[0]['data'] ?? []);
        _periods = results[1] is List ? results[1] : (results[1]['data'] ?? []);
        _loadingEmployees = false;
        _loadingPeriods = false;
      });
    } catch (_) { setState(() { _loadingEmployees = false; _loadingPeriods = false; }); }
  }

  Future<void> _loadPayslip() async {
    if (_selectedEmployee == null || _selectedPeriod == null) {
      ToastHelper.error(context, 'Select employee and period');
      return;
    }
    setState(() { _loading = true; _error = null; });
    try {
      final res = await ApiService.get('/payroll/payslip?employee_id=$_selectedEmployee&period_id=$_selectedPeriod');
      setState(() { _payslip = res; _loaded = true; _loading = false; });
    } on ApiException catch (e) { setState(() { _error = e.message; _loading = false; }); }
    catch (_) { setState(() { _error = 'Load failed'; _loading = false; }); }
  }

  String _f(dynamic v) => '$_currency ${_fmt.format((v is num ? v.toDouble() : double.tryParse(v?.toString() ?? '0') ?? 0))}';

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        title: const Text('Payslip', style: TextStyle(fontWeight: FontWeight.w800, color: Color(0xFF0F172A), fontSize: 20)),
        backgroundColor: Colors.white, elevation: 0, centerTitle: true,
        leading: IconButton(icon: const Icon(Icons.close), onPressed: () => Navigator.pop(context)),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(crossAxisAlignment: CrossAxisAlignment.stretch, children: [
          GlassCard(child: Column(children: [
            if (_loadingEmployees) const Center(child: Padding(padding: EdgeInsets.all(8), child: CircularProgressIndicator(strokeWidth: 2)))
            else DropdownButtonFormField<String>(value: _selectedEmployee, decoration: const InputDecoration(labelText: 'Employee'),
              items: _employees.map((e) => DropdownMenuItem(value: e['id'].toString(), child: Text(e['name']?.toString() ?? ''))).toList(),
              onChanged: (v) => setState(() => _selectedEmployee = v)),
            const SizedBox(height: 12),
            if (_loadingPeriods) const Center(child: Padding(padding: EdgeInsets.all(8), child: CircularProgressIndicator(strokeWidth: 2)))
            else DropdownButtonFormField<String>(value: _selectedPeriod, decoration: const InputDecoration(labelText: 'Pay Period'),
              items: _periods.map((p) => DropdownMenuItem(value: p['id'].toString(), child: Text(p['name']?.toString() ?? ''))).toList(),
              onChanged: (v) => setState(() => _selectedPeriod = v)),
            const SizedBox(height: 16),
            SizedBox(height: 50, child: ElevatedButton(
              style: ElevatedButton.styleFrom(backgroundColor: AppColors.primary, foregroundColor: Colors.white,
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14))),
              onPressed: _loading ? null : _loadPayslip,
              child: _loading
                  ? const SizedBox(width: 22, height: 22, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5))
                  : const Text('Generate Payslip', style: TextStyle(fontSize: 15, fontWeight: FontWeight.w700)),
            )),
          ])),
          if (_error != null) ...[
            const SizedBox(height: 16),
            Container(padding: const EdgeInsets.all(12), decoration: BoxDecoration(color: AppColors.dangerLt, borderRadius: BorderRadius.circular(10)),
              child: Text(_error!, style: const TextStyle(color: AppColors.danger))),
          ],
          if (_loaded && _payslip != null) ...[
            const SizedBox(height: 20),
            _buildPayslip(),
          ],
          const SizedBox(height: 40),
        ]),
      ),
    );
  }

  Widget _buildPayslip() {
    final p = _payslip!;
    return GlassCard(
      padding: const EdgeInsets.all(20),
      child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
        Center(child: Column(children: [
          Container(width: 56, height: 56, decoration: BoxDecoration(
            gradient: const LinearGradient(colors: [AppColors.primary, AppColors.primaryDark], begin: Alignment.topLeft, end: Alignment.bottomRight),
            borderRadius: BorderRadius.circular(16)),
            child: const Icon(Icons.receipt_long_rounded, color: Colors.white, size: 28)),
          const SizedBox(height: 8),
          Text('PAYSLIP', style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 20, color: AppColors.textPri, letterSpacing: 2)),
          const SizedBox(height: 4),
          Text(p['period_name']?.toString() ?? '', style: const TextStyle(fontSize: 12, color: AppColors.textSec)),
        ])),
        const SizedBox(height: 20),
        Divider(color: Colors.grey.withValues(alpha: 0.15)),
        const SizedBox(height: 12),
        Row(children: [
          Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            Text(p['employee_name']?.toString() ?? 'Employee', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 16, color: AppColors.textPri)),
            Text(p['position']?.toString() ?? '', style: const TextStyle(fontSize: 12, color: AppColors.textSec)),
            Text(p['department']?.toString() ?? '', style: const TextStyle(fontSize: 12, color: AppColors.textSec)),
          ])),
          Column(crossAxisAlignment: CrossAxisAlignment.end, children: [
            Text('Employee #', style: const TextStyle(fontSize: 10, color: AppColors.textLight)),
            Text(p['employee_code']?.toString() ?? '-', style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13, color: AppColors.textPri)),
          ]),
        ]),
        const SizedBox(height: 20),
        _breakdownSection('EARNINGS', p['earnings'] as List? ?? [], AppColors.success),
        const SizedBox(height: 16),
        _breakdownSection('DEDUCTIONS', p['deductions'] as List? ?? [], AppColors.secondary),
        const SizedBox(height: 20),
        Divider(color: Colors.grey.withValues(alpha: 0.2), thickness: 1.5),
        const SizedBox(height: 12),
        Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
          const Text('NET PAY', style: TextStyle(fontSize: 18, fontWeight: FontWeight.w900, color: AppColors.textPri, letterSpacing: 1)),
          Text(_f(p['net_pay'] ?? 0), style: const TextStyle(fontSize: 24, fontWeight: FontWeight.w900, color: AppColors.success, letterSpacing: -0.5)),
        ]),
        const SizedBox(height: 20),
        Divider(color: Colors.grey.withValues(alpha: 0.15)),
        const SizedBox(height: 16),
        Row(children: [
          _payslipAction(Icons.download_rounded, 'Download PDF', AppColors.primary, () {}),
          const SizedBox(width: 10),
          _payslipAction(Icons.print_rounded, 'Print', AppColors.textPri, () {}),
          const SizedBox(width: 10),
          _payslipAction(Icons.share_rounded, 'Share', AppColors.primary, () {}),
        ]),
      ]),
    );
  }

  Widget _breakdownSection(String title, List<dynamic> items, Color color) {
    final total = items.fold(0.0, (s, i) => s + ((i['amount'] as num?)?.toDouble() ?? 0));
    return Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
      Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
        Text(title, style: TextStyle(fontWeight: FontWeight.w800, fontSize: 13, color: color, letterSpacing: 1)),
        Text(_f(total), style: TextStyle(fontWeight: FontWeight.w800, fontSize: 14, color: color)),
      ]),
      const SizedBox(height: 8),
      ...items.map((i) => Padding(
        padding: const EdgeInsets.symmetric(vertical: 4),
        child: Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
          Text(i['label']?.toString() ?? '', style: const TextStyle(fontSize: 12, color: AppColors.textSec)),
          Text(_f(i['amount'] ?? 0), style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w600, color: AppColors.textPri)),
        ]),
      )),
    ]);
  }

  Widget _payslipAction(IconData icon, String label, Color color, VoidCallback onTap) {
    return Expanded(
      child: GestureDetector(
        onTap: onTap,
        child: Container(
          padding: const EdgeInsets.symmetric(vertical: 12),
          decoration: BoxDecoration(color: color.withValues(alpha: 0.08), borderRadius: BorderRadius.circular(12)),
          child: Column(children: [
            Icon(icon, color: color, size: 22),
            const SizedBox(height: 4),
            Text(label, style: TextStyle(fontSize: 10, fontWeight: FontWeight.w600, color: color)),
          ]),
        ),
      ),
    );
  }
}
