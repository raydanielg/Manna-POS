import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../core/api_service.dart';
import '../../shared/theme/app_theme.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/shimmer_loading.dart';
import '../../widgets/status_badge.dart';
import '../../widgets/section_header.dart';
import '../../widgets/toast_helper.dart';
import '../../widgets/confirm_dialog.dart';

class LoanDetailScreen extends StatefulWidget {
  final String loanId;
  final Map<String, dynamic>? loan;
  const LoanDetailScreen({super.key, required this.loanId, this.loan});
  @override State<LoanDetailScreen> createState() => _LoanDetailScreenState();
}

class _LoanDetailScreenState extends State<LoanDetailScreen> {
  bool _loading = true;
  String? _error;
  Map<String, dynamic>? _loan;
  final _fmt = NumberFormat('#,##0.00');
  final _currency = 'TSh';
  final _noteCtrl = TextEditingController();

  @override
  void initState() { super.initState(); _load(); }
  @override void dispose() { _noteCtrl.dispose(); super.dispose(); }

  Future<void> _load() async {
    if (widget.loan != null && widget.loan!.isNotEmpty) {
      setState(() { _loan = widget.loan; _loading = false; });
      return;
    }
    setState(() { _loading = true; _error = null; });
    try {
      final res = await ApiService.get('/microfinance/loans/${widget.loanId}');
      setState(() { _loan = res; _loading = false; });
    } on ApiException catch (e) { setState(() { _error = e.message; _loading = false; }); }
    catch (_) { setState(() { _error = 'Connection error'; _loading = false; }); }
  }

  String _f(dynamic v) => '$_currency ${_fmt.format((v is num ? v.toDouble() : double.tryParse(v?.toString() ?? '0') ?? 0))}';

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      body: _loading
          ? const ShimmerLoading(itemCount: 5)
          : _error != null
              ? Center(child: Column(mainAxisAlignment: MainAxisAlignment.center, children: [
                  const Icon(Icons.error_outline, size: 48, color: Colors.grey),
                  const SizedBox(height: 12), Text(_error!),
                  const SizedBox(height: 16), ElevatedButton(onPressed: _load, child: const Text('Retry')),
                ]))
              : RefreshIndicator(
                  onRefresh: _load,
                  child: SingleChildScrollView(
                    physics: const BouncingScrollPhysics(),
                    padding: const EdgeInsets.only(bottom: 120),
                    child: Column(children: [
                      _buildHeader(),
                      const SizedBox(height: 16),
                      _buildLoanInfo(),
                      const SizedBox(height: 16),
                      _buildRepaymentProgress(),
                      const SizedBox(height: 16),
                      _buildRepaymentHistory(),
                      const SizedBox(height: 16),
                      _buildActionButtons(),
                      const SizedBox(height: 16),
                      _buildNotes(),
                    ]),
                  ),
                ),
    );
  }

  Widget _buildHeader() {
    final l = _loan!;
    return Container(
      padding: const EdgeInsets.fromLTRB(20, 48, 20, 24),
      decoration: BoxDecoration(
        gradient: const LinearGradient(colors: [Color(0xFF1A3A5C), Color(0xFF0F1B2D)], begin: Alignment.topLeft, end: Alignment.bottomRight),
        borderRadius: const BorderRadius.vertical(bottom: Radius.circular(28)),
      ),
      child: SafeArea(
        bottom: false,
        child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
          Row(children: [
            GestureDetector(onTap: () => Navigator.pop(context), child: Container(padding: const EdgeInsets.all(8),
              decoration: BoxDecoration(color: Colors.white.withValues(alpha: 0.15), borderRadius: BorderRadius.circular(12)),
              child: const Icon(Icons.arrow_back_rounded, color: Colors.white, size: 22))),
            const Spacer(),
            StatusBadge.fromStatus(l['status']?.toString() ?? 'active'),
          ]),
          const SizedBox(height: 20),
          Row(children: [
            Container(width: 52, height: 52, decoration: BoxDecoration(color: Colors.white.withValues(alpha: 0.15), borderRadius: BorderRadius.circular(16)),
              child: Center(child: Text((l['client_name']?.toString() ?? '?')[0].toUpperCase(), style: const TextStyle(color: Colors.white, fontSize: 22, fontWeight: FontWeight.w800)))),
            const SizedBox(width: 14),
            Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
              Text(l['client_name']?.toString() ?? 'Client', style: const TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.w700)),
              const SizedBox(height: 2),
              Text(l['client_phone']?.toString() ?? '', style: TextStyle(color: Colors.white.withValues(alpha: 0.7), fontSize: 13)),
            ])),
          ]),
          const SizedBox(height: 16),
          Text('Loan Amount', style: TextStyle(color: Colors.white.withValues(alpha: 0.7), fontSize: 13)),
          const SizedBox(height: 4),
          Text(_f(l['amount'] ?? 0), style: const TextStyle(color: Colors.white, fontSize: 32, fontWeight: FontWeight.w900, letterSpacing: -1)),
        ]),
      ),
    );
  }

  Widget _buildLoanInfo() {
    final l = _loan!;
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16),
      child: GlassCard(
        child: Column(children: [
          _infoRow('Loan Product', l['product_name']?.toString() ?? '-'),
          _infoDivider(),
          _infoRow('Amount', _f(l['amount'] ?? 0)),
          _infoDivider(),
          _infoRow('Interest Rate', '${l['interest_rate'] ?? 0}%'),
          _infoDivider(),
          _infoRow('Duration', '${l['duration'] ?? 0} months'),
          _infoDivider(),
          _infoRow('Disbursement', l['disbursement_date']?.toString() ?? 'Pending'),
          _infoDivider(),
          _infoRow('Due Date', l['due_date']?.toString() ?? '-'),
          _infoDivider(),
          _infoRow('Balance', _f(l['balance'] ?? 0)),
        ]),
      ),
    );
  }

  Widget _infoRow(String label, String value) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 8),
      child: Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
        Text(label, style: const TextStyle(fontSize: 13, color: AppColors.textSec)),
        Text(value, style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w600, color: AppColors.textPri)),
      ]),
    );
  }

  Widget _infoDivider() => Divider(height: 1, color: Colors.grey.withValues(alpha: 0.1));

  Widget _buildRepaymentProgress() {
    final l = _loan!;
    final amount = (l['amount'] as num?)?.toDouble() ?? 0;
    final paid = (l['paid'] as num?)?.toDouble() ?? 0;
    final progress = amount > 0 ? paid / amount : 0.0;
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16),
      child: GlassCard(
        child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
          const SectionHeader(title: 'Repayment Progress', horizontalPadding: 0),
          const SizedBox(height: 12),
          ClipRRect(
            borderRadius: BorderRadius.circular(8),
            child: LinearProgressIndicator(
              value: progress,
              minHeight: 12,
              backgroundColor: Colors.grey.withValues(alpha: 0.1),
              valueColor: AlwaysStoppedAnimation(progress >= 1 ? AppColors.success : AppColors.primary),
            ),
          ),
          const SizedBox(height: 8),
          Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
            Text('${(progress * 100).toStringAsFixed(1)}% paid', style: TextStyle(fontWeight: FontWeight.w600, fontSize: 12, color: progress >= 1 ? AppColors.success : AppColors.primary)),
            Text('${_f(paid)} / ${_f(amount)}', style: const TextStyle(fontSize: 12, color: AppColors.textSec)),
          ]),
        ]),
      ),
    );
  }

  Widget _buildRepaymentHistory() {
    final history = (_loan?['repayments'] as List?) ?? [];
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16),
      child: GlassCard(
        child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
          SectionHeader(title: 'Repayment History', actionLabel: history.isNotEmpty ? '${history.length} items' : null, horizontalPadding: 0),
          const SizedBox(height: 8),
          if (history.isEmpty)
            const Padding(padding: EdgeInsets.all(16), child: Center(child: Text('No repayments yet', style: TextStyle(color: AppColors.textSec))))
          else
            ...history.map((r) => Padding(
              padding: const EdgeInsets.symmetric(vertical: 6),
              child: Row(children: [
                Container(width: 32, height: 32, decoration: BoxDecoration(color: AppColors.success.withValues(alpha: 0.12), borderRadius: BorderRadius.circular(8)),
                  child: const Icon(Icons.check_circle, color: AppColors.success, size: 16)),
                const SizedBox(width: 10),
                Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                  Text(_f(r['amount'] ?? 0), style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13, color: AppColors.textPri)),
                  Text('${r['method']?.toString() ?? ''} \u2022 ${r['date']?.toString() ?? ''}', style: const TextStyle(fontSize: 11, color: AppColors.textSec)),
                ])),
                if (r['receipt'] != null)
                  GestureDetector(onTap: () {}, child: const Icon(Icons.description_outlined, size: 18, color: AppColors.textSec)),
              ]),
            )),
        ]),
      ),
    );
  }

  Widget _buildActionButtons() {
    final status = _loan?['status']?.toString().toLowerCase() ?? '';
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16),
      child: Wrap(
        spacing: 10,
        runSpacing: 10,
        children: [
          _actionChip(Icons.payments_rounded, 'Record Repayment', AppColors.success, () {}),
          if (status == 'pending')
            _actionChip(Icons.check_circle_outline_rounded, 'Disburse', AppColors.primary, () {}),
          if (status == 'active')
            _actionChip(Icons.block_rounded, 'Close', AppColors.cyan, () {}),
          _actionChip(Icons.warning_amber_rounded, 'Mark Default', AppColors.danger, () {
            ConfirmDialog.show(context, title: 'Mark as Default', message: 'Mark this loan as defaulted?', confirmColor: AppColors.danger, icon: Icons.warning_amber_rounded);
          }),
        ],
      ),
    );
  }

  Widget _actionChip(IconData icon, String label, Color color, VoidCallback onTap) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
        decoration: BoxDecoration(color: color.withValues(alpha: 0.1), borderRadius: BorderRadius.circular(12)),
        child: Row(mainAxisSize: MainAxisSize.min, children: [
          Icon(icon, size: 16, color: color),
          const SizedBox(width: 6),
          Text(label, style: TextStyle(fontSize: 12, fontWeight: FontWeight.w600, color: color)),
        ]),
      ),
    );
  }

  Widget _buildNotes() {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16),
      child: GlassCard(
        child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
          const SectionHeader(title: 'Notes', horizontalPadding: 0),
          const SizedBox(height: 8),
          Text(_loan?['notes']?.toString() ?? 'No notes', style: const TextStyle(fontSize: 13, color: AppColors.textSec)),
          const SizedBox(height: 12),
          Row(children: [
            Expanded(child: TextField(
              controller: _noteCtrl,
              decoration: const InputDecoration(hintText: 'Add a note...', contentPadding: EdgeInsets.symmetric(horizontal: 14, vertical: 10), isDense: true),
            )),
            const SizedBox(width: 8),
            GestureDetector(
              onTap: () => ToastHelper.success(context, 'Note added'),
              child: Container(padding: const EdgeInsets.all(10), decoration: BoxDecoration(color: AppColors.primary, borderRadius: BorderRadius.circular(10)),
                child: const Icon(Icons.send_rounded, color: Colors.white, size: 18)),
            ),
          ]),
        ]),
      ),
    );
  }
}
