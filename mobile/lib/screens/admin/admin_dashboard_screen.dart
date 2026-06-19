import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../../core/api_service.dart';
import '../../shared/theme/app_colors.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/shimmer_loading.dart';
import '../../widgets/section_header.dart';
import '../../widgets/stat_card.dart';
import '../../widgets/chart_widgets.dart';
import '../../constants/app_constants.dart';

class AdminDashboardScreen extends StatefulWidget {
  const AdminDashboardScreen({super.key});
  @override State<AdminDashboardScreen> createState() => _AdminDashboardScreenState();
}

class _AdminDashboardScreenState extends State<AdminDashboardScreen> {
  Map<String, dynamic>? _data;
  bool _loading = true;
  String? _error;
  final _fmt = NumberFormat('#,##0');
  final _curFmt = NumberFormat('#,##0.00');

  @override
  void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    setState(() { _loading = true; _error = null; });
    try {
      final d = await ApiService.get('/api/dashboard/admin');
      setState(() { _data = d is Map ? d : {}; _loading = false; });
    } catch (e) { setState(() { _error = e.toString(); _loading = false; }); }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        title: const Text('Admin Dashboard'),
        actions: [IconButton(icon: const Icon(Icons.refresh), onPressed: _load)],
      ),
      body: _loading
          ? const ShimmerLoading(itemCount: 10)
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
                  child: SingleChildScrollView(
                    padding: const EdgeInsets.only(bottom: 100),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        _buildHeader(),
                        const SizedBox(height: 8),
                        _buildStatGrid(),
                        const SizedBox(height: 20),
                        _buildCharts(),
                        const SizedBox(height: 12),
                        _buildRecentRegistrations(),
                        const SizedBox(height: 12),
                        _buildSystemAlerts(),
                        const SizedBox(height: 12),
                        _buildQuickLinks(),
                      ],
                    ),
                  ),
                ),
    );
  }

  Widget _buildHeader() {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.fromLTRB(20, 48, 20, 24),
      decoration: BoxDecoration(
        gradient: const LinearGradient(colors: [Color(0xFF0F1B2D), Color(0xFF1E3A5F)], begin: Alignment.topLeft, end: Alignment.bottomRight),
        borderRadius: const BorderRadius.vertical(bottom: Radius.circular(32)),
        boxShadow: [BoxShadow(color: const Color(0xFF0F1B2D).withValues(alpha: 0.4), blurRadius: 30, offset: const Offset(0, 10))],
      ),
      child: SafeArea(
        bottom: false,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Container(
                  padding: const EdgeInsets.all(8),
                  decoration: BoxDecoration(color: Colors.white.withValues(alpha: 0.15), borderRadius: BorderRadius.circular(12)),
                  child: const Icon(Icons.admin_panel_settings_rounded, color: Colors.white, size: 24),
                ),
                const Spacer(),
              ],
            ),
            const SizedBox(height: 16),
            const Text('Admin Panel', style: TextStyle(color: Colors.white, fontSize: 24, fontWeight: FontWeight.w800, letterSpacing: -0.5)),
            const SizedBox(height: 4),
            Text('System overview & management', style: TextStyle(color: Colors.white.withValues(alpha: 0.7), fontSize: 13)),
          ],
        ),
      ),
    );
  }

  Widget _buildStatGrid() {
    final s = _data?['stats'] ?? {};
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16),
      child: GridView.count(
        crossAxisCount: 2,
        shrinkWrap: true,
        physics: const NeverScrollableScrollPhysics(),
        mainAxisSpacing: 12,
        crossAxisSpacing: 12,
        childAspectRatio: 1.5,
        children: [
          StatCard(icon: Icons.people_rounded, value: _fmt.format(s['total_users'] ?? 0), label: 'Total Users', color: AppColors.primary),
          StatCard(icon: Icons.business_rounded, value: _fmt.format(s['active_businesses'] ?? 0), label: 'Active Businesses', color: AppColors.success),
          StatCard(icon: Icons.attach_money_rounded, value: '${AppConstants.currency}${_curFmt.format(s['mrr'] ?? 0)}', label: 'MRR', color: AppColors.warning),
          StatCard(icon: Icons.verified_user_rounded, value: _fmt.format(s['pending_verifications'] ?? 0), label: 'Pending Verifications', color: AppColors.purple),
        ],
      ),
    );
  }

  Widget _buildCharts() {
    final userGrowth = _data?['user_growth'] is List ? _data!['user_growth'] as List : <Map<String, dynamic>>[];
    final revenue = _data?['monthly_revenue'] is List ? _data!['monthly_revenue'] as List : <Map<String, dynamic>>[];
    return Column(
      children: [
        if (userGrowth.isNotEmpty) Padding(
          padding: const EdgeInsets.symmetric(horizontal: 16),
          child: GlassCard(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text('User Growth', style: TextStyle(fontSize: 15, fontWeight: FontWeight.w700, color: AppColors.textPri)),
                const SizedBox(height: 12),
                SizedBox(height: 180, child: MiniLineChart(data: userGrowth.map((e) => {'label': e['month'] ?? '', 'value': (e['count'] ?? 0).toDouble()}).toList())),
              ],
            ),
          ),
        ),
        const SizedBox(height: 12),
        if (revenue.isNotEmpty) Padding(
          padding: const EdgeInsets.symmetric(horizontal: 16),
          child: GlassCard(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text('Monthly Revenue', style: TextStyle(fontSize: 15, fontWeight: FontWeight.w700, color: AppColors.textPri)),
                const SizedBox(height: 12),
                SizedBox(height: 180, child: BarChartWidget(data: revenue.map((e) => {'label': e['month'] ?? '', 'value': (e['total'] ?? 0).toDouble()}).toList())),
              ],
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildRecentRegistrations() {
    final registrations = _data?['recent_registrations'] is List ? _data!['recent_registrations'] as List : [];
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Padding(padding: EdgeInsets.symmetric(horizontal: 16), child: SectionHeader(title: 'Recent Registrations')),
        const SizedBox(height: 4),
        ...List.generate(registrations.length > 5 ? 5 : registrations.length, (i) {
          final u = registrations[i];
          return Padding(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
            child: GlassCard(
              child: Row(
                children: [
                  Container(
                    width: 40, height: 40,
                    decoration: BoxDecoration(color: AppColors.primaryLt, borderRadius: BorderRadius.circular(10)),
                    child: Center(child: Text(
                      (u['name'] ?? '?').toString().split(' ').map((w) => w.isNotEmpty ? w[0] : '').take(2).join().toUpperCase(),
                      style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w700, color: AppColors.primary),
                    )),
                  ),
                  const SizedBox(width: 12),
                  Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                    Text(u['name'] ?? '', style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 14)),
                    Text(u['email'] ?? '', style: const TextStyle(color: AppColors.textSec, fontSize: 11)),
                  ])),
                  Text(u['created_at'] != null ? DateTime.parse(u['created_at']).toLocal().toString().substring(0, 10) : '', style: const TextStyle(color: AppColors.textSec, fontSize: 11)),
                ],
              ),
            ),
          );
        }),
      ],
    );
  }

  Widget _buildSystemAlerts() {
    final alerts = _data?['system_alerts'] is List ? _data!['system_alerts'] as List : [];
    if (alerts.isEmpty) return const SizedBox.shrink();
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Padding(padding: EdgeInsets.symmetric(horizontal: 16), child: SectionHeader(title: 'System Alerts')),
        const SizedBox(height: 4),
        ...List.generate(alerts.length > 3 ? 3 : alerts.length, (i) {
          final a = alerts[i];
          final isWarning = (a['type'] ?? '').toString().contains('warning');
          final isError = (a['type'] ?? '').toString().contains('error');
          return Padding(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
            child: GlassCard(
              child: Row(
                children: [
                  Icon(isError ? Icons.error_rounded : isWarning ? Icons.warning_amber_rounded : Icons.info_rounded,
                    color: isError ? AppColors.danger : isWarning ? AppColors.warning : AppColors.info, size: 20),
                  const SizedBox(width: 12),
                  Expanded(child: Text(a['message'] ?? '', style: const TextStyle(fontSize: 13))),
                ],
              ),
            ),
          );
        }),
      ],
    );
  }

  Widget _buildQuickLinks() {
    return Padding(
      padding: const EdgeInsets.fromLTRB(16, 8, 16, 0),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const SectionHeader(title: 'Quick Links'),
          const SizedBox(height: 8),
          Row(
            children: [
              Expanded(child: _linkCard(Icons.people_rounded, 'Users', AppColors.primary, () {})),
              const SizedBox(width: 12),
              Expanded(child: _linkCard(Icons.business_rounded, 'Businesses', AppColors.success, () {})),
              const SizedBox(width: 12),
              Expanded(child: _linkCard(Icons.subscriptions_rounded, 'Subscriptions', AppColors.warning, () {})),
            ],
          ),
        ],
      ),
    );
  }

  Widget _linkCard(IconData icon, String label, Color color, VoidCallback onTap) {
    return GestureDetector(
      onTap: onTap,
      child: GlassCard(
        padding: const EdgeInsets.symmetric(vertical: 16, horizontal: 12),
        child: Column(
          children: [
            Container(
              width: 40, height: 40,
              decoration: BoxDecoration(color: color.withValues(alpha: 0.12), borderRadius: BorderRadius.circular(10)),
              child: Icon(icon, color: color, size: 22),
            ),
            const SizedBox(height: 8),
            Text(label, style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w600, color: AppColors.textPri)),
          ],
        ),
      ),
    );
  }
}
