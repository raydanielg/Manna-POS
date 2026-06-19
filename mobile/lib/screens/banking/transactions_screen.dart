import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../core/api_service.dart';
import '../../shared/theme/app_theme.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/shimmer_loading.dart';
import '../../widgets/empty_state.dart';
import '../../widgets/search_bar_widget.dart';
import '../../widgets/status_badge.dart';
import '../../widgets/toast_helper.dart';
import '../../widgets/app_bottom_sheet.dart';
import '../../widgets/section_header.dart';

class TransactionsScreen extends StatefulWidget {
  const TransactionsScreen({super.key});
  @override State<TransactionsScreen> createState() => _TransactionsScreenState();
}

class _TransactionsScreenState extends State<TransactionsScreen> {
  bool _loading = true;
  String? _error;
  List<dynamic> _transactions = [];
  List<dynamic> _filtered = [];
  String _selectedFilter = 'All';
  String _dateRange = 'Month';
  final _searchCtrl = TextEditingController();
  final _fmt = NumberFormat('#,##0.00');
  final _currency = 'TSh';
  double _runningBalance = 0;

  final _filters = ['All', 'Income', 'Expense', 'Transfer'];
  final _dateRanges = ['Today', 'Week', 'Month', 'Custom'];

  @override
  void initState() { super.initState(); _load(); }
  @override void dispose() { _searchCtrl.dispose(); super.dispose(); }

  Future<void> _load() async {
    setState(() { _loading = true; _error = null; });
    try {
      final res = await ApiService.get('/dashboard/banking/transactions');
      final txns = res is List ? res : (res['data'] ?? []);
      setState(() {
        _transactions = txns;
        _applyFilters();
        _loading = false;
      });
    } on ApiException catch (e) { setState(() { _error = e.message; _loading = false; }); }
    catch (_) { setState(() { _error = 'Connection error'; _loading = false; }); }
  }

  void _applyFilters() {
    var list = List<Map<String, dynamic>>.from(_transactions);
    if (_selectedFilter != 'All') {
      list = list.where((t) => t['type']?.toString().toLowerCase() == _selectedFilter.toLowerCase()).toList();
    }
    final q = _searchCtrl.text.toLowerCase();
    if (q.isNotEmpty) {
      list = list.where((t) =>
        (t['description']?.toString().toLowerCase() ?? '').contains(q) ||
        (t['category']?.toString().toLowerCase() ?? '').contains(q) ||
        (t['reference']?.toString().toLowerCase() ?? '').contains(q)
      ).toList();
    }
    setState(() {
      _filtered = list;
      _runningBalance = _filtered.fold(0.0, (s, t) {
        final amt = (t['amount'] as num?)?.toDouble() ?? 0;
        final isCredit = t['type']?.toString().toLowerCase() == 'income' || t['type']?.toString().toLowerCase() == 'deposit';
        return s + (isCredit ? amt : -amt);
      });
    });
  }

  String _f(dynamic v) => '$_currency ${_fmt.format((v is num ? v.toDouble() : double.tryParse(v?.toString() ?? '0') ?? 0))}';

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        title: const Text('Transactions', style: TextStyle(fontWeight: FontWeight.w800, color: Color(0xFF0F172A), fontSize: 20)),
        backgroundColor: Colors.white, elevation: 0, centerTitle: true,
        actions: [IconButton(icon: const Icon(Icons.refresh_rounded, color: AppColors.primary), onPressed: _load)],
      ),
      body: _loading
          ? const ShimmerLoading(itemCount: 8)
          : _error != null
              ? Center(child: Column(mainAxisAlignment: MainAxisAlignment.center, children: [
                  const Icon(Icons.error_outline, size: 48, color: Colors.grey),
                  const SizedBox(height: 12), Text(_error!),
                  const SizedBox(height: 16), ElevatedButton(onPressed: _load, child: const Text('Retry')),
                ]))
              : RefreshIndicator(
                  onRefresh: _load,
                  child: Column(children: [
                    _buildFilters(),
                    Expanded(child: _filtered.isEmpty
                        ? const EmptyState(icon: Icons.receipt_long_outlined, title: 'No transactions', subtitle: 'Try a different filter')
                        : ListView.builder(
                            padding: const EdgeInsets.fromLTRB(16, 0, 16, 100),
                            itemCount: _filtered.length,
                            itemBuilder: (_, i) => _txnTile(_filtered[i], i == 0 ? 0 : null),
                          )),
                  ]),
                ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () {},
        icon: const Icon(Icons.add),
        label: const Text('New Transaction'),
        backgroundColor: AppColors.primary, foregroundColor: Colors.white,
      ),
    );
  }

  Widget _buildFilters() {
    return Container(
      color: Colors.white,
      padding: const EdgeInsets.fromLTRB(16, 12, 16, 12),
      child: Column(children: [
        SearchBarWidget(hint: 'Search transactions...', onChanged: (_) => _applyFilters(), controller: _searchCtrl),
        const SizedBox(height: 12),
        Row(children: [
          Expanded(
            child: SizedBox(
              height: 34,
              child: ListView.separated(
                scrollDirection: Axis.horizontal,
                itemCount: _filters.length,
                separatorBuilder: (_, __) => const SizedBox(width: 8),
                itemBuilder: (_, i) => _filterChip(_filters[i]),
              ),
            ),
          ),
        ]),
        const SizedBox(height: 8),
        Row(children: _dateRanges.map((r) => _dateChip(r)).toList()),
      ]),
    );
  }

  Widget _filterChip(String label) {
    final selected = _selectedFilter == label;
    return GestureDetector(
      onTap: () => setState(() { _selectedFilter = label; _applyFilters(); }),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 6),
        decoration: BoxDecoration(
          color: selected ? AppColors.primary : AppColors.surfaceVariant,
          borderRadius: BorderRadius.circular(20),
        ),
        child: Text(label, style: TextStyle(
          color: selected ? Colors.white : AppColors.textSec,
          fontWeight: FontWeight.w600, fontSize: 12,
        )),
      ),
    );
  }

  Widget _dateChip(String label) {
    final selected = _dateRange == label;
    return Padding(
      padding: const EdgeInsets.only(right: 8),
      child: GestureDetector(
        onTap: () => setState(() { _dateRange = label; _applyFilters(); }),
        child: Container(
          padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 5),
          decoration: BoxDecoration(
            border: Border.all(color: selected ? AppColors.primary : AppColors.border),
            borderRadius: BorderRadius.circular(20),
          ),
          child: Text(label, style: TextStyle(
            color: selected ? AppColors.primary : AppColors.textSec,
            fontWeight: FontWeight.w600, fontSize: 11,
          )),
        ),
      ),
    );
  }

  Widget _txnTile(Map<String, dynamic> t, double? runningBal) {
    final isCredit = t['type']?.toString().toLowerCase() == 'income' || t['type']?.toString().toLowerCase() == 'deposit';
    final txnColor = isCredit ? AppColors.success : AppColors.secondary;
    return Padding(
      padding: const EdgeInsets.only(bottom: 10),
      child: GlassCard(
        padding: const EdgeInsets.all(14),
        child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
          Row(children: [
            Container(width: 42, height: 42, decoration: BoxDecoration(color: txnColor.withValues(alpha: 0.12), borderRadius: BorderRadius.circular(12)),
              child: Icon(isCredit ? Icons.arrow_upward_rounded : Icons.arrow_downward_rounded, color: txnColor, size: 20)),
            const SizedBox(width: 12),
            Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
              Text(t['description']?.toString() ?? 'Transaction', style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13, color: AppColors.textPri)),
              const SizedBox(height: 2),
              Row(children: [
                Text(t['category']?.toString() ?? '', style: const TextStyle(fontSize: 11, color: AppColors.textSec)),
                const SizedBox(width: 8),
                StatusBadge.fromStatus(t['status']?.toString() ?? 'completed'),
              ]),
            ])),
            Column(crossAxisAlignment: CrossAxisAlignment.end, children: [
              Text('${isCredit ? '+' : '-'} ${_f(t['amount'] ?? 0)}', style: TextStyle(fontWeight: FontWeight.w800, fontSize: 14, color: txnColor)),
              const SizedBox(height: 2),
              Text(t['date']?.toString() ?? '', style: const TextStyle(fontSize: 10, color: AppColors.textLight)),
            ]),
          ]),
          if (runningBal != null) ...[
            const SizedBox(height: 8),
            Row(mainAxisAlignment: MainAxisAlignment.end, children: [
              Text('Running: ', style: TextStyle(fontSize: 10, color: Colors.grey.shade400)),
              Text(_f(runningBal), style: TextStyle(fontSize: 10, fontWeight: FontWeight.w700, color: runningBal >= 0 ? AppColors.success : AppColors.danger)),
            ]),
          ],
        ]),
      ),
    );
  }
}
