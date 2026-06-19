import 'package:flutter/material.dart';
import '../../shared/theme/app_colors.dart';
import '../../shared/widgets/status_badge.dart';
import '../../shared/widgets/search_bar_widget.dart';
import '../../shared/widgets/empty_state.dart';
import '../../widgets/glass_card.dart';
import '../../core/api_service.dart';

class FeedbackScreen extends StatefulWidget {
  const FeedbackScreen({super.key});
  @override State<FeedbackScreen> createState() => _FeedbackScreenState();
}

class _FeedbackScreenState extends State<FeedbackScreen> {
  List<dynamic> _items = [];
  bool _loading = true;
  int _tabIndex = 0;

  @override void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    setState(() => _loading = true);
    try {
      final path = _tabIndex == 0 ? '/support/feedback' : '/support/inbox';
      final d = await ApiService.get(path);
      if (mounted) setState(() { _items = d['data'] ?? d as List? ?? []; _loading = false; });
    } catch (_) { if (mounted) setState(() => _loading = false); }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(title: const Text('Feedback & Support')),
      body: Column(children: [
        Padding(
          padding: const EdgeInsets.all(16),
          child: Row(children: [
            _tabChip('My Feedback', 0),
            const SizedBox(width: 8),
            _tabChip('Support Inbox', 1),
          ]),
        ),
        Expanded(
          child: _loading
              ? const Center(child: CircularProgressIndicator())
              : _items.isEmpty
                  ? const EmptyState(icon: Icons.feedback_outlined, title: 'No Feedback', subtitle: 'Tap + to submit feedback')
                  : RefreshIndicator(
                      onRefresh: _load,
                      child: ListView.builder(
                        padding: const EdgeInsets.fromLTRB(16, 0, 16, 80),
                        itemCount: _items.length,
                        itemBuilder: (context, i) {
                          final item = _items[i];
                          return Padding(
                            padding: const EdgeInsets.only(bottom: 10),
                            child: GlassCard(
                              child: InkWell(
                                borderRadius: BorderRadius.circular(14),
                                onTap: () => Navigator.pushNamed(context, '/support/feedback/detail', arguments: item),
                                child: Padding(
                                  padding: const EdgeInsets.all(14),
                                  child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                                    Row(children: [
                                      Expanded(child: Text(item['subject'] ?? '', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 15))),
                                      Row(mainAxisSize: MainAxisSize.min, children: [
                                        StatusBadge.fromStatus(item['status'] ?? 'open'),
                                        const SizedBox(width: 4),
                                        _priorityBadge(item['priority'] ?? 'medium'),
                                      ]),
                                    ]),
                                    const SizedBox(height: 6),
                                    Text(item['message'] ?? '', maxLines: 2, overflow: TextOverflow.ellipsis, style: const TextStyle(color: AppColors.textSec, fontSize: 13)),
                                    const SizedBox(height: 6),
                                    Text(item['created_at'] ?? '', style: const TextStyle(color: AppColors.textLight, fontSize: 11)),
                                  ]),
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
        onPressed: () => Navigator.pushNamed(context, '/support/feedback/new'),
        backgroundColor: AppColors.primary,
        child: const Icon(Icons.add, color: Colors.white),
      ),
    );
  }

  Widget _tabChip(String label, int index) {
    final sel = _tabIndex == index;
    return GestureDetector(
      onTap: () => setState(() { _tabIndex = index; _load(); }),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 10),
        decoration: BoxDecoration(
          color: sel ? AppColors.primary : Colors.white,
          borderRadius: BorderRadius.circular(20),
          border: Border.all(color: sel ? AppColors.primary : AppColors.border),
        ),
        child: Text(label, style: TextStyle(color: sel ? Colors.white : AppColors.textSec, fontWeight: FontWeight.w600, fontSize: 13)),
      ),
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
