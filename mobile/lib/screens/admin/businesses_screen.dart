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

class AdminBusinessesScreen extends StatefulWidget {
  const AdminBusinessesScreen({super.key});
  @override State<AdminBusinessesScreen> createState() => _AdminBusinessesScreenState();
}

class _AdminBusinessesScreenState extends State<AdminBusinessesScreen> {
  List<Map<String, dynamic>> _businesses = [];
  List<Map<String, dynamic>> _filtered = [];
  bool _loading = true;
  String? _error;
  String _search = '';
  String _statusFilter = '';
  final _searchCtrl = TextEditingController();

  final _statuses = ['', 'verified', 'pending', 'rejected', 'suspended'];
  final _statusLabels = ['All', 'Verified', 'Pending', 'Rejected', 'Suspended'];

  @override
  void initState() { super.initState(); _load(); }
  @override
  void dispose() { _searchCtrl.dispose(); super.dispose(); }

  void _showDetail(Map<String, dynamic> business) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (_) => _BusinessDetailSheet(business: business, onAction: _load),
    );
  }

  Future<void> _load() async {
    setState(() { _loading = true; _error = null; });
    try {
      final data = await ApiService.get('/api/admin/businesses');
      final list = data is List ? data.map((e) => Map<String, dynamic>.from(e)).toList() : <Map<String, dynamic>>[];
      setState(() { _businesses = list; _filter(); _loading = false; });
    } catch (e) { setState(() { _error = e.toString(); _loading = false; }); }
  }

  void _filter() {
    setState(() {
      _filtered = _businesses.where((b) {
        if (_statusFilter.isNotEmpty && b['status'] != _statusFilter) return false;
        if (_search.isNotEmpty) {
          final q = _search.toLowerCase();
          if (!(b['business_name'] ?? '').toString().toLowerCase().contains(q) &&
              !(b['owner_name'] ?? '').toString().toLowerCase().contains(q) &&
              !(b['email'] ?? '').toString().toLowerCase().contains(q)) return false;
        }
        return true;
      }).toList();
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(title: const Text('Business Management')),
      body: Column(
        children: [
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 12, 16, 0),
            child: AppSearchBar(hint: 'Search business, owner, email...', controller: _searchCtrl, onChanged: (v) { _search = v; _filter(); }),
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
        icon: Icons.business_outlined,
        title: 'No Businesses Found',
        subtitle: _search.isNotEmpty ? 'Try a different search term' : null,
      );
    }
    return RefreshIndicator(
      onRefresh: _load,
      child: ListView.builder(
        padding: const EdgeInsets.fromLTRB(16, 0, 16, 100),
        itemCount: _filtered.length,
        itemBuilder: (_, i) => _businessCard(_filtered[i]),
      ),
    );
  }

  Widget _businessCard(Map<String, dynamic> b) {
    final status = b['status'] ?? 'pending';
    return Padding(
      padding: const EdgeInsets.only(bottom: 10),
      child: GlassCard(
        onTap: () => _showDetail(b),
        child: Row(
          children: [
            Container(
              width: 44, height: 44,
              decoration: BoxDecoration(color: AppColors.primaryLt, borderRadius: BorderRadius.circular(12)),
              child: const Icon(Icons.store_rounded, size: 22, color: AppColors.primary),
            ),
            const SizedBox(width: 14),
            Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
              Text(b['business_name'] ?? '', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 15, color: AppColors.textPri)),
              const SizedBox(height: 2),
              Text(b['owner_name'] ?? '', style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
              Text(b['email'] ?? '', style: const TextStyle(color: AppColors.textSec, fontSize: 11)),
            ])),
            const SizedBox(width: 8),
            StatusBadge.fromStatus(status),
          ],
        ),
      ),
    );
  }
}

class _BusinessDetailSheet extends StatefulWidget {
  final Map<String, dynamic> business;
  final VoidCallback onAction;
  const _BusinessDetailSheet({required this.business, required this.onAction});
  @override State<_BusinessDetailSheet> createState() => _BusinessDetailSheetState();
}

class _BusinessDetailSheetState extends State<_BusinessDetailSheet> {
  bool _actioning = false;

  Future<void> _updateStatus(String status) async {
    final label = {'verified': 'Verify', 'rejected': 'Reject', 'suspended': 'Suspend'}[status] ?? status;
    final confirmed = await ConfirmDialog.show(context,
      title: '$label Business',
      message: '${label} "${widget.business['business_name']}"?',
    );
    if (confirmed != true) return;
    setState(() => _actioning = true);
    try {
      await ApiService.post('/api/admin/businesses/${widget.business['id']}/status', {'status': status});
      if (mounted) { ToastHelper.show(context, message: 'Business $label\'d'); widget.onAction(); Navigator.pop(context); }
    } catch (e) { if (mounted) ToastHelper.show(context, message: 'Action failed', error: true); setState(() => _actioning = false); }
  }

  @override
  Widget build(BuildContext context) {
    final b = widget.business;
    final status = b['status'] ?? 'pending';
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
                child: const Icon(Icons.store_rounded, size: 26, color: AppColors.primary),
              ),
              const SizedBox(width: 14),
              Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                Text(b['business_name'] ?? '', style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w700)),
                StatusBadge.fromStatus(status),
              ])),
              IconButton(icon: const Icon(Icons.close), onPressed: () => Navigator.pop(context)),
            ]),
            const SizedBox(height: 20),
            _detailRow(Icons.person_outline, 'Owner', b['owner_name'] ?? ''),
            _detailRow(Icons.email_outlined, 'Email', b['email'] ?? ''),
            _detailRow(Icons.phone_outlined, 'Phone', b['phone'] ?? '-'),
            _detailRow(Icons.category_outlined, 'Type', b['business_type'] ?? '-'),
            _detailRow(Icons.location_on_outlined, 'Location', b['city'] != null ? '${b['city']}, ${b['country'] ?? ''}' : '-'),
            _detailRow(Icons.calendar_today_outlined, 'Created', b['created_at']?.toString().substring(0, 10) ?? ''),
            const SizedBox(height: 24),
            if (_actioning)
              const Center(child: CircularProgressIndicator())
            else ...[
              if (status == 'pending' || status == 'rejected' || status == 'suspended')
                SizedBox(
                  width: double.infinity, height: 48,
                  child: ElevatedButton.icon(
                    onPressed: () => _updateStatus('verified'),
                    icon: const Icon(Icons.check_circle_outline),
                    label: const Text('Verify Business'),
                    style: ElevatedButton.styleFrom(backgroundColor: AppColors.success, foregroundColor: Colors.white),
                  ),
                ),
              if (status == 'pending' || status == 'verified') ...[
                const SizedBox(height: 10),
                SizedBox(
                  width: double.infinity, height: 48,
                  child: OutlinedButton.icon(
                    onPressed: () => _updateStatus('rejected'),
                    icon: const Icon(Icons.cancel_outlined, color: AppColors.danger),
                    label: const Text('Reject', style: TextStyle(color: AppColors.danger)),
                    style: OutlinedButton.styleFrom(side: const BorderSide(color: AppColors.danger)),
                  ),
                ),
              ],
              if (status == 'verified' || status == 'pending') ...[
                const SizedBox(height: 10),
                SizedBox(
                  width: double.infinity, height: 48,
                  child: OutlinedButton.icon(
                    onPressed: () => _updateStatus('suspended'),
                    icon: const Icon(Icons.pause_circle_outline, color: AppColors.warning),
                    label: const Text('Suspend', style: TextStyle(color: AppColors.warning)),
                    style: OutlinedButton.styleFrom(side: const BorderSide(color: AppColors.warning)),
                  ),
                ),
              ],
            ],
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
