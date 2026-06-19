import 'package:flutter/material.dart';
import '../../shared/theme/app_colors.dart';
import '../../shared/widgets/status_badge.dart';
import '../../widgets/glass_card.dart';
import '../../core/api_service.dart';

class FeedbackDetailScreen extends StatefulWidget {
  final Map<String, dynamic>? feedback;
  const FeedbackDetailScreen({super.key, this.feedback});
  @override State<FeedbackDetailScreen> createState() => _FeedbackDetailScreenState();
}

class _FeedbackDetailScreenState extends State<FeedbackDetailScreen> {
  final _replyCtrl = TextEditingController();
  Map<String, dynamic>? _item;
  bool _sending = false;

  @override
  void initState() { super.initState(); _item = widget.feedback; }

  @override void dispose() { _replyCtrl.dispose(); super.dispose(); }

  Future<void> _sendReply() async {
    if (_replyCtrl.text.trim().isEmpty) return;
    setState(() => _sending = true);
    try {
      final res = await ApiService.post('/support/feedback/${_item!['id']}/reply', {'message': _replyCtrl.text.trim()});
      if (mounted) {
        setState(() { _item = res; _replyCtrl.clear(); _sending = false; });
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Error: $e'), backgroundColor: AppColors.danger));
        setState(() => _sending = false);
      }
    }
  }

  Future<void> _updateStatus(String status) async {
    try {
      final res = await ApiService.put('/support/feedback/${_item!['id']}', {'status': status});
      if (mounted) setState(() => _item = res);
    } catch (e) {
      if (mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Error: $e'), backgroundColor: AppColors.danger));
    }
  }

  @override
  Widget build(BuildContext context) {
    if (_item == null) return Scaffold(backgroundColor: AppColors.bg, appBar: AppBar(title: const Text('Feedback')), body: const Center(child: Text('No data')));
    final replies = (_item!['replies'] as List?) ?? [];

    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(title: const Text('Feedback')),
      body: Column(children: [
        Expanded(
          child: ListView(
            padding: const EdgeInsets.all(16),
            children: [
              GlassCard(
                child: Padding(
                  padding: const EdgeInsets.all(16),
                  child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                    Row(children: [
                      Expanded(child: Text(_item!['subject'] ?? '', style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 18))),
                      Column(children: [
                        StatusBadge.fromStatus(_item!['status'] ?? 'open'),
                        const SizedBox(height: 4),
                        _priorityBadge(_item!['priority'] ?? 'medium'),
                      ]),
                    ]),
                    const SizedBox(height: 12),
                    Container(
                      width: double.infinity,
                      padding: const EdgeInsets.all(14),
                      decoration: BoxDecoration(color: AppColors.surfaceVariant, borderRadius: BorderRadius.circular(10)),
                      child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                        Text(_item!['message'] ?? '', style: const TextStyle(fontSize: 14, height: 1.5)),
                        const SizedBox(height: 8),
                        Text(_item!['created_at'] ?? '', style: const TextStyle(color: AppColors.textLight, fontSize: 11)),
                      ]),
                    ),
                    const SizedBox(height: 12),
                    Row(children: [
                      Text('Category: ${_item!['category'] ?? '-'}', style: const TextStyle(color: AppColors.textSec, fontSize: 13)),
                      const Spacer(),
                      if (_item!['status'] != 'resolved')
                        TextButton(onPressed: () => _updateStatus('resolved'), child: const Text('Resolve')),
                      if (_item!['status'] != 'closed')
                        TextButton(onPressed: () => _updateStatus('closed'), child: const Text('Close')),
                      if (_item!['status'] == 'closed' || _item!['status'] == 'resolved')
                        TextButton(onPressed: () => _updateStatus('open'), child: const Text('Reopen')),
                    ]),
                  ]),
                ),
              ),
              const SizedBox(height: 16),
              if (replies.isNotEmpty) ...[
                const Text('Conversation', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 16)),
                const SizedBox(height: 8),
                ...replies.map((r) => Padding(
                  padding: const EdgeInsets.only(bottom: 8),
                  child: GlassCard(
                    child: Padding(
                      padding: const EdgeInsets.all(14),
                      child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                        Row(children: [
                          Text(r['user'] ?? 'Staff', style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13)),
                          const Spacer(),
                          Text(r['created_at'] ?? '', style: const TextStyle(color: AppColors.textLight, fontSize: 11)),
                        ]),
                        const SizedBox(height: 8),
                        Text(r['message'] ?? '', style: const TextStyle(fontSize: 13, height: 1.4)),
                      ]),
                    ),
                  ),
                )),
              ],
              const SizedBox(height: 80),
            ],
          ),
        ),
        Container(
          decoration: BoxDecoration(color: AppColors.surface, boxShadow: [BoxShadow(color: Colors.black.withValues(alpha: 0.05), blurRadius: 8, offset: const Offset(0, -2))]),
          padding: EdgeInsets.only(left: 16, right: 16, top: 12, bottom: MediaQuery.of(context).viewInsets.bottom + 12),
          child: Row(children: [
            Expanded(
              child: TextFormField(controller: _replyCtrl, decoration: const InputDecoration(hintText: 'Type a reply...', isDense: true, contentPadding: EdgeInsets.symmetric(horizontal: 16, vertical: 12)), maxLines: 3, minLines: 1),
            ),
            const SizedBox(width: 8),
            IconButton(
              onPressed: _sending ? null : _sendReply,
              icon: _sending ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(strokeWidth: 2)) : const Icon(Icons.send, color: AppColors.primary),
            ),
          ]),
        ),
      ]),
    );
  }

  Widget _priorityBadge(String priority) {
    Color c;
    switch (priority.toLowerCase()) {
      case 'high': c = AppColors.danger; break;
      case 'medium': c = AppColors.warning; break;
      default: c = AppColors.success;
    }
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
      decoration: BoxDecoration(color: c.withValues(alpha: 0.15), borderRadius: BorderRadius.circular(10)),
      child: Text(priority, style: TextStyle(color: c, fontSize: 10, fontWeight: FontWeight.w700)),
    );
  }
}
