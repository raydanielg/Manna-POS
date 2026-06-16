import 'package:flutter/material.dart';
import '../../core/api_service.dart';
import '../../shared/theme/app_colors.dart';
import '../../shared/widgets/app_card.dart';
import '../../shared/widgets/empty_state.dart';

class UnitsPage extends StatefulWidget {
  const UnitsPage({super.key});
  @override State<UnitsPage> createState() => _UnitsPageState();
}

class _UnitsPageState extends State<UnitsPage> {
  List<dynamic> _units = [];
  bool _loading = true;
  String? _error;

  @override void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    setState(() { _loading = true; _error = null; });
    try {
      final data = await ApiService.get('/units');
      setState(() { _units = (data as List); _loading = false; });
    } on ApiException catch (e) { setState(() { _error = e.message; _loading = false; }); }
    catch (_) { setState(() { _error = 'Connection error'; _loading = false; }); }
  }

  void _showForm([dynamic u]) => showModalBottomSheet(context: context, isScrollControlled: true, backgroundColor: Colors.transparent, builder: (_) => _UnitForm(unit: u, onSaved: _load));

  Future<void> _delete(dynamic u) async {
    final ok = await showDialog<bool>(context: context, builder: (_) => AlertDialog(
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
      title: const Text('Delete Unit'),
      content: Text('Delete "${u['name']}"?'),
      actions: [TextButton(onPressed: () => Navigator.pop(context, false), child: const Text('Cancel')),
        ElevatedButton(style: ElevatedButton.styleFrom(backgroundColor: AppColors.danger), onPressed: () => Navigator.pop(context, true), child: const Text('Delete'))],
    ));
    if (ok != true) return;
    try { await ApiService.delete('/units/${u['id']}'); _load(); } on ApiException catch (e) { _snack(e.message, error: true); }
  }

  void _snack(String msg, {bool error = false}) => ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(msg), backgroundColor: error ? AppColors.danger : AppColors.success, behavior: SnackBarBehavior.floating, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10))));

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(title: const Text('Units'), actions: [IconButton(icon: const Icon(Icons.refresh, color: Colors.white), onPressed: _load)]),
      body: _loading ? const LoadingWidget(message: 'Loading...')
        : _error != null ? ErrorWidget2(message: _error!, onRetry: _load)
        : _units.isEmpty ? const EmptyState(icon: Icons.straighten_outlined, title: 'No Units', subtitle: 'Add your first unit')
        : RefreshIndicator(color: AppColors.primary, onRefresh: _load,
            child: ListView.separated(padding: const EdgeInsets.fromLTRB(16, 0, 16, 100), itemCount: _units.length, separatorBuilder: (_, __) => const SizedBox(height: 10), itemBuilder: (_, i) => _tile(_units[i]))),
      floatingActionButton: FloatingActionButton.extended(onPressed: () => _showForm(), icon: const Icon(Icons.add), label: const Text('Add Unit')),
    );
  }

  Widget _tile(dynamic u) => AppCard(child: Padding(padding: const EdgeInsets.all(16), child: Row(children: [
    Container(width: 46, height: 46, decoration: BoxDecoration(color: AppColors.cyan.withValues(alpha: 0.12), borderRadius: BorderRadius.circular(12)),
      child: const Icon(Icons.straighten_outlined, color: AppColors.cyan, size: 22)),
    const SizedBox(width: 14),
    Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
      Text(u['name'] ?? '', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 15)),
      if (u['short_name'] != null) Text('Short: ${u['short_name']}', style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
    ])),
    Column(children: [
      if (u['allow_decimal'] == true)
        Container(padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3), decoration: BoxDecoration(color: AppColors.primaryLt, borderRadius: BorderRadius.circular(20)),
          child: const Text('Decimal', style: TextStyle(color: AppColors.primary, fontSize: 10, fontWeight: FontWeight.w700))),
      const SizedBox(height: 6),
      PopupMenuButton<String>(shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)), onSelected: (v) { if (v == 'edit') _showForm(u); else _delete(u); },
        itemBuilder: (_) => [const PopupMenuItem(value: 'edit', child: Row(children: [Icon(Icons.edit_outlined, size: 18, color: AppColors.primary), SizedBox(width: 8), Text('Edit')])), const PopupMenuItem(value: 'delete', child: Row(children: [Icon(Icons.delete_outline, size: 18, color: AppColors.danger), SizedBox(width: 8), Text('Delete', style: TextStyle(color: AppColors.danger))]))]),
    ]),
  ])));
}

class _UnitForm extends StatefulWidget {
  final dynamic unit;
  final VoidCallback onSaved;
  const _UnitForm({this.unit, required this.onSaved});
  @override State<_UnitForm> createState() => _UnitFormState();
}

class _UnitFormState extends State<_UnitForm> {
  final _form = GlobalKey<FormState>();
  late TextEditingController _name, _short;
  bool _saving = false;
  bool _allowDecimal = false;
  String? _err;

  @override
  void initState() {
    super.initState();
    final u = widget.unit;
    _name = TextEditingController(text: u?['name']);
    _short = TextEditingController(text: u?['short_name']);
    if (u != null) _allowDecimal = u['allow_decimal'] == true;
  }

  @override void dispose() { _name.dispose(); _short.dispose(); super.dispose(); }

  Future<void> _save() async {
    if (!_form.currentState!.validate()) return;
    setState(() { _saving = true; _err = null; });
    final body = {'name': _name.text.trim(), if (_short.text.isNotEmpty) 'short_name': _short.text.trim(), 'allow_decimal': _allowDecimal};
    try {
      if (widget.unit != null) await ApiService.put('/units/${widget.unit!['id']}', body);
      else await ApiService.post('/units', body);
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
        Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [Text(widget.unit != null ? 'Edit Unit' : 'Add Unit', style: const TextStyle(fontSize: 20, fontWeight: FontWeight.w700)), IconButton(icon: const Icon(Icons.close), onPressed: () => Navigator.pop(context))]),
        if (_err != null) ...[const SizedBox(height: 12), Container(padding: const EdgeInsets.all(12), decoration: BoxDecoration(color: AppColors.dangerLt, borderRadius: BorderRadius.circular(10)), child: Text(_err!, style: const TextStyle(color: AppColors.danger)))],
        const SizedBox(height: 16),
        TextFormField(controller: _name, decoration: const InputDecoration(labelText: 'Unit Name *'), validator: (v) => v!.isNotEmpty ? null : 'Required'),
        const SizedBox(height: 12),
        Row(children: [
          Expanded(child: TextFormField(controller: _short, decoration: const InputDecoration(labelText: 'Short Name'))),
          const SizedBox(width: 12),
          FilterChip(label: const Text('Allow Decimal'), selected: _allowDecimal, onSelected: (v) => setState(() => _allowDecimal = v)),
        ]),
        const SizedBox(height: 24),
        SizedBox(height: 52, child: ElevatedButton(onPressed: _saving ? null : _save, child: _saving ? const SizedBox(width: 22, height: 22, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5)) : Text(widget.unit != null ? 'Update Unit' : 'Add Unit', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700)))),
      ]))),
    );
  }
}
