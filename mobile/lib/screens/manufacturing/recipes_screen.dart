import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../../core/api_service.dart';
import '../../shared/theme/app_colors.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/status_badge.dart';
import '../../widgets/search_bar_widget.dart';
import '../../widgets/shimmer_loading.dart';
import '../../widgets/empty_state.dart';
import '../../constants/app_constants.dart';
import 'recipe_detail_screen.dart';
import 'recipe_form_screen.dart';

class RecipesScreen extends StatefulWidget {
  const RecipesScreen({super.key});
  @override State<RecipesScreen> createState() => _RecipesScreenState();
}

class _RecipesScreenState extends State<RecipesScreen> {
  List<Map<String, dynamic>> _recipes = [];
  List<Map<String, dynamic>> _filtered = [];
  bool _loading = true;
  String? _error;
  String _search = '';
  String _statusFilter = '';
  final _searchCtrl = TextEditingController();
  final _curFmt = NumberFormat('#,##0.00');

  final _statuses = ['', 'active', 'inactive'];
  final _statusLabels = ['All', 'Active', 'Inactive'];

  @override
  void initState() { super.initState(); _load(); }
  @override
  void dispose() { _searchCtrl.dispose(); super.dispose(); }

  Future<void> _load() async {
    setState(() { _loading = true; _error = null; });
    try {
      final data = await ApiService.get('/api/manufacturing/recipes');
      final list = data is List ? data.map((e) => Map<String, dynamic>.from(e)).toList() : <Map<String, dynamic>>[];
      setState(() { _recipes = list; _filter(); _loading = false; });
    } catch (e) { setState(() { _error = e.toString(); _loading = false; }); }
  }

  void _filter() {
    setState(() {
      _filtered = _recipes.where((r) {
        if (_statusFilter.isNotEmpty && r['status'] != _statusFilter) return false;
        if (_search.isNotEmpty && !(r['name'] ?? '').toString().toLowerCase().contains(_search.toLowerCase())) return false;
        return true;
      }).toList();
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(title: const Text('Recipes & BOMs')),
      body: Column(
        children: [
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 12, 16, 0),
            child: AppSearchBar(hint: 'Search recipes...', controller: _searchCtrl, onChanged: (v) { _search = v; _filter(); }),
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
        onPressed: () async { await Navigator.push(context, MaterialPageRoute(builder: (_) => const RecipeFormScreen())); _load(); },
        icon: const Icon(Icons.add),
        label: const Text('New Recipe'),
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
        icon: Icons.menu_book_outlined,
        title: 'No Recipes',
        subtitle: _search.isNotEmpty ? 'No recipes match your search' : 'Create your first recipe to start production',
        actionLabel: _search.isNotEmpty ? null : 'New Recipe',
        onAction: _search.isNotEmpty ? null : () async { await Navigator.push(context, MaterialPageRoute(builder: (_) => const RecipeFormScreen())); _load(); },
      );
    }
    return RefreshIndicator(
      onRefresh: _load,
      child: ListView.builder(
        padding: const EdgeInsets.fromLTRB(16, 0, 16, 100),
        itemCount: _filtered.length,
        itemBuilder: (_, i) => _recipeCard(_filtered[i]),
      ),
    );
  }

  Widget _recipeCard(Map<String, dynamic> r) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 10),
      child: GlassCard(
        onTap: () async { await Navigator.push(context, MaterialPageRoute(builder: (_) => RecipeDetailScreen(recipeId: r['id']))); _load(); },
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Container(
                  width: 40, height: 40,
                  decoration: BoxDecoration(color: AppColors.primaryLt, borderRadius: BorderRadius.circular(10)),
                  child: const Icon(Icons.menu_book_rounded, size: 20, color: AppColors.primary),
                ),
                const SizedBox(width: 12),
                Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                  Text(r['name'] ?? '', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 15, color: AppColors.textPri)),
                  if (r['version'] != null) Text('v${r['version']}', style: const TextStyle(color: AppColors.textSec, fontSize: 11)),
                ])),
                StatusBadge.fromStatus(r['status'] ?? ''),
              ],
            ),
            const Divider(height: 20),
            Row(
              children: [
                _infoChip(Icons.inventory_2_outlined, '${r['ingredients_count'] ?? 0} ingredients'),
                const SizedBox(width: 12),
                _infoChip(Icons.science_outlined, 'Yield: ${r['expected_yield'] ?? 0}'),
              ],
            ),
            const SizedBox(height: 10),
            Row(
              children: [
                Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                  Text('Cost', style: const TextStyle(color: AppColors.textSec, fontSize: 11)),
                  Text('${AppConstants.currency} ${_curFmt.format(r['production_cost'] ?? 0)}', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14, color: AppColors.textPri)),
                ]),
                const Spacer(),
                Column(crossAxisAlignment: CrossAxisAlignment.end, children: [
                  Text('Selling Price', style: const TextStyle(color: AppColors.textSec, fontSize: 11)),
                  Text('${AppConstants.currency} ${_curFmt.format(r['selling_price'] ?? 0)}', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14, color: AppColors.primary)),
                ]),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _infoChip(IconData icon, String text) {
    return Row(
      mainAxisSize: MainAxisSize.min,
      children: [
        Icon(icon, size: 14, color: AppColors.textSec),
        const SizedBox(width: 4),
        Text(text, style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
      ],
    );
  }
}
