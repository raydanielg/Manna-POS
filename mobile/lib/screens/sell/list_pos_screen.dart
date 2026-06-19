import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/status_badge.dart';
import '../../widgets/search_bar_widget.dart';
import '../../widgets/shimmer_loading.dart';
import '../../widgets/empty_state.dart';
import '../../widgets/section_header.dart';
import '../../widgets/confirm_dialog.dart';
import '../../widgets/toast_helper.dart';
import '../../widgets/app_bottom_sheet.dart';
import '../../widgets/stat_card.dart';
import '../../providers/sale_provider.dart';

class ListPosScreen extends StatefulWidget {
  const ListPosScreen({super.key});
  @override State<ListPosScreen> createState() => _ListPosScreenState();
}

class _ListPosScreenState extends State<ListPosScreen> {
  List<Map<String, dynamic>> _registers = [];
  bool _loading = true;
  final _fmt = NumberFormat('#,##0');

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() => _loading = true);
    try {
      await context.read<SaleProvider>().fetchSales();
      setState(() {
        _registers = [
          {
            'id': 1, 'name': 'Main Register',
            'status': 'open', 'opened_at': '2026-06-19 08:00',
            'closed_at': null, 'opening_amount': 500000.0,
            'closing_amount': null, 'sales_count': 24,
          },
          {
            'id': 2, 'name': 'Secondary Register',
            'status': 'closed', 'opened_at': '2026-06-18 09:00',
            'closed_at': '2026-06-18 20:00',
            'opening_amount': 300000.0, 'closing_amount': 845000.0,
            'sales_count': 18,
          },
          {
            'id': 3, 'name': 'Weekend Kiosk',
            'status': 'open', 'opened_at': '2026-06-19 10:00',
            'closed_at': null, 'opening_amount': 200000.0,
            'closing_amount': null, 'sales_count': 7,
          },
        ];
        _loading = false;
      });
    } catch (_) {
      if (mounted) setState(() => _loading = false);
    }
  }

  Future<void> _toggleRegister(Map<String, dynamic> reg) async {
    final isOpen = reg['status'] == 'open';
    final action = isOpen ? 'Close' : 'Open';
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (_) => ConfirmDialog(
        title: '$action Register',
        message: '${action} "${reg['name']}"?',
        confirmLabel: action,
      ),
    );
    if (confirmed == true && mounted) {
      setState(() {
        reg['status'] = isOpen ? 'closed' : 'open';
        if (isOpen) {
          reg['closed_at'] = DateFormat('yyyy-MM-dd HH:mm').format(DateTime.now());
          reg['closing_amount'] = 0.0;
        } else {
          reg['opened_at'] = DateFormat('yyyy-MM-dd HH:mm').format(DateTime.now());
          reg['opening_amount'] = 0.0;
        }
      });
      ToastHelper.success(context, '${reg['name']} ${isOpen ? 'closed' : 'opened'}');
    }
  }

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    return Scaffold(
      backgroundColor: theme.scaffoldBackgroundColor,
      appBar: AppBar(
        backgroundColor: theme.colorScheme.primary,
        foregroundColor: Colors.white,
        elevation: 0,
        title: const Text('POS Registers', style: TextStyle(fontWeight: FontWeight.w700)),
        actions: [
          IconButton(icon: const Icon(Icons.refresh), onPressed: _load),
        ],
      ),
      body: _loading
          ? const ShimmerLoading(child: ShimmerCard())
          : _registers.isEmpty
              ? EmptyState(
                  icon: Icons.point_of_sale_outlined,
                  title: 'No Registers',
                  subtitle: 'Add a POS register to get started',
                )
              : RefreshIndicator(
                  onRefresh: _load,
                  child: ListView.separated(
                    padding: const EdgeInsets.fromLTRB(16, 16, 16, 100),
                    itemCount: _registers.length,
                    separatorBuilder: (_, __) => const SizedBox(height: 12),
                    itemBuilder: (_, i) => _registerCard(_registers[i], theme),
                  ),
                ),
    );
  }

  Widget _registerCard(Map<String, dynamic> reg, ThemeData theme) {
    final isOpen = reg['status'] == 'open';
    return GlassCard(
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Container(
                  width: 46, height: 46,
                  decoration: BoxDecoration(
                    color: isOpen ? Colors.green.withValues(alpha: 0.15) : Colors.grey.withValues(alpha: 0.15),
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: Icon(Icons.point_of_sale, size: 22,
                    color: isOpen ? Colors.green : Colors.grey),
                ),
                const SizedBox(width: 14),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(reg['name'],
                        style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14)),
                      const SizedBox(height: 3),
                      Text('Opened: ${reg['opened_at']}',
                        style: TextStyle(fontSize: 12, color: theme.colorScheme.onSurface.withValues(alpha: 0.6))),
                    ],
                  ),
                ),
                StatusBadge(
                  label: isOpen ? 'Open' : 'Closed',
                  color: isOpen ? Colors.green : Colors.grey,
                  bgColor: isOpen ? Colors.green.withValues(alpha: 0.15) : Colors.grey.withValues(alpha: 0.15),
                ),
              ],
            ),
            const SizedBox(height: 16),
            Row(
              children: [
                Expanded(
                  child: _statItem('Opening', 'TSh ${_fmt.format(reg['opening_amount'])}', theme),
                ),
                Container(width: 1, height: 40, color: theme.colorScheme.outline.withValues(alpha: 0.2)),
                Expanded(
                  child: _statItem(
                    isOpen ? 'Current' : 'Closing',
                    isOpen ? '---' : 'TSh ${_fmt.format(reg['closing_amount'] ?? 0)}',
                    theme,
                  ),
                ),
                Container(width: 1, height: 40, color: theme.colorScheme.outline.withValues(alpha: 0.2)),
                Expanded(
                  child: _statItem('Sales', '${reg['sales_count']}', theme),
                ),
              ],
            ),
            const SizedBox(height: 12),
            if (!isOpen && reg['closed_at'] != null)
              Padding(
                padding: const EdgeInsets.only(bottom: 8),
                child: Text('Closed: ${reg['closed_at']}',
                  style: TextStyle(fontSize: 12, color: theme.colorScheme.onSurface.withValues(alpha: 0.5))),
              ),
            SizedBox(
              width: double.infinity,
              child: ElevatedButton.icon(
                onPressed: () => _toggleRegister(reg),
                icon: Icon(isOpen ? Icons.lock_outline : Icons.lock_open),
                label: Text(isOpen ? 'Close Register' : 'Open Register'),
                style: ElevatedButton.styleFrom(
                  backgroundColor: isOpen ? theme.colorScheme.error : theme.colorScheme.primary,
                  foregroundColor: Colors.white,
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _statItem(String label, String value, ThemeData theme) {
    return Column(
      children: [
        Text(value, style: TextStyle(fontWeight: FontWeight.w800, fontSize: 15, color: theme.colorScheme.onSurface)),
        const SizedBox(height: 2),
        Text(label, style: TextStyle(fontSize: 11, color: theme.colorScheme.onSurface.withValues(alpha: 0.6))),
      ],
    );
  }
}
