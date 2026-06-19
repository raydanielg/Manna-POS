import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../../core/api_service.dart';
import '../../shared/theme/app_colors.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/status_badge.dart';
import '../../widgets/shimmer_loading.dart';
import '../../widgets/confirm_dialog.dart';
import '../../widgets/toast_helper.dart';
import '../../constants/app_constants.dart';
import 'recipe_form_screen.dart';
import 'production_form_screen.dart';

class RecipeDetailScreen extends StatefulWidget {
  final dynamic recipeId;
  const RecipeDetailScreen({super.key, required this.recipeId});
  @override State<RecipeDetailScreen> createState() => _RecipeDetailScreenState();
}

class _RecipeDetailScreenState extends State<RecipeDetailScreen> {
  Map<String, dynamic>? _recipe;
  bool _loading = true;
  String? _error;
  final _curFmt = NumberFormat('#,##0.00');

  @override
  void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    setState(() { _loading = true; _error = null; });
    try {
      final data = await ApiService.get('/api/manufacturing/recipes/${widget.recipeId}');
      setState(() { _recipe = data is Map ? Map<String, dynamic>.from(data) : null; _loading = false; });
    } catch (e) { setState(() { _error = e.toString(); _loading = false; }); }
  }

  Future<void> _delete() async {
    final confirmed = await ConfirmDialog.show(context, title: 'Delete Recipe', message: 'Are you sure you want to delete "${_recipe?['name']}"? This cannot be undone.');
    if (confirmed != true) return;
    try {
      await ApiService.delete('/api/manufacturing/recipes/${widget.recipeId}');
      if (mounted) { ToastHelper.show(context, message: 'Recipe deleted'); Navigator.pop(context); }
    } catch (e) { if (mounted) ToastHelper.show(context, message: 'Delete failed: $e', error: true); }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        title: const Text('Recipe Details'),
        actions: [
          PopupMenuButton<String>(
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
            onSelected: (v) async {
              if (v == 'edit') {
                await Navigator.push(context, MaterialPageRoute(builder: (_) => RecipeFormScreen(recipe: _recipe)));
                _load();
              } else if (v == 'delete') {
                _delete();
              }
            },
            itemBuilder: (_) => [
              const PopupMenuItem(value: 'edit', child: Row(children: [Icon(Icons.edit_outlined, size: 18), SizedBox(width: 8), Text('Edit')])),
              const PopupMenuItem(value: 'delete', child: Row(children: [Icon(Icons.delete_outline, size: 18, color: AppColors.danger), SizedBox(width: 8), Text('Delete', style: TextStyle(color: AppColors.danger))])),
            ],
          ),
        ],
      ),
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
                        _buildInfoCard(),
                        const SizedBox(height: 12),
                        _buildIngredientsCard(),
                        const SizedBox(height: 12),
                        _buildTotalCost(),
                        const SizedBox(height: 12),
                        _buildProductionHistory(),
                        const SizedBox(height: 16),
                        _buildActions(),
                      ],
                    ),
                  ),
                ),
    );
  }

  Widget _buildHeader() {
    final r = _recipe!;
    return GlassCard(
      child: Row(
        children: [
          Container(
            width: 48, height: 48,
            decoration: BoxDecoration(color: AppColors.primaryLt, borderRadius: BorderRadius.circular(12)),
            child: const Icon(Icons.menu_book_rounded, size: 24, color: AppColors.primary),
          ),
          const SizedBox(width: 14),
          Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            Text(r['name'] ?? '', style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w700, color: AppColors.textPri)),
            const SizedBox(height: 4),
            Row(children: [
              StatusBadge.fromStatus(r['status'] ?? ''),
              if (r['version'] != null) ...[const SizedBox(width: 8), Text('v${r['version']}', style: const TextStyle(color: AppColors.textSec, fontSize: 12))],
            ]),
          ])),
        ],
      ),
    );
  }

  Widget _buildInfoCard() {
    final r = _recipe!;
    final cost = (r['production_cost'] ?? 0).toDouble();
    final price = (r['selling_price'] ?? 0).toDouble();
    final margin = cost > 0 ? ((price - cost) / cost * 100) : 0.0;
    return GlassCard(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text('Production Info', style: TextStyle(fontSize: 15, fontWeight: FontWeight.w700)),
          const SizedBox(height: 12),
          Row(children: [
            Expanded(child: _infoItem('Production Cost', '${AppConstants.currency} ${_curFmt.format(cost)}', AppColors.textPri)),
            Expanded(child: _infoItem('Selling Price', '${AppConstants.currency} ${_curFmt.format(price)}', AppColors.primary)),
          ]),
          const SizedBox(height: 10),
          Row(children: [
            Expanded(child: _infoItem('Expected Yield', '${r['expected_yield'] ?? 0} units', AppColors.textPri)),
            Expanded(child: _infoItem('Profit Margin', '${margin.toStringAsFixed(1)}%', margin >= 0 ? AppColors.success : AppColors.danger)),
          ]),
        ],
      ),
    );
  }

  Widget _infoItem(String label, String value, Color color) {
    return Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
      Text(label, style: const TextStyle(color: AppColors.textSec, fontSize: 11)),
      const SizedBox(height: 4),
      Text(value, style: TextStyle(fontWeight: FontWeight.w700, fontSize: 15, color: color)),
    ]);
  }

  Widget _buildIngredientsCard() {
    final r = _recipe!;
    final ingredients = r['ingredients'] is List ? r['ingredients'] as List : [];
    return GlassCard(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
            const Text('Ingredients / BOM', style: TextStyle(fontSize: 15, fontWeight: FontWeight.w700)),
            Text('${ingredients.length} items', style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
          ]),
          const SizedBox(height: 12),
          if (ingredients.isEmpty)
            const Text('No ingredients added', style: TextStyle(color: AppColors.textSec))
          else
            ...List.generate(ingredients.length, (i) {
              final ing = ingredients[i];
              final subtotal = ((ing['quantity'] ?? 0).toDouble() * (ing['cost'] ?? 0).toDouble());
              return Padding(
                padding: EdgeInsets.only(bottom: i < ingredients.length - 1 ? 10 : 0),
                child: Row(
                  children: [
                    Expanded(flex: 3, child: Text(ing['product_name'] ?? '', style: const TextStyle(fontWeight: FontWeight.w500, fontSize: 13))),
                    Expanded(flex: 2, child: Text('${ing['quantity']} ${ing['unit'] ?? ''}', style: const TextStyle(color: AppColors.textSec, fontSize: 12), textAlign: TextAlign.center)),
                    Expanded(flex: 2, child: Text('${AppConstants.currency} ${_curFmt.format(ing['cost'] ?? 0)}', style: const TextStyle(color: AppColors.textSec, fontSize: 12), textAlign: TextAlign.center)),
                    Expanded(flex: 2, child: Text('${AppConstants.currency} ${_curFmt.format(subtotal)}', style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13), textAlign: TextAlign.right)),
                  ],
                ),
              );
            }),
        ],
      ),
    );
  }

  Widget _buildTotalCost() {
    final r = _recipe!;
    final total = (r['production_cost'] ?? 0).toDouble();
    return GlassCard(
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          const Text('Total Production Cost', style: TextStyle(fontSize: 15, fontWeight: FontWeight.w600)),
          Text('${AppConstants.currency} ${_curFmt.format(total)}', style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w800, color: AppColors.primary)),
        ],
      ),
    );
  }

  Widget _buildProductionHistory() {
    final history = _recipe?['production_history'] is List ? _recipe!['production_history'] as List : [];
    if (history.isEmpty) return const SizedBox.shrink();
    return GlassCard(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text('Production History', style: TextStyle(fontSize: 15, fontWeight: FontWeight.w700)),
          const SizedBox(height: 12),
          ...List.generate(history.length, (i) {
            final h = history[i];
            return Padding(
              padding: EdgeInsets.only(bottom: i < history.length - 1 ? 10 : 0),
              child: Row(
                children: [
                  Container(
                    width: 36, height: 36,
                    decoration: BoxDecoration(color: AppColors.primaryLt, borderRadius: BorderRadius.circular(8)),
                    child: const Icon(Icons.production_quantity_limits_rounded, size: 18, color: AppColors.primary),
                  ),
                  const SizedBox(width: 10),
                  Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                    Text('Batch: ${h['batch_number'] ?? ''}', style: const TextStyle(fontWeight: FontWeight.w500, fontSize: 13)),
                    Text('${h['quantity_produced'] ?? 0} units', style: const TextStyle(color: AppColors.textSec, fontSize: 11)),
                  ])),
                  StatusBadge.fromStatus(h['status'] ?? ''),
                ],
              ),
            );
          }),
        ],
      ),
    );
  }

  Widget _buildActions() {
    return Row(
      children: [
        Expanded(
          child: SizedBox(
            height: 48,
            child: ElevatedButton.icon(
              onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (_) => ProductionFormScreen(recipeId: widget.recipeId))),
              icon: const Icon(Icons.play_arrow_rounded, size: 20),
              label: const Text('Start Production'),
              style: ElevatedButton.styleFrom(backgroundColor: AppColors.success, foregroundColor: Colors.white),
            ),
          ),
        ),
        const SizedBox(width: 12),
        Expanded(
          child: SizedBox(
            height: 48,
            child: OutlinedButton.icon(
              onPressed: () async { await Navigator.push(context, MaterialPageRoute(builder: (_) => RecipeFormScreen(recipe: _recipe))); _load(); },
              icon: const Icon(Icons.edit_outlined, size: 20),
              label: const Text('Edit Recipe'),
            ),
          ),
        ),
      ],
    );
  }
}
