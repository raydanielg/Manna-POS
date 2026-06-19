import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/expense_provider.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/empty_state.dart';
import '../../widgets/shimmer_loading.dart';
import '../../widgets/confirm_dialog.dart';
import '../../widgets/toast_helper.dart';
import '../../widgets/app_bottom_sheet.dart';

class ExpenseCategoriesScreen extends StatefulWidget {
  const ExpenseCategoriesScreen({super.key});
  @override State<ExpenseCategoriesScreen> createState() => _ExpenseCategoriesScreenState();
}

class _ExpenseCategoriesScreenState extends State<ExpenseCategoriesScreen> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<ExpenseProvider>().fetchCategories();
    });
  }

  Future<void> _refresh() async {
    await context.read<ExpenseProvider>().fetchCategories();
  }

  void _showForm([Map<String, dynamic>? category]) {
    AppBottomSheet.show(
      context: context,
      title: category != null ? 'Edit Category' : 'Add Category',
      child: _ExpenseCategoryForm(category: category, onSaved: _refresh),
    );
  }

  Future<void> _delete(Map<String, dynamic> c) async {
    final confirmed = await ConfirmDialog.show(context, title: 'Delete Category', message: 'Delete "${c['name']}"?');
    if (confirmed != true) return;
    try {
      await context.read<ExpenseProvider>().deleteExpenseCategory(c['id']);
      if (mounted) ToastHelper.showSuccess(context, 'Category deleted');
      _refresh();
    } catch (e) {
      if (mounted) ToastHelper.showError(context, e.toString());
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Theme.of(context).scaffoldBackgroundColor,
      appBar: AppBar(
        title: const Text('Expense Categories'),
        actions: [IconButton(icon: const Icon(Icons.refresh), onPressed: _refresh)],
      ),
      body: Consumer<ExpenseProvider>(
        builder: (context, provider, _) {
          if (provider.isLoading) return const ShimmerLoading();
          final categories = provider.categories;
          if (categories.isEmpty) {
            return const EmptyState(icon: Icons.category_outlined, title: 'No Categories', subtitle: 'Add your first expense category', actionLabel: 'Add Category');
          }
          return RefreshIndicator(
            onRefresh: _refresh,
            child: ListView.separated(
              padding: const EdgeInsets.fromLTRB(16, 8, 16, 100),
              itemCount: categories.length,
              separatorBuilder: (_, __) => const SizedBox(height: 8),
              itemBuilder: (_, i) {
                final c = categories[i];
                return Dismissible(
                  key: ValueKey(c.id),
                  direction: DismissDirection.endToStart,
                  background: Container(alignment: Alignment.centerRight, padding: const EdgeInsets.only(right: 20), decoration: BoxDecoration(color: AppColors.danger, borderRadius: BorderRadius.circular(14)), child: const Icon(Icons.delete, color: Colors.white)),
                  confirmDismiss: (_) async { await _delete({'id': c.id, 'name': c.name}); return false; },
                  child: GlassCard(
                    onTap: () => _showForm({'id': c.id, 'name': c.name, 'description': c.description}),
                    child: ListTile(
                      leading: Container(width: 14, height: 14, decoration: BoxDecoration(color: c.color ?? AppColors.secondary, shape: BoxShape.circle)),
                      title: Text(c.name, style: const TextStyle(fontWeight: FontWeight.w600)),
                      subtitle: c.description != null ? Text(c.description!, style: const TextStyle(fontSize: 11)) : null,
                      trailing: Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          Text('${c.totalExpenses.toStringAsFixed(0)} expenses', style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
                          const SizedBox(width: 8),
                          const Icon(Icons.chevron_right, color: AppColors.textLight, size: 20),
                        ],
                      ),
                    ),
                  ),
                );
              },
            ),
          );
        },
      ),
      floatingActionButton: FloatingActionButton.extended(onPressed: () => _showForm(), icon: const Icon(Icons.add), label: const Text('Add Category')),
    );
  }
}

class _ExpenseCategoryForm extends StatefulWidget {
  final Map<String, dynamic>? category;
  final VoidCallback onSaved;
  const _ExpenseCategoryForm({this.category, required this.onSaved});
  @override State<_ExpenseCategoryForm> createState() => _ExpenseCategoryFormState();
}

class _ExpenseCategoryFormState extends State<_ExpenseCategoryForm> {
  final _form = GlobalKey<FormState>();
  late TextEditingController _nameCtrl, _descCtrl;
  Color _selectedColor = AppColors.secondary;
  bool _saving = false;

  final _colors = [
    AppColors.secondary, AppColors.primary, AppColors.success, AppColors.warning,
    AppColors.error, AppColors.purple, AppColors.pink, AppColors.cyan, AppColors.orange, AppColors.accent,
  ];

  @override
  void initState() {
    super.initState();
    final c = widget.category;
    _nameCtrl = TextEditingController(text: c?['name']);
    _descCtrl = TextEditingController(text: c?['description']);
  }

  @override
  void dispose() { _nameCtrl.dispose(); _descCtrl.dispose(); super.dispose(); }

  Future<void> _save() async {
    if (!_form.currentState!.validate()) return;
    setState(() => _saving = true);
    final body = {
      'name': _nameCtrl.text.trim(),
      'color': '#${_selectedColor.value.toRadixString(16).padLeft(8, '0').substring(2)}',
      if (_descCtrl.text.trim().isNotEmpty) 'description': _descCtrl.text.trim(),
    };
    try {
      if (widget.category != null) {
        await context.read<ExpenseProvider>().createExpense({'expense_category_id': widget.category!['id'], ...body});
      } else {
        await context.read<ExpenseProvider>().createExpense(body);
      }
      widget.onSaved();
      if (mounted) Navigator.pop(context);
    } catch (e) {
      if (mounted) ToastHelper.showError(context, e.toString());
      setState(() => _saving = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Form(
      key: _form,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          TextFormField(controller: _nameCtrl, decoration: const InputDecoration(labelText: 'Category Name *'), validator: (v) => v!.trim().isEmpty ? 'Required' : null),
          const SizedBox(height: 12),
          TextFormField(controller: _descCtrl, maxLines: 2, decoration: const InputDecoration(labelText: 'Description')),
          const SizedBox(height: 16),
          const Text('Color', style: TextStyle(fontSize: 13, fontWeight: FontWeight.w600, color: AppColors.textSec)),
          const SizedBox(height: 8),
          Wrap(
            spacing: 10, runSpacing: 10,
            children: _colors.map((color) => GestureDetector(
              onTap: () => setState(() => _selectedColor = color),
              child: Container(
                width: 36, height: 36,
                decoration: BoxDecoration(
                  color: color,
                  shape: BoxShape.circle,
                  border: _selectedColor == color ? Border.all(color: Colors.white, width: 3) : null,
                  boxShadow: _selectedColor == color ? [BoxShadow(color: color.withValues(alpha: 0.4), blurRadius: 8)] : null,
                ),
                child: _selectedColor == color ? const Icon(Icons.check, color: Colors.white, size: 18) : null,
              ),
            )).toList(),
          ),
          const SizedBox(height: 24),
          SizedBox(height: 52, child: ElevatedButton(
            onPressed: _saving ? null : _save,
            child: _saving ? const SizedBox(width: 22, height: 22, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5)) : Text(widget.category != null ? 'Update Category' : 'Add Category', style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w700)),
          )),
        ],
      ),
    );
  }
}
