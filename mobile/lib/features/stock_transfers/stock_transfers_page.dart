import 'package:flutter/material.dart';
import '../../core/api_service.dart';
import '../../shared/theme/app_colors.dart';
import '../../shared/widgets/app_card.dart';
import '../../shared/widgets/empty_state.dart';
import '../../shared/widgets/status_badge.dart';
class StockTransfersPage extends StatefulWidget {
  const StockTransfersPage({super.key});
  @override State<StockTransfersPage> createState() => _StockTransfersPageState();
}

class _StockTransfersPageState extends State<StockTransfersPage> {
  List<dynamic> _transfers = [];
  bool _loading = true;
  String? _error;

  @override void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    setState(() { _loading = true; _error = null; });
    try {
      final data = await ApiService.get('/stock-transfers');
      setState(() { _transfers = (data as List); _loading = false; });
    } on ApiException catch (e) { setState(() { _error = e.message; _loading = false; }); }
    catch (_) { setState(() { _error = 'Connection error'; _loading = false; }); }
  }

  void _showForm() => showModalBottomSheet(context: context, isScrollControlled: true, backgroundColor: Colors.transparent, builder: (_) => _TransferForm(onSaved: _load));

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(title: const Text('Stock Transfers'), actions: [IconButton(icon: const Icon(Icons.refresh, color: Colors.white), onPressed: _load)]),
      body: _loading ? const LoadingWidget(message: 'Loading...')
        : _error != null ? ErrorWidget2(message: _error!, onRetry: _load)
        : _transfers.isEmpty ? const EmptyState(icon: Icons.swap_horiz_outlined, title: 'No Transfers', subtitle: 'Stock transfers will appear here')
        : RefreshIndicator(color: AppColors.primary, onRefresh: _load,
            child: ListView.separated(padding: const EdgeInsets.fromLTRB(16, 0, 16, 100), itemCount: _transfers.length, separatorBuilder: (_, __) => const SizedBox(height: 10), itemBuilder: (_, i) => _tile(_transfers[i]))),
      floatingActionButton: FloatingActionButton.extended(onPressed: _showForm, icon: const Icon(Icons.add), label: const Text('New Transfer')),
    );
  }

  Widget _tile(dynamic t) => AppCard(child: Padding(padding: const EdgeInsets.all(16), child: Column(children: [
    Row(children: [
      Container(width: 46, height: 46, decoration: BoxDecoration(color: AppColors.primaryLt, borderRadius: BorderRadius.circular(12)),
        child: const Icon(Icons.swap_horiz_outlined, color: AppColors.primary, size: 22)),
      const SizedBox(width: 14),
      Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
        Text(t['reference'] ?? '', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14)),
        const SizedBox(height: 3),
        Text('${t['from_location'] ?? ''} → ${t['to_location'] ?? ''}', style: const TextStyle(color: AppColors.textSec, fontSize: 13)),
      ])),
      StatusBadge.fromStatus(t['status'] ?? ''),
    ]),
    const SizedBox(height: 8),
    Row(children: [
      Icon(Icons.calendar_today_outlined, size: 12, color: AppColors.textSec),
      const SizedBox(width: 4),
      Text(_fmtDate(t['transfer_date']), style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
      const Spacer(),
      if (t['notes'] != null) Text(t['notes'], style: const TextStyle(color: AppColors.textSec, fontSize: 11)),
    ]),
  ])));

  String _fmtDate(String? d) {
    if (d == null) return '';
    try { return '${DateTime.parse(d).day}/${DateTime.parse(d).month}/${DateTime.parse(d).year}'; } catch (_) { return d; }
  }
}

class _TransferForm extends StatefulWidget {
  final VoidCallback onSaved;
  const _TransferForm({required this.onSaved});
  @override State<_TransferForm> createState() => _TransferFormState();
}

class _TransferFormState extends State<_TransferForm> {
  final _form = GlobalKey<FormState>();
  final _fromCtrl = TextEditingController();
  final _toCtrl = TextEditingController();
  final _notesCtrl = TextEditingController();
  final _dateCtrl = TextEditingController(text: DateTime.now().toIso8601String().split('T')[0]);
  bool _saving = false;
  String? _err;
  String _status = 'draft';

  @override void dispose() { _fromCtrl.dispose(); _toCtrl.dispose(); _notesCtrl.dispose(); _dateCtrl.dispose(); super.dispose(); }

  Future<void> _save() async {
    if (!_form.currentState!.validate()) return;
    setState(() { _saving = true; _err = null; });
    try {
      await ApiService.post('/stock-transfers', {
        'from_location': _fromCtrl.text.trim(),
        'to_location': _toCtrl.text.trim(),
        'transfer_date': _dateCtrl.text.trim(),
        'status': _status,
        'notes': _notesCtrl.text.trim(),
      });
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
        Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [const Text('New Transfer', style: TextStyle(fontSize: 20, fontWeight: FontWeight.w700)), IconButton(icon: const Icon(Icons.close), onPressed: () => Navigator.pop(context))]),
        if (_err != null) ...[const SizedBox(height: 12), Container(padding: const EdgeInsets.all(12), decoration: BoxDecoration(color: AppColors.dangerLt, borderRadius: BorderRadius.circular(10)), child: Text(_err!, style: const TextStyle(color: AppColors.danger)))],
        const SizedBox(height: 16),
        TextFormField(controller: _fromCtrl, decoration: const InputDecoration(labelText: 'From Location *'), validator: (v) => v!.isNotEmpty ? null : 'Required'),
        const SizedBox(height: 12),
        TextFormField(controller: _toCtrl, decoration: const InputDecoration(labelText: 'To Location *'), validator: (v) => v!.isNotEmpty ? null : 'Required'),
        const SizedBox(height: 12),
        TextFormField(controller: _dateCtrl, decoration: const InputDecoration(labelText: 'Transfer Date', prefixIcon: Icon(Icons.calendar_today_outlined, size: 18))),
        const SizedBox(height: 12),
        DropdownButtonFormField<String>(decoration: const InputDecoration(labelText: 'Status'), value: _status, items: ['draft','completed','cancelled'].map((s) => DropdownMenuItem(value: s, child: Text(s))).toList(), onChanged: (v) => _status = v!),
        const SizedBox(height: 12),
        TextFormField(controller: _notesCtrl, maxLines: 2, decoration: const InputDecoration(labelText: 'Notes')),
        const SizedBox(height: 24),
        SizedBox(height: 52, child: ElevatedButton(onPressed: _saving ? null : _save, child: _saving ? const SizedBox(width: 22, height: 22, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5)) : const Text('Create Transfer', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700)))),
      ]))),
    );
  }
}
