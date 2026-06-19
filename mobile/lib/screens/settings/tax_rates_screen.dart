import 'package:flutter/material.dart';
import '../../shared/theme/app_colors.dart';
import '../../shared/widgets/status_badge.dart';
import '../../shared/widgets/empty_state.dart';
import '../../widgets/glass_card.dart';
import '../../core/api_service.dart';

class TaxRatesScreen extends StatefulWidget {
  const TaxRatesScreen({super.key});
  @override State<TaxRatesScreen> createState() => _TaxRatesScreenState();
}

class _TaxRatesScreenState extends State<TaxRatesScreen> {
  List<dynamic> _taxes = [];
  bool _loading = true;

  @override void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    setState(() => _loading = true);
    try {
      final d = await ApiService.get('/settings/tax-rates');
      if (mounted) setState(() { _taxes = d['data'] ?? d as List? ?? []; _loading = false; });
    } catch (_) { if (mounted) setState(() => _loading = false); }
  }

  Future<void> _toggleStatus(int index) async {
    final tax = _taxes[index];
    try {
      await ApiService.put('/settings/tax-rates/${tax['id']}', {'is_active': tax['is_active'] != true});
      _load();
    } catch (_) {}
  }

  Future<void> _delete(int index) async {
    final tax = _taxes[index];
    final ok = await showDialog<bool>(context: context, builder: (_) => AlertDialog(
      title: const Text('Delete Tax Rate'),
      content: Text('Delete ${tax['name'] ?? 'this tax rate'}?'),
      actions: [
        TextButton(onPressed: () => Navigator.pop(context, false), child: const Text('Cancel')),
        ElevatedButton(onPressed: () => Navigator.pop(context, true), style: ElevatedButton.styleFrom(backgroundColor: AppColors.danger), child: const Text('Delete')),
      ],
    ));
    if (ok == true) {
      try {
        await ApiService.delete('/settings/tax-rates/${tax['id']}');
        _load();
      } catch (_) {}
    }
  }

  void _showForm([Map<String, dynamic>? tax]) {
    final nameCtrl = TextEditingController(text: tax?['name'] ?? '');
    final rateCtrl = TextEditingController(text: tax?['rate']?.toString() ?? '');
    String type = tax?['type'] ?? 'percentage';
    bool saving = false;

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (ctx) => StatefulBuilder(
        builder: (ctx, setSheetState) => Container(
          decoration: const BoxDecoration(color: AppColors.surface, borderRadius: BorderRadius.vertical(top: Radius.circular(24))),
          padding: EdgeInsets.only(bottom: MediaQuery.of(ctx).viewInsets.bottom),
          child: Padding(
            padding: const EdgeInsets.fromLTRB(24, 16, 24, 32),
            child: Column(mainAxisSize: MainAxisSize.min, children: [
              Center(child: Container(width: 40, height: 4, decoration: BoxDecoration(color: AppColors.border, borderRadius: BorderRadius.circular(4)))),
              const SizedBox(height: 20),
              Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
                Text(tax != null ? 'Edit Tax Rate' : 'Add Tax Rate', style: const TextStyle(fontSize: 19, fontWeight: FontWeight.w800)),
                IconButton(icon: const Icon(Icons.close), onPressed: () => Navigator.pop(ctx)),
              ]),
              const SizedBox(height: 16),
              TextFormField(controller: nameCtrl, decoration: const InputDecoration(labelText: 'Tax Name', prefixIcon: Icon(Icons.percent, size: 20))),
              const SizedBox(height: 12),
              TextFormField(controller: rateCtrl, decoration: const InputDecoration(labelText: 'Rate', prefixIcon: Icon(Icons.numbers, size: 20)), keyboardType: TextInputType.number),
              const SizedBox(height: 12),
              DropdownButtonFormField<String>(
                value: type,
                decoration: const InputDecoration(labelText: 'Type', prefixIcon: Icon(Icons.category, size: 20)),
                items: const [
                  DropdownMenuItem(value: 'percentage', child: Text('Percentage (%)')),
                  DropdownMenuItem(value: 'fixed', child: Text('Fixed Amount')),
                ],
                onChanged: (v) => setSheetState(() => type = v!),
              ),
              const SizedBox(height: 20),
              SizedBox(
                width: double.infinity, height: 52,
                child: ElevatedButton(
                  onPressed: saving ? null : () async {
                    setSheetState(() => saving = true);
                    try {
                      final body = {'name': nameCtrl.text, 'rate': double.tryParse(rateCtrl.text) ?? 0, 'type': type};
                      if (tax != null) {
                        await ApiService.put('/settings/tax-rates/${tax['id']}', body);
                      } else {
                        await ApiService.post('/settings/tax-rates', body);
                      }
                      if (ctx.mounted) Navigator.pop(ctx);
                      _load();
                    } catch (e) {
                      setSheetState(() => saving = false);
                      if (ctx.mounted) ScaffoldMessenger.of(ctx).showSnackBar(SnackBar(content: Text('Error: $e'), backgroundColor: AppColors.danger));
                    }
                  },
                  child: saving ? const SizedBox(width: 22, height: 22, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5)) : Text(tax != null ? 'Update' : 'Add Tax Rate'),
                ),
              ),
            ]),
          ),
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(title: const Text('Tax Rates')),
      body: _loading
          ? const Center(child: CircularProgressIndicator())
          : _taxes.isEmpty
              ? const EmptyState(icon: Icons.percent, title: 'No Tax Rates', subtitle: 'Tap + to add a tax rate')
              : RefreshIndicator(
                  onRefresh: _load,
                  child: ListView.builder(
                    padding: const EdgeInsets.all(16),
                    itemCount: _taxes.length,
                    itemBuilder: (context, i) {
                      final tax = _taxes[i];
                      return Dismissible(
                        key: Key('tax_${tax['id'] ?? i}'),
                        direction: DismissDirection.endToStart,
                        background: Container(alignment: Alignment.centerRight, padding: const EdgeInsets.only(right: 20), decoration: BoxDecoration(color: AppColors.danger, borderRadius: BorderRadius.circular(14)), child: const Icon(Icons.delete, color: Colors.white)),
                        onDismissed: (_) => _delete(i),
                        child: Padding(
                          padding: const EdgeInsets.only(bottom: 10),
                          child: GlassCard(
                            child: ListTile(
                              leading: Container(
                                width: 44, height: 44,
                                decoration: BoxDecoration(color: AppColors.warningLt, borderRadius: BorderRadius.circular(10)),
                                child: const Icon(Icons.percent, color: AppColors.warning, size: 22),
                              ),
                              title: Row(children: [
                                Text(tax['name'] ?? '', style: const TextStyle(fontWeight: FontWeight.w700)),
                                const SizedBox(width: 8),
                                Text('${tax['rate'] ?? 0}%', style: const TextStyle(color: AppColors.primary, fontWeight: FontWeight.w800)),
                              ]),
                              subtitle: Text(tax['type'] ?? 'percentage', style: const TextStyle(fontSize: 12, color: AppColors.textSec)),
                              trailing: Row(mainAxisSize: MainAxisSize.min, children: [
                                Switch(
                                  value: tax['is_active'] == true,
                                  onChanged: (_) => _toggleStatus(i),
                                  activeColor: AppColors.success,
                                ),
                                StatusBadge.fromStatus(tax['is_active'] == true ? 'active' : 'inactive'),
                              ]),
                              onTap: () => _showForm(tax),
                            ),
                          ),
                        ),
                      );
                    },
                  ),
                ),
      floatingActionButton: FloatingActionButton(
        onPressed: () => _showForm(),
        backgroundColor: AppColors.primary,
        child: const Icon(Icons.add, color: Colors.white),
      ),
    );
  }
}
