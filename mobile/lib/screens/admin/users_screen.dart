import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../../core/api_service.dart';
import '../../shared/theme/app_colors.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/status_badge.dart';
import '../../widgets/search_bar_widget.dart';
import '../../widgets/shimmer_loading.dart';
import '../../widgets/empty_state.dart';
import '../../widgets/confirm_dialog.dart';
import '../../widgets/toast_helper.dart';

class AdminUsersScreen extends StatefulWidget {
  const AdminUsersScreen({super.key});
  @override State<AdminUsersScreen> createState() => _AdminUsersScreenState();
}

class _AdminUsersScreenState extends State<AdminUsersScreen> {
  List<Map<String, dynamic>> _users = [];
  List<Map<String, dynamic>> _filtered = [];
  bool _loading = true;
  String? _error;
  String _search = '';
  String _statusFilter = '';
  final _searchCtrl = TextEditingController();

  final _statuses = ['', 'active', 'blocked', 'pending_verification'];
  final _statusLabels = ['All', 'Active', 'Blocked', 'Pending Verification'];

  @override
  void initState() { super.initState(); _load(); }
  @override
  void dispose() { _searchCtrl.dispose(); super.dispose(); }

  void _showDetail(Map<String, dynamic> user) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (_) => _UserDetailSheet(user: user, onAction: _load),
    );
  }

  Future<void> _load() async {
    setState(() { _loading = true; _error = null; });
    try {
      final data = await ApiService.get('/api/admin/users');
      final list = data is List ? data.map((e) => Map<String, dynamic>.from(e)).toList() : <Map<String, dynamic>>[];
      setState(() { _users = list; _filter(); _loading = false; });
    } catch (e) { setState(() { _error = e.toString(); _loading = false; }); }
  }

  void _filter() {
    setState(() {
      _filtered = _users.where((u) {
        if (_statusFilter.isNotEmpty) {
          final status = u['status'] ?? 'active';
          if (status != _statusFilter) return false;
        }
        if (_search.isNotEmpty) {
          final q = _search.toLowerCase();
          final name = (u['name'] ?? '').toString().toLowerCase();
          final email = (u['email'] ?? '').toString().toLowerCase();
          final business = (u['business_name'] ?? '').toString().toLowerCase();
          if (!name.contains(q) && !email.contains(q) && !business.contains(q)) return false;
        }
        return true;
      }).toList();
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(title: const Text('User Management')),
      body: Column(
        children: [
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 12, 16, 0),
            child: AppSearchBar(hint: 'Search by name, email, business...', controller: _searchCtrl, onChanged: (v) { _search = v; _filter(); }),
          ),
          const SizedBox(height: 8),
          FilterChipRow(
            labels: _statusLabels,
            selected: _statusLabels[_statuses.indexOf(_statusFilter)],
            onSelected: (l) { setState(() => _statusFilter = _statuses[_statusLabels.indexOf(l)]); _filter(); },
          ),
          const SizedBox(height: 4),
          Expanded(child: _buildContent()),
        ],
      ),
    );
  }

  Widget _buildContent() {
    if (_loading) return const ShimmerLoading();
    if (_error != null) {
      return Center(child: Column(mainAxisSize: MainAxisSize.min, children: [
        const Icon(Icons.error_outline, size: 48, color: AppColors.error),
        const SizedBox(height: 12),
        Text(_error!, style: const TextStyle(color: AppColors.textSec)),
        const SizedBox(height: 16),
        ElevatedButton(onPressed: _load, child: const Text('Retry')),
      ]));
    }
    if (_filtered.isEmpty) {
      return EmptyState(
        icon: Icons.people_outline,
        title: 'No Users Found',
        subtitle: _search.isNotEmpty ? 'Try a different search term' : null,
      );
    }
    return RefreshIndicator(
      onRefresh: _load,
      child: ListView.builder(
        padding: const EdgeInsets.fromLTRB(16, 0, 16, 100),
        itemCount: _filtered.length,
        itemBuilder: (_, i) => _userCard(_filtered[i]),
      ),
    );
  }

  Widget _userCard(Map<String, dynamic> u) {
    final status = u['status'] ?? 'active';
    return Padding(
      padding: const EdgeInsets.only(bottom: 10),
      child: GlassCard(
        onTap: () => _showDetail(u),
        child: Row(
          children: [
            Container(
              width: 44, height: 44,
              decoration: BoxDecoration(color: AppColors.primaryLt, borderRadius: BorderRadius.circular(12)),
              child: Center(child: Text(
                (u['name'] ?? 'U').toString().split(' ').map((w) => w.isNotEmpty ? w[0] : '').take(2).join().toUpperCase(),
                style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w700, color: AppColors.primary),
              )),
            ),
            const SizedBox(width: 14),
            Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
              Text(u['name'] ?? '', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 15, color: AppColors.textPri)),
              const SizedBox(height: 2),
              Text(u['email'] ?? '', style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
              if (u['business_name'] != null) Text(u['business_name'], style: const TextStyle(color: AppColors.textSec, fontSize: 11)),
            ])),
            const SizedBox(width: 8),
            StatusBadge.fromStatus(status),
          ],
        ),
      ),
    );
  }
}

class _UserDetailSheet extends StatefulWidget {
  final Map<String, dynamic> user;
  final VoidCallback onAction;
  const _UserDetailSheet({required this.user, required this.onAction});
  @override State<_UserDetailSheet> createState() => _UserDetailSheetState();
}

class _UserDetailSheetState extends State<_UserDetailSheet> {
  bool _actioning = false;

  Future<void> _toggleBlock() async {
    final isBlocked = widget.user['status'] == 'blocked';
    final confirmed = await ConfirmDialog.show(context,
      title: isBlocked ? 'Unblock User' : 'Block User',
      message: isBlocked ? 'Allow "${widget.user['name']}" to access the system again?' : 'Block "${widget.user['name']}" from accessing the system?',
    );
    if (confirmed != true) return;
    setState(() => _actioning = true);
    try {
      if (isBlocked) {
        await ApiService.post('/api/admin/users/${widget.user['id']}/unblock', {});
      } else {
        await ApiService.post('/api/admin/users/${widget.user['id']}/block', {});
      }
      if (mounted) { ToastHelper.show(context, message: isBlocked ? 'User unblocked' : 'User blocked'); widget.onAction(); Navigator.pop(context); }
    } catch (e) { if (mounted) ToastHelper.show(context, message: 'Action failed', error: true); setState(() => _actioning = false); }
  }

  @override
  Widget build(BuildContext context) {
    final u = widget.user;
    return Container(
      decoration: const BoxDecoration(color: Colors.white, borderRadius: BorderRadius.vertical(top: Radius.circular(24))),
      padding: const EdgeInsets.fromLTRB(24, 16, 24, 32),
      child: SingleChildScrollView(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          mainAxisSize: MainAxisSize.min,
          children: [
            Center(child: Container(width: 40, height: 4, decoration: BoxDecoration(color: AppColors.border, borderRadius: BorderRadius.circular(4)))),
            const SizedBox(height: 16),
            Row(children: [
              Container(
                width: 52, height: 52,
                decoration: BoxDecoration(color: AppColors.primaryLt, borderRadius: BorderRadius.circular(14)),
                child: Center(child: Text(
                  (u['name'] ?? 'U').toString().split(' ').map((w) => w.isNotEmpty ? w[0] : '').take(2).join().toUpperCase(),
                  style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w700, color: AppColors.primary),
                )),
              ),
              const SizedBox(width: 14),
              Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                Text(u['name'] ?? '', style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w700)),
                Row(children: [
                  StatusBadge.fromStatus(u['status'] ?? 'active'),
                  if (u['role'] != null) ...[const SizedBox(width: 8), Text(u['role'], style: const TextStyle(color: AppColors.textSec, fontSize: 12))],
                ]),
              ])),
              IconButton(icon: const Icon(Icons.close), onPressed: () => Navigator.pop(context)),
            ]),
            const SizedBox(height: 20),
            _detailRow(Icons.email_outlined, 'Email', u['email'] ?? ''),
            _detailRow(Icons.business_outlined, 'Business', u['business_name'] ?? '-'),
            _detailRow(Icons.calendar_today_outlined, 'Member Since', u['created_at']?.toString().substring(0, 10) ?? ''),
            _detailRow(Icons.subscriptions_outlined, 'Subscription', u['subscription_plan'] ?? 'Free'),
            const SizedBox(height: 24),
            SizedBox(
              width: double.infinity,
              height: 48,
              child: ElevatedButton.icon(
                onPressed: _actioning ? null : _toggleBlock,
                icon: _actioning
                    ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(strokeWidth: 2.5, color: Colors.white))
                    : Icon(u['status'] == 'blocked' ? Icons.lock_open_rounded : Icons.lock_outline_rounded),
                label: Text(u['status'] == 'blocked' ? 'Unblock User' : 'Block User'),
                style: ElevatedButton.styleFrom(
                  backgroundColor: u['status'] == 'blocked' ? AppColors.success : AppColors.danger,
                  foregroundColor: Colors.white,
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _detailRow(IconData icon, String label, String value) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 12),
      child: Row(children: [
        Icon(icon, size: 18, color: AppColors.textSec),
        const SizedBox(width: 10),
        Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
          Text(label, style: const TextStyle(color: AppColors.textSec, fontSize: 11)),
          Text(value, style: const TextStyle(fontWeight: FontWeight.w500, fontSize: 14)),
        ])),
      ]),
    );
  }
}
