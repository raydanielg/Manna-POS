import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../core/api_service.dart';
import '../../shared/theme/app_theme.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/toast_helper.dart';
import '../../widgets/search_bar_widget.dart';

class AddRepaymentScreen extends StatefulWidget {
  const AddRepaymentScreen({super.key});
  @override State<AddRepaymentScreen> createState() => _AddRepaymentScreenState();
}

class _AddRepaymentScreenState extends State<AddRepaymentScreen> {
  final _form = GlobalKey<FormState>();
  final _amountCtrl = TextEditingController();
  final _receiptCtrl = TextEditingController();
  final _notesCtrl = TextEditingController();
  final _searchCtrl = TextEditingController();
  String? _selectedLoan;
  String _paymentMethod = 'Cash';
  DateTime _date = DateTime.now();
  bool _saving = false;
  String? _err;
  List<dynamic> _loans = [];
  bool _loadingLoans = true;
  double _remainingBalance = 0;
  final _fmt = NumberFormat('#,##0.00');
  final _currency = 'TSh';

  final _methods = ['Cash', 'Bank Transfer', 'Mobile Money', 'Cheque', 'Other'];

  @override
  void initState() { super.initState(); _loadLoans(); }
  @override void dispose() { for (final c in [_amountCtrl, _receiptCtrl, _notesCtrl, _searchCtrl]) c.dispose(); super.dispose(); }

  Future<void> _loadLoans() async {
    try {
      final res = await ApiService.get('/microfinance/loans');
      setState(() { _loans = res is List ? res : (res['data'] ?? []); _loadingLoans = false; });
    } catch (_) { setState(() { _loadingLoans = false; }); }
  }

  Future<void> _save() async {
    if (!_form.currentState!.validate()) return;
    if (_selectedLoan == null) { ToastHelper.error(context, 'Please select a loan'); return; }
    setState(() { _saving = true; _err = null; });
    try {
      await ApiService.post('/microfinance/repayments', {
        'loan_id': _selectedLoan,
        'amount': double.tryParse(_amountCtrl.text) ?? 0,
        'payment_method': _paymentMethod,
        'payment_date': DateFormat('yyyy-MM-dd').format(_date),
        'receipt_reference': _receiptCtrl.text.trim(),
        'notes': _notesCtrl.text.trim(),
      });
      if (mounted) { ToastHelper.success(context, 'Repayment recorded'); Navigator.pop(context); }
    } on ApiException catch (e) { setState(() { _err = e.message; _saving = false; }); }
    catch (_) { setState(() { _err = 'Save failed'; _saving = false; }); }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        title: const Text('Record Repayment', style: TextStyle(fontWeight: FontWeight.w800, color: Color(0xFF0F172A), fontSize: 20)),
        backgroundColor: Colors.white, elevation: 0, centerTitle: true,
        leading: IconButton(icon: const Icon(Icons.close), onPressed: () => Navigator.pop(context)),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Form(key: _form, child: Column(crossAxisAlignment: CrossAxisAlignment.stretch, children: [
          if (_err != null) Container(padding: const EdgeInsets.all(12), margin: const EdgeInsets.only(bottom: 16),
            decoration: BoxDecoration(color: AppColors.dangerLt, borderRadius: BorderRadius.circular(10)),
            child: Text(_err!, style: const TextStyle(color: AppColors.danger))),
          GlassCard(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            const Text('Select Loan', style: TextStyle(fontWeight: FontWeight.w600, fontSize: 13, color: AppColors.textPri)),
            const SizedBox(height: 8),
            TextFormField(controller: _searchCtrl, decoration: const InputDecoration(hintText: 'Search by client name...', prefixIcon: Icon(Icons.search, size: 20)),
              onChanged: (_) => setState(() {})),
            const SizedBox(height: 8),
            if (_loadingLoans)
              const Center(child: Padding(padding: EdgeInsets.all(8), child: CircularProgressIndicator(strokeWidth: 2)))
            else
              SizedBox(
                height: 140,
                child: ListView(
                  children: _loans.where((l) => _searchCtrl.text.isEmpty ||
                      (l['client_name']?.toString().toLowerCase() ?? '').contains(_searchCtrl.text.toLowerCase())).map((l) {
                    final selected = _selectedLoan == l['id'].toString();
                    final balance = (l['balance'] as num?)?.toDouble() ?? 0;
                    return ListTile(
                      dense: true,
                      leading: CircleAvatar(radius: 16, backgroundColor: selected ? AppColors.success : AppColors.successLt,
                        child: Text((l['client_name']?.toString() ?? '?')[0], style: TextStyle(color: selected ? Colors.white : AppColors.success, fontWeight: FontWeight.w700, fontSize: 13))),
                      title: Text(l['client_name']?.toString() ?? '', style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w600)),
                      subtitle: Text('Balance: $_currency ${_fmt.format(balance)}', style: TextStyle(fontSize: 11, color: balance > 0 ? AppColors.warning : AppColors.success)),
                      trailing: selected ? const Icon(Icons.check_circle, color: AppColors.success, size: 20) : null,
                      onTap: () {
                        setState(() {
                          _selectedLoan = l['id'].toString();
                          _remainingBalance = balance;
                        });
                      },
                    );
                  }).toList(),
                ),
              ),
          ])),
          const SizedBox(height: 16),
          GlassCard(child: Column(children: [
            TextFormField(controller: _amountCtrl, keyboardType: TextInputType.number,
              decoration: InputDecoration(labelText: 'Amount *',
                hintText: _remainingBalance > 0 ? 'Remaining: $_currency ${_fmt.format(_remainingBalance)}' : null),
              validator: (v) => (v != null && double.tryParse(v) != null && double.parse(v) > 0) ? null : 'Enter valid amount'),
            const SizedBox(height: 12),
            DropdownButtonFormField<String>(value: _paymentMethod, decoration: const InputDecoration(labelText: 'Payment Method'),
              items: _methods.map((m) => DropdownMenuItem(value: m, child: Text(m))).toList(),
              onChanged: (v) => setState(() => _paymentMethod = v!)),
            const SizedBox(height: 12),
            GestureDetector(
              onTap: () async {
                final d = await showDatePicker(context: context, initialDate: _date, firstDate: DateTime(2020), lastDate: DateTime(2030));
                if (d != null) setState(() => _date = d);
              },
              child: AbsorbPointer(
                child: TextFormField(decoration: InputDecoration(labelText: 'Payment Date',
                  hintText: DateFormat('dd MMM yyyy').format(_date), suffixIcon: const Icon(Icons.calendar_today, size: 18))),
              ),
            ),
          ])),
          const SizedBox(height: 16),
          GlassCard(child: Column(children: [
            TextFormField(controller: _receiptCtrl, decoration: const InputDecoration(labelText: 'Receipt / Reference')),
            const SizedBox(height: 12),
            TextFormField(controller: _notesCtrl, maxLines: 2, decoration: const InputDecoration(labelText: 'Notes (optional)')),
          ])),
          const SizedBox(height: 24),
          SizedBox(height: 54, child: ElevatedButton(
            style: ElevatedButton.styleFrom(backgroundColor: AppColors.success, foregroundColor: Colors.white,
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14))),
            onPressed: _saving ? null : _save,
            child: _saving
                ? const SizedBox(width: 22, height: 22, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5))
                : const Text('Record Repayment', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700)),
          )),
          const SizedBox(height: 40),
        ])),
      ),
    );
  }
}
