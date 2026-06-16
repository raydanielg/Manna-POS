import 'package:flutter/material.dart';
import '../../core/api_service.dart';
import '../../shared/theme/app_colors.dart';
import '../../shared/widgets/app_card.dart';
import '../../shared/widgets/empty_state.dart';
import '../../shared/widgets/status_badge.dart';

class BrandsPage extends StatefulWidget {
  const BrandsPage({super.key});
  @override State<BrandsPage> createState() => _BrandsPageState();
}

class _BrandsPageState extends State<BrandsPage> {
  List<dynamic> _brands = [];
  bool _loading = true;
  String? _error;

  @override void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    setState(() { _loading = true; _error = null; });
    try {
      final data = await ApiService.get('/brands');
      setState(() { _brands = (data as List); _loading = false; });
    } on ApiException catch (e) { setState(() { _error = e.message; _loading = false; }); }
    catch (_) { setState(() { _error = 'Connection error'; _loading = false; }); }
  }

  void _showForm([dynamic b]) => showModalBottomSheet(context: context, isScrollControlled: true, backgroundColor: Colors.transparent, builder: (_) => _BrandForm(brand: b, onSaved: _load));

  Future<void> _delete(dynamic b) async {
    final ok = await showDialog<bool>(context: context, builder: (_) => AlertDialog(
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
      title: const Text('Delete Brand'),
      content: Text('Delete "${b['name']}"?'),
      actions: [TextButton(onPressed: () => Navigator.pop(context, false), child: const Text('Cancel')),
        ElevatedButton(style: ElevatedButton.styleFrom(backgroundColor: AppColors.danger), onPressed: () => Navigator.pop(context, true), child: const Text('Delete'))],
    ));
    if (ok != true) return;
    try { await ApiService.delete('/brands/${b['id']}'); _load(); } on ApiException catch (e) { _snack(e.message, error: true); }
  }

  void _snack(String msg, {bool error = false}) => ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(msg), backgroundColor: error ? AppColors.danger : AppColors.success, behavior: SnackBarBehavior.floating, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10))));

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(title: const Text('Brands'), actions: [IconButton(icon: const Icon(Icons.refresh, color: Colors.white), onPressed: _load)]),
      body: _loading ? const LoadingWidget(message: 'Loading...')
        : _error != null ? ErrorWidget2(message: _error!, onRetry: _load)
        : _brands.isEmpty ? const EmptyState(icon: Icons.bookmark_outline, title: 'No Brands', subtitle: 'Add your first brand')
        : RefreshIndicator(color: AppColors.primary, onRefresh: _load,
            child: ListView.separated(padding: const EdgeInsets.fromLTRB(16, 0, 16, 100), itemCount: _brands.length, separatorBuilder: (_, __) => const SizedBox(height: 10), itemBuilder: (_, i) => _tile(_brands[i]))),
      floatingActionButton: FloatingActionButton.extended(onPressed: () => _showForm(), icon: const Icon(Icons.add), label: const Text('Add Brand')),
    );
  }

  Widget _tile(dynamic b) => AppCard(child: Padding(padding: const EdgeInsets.all(16), child: Row(children: [
    Container(width: 46, height: 46, decoration: BoxDecoration(color: AppColors.accent.withValues(alpha: 0.12), borderRadius: BorderRadius.circular(12)),
      child: Icon(Icons.bookmark_outline, color: AppColors.accent, size: 22)),
    const SizedBox(width: 14),
    Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
      Text(b['name'] ?? '', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 15)),
      if (b['description'] != null) Text(b['description'], style: const TextStyle(color: AppColors.textSec, fontSize: 12), maxLines: 1, overflow: TextOverflow.ellipsis),
    ])),
    Column(crossAxisAlignment: CrossAxisAlignment.end, children: [
      StatusBadge.fromStatus(b['status'] ?? 'active'),
      const SizedBox(height: 6),
      PopupMenuButton<String>(shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)), onSelected: (v) { if (v == 'edit') _showForm(b); else _delete(b); },
        itemBuilder: (_) => [const PopupMenuItem(value: 'edit', child: Row(children: [Icon(Icons.edit_outlined, size: 18, color: AppColors.primary), SizedBox(width: 8), Text('Edit')])), const PopupMenuItem(value: 'delete', child: Row(children: [Icon(Icons.delete_outline, size: 18, color: AppColors.danger), SizedBox(width: 8), Text('Delete', style: TextStyle(color: AppColors.danger))]))]),
    ]),
  ])));
}

class _BrandForm extends StatefulWidget {
  final dynamic brand;
  final VoidCallback onSaved;
  const _BrandForm({this.brand, required this.onSaved});
  @override State<_BrandForm> createState() => _BrandFormState();
}

class _BrandFormState extends State<_BrandForm> {
  final _form = GlobalKey<FormState>();
  late TextEditingController _name, _desc;
  bool _saving = false;
  String? _err;

  @override
  void initState() {
    super.initState();
    final b = widget.brand;
    _name = TextEditingController(text: b?['name']);
    _desc = TextEditingController(text: b?['description']);
  }

  @override void dispose() { _name.dispose(); _desc.dispose(); super.dispose(); }

  Future<void> _save() async {
    if (!_form.currentState!.validate()) return;
    setState(() { _saving = true; _err = null; });
    final body = {'name': _name.text.trim(), if (_desc.text.isNotEmpty) 'description': _desc.text.trim()};
    try {
      if (widget.brand != null) await ApiService.put('/brands/${widget.brand!['id']}', body);
      else await ApiService.post('/brands', body);
      widget.onSaved();
      if (mounted) Navigator.pop(context);
    } on ApiException catch (e) { setState(() { _err = e.message; _saving = false; }); }
    catch (_) { setState(() { _err = 'Save failed'; _saving = false; }); }
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: const BoxDecoration(color: AppColors.surface, borderRadius: BorderRadius.vertical(top: Radius.circular(24))),
      padding: EdgeInsets.only(bottom: MediaQuery.of(context).viewInsets.bottom),
      child: SingleChildScrollView(padding: const EdgeInsets.fromLTRB(24, 16, 24, 32), child: Form(key: _form, child: Column(crossAxisAlignment: CrossAxisAlignment.stretch, children: [
        Center(child: Container(width: 40, height: 4, decoration: BoxDecoration(color: AppColors.border, borderRadius: BorderRadius.circular(4)))),
        const SizedBox(height: 20),
        Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [Text(widget.brand != null ? 'Edit Brand' : 'Add Brand', style: const TextStyle(fontSize: 20, fontWeight: FontWeight.w700)), IconButton(icon: const Icon(Icons.close), onPressed: () => Navigator.pop(context))]),
        if (_err != null) ...[const SizedBox(height: 12), Container(padding: const EdgeInsets.all(12), decoration: BoxDecoration(color: AppColors.dangerLt, borderRadius: BorderRadius.circular(10)), child: Text(_err!, style: const TextStyle(color: AppColors.danger)))],
        const SizedBox(height: 16),
        TextFormField(controller: _name, decoration: const InputDecoration(labelText: 'Brand Name *'), validator: (v) => v!.isNotEmpty ? null : 'Required'),
        const SizedBox(height: 12),
        TextFormField(controller: _desc, maxLines: 2, decoration: const InputDecoration(labelText: 'Description')),
        const SizedBox(height: 24),
        SizedBox(height: 52, child: ElevatedButton(onPressed: _saving ? null : _save, child: _saving ? const SizedBox(width: 22, height: 22, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5)) : Text(widget.brand != null ? 'Update Brand' : 'Add Brand', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700)))),
      ]))),
    );
  }
}
