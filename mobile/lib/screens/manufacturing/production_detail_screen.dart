import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../../core/api_service.dart';
import '../../shared/theme/app_colors.dart';
import '../../shared/utils/formatters.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/status_badge.dart';
import '../../widgets/shimmer_loading.dart';
import '../../widgets/toast_helper.dart';
import '../../widgets/confirm_dialog.dart';
import '../../widgets/app_bottom_sheet.dart';
import '../../constants/app_constants.dart';

class ProductionDetailScreen extends StatefulWidget {
  final dynamic runId;
  const ProductionDetailScreen({super.key, required this.runId});
  @override State<ProductionDetailScreen> createState() => _ProductionDetailScreenState();
}

class _ProductionDetailScreenState extends State<ProductionDetailScreen> {
  Map<String, dynamic>? _run;
  bool _loading = true;
  String? _error;
  final _curFmt = NumberFormat('#,##0.00');

  @override
  void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    setState(() { _loading = true; _error = null; });
    try {
      final data = await ApiService.get('/api/manufacturing/production/${widget.runId}');
      setState(() { _run = data is Map ? Map<String, dynamic>.from(data) : null; _loading = false; });
    } catch (e) { setState(() { _error = e.toString(); _loading = false; }); }
  }

  Future<void> _updateStatus(String status) async {
    try {
      await ApiService.put('/api/manufacturing/production/${widget.runId}/status', {'status': status});
      if (mounted) { ToastHelper.show(context, message: 'Status updated to $status'); _load(); }
    } catch (e) { if (mounted) ToastHelper.show(context, message: 'Update failed', error: true); }
  }

  Future<void> _confirmStatusChange(String status) async {
    final confirmed = await ConfirmDialog.show(context, title: 'Update Status', message: 'Change status to "$status"?');
    if (confirmed == true) _updateStatus(status);
  }

  double get _progress {
    final r = _run;
    if (r == null) return 0;
    final planned = (r['quantity_planned'] ?? 0).toDouble();
    final produced = (r['quantity_produced'] ?? 0).toDouble();
    if (planned <= 0) return 0;
    return (produced / planned).clamp(0.0, 1.0);
  }

  Color _statusColor(String? status) {
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
      appBar: AppBar(title: const Text('Production Details')),
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
                  child: SingleChildScrollView(
                    padding: const EdgeInsets.fromLTRB(16, 8, 16, 100),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        _buildHeader(),
                        const SizedBox(height: 12),
                        _buildProgressCard(),
                        const SizedBox(height: 12),
                        _buildInfoCard(),
                        const SizedBox(height: 12),
                        _buildIngredientsConsumed(),
                        const SizedBox(height: 12),
                        _buildOutputWaste(),
                        const SizedBox(height: 12),
                        _buildTimeline(),
                        const SizedBox(height: 16),
                        _buildActions(),
                      ],
                    ),
                  ),
                ),
    );
  }

  Widget _buildHeader() {
    final r = _run!;
    return GlassCard(
      child: Row(
        children: [
          Container(
            width: 48, height: 48,
            decoration: BoxDecoration(color: _statusColor(r['status']).withValues(alpha: 0.12), borderRadius: BorderRadius.circular(12)),
            child: Icon(Icons.production_quantity_limits_rounded, size: 24, color: _statusColor(r['status'])),
          ),
          const SizedBox(width: 14),
          Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            Text(r['recipe_name'] ?? '', style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w700, color: AppColors.textPri)),
            const SizedBox(height: 4),
            Text('Batch: ${r['batch_number'] ?? ''}', style: const TextStyle(color: AppColors.textSec, fontSize: 13)),
          ])),
          StatusBadge(label: r['status'] ?? '', color: _statusColor(r['status']), bgColor: _statusColor(r['status']).withValues(alpha: 0.12)),
        ],
      ),
    );
  }

  Widget _buildProgressCard() {
    final r = _run!;
    final planned = (r['quantity_planned'] ?? 0).toDouble();
    final produced = (r['quantity_produced'] ?? 0).toDouble();
    return GlassCard(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
            const Text('Progress', style: TextStyle(fontSize: 15, fontWeight: FontWeight.w700)),
            Text('${(_progress * 100).toStringAsFixed(0)}%', style: TextStyle(fontSize: 15, fontWeight: FontWeight.w700, color: _progress >= 1.0 ? AppColors.success : AppColors.primary)),
          ]),
          const SizedBox(height: 10),
          ClipRRect(
            borderRadius: BorderRadius.circular(8),
            child: LinearProgressIndicator(
              value: _progress,
              minHeight: 10,
              backgroundColor: AppColors.background,
              valueColor: AlwaysStoppedAnimation(_progress >= 1.0 ? AppColors.success : AppColors.primary),
            ),
          ),
          const SizedBox(height: 8),
          Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
            Text('Produced: ${produced.toInt()}', style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
            Text('Planned: ${planned.toInt()}', style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
          ]),
        ],
      ),
    );
  }

  Widget _buildInfoCard() {
    final r = _run!;
    final planned = (r['quantity_planned'] ?? 0).toDouble();
    final produced = (r['quantity_produced'] ?? 0).toDouble();
    final yieldPct = planned > 0 ? (produced / planned * 100) : 0.0;
    return GlassCard(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text('Production Info', style: TextStyle(fontSize: 15, fontWeight: FontWeight.w700)),
          const SizedBox(height: 12),
          Row(children: [
            Expanded(child: _infoItem('Start Date', fmtDate(r['start_date']))),
            Expanded(child: _infoItem('End Date', r['end_date'] != null ? fmtDate(r['end_date']) : '-')),
          ]),
          const SizedBox(height: 10),
          Row(children: [
            Expanded(child: _infoItem('Quantity Planned', '${planned.toInt()}')),
            Expanded(child: _infoItem('Quantity Produced', '${produced.toInt()}')),
          ]),
          const SizedBox(height: 10),
          Row(children: [
            Expanded(child: _infoItem('Yield %', '${yieldPct.toStringAsFixed(1)}%')),
            Expanded(child: _infoItem('Waste', '${r['waste_quantity'] ?? 0}')),
          ]),
        ],
      ),
    );
  }

  Widget _infoItem(String label, String value) {
    return Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
      Text(label, style: const TextStyle(color: AppColors.textSec, fontSize: 11)),
      const SizedBox(height: 4),
      Text(value, style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 14, color: AppColors.textPri)),
    ]);
  }

  Widget _buildIngredientsConsumed() {
    final ingredients = _run?['ingredients_consumed'] is List ? _run!['ingredients_consumed'] as List : [];
    return GlassCard(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text('Ingredients Consumed', style: TextStyle(fontSize: 15, fontWeight: FontWeight.w700)),
          const SizedBox(height: 12),
          if (ingredients.isEmpty)
            const Text('No data recorded', style: TextStyle(color: AppColors.textSec))
          else ...[
            Row(children: [
              const Expanded(flex: 3, child: Text('Product', style: TextStyle(color: AppColors.textSec, fontSize: 11, fontWeight: FontWeight.w600))),
              const Expanded(flex: 2, child: Text('Planned', style: TextStyle(color: AppColors.textSec, fontSize: 11, fontWeight: FontWeight.w600), textAlign: TextAlign.center)),
              const Expanded(flex: 2, child: Text('Actual', style: TextStyle(color: AppColors.textSec, fontSize: 11, fontWeight: FontWeight.w600), textAlign: TextAlign.center)),
              const Expanded(flex: 2, child: Text('Variance', style: TextStyle(color: AppColors.textSec, fontSize: 11, fontWeight: FontWeight.w600), textAlign: TextAlign.right)),
            ]),
            const SizedBox(height: 8),
            ...List.generate(ingredients.length, (i) {
              final ing = ingredients[i];
              final planned = (ing['planned_qty'] ?? 0).toDouble();
              final actual = (ing['actual_qty'] ?? 0).toDouble();
              final variance = actual - planned;
              return Padding(
                padding: EdgeInsets.only(bottom: i < ingredients.length - 1 ? 8 : 0),
                child: Row(children: [
                  Expanded(flex: 3, child: Text(ing['product_name'] ?? '', style: const TextStyle(fontWeight: FontWeight.w500, fontSize: 13))),
                  Expanded(flex: 2, child: Text('${planned.toStringAsFixed(1)} ${ing['unit'] ?? ''}', style: const TextStyle(color: AppColors.textSec, fontSize: 12), textAlign: TextAlign.center)),
                  Expanded(flex: 2, child: Text('${actual.toStringAsFixed(1)} ${ing['unit'] ?? ''}', style: const TextStyle(color: AppColors.textSec, fontSize: 12), textAlign: TextAlign.center)),
                  Expanded(flex: 2, child: Text('${variance >= 0 ? '+' : ''}${variance.toStringAsFixed(1)}', style: TextStyle(fontWeight: FontWeight.w600, fontSize: 12, color: variance > 0 ? AppColors.danger : variance < 0 ? AppColors.success : AppColors.textSec), textAlign: TextAlign.right)),
                ]),
              );
            }),
          ],
        ],
      ),
    );
  }

  Widget _buildOutputWaste() {
    final r = _run!;
    return GlassCard(
      child: Row(
        children: [
          Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            const Text('Finished Goods', style: TextStyle(color: AppColors.textSec, fontSize: 11)),
            const SizedBox(height: 4),
            Text('${r['quantity_produced'] ?? 0}', style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 20, color: AppColors.success)),
          ])),
          Container(height: 40, width: 1, color: AppColors.border),
          Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.end, children: [
            const Text('Waste', style: TextStyle(color: AppColors.textSec, fontSize: 11)),
            const SizedBox(height: 4),
            Text('${r['waste_quantity'] ?? 0}', style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 20, color: AppColors.danger)),
          ])),
        ],
      ),
    );
  }

  Widget _buildTimeline() {
    final logs = _run?['status_logs'] is List ? _run!['status_logs'] as List : [];
    if (logs.isEmpty) return const SizedBox.shrink();
    return GlassCard(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text('Timeline', style: TextStyle(fontSize: 15, fontWeight: FontWeight.w700)),
          const SizedBox(height: 12),
          ...List.generate(logs.length, (i) {
            final log = logs[i];
            return Padding(
              padding: EdgeInsets.only(bottom: i < logs.length - 1 ? 12 : 0),
              child: Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Column(children: [
                    Container(width: 12, height: 12, decoration: BoxDecoration(color: _statusColor(log['status']), shape: BoxShape.circle)),
                    if (i < logs.length - 1) Container(width: 2, height: 24, color: AppColors.border),
                  ]),
                  const SizedBox(width: 12),
                  Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                    Text('Status: ${log['status'] ?? ''}', style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13)),
                    if (log['changed_at'] != null) Text(fmtDate(log['changed_at']), style: const TextStyle(color: AppColors.textSec, fontSize: 11)),
                    if (log['notes'] != null) Text(log['notes'], style: const TextStyle(color: AppColors.textSec, fontSize: 11)),
                  ])),
                ],
              ),
            );
          }),
        ],
      ),
    );
  }

  Widget _buildActions() {
    final status = _run?['status'] ?? '';
    return Column(
      children: [
        if (status == 'planned')
          SizedBox(
            width: double.infinity,
            height: 48,
            child: ElevatedButton.icon(
              onPressed: () => _confirmStatusChange('in_progress'),
              icon: const Icon(Icons.play_arrow_rounded),
              label: const Text('Start Production (In Progress)'),
              style: ElevatedButton.styleFrom(backgroundColor: AppColors.warning, foregroundColor: Colors.white),
            ),
          ),
        if (status == 'in_progress') ...[
          SizedBox(
            width: double.infinity,
            height: 48,
            child: ElevatedButton.icon(
              onPressed: () => _confirmStatusChange('completed'),
              icon: const Icon(Icons.check_circle_rounded),
              label: const Text('Mark as Completed'),
              style: ElevatedButton.styleFrom(backgroundColor: AppColors.success, foregroundColor: Colors.white),
            ),
          ),
          const SizedBox(height: 10),
          SizedBox(
            width: double.infinity,
            height: 48,
            child: OutlinedButton.icon(
              onPressed: () => _confirmStatusChange('cancelled'),
              icon: const Icon(Icons.cancel_outlined, color: AppColors.danger),
              label: const Text('Cancel Production', style: TextStyle(color: AppColors.danger)),
              style: OutlinedButton.styleFrom(side: const BorderSide(color: AppColors.danger)),
            ),
          ),
        ],
        if (status != 'cancelled' && status != 'completed') ...[
          const SizedBox(height: 10),
          SizedBox(
            width: double.infinity,
            height: 48,
            child: OutlinedButton.icon(
              onPressed: () => _showRecordUsage(),
              icon: const Icon(Icons.edit_note_rounded),
              label: const Text('Record Ingredient Usage'),
            ),
          ),
          const SizedBox(height: 10),
          SizedBox(
            width: double.infinity,
            height: 48,
            child: OutlinedButton.icon(
              onPressed: () => _showRecordOutput(),
              icon: const Icon(Icons.output_rounded),
              label: const Text('Record Output'),
            ),
          ),
        ],
      ],
    );
  }

  void _showRecordUsage() {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (_) => _RecordUsageSheet(runId: widget.runId, onSaved: _load),
    );
  }

  void _showRecordOutput() {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (_) => _RecordOutputSheet(runId: widget.runId, onSaved: _load),
    );
  }
}

class _RecordUsageSheet extends StatefulWidget {
  final dynamic runId;
  final VoidCallback onSaved;
  const _RecordUsageSheet({required this.runId, required this.onSaved});
  @override State<_RecordUsageSheet> createState() => _RecordUsageSheetState();
}

class _RecordUsageSheetState extends State<_RecordUsageSheet> {
  List<Map<String, dynamic>> _ingredients = [];
  bool _loading = true;
  bool _saving = false;

  @override
  void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    try {
      final data = await ApiService.get('/api/manufacturing/production/${widget.runId}/ingredients');
      final list = data is List ? data.map((e) => Map<String, dynamic>.from(e)).toList() : <Map<String, dynamic>>[];
      setState(() { _ingredients = list.map((e) => {
        ...e,
        'actual_qty_ctrl': TextEditingController(text: (e['planned_qty'] ?? 0).toString()),
      }).toList(); _loading = false; });
    } catch (_) { setState(() => _loading = false); }
  }

  Future<void> _save() async {
    setState(() => _saving = true);
    try {
      await ApiService.put('/api/manufacturing/production/${widget.runId}/ingredients', {
        'ingredients': _ingredients.map((e) => {
          'product_id': e['product_id'],
          'actual_qty': double.tryParse(e['actual_qty_ctrl'].text) ?? 0,
        }).toList(),
      });
      widget.onSaved();
      if (mounted) Navigator.pop(context);
    } catch (_) { setState(() => _saving = false); }
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: const BoxDecoration(color: Colors.white, borderRadius: BorderRadius.vertical(top: Radius.circular(24))),
      padding: EdgeInsets.only(bottom: MediaQuery.of(context).viewInsets.bottom),
      child: SingleChildScrollView(
        padding: const EdgeInsets.fromLTRB(24, 16, 24, 32),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          mainAxisSize: MainAxisSize.min,
          children: [
            Center(child: Container(width: 40, height: 4, decoration: BoxDecoration(color: AppColors.border, borderRadius: BorderRadius.circular(4)))),
            const SizedBox(height: 16),
            Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
              const Text('Record Usage', style: TextStyle(fontSize: 18, fontWeight: FontWeight.w700)),
              IconButton(icon: const Icon(Icons.close), onPressed: () => Navigator.pop(context)),
            ]),
            const SizedBox(height: 16),
            if (_loading) const LinearProgressIndicator(),
            if (!_loading) ...List.generate(_ingredients.length, (i) {
              final ing = _ingredients[i];
              return Padding(
                padding: const EdgeInsets.only(bottom: 12),
                child: Row(
                  children: [
                    Expanded(flex: 3, child: Text(ing['product_name'] ?? '', style: const TextStyle(fontWeight: FontWeight.w500))),
                    Expanded(
                      flex: 2,
                      child: TextField(
                        controller: ing['actual_qty_ctrl'],
                        keyboardType: TextInputType.number,
                        decoration: InputDecoration(
                          labelText: ing['unit'] ?? 'qty',
                          isDense: true,
                          contentPadding: const EdgeInsets.symmetric(horizontal: 8, vertical: 10),
                        ),
                      ),
                    ),
                  ],
                ),
              );
            }),
            const SizedBox(height: 16),
            SizedBox(
              width: double.infinity,
              height: 48,
              child: ElevatedButton(
                onPressed: _saving ? null : _save,
                child: _saving ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(strokeWidth: 2.5, color: Colors.white)) : const Text('Save Usage'),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _RecordOutputSheet extends StatefulWidget {
  final dynamic runId;
  final VoidCallback onSaved;
  const _RecordOutputSheet({required this.runId, required this.onSaved});
  @override State<_RecordOutputSheet> createState() => _RecordOutputSheetState();
}

class _RecordOutputSheetState extends State<_RecordOutputSheet> {
  late TextEditingController _outputCtrl, _wasteCtrl;
  bool _saving = false;

  @override
  void initState() { super.initState(); _outputCtrl = TextEditingController(); _wasteCtrl = TextEditingController(); }

  @override
  void dispose() { _outputCtrl.dispose(); _wasteCtrl.dispose(); super.dispose(); }

  Future<void> _save() async {
    setState(() => _saving = true);
    try {
      await ApiService.put('/api/manufacturing/production/${widget.runId}/output', {
        'quantity_produced': int.tryParse(_outputCtrl.text) ?? 0,
        'waste_quantity': int.tryParse(_wasteCtrl.text) ?? 0,
      });
      widget.onSaved();
      if (mounted) Navigator.pop(context);
    } catch (_) { setState(() => _saving = false); }
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: const BoxDecoration(color: Colors.white, borderRadius: BorderRadius.vertical(top: Radius.circular(24))),
      padding: EdgeInsets.only(bottom: MediaQuery.of(context).viewInsets.bottom),
      child: SingleChildScrollView(
        padding: const EdgeInsets.fromLTRB(24, 16, 24, 32),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Center(child: Container(width: 40, height: 4, decoration: BoxDecoration(color: AppColors.border, borderRadius: BorderRadius.circular(4)))),
            const SizedBox(height: 16),
            Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
              const Text('Record Output', style: TextStyle(fontSize: 18, fontWeight: FontWeight.w700)),
              IconButton(icon: const Icon(Icons.close), onPressed: () => Navigator.pop(context)),
            ]),
            const SizedBox(height: 16),
            TextFormField(controller: _outputCtrl, keyboardType: TextInputType.number, decoration: const InputDecoration(labelText: 'Good Units Produced *'), autofocus: true),
            const SizedBox(height: 12),
            TextFormField(controller: _wasteCtrl, keyboardType: TextInputType.number, decoration: const InputDecoration(labelText: 'Waste / Defective Units')),
            const SizedBox(height: 20),
            SizedBox(
              width: double.infinity,
              height: 48,
              child: ElevatedButton(
                onPressed: _saving ? null : _save,
                child: _saving ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(strokeWidth: 2.5, color: Colors.white)) : const Text('Save Output'),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
