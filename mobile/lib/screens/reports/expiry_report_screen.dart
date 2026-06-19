import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../shared/theme/app_colors.dart';
import '../../shared/widgets/status_badge.dart';
import '../../widgets/glass_card.dart';
import '../../core/api_service.dart';

class ExpiryReportScreen extends StatefulWidget {
  const ExpiryReportScreen({super.key});
  @override State<ExpiryReportScreen> createState() => _ExpiryReportScreenState();
}

class _ExpiryReportScreenState extends State<ExpiryReportScreen> {
  Map<String, dynamic>? _data;
  bool _loading = true;
  int _filterDays = 30;
  final _fmt = NumberFormat('#,##0.00');

  @override void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    setState(() => _loading = true);
    try {
      final d = await ApiService.get('/reports/expiry?days=$_filterDays');
      if (mounted) setState(() { _data = d; _loading = false; });
    } catch (_) { if (mounted) setState(() => _loading = false); }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(title: const Text('Expiry Report')),
      body: Column(children: [
        GlassCard(
          child: Padding(
            padding: const EdgeInsets.all(12),
            child: Row(children: [
              _dayChip(30, '30 Days'),
              const SizedBox(width: 8),
              _dayChip(60, '60 Days'),
              const SizedBox(width: 8),
              _dayChip(90, '90 Days'),
            ]),
          ),
        ),
        Expanded(
          child: _loading
              ? const Center(child: CircularProgressIndicator())
              : _data == null
                  ? const Center(child: Text('No data'))
                  : ListView.builder(
                      padding: const EdgeInsets.all(16),
                      itemCount: (_data!['products'] as List?)?.length ?? 0,
                      itemBuilder: (context, i) {
                        final p = (_data!['products'] as List)[i];
                        final daysLeft = p['days_remaining'] ?? 0;
                        final expiryDate = p['expiry_date'] ?? '';
                        Color cardColor;
                        Color badgeColor;
                        if (daysLeft < 30) {
                          cardColor = AppColors.danger.withValues(alpha: 0.05);
                          badgeColor = AppColors.danger;
                        } else if (daysLeft < 60) {
                          cardColor = AppColors.warning.withValues(alpha: 0.05);
                          badgeColor = AppColors.warning;
                        } else {
                          cardColor = AppColors.success.withValues(alpha: 0.05);
                          badgeColor = AppColors.success;
                        }
                        return Padding(
                          padding: const EdgeInsets.only(bottom: 10),
                          child: GlassCard(
                            child: Container(
                              decoration: BoxDecoration(
                                border: Border(left: BorderSide(color: badgeColor, width: 3)),
                                borderRadius: BorderRadius.circular(14),
                              ),
                              padding: const EdgeInsets.all(14),
                              child: Row(children: [
                                Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                                  Text(p['name'] ?? '', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 15)),
                                  const SizedBox(height: 4),
                                  Text('Expires: $expiryDate', style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
                                  const SizedBox(height: 2),
                                  Text('Stock: ${p['stock_quantity'] ?? 0}', style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
                                ])),
                                Column(crossAxisAlignment: CrossAxisAlignment.end, children: [
                                  StatusBadge(label: '$daysLeft days', color: badgeColor, bgColor: badgeColor.withValues(alpha: 0.15)),
                                  const SizedBox(height: 6),
                                  Text('TSh ${_fmt.format((p['price'] ?? 0).toDouble())}', style: TextStyle(fontWeight: FontWeight.w700, color: badgeColor, fontSize: 13)),
                                ]),
                              ]),
                            ),
                          ),
                        );
                      },
                    ),
        ),
      ]),
    );
  }

  Widget _dayChip(int days, String label) {
    final sel = _filterDays == days;
    return Expanded(
      child: GestureDetector(
        onTap: () => setState(() { _filterDays = days; _load(); }),
        child: Container(
          padding: const EdgeInsets.symmetric(vertical: 10),
          decoration: BoxDecoration(
            color: sel ? AppColors.primary : Colors.transparent,
            borderRadius: BorderRadius.circular(10),
            border: Border.all(color: sel ? AppColors.primary : AppColors.border),
          ),
          child: Text(label, textAlign: TextAlign.center, style: TextStyle(color: sel ? Colors.white : AppColors.textSec, fontWeight: FontWeight.w600, fontSize: 13)),
        ),
      ),
    );
  }
}
