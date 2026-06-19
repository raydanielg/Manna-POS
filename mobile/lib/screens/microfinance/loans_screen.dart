import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../core/api_service.dart';
import '../../shared/theme/app_theme.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/stat_card.dart';
import '../../widgets/shimmer_loading.dart';
import '../../widgets/empty_state.dart';
import '../../widgets/status_badge.dart';
import '../../widgets/toast_helper.dart';
import 'loan_detail_screen.dart';

class LoansScreen extends StatefulWidget {
  const LoansScreen({super.key});
  @override State<LoansScreen> createState() => _LoansScreenState();
}

class _LoansScreenState extends State<LoansScreen> {
  bool _loading = true;
  String? _error;
  List<dynamic> _loans = [];
  List<dynamic> _filtered = [];
  String _filter = 'All';
  final _fmt = NumberFormat('#,##0.00');
  final _currency = 'TSh';

  final _filters = ['All', 'Active', 'Closed', 'Default', 'Pending'];

  @override
  void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    setState(() { _loading = true; _error = null; });
    try {
      final res = await ApiService.get('/microfinance/loans');
      setState(() { _loans = res is List ? res : (res['data'] ?? []); _loading = false; _applyFilter(); });
    } on ApiException catch (e) { setState(() { _error = e.message; _loading = false; }); }
    catch (_) { setState(() { _error = 'Connection error'; _loading = false; }); }
  }

  void _applyFilter() {
    if (_filter == 'All') { _filtered = List.from(_loans); return; }
    _filtered = _loans.where((l) => l['status']?.toString().toLowerCase() == _filter.toLowerCase()).toList();
  }

  String _f(dynamic v) => '$_currency ${_fmt.format((v is num ? v.toDouble() : double.tryParse(v?.toString() ?? '0') ?? 0))}';

  Map<String, dynamic> get _stats {
    final total = _loans.length;
    final active = _loans.where((l) => l['status']?.toString().toLowerCase() == 'active').length;
    final closed = _loans.where((l) => l['status']?.toString().toLowerCase() == 'closed').length;
    final def = _loans.where((l) => l['status']?.toString().toLowerCase() == 'default').length;
    final pending = _loans.where((l) => l['status']?.toString().toLowerCase() == 'pending').length;
    final totalAmount = _loans.fold(0.0, (s, l) => s + ((l['amount'] as num?)?.toDouble() ?? 0));
    return {'total': total, 'active': active, 'closed': closed, 'default': def, 'pending': pending, 'totalAmount': totalAmount};
  }

  @override
  Widget build(BuildContext context) {
    final stats = _stats;
    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        title: const Text('Loans', style: TextStyle(fontWeight: FontWeight.w800, color: Color(0xFF0F172A), fontSize: 20)),
        backgroundColor: Colors.white, elevation: 0, centerTitle: true,
        actions: [IconButton(icon: const Icon(Icons.refresh_rounded, color: AppColors.primary), onPressed: _load)],
      ),
      body: _loading
          ? const ShimmerLoading(itemCount: 6)
          : _error != null
              ? Center(child: Column(mainAxisAlignment: MainAxisAlignment.center, children: [
                  const Icon(Icons.error_outline, size: 48, color: Colors.grey),
                  const SizedBox(height: 12), Text(_error!),
                  const SizedBox(height: 16), ElevatedButton(onPressed: _load, child: const Text('Retry')),
                ]))
              : RefreshIndicator(
                  onRefresh: _load,
                  child: Column(children: [
                    _buildStatsRow(stats),
                    _buildFilterChips(),
                    Expanded(child: _filtered.isEmpty
                        ? const EmptyState(icon: Icons.credit_card_off_outlined, title: 'No loans', subtitle: 'No loans match this filter')
                        : ListView.builder(
                            padding: const EdgeInsets.fromLTRB(16, 8, 16, 100),
                            itemCount: _filtered.length,
                            itemBuilder: (_, i) => _loanCard(_filtered[i]),
                          )),
                  ]),
                ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () {},
        icon: const Icon(Icons.add),
        label: const Text('New Loan'),
        backgroundColor: AppColors.primary, foregroundColor: Colors.white,
      ),
    );
  }

  Widget _buildStatsRow(Map<String, dynamic> stats) {
    return Padding(
      padding: const EdgeInsets.fromLTRB(16, 12, 16, 4),
      child: Row(children: [
        _statBadge('${stats['total']}', 'Total', AppColors.primary),
        const SizedBox(width: 8),
        _statBadge('${stats['active']}', 'Active', AppColors.success),
        const SizedBox(width: 8),
        _statBadge('${stats['closed']}', 'Closed', AppColors.cyan),
        const SizedBox(width: 8),
        _statBadge('${stats['default']}', 'Default', AppColors.danger),
        const SizedBox(width: 8),
        _statBadge('${stats['pending']}', 'Pending', AppColors.warning),
      ]),
    );
  }

  Widget _statBadge(String value, String label, Color color) {
    return Expanded(
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 10, horizontal: 4),
        decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(10), border: Border.all(color: AppColors.border)),
        child: Column(children: [
          Text(value, style: TextStyle(fontWeight: FontWeight.w800, fontSize: 16, color: color)),
          Text(label, style: const TextStyle(fontSize: 9, color: AppColors.textSec)),
        ]),
      ),
    );
  }

  Widget _buildFilterChips() {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
      child: SizedBox(
        height: 34,
        child: ListView.separated(
          scrollDirection: Axis.horizontal,
          itemCount: _filters.length,
          separatorBuilder: (_, __) => const SizedBox(width: 8),
          itemBuilder: (_, i) {
            final selected = _filter == _filters[i];
            return GestureDetector(
              onTap: () => setState(() { _filter = _filters[i]; _applyFilter(); }),
              child: Container(
                padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 6),
                decoration: BoxDecoration(
                  color: selected ? AppColors.primary : AppColors.surfaceVariant,
                  borderRadius: BorderRadius.circular(20),
                ),
                child: Text(_filters[i], style: TextStyle(
                  color: selected ? Colors.white : AppColors.textSec,
                  fontWeight: FontWeight.w600, fontSize: 12,
                )),
              ),
            );
          },
        ),
      ),
    );
  }

  Widget _loanCard(Map<String, dynamic> l) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 12),
      child: GlassCard(
        onTap: () {
          Navigator.push(context, MaterialPageRoute(builder: (_) => LoanDetailScreen(loanId: l['id']?.toString() ?? '', loan: l)));
        },
        child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
          Row(children: [
            Container(width: 44, height: 44, decoration: BoxDecoration(color: AppColors.primary.withValues(alpha: 0.1), borderRadius: BorderRadius.circular(12)),
              child: Center(child: Text((l['client_name']?.toString() ?? '?')[0].toUpperCase(), style: const TextStyle(color: AppColors.primary, fontWeight: FontWeight.w800, fontSize: 18)))),
            const SizedBox(width: 12),
            Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
              Text(l['client_name']?.toString() ?? 'Client', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14, color: AppColors.textPri)),
              const SizedBox(height: 2),
              Text(l['product_name']?.toString() ?? '', style: const TextStyle(fontSize: 12, color: AppColors.textSec)),
            ])),
            StatusBadge.fromStatus(l['status']?.toString() ?? 'active'),
          ]),
          const SizedBox(height: 12),
          Row(children: [
            _infoChip(Icons.monetization_on_outlined, _f(l['amount'] ?? 0)),
            const SizedBox(width: 16),
            _infoChip(Icons.account_balance_wallet_outlined, 'Bal: ${_f(l['balance'] ?? 0)}'),
            const Spacer(),
            Text('${l['interest_rate'] ?? 0}%', style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w600, color: AppColors.primary)),
          ]),
          const SizedBox(height: 6),
          Text(l['date']?.toString() ?? '', style: const TextStyle(fontSize: 10, color: AppColors.textLight)),
        ]),
      ),
    );
  }

  Widget _infoChip(IconData icon, String text) {
    return Row(mainAxisSize: MainAxisSize.min, children: [
      Icon(icon, size: 14, color: AppColors.textSec),
      const SizedBox(width: 4),
      Text(text, style: const TextStyle(fontSize: 12, color: AppColors.textSec, fontWeight: FontWeight.w500)),
    ]);
  }
}
