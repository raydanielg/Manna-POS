import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/product_provider.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/empty_state.dart';
import '../../widgets/shimmer_loading.dart';
import '../../widgets/confirm_dialog.dart';
import '../../widgets/toast_helper.dart';
import '../../widgets/app_bottom_sheet.dart';

class BrandsScreen extends StatefulWidget {
  const BrandsScreen({super.key});
  @override State<BrandsScreen> createState() => _BrandsScreenState();
}

class _BrandsScreenState extends State<BrandsScreen> {
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

  void _showForm([Map<String, dynamic>? brand]) {
    AppBottomSheet.show(
      context: context,
      title: brand != null ? 'Edit Brand' : 'Add Brand',
      child: _BrandForm(brand: brand, onSaved: _refresh),
    );
  }

  Future<void> _delete(Map<String, dynamic> b) async {
    final confirmed = await ConfirmDialog.show(context, title: 'Delete Brand', message: 'Delete "${b['name']}"?');
    if (confirmed != true) return;
    try {
      await context.read<ProductProvider>().deleteBrand(b['id']);
      if (mounted) ToastHelper.showSuccess(context, 'Brand deleted');
      _refresh();
    } catch (e) {
      if (mounted) ToastHelper.showError(context, e.toString());
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Theme.of(context).scaffoldBackgroundColor,
      appBar: AppBar(title: const Text('Brands'), actions: [IconButton(icon: const Icon(Icons.refresh), onPressed: _refresh)]),
      body: Consumer<ProductProvider>(
        builder: (context, provider, _) {
          if (provider.isLoading) return const ShimmerLoading();
          if (provider.brands.isEmpty) {
            return const EmptyState(icon: Icons.bookmark_outline, title: 'No Brands', subtitle: 'Add your first brand', actionLabel: 'Add Brand');
          }
          return RefreshIndicator(
            onRefresh: _refresh,
            child: ListView.separated(
              padding: const EdgeInsets.fromLTRB(16, 8, 16, 100),
              itemCount: provider.brands.length,
              separatorBuilder: (_, __) => const SizedBox(height: 8),
              itemBuilder: (_, i) {
                final b = provider.brands[i];
                return Dismissible(
                  key: ValueKey(b.id),
                  direction: DismissDirection.endToStart,
                  background: Container(alignment: Alignment.centerRight, padding: const EdgeInsets.only(right: 20), decoration: BoxDecoration(color: AppColors.danger, borderRadius: BorderRadius.circular(14)), child: const Icon(Icons.delete, color: Colors.white)),
                  confirmDismiss: (_) async { await _delete(b.toJson()); return false; },
                  child: GlassCard(
                    onTap: () => _showForm(b.toJson()),
                    child: ListTile(
                      leading: Container(
                        width: 44, height: 44,
                        decoration: BoxDecoration(color: AppColors.accent.withValues(alpha: 0.12), borderRadius: BorderRadius.circular(12)),
                        child: Icon(Icons.bookmark_outline, color: AppColors.accent, size: 22),
                      ),
                      title: Text(b.name ?? '', style: const TextStyle(fontWeight: FontWeight.w600)),
                      subtitle: b.description != null ? Text(b.description!, style: const TextStyle(fontSize: 11), maxLines: 1, overflow: TextOverflow.ellipsis) : null,
                      trailing: Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          Text('${b.productCount ?? 0} products', style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
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
      floatingActionButton: FloatingActionButton.extended(onPressed: () => _showForm(), icon: const Icon(Icons.add), label: const Text('Add Brand')),
    );
  }
}

class _BrandForm extends StatefulWidget {
  final Map<String, dynamic>? brand;
  final VoidCallback onSaved;
  const _BrandForm({this.brand, required this.onSaved});
  @override State<_BrandForm> createState() => _BrandFormState();
}

class _BrandFormState extends State<_BrandForm> {
  final _form = GlobalKey<FormState>();
  late TextEditingController _nameCtrl, _descCtrl;
  bool _saving = false;

  @override
  void initState() {
    super.initState();
    final b = widget.brand;
    _nameCtrl = TextEditingController(text: b?['name']);
    _descCtrl = TextEditingController(text: b?['description']);
  }

  @override
  void dispose() { _nameCtrl.dispose(); _descCtrl.dispose(); super.dispose(); }

  Future<void> _save() async {
    if (!_form.currentState!.validate()) return;
    setState(() => _saving = true);
    final body = {'name': _nameCtrl.text.trim(), if (_descCtrl.text.trim().isNotEmpty) 'description': _descCtrl.text.trim()};
    try {
      if (widget.brand != null) {
        await context.read<ProductProvider>().updateBrand(widget.brand!['id'], body);
      } else {
        await context.read<ProductProvider>().createBrand(body);
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
          TextFormField(controller: _nameCtrl, decoration: const InputDecoration(labelText: 'Brand Name *'), validator: (v) => v!.trim().isEmpty ? 'Required' : null),
          const SizedBox(height: 12),
          TextFormField(controller: _descCtrl, maxLines: 2, decoration: const InputDecoration(labelText: 'Description')),
          const SizedBox(height: 24),
          SizedBox(height: 52, child: ElevatedButton(
            onPressed: _saving ? null : _save,
            child: _saving ? const SizedBox(width: 22, height: 22, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5)) : Text(widget.brand != null ? 'Update Brand' : 'Add Brand', style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w700)),
          )),
        ],
      ),
    );
  }
}
