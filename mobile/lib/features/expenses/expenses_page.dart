import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../core/api_service.dart';
import '../../shared/theme/app_theme.dart';
import '../../shared/models/expense.dart';
import '../../shared/widgets/app_card.dart';
import '../../shared/widgets/empty_state.dart';

class ExpensesPage extends StatefulWidget {
  const ExpensesPage({super.key});
  @override State<ExpensesPage> createState() => _ExpensesPageState();
}

class _ExpensesPageState extends State<ExpensesPage> {
  List<Expense> _expenses = [];
  List<Map<String, dynamic>> _categories = [];
  bool _loading = true;
  String? _error;
  final _fmt = NumberFormat('#,##0.00');

  @override void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    setState(() { _loading = true; _error = null; });
    try {
      final res = await Future.wait([ApiService.get('/expenses'), ApiService.get('/expense-categories')]);
      setState(() {
        _expenses = (res[0] as List).map((e) => Expense.fromJson(e)).toList();
        _categories = (res[1] as List).map((e) => {'id': e['id'], 'name': e['name']}).toList();
        _loading = false;
      });
    } on ApiException catch (e) { setState(() { _error = e.message; _loading = false; }); }
    catch (_) { setState(() { _error = 'Connection error'; _loading = false; }); }
  }

  void _showForm([Expense? e]) => showModalBottomSheet(context: context, isScrollControlled: true, backgroundColor: Colors.transparent,
    builder: (_) => _ExpenseForm(expense: e, categories: _categories, onSaved: _load));

  Future<void> _delete(Expense e) async {
    final ok = await showDialog<bool>(context: context, builder: (_) => AlertDialog(
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
      title: const Text('Delete Expense'),
      content: Text('Delete "${e.title}"?'),
      actions: [TextButton(onPressed: () => Navigator.pop(context, false), child: const Text('Cancel')),
        ElevatedButton(style: ElevatedButton.styleFrom(backgroundColor: AppColors.danger), onPressed: () => Navigator.pop(context, true), child: const Text('Delete'))],
    ));
    if (ok != true) return;
    try { await ApiService.delete('/expenses/${e.id}'); _load(); }
    on ApiException catch (ex) { _snack(ex.message, error: true); }
  }

  void _snack(String msg, {bool error = false}) => ScaffoldMessenger.of(context).showSnackBar(SnackBar(
    content: Text(msg), backgroundColor: error ? AppColors.danger : AppColors.success,
    behavior: SnackBarBehavior.floating, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10))));

  double get _total => _expenses.fold(0, (s, e) => s + e.amount);

  @override
  Widget build(BuildContext context) => Scaffold(
    backgroundColor: AppColors.bg,
    appBar: AppBar(title: const Text('Expenses'), actions: [IconButton(icon: const Icon(Icons.refresh, color: Colors.white), onPressed: _load)]),
    body: _loading ? const LoadingWidget(message: 'Loading expenses...')
      : _error != null ? ErrorWidget2(message: _error!, onRetry: _load)
      : Column(children: [
          if (_expenses.isNotEmpty) Container(margin: const EdgeInsets.all(16), padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 14),
            decoration: BoxDecoration(color: AppColors.dangerLt, borderRadius: BorderRadius.circular(14), border: Border.all(color: AppColors.danger.withOpacity(0.3))),
            child: Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
              const Text('Total Expenses', style: TextStyle(color: AppColors.danger, fontWeight: FontWeight.w600)),
              Text('TSh ${_fmt.format(_total)}', style: const TextStyle(color: AppColors.danger, fontWeight: FontWeight.w800, fontSize: 18)),
            ])),
          Expanded(child: _expenses.isEmpty
            ? EmptyState(icon: Icons.receipt_long_outlined, title: 'No Expenses', subtitle: 'Track your business expenses', actionLabel: 'Add Expense', onAction: () => _showForm())
            : RefreshIndicator(color: AppColors.primary, onRefresh: _load,
                child: ListView.separated(padding: const EdgeInsets.fromLTRB(16, 0, 16, 100), itemCount: _expenses.length, separatorBuilder: (_, __) => const SizedBox(height: 10), itemBuilder: (_, i) => _tile(_expenses[i])))),
        ]),
    floatingActionButton: FloatingActionButton.extended(onPressed: () => _showForm(), icon: const Icon(Icons.add), label: const Text('Add Expense')),
  );

  Widget _tile(Expense e) => AppCard(onTap: () => _showForm(e), child: Padding(padding: const EdgeInsets.all(16), child: Row(children: [
    Container(width: 48, height: 48, decoration: BoxDecoration(color: AppColors.dangerLt, borderRadius: BorderRadius.circular(12)),
      child: const Icon(Icons.receipt_outlined, color: AppColors.danger)),
    const SizedBox(width: 14),
    Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
      Text(e.title, style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 15)),
      Text(e.categoryName, style: const TextStyle(color: AppColors.textSec, fontSize: 13)),
      Text(e.expenseDate, style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
    ])),
    Column(crossAxisAlignment: CrossAxisAlignment.end, children: [
      Text('TSh ${_fmt.format(e.amount)}', style: const TextStyle(color: AppColors.danger, fontWeight: FontWeight.w800, fontSize: 15)),
      const SizedBox(height: 6),
      PopupMenuButton<String>(shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)), onSelected: (v) { if (v == 'edit') _showForm(e); else _delete(e); },
        itemBuilder: (_) => [const PopupMenuItem(value: 'edit', child: Row(children: [Icon(Icons.edit_outlined, size: 18, color: AppColors.primary), SizedBox(width: 8), Text('Edit')])), const PopupMenuItem(value: 'delete', child: Row(children: [Icon(Icons.delete_outline, size: 18, color: AppColors.danger), SizedBox(width: 8), Text('Delete', style: TextStyle(color: AppColors.danger))]))]),
    ]),
  ])));
}

class _ExpenseForm extends StatefulWidget {
  final Expense? expense;
  final List<Map<String, dynamic>> categories;
  final VoidCallback onSaved;
  const _ExpenseForm({this.expense, required this.categories, required this.onSaved});
  @override State<_ExpenseForm> createState() => _ExpenseFormState();
}

class _ExpenseFormState extends State<_ExpenseForm> {
  final _form = GlobalKey<FormState>();
  late TextEditingController _title, _amount, _date, _notes;
  int? _catId;
  bool _saving = false;
  String? _err;

  @override
  void initState() {
    super.initState();
    final e = widget.expense;
    _title = TextEditingController(text: e?.title);
    _amount = TextEditingController(text: e?.amount.toString());
    _date = TextEditingController(text: e?.expenseDate ?? DateFormat('yyyy-MM-dd').format(DateTime.now()));
    _notes = TextEditingController(text: e?.notes);
    _catId = e?.categoryId;
  }

  @override void dispose() { for (final c in [_title, _amount, _date, _notes]) c.dispose(); super.dispose(); }

  Future<void> _save() async {
    if (!_form.currentState!.validate()) return;
    setState(() { _saving = true; _err = null; });
    final body = {'reference': _title.text.trim(), 'amount': double.tryParse(_amount.text) ?? 0, 'expense_date': _date.text, if (_catId != null) 'expense_category_id': _catId, if (_notes.text.isNotEmpty) 'notes': _notes.text.trim()};
    try {
      if (widget.expense != null) await ApiService.put('/expenses/${widget.expense!.id}', body);
      else await ApiService.post('/expenses', body);
      widget.onSaved();
      if (mounted) Navigator.pop(context);
    } on ApiException catch (e) { setState(() { _err = e.message; _saving = false; }); }
    catch (_) { setState(() { _err = 'Save failed'; _saving = false; }); }
  }

  @override
  Widget build(BuildContext context) => Container(
    decoration: const BoxDecoration(color: AppColors.surface, borderRadius: BorderRadius.vertical(top: Radius.circular(24))),
    padding: EdgeInsets.only(bottom: MediaQuery.of(context).viewInsets.bottom),
    child: SingleChildScrollView(padding: const EdgeInsets.fromLTRB(24, 16, 24, 32), child: Form(key: _form, child: Column(crossAxisAlignment: CrossAxisAlignment.stretch, children: [
      Center(child: Container(width: 40, height: 4, decoration: BoxDecoration(color: AppColors.border, borderRadius: BorderRadius.circular(4)))),
      const SizedBox(height: 20),
      Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [Text(widget.expense != null ? 'Edit Expense' : 'Add Expense', style: const TextStyle(fontSize: 20, fontWeight: FontWeight.w700)), IconButton(icon: const Icon(Icons.close), onPressed: () => Navigator.pop(context))]),
      if (_err != null) ...[const SizedBox(height: 12), Container(padding: const EdgeInsets.all(12), decoration: BoxDecoration(color: AppColors.dangerLt, borderRadius: BorderRadius.circular(10)), child: Text(_err!, style: const TextStyle(color: AppColors.danger)))],
      const SizedBox(height: 16),
      TextFormField(controller: _title, decoration: const InputDecoration(labelText: 'Reference / Title *'), validator: (v) => v!.isNotEmpty ? null : 'Required'),
      const SizedBox(height: 12),
      Row(children: [
        Expanded(child: TextFormField(controller: _amount, keyboardType: TextInputType.number, decoration: const InputDecoration(labelText: 'Amount *'), validator: (v) => double.tryParse(v ?? '') != null ? null : 'Enter valid amount')),
        const SizedBox(width: 12),
        Expanded(child: TextFormField(controller: _date, readOnly: true, decoration: const InputDecoration(labelText: 'Date', suffixIcon: Icon(Icons.calendar_today, size: 18)),
          onTap: () async { final d = await showDatePicker(context: context, initialDate: DateTime.now(), firstDate: DateTime(2020), lastDate: DateTime(2030)); if (d != null) _date.text = DateFormat('yyyy-MM-dd').format(d); })),
      ]),
      const SizedBox(height: 12),
      DropdownButtonFormField<int>(value: _catId, decoration: const InputDecoration(labelText: 'Category'),
        items: widget.categories.map((c) => DropdownMenuItem<int>(value: c['id'], child: Text(c['name'].toString()))).toList(),
        onChanged: (v) => setState(() => _catId = v)),
      const SizedBox(height: 12),
      TextFormField(controller: _notes, maxLines: 2, decoration: const InputDecoration(labelText: 'Notes')),
      const SizedBox(height: 24),
      SizedBox(height: 52, child: ElevatedButton(onPressed: _saving ? null : _save, child: _saving ? const SizedBox(width: 22, height: 22, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5)) : Text(widget.expense != null ? 'Update Expense' : 'Add Expense', style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w700)))),
    ]))),
  );
}