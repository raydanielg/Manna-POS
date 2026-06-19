import 'package:flutter/material.dart';
import '../../shared/theme/app_colors.dart';
import '../../shared/widgets/status_badge.dart';
import '../../shared/widgets/empty_state.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/confirm_dialog.dart';
import '../../core/api_service.dart';

class SmsCampaignsScreen extends StatefulWidget {
  const SmsCampaignsScreen({super.key});
  @override State<SmsCampaignsScreen> createState() => _SmsCampaignsScreenState();
}

class _SmsCampaignsScreenState extends State<SmsCampaignsScreen> {
  List<dynamic> _campaigns = [];
  bool _loading = true;

  @override void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    setState(() => _loading = true);
    try {
      final d = await ApiService.get('/sms-campaigns');
      if (mounted) setState(() { _campaigns = d['data'] ?? d as List? ?? []; _loading = false; });
    } catch (_) { if (mounted) setState(() => _loading = false); }
  }

  Future<void> _action(String id, String action) async {
    try {
      await ApiService.post('/sms-campaigns/$id/$action', {});
      _load();
      if (mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Campaign $action'), backgroundColor: AppColors.success));
    } catch (e) {
      if (mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Error: $e'), backgroundColor: AppColors.danger));
    }
  }

  void _showDetail(Map<String, dynamic> campaign) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (ctx) => Container(
        decoration: const BoxDecoration(color: AppColors.surface, borderRadius: BorderRadius.vertical(top: Radius.circular(24))),
        padding: const EdgeInsets.fromLTRB(24, 16, 24, 32),
        child: Column(mainAxisSize: MainAxisSize.min, crossAxisAlignment: CrossAxisAlignment.start, children: [
          Center(child: Container(width: 40, height: 4, decoration: BoxDecoration(color: AppColors.border, borderRadius: BorderRadius.circular(4)))),
          const SizedBox(height: 20),
          Row(children: [
            Expanded(child: Text(campaign['name'] ?? '', style: const TextStyle(fontSize: 19, fontWeight: FontWeight.w800))),
            StatusBadge.fromStatus(campaign['status'] ?? 'draft'),
          ]),
          const SizedBox(height: 12),
          if (campaign['message'] != null) Text(campaign['message'], style: const TextStyle(color: AppColors.textSec)),
          const SizedBox(height: 8),
          Text('Recipients: ${campaign['recipients'] ?? 0}', style: const TextStyle(fontSize: 13, color: AppColors.textSec)),
          Text('Sent: ${campaign['sent_count'] ?? 0}', style: const TextStyle(fontSize: 13, color: AppColors.textSec)),
          Text('Delivered: ${campaign['delivered'] ?? 0}', style: const TextStyle(fontSize: 13, color: AppColors.success)),
          Text('Failed: ${campaign['failed'] ?? 0}', style: const TextStyle(fontSize: 13, color: AppColors.danger)),
          Text('Date: ${campaign['created_at'] ?? ''}', style: const TextStyle(fontSize: 13, color: AppColors.textSec)),
          const SizedBox(height: 20),
          Row(children: [
            if (campaign['status'] == 'draft')
              Expanded(child: ElevatedButton(onPressed: () { Navigator.pop(ctx); _action(campaign['id'].toString(), 'send'); }, child: const Text('Send Now'))),
            if (campaign['status'] == 'draft') ...[
              const SizedBox(width: 8),
              Expanded(child: OutlinedButton(onPressed: () { Navigator.pop(ctx); _schedule(campaign); }, child: const Text('Schedule'))),
            ],
          ]),
          const SizedBox(height: 8),
          Row(children: [
            Expanded(child: OutlinedButton(onPressed: () { Navigator.pop(ctx); _duplicate(campaign); }, child: const Text('Duplicate'))),
            const SizedBox(width: 8),
            Expanded(child: OutlinedButton(onPressed: () { Navigator.pop(ctx); _delete(campaign); }, style: OutlinedButton.styleFrom(foregroundColor: AppColors.danger)), child: const Text('Delete')),
          ]),
        ]),
      ),
    );
  }

  void _schedule(Map<String, dynamic> campaign) {
    showDatePicker(context: context, initialDate: DateTime.now().add(const Duration(days: 1)), firstDate: DateTime.now(), lastDate: DateTime(2030))
      .then((d) {
        if (d != null) {
          showTimePicker(context: context, initialTime: TimeOfDay.now()).then((t) {
            if (t != null) {
              _action(campaign['id'].toString(), 'schedule');
            }
          });
        }
      });
  }

  void _duplicate(Map<String, dynamic> campaign) async {
    try {
      await ApiService.post('/sms-campaigns/${campaign['id']}/duplicate', {});
      _load();
    } catch (_) {}
  }

  Future<void> _delete(Map<String, dynamic> campaign) async {
    final ok = await showDialog<bool>(context: context, builder: (_) => AlertDialog(
      title: const Text('Delete Campaign'),
      content: Text('Delete ${campaign['name']}?'),
      actions: [
        TextButton(onPressed: () => Navigator.pop(context, false), child: const Text('Cancel')),
        ElevatedButton(onPressed: () => Navigator.pop(context, true), style: ElevatedButton.styleFrom(backgroundColor: AppColors.danger), child: const Text('Delete')),
      ],
    ));
    if (ok == true) {
      try {
        await ApiService.delete('/sms-campaigns/${campaign['id']}');
        _load();
      } catch (_) {}
    }
  }

  void _createCampaign() {
    final nameCtrl = TextEditingController();
    final messageCtrl = TextEditingController();
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
              const Text('New SMS Campaign', style: TextStyle(fontSize: 19, fontWeight: FontWeight.w800)),
              const SizedBox(height: 16),
              TextFormField(controller: nameCtrl, decoration: const InputDecoration(labelText: 'Campaign Name', prefixIcon: Icon(Icons.campaign, size: 20))),
              const SizedBox(height: 12),
              TextFormField(controller: messageCtrl, decoration: const InputDecoration(labelText: 'Message', alignLabelWithHint: true), maxLines: 4),
              const SizedBox(height: 20),
              SizedBox(
                width: double.infinity, height: 52,
                child: ElevatedButton(
                  onPressed: saving ? null : () async {
                    if (nameCtrl.text.trim().isEmpty) return;
                    setSheetState(() => saving = true);
                    try {
                      await ApiService.post('/sms-campaigns', {'name': nameCtrl.text.trim(), 'message': messageCtrl.text.trim()});
                      if (ctx.mounted) Navigator.pop(ctx);
                      _load();
                    } catch (e) {
                      setSheetState(() => saving = false);
                      if (ctx.mounted) ScaffoldMessenger.of(ctx).showSnackBar(SnackBar(content: Text('Error: $e'), backgroundColor: AppColors.danger));
                    }
                  },
                  child: saving ? const SizedBox(width: 22, height: 22, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5)) : const Text('Create Campaign'),
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
      appBar: AppBar(title: const Text('SMS Campaigns')),
      body: _loading
          ? const Center(child: CircularProgressIndicator())
          : _campaigns.isEmpty
              ? const EmptyState(icon: Icons.campaign_outlined, title: 'No Campaigns', subtitle: 'Tap + to create your first campaign')
              : RefreshIndicator(
                  onRefresh: _load,
                  child: ListView.builder(
                    padding: const EdgeInsets.all(16),
                    itemCount: _campaigns.length,
                    itemBuilder: (context, i) {
                      final c = _campaigns[i];
                      return Padding(
                        padding: const EdgeInsets.only(bottom: 10),
                        child: GlassCard(
                          child: InkWell(
                            borderRadius: BorderRadius.circular(14),
                            onTap: () => _showDetail(c),
                            child: Padding(
                              padding: const EdgeInsets.all(14),
                              child: Row(children: [
                                Container(
                                  width: 44, height: 44,
                                  decoration: BoxDecoration(color: AppColors.primaryLt, borderRadius: BorderRadius.circular(10)),
                                  child: const Icon(Icons.campaign, color: AppColors.primary, size: 22),
                                ),
                                const SizedBox(width: 12),
                                Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                                  Text(c['name'] ?? '', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 15)),
                                  const SizedBox(height: 2),
                                  Text('Sent: ${c['sent_count'] ?? 0} | ${c['created_at'] ?? ''}', style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
                                ])),
                                StatusBadge.fromStatus(c['status'] ?? 'draft'),
                              ]),
                            ),
                          ),
                        ),
                      );
                    },
                  ),
                ),
      floatingActionButton: FloatingActionButton(
        onPressed: _createCampaign,
        backgroundColor: AppColors.primary,
        child: const Icon(Icons.add, color: Colors.white),
      ),
    );
  }
}
