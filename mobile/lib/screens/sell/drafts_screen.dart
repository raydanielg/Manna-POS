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
import '../../providers/sale_provider.dart';
import '../../models/sale.dart';
import 'add_sale_screen.dart';

class DraftsScreen extends StatefulWidget {
  const DraftsScreen({super.key});
  @override State<DraftsScreen> createState() => _DraftsScreenState();
}

class _DraftsScreenState extends State<DraftsScreen> {
  List<Sale> _drafts = [];
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
      await context.read<SaleProvider>().fetchSales(status: 'draft');
      if (mounted) {
        setState(() {
          _drafts = context.read<SaleProvider>().sales;
          _loading = false;
        });
      }
    } catch (_) {
      if (mounted) setState(() => _loading = false);
    }
  }

  Future<void> _deleteDraft(Sale draft) async {
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (_) => ConfirmDialog(
        title: 'Delete Draft',
        message: 'Delete "${draft.reference}"?',
        confirmLabel: 'Delete',
        isDestructive: true,
      ),
    );
    if (confirmed == true && mounted) {
      try {
        await context.read<SaleProvider>().deleteSale(draft.id);
        if (mounted) {
          ToastHelper.success(context, 'Draft deleted');
          _load();
        }
      } catch (_) {
        if (mounted) ToastHelper.error(context, 'Failed to delete');
      }
    }
  }

  void _editDraft(Sale draft) {
    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (_) => AddSaleScreen(draftData: {
          'id': draft.id,
          'notes': '',
        }),
      ),
    ).then((_) => _load());
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
        title: const Text('Drafts', style: TextStyle(fontWeight: FontWeight.w700)),
        actions: [
          IconButton(icon: const Icon(Icons.refresh), onPressed: _load),
        ],
      ),
      body: _loading
          ? const ShimmerLoading(child: ShimmerCard())
          : _drafts.isEmpty
              ? EmptyState(
                  icon: Icons.edit_note,
                  title: 'No Drafts',
                  subtitle: 'Save a sale as draft to see it here',
                )
              : RefreshIndicator(
                  onRefresh: _load,
                  child: ListView.separated(
                    padding: const EdgeInsets.fromLTRB(16, 16, 16, 100),
                    itemCount: _drafts.length,
                    separatorBuilder: (_, __) => const SizedBox(height: 10),
                    itemBuilder: (_, i) => _draftCard(_drafts[i], theme),
                  ),
                ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () {
          Navigator.push(
            context,
            MaterialPageRoute(builder: (_) => const AddDraftScreen()),
          ).then((_) => _load());
        },
        backgroundColor: theme.colorScheme.primary,
        foregroundColor: Colors.white,
        icon: const Icon(Icons.add),
        label: const Text('New Draft'),
      ),
    );
  }

  Widget _draftCard(Sale draft, ThemeData theme) {
    return Dismissible(
      key: ValueKey(draft.id),
      direction: DismissDirection.endToStart,
      background: Container(
        alignment: Alignment.centerRight,
        padding: const EdgeInsets.only(right: 20),
        decoration: BoxDecoration(
          color: theme.colorScheme.error,
          borderRadius: BorderRadius.circular(14),
        ),
        child: const Icon(Icons.delete_outline, color: Colors.white, size: 28),
      ),
      confirmDismiss: (_) async {
        await _deleteDraft(draft);
        return false;
      },
      child: GlassCard(
        child: InkWell(
          onTap: () => _editDraft(draft),
          borderRadius: BorderRadius.circular(14),
          child: Padding(
            padding: const EdgeInsets.all(16),
            child: Row(
              children: [
                Container(
                  width: 46, height: 46,
                  decoration: BoxDecoration(
                    color: Colors.orange.withValues(alpha: 0.15),
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: const Icon(Icons.edit_outlined,
                    color: Colors.orange, size: 22),
                ),
                const SizedBox(width: 14),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(draft.reference,
                        style: const TextStyle(
                          fontWeight: FontWeight.w700, fontSize: 14)),
                      const SizedBox(height: 3),
                      Text(draft.customerName,
                        style: TextStyle(
                          color: theme.colorScheme.onSurface.withValues(alpha: 0.6),
                          fontSize: 13)),
                      const SizedBox(height: 4),
                      Row(
                        children: [
                          Icon(Icons.calendar_today, size: 11,
                            color: theme.colorScheme.onSurface.withValues(alpha: 0.5)),
                          const SizedBox(width: 4),
                          Text(draft.saleDate,
                            style: TextStyle(
                              fontSize: 11,
                              color: theme.colorScheme.onSurface.withValues(alpha: 0.5))),
                        ],
                      ),
                    ],
                  ),
                ),
                Column(
                  crossAxisAlignment: CrossAxisAlignment.end,
                  children: [
                    Text('TSh ${_fmt.format(draft.total)}',
                      style: const TextStyle(
                        fontWeight: FontWeight.w800, fontSize: 15)),
                    const SizedBox(height: 4),
                    StatusBadge.fromStatus('draft'),
                  ],
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
