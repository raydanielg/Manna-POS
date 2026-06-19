import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../shared/theme/app_colors.dart';
import '../../shared/widgets/status_badge.dart';
import '../../shared/widgets/empty_state.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/stat_card.dart';
import '../../core/api_service.dart';

class CrmScreen extends StatefulWidget {
  const CrmScreen({super.key});
  @override State<CrmScreen> createState() => _CrmScreenState();
}

class _CrmScreenState extends State<CrmScreen> {
  List<dynamic> _activities = [];
  Map<String, dynamic>? _stats;
  bool _loading = true;
  String _filter = 'All';

  static const _filters = ['All', 'Calls', 'Meetings', 'Emails', 'Follow-ups', 'Others'];

  @override void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    setState(() => _loading = true);
    try {
      final params = _filter != 'All' ? '?type=${_filter.toLowerCase()}' : '';
      final d = await ApiService.get('/crm/activities$params');
      if (mounted) setState(() { _activities = d['data'] ?? d as List? ?? []; _stats = d['stats']; _loading = false; });
    } catch (_) { if (mounted) setState(() => _loading = false); }
  }

  IconData _iconForType(String type) {
    switch (type.toLowerCase()) {
      case 'call': return Icons.phone;
      case 'meeting': return Icons.people;
      case 'email': return Icons.email;
      case 'follow-up': return Icons.assignment;
      default: return Icons.circle_outlined;
    }
  }

  Color _colorForType(String type) {
    switch (type.toLowerCase()) {
      case 'call': return AppColors.success;
      case 'meeting': return AppColors.primary;
      case 'email': return AppColors.warning;
      case 'follow-up': return AppColors.purple;
      default: return AppColors.textSec;
    }
  }

  void _showAddForm() {
    final titleCtrl = TextEditingController();
    final descCtrl = TextEditingController();
    final clientCtrl = TextEditingController();
    String type = 'call';

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
                const Text('Add Activity', style: TextStyle(fontSize: 19, fontWeight: FontWeight.w800)),
                IconButton(icon: const Icon(Icons.close), onPressed: () => Navigator.pop(ctx)),
              ]),
              const SizedBox(height: 16),
              DropdownButtonFormField<String>(
                value: type,
                decoration: const InputDecoration(labelText: 'Type', prefixIcon: Icon(Icons.category, size: 20)),
                items: const [
                  DropdownMenuItem(value: 'call', child: Row(children: [Icon(Icons.phone, size: 16, color: AppColors.success), SizedBox(width: 8), Text('Call')])),
                  DropdownMenuItem(value: 'meeting', child: Row(children: [Icon(Icons.people, size: 16, color: AppColors.primary), SizedBox(width: 8), Text('Meeting')])),
                  DropdownMenuItem(value: 'email', child: Row(children: [Icon(Icons.email, size: 16, color: AppColors.warning), SizedBox(width: 8), Text('Email')])),
                  DropdownMenuItem(value: 'follow-up', child: Row(children: [Icon(Icons.assignment, size: 16, color: AppColors.purple), SizedBox(width: 8), Text('Follow-up')])),
                  DropdownMenuItem(value: 'other', child: Row(children: [Icon(Icons.circle_outlined, size: 16), SizedBox(width: 8), Text('Other')])),
                ],
                onChanged: (v) => setSheetState(() => type = v!),
              ),
              const SizedBox(height: 12),
              TextFormField(controller: titleCtrl, decoration: const InputDecoration(labelText: 'Title', prefixIcon: Icon(Icons.title, size: 20))),
              const SizedBox(height: 12),
              TextFormField(controller: descCtrl, decoration: const InputDecoration(labelText: 'Description', prefixIcon: Icon(Icons.description, size: 20))),
              const SizedBox(height: 12),
              TextFormField(controller: clientCtrl, decoration: const InputDecoration(labelText: 'Client Name', prefixIcon: Icon(Icons.person, size: 20))),
              const SizedBox(height: 20),
              SizedBox(
                width: double.infinity, height: 52,
                child: ElevatedButton(
                  onPressed: () async {
                    if (titleCtrl.text.trim().isEmpty) return;
                    try {
                      await ApiService.post('/crm/activities', {
                        'type': type, 'title': titleCtrl.text.trim(),
                        'description': descCtrl.text.trim(), 'client': clientCtrl.text.trim(),
                      });
                      if (ctx.mounted) Navigator.pop(ctx);
                      _load();
                    } catch (e) {
                      if (ctx.mounted) ScaffoldMessenger.of(ctx).showSnackBar(SnackBar(content: Text('Error: $e'), backgroundColor: AppColors.danger));
                    }
                  },
                  child: const Text('Add Activity'),
                ),
              ),
            ]),
          ),
        ),
      ),
    );
  }

  void _showDetail(Map<String, dynamic> activity) {
    showModalBottomSheet(
      context: context,
      backgroundColor: Colors.transparent,
      builder: (ctx) => Container(
        decoration: const BoxDecoration(color: AppColors.surface, borderRadius: BorderRadius.vertical(top: Radius.circular(24))),
        padding: const EdgeInsets.fromLTRB(24, 16, 24, 32),
        child: Column(mainAxisSize: MainAxisSize.min, crossAxisAlignment: CrossAxisAlignment.start, children: [
          Center(child: Container(width: 40, height: 4, decoration: BoxDecoration(color: AppColors.border, borderRadius: BorderRadius.circular(4)))),
          const SizedBox(height: 20),
          Row(children: [
            Container(
              width: 44, height: 44,
              decoration: BoxDecoration(color: _colorForType(activity['type'] ?? '').withValues(alpha: 0.15), borderRadius: BorderRadius.circular(10)),
              child: Icon(_iconForType(activity['type'] ?? ''), color: _colorForType(activity['type'] ?? ''), size: 22),
            ),
            const SizedBox(width: 12),
            Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
              Text(activity['title'] ?? '', style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 17)),
              if (activity['client'] != null) Text(activity['client'], style: const TextStyle(color: AppColors.textSec, fontSize: 13)),
            ])),
            StatusBadge.fromStatus(activity['status'] ?? 'pending'),
          ]),
          if (activity['description'] != null) ...[
            const SizedBox(height: 16),
            Container(
              width: double.infinity, padding: const EdgeInsets.all(14),
              decoration: BoxDecoration(color: AppColors.surfaceVariant, borderRadius: BorderRadius.circular(10)),
              child: Text(activity['description'], style: const TextStyle(fontSize: 14, height: 1.5)),
            ),
          ],
          if (activity['created_at'] != null) ...[
            const SizedBox(height: 12),
            Text('Date: ${activity['created_at']}', style: const TextStyle(color: AppColors.textLight, fontSize: 12)),
          ],
        ]),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(title: const Text('CRM')),
      body: _loading
          ? const Center(child: CircularProgressIndicator())
          : RefreshIndicator(
              onRefresh: _load,
              child: ListView(
                padding: const EdgeInsets.all(16),
                children: [
                  if (_stats != null) ...[
                    Row(children: [
                      Expanded(child: StatCard(icon: Icons.assignment, value: '${_stats!['total'] ?? 0}', label: 'Total', color: AppColors.primary)),
                      const SizedBox(width: 10),
                      Expanded(child: StatCard(icon: Icons.schedule, value: '${_stats!['pending_followups'] ?? 0}', label: 'Pending', color: AppColors.warning)),
                    ]),
                    const SizedBox(height: 10),
                    Row(children: [
                      Expanded(child: StatCard(icon: Icons.check_circle, value: '${_stats!['completed_today'] ?? 0}', label: 'Done Today', color: AppColors.success)),
                      const SizedBox(width: 10),
                      Expanded(child: StatCard(icon: Icons.error_outline, value: '${_stats!['overdue'] ?? 0}', label: 'Overdue', color: AppColors.danger)),
                    ]),
                    const SizedBox(height: 16),
                  ],
                  SizedBox(
                    height: 36,
                    child: ListView(
                      scrollDirection: Axis.horizontal,
                      children: _filters.map((f) {
                        final sel = _filter == f;
                        return Padding(
                          padding: const EdgeInsets.only(right: 8),
                          child: GestureDetector(
                            onTap: () => setState(() { _filter = f; _load(); }),
                            child: Container(
                              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                              decoration: BoxDecoration(
                                color: sel ? AppColors.primary : Colors.white,
                                borderRadius: BorderRadius.circular(20),
                                border: Border.all(color: sel ? AppColors.primary : AppColors.border),
                              ),
                              child: Text(f, style: TextStyle(color: sel ? Colors.white : AppColors.textSec, fontWeight: FontWeight.w600, fontSize: 12)),
                            ),
                          ),
                        );
                      }).toList(),
                    ),
                  ),
                  const SizedBox(height: 12),
                  if (_activities.isEmpty)
                    const Padding(padding: EdgeInsets.only(top: 40), child: EmptyState(icon: Icons.event_note, title: 'No Activities', subtitle: 'Tap + to add an activity'))
                  else
                    ..._activities.map((a) => Padding(
                      padding: const EdgeInsets.only(bottom: 10),
                      child: GlassCard(
                        child: InkWell(
                          borderRadius: BorderRadius.circular(14),
                          onTap: () => _showDetail(a),
                          child: Padding(
                            padding: const EdgeInsets.all(14),
                            child: Row(children: [
                              Container(
                                width: 44, height: 44,
                                decoration: BoxDecoration(color: _colorForType(a['type'] ?? '').withValues(alpha: 0.15), borderRadius: BorderRadius.circular(10)),
                                child: Icon(_iconForType(a['type'] ?? ''), color: _colorForType(a['type'] ?? ''), size: 22),
                              ),
                              const SizedBox(width: 12),
                              Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                                Row(children: [
                                  Text(a['title'] ?? '', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14)),
                                  const SizedBox(width: 4),
                                  StatusBadge(label: a['type'] ?? '', color: _colorForType(a['type'] ?? ''), bgColor: _colorForType(a['type'] ?? '').withValues(alpha: 0.1)),
                                ]),
                                const SizedBox(height: 2),
                                if (a['client'] != null) Text(a['client'], style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
                                if (a['created_at'] != null) Text(a['created_at'], style: const TextStyle(color: AppColors.textLight, fontSize: 11)),
                              ])),
                              StatusBadge.fromStatus(a['status'] ?? 'pending'),
                            ]),
                          ),
                        ),
                      ),
                    )),
                  const SizedBox(height: 60),
                ],
              ),
            ),
      floatingActionButton: FloatingActionButton(
        onPressed: _showAddForm,
        backgroundColor: AppColors.primary,
        child: const Icon(Icons.add, color: Colors.white),
      ),
    );
  }
}
