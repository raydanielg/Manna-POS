import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../../core/api_service.dart';
import '../../shared/theme/app_colors.dart';
import '../../shared/utils/formatters.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/status_badge.dart';
import '../../widgets/shimmer_loading.dart';
import '../../widgets/empty_state.dart';
import '../../widgets/confirm_dialog.dart';
import '../../widgets/toast_helper.dart';
import '../../widgets/stat_card.dart';
import '../../constants/app_constants.dart';

class AdminSubscriptionsScreen extends StatefulWidget {
  const AdminSubscriptionsScreen({super.key});
  @override State<AdminSubscriptionsScreen> createState() => _AdminSubscriptionsScreenState();
}

class _AdminSubscriptionsScreenState extends State<AdminSubscriptionsScreen> with SingleTickerProviderStateMixin {
  late TabController _tabCtrl;
  List<Map<String, dynamic>> _subscriptions = [];
  List<Map<String, dynamic>> _filtered = [];
  bool _loading = true;
  String? _error;
  Map<String, dynamic>? _stats;
  final _curFmt = NumberFormat('#,##0.00');

  final _tabs = ['All', 'Active', 'Expired', 'Trial', 'Pending'];
  final _tabStatuses = ['', 'active', 'expired', 'trial', 'pending'];

  @override
  void initState() {
    super.initState();
    _tabCtrl = TabController(length: _tabs.length, vsync: this);
    _tabCtrl.addListener(() { if (!_tabCtrl.indexIsChanging) setState(() {}); });
    _load();
  }

  @override
  void dispose() { _tabCtrl.dispose(); super.dispose(); }

  Future<void> _load() async {
    setState(() { _loading = true; _error = null; });
    try {
      final data = await ApiService.get('/api/admin/subscriptions');
      if (data is Map) {
        setState(() {
          _subscriptions = (data['subscriptions'] is List ? data['subscriptions'] : []).map((e) => Map<String, dynamic>.from(e)).toList();
          _stats = data['stats'] is Map ? Map<String, dynamic>.from(data['stats']) : null;
          _loading = false;
        });
      } else {
        setState(() { _subscriptions = []; _loading = false; });
      }
    } catch (e) { setState(() { _error = e.toString(); _loading = false; }); }
  }

  Future<void> _cancelSub(dynamic id) async {
    final confirmed = await ConfirmDialog.show(context, title: 'Cancel Subscription', message: 'Cancel this subscription? This action cannot be undone.');
    if (confirmed != true) return;
    try {
      await ApiService.post('/api/admin/subscriptions/$id/cancel', {});
      if (mounted) { ToastHelper.show(context, message: 'Subscription cancelled'); _load(); }
    } catch (e) { if (mounted) ToastHelper.show(context, message: 'Failed to cancel', error: true); }
  }

  void _showDetail(Map<String, dynamic> sub) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (_) => _SubscriptionDetailSheet(subscription: sub, onCancel: () => _cancelSub(sub['id']), onAction: _load),
    );
  }

  @override
  Widget build(BuildContext context) {
    final currentTab = _tabCtrl.index;
    final statusFilter = _tabStatuses[currentTab];
    final filtered = statusFilter.isEmpty ? _subscriptions : _subscriptions.where((s) => s['status'] == statusFilter).toList();

    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        title: const Text('Subscriptions'),
        bottom: TabBar(
          controller: _tabCtrl,
          isScrollable: true,
          labelColor: Theme.of(context).colorScheme.primary,
          unselectedLabelColor: AppColors.textSec,
          indicatorColor: Theme.of(context).colorScheme.primary,
          tabs: _tabs.map((t) => Tab(text: t)).toList(),
        ),
      ),
      body: _loading
          ? const ShimmerLoading()
          : _error != null
              ? Center(child: Column(mainAxisSize: MainAxisSize.min, children: [
                  const Icon(Icons.error_outline, size: 48, color: AppColors.error),
                  const SizedBox(height: 12),
                  Text(_error!, style: const TextStyle(color: AppColors.textSec)),
                  const SizedBox(height: 16),
                  ElevatedButton(onPressed: _load, child: const Text('Retry')),
                ]))
              : RefreshIndicator(
                  onRefresh: _load,
                  child: ListView(
                    padding: const EdgeInsets.fromLTRB(16, 12, 16, 100),
                    children: [
                      if (_stats != null) _buildStatsRow(),
                      const SizedBox(height: 16),
                      if (filtered.isEmpty)
                        EmptyState(icon: Icons.subscriptions_outlined, title: 'No Subscriptions', subtitle: 'No subscriptions match this filter')
                      else
                        ...filtered.map((s) => Padding(
                          padding: const EdgeInsets.only(bottom: 10),
                          child: _subscriptionCard(s),
                        )),
                    ],
                  ),
                ),
    );
  }

  Widget _buildStatsRow() {
    return Row(
      children: [
        Expanded(child: StatCard(icon: Icons.subscriptions_rounded, value: '${_stats?['total'] ?? 0}', label: 'Total', color: AppColors.primary)),
        const SizedBox(width: 8),
        Expanded(child: StatCard(icon: Icons.check_circle_rounded, value: '${_stats?['active'] ?? 0}', label: 'Active', color: AppColors.success)),
        const SizedBox(width: 8),
        Expanded(child: StatCard(icon: Icons.science_rounded, value: '${_stats?['trial'] ?? 0}', label: 'Trial', color: AppColors.purple)),
      ],
    );
  }

  Widget _subscriptionCard(Map<String, dynamic> s) {
    return GlassCard(
      onTap: () => _showDetail(s),
      child: Row(
        children: [
          Container(
            width: 44, height: 44,
            decoration: BoxDecoration(color: AppColors.primaryLt, borderRadius: BorderRadius.circular(12)),
            child: const Icon(Icons.subscriptions_rounded, size: 22, color: AppColors.primary),
          ),
          const SizedBox(width: 14),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(s['business_name'] ?? s['user_name'] ?? '', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 15, color: AppColors.textPri)),
                const SizedBox(height: 2),
                Text('Plan: ${s['plan_name'] ?? 'N/A'}', style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
                const SizedBox(height: 2),
                Text('${fmtDate(s['start_date'])} - ${fmtDate(s['end_date']) ?? 'N/A'}', style: const TextStyle(color: AppColors.textSec, fontSize: 11)),
              ],
            ),
          ),
          const SizedBox(width: 8),
          Column(crossAxisAlignment: CrossAxisAlignment.end, children: [
            StatusBadge.fromStatus(s['status'] ?? ''),
            const SizedBox(height: 4),
            Text('${AppConstants.currency}${_curFmt.format(s['amount'] ?? 0)}', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 13, color: AppColors.primary)),
          ]),
        ],
      ),
    );
  }
}

class _SubscriptionDetailSheet extends StatelessWidget {
  final Map<String, dynamic> subscription;
  final VoidCallback onCancel;
  final VoidCallback onAction;
  const _SubscriptionDetailSheet({required this.subscription, required this.onCancel, required this.onAction});

  @override
  Widget build(BuildContext context) {
    final s = subscription;
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
                child: const Icon(Icons.subscriptions_rounded, size: 26, color: AppColors.primary),
              ),
              const SizedBox(width: 14),
              Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                Text(s['business_name'] ?? s['user_name'] ?? '', style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w700)),
                const SizedBox(height: 4),
                StatusBadge.fromStatus(s['status'] ?? ''),
              ])),
              IconButton(icon: const Icon(Icons.close), onPressed: () => Navigator.pop(context)),
            ]),
            const SizedBox(height: 20),
            _row('Plan', s['plan_name'] ?? '-'),
            _row('Amount', '${AppConstants.currency}${NumberFormat('#,##0.00').format(s['amount'] ?? 0)}'),
            _row('Start Date', fmtDate(s['start_date'])),
            _row('End Date', fmtDate(s['end_date'])),
            _row('Billing Cycle', s['billing_cycle'] ?? '-'),
            _row('Status', s['status'] ?? '-'),
            const SizedBox(height: 24),
            SizedBox(
              width: double.infinity, height: 48,
              child: ElevatedButton.icon(
                onPressed: s['status'] == 'active' ? onCancel : null,
                icon: const Icon(Icons.cancel_outlined),
                label: const Text('Cancel Subscription'),
                style: ElevatedButton.styleFrom(backgroundColor: AppColors.danger, foregroundColor: Colors.white),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _row(String label, String value) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 10),
      child: Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
        Text(label, style: const TextStyle(color: AppColors.textSec, fontSize: 13)),
        Text(value, style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 14)),
      ]),
    );
  }
}
