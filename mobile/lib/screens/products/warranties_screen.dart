import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/product_provider.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/empty_state.dart';
import '../../widgets/shimmer_loading.dart';
import '../../widgets/confirm_dialog.dart';
import '../../widgets/toast_helper.dart';
import '../../widgets/app_bottom_sheet.dart';

class WarrantiesScreen extends StatefulWidget {
  const WarrantiesScreen({super.key});
  @override State<WarrantiesScreen> createState() => _WarrantiesScreenState();
}

class _WarrantiesScreenState extends State<WarrantiesScreen> {
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

  void _showForm([Map<String, dynamic>? warranty]) {
    AppBottomSheet.show(
      context: context,
      title: warranty != null ? 'Edit Warranty' : 'Add Warranty',
      child: _WarrantyForm(warranty: warranty, onSaved: _refresh),
    );
  }

  Future<void> _delete(Map<String, dynamic> w) async {
    final confirmed = await ConfirmDialog.show(context, title: 'Delete Warranty', message: 'Delete "${w['name']}"?');
    if (confirmed != true) return;
    try {
      await context.read<ProductProvider>().deleteWarranty(w['id']);
      if (mounted) ToastHelper.showSuccess(context, 'Warranty deleted');
      _refresh();
    } catch (e) {
      if (mounted) ToastHelper.showError(context, e.toString());
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Theme.of(context).scaffoldBackgroundColor,
      appBar: AppBar(title: const Text('Warranties'), actions: [IconButton(icon: const Icon(Icons.refresh), onPressed: _refresh)]),
      body: Consumer<ProductProvider>(
        builder: (context, provider, _) {
          if (provider.isLoading) return const ShimmerLoading();
          if (provider.warranties.isEmpty) {
            return const EmptyState(icon: Icons.verified_outlined, title: 'No Warranties', subtitle: 'Add your first warranty', actionLabel: 'Add Warranty');
          }
          return RefreshIndicator(
            onRefresh: _refresh,
            child: ListView.separated(
              padding: const EdgeInsets.fromLTRB(16, 8, 16, 100),
              itemCount: provider.warranties.length,
              separatorBuilder: (_, __) => const SizedBox(height: 8),
              itemBuilder: (_, i) {
                final w = provider.warranties[i];
                return Dismissible(
                  key: ValueKey(w.id),
                  direction: DismissDirection.endToStart,
                  background: Container(alignment: Alignment.centerRight, padding: const EdgeInsets.only(right: 20), decoration: BoxDecoration(color: AppColors.danger, borderRadius: BorderRadius.circular(14)), child: const Icon(Icons.delete, color: Colors.white)),
                  confirmDismiss: (_) async { await _delete(w.toJson()); return false; },
                  child: GlassCard(
                    onTap: () => _showForm(w.toJson()),
                    child: ListTile(
                      leading: Container(
                        width: 44, height: 44,
                        decoration: BoxDecoration(color: AppColors.purple.withValues(alpha: 0.12), borderRadius: BorderRadius.circular(12)),
                        child: const Icon(Icons.verified_outlined, color: AppColors.purple, size: 22),
                      ),
                      title: Text(w.name ?? '', style: const TextStyle(fontWeight: FontWeight.w600)),
                      subtitle: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          if (w.duration != null) Text('${w.duration} ${w.durationUnit ?? ''}', style: const TextStyle(fontSize: 12)),
                          if (w.description != null) Text(w.description!, style: const TextStyle(fontSize: 11), maxLines: 1, overflow: TextOverflow.ellipsis),
                        ],
                      ),
                      trailing: const Icon(Icons.chevron_right, color: AppColors.textLight, size: 20),
                    ),
                  ),
                );
              },
            ),
          );
        },
      ),
      floatingActionButton: FloatingActionButton.extended(onPressed: () => _showForm(), icon: const Icon(Icons.add), label: const Text('Add Warranty')),
    );
  }
}

class _WarrantyForm extends StatefulWidget {
  final Map<String, dynamic>? warranty;
  final VoidCallback onSaved;
  const _WarrantyForm({this.warranty, required this.onSaved});
  @override State<_WarrantyForm> createState() => _WarrantyFormState();
}

class _WarrantyFormState extends State<_WarrantyForm> {
  final _form = GlobalKey<FormState>();
  late TextEditingController _nameCtrl, _durationCtrl, _descCtrl;
  bool _saving = false;
  String _durationUnit = 'months';

  @override
  void initState() {
    super.initState();
    final w = widget.warranty;
    _nameCtrl = TextEditingController(text: w?['name']);
    _durationCtrl = TextEditingController(text: w?['duration']?.toString());
    _descCtrl = TextEditingController(text: w?['description']);
    if (w != null) _durationUnit = w['duration_unit'] ?? 'months';
  }

  @override
  void dispose() { _nameCtrl.dispose(); _durationCtrl.dispose(); _descCtrl.dispose(); super.dispose(); }

  Future<void> _save() async {
    if (!_form.currentState!.validate()) return;
    setState(() => _saving = true);
    final body = {
      'name': _nameCtrl.text.trim(),
      'duration': int.tryParse(_durationCtrl.text) ?? 1,
      'duration_unit': _durationUnit,
      if (_descCtrl.text.trim().isNotEmpty) 'description': _descCtrl.text.trim(),
    };
    try {
      if (widget.warranty != null) {
        await context.read<ProductProvider>().updateWarranty(widget.warranty!['id'], body);
      } else {
        await context.read<ProductProvider>().createWarranty(body);
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
          TextFormField(controller: _nameCtrl, decoration: const InputDecoration(labelText: 'Warranty Name *'), validator: (v) => v!.trim().isEmpty ? 'Required' : null),
          const SizedBox(height: 12),
          Row(children: [
            Expanded(child: TextFormField(controller: _durationCtrl, keyboardType: TextInputType.number, decoration: const InputDecoration(labelText: 'Duration *'), validator: (v) => v!.trim().isEmpty ? 'Required' : null)),
            const SizedBox(width: 12),
            Expanded(child: DropdownButtonFormField<String>(
              decoration: const InputDecoration(labelText: 'Unit'),
              value: _durationUnit,
              items: ['days', 'months', 'years'].map((u) => DropdownMenuItem(value: u, child: Text(u))).toList(),
              onChanged: (v) => setState(() => _durationUnit = v!),
            )),
          ]),
          const SizedBox(height: 12),
          TextFormField(controller: _descCtrl, maxLines: 2, decoration: const InputDecoration(labelText: 'Description')),
          const SizedBox(height: 24),
          SizedBox(height: 52, child: ElevatedButton(
            onPressed: _saving ? null : _save,
            child: _saving ? const SizedBox(width: 22, height: 22, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5)) : Text(widget.warranty != null ? 'Update Warranty' : 'Add Warranty', style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w700)),
          )),
        ],
      ),
    );
  }
}
