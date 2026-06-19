import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../../core/api_service.dart';
import '../../shared/theme/app_colors.dart';
import '../../shared/utils/formatters.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/status_badge.dart';
import '../../widgets/search_bar_widget.dart';
import '../../widgets/shimmer_loading.dart';
import '../../widgets/empty_state.dart';
import '../../constants/app_constants.dart';
import 'production_detail_screen.dart';
import 'production_form_screen.dart';

class ProductionScreen extends StatefulWidget {
  const ProductionScreen({super.key});
  @override State<ProductionScreen> createState() => _ProductionScreenState();
}

class _ProductionScreenState extends State<ProductionScreen> {
  List<Map<String, dynamic>> _runs = [];
  List<Map<String, dynamic>> _filtered = [];
  bool _loading = true;
  String? _error;
  String _search = '';
  String _statusFilter = '';
  final _searchCtrl = TextEditingController();

  final _statuses = ['', 'planned', 'in_progress', 'completed', 'cancelled'];
  final _statusLabels = ['All', 'Planned', 'In Progress', 'Completed', 'Cancelled'];

  @override
  void initState() { super.initState(); _load(); }
  @override
  void dispose() { _searchCtrl.dispose(); super.dispose(); }

  Future<void> _load() async {
    setState(() { _loading = true; _error = null; });
    try {
      final data = await ApiService.get('/api/manufacturing/production');
      final list = data is List ? data.map((e) => Map<String, dynamic>.from(e)).toList() : <Map<String, dynamic>>[];
      setState(() { _runs = list; _filter(); _loading = false; });
    } catch (e) { setState(() { _error = e.toString(); _loading = false; }); }
  }

  void _filter() {
    setState(() {
      _filtered = _runs.where((r) {
        if (_statusFilter.isNotEmpty && r['status'] != _statusFilter) return false;
        if (_search.isNotEmpty) {
          final q = _search.toLowerCase();
          final name = (r['recipe_name'] ?? '').toString().toLowerCase();
          final batch = (r['batch_number'] ?? '').toString().toLowerCase();
          if (!name.contains(q) && !batch.contains(q)) return false;
        }
        return true;
      }).toList();
    });
  }

  Color _statusBg(String status) {
    switch (status) {
      case 'planned': return AppColors.info.withValues(alpha: 0.1);
      case 'in_progress': return AppColors.warningLt;
      case 'completed': return AppColors.successLt;
      case 'cancelled': return AppColors.dangerLt;
      default: return AppColors.surfaceVariant;
    }
  }

  Color _statusFg(String status) {
    switch (status) {
      case 'planned': return AppColors.info;
      case 'in_progress': return AppColors.warning;
      case 'completed': return AppColors.success;
      case 'cancelled': return AppColors.danger;
      default: return AppColors.textSec;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(title: const Text('Production Runs')),
      body: Column(
        children: [
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 12, 16, 0),
            child: AppSearchBar(hint: 'Search batch or recipe...', controller: _searchCtrl, onChanged: (v) { _search = v; _filter(); }),
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
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () async { await Navigator.push(context, MaterialPageRoute(builder: (_) => const ProductionFormScreen())); _load(); },
        icon: const Icon(Icons.add),
        label: const Text('New Production'),
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
        icon: Icons.production_quantity_limits_outlined,
        title: 'No Production Runs',
        subtitle: _search.isNotEmpty ? 'No runs match your search' : 'Start your first production run',
        actionLabel: _search.isNotEmpty ? null : 'New Production',
        onAction: _search.isNotEmpty ? null : () async { await Navigator.push(context, MaterialPageRoute(builder: (_) => const ProductionFormScreen())); _load(); },
      );
    }
    return RefreshIndicator(
      onRefresh: _load,
      child: ListView.builder(
        padding: const EdgeInsets.fromLTRB(16, 0, 16, 100),
        itemCount: _filtered.length,
        itemBuilder: (_, i) => _runCard(_filtered[i]),
      ),
    );
  }

  Widget _runCard(Map<String, dynamic> r) {
    final status = r['status'] ?? '';
    return Padding(
      padding: const EdgeInsets.only(bottom: 10),
      child: GlassCard(
        onTap: () async { await Navigator.push(context, MaterialPageRoute(builder: (_) => ProductionDetailScreen(runId: r['id']))); _load(); },
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Container(
                  width: 40, height: 40,
                  decoration: BoxDecoration(color: _statusBg(status), borderRadius: BorderRadius.circular(10)),
                  child: Icon(Icons.production_quantity_limits_rounded, size: 20, color: _statusFg(status)),
                ),
                const SizedBox(width: 12),
                Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                  Text(r['recipe_name'] ?? '', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 15, color: AppColors.textPri)),
                  Text('Batch: ${r['batch_number'] ?? ''}', style: const TextStyle(color: AppColors.textSec, fontSize: 11)),
                ])),
                StatusBadge(label: status, color: _statusFg(status), bgColor: _statusBg(status)),
              ],
            ),
            const SizedBox(height: 12),
            Row(
              children: [
                _infoCol('Quantity', '${r['quantity_produced'] ?? 0} / ${r['quantity_planned'] ?? 0}'),
                const SizedBox(width: 24),
                _infoCol('Start Date', fmtDate(r['start_date'])),
                const Spacer(),
                if (r['end_date'] != null) _infoCol('End Date', fmtDate(r['end_date'])),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _infoCol(String label, String value) {
    return Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
      Text(label, style: const TextStyle(color: AppColors.textSec, fontSize: 11)),
      const SizedBox(height: 2),
      Text(value, style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13, color: AppColors.textPri)),
    ]);
  }
}
