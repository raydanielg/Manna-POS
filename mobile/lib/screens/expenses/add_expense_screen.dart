import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../../providers/expense_provider.dart';
import '../../models/expense.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/toast_helper.dart';
import '../../constants/app_constants.dart';

class AddExpenseScreen extends StatefulWidget {
  final Expense? expense;
  const AddExpenseScreen({super.key, this.expense});
  @override State<AddExpenseScreen> createState() => _AddExpenseScreenState();
}

class _AddExpenseScreenState extends State<AddExpenseScreen> {
  final _form = GlobalKey<FormState>();
  late TextEditingController _referenceCtrl;
  late TextEditingController _descCtrl;
  late TextEditingController _amountCtrl;
  late TextEditingController _dateCtrl;
  late TextEditingController _notesCtrl;
  int? _categoryId;
  bool _saving = false;
  bool _isEdit = false;

  @override
  void initState() {
    super.initState();
    final e = widget.expense;
    _isEdit = e != null;
    _referenceCtrl = TextEditingController(text: e?.reference ?? 'EXP-${DateTime.now().millisecondsSinceEpoch}');
    _descCtrl = TextEditingController(text: e?.description ?? '');
    _amountCtrl = TextEditingController(text: e?.amount.toString());
    _dateCtrl = TextEditingController(text: e?.expenseDate ?? DateFormat('yyyy-MM-dd').format(DateTime.now()));
    _notesCtrl = TextEditingController(text: e?.notes ?? '');
    _categoryId = e?.categoryId;
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final ep = context.read<ExpenseProvider>();
      if (ep.categories.isEmpty) ep.fetchCategories();
    });
  }

  @override
  void dispose() {
    for (final c in [_referenceCtrl, _descCtrl, _amountCtrl, _dateCtrl, _notesCtrl]) c.dispose();
    super.dispose();
  }

  Future<void> _save() async {
    if (!_form.currentState!.validate()) return;
    setState(() => _saving = true);
    try {
      final provider = context.read<ExpenseProvider>();
      final data = {
        'reference': _referenceCtrl.text.trim(),
        'description': _descCtrl.text.trim(),
        'amount': double.tryParse(_amountCtrl.text) ?? 0,
        'expense_date': _dateCtrl.text.trim(),
        'notes': _notesCtrl.text.trim(),
        if (_categoryId != null) 'expense_category_id': _categoryId,
      };
      if (_isEdit) {
        await provider.updateExpense(widget.expense!.id, data);
      } else {
        await provider.createExpense(data);
      }
      if (mounted) {
        ToastHelper.showSuccess(context, _isEdit ? 'Expense updated' : 'Expense created');
        Navigator.pop(context, true);
      }
    } catch (e) {
      if (mounted) ToastHelper.showError(context, e.toString());
      setState(() => _saving = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    final ep = context.watch<ExpenseProvider>();
    return Scaffold(
      backgroundColor: theme.scaffoldBackgroundColor,
      appBar: AppBar(
        title: Text(_isEdit ? 'Edit Expense' : 'Add Expense'),
        actions: [
          TextButton(
            onPressed: _saving ? null : _save,
            child: _saving
                ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(strokeWidth: 2))
                : Text(_isEdit ? 'Update' : 'Save', style: const TextStyle(fontWeight: FontWeight.w700)),
          ),
        ],
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.fromLTRB(16, 8, 16, 100),
        child: Form(
          key: _form,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              TextFormField(
                controller: _referenceCtrl,
                decoration: const InputDecoration(labelText: 'Reference', prefixIcon: Icon(Icons.tag, size: 20)),
              ),
              const SizedBox(height: 12),
              TextFormField(
                controller: _descCtrl,
                decoration: const InputDecoration(labelText: 'Description *', prefixIcon: Icon(Icons.description_outlined, size: 20)),
                validator: (v) => v!.trim().isEmpty ? 'Required' : null,
              ),
              const SizedBox(height: 12),
              Row(children: [
                Expanded(child: TextFormField(
                  controller: _amountCtrl,
                  keyboardType: TextInputType.number,
                  decoration: const InputDecoration(labelText: 'Amount *', prefixIcon: Icon(Icons.attach_money, size: 20)),
                  validator: (v) => (v == null || double.tryParse(v) == null || double.parse(v) <= 0) ? 'Enter valid amount' : null,
                )),
                const SizedBox(width: 12),
                Expanded(child: TextFormField(
                  controller: _dateCtrl,
                  readOnly: true,
                  decoration: const InputDecoration(labelText: 'Date', prefixIcon: Icon(Icons.calendar_today_outlined, size: 20)),
                  onTap: () async {
                    final d = await showDatePicker(context: context, initialDate: DateTime.now(), firstDate: DateTime(2020), lastDate: DateTime(2030));
                    if (d != null) _dateCtrl.text = DateFormat('yyyy-MM-dd').format(d);
                  },
                )),
              ]),
              const SizedBox(height: 12),
              DropdownButtonFormField<int>(
                decoration: const InputDecoration(labelText: 'Category', prefixIcon: Icon(Icons.category_outlined, size: 20)),
                value: _categoryId,
                items: ep.categories.map((c) => DropdownMenuItem(value: c.id, child: Text(c.name))).toList(),
                onChanged: (v) => setState(() => _categoryId = v),
              ),
              const SizedBox(height: 12),
              TextFormField(
                controller: _notesCtrl,
                maxLines: 3,
                decoration: const InputDecoration(labelText: 'Notes', prefixIcon: Icon(Icons.notes_outlined, size: 20), alignLabelWithHint: true),
              ),
              const SizedBox(height: 16),
              GlassCard(
                child: InkWell(
                  borderRadius: BorderRadius.circular(14),
                  onTap: () => ToastHelper.showInfo(context, 'Receipt upload coming soon'),
                  child: Container(
                    padding: const EdgeInsets.symmetric(vertical: 24),
                    child: Column(
                      children: [
                        Icon(Icons.cloud_upload_outlined, size: 36, color: AppColors.primary.withValues(alpha: 0.6)),
                        const SizedBox(height: 8),
                        const Text('Upload Receipt', style: TextStyle(color: AppColors.primary, fontWeight: FontWeight.w600)),
                        const SizedBox(height: 4),
                        const Text('PDF, JPG, PNG up to 5MB', style: TextStyle(color: AppColors.textSec, fontSize: 11)),
                      ],
                    ),
                  ),
                ),
              ),
              const SizedBox(height: 32),
              SizedBox(
                height: 54,
                child: ElevatedButton(
                  onPressed: _saving ? null : _save,
                  child: _saving
                      ? const SizedBox(width: 24, height: 24, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5))
                      : Text(_isEdit ? 'Update Expense' : 'Create Expense', style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w700)),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
