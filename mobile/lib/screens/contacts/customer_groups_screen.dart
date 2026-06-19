import 'package:flutter/material.dart';
import '../../shared/theme/app_colors.dart';
import '../../shared/widgets/empty_state.dart';
import '../../widgets/glass_card.dart';
import '../../core/api_service.dart';

class CustomerGroupsScreen extends StatefulWidget {
  const CustomerGroupsScreen({super.key});
  @override State<CustomerGroupsScreen> createState() => _CustomerGroupsScreenState();
}

class _CustomerGroupsScreenState extends State<CustomerGroupsScreen> {
  List<dynamic> _groups = [];
  bool _loading = true;

  @override void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    setState(() => _loading = true);
    try {
      final d = await ApiService.get('/customer-groups');
      if (mounted) setState(() { _groups = d['data'] ?? d as List? ?? []; _loading = false; });
    } catch (_) { if (mounted) setState(() => _loading = false); }
  }

  void _showForm([Map<String, dynamic>? group]) {
    final nameCtrl = TextEditingController(text: group?['name'] ?? '');
    final descCtrl = TextEditingController(text: group?['description'] ?? '');
    String colorStr = group?['color'] ?? '#2563EB';
    bool saving = false;

    final colors = [
      '#2563EB', '#EF4444', '#F59E0B', '#10B981', '#6366F1',
      '#EC4899', '#06B6D4', '#F97316', '#8B5CF6', '#14B8A6',
    ];

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
                Text(group != null ? 'Edit Group' : 'Add Group', style: const TextStyle(fontSize: 19, fontWeight: FontWeight.w800)),
                IconButton(icon: const Icon(Icons.close), onPressed: () => Navigator.pop(ctx)),
              ]),
              const SizedBox(height: 16),
              TextFormField(controller: nameCtrl, decoration: const InputDecoration(labelText: 'Group Name', prefixIcon: Icon(Icons.group, size: 20))),
              const SizedBox(height: 12),
              TextFormField(controller: descCtrl, decoration: const InputDecoration(labelText: 'Description', prefixIcon: Icon(Icons.description, size: 20))),
              const SizedBox(height: 12),
              const Text('Color', style: TextStyle(fontWeight: FontWeight.w600, fontSize: 13)),
              const SizedBox(height: 8),
              Wrap(spacing: 8, runSpacing: 8, children: colors.map((c) {
                final color = Color(int.parse(c.replaceFirst('#', '0xFF')));
                final sel = colorStr == c;
                return GestureDetector(
                  onTap: () => setSheetState(() => colorStr = c),
                  child: Container(
                    width: 36, height: 36,
                    decoration: BoxDecoration(
                      color: color,
                      shape: BoxShape.circle,
                      border: sel ? Border.all(color: Colors.white, width: 3) : null,
                      boxShadow: sel ? [BoxShadow(color: color.withValues(alpha: 0.5), blurRadius: 8)] : null,
                    ),
                    child: sel ? const Icon(Icons.check, color: Colors.white, size: 16) : null,
                  ),
                );
              }).toList()),
              const SizedBox(height: 20),
              SizedBox(
                width: double.infinity, height: 52,
                child: ElevatedButton(
                  onPressed: saving ? null : () async {
                    if (nameCtrl.text.trim().isEmpty) return;
                    setSheetState(() => saving = true);
                    try {
                      final body = {'name': nameCtrl.text.trim(), 'description': descCtrl.text.trim(), 'color': colorStr};
                      if (group != null) {
                        await ApiService.put('/customer-groups/${group['id']}', body);
                      } else {
                        await ApiService.post('/customer-groups', body);
                      }
                      if (ctx.mounted) Navigator.pop(ctx);
                      _load();
                    } catch (e) {
                      setSheetState(() => saving = false);
                      if (ctx.mounted) ScaffoldMessenger.of(ctx).showSnackBar(SnackBar(content: Text('Error: $e'), backgroundColor: AppColors.danger));
                    }
                  },
                  child: saving ? const SizedBox(width: 22, height: 22, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5)) : Text(group != null ? 'Update' : 'Add Group'),
                ),
              ),
            ]),
          ),
        ),
      ),
    );
  }

  Future<void> _delete(int index) async {
    final g = _groups[index];
    final ok = await showDialog<bool>(context: context, builder: (_) => AlertDialog(title: const Text('Delete Group'), content: Text('Delete ${g['name']}?'), actions: [
      TextButton(onPressed: () => Navigator.pop(context, false), child: const Text('Cancel')),
      ElevatedButton(onPressed: () => Navigator.pop(context, true), style: ElevatedButton.styleFrom(backgroundColor: AppColors.danger), child: const Text('Delete')),
    ]));
    if (ok == true) {
      try { await ApiService.delete('/customer-groups/${g['id']}'); _load(); } catch (_) {}
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(title: const Text('Customer Groups')),
      body: _loading
          ? const Center(child: CircularProgressIndicator())
          : _groups.isEmpty
              ? const EmptyState(icon: Icons.group_work_outlined, title: 'No Groups', subtitle: 'Tap + to create a group')
              : RefreshIndicator(
                  onRefresh: _load,
                  child: ListView.builder(
                    padding: const EdgeInsets.all(16),
                    itemCount: _groups.length,
                    itemBuilder: (context, i) {
                      final g = _groups[i];
                      final color = Color(int.parse((g['color'] ?? '#2563EB').toString().replaceFirst('#', '0xFF')));
                      return Dismissible(
                        key: Key('grp_${g['id'] ?? i}'),
                        direction: DismissDirection.endToStart,
                        background: Container(alignment: Alignment.centerRight, padding: const EdgeInsets.only(right: 20), decoration: BoxDecoration(color: AppColors.danger, borderRadius: BorderRadius.circular(14)), child: const Icon(Icons.delete, color: Colors.white)),
                        onDismissed: (_) => _delete(i),
                        child: Padding(
                          padding: const EdgeInsets.only(bottom: 10),
                          child: GlassCard(
                            child: ListTile(
                              leading: Container(
                                width: 44, height: 44,
                                decoration: BoxDecoration(color: color.withValues(alpha: 0.15), borderRadius: BorderRadius.circular(10)),
                                child: Icon(Icons.group, color: color, size: 22),
                              ),
                              title: Text(g['name'] ?? '', style: const TextStyle(fontWeight: FontWeight.w700)),
                              subtitle: Text('${g['member_count'] ?? 0} members · ${g['description'] ?? ''}', style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
                              trailing: const Icon(Icons.chevron_right, color: AppColors.textSec),
                              onTap: () => _showForm(g),
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
