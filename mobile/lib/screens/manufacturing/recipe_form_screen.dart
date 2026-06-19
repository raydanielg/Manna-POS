import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../../core/api_service.dart';
import '../../shared/theme/app_colors.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/search_bar_widget.dart';
import '../../widgets/toast_helper.dart';
import '../../constants/app_constants.dart';

class RecipeFormScreen extends StatefulWidget {
  final Map<String, dynamic>? recipe;
  const RecipeFormScreen({super.key, this.recipe});
  @override State<RecipeFormScreen> createState() => _RecipeFormScreenState();
}

class _RecipeFormScreenState extends State<RecipeFormScreen> {
  final _form = GlobalKey<FormState>();
  late TextEditingController _nameCtrl, _yieldCtrl, _notesCtrl, _priceCtrl;
  bool _isActive = true;
  bool _saving = false;
  String? _error;
  bool _isEdit = false;

  List<Map<String, dynamic>> _ingredients = [];
  List<Map<String, dynamic>> _searchResults = [];
  bool _searching = false;

  final _curFmt = NumberFormat('#,##0.00');

  @override
  void initState() {
    super.initState();
    final r = widget.recipe;
    _isEdit = r != null;
    _nameCtrl = TextEditingController(text: r?['name']);
    _yieldCtrl = TextEditingController(text: r?['expected_yield']?.toString());
    _notesCtrl = TextEditingController(text: r?['instructions']);
    _priceCtrl = TextEditingController(text: r?['selling_price']?.toString());
    _isActive = r?['status'] == 'active';
    if (r != null && r['ingredients'] is List) {
      _ingredients = (r['ingredients'] as List).map((e) => Map<String, dynamic>.from(e)).toList();
    }
  }

  @override
  void dispose() {
    _nameCtrl.dispose(); _yieldCtrl.dispose(); _notesCtrl.dispose(); _priceCtrl.dispose();
    super.dispose();
  }

  double get _totalCost => _ingredients.fold(0.0, (sum, ing) => sum + ((ing['quantity'] ?? 0).toDouble() * (ing['cost'] ?? 0).toDouble()));

  Future<void> _searchProducts(String q) async {
    if (q.isEmpty) { setState(() => _searchResults = []); return; }
    setState(() => _searching = true);
    try {
      final data = await ApiService.get('/api/products/search?q=${Uri.encodeComponent(q)}');
      setState(() { _searchResults = data is List ? data.map((e) => Map<String, dynamic>.from(e)).toList() : []; _searching = false; });
    } catch (_) { setState(() => _searching = false); }
  }

  void _addIngredient(Map<String, dynamic> product) {
    setState(() {
      _ingredients.add({
        'product_id': product['id'],
        'product_name': product['name'],
        'quantity': 1.0,
        'unit': product['unit'] ?? 'pcs',
        'cost': product['cost_price']?.toDouble() ?? 0.0,
      });
    });
  }

  void _removeIngredient(int index) {
    setState(() => _ingredients.removeAt(index));
  }

  Future<void> _save() async {
    if (!_form.currentState!.validate()) return;
    setState(() { _saving = true; _error = null; });
    try {
      final body = {
        'name': _nameCtrl.text.trim(),
        'expected_yield': int.tryParse(_yieldCtrl.text) ?? 0,
        'selling_price': double.tryParse(_priceCtrl.text) ?? 0,
        'instructions': _notesCtrl.text,
        'status': _isActive ? 'active' : 'inactive',
        'ingredients': _ingredients.map((ing) => {
          'product_id': ing['product_id'],
          'quantity': ing['quantity'],
          'cost': ing['cost'],
        }).toList(),
      };
      if (_isEdit) {
        await ApiService.put('/api/manufacturing/recipes/${widget.recipe!['id']}', body);
        if (mounted) ToastHelper.show(context, message: 'Recipe updated');
      } else {
        await ApiService.post('/api/manufacturing/recipes', body);
        if (mounted) ToastHelper.show(context, message: 'Recipe created');
      }
      if (mounted) Navigator.pop(context);
    } on ApiException catch (e) { setState(() { _error = e.message; _saving = false; }); }
    catch (_) { setState(() { _error = 'Save failed'; _saving = false; }); }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(title: Text(_isEdit ? 'Edit Recipe' : 'New Recipe')),
      body: SingleChildScrollView(
        padding: const EdgeInsets.fromLTRB(16, 8, 16, 100),
        child: Form(
          key: _form,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              GlassCard(
                child: Column(
                  children: [
                    TextFormField(controller: _nameCtrl, decoration: const InputDecoration(labelText: 'Recipe Name *'), validator: (v) => v!.trim().isEmpty ? 'Required' : null),
                    const SizedBox(height: 12),
                    Row(children: [
                      Expanded(child: TextFormField(controller: _yieldCtrl, keyboardType: TextInputType.number, decoration: const InputDecoration(labelText: 'Expected Yield *'), validator: (v) => v!.isEmpty ? 'Required' : null)),
                      const SizedBox(width: 12),
                      Expanded(child: TextFormField(controller: _priceCtrl, keyboardType: TextInputType.number, decoration: const InputDecoration(labelText: 'Selling Price'))),
                    ]),
                    const SizedBox(height: 12),
                    TextFormField(controller: _notesCtrl, decoration: const InputDecoration(labelText: 'Instructions / Notes'), maxLines: 3),
                    const SizedBox(height: 12),
                    SwitchListTile(
                      title: const Text('Active', style: TextStyle(fontSize: 14)),
                      value: _isActive,
                      onChanged: (v) => setState(() => _isActive = v),
                      contentPadding: EdgeInsets.zero,
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 16),
              GlassCard(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text('Ingredients', style: TextStyle(fontSize: 15, fontWeight: FontWeight.w700)),
                    const SizedBox(height: 12),
                    AppSearchBar(hint: 'Search products to add...', onChanged: _searchProducts),
                    if (_searching) const Padding(padding: EdgeInsets.all(8), child: LinearProgressIndicator()),
                    if (_searchResults.isNotEmpty) ...[
                      const SizedBox(height: 8),
                      Container(
                        constraints: const BoxConstraints(maxHeight: 200),
                        decoration: BoxDecoration(border: Border.all(color: AppColors.border), borderRadius: BorderRadius.circular(10)),
                        child: ListView.separated(
                          shrinkWrap: true,
                          itemCount: _searchResults.length,
                          separatorBuilder: (_, __) => const Divider(height: 1),
                          itemBuilder: (_, i) {
                            final p = _searchResults[i];
                            return ListTile(
                              dense: true,
                              leading: Container(
                                width: 32, height: 32,
                                decoration: BoxDecoration(color: AppColors.primaryLt, borderRadius: BorderRadius.circular(8)),
                                child: const Icon(Icons.inventory_2_outlined, size: 16, color: AppColors.primary),
                              ),
                              title: Text(p['name'] ?? '', style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w500)),
                              subtitle: Text('${AppConstants.currency} ${_curFmt.format(p['cost_price']?.toDouble() ?? 0)}', style: const TextStyle(fontSize: 11)),
                              trailing: IconButton(icon: const Icon(Icons.add_circle_outline, color: AppColors.primary), onPressed: () => _addIngredient(p)),
                            );
                          },
                        ),
                      ),
                    ],
                    const SizedBox(height: 12),
                    if (_ingredients.isEmpty)
                      const Padding(padding: EdgeInsets.all(16), child: Center(child: Text('No ingredients added', style: TextStyle(color: AppColors.textSec))))
                    else
                      ...List.generate(_ingredients.length, (i) {
                        final ing = _ingredients[i];
                        return Padding(
                          padding: EdgeInsets.only(bottom: i < _ingredients.length - 1 ? 8 : 0),
                          child: Row(
                            children: [
                              Expanded(flex: 3, child: Text(ing['product_name'] ?? '', style: const TextStyle(fontWeight: FontWeight.w500, fontSize: 13))),
                              SizedBox(width: 40, child: Text('${ing['quantity']}', style: const TextStyle(fontSize: 12))),
                              Expanded(flex: 2, child: Text(ing['unit'] ?? '', style: const TextStyle(color: AppColors.textSec, fontSize: 12))),
                              Expanded(flex: 2, child: Text('${AppConstants.currency} ${_curFmt.format(ing['cost'] ?? 0)}', style: const TextStyle(fontSize: 12, color: AppColors.textSec), textAlign: TextAlign.right)),
                              IconButton(icon: const Icon(Icons.remove_circle_outline, size: 18, color: AppColors.danger), onPressed: () => _removeIngredient(i)),
                            ],
                          ),
                        );
                      }),
                    const SizedBox(height: 8),
                    Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
                      const Text('Total Cost', style: TextStyle(fontSize: 15, fontWeight: FontWeight.w700)),
                      Text('${AppConstants.currency} ${_curFmt.format(_totalCost)}', style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w800, color: AppColors.primary)),
                    ]),
                    if (_priceCtrl.text.isNotEmpty) ...[
                      const SizedBox(height: 4),
                      Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
                        const Text('Suggested Margin', style: TextStyle(color: AppColors.textSec, fontSize: 11)),
                        Text(
                          _totalCost > 0 ? '+${((double.tryParse(_priceCtrl.text) ?? 0) - _totalCost) / _totalCost * 100).toStringAsFixed(1)}%' : '-',
                          style: TextStyle(fontSize: 12, fontWeight: FontWeight.w600, color: (double.tryParse(_priceCtrl.text) ?? 0) > _totalCost ? AppColors.success : AppColors.danger),
                        ),
                      ]),
                    ],
                  ],
                ),
              ),
              if (_error != null) ...[
                const SizedBox(height: 12),
                Container(padding: const EdgeInsets.all(12), decoration: BoxDecoration(color: AppColors.dangerLt, borderRadius: BorderRadius.circular(10)),
                  child: Text(_error!, style: const TextStyle(color: AppColors.danger))),
              ],
              const SizedBox(height: 20),
              SizedBox(
                width: double.infinity,
                height: 52,
                child: ElevatedButton(
                  onPressed: _saving ? null : _save,
                  child: _saving
                      ? const SizedBox(width: 22, height: 22, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5))
                      : Text(_isEdit ? 'Update Recipe' : 'Save Recipe', style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w700)),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
