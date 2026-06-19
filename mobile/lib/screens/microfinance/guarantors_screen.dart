import 'package:flutter/material.dart';
import '../../core/api_service.dart';
import '../../shared/theme/app_theme.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/shimmer_loading.dart';
import '../../widgets/empty_state.dart';
import '../../widgets/search_bar_widget.dart';
import '../../widgets/status_badge.dart';
import '../../widgets/toast_helper.dart';
import '../../widgets/app_bottom_sheet.dart';
import '../../widgets/confirm_dialog.dart';

class GuarantorsScreen extends StatefulWidget {
  const GuarantorsScreen({super.key});
  @override State<GuarantorsScreen> createState() => _GuarantorsScreenState();
}

class _GuarantorsScreenState extends State<GuarantorsScreen> {
  bool _loading = true;
  String? _error;
  List<dynamic> _guarantors = [];
  List<dynamic> _filtered = [];
  final _searchCtrl = TextEditingController();

  @override
  void initState() { super.initState(); _load(); }
  @override void dispose() { _searchCtrl.dispose(); super.dispose(); }

  Future<void> _load() async {
    setState(() { _loading = true; _error = null; });
    try {
      final res = await ApiService.get('/microfinance/guarantors');
      setState(() { _guarantors = res is List ? res : (res['data'] ?? []); _loading = false; _applyFilter(); });
    } on ApiException catch (e) { setState(() { _error = e.message; _loading = false; }); }
    catch (_) { setState(() { _error = 'Connection error'; _loading = false; }); }
  }

  void _applyFilter() {
    final q = _searchCtrl.text.toLowerCase();
    setState(() {
      _filtered = q.isEmpty ? List.from(_guarantors) : _guarantors.where((g) =>
        (g['name']?.toString().toLowerCase() ?? '').contains(q) ||
        (g['phone']?.toString().toLowerCase() ?? '').contains(q) ||
        (g['client_name']?.toString().toLowerCase() ?? '').contains(q)
      ).toList();
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        title: const Text('Guarantors', style: TextStyle(fontWeight: FontWeight.w800, color: Color(0xFF0F172A), fontSize: 20)),
        backgroundColor: Colors.white, elevation: 0, centerTitle: true,
        actions: [IconButton(icon: const Icon(Icons.refresh_rounded, color: AppColors.primary), onPressed: _load)],
      ),
      body: _loading
          ? const ShimmerLoading(itemCount: 6)
          : _error != null
              ? Center(child: Column(mainAxisAlignment: MainAxisAlignment.center, children: [
                  const Icon(Icons.error_outline, size: 48, color: Colors.grey),
                  const SizedBox(height: 12), Text(_error!),
                  const SizedBox(height: 16), ElevatedButton(onPressed: _load, child: const Text('Retry')),
                ]))
              : Column(children: [
                  Padding(
                    padding: const EdgeInsets.fromLTRB(16, 12, 16, 8),
                    child: SearchBarWidget(hint: 'Search guarantors...', onChanged: (_) => _applyFilter(), controller: _searchCtrl),
                  ),
                  Expanded(child: _filtered.isEmpty
                      ? const EmptyState(icon: Icons.people_outline, title: 'No Guarantors', subtitle: 'Add guarantors to client loans')
                      : RefreshIndicator(
                          onRefresh: _load,
                          child: ListView.builder(
                            padding: const EdgeInsets.fromLTRB(16, 4, 16, 100),
                            itemCount: _filtered.length,
                            itemBuilder: (_, i) => _guarantorTile(_filtered[i]),
                          ),
                        )),
                ]),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () => _showForm(null),
        icon: const Icon(Icons.add),
        label: const Text('Add Guarantor'),
        backgroundColor: AppColors.primary, foregroundColor: Colors.white,
      ),
    );
  }

  Widget _guarantorTile(Map<String, dynamic> g) {
    return Dismissible(
      key: ValueKey(g['id'] ?? ''),
      direction: DismissDirection.endToStart,
      confirmDismiss: (_) => ConfirmDialog.show(context,
        title: 'Delete Guarantor',
        message: 'Delete "${g['name']}"?',
        confirmLabel: 'Delete',
        icon: Icons.delete_outline_rounded,
      ),
      onDismissed: (_) => _delete(g['id']),
      background: Container(alignment: Alignment.centerRight, padding: const EdgeInsets.only(right: 20),
        decoration: BoxDecoration(color: AppColors.danger, borderRadius: BorderRadius.circular(14)),
        child: const Icon(Icons.delete_outline, color: Colors.white)),
      child: Padding(
        padding: const EdgeInsets.only(bottom: 10),
        child: GlassCard(
          onTap: () => _showForm(g),
          padding: const EdgeInsets.all(14),
          child: Row(children: [
            Container(width: 46, height: 46, decoration: BoxDecoration(color: AppColors.purple.withValues(alpha: 0.1), borderRadius: BorderRadius.circular(12)),
              child: Center(child: Text((g['name']?.toString() ?? '?')[0].toUpperCase(), style: const TextStyle(color: AppColors.purple, fontWeight: FontWeight.w800, fontSize: 18)))),
            const SizedBox(width: 12),
            Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
              Text(g['name']?.toString() ?? '', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14, color: AppColors.textPri)),
              const SizedBox(height: 2),
              Text(g['phone']?.toString() ?? '', style: const TextStyle(fontSize: 12, color: AppColors.textSec)),
              Row(children: [
                Text('Client: ${g['client_name']?.toString() ?? '-'}', style: const TextStyle(fontSize: 11, color: AppColors.textLight)),
                const SizedBox(width: 8),
                Text(g['relationship']?.toString() ?? '', style: const TextStyle(fontSize: 11, color: AppColors.textLight)),
              ]),
            ])),
            StatusBadge.fromStatus(g['status']?.toString() ?? 'pending'),
          ]),
        ),
      ),
    );
  }

  void _showForm(Map<String, dynamic>? guarantor) {
    AppBottomSheet.show(context, title: guarantor != null ? 'Edit Guarantor' : 'Add Guarantor', child: _GuarantorForm(
      guarantor: guarantor,
      onSaved: () { _load(); Navigator.pop(context); },
    ));
  }

  Future<void> _delete(dynamic id) async {
    try {
      await ApiService.delete('/microfinance/guarantors/$id');
      ToastHelper.success(context, 'Guarantor deleted');
      _load();
    } catch (_) { ToastHelper.error(context, 'Delete failed'); }
  }
}

class _GuarantorForm extends StatefulWidget {
  final Map<String, dynamic>? guarantor;
  final VoidCallback onSaved;
  const _GuarantorForm({this.guarantor, required this.onSaved});
  @override State<_GuarantorForm> createState() => _GuarantorFormState();
}

class _GuarantorFormState extends State<_GuarantorForm> {
  final _form = GlobalKey<FormState>();
  late TextEditingController _name, _phone, _email, _relationship;
  String? _selectedClient;
  bool _saving = false;
  String? _err;
  List<dynamic> _clients = [];
  bool _loadingClients = true;

  @override
  void initState() {
    super.initState();
    final g = widget.guarantor;
    _name = TextEditingController(text: g?['name']?.toString());
    _phone = TextEditingController(text: g?['phone']?.toString());
    _email = TextEditingController(text: g?['email']?.toString());
    _relationship = TextEditingController(text: g?['relationship']?.toString());
    _loadClients();
  }

  @override void dispose() { for (final c in [_name, _phone, _email, _relationship]) c.dispose(); super.dispose(); }

  Future<void> _loadClients() async {
    try {
      final res = await ApiService.get('/microfinance/clients');
      setState(() { _clients = res is List ? res : (res['data'] ?? []); _loadingClients = false; });
    } catch (_) { setState(() { _loadingClients = false; }); }
  }

  Future<void> _save() async {
    if (!_form.currentState!.validate()) return;
    setState(() { _saving = true; _err = null; });
    final body = {
      'name': _name.text.trim(),
      'phone': _phone.text.trim(),
      'email': _email.text.trim(),
      'relationship': _relationship.text.trim(),
      'client_id': _selectedClient,
    };
    try {
      if (widget.guarantor != null) await ApiService.put('/microfinance/guarantors/${widget.guarantor!['id']}', body);
      else await ApiService.post('/microfinance/guarantors', body);
      widget.onSaved();
    } on ApiException catch (e) { setState(() { _err = e.message; _saving = false; }); }
    catch (_) { setState(() { _err = 'Save failed'; _saving = false; }); }
  }

  @override
  Widget build(BuildContext context) => Form(key: _form, child: Column(crossAxisAlignment: CrossAxisAlignment.stretch, children: [
    if (_err != null) Container(padding: const EdgeInsets.all(12), margin: const EdgeInsets.only(bottom: 12),
      decoration: BoxDecoration(color: AppColors.dangerLt, borderRadius: BorderRadius.circular(10)),
      child: Text(_err!, style: const TextStyle(color: AppColors.danger))),
    TextFormField(controller: _name, decoration: const InputDecoration(labelText: 'Name *'), validator: (v) => v!.isNotEmpty ? null : 'Required'),
    const SizedBox(height: 12),
    TextFormField(controller: _phone, keyboardType: TextInputType.phone, decoration: const InputDecoration(labelText: 'Phone *'), validator: (v) => v!.isNotEmpty ? null : 'Required'),
    const SizedBox(height: 12),
    TextFormField(controller: _email, keyboardType: TextInputType.emailAddress, decoration: const InputDecoration(labelText: 'Email')),
    const SizedBox(height: 12),
    TextFormField(controller: _relationship, decoration: const InputDecoration(labelText: 'Relationship to Client')),
    const SizedBox(height: 12),
    if (_loadingClients) const Center(child: Padding(padding: EdgeInsets.all(8), child: CircularProgressIndicator(strokeWidth: 2)))
    else DropdownButtonFormField<String>(value: _selectedClient, decoration: const InputDecoration(labelText: 'Client'),
      items: _clients.map((c) => DropdownMenuItem(value: c['id'].toString(), child: Text(c['name']?.toString() ?? ''))).toList(),
      onChanged: (v) => setState(() => _selectedClient = v)),
    const SizedBox(height: 24),
    SizedBox(height: 52, child: ElevatedButton(onPressed: _saving ? null : _save,
      child: _saving ? const SizedBox(width: 22, height: 22, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5))
          : Text(widget.guarantor != null ? 'Update Guarantor' : 'Add Guarantor', style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w700)))),
  ]));
}
