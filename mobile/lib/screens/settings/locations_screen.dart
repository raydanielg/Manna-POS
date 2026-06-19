import 'package:flutter/material.dart';
import '../../shared/theme/app_colors.dart';
import '../../shared/widgets/status_badge.dart';
import '../../shared/widgets/empty_state.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/confirm_dialog.dart';
import '../../widgets/app_bottom_sheet.dart';
import '../../core/api_service.dart';

class LocationsScreen extends StatefulWidget {
  const LocationsScreen({super.key});
  @override State<LocationsScreen> createState() => _LocationsScreenState();
}

class _LocationsScreenState extends State<LocationsScreen> {
  List<dynamic> _locations = [];
  bool _loading = true;

  @override void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    setState(() => _loading = true);
    try {
      final d = await ApiService.get('/settings/locations');
      if (mounted) setState(() { _locations = d['data'] ?? d as List? ?? []; _loading = false; });
    } catch (_) { if (mounted) setState(() => _loading = false); }
  }

  Future<void> _delete(int index) async {
    final loc = _locations[index];
    final ok = await showDialog<bool>(context: context, builder: (_) => AlertDialog(
      title: const Text('Delete Location'),
      content: Text('Delete ${loc['name'] ?? 'this location'}?'),
      actions: [
        TextButton(onPressed: () => Navigator.pop(context, false), child: const Text('Cancel')),
        ElevatedButton(onPressed: () => Navigator.pop(context, true), style: ElevatedButton.styleFrom(backgroundColor: AppColors.danger), child: const Text('Delete')),
      ],
    ));
    if (ok == true) {
      try {
        await ApiService.delete('/settings/locations/${loc['id']}');
        _load();
      } catch (_) {}
    }
  }

  void _showForm([Map<String, dynamic>? loc]) {
    final nameCtrl = TextEditingController(text: loc?['name'] ?? '');
    final addressCtrl = TextEditingController(text: loc?['address'] ?? '');
    final phoneCtrl = TextEditingController(text: loc?['phone'] ?? '');
    final managerCtrl = TextEditingController(text: loc?['manager'] ?? '');
    bool active = loc?['is_active'] ?? true;
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
                Text(loc != null ? 'Edit Location' : 'Add Location', style: const TextStyle(fontSize: 19, fontWeight: FontWeight.w800)),
                IconButton(icon: const Icon(Icons.close), onPressed: () => Navigator.pop(ctx)),
              ]),
              const SizedBox(height: 16),
              TextFormField(controller: nameCtrl, decoration: const InputDecoration(labelText: 'Location Name', prefixIcon: Icon(Icons.store, size: 20))),
              const SizedBox(height: 12),
              TextFormField(controller: addressCtrl, decoration: const InputDecoration(labelText: 'Address', prefixIcon: Icon(Icons.location_on, size: 20))),
              const SizedBox(height: 12),
              TextFormField(controller: phoneCtrl, decoration: const InputDecoration(labelText: 'Phone', prefixIcon: Icon(Icons.phone, size: 20)), keyboardType: TextInputType.phone),
              const SizedBox(height: 12),
              TextFormField(controller: managerCtrl, decoration: const InputDecoration(labelText: 'Manager', prefixIcon: Icon(Icons.person, size: 20))),
              const SizedBox(height: 12),
              SwitchListTile(
                title: const Text('Active'),
                value: active,
                onChanged: (v) => setSheetState(() => active = v),
                contentPadding: EdgeInsets.zero,
              ),
              const SizedBox(height: 20),
              SizedBox(
                width: double.infinity, height: 52,
                child: ElevatedButton(
                  onPressed: saving ? null : () async {
                    setSheetState(() => saving = true);
                    try {
                      final body = {'name': nameCtrl.text, 'address': addressCtrl.text, 'phone': phoneCtrl.text, 'manager': managerCtrl.text, 'is_active': active};
                      if (loc != null) {
                        await ApiService.put('/settings/locations/${loc['id']}', body);
                      } else {
                        await ApiService.post('/settings/locations', body);
                      }
                      if (ctx.mounted) Navigator.pop(ctx);
                      _load();
                    } catch (e) {
                      setSheetState(() => saving = false);
                      if (ctx.mounted) ScaffoldMessenger.of(ctx).showSnackBar(SnackBar(content: Text('Error: $e'), backgroundColor: AppColors.danger));
                    }
                  },
                  child: saving ? const SizedBox(width: 22, height: 22, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5)) : Text(loc != null ? 'Update' : 'Add Location'),
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
      appBar: AppBar(title: const Text('Locations')),
      body: _loading
          ? const Center(child: CircularProgressIndicator())
          : _locations.isEmpty
              ? const EmptyState(icon: Icons.location_off, title: 'No Locations', subtitle: 'Tap + to add a business location')
              : RefreshIndicator(
                  onRefresh: _load,
                  child: ListView.builder(
                    padding: const EdgeInsets.all(16),
                    itemCount: _locations.length,
                    itemBuilder: (context, i) {
                      final loc = _locations[i];
                      return Dismissible(
                        key: Key('loc_${loc['id'] ?? i}'),
                        direction: DismissDirection.endToStart,
                        background: Container(alignment: Alignment.centerRight, padding: const EdgeInsets.only(right: 20), decoration: BoxDecoration(color: AppColors.danger, borderRadius: BorderRadius.circular(14)), child: const Icon(Icons.delete, color: Colors.white)),
                        onDismissed: (_) => _delete(i),
                        child: Padding(
                          padding: const EdgeInsets.only(bottom: 10),
                          child: GlassCard(
                            child: ListTile(
                              leading: Container(
                                width: 44, height: 44,
                                decoration: BoxDecoration(color: AppColors.primaryLt, borderRadius: BorderRadius.circular(10)),
                                child: const Icon(Icons.store, color: AppColors.primary, size: 22),
                              ),
                              title: Text(loc['name'] ?? '', style: const TextStyle(fontWeight: FontWeight.w700)),
                              subtitle: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                                if (loc['address'] != null && loc['address'].toString().isNotEmpty) Text(loc['address'], style: const TextStyle(fontSize: 12, color: AppColors.textSec)),
                                if (loc['phone'] != null && loc['phone'].toString().isNotEmpty) Text(loc['phone'], style: const TextStyle(fontSize: 12, color: AppColors.textSec)),
                                if (loc['manager'] != null && loc['manager'].toString().isNotEmpty) Text('Manager: ${loc['manager']}', style: const TextStyle(fontSize: 12, color: AppColors.textSec)),
                              ]),
                              trailing: StatusBadge.fromStatus(loc['is_active'] == true ? 'active' : 'inactive'),
                              onTap: () => _showForm(loc),
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
