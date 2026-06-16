import 'package:flutter/material.dart';
import '../../core/api_service.dart';
import '../../shared/theme/app_colors.dart';
import '../../shared/widgets/app_card.dart';
import '../../shared/widgets/empty_state.dart';
import '../../shared/widgets/search_bar_widget.dart';
import '../../shared/widgets/status_badge.dart';

class SuppliersPage extends StatefulWidget {
  const SuppliersPage({super.key});
  @override State<SuppliersPage> createState() => _SuppliersPageState();
}

class _SuppliersPageState extends State<SuppliersPage> {
  List<dynamic> _suppliers = [];
  bool _loading = true;
  String? _error;
  String _search = '';

  @override void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    setState(() { _loading = true; _error = null; });
    try {
      final data = await ApiService.get('/suppliers?search=${Uri.encodeComponent(_search)}');
      setState(() { _suppliers = (data as List); _loading = false; });
    } on ApiException catch (e) { setState(() { _error = e.message; _loading = false; }); }
    catch (_) { setState(() { _error = 'Connection error'; _loading = false; }); }
  }

  void _showForm([dynamic s]) => showModalBottomSheet(context: context, isScrollControlled: true, backgroundColor: Colors.transparent, builder: (_) => _SupplierForm(supplier: s, onSaved: _load));

  Future<void> _delete(dynamic s) async {
    final ok = await showDialog<bool>(context: context, builder: (_) => AlertDialog(
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
      title: const Text('Delete Supplier'),
      content: Text('Delete "${s['name']}"?'),
      actions: [TextButton(onPressed: () => Navigator.pop(context, false), child: const Text('Cancel')),
        ElevatedButton(style: ElevatedButton.styleFrom(backgroundColor: AppColors.danger), onPressed: () => Navigator.pop(context, true), child: const Text('Delete'))],
    ));
    if (ok != true) return;
    try { await ApiService.delete('/suppliers/${s['id']}'); _load(); } on ApiException catch (e) { _snack(e.message, error: true); }
  }

  void _snack(String msg, {bool error = false}) => ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(msg), backgroundColor: error ? AppColors.danger : AppColors.success, behavior: SnackBarBehavior.floating, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10))));

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(title: const Text('Suppliers'), actions: [IconButton(icon: const Icon(Icons.refresh, color: Colors.white), onPressed: _load)]),
      body: Column(children: [
        Padding(padding: const EdgeInsets.all(16), child: SearchBarWidget(hint: 'Search suppliers...', onChanged: (v) { _search = v; _load(); })),
        Expanded(child: _loading ? const LoadingWidget(message: 'Loading suppliers...')
          : _error != null ? ErrorWidget2(message: _error!, onRetry: _load)
          : _suppliers.isEmpty ? EmptyState(icon: Icons.business_outlined, title: 'No Suppliers', subtitle: 'Add your first supplier', actionLabel: 'Add Supplier', onAction: () => _showForm())
          : RefreshIndicator(color: AppColors.primary, onRefresh: _load,
              child: ListView.separated(padding: const EdgeInsets.fromLTRB(16, 0, 16, 100), itemCount: _suppliers.length, separatorBuilder: (_, __) => const SizedBox(height: 10), itemBuilder: (_, i) => _tile(_suppliers[i])))),
      ]),
      floatingActionButton: FloatingActionButton.extended(onPressed: () => _showForm(), icon: const Icon(Icons.add), label: const Text('Add Supplier')),
    );
  }

  Widget _tile(dynamic s) => AppCard(onTap: () => _showForm(s), child: Padding(padding: const EdgeInsets.all(16), child: Row(children: [
    Container(width: 48, height: 48, decoration: BoxDecoration(color: AppColors.primaryLt, shape: BoxShape.circle),
      child: Center(child: Text(_initials(s['name'] ?? ''), style: const TextStyle(color: AppColors.primary, fontWeight: FontWeight.w800, fontSize: 16)))),
    const SizedBox(width: 14),
    Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
      Text(s['name'] ?? '', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 15)),
      if (s['company'] != null) Text('${s['company']}', style: const TextStyle(color: AppColors.textSec, fontSize: 13)),
      if (s['phone'] != null) Text(s['phone'], style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
    ])),
    Column(crossAxisAlignment: CrossAxisAlignment.end, children: [
      StatusBadge.fromStatus(s['status'] ?? 'active'),
      const SizedBox(height: 6),
      PopupMenuButton<String>(shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)), onSelected: (v) { if (v == 'edit') _showForm(s); else _delete(s); },
        itemBuilder: (_) => [const PopupMenuItem(value: 'edit', child: Row(children: [Icon(Icons.edit_outlined, size: 18, color: AppColors.primary), SizedBox(width: 8), Text('Edit')])), const PopupMenuItem(value: 'delete', child: Row(children: [Icon(Icons.delete_outline, size: 18, color: AppColors.danger), SizedBox(width: 8), Text('Delete', style: TextStyle(color: AppColors.danger))]))]),
    ]),
  ])));

  String _initials(String name) {
    final parts = name.split(' ');
    return parts.take(2).map((w) => w[0]).join().toUpperCase();
  }
}

class _SupplierForm extends StatefulWidget {
  final dynamic supplier;
  final VoidCallback onSaved;
  const _SupplierForm({this.supplier, required this.onSaved});
  @override State<_SupplierForm> createState() => _SupplierFormState();
}

class _SupplierFormState extends State<_SupplierForm> {
  final _form = GlobalKey<FormState>();
  late TextEditingController _name, _company, _email, _phone, _address, _city, _country, _tax, _payTerm;
  bool _saving = false;
  String? _err;

  @override
  void initState() {
    super.initState();
    final s = widget.supplier;
    _name = TextEditingController(text: s?['name']);
    _company = TextEditingController(text: s?['company']);
    _email = TextEditingController(text: s?['email']);
    _phone = TextEditingController(text: s?['phone']);
    _address = TextEditingController(text: s?['address']);
    _city = TextEditingController(text: s?['city']);
    _country = TextEditingController(text: s?['country']);
    _tax = TextEditingController(text: s?['tax_number']);
    _payTerm = TextEditingController(text: s?['pay_term']);
  }

  @override void dispose() { for (final c in [_name, _company, _email, _phone, _address, _city, _country, _tax, _payTerm]) c.dispose(); super.dispose(); }

  Future<void> _save() async {
    if (!_form.currentState!.validate()) return;
    setState(() { _saving = true; _err = null; });
    final body = {
      'name': _name.text.trim(),
      if (_company.text.isNotEmpty) 'company': _company.text.trim(),
      if (_email.text.isNotEmpty) 'email': _email.text.trim(),
      if (_phone.text.isNotEmpty) 'phone': _phone.text.trim(),
      if (_address.text.isNotEmpty) 'address': _address.text.trim(),
      if (_city.text.isNotEmpty) 'city': _city.text.trim(),
      if (_country.text.isNotEmpty) 'country': _country.text.trim(),
      if (_tax.text.isNotEmpty) 'tax_number': _tax.text.trim(),
      if (_payTerm.text.isNotEmpty) 'pay_term': _payTerm.text.trim(),
    };
    try {
      if (widget.supplier != null) await ApiService.put('/suppliers/${widget.supplier!['id']}', body);
      else await ApiService.post('/suppliers', body);
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
        Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [Text(widget.supplier != null ? 'Edit Supplier' : 'Add Supplier', style: const TextStyle(fontSize: 20, fontWeight: FontWeight.w700)), IconButton(icon: const Icon(Icons.close), onPressed: () => Navigator.pop(context))]),
        if (_err != null) ...[const SizedBox(height: 12), Container(padding: const EdgeInsets.all(12), decoration: BoxDecoration(color: AppColors.dangerLt, borderRadius: BorderRadius.circular(10)), child: Text(_err!, style: const TextStyle(color: AppColors.danger)))],
        const SizedBox(height: 16),
        TextFormField(controller: _name, decoration: const InputDecoration(labelText: 'Supplier Name *'), validator: (v) => v!.isNotEmpty ? null : 'Required'),
        const SizedBox(height: 12),
        TextFormField(controller: _company, decoration: const InputDecoration(labelText: 'Company')),
        const SizedBox(height: 12),
        Row(children: [Expanded(child: TextFormField(controller: _email, keyboardType: TextInputType.emailAddress, decoration: const InputDecoration(labelText: 'Email'))), const SizedBox(width: 12), Expanded(child: TextFormField(controller: _phone, keyboardType: TextInputType.phone, decoration: const InputDecoration(labelText: 'Phone')))]),
        const SizedBox(height: 12),
        TextFormField(controller: _address, decoration: const InputDecoration(labelText: 'Address')),
        const SizedBox(height: 12),
        Row(children: [Expanded(child: TextFormField(controller: _city, decoration: const InputDecoration(labelText: 'City'))), const SizedBox(width: 12), Expanded(child: TextFormField(controller: _country, decoration: const InputDecoration(labelText: 'Country')))]),
        const SizedBox(height: 12),
        Row(children: [Expanded(child: TextFormField(controller: _tax, decoration: const InputDecoration(labelText: 'Tax Number'))), const SizedBox(width: 12), Expanded(child: TextFormField(controller: _payTerm, decoration: const InputDecoration(labelText: 'Payment Term')))]),
        const SizedBox(height: 24),
        SizedBox(height: 52, child: ElevatedButton(onPressed: _saving ? null : _save, child: _saving ? const SizedBox(width: 22, height: 22, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5)) : Text(widget.supplier != null ? 'Update Supplier' : 'Add Supplier', style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w700)))),
      ]))),
    );
  }
}
