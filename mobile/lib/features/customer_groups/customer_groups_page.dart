import 'package:flutter/material.dart';
import '../../core/api_service.dart';
import '../../shared/theme/app_colors.dart';
import '../../shared/widgets/app_card.dart';
import '../../shared/widgets/empty_state.dart';

class CustomerGroupsPage extends StatefulWidget {
  const CustomerGroupsPage({super.key});
  @override State<CustomerGroupsPage> createState() => _CustomerGroupsPageState();
}

class _CustomerGroupsPageState extends State<CustomerGroupsPage> {
  List<dynamic> _groups = [];
  bool _loading = true;
  String? _error;

  @override void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    setState(() { _loading = true; _error = null; });
    try {
      final data = await ApiService.get('/customer-groups');
      setState(() { _groups = (data as List); _loading = false; });
    } on ApiException catch (e) { setState(() { _error = e.message; _loading = false; }); }
    catch (_) { setState(() { _error = 'Connection error'; _loading = false; }); }
  }

  void _showForm([dynamic g]) => showModalBottomSheet(context: context, isScrollControlled: true, backgroundColor: Colors.transparent, builder: (_) => _GroupForm(group: g, onSaved: _load));

  Future<void> _delete(dynamic g) async {
    final ok = await showDialog<bool>(context: context, builder: (_) => AlertDialog(
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
      title: const Text('Delete Group'),
      content: Text('Delete "${g['name']}"?'),
      actions: [TextButton(onPressed: () => Navigator.pop(context, false), child: const Text('Cancel')),
        ElevatedButton(style: ElevatedButton.styleFrom(backgroundColor: AppColors.danger), onPressed: () => Navigator.pop(context, true), child: const Text('Delete'))],
    ));
    if (ok != true) return;
    try { await ApiService.delete('/customer-groups/${g['id']}'); _load(); } on ApiException catch (e) { _snack(e.message, error: true); }
  }

  void _snack(String msg, {bool error = false}) => ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(msg), backgroundColor: error ? AppColors.danger : AppColors.success, behavior: SnackBarBehavior.floating, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10))));

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(title: const Text('Customer Groups'), actions: [IconButton(icon: const Icon(Icons.refresh, color: Colors.white), onPressed: _load)]),
      body: _loading ? const LoadingWidget(message: 'Loading...')
        : _error != null ? ErrorWidget2(message: _error!, onRetry: _load)
        : _groups.isEmpty ? const EmptyState(icon: Icons.group_outlined, title: 'No Groups', subtitle: 'Add your first customer group')
        : RefreshIndicator(color: AppColors.primary, onRefresh: _load,
            child: ListView.separated(padding: const EdgeInsets.fromLTRB(16, 0, 16, 100), itemCount: _groups.length, separatorBuilder: (_, __) => const SizedBox(height: 10), itemBuilder: (_, i) => _tile(_groups[i]))),
      floatingActionButton: FloatingActionButton.extended(onPressed: () => _showForm(), icon: const Icon(Icons.add), label: const Text('Add Group')),
    );
  }

  Widget _tile(dynamic g) => AppCard(child: Padding(padding: const EdgeInsets.all(16), child: Row(children: [
    Container(width: 46, height: 46, decoration: BoxDecoration(color: AppColors.primaryLt, borderRadius: BorderRadius.circular(12)),
      child: const Icon(Icons.group_outlined, color: AppColors.primary, size: 22)),
    const SizedBox(width: 14),
    Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
      Text(g['name'] ?? '', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 15)),
      Row(children: [
        if (g['discount'] != null) Text('${g['discount']}% off', style: const TextStyle(color: AppColors.warning, fontSize: 12, fontWeight: FontWeight.w600)),
        const SizedBox(width: 8),
        if (g['customers_count'] != null) Text('${g['customers_count']} members', style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
      ]),
    ])),
    PopupMenuButton<String>(shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)), onSelected: (v) { if (v == 'edit') _showForm(g); else _delete(g); },
      itemBuilder: (_) => [const PopupMenuItem(value: 'edit', child: Row(children: [Icon(Icons.edit_outlined, size: 18, color: AppColors.primary), SizedBox(width: 8), Text('Edit')])), const PopupMenuItem(value: 'delete', child: Row(children: [Icon(Icons.delete_outline, size: 18, color: AppColors.danger), SizedBox(width: 8), Text('Delete', style: TextStyle(color: AppColors.danger))]))]),
  ])));
}

class _GroupForm extends StatefulWidget {
  final dynamic group;
  final VoidCallback onSaved;
  const _GroupForm({this.group, required this.onSaved});
  @override State<_GroupForm> createState() => _GroupFormState();
}

class _GroupFormState extends State<_GroupForm> {
  final _form = GlobalKey<FormState>();
  late TextEditingController _name, _discount, _desc;
  bool _saving = false;
  String? _err;

  @override void initState() { super.initState(); final g = widget.group; _name = TextEditingController(text: g?['name']); _discount = TextEditingController(text: g?['discount']?.toString()); _desc = TextEditingController(text: g?['description']); }
  @override void dispose() { _name.dispose(); _discount.dispose(); _desc.dispose(); super.dispose(); }

  Future<void> _save() async {
    if (!_form.currentState!.validate()) return;
    setState(() { _saving = true; _err = null; });
    final body = {'name': _name.text.trim(), if (_discount.text.isNotEmpty) 'discount': double.tryParse(_discount.text) ?? 0, if (_desc.text.isNotEmpty) 'description': _desc.text.trim()};
    try {
      if (widget.group != null) await ApiService.put('/customer-groups/${widget.group!['id']}', body);
      else await ApiService.post('/customer-groups', body);
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
        Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [Text(widget.group != null ? 'Edit Group' : 'Add Group', style: const TextStyle(fontSize: 20, fontWeight: FontWeight.w700)), IconButton(icon: const Icon(Icons.close), onPressed: () => Navigator.pop(context))]),
        if (_err != null) ...[const SizedBox(height: 12), Container(padding: const EdgeInsets.all(12), decoration: BoxDecoration(color: AppColors.dangerLt, borderRadius: BorderRadius.circular(10)), child: Text(_err!, style: const TextStyle(color: AppColors.danger)))],
        const SizedBox(height: 16),
        TextFormField(controller: _name, decoration: const InputDecoration(labelText: 'Group Name *'), validator: (v) => v!.isNotEmpty ? null : 'Required'),
        const SizedBox(height: 12),
        TextFormField(controller: _discount, keyboardType: TextInputType.number, decoration: const InputDecoration(labelText: 'Discount (%)', suffixText: '%')),
        const SizedBox(height: 12),
        TextFormField(controller: _desc, maxLines: 2, decoration: const InputDecoration(labelText: 'Description')),
        const SizedBox(height: 24),
        SizedBox(height: 52, child: ElevatedButton(onPressed: _saving ? null : _save, child: _saving ? const SizedBox(width: 22, height: 22, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5)) : Text(widget.group != null ? 'Update Group' : 'Add Group', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700)))),
      ]))),
    );
  }
}
