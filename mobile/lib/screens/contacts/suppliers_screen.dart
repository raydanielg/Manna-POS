import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../shared/theme/app_colors.dart';
import '../../shared/widgets/status_badge.dart';
import '../../shared/widgets/search_bar_widget.dart';
import '../../shared/widgets/empty_state.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/shimmer_loading.dart';
import '../../core/api_service.dart';

class SuppliersScreen extends StatefulWidget {
  const SuppliersScreen({super.key});
  @override State<SuppliersScreen> createState() => _SuppliersScreenState();
}

class _SuppliersScreenState extends State<SuppliersScreen> {
  List<dynamic> _suppliers = [];
  bool _loading = true;
  final _search = TextEditingController();
  final _fmt = NumberFormat('#,##0.00');

  @override void initState() { super.initState(); _load(); }
  @override void dispose() { _search.dispose(); super.dispose(); }

  Future<void> _load() async {
    setState(() => _loading = true);
    try {
      final d = await ApiService.get('/suppliers');
      if (mounted) setState(() { _suppliers = d['data'] ?? d as List? ?? []; _loading = false; });
    } catch (_) { if (mounted) setState(() => _loading = false); }
  }

  void _showForm([Map<String, dynamic>? supplier]) {
    final nameCtrl = TextEditingController(text: supplier?['name'] ?? '');
    final phoneCtrl = TextEditingController(text: supplier?['phone'] ?? '');
    final emailCtrl = TextEditingController(text: supplier?['email'] ?? '');
    final addressCtrl = TextEditingController(text: supplier?['address'] ?? '');
    final cityCtrl = TextEditingController(text: supplier?['city'] ?? '');
    final countryCtrl = TextEditingController(text: supplier?['country'] ?? '');
    final taxCtrl = TextEditingController(text: supplier?['tax_number'] ?? '');
    bool saving = false;

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (ctx) => StatefulBuilder(
        builder: (ctx, setSheetState) => Container(
          decoration: const BoxDecoration(color: AppColors.surface, borderRadius: BorderRadius.vertical(top: Radius.circular(24))),
          padding: EdgeInsets.only(bottom: MediaQuery.of(ctx).viewInsets.bottom),
          child: SingleChildScrollView(
            padding: const EdgeInsets.fromLTRB(24, 16, 24, 32),
            child: Column(mainAxisSize: MainAxisSize.min, children: [
              Center(child: Container(width: 40, height: 4, decoration: BoxDecoration(color: AppColors.border, borderRadius: BorderRadius.circular(4)))),
              const SizedBox(height: 20),
              Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
                Text(supplier != null ? 'Edit Supplier' : 'Add Supplier', style: const TextStyle(fontSize: 19, fontWeight: FontWeight.w800)),
                IconButton(icon: const Icon(Icons.close), onPressed: () => Navigator.pop(ctx)),
              ]),
              const SizedBox(height: 16),
              TextFormField(controller: nameCtrl, decoration: const InputDecoration(labelText: 'Name', prefixIcon: Icon(Icons.person, size: 20))),
              const SizedBox(height: 12),
              TextFormField(controller: phoneCtrl, decoration: const InputDecoration(labelText: 'Phone', prefixIcon: Icon(Icons.phone, size: 20)), keyboardType: TextInputType.phone),
              const SizedBox(height: 12),
              TextFormField(controller: emailCtrl, decoration: const InputDecoration(labelText: 'Email', prefixIcon: Icon(Icons.email, size: 20)), keyboardType: TextInputType.emailAddress),
              const SizedBox(height: 12),
              TextFormField(controller: addressCtrl, decoration: const InputDecoration(labelText: 'Address', prefixIcon: Icon(Icons.location_on, size: 20))),
              const SizedBox(height: 12),
              Row(children: [
                Expanded(child: TextFormField(controller: cityCtrl, decoration: const InputDecoration(labelText: 'City'))),
                const SizedBox(width: 12),
                Expanded(child: TextFormField(controller: countryCtrl, decoration: const InputDecoration(labelText: 'Country'))),
              ]),
              const SizedBox(height: 12),
              TextFormField(controller: taxCtrl, decoration: const InputDecoration(labelText: 'Tax Number', prefixIcon: Icon(Icons.badge, size: 20))),
              const SizedBox(height: 20),
              SizedBox(
                width: double.infinity, height: 52,
                child: ElevatedButton(
                  onPressed: saving ? null : () async {
                    if (nameCtrl.text.trim().isEmpty) return;
                    setSheetState(() => saving = true);
                    try {
                      final body = {'name': nameCtrl.text.trim(), 'phone': phoneCtrl.text.trim(), 'email': emailCtrl.text.trim(), 'address': addressCtrl.text.trim(), 'city': cityCtrl.text.trim(), 'country': countryCtrl.text.trim(), 'tax_number': taxCtrl.text.trim()};
                      if (supplier != null) {
                        await ApiService.put('/suppliers/${supplier['id']}', body);
                      } else {
                        await ApiService.post('/suppliers', body);
                      }
                      if (ctx.mounted) Navigator.pop(ctx);
                      _load();
                    } catch (e) {
                      setSheetState(() => saving = false);
                      if (ctx.mounted) ScaffoldMessenger.of(ctx).showSnackBar(SnackBar(content: Text('Error: $e'), backgroundColor: AppColors.danger));
                    }
                  },
                  child: saving ? const SizedBox(width: 22, height: 22, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5)) : Text(supplier != null ? 'Update' : 'Add Supplier'),
                ),
              ),
            ]),
          ),
        ),
      ),
    );
  }

  Future<void> _delete(int index) async {
    final s = _suppliers[index];
    final ok = await showDialog<bool>(context: context, builder: (_) => AlertDialog(
      title: const Text('Delete Supplier'),
      content: Text('Delete ${s['name']}?'),
      actions: [
        TextButton(onPressed: () => Navigator.pop(context, false), child: const Text('Cancel')),
        ElevatedButton(onPressed: () => Navigator.pop(context, true), style: ElevatedButton.styleFrom(backgroundColor: AppColors.danger), child: const Text('Delete')),
      ],
    ));
    if (ok == true) {
      try {
        await ApiService.delete('/suppliers/${s['id']}');
        _load();
      } catch (_) {}
    }
  }

  @override
  Widget build(BuildContext context) {
    final filtered = _suppliers.where((s) {
      final q = _search.text.toLowerCase();
      return q.isEmpty || (s['name']?.toString().toLowerCase().contains(q) ?? false) || (s['phone']?.toString().contains(q) ?? false);
    }).toList();

    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(title: const Text('Suppliers')),
      body: Column(children: [
        Padding(
          padding: const EdgeInsets.fromLTRB(16, 8, 16, 8),
          child: SearchBarWidget(hint: 'Search suppliers...', controller: _search, onChanged: (_) => setState(() {})),
        ),
        Expanded(
          child: _loading
              ? const Center(child: CircularProgressIndicator())
              : filtered.isEmpty
                  ? const EmptyState(icon: Icons.people_outline, title: 'No Suppliers', subtitle: 'Tap + to add a supplier')
                  : RefreshIndicator(
                      onRefresh: _load,
                      child: ListView.builder(
                        padding: const EdgeInsets.fromLTRB(16, 0, 16, 80),
                        itemCount: filtered.length,
                        itemBuilder: (context, i) {
                          final s = filtered[i];
                          return Dismissible(
                            key: Key('sup_${s['id'] ?? i}'),
                            direction: DismissDirection.endToStart,
                            background: Container(alignment: Alignment.centerRight, padding: const EdgeInsets.only(right: 20), decoration: BoxDecoration(color: AppColors.danger, borderRadius: BorderRadius.circular(14)), child: const Icon(Icons.delete, color: Colors.white)),
                            onDismissed: (_) => _delete(_suppliers.indexOf(s)),
                            child: Padding(
                              padding: const EdgeInsets.only(bottom: 10),
                              child: GlassCard(
                                child: InkWell(
                                  borderRadius: BorderRadius.circular(14),
                                  onTap: () => _showForm(s),
                                  child: Padding(
                                    padding: const EdgeInsets.all(14),
                                    child: Row(children: [
                                      Container(
                                        width: 44, height: 44,
                                        decoration: BoxDecoration(color: AppColors.primaryLt, borderRadius: BorderRadius.circular(10)),
                                        child: const Icon(Icons.store, color: AppColors.primary, size: 22),
                                      ),
                                      const SizedBox(width: 12),
                                      Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                                        Row(children: [
                                          Text(s['name'] ?? '', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 15)),
                                          const SizedBox(width: 8),
                                          StatusBadge.fromStatus(s['is_active'] == true ? 'active' : 'inactive'),
                                        ]),
                                        const SizedBox(height: 2),
                                        if (s['phone'] != null) Text(s['phone'], style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
                                        if (s['email'] != null) Text(s['email'], style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
                                      ])),
                                      Column(crossAxisAlignment: CrossAxisAlignment.end, children: [
                                        Text('TSh ${_fmt.format((s['total_purchases'] ?? 0).toDouble())}', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 13)),
                                        Text('Bal: TSh ${_fmt.format((s['balance'] ?? 0).toDouble())}', style: TextStyle(color: (s['balance'] ?? 0) > 0 ? AppColors.danger : AppColors.success, fontSize: 11)),
                                      ]),
                                    ]),
                                  ),
                                ),
                              ),
                            ),
                          );
                        },
                      ),
                    ),
        ),
      ]),
      floatingActionButton: FloatingActionButton(
        onPressed: () => _showForm(),
        backgroundColor: AppColors.primary,
        child: const Icon(Icons.add, color: Colors.white),
      ),
    );
  }
}
