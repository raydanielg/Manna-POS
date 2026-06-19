import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../../core/api_service.dart';
import '../../shared/theme/app_colors.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/status_badge.dart';
import '../../widgets/shimmer_loading.dart';
import '../../widgets/empty_state.dart';
import '../../widgets/section_header.dart';
import '../../widgets/stat_card.dart';
import '../../widgets/chart_widgets.dart';
import '../../constants/app_constants.dart';

class ManufacturingDashboardScreen extends StatefulWidget {
  const ManufacturingDashboardScreen({super.key});
  @override State<ManufacturingDashboardScreen> createState() => _ManufacturingDashboardScreenState();
}

class _ManufacturingDashboardScreenState extends State<ManufacturingDashboardScreen> {
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
      final d = await ApiService.get('/api/dashboard/manufacturing');
      setState(() { _data = d is Map ? d : {}; _loading = false; });
    } catch (e) { setState(() { _error = e.toString(); _loading = false; }); }
  }

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    return Scaffold(
      backgroundColor: AppColors.background,
      body: _loading
          ? const ShimmerLoading(itemCount: 8)
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
                        const SizedBox(height: 20),
                        _buildRecentProduction(),
                        const SizedBox(height: 12),
                        _buildRecentRecipes(),
                        const SizedBox(height: 12),
                        _buildQuickActions(),
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
        gradient: const LinearGradient(colors: [Color(0xFF1E3A5F), Color(0xFF0F1B2D)], begin: Alignment.topLeft, end: Alignment.bottomRight),
        borderRadius: const BorderRadius.vertical(bottom: Radius.circular(32)),
        boxShadow: [BoxShadow(color: const Color(0xFF1E3A5F).withValues(alpha: 0.4), blurRadius: 30, offset: const Offset(0, 10))],
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
                  child: const Icon(Icons.precision_manufacturing_rounded, color: Colors.white, size: 24),
                ),
                const Spacer(),
                IconButton(icon: const Icon(Icons.refresh, color: Colors.white), onPressed: _load),
              ],
            ),
            const SizedBox(height: 16),
            const Text('Manufacturing', style: TextStyle(color: Colors.white, fontSize: 24, fontWeight: FontWeight.w800, letterSpacing: -0.5)),
            const SizedBox(height: 4),
            Text('Production & Recipe Management', style: TextStyle(color: Colors.white.withValues(alpha: 0.7), fontSize: 13)),
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
          StatCard(icon: Icons.menu_book_rounded, value: _fmt.format(s['total_recipes'] ?? 0), label: 'Total Recipes', color: AppColors.primary),
          StatCard(icon: Icons.play_circle_rounded, value: _fmt.format(s['active_runs'] ?? 0), label: 'Active Runs', color: AppColors.warning),
          StatCard(icon: Icons.check_circle_rounded, value: _fmt.format(s['completed_today'] ?? 0), label: 'Completed Today', color: AppColors.success),
          StatCard(icon: Icons.inventory_rounded, value: _fmt.format(s['total_output'] ?? 0), label: 'Total Output (units)', color: AppColors.purple),
        ],
      ),
    );
  }

  Widget _buildCharts() {
    final statusDist = _data?['production_status_distribution'] is List ? _data!['production_status_distribution'] as List : <Map<String, dynamic>>[];
    final monthly = _data?['monthly_production'] is List ? _data!['monthly_production'] as List : <Map<String, dynamic>>[];
    return Column(
      children: [
        if (statusDist.isNotEmpty) Padding(
          padding: const EdgeInsets.symmetric(horizontal: 16),
          child: GlassCard(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text('Production Status', style: TextStyle(fontSize: 15, fontWeight: FontWeight.w700, color: AppColors.textPri)),
                const SizedBox(height: 12),
                SizedBox(height: 180, child: DonutChartWidget(data: statusDist.map((e) => {'label': e['label'] ?? '', 'value': (e['value'] ?? 0).toDouble(), 'color': _statusColor(e['label'])}).toList())),
              ],
            ),
          ),
        ),
        const SizedBox(height: 12),
        if (monthly.isNotEmpty) Padding(
          padding: const EdgeInsets.symmetric(horizontal: 16),
          child: GlassCard(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text('Monthly Production Output', style: TextStyle(fontSize: 15, fontWeight: FontWeight.w700, color: AppColors.textPri)),
                const SizedBox(height: 12),
                SizedBox(height: 180, child: BarChartWidget(data: monthly.map((e) => {'label': e['month'] ?? '', 'value': (e['total'] ?? 0).toDouble()}).toList())),
              ],
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildRecentProduction() {
    final runs = _data?['recent_production_runs'] is List ? _data!['recent_production_runs'] as List : [];
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        SectionHeader(title: 'Recent Production Runs', actionLabel: 'View All', onAction: () {}),
        const SizedBox(height: 4),
        ...List.generate(runs.length > 3 ? 3 : runs.length, (i) {
          final r = runs[i];
          return Padding(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
            child: GlassCard(
              child: Row(
                children: [
                  Container(
                    width: 40, height: 40,
                    decoration: BoxDecoration(color: AppColors.primaryLt, borderRadius: BorderRadius.circular(10)),
                    child: const Icon(Icons.production_quantity_limits_rounded, size: 20, color: AppColors.primary),
                  ),
                  const SizedBox(width: 12),
                  Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                    Text(r['recipe_name'] ?? '', style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 14)),
                    Text('Batch: ${r['batch_number'] ?? ''}', style: const TextStyle(color: AppColors.textSec, fontSize: 11)),
                  ])),
                  StatusBadge.fromStatus(r['status'] ?? ''),
                ],
              ),
            ),
          );
        }),
      ],
    );
  }

  Widget _buildRecentRecipes() {
    final recipes = _data?['recent_recipes'] is List ? _data!['recent_recipes'] as List : [];
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        SectionHeader(title: 'Recent Recipes', actionLabel: 'View All', onAction: () {}),
        const SizedBox(height: 4),
        ...List.generate(recipes.length > 3 ? 3 : recipes.length, (i) {
          final r = recipes[i];
          return Padding(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
            child: GlassCard(
              child: Row(
                children: [
                  Container(
                    width: 40, height: 40,
                    decoration: BoxDecoration(color: AppColors.accent.withValues(alpha: 0.12), borderRadius: BorderRadius.circular(10)),
                    child: const Icon(Icons.menu_book_rounded, size: 20, color: AppColors.accent),
                  ),
                  const SizedBox(width: 12),
                  Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                    Text(r['name'] ?? '', style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 14)),
                    Text('${r['ingredients_count'] ?? 0} ingredients', style: const TextStyle(color: AppColors.textSec, fontSize: 11)),
                  ])),
                  Text('${AppConstants.currency} ${_curFmt.format(r['production_cost'] ?? 0)}', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 13, color: AppColors.primary)),
                ],
              ),
            ),
          );
        }),
      ],
    );
  }

  Widget _buildQuickActions() {
    return Padding(
      padding: const EdgeInsets.fromLTRB(16, 8, 16, 0),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const SectionHeader(title: 'Quick Actions'),
          const SizedBox(height: 8),
          Row(
            children: [
              Expanded(child: _actionCard(Icons.add_circle_rounded, 'New Recipe', AppColors.primary, () {})),
              const SizedBox(width: 12),
              Expanded(child: _actionCard(Icons.play_arrow_rounded, 'Start Production', AppColors.success, () {})),
            ],
          ),
        ],
      ),
    );
  }

  Widget _actionCard(IconData icon, String label, Color color, VoidCallback onTap) {
    return GestureDetector(
      onTap: onTap,
      child: GlassCard(
        padding: const EdgeInsets.symmetric(vertical: 20, horizontal: 16),
        child: Column(
          children: [
            Container(
              width: 44, height: 44,
              decoration: BoxDecoration(color: color.withValues(alpha: 0.12), borderRadius: BorderRadius.circular(12)),
              child: Icon(icon, color: color, size: 24),
            ),
            const SizedBox(height: 10),
            Text(label, style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w600, color: AppColors.textPri)),
          ],
        ),
      ),
    );
  }

  Color _statusColor(String? label) {
    switch (label?.toLowerCase() ?? '') {
      case 'planned': return AppColors.info;
      case 'in_progress': case 'in progress': return AppColors.warning;
      case 'completed': return AppColors.success;
      case 'cancelled': return AppColors.danger;
      default: return AppColors.textSec;
    }
  }
}
