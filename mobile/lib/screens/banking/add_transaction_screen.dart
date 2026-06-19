import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../core/api_service.dart';
import '../../shared/theme/app_theme.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/toast_helper.dart';
import '../../widgets/status_badge.dart';

class AddTransactionScreen extends StatefulWidget {
  const AddTransactionScreen({super.key});
  @override State<AddTransactionScreen> createState() => _AddTransactionScreenState();
}

class _AddTransactionScreenState extends State<AddTransactionScreen> {
  final _form = GlobalKey<FormState>();
  String _type = 'Income';
  final _amountCtrl = TextEditingController();
  final _descCtrl = TextEditingController();
  final _refCtrl = TextEditingController();
  String? _fromAccount;
  String? _toAccount;
  String? _category;
  DateTime _date = DateTime.now();
  bool _saving = false;
  String? _err;
  List<dynamic> _accounts = [];
  bool _loadingAccounts = true;

  final _types = ['Income', 'Expense', 'Transfer'];
  final _categories = ['Salary', 'Sales', 'Transfer', 'Utilities', 'Rent', 'Supplies', 'Services', 'Other'];

  @override
  void initState() { super.initState(); _loadAccounts(); }
  @override void dispose() { for (final c in [_amountCtrl, _descCtrl, _refCtrl]) c.dispose(); super.dispose(); }

  Future<void> _loadAccounts() async {
    try {
      final res = await ApiService.get('/dashboard/banking/accounts');
      setState(() { _accounts = res is List ? res : (res['data'] ?? []); _loadingAccounts = false; });
    } catch (_) { setState(() { _loadingAccounts = false; }); }
  }

  Future<void> _save() async {
    if (!_form.currentState!.validate()) return;
    setState(() { _saving = true; _err = null; });
    try {
      await ApiService.post('/dashboard/banking/transactions', {
        'type': _type.toLowerCase(),
        'amount': double.tryParse(_amountCtrl.text) ?? 0,
        'description': _descCtrl.text.trim(),
        'reference': _refCtrl.text.trim(),
        'from_account': _fromAccount,
        'to_account': _toAccount,
        'category': _category,
        'date': DateFormat('yyyy-MM-dd').format(_date),
      });
      if (mounted) { ToastHelper.success(context, 'Transaction added'); Navigator.pop(context); }
    } on ApiException catch (e) { setState(() { _err = e.message; _saving = false; }); }
    catch (_) { setState(() { _err = 'Save failed'; _saving = false; }); }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        title: const Text('New Transaction', style: TextStyle(fontWeight: FontWeight.w800, color: Color(0xFF0F172A), fontSize: 20)),
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
            const Text('Transaction Type', style: TextStyle(fontWeight: FontWeight.w600, fontSize: 13, color: AppColors.textPri)),
            const SizedBox(height: 10),
            Row(children: _types.map((t) {
              final selected = _type == t;
              return Expanded(child: Padding(
                padding: const EdgeInsets.symmetric(horizontal: 3),
                child: GestureDetector(
                  onTap: () => setState(() => _type = t),
                  child: Container(
                    padding: const EdgeInsets.symmetric(vertical: 12),
                    decoration: BoxDecoration(
                      color: selected ? _typeColor(t) : AppColors.surfaceVariant,
                      borderRadius: BorderRadius.circular(10),
                      border: Border.all(color: selected ? _typeColor(t) : AppColors.border),
                    ),
                    child: Column(children: [
                      Icon(_typeIcon(t), color: selected ? Colors.white : AppColors.textSec, size: 20),
                      const SizedBox(height: 4),
                      Text(t, style: TextStyle(fontSize: 11, fontWeight: FontWeight.w600, color: selected ? Colors.white : AppColors.textSec)),
                    ]),
                  ),
                ),
              ));
            }).toList()),
          ])),
          const SizedBox(height: 16),
          GlassCard(child: Column(children: [
            TextFormField(controller: _amountCtrl, keyboardType: TextInputType.number,
              decoration: InputDecoration(labelText: 'Amount *', prefixText: 'TSh ', prefixStyle: const TextStyle(fontWeight: FontWeight.w700, color: AppColors.textPri)),
              validator: (v) => (v != null && double.tryParse(v) != null && double.parse(v) > 0) ? null : 'Enter valid amount'),
            const SizedBox(height: 12),
            if (_type == 'Transfer') ...[
              DropdownButtonFormField<String>(value: _fromAccount, decoration: const InputDecoration(labelText: 'From Account'),
                items: _accounts.map((a) => DropdownMenuItem(value: a['id'].toString(), child: Text(a['account_name']?.toString() ?? ''))).toList(),
                onChanged: (v) => setState(() => _fromAccount = v)),
              const SizedBox(height: 12),
              DropdownButtonFormField<String>(value: _toAccount, decoration: const InputDecoration(labelText: 'To Account'),
                items: _accounts.map((a) => DropdownMenuItem(value: a['id'].toString(), child: Text(a['account_name']?.toString() ?? ''))).toList(),
                onChanged: (v) => setState(() => _toAccount = v)),
            ],
            if (_type != 'Transfer') ...[
              DropdownButtonFormField<String>(value: _fromAccount, decoration: InputDecoration(labelText: _type == 'Income' ? 'Deposit To *' : 'From Account *'),
                items: _accounts.map((a) => DropdownMenuItem(value: a['id'].toString(), child: Text(a['account_name']?.toString() ?? ''))).toList(),
                onChanged: (v) => setState(() => _fromAccount = v)),
            ],
            const SizedBox(height: 12),
            DropdownButtonFormField<String>(value: _category, decoration: const InputDecoration(labelText: 'Category'),
              items: _categories.map((c) => DropdownMenuItem(value: c, child: Text(c))).toList(),
              onChanged: (v) => setState(() => _category = v)),
            const SizedBox(height: 12),
            GestureDetector(
              onTap: () async {
                final d = await showDatePicker(context: context, initialDate: _date, firstDate: DateTime(2020), lastDate: DateTime(2030));
                if (d != null) setState(() => _date = d);
              },
              child: AbsorbPointer(
                child: TextFormField(decoration: InputDecoration(labelText: 'Date', suffixIcon: const Icon(Icons.calendar_today, size: 18),
                  hintText: DateFormat('dd MMM yyyy').format(_date))),
              ),
            ),
          ])),
          const SizedBox(height: 16),
          GlassCard(child: Column(children: [
            TextFormField(controller: _descCtrl, maxLines: 2, decoration: const InputDecoration(labelText: 'Description', hintText: 'What is this for?')),
            const SizedBox(height: 12),
            TextFormField(controller: _refCtrl, decoration: const InputDecoration(labelText: 'Reference Number', hintText: 'Optional')),
          ])),
          const SizedBox(height: 24),
          SizedBox(height: 54, child: ElevatedButton(
            style: ElevatedButton.styleFrom(backgroundColor: AppColors.primary, foregroundColor: Colors.white,
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14))),
            onPressed: _saving ? null : _save,
            child: _saving
                ? const SizedBox(width: 22, height: 22, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5))
                : const Text('Submit Transaction', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700)),
          )),
          const SizedBox(height: 40),
        ])),
      ),
    );
  }

  Color _typeColor(String t) {
    switch (t) {
      case 'Income': return AppColors.success;
      case 'Expense': return AppColors.secondary;
      case 'Transfer': return AppColors.primary;
      default: return AppColors.primary;
    }
  }

  IconData _typeIcon(String t) {
    switch (t) {
      case 'Income': return Icons.arrow_downward_rounded;
      case 'Expense': return Icons.arrow_upward_rounded;
      case 'Transfer': return Icons.swap_horiz_rounded;
      default: return Icons.swap_horiz_rounded;
    }
  }
}
