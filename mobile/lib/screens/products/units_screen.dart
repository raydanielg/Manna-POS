import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/product_provider.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/empty_state.dart';
import '../../widgets/shimmer_loading.dart';
import '../../widgets/confirm_dialog.dart';
import '../../widgets/toast_helper.dart';
import '../../widgets/app_bottom_sheet.dart';

class UnitsScreen extends StatefulWidget {
  const UnitsScreen({super.key});
  @override State<UnitsScreen> createState() => _UnitsScreenState();
}

class _UnitsScreenState extends State<UnitsScreen> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<ProductProvider>().fetchProducts();
    });
  }

  Future<void> _refresh() async {
    await context.read<ProductProvider>().fetchProducts();
  }

  void _showForm([Map<String, dynamic>? unit]) {
    AppBottomSheet.show(
      context: context,
      title: unit != null ? 'Edit Unit' : 'Add Unit',
      child: _UnitForm(unit: unit, onSaved: _refresh),
    );
  }

  Future<void> _delete(Map<String, dynamic> u) async {
    final confirmed = await ConfirmDialog.show(context, title: 'Delete Unit', message: 'Delete "${u['name']}"?');
    if (confirmed != true) return;
    try {
      await context.read<ProductProvider>().deleteUnit(u['id']);
      if (mounted) ToastHelper.showSuccess(context, 'Unit deleted');
      _refresh();
    } catch (e) {
      if (mounted) ToastHelper.showError(context, e.toString());
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Theme.of(context).scaffoldBackgroundColor,
      appBar: AppBar(title: const Text('Units'), actions: [IconButton(icon: const Icon(Icons.refresh), onPressed: _refresh)]),
      body: Consumer<ProductProvider>(
        builder: (context, provider, _) {
          if (provider.isLoading) return const ShimmerLoading();
          if (provider.units.isEmpty) {
            return const EmptyState(icon: Icons.straighten_outlined, title: 'No Units', subtitle: 'Add your first unit', actionLabel: 'Add Unit');
          }
          return RefreshIndicator(
            onRefresh: _refresh,
            child: ListView.separated(
              padding: const EdgeInsets.fromLTRB(16, 8, 16, 100),
              itemCount: provider.units.length,
              separatorBuilder: (_, __) => const SizedBox(height: 8),
              itemBuilder: (_, i) {
                final u = provider.units[i];
                return Dismissible(
                  key: ValueKey(u.id),
                  direction: DismissDirection.endToStart,
                  background: Container(alignment: Alignment.centerRight, padding: const EdgeInsets.only(right: 20), decoration: BoxDecoration(color: AppColors.danger, borderRadius: BorderRadius.circular(14)), child: const Icon(Icons.delete, color: Colors.white)),
                  confirmDismiss: (_) async { await _delete(u.toJson()); return false; },
                  child: GlassCard(
                    onTap: () => _showForm(u.toJson()),
                    child: ListTile(
                      leading: Container(
                        width: 44, height: 44,
                        decoration: BoxDecoration(color: AppColors.cyan.withValues(alpha: 0.12), borderRadius: BorderRadius.circular(12)),
                        child: const Icon(Icons.straighten_outlined, color: AppColors.cyan, size: 22),
                      ),
                      title: Text(u.name ?? '', style: const TextStyle(fontWeight: FontWeight.w600)),
                      subtitle: u.shortName != null ? Text('Short: ${u.shortName}', style: const TextStyle(fontSize: 12)) : null,
                      trailing: const Icon(Icons.chevron_right, color: AppColors.textLight, size: 20),
                    ),
                  ),
                );
              },
            ),
          );
        },
      ),
      floatingActionButton: FloatingActionButton.extended(onPressed: () => _showForm(), icon: const Icon(Icons.add), label: const Text('Add Unit')),
    );
  }
}

class _UnitForm extends StatefulWidget {
  final Map<String, dynamic>? unit;
  final VoidCallback onSaved;
  const _UnitForm({this.unit, required this.onSaved});
  @override State<_UnitForm> createState() => _UnitFormState();
}

class _UnitFormState extends State<_UnitForm> {
  final _form = GlobalKey<FormState>();
  late TextEditingController _nameCtrl, _shortCtrl;
  bool _saving = false;
  bool _allowDecimal = false;

  @override
  void initState() {
    super.initState();
    final u = widget.unit;
    _nameCtrl = TextEditingController(text: u?['name']);
    _shortCtrl = TextEditingController(text: u?['short_name']);
    if (u != null) _allowDecimal = u['allow_decimal'] == true;
  }

  @override
  void dispose() { _nameCtrl.dispose(); _shortCtrl.dispose(); super.dispose(); }

  Future<void> _save() async {
    if (!_form.currentState!.validate()) return;
    setState(() => _saving = true);
    final body = {
      'name': _nameCtrl.text.trim(),
      'short_name': _shortCtrl.text.trim(),
      'allow_decimal': _allowDecimal,
    };
    try {
      if (widget.unit != null) {
        await context.read<ProductProvider>().updateUnit(widget.unit!['id'], body);
      } else {
        await context.read<ProductProvider>().createUnit(body);
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
          TextFormField(controller: _nameCtrl, decoration: const InputDecoration(labelText: 'Unit Name *'), validator: (v) => v!.trim().isEmpty ? 'Required' : null),
          const SizedBox(height: 12),
          Row(children: [
            Expanded(child: TextFormField(controller: _shortCtrl, decoration: const InputDecoration(labelText: 'Short Name'))),
            const SizedBox(width: 12),
            FilterChip(label: const Text('Allow Decimal'), selected: _allowDecimal, onSelected: (v) => setState(() => _allowDecimal = v)),
          ]),
          const SizedBox(height: 24),
          SizedBox(height: 52, child: ElevatedButton(
            onPressed: _saving ? null : _save,
            child: _saving ? const SizedBox(width: 22, height: 22, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5)) : Text(widget.unit != null ? 'Update Unit' : 'Add Unit', style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w700)),
          )),
        ],
      ),
    );
  }
}
