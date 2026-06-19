import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../core/api_service.dart';
import '../../shared/theme/app_theme.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/toast_helper.dart';
import '../../widgets/search_bar_widget.dart';

class LoanFormScreen extends StatefulWidget {
  const LoanFormScreen({super.key});
  @override State<LoanFormScreen> createState() => _LoanFormScreenState();
}

class _LoanFormScreenState extends State<LoanFormScreen> {
  final _form = GlobalKey<FormState>();
  final _amountCtrl = TextEditingController();
  final _interestCtrl = TextEditingController();
  final _durationCtrl = TextEditingController();
  final _purposeCtrl = TextEditingController();
  final _clientSearchCtrl = TextEditingController();
  final _guarantorSearchCtrl = TextEditingController();

  String? _selectedClient;
  String? _selectedProduct;
  DateTime _disbursementDate = DateTime.now();
  List<dynamic> _clients = [];
  List<dynamic> _products = [];
  List<dynamic> _guarantors = [];
  List<Map<String, dynamic>> _selectedGuarantors = [];
  bool _saving = false;
  bool _loadingClients = true;
  bool _loadingProducts = true;
  String? _err;

  @override
  void initState() { super.initState(); _loadData(); }
  @override void dispose() {
    for (final c in [_amountCtrl, _interestCtrl, _durationCtrl, _purposeCtrl, _clientSearchCtrl, _guarantorSearchCtrl]) c.dispose();
    super.dispose();
  }

  Future<void> _loadData() async {
    try {
      final results = await Future.wait([
        ApiService.get('/microfinance/clients'),
        ApiService.get('/microfinance/loan-products'),
      ]);
      setState(() {
        _clients = results[0] is List ? results[0] : (results[0]['data'] ?? []);
        _products = results[1] is List ? results[1] : (results[1]['data'] ?? []);
        _loadingClients = false;
        _loadingProducts = false;
      });
    } catch (_) { setState(() { _loadingClients = false; _loadingProducts = false; }); }
  }

  Future<void> _save() async {
    if (!_form.currentState!.validate()) return;
    if (_selectedClient == null) { ToastHelper.error(context, 'Please select a client'); return; }
    if (_selectedProduct == null) { ToastHelper.error(context, 'Please select a loan product'); return; }
    setState(() { _saving = true; _err = null; });
    try {
      await ApiService.post('/microfinance/loans', {
        'client_id': _selectedClient,
        'loan_product_id': _selectedProduct,
        'amount': double.tryParse(_amountCtrl.text) ?? 0,
        'interest_rate': double.tryParse(_interestCtrl.text) ?? 0,
        'duration': int.tryParse(_durationCtrl.text) ?? 0,
        'purpose': _purposeCtrl.text.trim(),
        'disbursement_date': DateFormat('yyyy-MM-dd').format(_disbursementDate),
        'guarantors': _selectedGuarantors.map((g) => g['id']).toList(),
      });
      if (mounted) { ToastHelper.success(context, 'Loan application submitted'); Navigator.pop(context); }
    } on ApiException catch (e) { setState(() { _err = e.message; _saving = false; }); }
    catch (_) { setState(() { _err = 'Save failed'; _saving = false; }); }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        title: const Text('New Loan Application', style: TextStyle(fontWeight: FontWeight.w800, color: Color(0xFF0F172A), fontSize: 20)),
        backgroundColor: Colors.white, elevation: 0, centerTitle: true,
        leading: IconButton(icon: const Icon(Icons.close), onPressed: () => Navigator.pop(context)),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Form(key: _form, child: Column(crossAxisAlignment: CrossAxisAlignment.stretch, children: [
          if (_err != null) Container(padding: const EdgeInsets.all(12), margin: const EdgeInsets.only(bottom: 16),
            decoration: BoxDecoration(color: AppColors.dangerLt, borderRadius: BorderRadius.circular(10)),
            child: Text(_err!, style: const TextStyle(color: AppColors.danger))),
          GlassCard(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            const Text('Client', style: TextStyle(fontWeight: FontWeight.w600, fontSize: 13, color: AppColors.textPri, margin: EdgeInsets.only(bottom: 8))),
            TextFormField(controller: _clientSearchCtrl, decoration: const InputDecoration(hintText: 'Search clients...', prefixIcon: Icon(Icons.search, size: 20)),
              onChanged: (_) => setState(() {})),
            const SizedBox(height: 8),
            if (_loadingClients)
              const Center(child: Padding(padding: EdgeInsets.all(8), child: CircularProgressIndicator(strokeWidth: 2)))
            else
              SizedBox(
                height: 120,
                child: ListView(
                  children: _clients.where((c) => _clientSearchCtrl.text.isEmpty ||
                      (c['name']?.toString().toLowerCase() ?? '').contains(_clientSearchCtrl.text.toLowerCase())).map((c) {
                    final selected = _selectedClient == c['id'].toString();
                    return ListTile(
                      dense: true,
                      leading: CircleAvatar(radius: 16, backgroundColor: selected ? AppColors.primary : AppColors.primaryLt,
                        child: Text((c['name']?.toString() ?? '?')[0], style: TextStyle(color: selected ? Colors.white : AppColors.primary, fontWeight: FontWeight.w700, fontSize: 13))),
                      title: Text(c['name']?.toString() ?? '', style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w600)),
                      subtitle: Text(c['phone']?.toString() ?? '', style: const TextStyle(fontSize: 11)),
                      trailing: selected ? const Icon(Icons.check_circle, color: AppColors.primary, size: 20) : null,
                      onTap: () => setState(() => _selectedClient = c['id'].toString()),
                    );
                  }).toList(),
                ),
              ),
          ])),
          const SizedBox(height: 16),
          GlassCard(child: Column(children: [
            DropdownButtonFormField<String>(value: _selectedProduct, decoration: const InputDecoration(labelText: 'Loan Product *'),
              items: _products.map((p) => DropdownMenuItem(value: p['id'].toString(), child: Text(p['name']?.toString() ?? ''))).toList(),
              onChanged: (v) {
                setState(() {
                  _selectedProduct = v;
                  final product = _products.firstWhere((p) => p['id'].toString() == v, orElse: () => {});
                  if (product.isNotEmpty) {
                    _interestCtrl.text = '${product['interest_rate'] ?? ''}';
                    _durationCtrl.text = '${product['duration'] ?? ''}';
                  }
                });
              }),
            const SizedBox(height: 12),
            Row(children: [
              Expanded(child: TextFormField(controller: _amountCtrl, keyboardType: TextInputType.number,
                decoration: const InputDecoration(labelText: 'Amount *'), validator: (v) => (v != null && double.tryParse(v) != null) ? null : 'Required')),
              const SizedBox(width: 12),
              Expanded(child: TextFormField(controller: _interestCtrl, keyboardType: TextInputType.number,
                decoration: const InputDecoration(labelText: 'Interest Rate (%)', suffixText: '%'))),
            ]),
            const SizedBox(height: 12),
            TextFormField(controller: _durationCtrl, keyboardType: TextInputType.number,
              decoration: const InputDecoration(labelText: 'Duration (months)'), validator: (v) => (v != null && int.tryParse(v) != null) ? null : 'Required'),
            const SizedBox(height: 12),
            TextFormField(controller: _purposeCtrl, maxLines: 2, decoration: const InputDecoration(labelText: 'Purpose / Description')),
            const SizedBox(height: 12),
            GestureDetector(
              onTap: () async {
                final d = await showDatePicker(context: context, initialDate: _disbursementDate, firstDate: DateTime(2020), lastDate: DateTime(2030));
                if (d != null) setState(() => _disbursementDate = d);
              },
              child: AbsorbPointer(
                child: TextFormField(decoration: InputDecoration(labelText: 'Disbursement Date',
                  hintText: DateFormat('dd MMM yyyy').format(_disbursementDate), suffixIcon: const Icon(Icons.calendar_today, size: 18))),
              ),
            ),
          ])),
          const SizedBox(height: 16),
          GlassCard(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            const Text('Guarantors', style: TextStyle(fontWeight: FontWeight.w600, fontSize: 13, color: AppColors.textPri)),
            const SizedBox(height: 8),
            TextFormField(controller: _guarantorSearchCtrl, decoration: const InputDecoration(hintText: 'Search guarantors...', prefixIcon: Icon(Icons.search, size: 20)),
              onChanged: (_) => setState(() {})),
            const SizedBox(height: 8),
            if (_selectedGuarantors.isNotEmpty)
              Wrap(spacing: 8, runSpacing: 4, children: _selectedGuarantors.map((g) => Chip(
                label: Text(g['name']?.toString() ?? '', style: const TextStyle(fontSize: 12)),
                deleteIcon: const Icon(Icons.close, size: 16),
                onDeleted: () => setState(() => _selectedGuarantors.remove(g)),
              )).toList()),
            const SizedBox(height: 8),
            SizedBox(
              height: 100,
              child: ListView(
                children: _guarantors.where((g) => _guarantorSearchCtrl.text.isEmpty ||
                    (g['name']?.toString().toLowerCase() ?? '').contains(_guarantorSearchCtrl.text.toLowerCase())).map((g) {
                  final selected = _selectedGuarantors.any((sg) => sg['id'].toString() == g['id'].toString());
                  if (selected) return const SizedBox.shrink();
                  return ListTile(
                    dense: true,
                    leading: CircleAvatar(radius: 16, child: Text((g['name']?.toString() ?? '?')[0])),
                    title: Text(g['name']?.toString() ?? '', style: const TextStyle(fontSize: 13)),
                    subtitle: Text(g['phone']?.toString() ?? '', style: const TextStyle(fontSize: 11)),
                    trailing: const Icon(Icons.add_circle_outline, color: AppColors.primary, size: 20),
                    onTap: () => setState(() => _selectedGuarantors.add({'id': g['id'], 'name': g['name']})),
                  );
                }).toList(),
              ),
            ),
          ])),
          const SizedBox(height: 24),
          SizedBox(height: 54, child: ElevatedButton(
            style: ElevatedButton.styleFrom(backgroundColor: AppColors.primary, foregroundColor: Colors.white,
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14))),
            onPressed: _saving ? null : _save,
            child: _saving
                ? const SizedBox(width: 22, height: 22, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5))
                : const Text('Submit Application', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700)),
          )),
          const SizedBox(height: 40),
        ])),
      ),
    );
  }
}
