import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../../core/api_service.dart';
import '../../shared/theme/app_colors.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/search_bar_widget.dart';
import '../../widgets/toast_helper.dart';
import '../../constants/app_constants.dart';

class ProductionFormScreen extends StatefulWidget {
  final dynamic recipeId;
  const ProductionFormScreen({super.key, this.recipeId});
  @override State<ProductionFormScreen> createState() => _ProductionFormScreenState();
}

class _ProductionFormScreenState extends State<ProductionFormScreen> {
  final _form = GlobalKey<FormState>();
  late TextEditingController _qtyCtrl, _batchCtrl, _notesCtrl;
  DateTime _startDate = DateTime.now();
  DateTime _endDate = DateTime.now().add(const Duration(days: 7));
  bool _saving = false;
  String? _error;

  Map<String, dynamic>? _selectedRecipe;
  List<Map<String, dynamic>> _searchResults = [];
  bool _searching = false;
  bool _loadingRecipe = false;

  List<Map<String, dynamic>> _ingredientCheck = [];
  bool _checkingStock = false;

  final _dateFmt = DateFormat('dd MMM yyyy');

  @override
  void initState() {
    super.initState();
    _qtyCtrl = TextEditingController();
    _batchCtrl = TextEditingController();
    _notesCtrl = TextEditingController();
    if (widget.recipeId != null) _loadRecipe();
  }

  @override
  void dispose() {
    _qtyCtrl.dispose(); _batchCtrl.dispose(); _notesCtrl.dispose();
    super.dispose();
  }

  Future<void> _loadRecipe() async {
    setState(() => _loadingRecipe = true);
    try {
      final data = await ApiService.get('/api/manufacturing/recipes/${widget.recipeId}');
      if (data is Map) {
        final recipe = Map<String, dynamic>.from(data);
        setState(() { _selectedRecipe = recipe; _loadingRecipe = false; });
        _checkIngredients();
      }
    } catch (_) { setState(() => _loadingRecipe = false); }
  }

  Future<void> _searchRecipes(String q) async {
    if (q.isEmpty) { setState(() => _searchResults = []); return; }
    setState(() => _searching = true);
    try {
      final data = await ApiService.get('/api/manufacturing/recipes?search=${Uri.encodeComponent(q)}');
      setState(() { _searchResults = data is List ? data.map((e) => Map<String, dynamic>.from(e)).toList() : []; _searching = false; });
    } catch (_) { setState(() => _searching = false); }
  }

  void _selectRecipe(Map<String, dynamic> recipe) {
    setState(() {
      _selectedRecipe = recipe;
      _searchResults = [];
    });
    _checkIngredients();
  }

  Future<void> _checkIngredients() async {
    if (_selectedRecipe == null) return;
    setState(() => _checkingStock = true);
    try {
      final data = await ApiService.get('/api/manufacturing/recipes/${_selectedRecipe!['id']}/stock-check');
      setState(() { _ingredientCheck = data is List ? data.map((e) => Map<String, dynamic>.from(e)).toList() : []; _checkingStock = false; });
    } catch (_) { setState(() => _checkingStock = false); }
  }

  Future<void> _pickDate(bool isStart) async {
    final picked = await showDatePicker(
      context: context,
      initialDate: isStart ? _startDate : _endDate,
      firstDate: DateTime.now().subtract(const Duration(days: 30)),
      lastDate: DateTime.now().add(const Duration(days: 365)),
    );
    if (picked != null) setState(() { if (isStart) _startDate = picked; else _endDate = picked; });
  }

  Future<void> _save() async {
    if (!_form.currentState!.validate()) return;
    if (_selectedRecipe == null) { ToastHelper.show(context, message: 'Please select a recipe', error: true); return; }
    setState(() { _saving = true; _error = null; });
    try {
      await ApiService.post('/api/manufacturing/production', {
        'recipe_id': _selectedRecipe!['id'],
        'batch_number': _batchCtrl.text.trim(),
        'quantity_planned': int.tryParse(_qtyCtrl.text) ?? 0,
        'start_date': _startDate.toIso8601String().split('T')[0],
        'expected_end_date': _endDate.toIso8601String().split('T')[0],
        'notes': _notesCtrl.text,
      });
      if (mounted) { ToastHelper.show(context, message: 'Production started'); Navigator.pop(context); }
    } on ApiException catch (e) { setState(() { _error = e.message; _saving = false; }); }
    catch (_) { setState(() { _error = 'Failed to start production'; _saving = false; }); }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(title: const Text('Start Production')),
      body: SingleChildScrollView(
        padding: const EdgeInsets.fromLTRB(16, 8, 16, 100),
        child: Form(
          key: _form,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              GlassCard(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text('Recipe', style: TextStyle(fontSize: 15, fontWeight: FontWeight.w700)),
                    const SizedBox(height: 8),
                    if (_loadingRecipe)
                      const LinearProgressIndicator()
                    else if (_selectedRecipe != null) ...[
                      Container(
                        padding: const EdgeInsets.all(12),
                        decoration: BoxDecoration(color: AppColors.primaryLt, borderRadius: BorderRadius.circular(10)),
                        child: Row(children: [
                          const Icon(Icons.check_circle, color: AppColors.primary, size: 20),
                          const SizedBox(width: 8),
                          Expanded(child: Text(_selectedRecipe!['name'] ?? '', style: const TextStyle(fontWeight: FontWeight.w600))),
                          IconButton(icon: const Icon(Icons.close, size: 18), onPressed: () => setState(() => _selectedRecipe = null)),
                        ]),
                      ),
                    ] else ...[
                      AppSearchBar(hint: 'Search recipes...', onChanged: _searchRecipes),
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
                              final r = _searchResults[i];
                              return ListTile(
                                dense: true,
                                leading: Container(
                                  width: 32, height: 32,
                                  decoration: BoxDecoration(color: AppColors.primaryLt, borderRadius: BorderRadius.circular(8)),
                                  child: const Icon(Icons.menu_book_rounded, size: 16, color: AppColors.primary),
                                ),
                                title: Text(r['name'] ?? '', style: const TextStyle(fontSize: 13)),
                                subtitle: Text('Yield: ${r['expected_yield'] ?? 0}', style: const TextStyle(fontSize: 11)),
                                onTap: () => _selectRecipe(r),
                              );
                            },
                          ),
                        ),
                      ],
                    ],
                  ],
                ),
              ),
              const SizedBox(height: 12),
              GlassCard(
                child: Column(
                  children: [
                    TextFormField(controller: _batchCtrl, decoration: const InputDecoration(labelText: 'Batch / Lot Number *'), validator: (v) => v!.trim().isEmpty ? 'Required' : null),
                    const SizedBox(height: 12),
                    TextFormField(controller: _qtyCtrl, keyboardType: TextInputType.number, decoration: const InputDecoration(labelText: 'Quantity to Produce *'), validator: (v) => v!.isEmpty ? 'Required' : null),
                    const SizedBox(height: 12),
                    GestureDetector(
                      onTap: () => _pickDate(true),
                      child: AbsorbPointer(
                        child: TextFormField(
                          decoration: const InputDecoration(labelText: 'Start Date'),
                          controller: TextEditingController(text: _dateFmt.format(_startDate)),
                        ),
                      ),
                    ),
                    const SizedBox(height: 12),
                    GestureDetector(
                      onTap: () => _pickDate(false),
                      child: AbsorbPointer(
                        child: TextFormField(
                          decoration: const InputDecoration(labelText: 'Expected Completion Date'),
                          controller: TextEditingController(text: _dateFmt.format(_endDate)),
                        ),
                      ),
                    ),
                    const SizedBox(height: 12),
                    TextFormField(controller: _notesCtrl, decoration: const InputDecoration(labelText: 'Notes'), maxLines: 2),
                  ],
                ),
              ),
              if (_selectedRecipe != null) ...[
                const SizedBox(height: 12),
                _buildStockCheck(),
              ],
              if (_error != null) ...[
                const SizedBox(height: 12),
                Container(padding: const EdgeInsets.all(12), decoration: BoxDecoration(color: AppColors.dangerLt, borderRadius: BorderRadius.circular(10)),
                  child: Text(_error!, style: const TextStyle(color: AppColors.danger))),
              ],
              const SizedBox(height: 20),
              SizedBox(
                width: double.infinity,
                height: 52,
                child: ElevatedButton.icon(
                  onPressed: _saving ? null : _save,
                  icon: _saving ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5)) : const Icon(Icons.play_arrow_rounded),
                  label: Text(_saving ? 'Starting...' : 'Start Production', style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w700)),
                  style: ElevatedButton.styleFrom(backgroundColor: AppColors.success, foregroundColor: Colors.white),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildStockCheck() {
    return GlassCard(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(children: [
            const Text('Ingredient Stock Check', style: TextStyle(fontSize: 15, fontWeight: FontWeight.w700)),
            const Spacer(),
            if (_checkingStock) const SizedBox(width: 16, height: 16, child: CircularProgressIndicator(strokeWidth: 2)),
          ]),
          const SizedBox(height: 12),
          if (_ingredientCheck.isEmpty && !_checkingStock)
            const Text('No ingredients to check', style: TextStyle(color: AppColors.textSec))
          else if (!_checkingStock)
            ...List.generate(_ingredientCheck.length, (i) {
              final item = _ingredientCheck[i];
              final sufficient = item['sufficient'] == true;
              return Padding(
                padding: EdgeInsets.only(bottom: i < _ingredientCheck.length - 1 ? 8 : 0),
                child: Row(
                  children: [
                    Icon(sufficient ? Icons.check_circle : Icons.warning_amber_rounded, size: 18, color: sufficient ? AppColors.success : AppColors.danger),
                    const SizedBox(width: 8),
                    Expanded(child: Text(item['product_name'] ?? '', style: const TextStyle(fontSize: 13))),
                    Text('${item['available'] ?? 0} / ${item['required'] ?? 0}', style: TextStyle(fontSize: 12, color: sufficient ? AppColors.textSec : AppColors.danger)),
                  ],
                ),
              );
            }),
        ],
      ),
    );
  }
}
