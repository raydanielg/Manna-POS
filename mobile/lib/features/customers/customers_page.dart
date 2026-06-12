import 'package:flutter/material.dart';
import '../../core/api_service.dart';
import '../../shared/theme/app_theme.dart';
import '../../shared/models/customer.dart';
import '../../shared/widgets/app_card.dart';
import '../../shared/widgets/empty_state.dart';
import '../../shared/widgets/search_bar_widget.dart';
import '../../shared/widgets/status_badge.dart';

class CustomersPage extends StatefulWidget {
  const CustomersPage({super.key});
  @override State<CustomersPage> createState() => _CustomersPageState();
}

class _CustomersPageState extends State<CustomersPage> {
  List<Customer> _customers = [];
  bool _loading = true;
  String? _error;
  String _search = '';

  @override void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    setState(() { _loading = true; _error = null; });
    try {
      final data = await ApiService.get('/customers?search=${Uri.encodeComponent(_search)}');
      setState(() { _customers = (data as List).map((e) => Customer.fromJson(e)).toList(); _loading = false; });
    } on ApiException catch (e) { setState(() { _error = e.message; _loading = false; }); }
    catch (_) { setState(() { _error = 'Connection error'; _loading = false; }); }
  }

  void _showForm([Customer? c]) => showModalBottomSheet(context: context, isScrollControlled: true, backgroundColor: Colors.transparent, builder: (_) => _CustomerForm(customer: c, onSaved: _load));

  Future<void> _delete(Customer c) async {
    final ok = await showDialog<bool>(context: context, builder: (_) => AlertDialog(
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
      title: const Text('Delete Customer'),
      content: Text('Delete "${c.name}"?'),
      actions: [TextButton(onPressed: () => Navigator.pop(context, false), child: const Text('Cancel')),
        ElevatedButton(style: ElevatedButton.styleFrom(backgroundColor: AppColors.danger), onPressed: () => Navigator.pop(context, true), child: const Text('Delete'))],
    ));
    if (ok != true) return;
    try { await ApiService.delete('/customers/${c.id}'); _load(); } on ApiException catch (e) { _snack(e.message, error: true); }
  }

  void _snack(String msg, {bool error = false}) => ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(msg), backgroundColor: error ? AppColors.danger : AppColors.success, behavior: SnackBarBehavior.floating, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10))));

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(title: const Text('Customers'), actions: [IconButton(icon: const Icon(Icons.refresh, color: Colors.white), onPressed: _load)]),
      body: Column(children: [
        Padding(padding: const EdgeInsets.all(16), child: SearchBarWidget(hint: 'Search customers...', onChanged: (v) { _search = v; _load(); })),
        Expanded(child: _loading ? const LoadingWidget(message: 'Loading customers...')
          : _error != null ? ErrorWidget2(message: _error!, onRetry: _load)
          : _customers.isEmpty ? EmptyState(icon: Icons.people_outlined, title: 'No Customers', subtitle: 'Add your first customer', actionLabel: 'Add Customer', onAction: () => _showForm())
          : RefreshIndicator(color: AppColors.primary, onRefresh: _load,
              child: ListView.separated(padding: const EdgeInsets.fromLTRB(16, 0, 16, 100), itemCount: _customers.length, separatorBuilder: (_, __) => const SizedBox(height: 10), itemBuilder: (_, i) => _tile(_customers[i])))),
      ]),
      floatingActionButton: FloatingActionButton.extended(onPressed: () => _showForm(), icon: const Icon(Icons.add), label: const Text('Add Customer')),
    );
  }

  Widget _tile(Customer c) => AppCard(onTap: () => _showForm(c), child: Padding(padding: const EdgeInsets.all(16), child: Row(children: [
    Container(width: 48, height: 48, decoration: BoxDecoration(color: AppColors.primaryLt, shape: BoxShape.circle),
      child: Center(child: Text(c.initials, style: const TextStyle(color: AppColors.primary, fontWeight: FontWeight.w800, fontSize: 16)))),
    const SizedBox(width: 14),
    Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
      Text(c.name, style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 15)),
      if (c.phone != null) Text(c.phone!, style: const TextStyle(color: AppColors.textSec, fontSize: 13)),
      if (c.email != null) Text(c.email!, style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
    ])),
    Column(crossAxisAlignment: CrossAxisAlignment.end, children: [
      StatusBadge.fromStatus(c.status),
      const SizedBox(height: 6),
      PopupMenuButton<String>(shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)), onSelected: (v) { if (v == 'edit') _showForm(c); else _delete(c); },
        itemBuilder: (_) => [const PopupMenuItem(value: 'edit', child: Row(children: [Icon(Icons.edit_outlined, size: 18, color: AppColors.primary), SizedBox(width: 8), Text('Edit')])), const PopupMenuItem(value: 'delete', child: Row(children: [Icon(Icons.delete_outline, size: 18, color: AppColors.danger), SizedBox(width: 8), Text('Delete', style: TextStyle(color: AppColors.danger))]))]),
    ]),
  ])));
}

class _CustomerForm extends StatefulWidget {
  final Customer? customer;
  final VoidCallback onSaved;
  const _CustomerForm({this.customer, required this.onSaved});
  @override State<_CustomerForm> createState() => _CustomerFormState();
}

class _CustomerFormState extends State<_CustomerForm> {
  final _form = GlobalKey<FormState>();
  late TextEditingController _name, _email, _phone, _address, _city;
  bool _saving = false;
  String? _err;

  @override
  void initState() {
    super.initState();
    final c = widget.customer;
    _name = TextEditingController(text: c?.name);
    _email = TextEditingController(text: c?.email);
    _phone = TextEditingController(text: c?.phone);
    _address = TextEditingController(text: c?.address);
    _city = TextEditingController(text: c?.city);
  }

  @override void dispose() { for (final c in [_name, _email, _phone, _address, _city]) c.dispose(); super.dispose(); }

  Future<void> _save() async {
    if (!_form.currentState!.validate()) return;
    setState(() { _saving = true; _err = null; });
    final body = {'name': _name.text.trim(), if (_email.text.isNotEmpty) 'email': _email.text.trim(), if (_phone.text.isNotEmpty) 'phone': _phone.text.trim(), if (_address.text.isNotEmpty) 'address': _address.text.trim(), if (_city.text.isNotEmpty) 'city': _city.text.trim()};
    try {
      if (widget.customer != null) await ApiService.put('/customers/${widget.customer!.id}', body);
      else await ApiService.post('/customers', body);
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
        Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [Text(widget.customer != null ? 'Edit Customer' : 'Add Customer', style: const TextStyle(fontSize: 20, fontWeight: FontWeight.w700)), IconButton(icon: const Icon(Icons.close), onPressed: () => Navigator.pop(context))]),
        if (_err != null) ...[const SizedBox(height: 12), Container(padding: const EdgeInsets.all(12), decoration: BoxDecoration(color: AppColors.dangerLt, borderRadius: BorderRadius.circular(10)), child: Text(_err!, style: const TextStyle(color: AppColors.danger)))],
        const SizedBox(height: 16),
        TextFormField(controller: _name, decoration: const InputDecoration(labelText: 'Full Name *'), validator: (v) => v!.isNotEmpty ? null : 'Required'),
        const SizedBox(height: 12),
        TextFormField(controller: _email, keyboardType: TextInputType.emailAddress, decoration: const InputDecoration(labelText: 'Email')),
        const SizedBox(height: 12),
        TextFormField(controller: _phone, keyboardType: TextInputType.phone, decoration: const InputDecoration(labelText: 'Phone')),
        const SizedBox(height: 12),
        Row(children: [Expanded(child: TextFormField(controller: _city, decoration: const InputDecoration(labelText: 'City'))), const SizedBox(width: 12), Expanded(child: TextFormField(controller: _address, decoration: const InputDecoration(labelText: 'Address')))]),
        const SizedBox(height: 24),
        SizedBox(height: 52, child: ElevatedButton(onPressed: _saving ? null : _save, child: _saving ? const SizedBox(width: 22, height: 22, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5)) : Text(widget.customer != null ? 'Update Customer' : 'Add Customer', style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w700)))),
      ]))),
    );
  }
}