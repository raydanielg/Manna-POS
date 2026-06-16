import 'package:flutter/material.dart';
import '../../core/api_service.dart';
import '../../shared/theme/app_colors.dart';
import '../../shared/widgets/app_card.dart';
import '../../shared/widgets/empty_state.dart';
import '../../shared/widgets/status_badge.dart';

class BusinessLocationsPage extends StatefulWidget {
  const BusinessLocationsPage({super.key});
  @override State<BusinessLocationsPage> createState() => _BusinessLocationsPageState();
}

class _BusinessLocationsPageState extends State<BusinessLocationsPage> {
  List<dynamic> _locations = [];
  bool _loading = true;
  String? _error;

  @override void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    setState(() { _loading = true; _error = null; });
    try {
      final data = await ApiService.get('/business-locations');
      setState(() { _locations = (data as List); _loading = false; });
    } on ApiException catch (e) { setState(() { _error = e.message; _loading = false; }); }
    catch (_) { setState(() { _error = 'Connection error'; _loading = false; }); }
  }

  void _showForm([dynamic l]) => showModalBottomSheet(context: context, isScrollControlled: true, backgroundColor: Colors.transparent, builder: (_) => _LocationForm(location: l, onSaved: _load));

  Future<void> _delete(dynamic l) async {
    final ok = await showDialog<bool>(context: context, builder: (_) => AlertDialog(
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
      title: const Text('Delete Location'),
      content: Text('Delete "${l['name']}"?'),
      actions: [TextButton(onPressed: () => Navigator.pop(context, false), child: const Text('Cancel')),
        ElevatedButton(style: ElevatedButton.styleFrom(backgroundColor: AppColors.danger), onPressed: () => Navigator.pop(context, true), child: const Text('Delete'))],
    ));
    if (ok != true) return;
    try { await ApiService.delete('/business-locations/${l['id']}'); _load(); } on ApiException catch (e) { _snack(e.message, error: true); }
  }

  void _snack(String msg, {bool error = false}) => ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(msg), backgroundColor: error ? AppColors.danger : AppColors.success, behavior: SnackBarBehavior.floating, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10))));

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(title: const Text('Business Locations'), actions: [IconButton(icon: const Icon(Icons.refresh, color: Colors.white), onPressed: _load)]),
      body: _loading ? const LoadingWidget(message: 'Loading...')
        : _error != null ? ErrorWidget2(message: _error!, onRetry: _load)
        : _locations.isEmpty ? const EmptyState(icon: Icons.store_outlined, title: 'No Locations', subtitle: 'Add your first business location')
        : RefreshIndicator(color: AppColors.primary, onRefresh: _load,
            child: ListView.separated(padding: const EdgeInsets.fromLTRB(16, 0, 16, 100), itemCount: _locations.length, separatorBuilder: (_, __) => const SizedBox(height: 10), itemBuilder: (_, i) => _tile(_locations[i]))),
      floatingActionButton: FloatingActionButton.extended(onPressed: () => _showForm(), icon: const Icon(Icons.add), label: const Text('Add Location')),
    );
  }

  Widget _tile(dynamic l) => AppCard(child: Padding(padding: const EdgeInsets.all(16), child: Row(children: [
    Container(width: 46, height: 46, decoration: BoxDecoration(color: AppColors.primaryLt, borderRadius: BorderRadius.circular(12)),
      child: const Icon(Icons.store_outlined, color: AppColors.primary, size: 22)),
    const SizedBox(width: 14),
    Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
      Text(l['name'] ?? '', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 15)),
      if (l['city'] != null || l['country'] != null) Text('${l['city'] ?? ''}, ${l['country'] ?? ''}', style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
      if (l['phone'] != null) Text(l['phone'], style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
    ])),
    Column(crossAxisAlignment: CrossAxisAlignment.end, children: [
      StatusBadge.fromStatus(l['status'] ?? 'active'),
      const SizedBox(height: 6),
      PopupMenuButton<String>(shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)), onSelected: (v) { if (v == 'edit') _showForm(l); else _delete(l); },
        itemBuilder: (_) => [const PopupMenuItem(value: 'edit', child: Row(children: [Icon(Icons.edit_outlined, size: 18, color: AppColors.primary), SizedBox(width: 8), Text('Edit')])), const PopupMenuItem(value: 'delete', child: Row(children: [Icon(Icons.delete_outline, size: 18, color: AppColors.danger), SizedBox(width: 8), Text('Delete', style: TextStyle(color: AppColors.danger))]))]),
    ]),
  ])));
}

class _LocationForm extends StatefulWidget {
  final dynamic location;
  final VoidCallback onSaved;
  const _LocationForm({this.location, required this.onSaved});
  @override State<_LocationForm> createState() => _LocationFormState();
}

class _LocationFormState extends State<_LocationForm> {
  final _form = GlobalKey<FormState>();
  late TextEditingController _name, _address, _city, _country, _phone;
  bool _saving = false;
  String? _err;

  @override
  void initState() {
    super.initState();
    final l = widget.location;
    _name = TextEditingController(text: l?['name']);
    _address = TextEditingController(text: l?['address']);
    _city = TextEditingController(text: l?['city']);
    _country = TextEditingController(text: l?['country']);
    _phone = TextEditingController(text: l?['phone']);
  }

  @override void dispose() { for (final c in [_name, _address, _city, _country, _phone]) c.dispose(); super.dispose(); }

  Future<void> _save() async {
    if (!_form.currentState!.validate()) return;
    setState(() { _saving = true; _err = null; });
    final body = {
      'name': _name.text.trim(),
      if (_address.text.isNotEmpty) 'address': _address.text.trim(),
      if (_city.text.isNotEmpty) 'city': _city.text.trim(),
      if (_country.text.isNotEmpty) 'country': _country.text.trim(),
      if (_phone.text.isNotEmpty) 'phone': _phone.text.trim(),
    };
    try {
      if (widget.location != null) await ApiService.put('/business-locations/${widget.location!['id']}', body);
      else await ApiService.post('/business-locations', body);
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
        Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [Text(widget.location != null ? 'Edit Location' : 'Add Location', style: const TextStyle(fontSize: 20, fontWeight: FontWeight.w700)), IconButton(icon: const Icon(Icons.close), onPressed: () => Navigator.pop(context))]),
        if (_err != null) ...[const SizedBox(height: 12), Container(padding: const EdgeInsets.all(12), decoration: BoxDecoration(color: AppColors.dangerLt, borderRadius: BorderRadius.circular(10)), child: Text(_err!, style: const TextStyle(color: AppColors.danger)))],
        const SizedBox(height: 16),
        TextFormField(controller: _name, decoration: const InputDecoration(labelText: 'Location Name *'), validator: (v) => v!.isNotEmpty ? null : 'Required'),
        const SizedBox(height: 12),
        TextFormField(controller: _address, maxLines: 2, decoration: const InputDecoration(labelText: 'Address')),
        const SizedBox(height: 12),
        Row(children: [Expanded(child: TextFormField(controller: _city, decoration: const InputDecoration(labelText: 'City'))), const SizedBox(width: 12), Expanded(child: TextFormField(controller: _country, decoration: const InputDecoration(labelText: 'Country')))]),
        const SizedBox(height: 12),
        TextFormField(controller: _phone, keyboardType: TextInputType.phone, decoration: const InputDecoration(labelText: 'Phone')),
        const SizedBox(height: 24),
        SizedBox(height: 52, child: ElevatedButton(onPressed: _saving ? null : _save, child: _saving ? const SizedBox(width: 22, height: 22, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5)) : Text(widget.location != null ? 'Update Location' : 'Add Location', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700)))),
      ]))),
    );
  }
}
