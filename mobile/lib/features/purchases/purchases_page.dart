import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../core/api_service.dart';
import '../../shared/theme/app_colors.dart';
import '../../shared/widgets/app_card.dart';
import '../../shared/widgets/empty_state.dart';
import '../../shared/widgets/search_bar_widget.dart';
import '../../shared/widgets/status_badge.dart';
import '../../shared/constants/app_constants.dart';

class PurchasesPage extends StatefulWidget {
  const PurchasesPage({super.key});
  @override State<PurchasesPage> createState() => _PurchasesPageState();
}

class _PurchasesPageState extends State<PurchasesPage> with SingleTickerProviderStateMixin {
  List<dynamic> _purchases = [];
  bool _loading = true;
  String? _error;
  String _search = '';
  String _status = '';
  final fmt = NumberFormat('#,##0.00');
  late TabController _tabs;

  @override
  void initState() { super.initState(); _tabs = TabController(length: 4, vsync: this); _tabs.addListener(() { if (!_tabs.indexIsChanging) { _status = ['', 'received', 'pending', 'cancelled'][_tabs.index]; _load(); } }); _load(); }
  @override void dispose() { _tabs.dispose(); super.dispose(); }

  Future<void> _load() async {
    setState(() { _loading = true; _error = null; });
    try {
      final data = await ApiService.get('/purchases?search=${Uri.encodeComponent(_search)}&status=$_status');
      setState(() { _purchases = (data as List); _loading = false; });
    } on ApiException catch (e) { setState(() { _error = e.message; _loading = false; }); }
    catch (_) { setState(() { _error = 'Connection error'; _loading = false; }); }
  }

  void _showForm() => showModalBottomSheet(context: context, isScrollControlled: true, backgroundColor: Colors.transparent, builder: (_) => _PurchaseForm(onSaved: _load));

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(
        title: const Text('Purchases'),
        actions: [IconButton(icon: const Icon(Icons.refresh, color: Colors.white), onPressed: _load)],
        bottom: TabBar(controller: _tabs, labelColor: Colors.white, unselectedLabelColor: Colors.white70, indicatorColor: Colors.white, indicatorWeight: 3,
          tabs: const [Tab(text: 'All'), Tab(text: 'Received'), Tab(text: 'Pending'), Tab(text: 'Cancelled')]),
      ),
      body: Column(children: [
        Padding(padding: const EdgeInsets.all(16), child: SearchBarWidget(hint: 'Search by reference...', onChanged: (v) { _search = v; _load(); })),
        Expanded(child: _loading ? const LoadingWidget(message: 'Loading purchases...')
          : _error != null ? ErrorWidget2(message: _error!, onRetry: _load)
          : _purchases.isEmpty ? const EmptyState(icon: Icons.shopping_cart_outlined, title: 'No Purchases', subtitle: 'Purchase orders will appear here')
          : RefreshIndicator(color: AppColors.primary, onRefresh: _load,
              child: ListView.separated(padding: const EdgeInsets.fromLTRB(16, 0, 16, 100), itemCount: _purchases.length, separatorBuilder: (_, __) => const SizedBox(height: 10), itemBuilder: (_, i) => _tile(_purchases[i])))),
      ]),
      floatingActionButton: FloatingActionButton.extended(onPressed: _showForm, icon: const Icon(Icons.add), label: const Text('New Purchase')),
    );
  }

  Widget _tile(dynamic p) => AppCard(onTap: () => _showDetail(p), child: Padding(padding: const EdgeInsets.all(16), child: Column(children: [
    Row(children: [
      Container(width: 46, height: 46, decoration: BoxDecoration(color: AppColors.primaryLt, borderRadius: BorderRadius.circular(12)),
        child: const Icon(Icons.shopping_cart_outlined, color: AppColors.primary, size: 22)),
      const SizedBox(width: 14),
      Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
        Text(p['reference'] ?? '', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14)),
        const SizedBox(height: 3),
        Text(p['supplier'] != null ? p['supplier']['name'] ?? '' : 'No Supplier', style: const TextStyle(color: AppColors.textSec, fontSize: 13)),
      ])),
      Column(crossAxisAlignment: CrossAxisAlignment.end, children: [
        Text('${AppConstants.currency} ${fmt.format((p['total'] ?? 0).toDouble())}', style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 15, color: AppColors.textPri)),
        const SizedBox(height: 4),
        StatusBadge.fromStatus(p['status'] ?? ''),
      ]),
    ]),
    const SizedBox(height: 10), const Divider(height: 1), const SizedBox(height: 10),
    Row(children: [
      _chip(Icons.calendar_today_outlined, fmtDate(p['purchase_date']), AppColors.textSec),
      const Spacer(),
      StatusBadge.fromStatus(p['payment_status'] ?? ''),
    ]),
  ])));

  Widget _chip(IconData icon, String label, Color color) => Row(children: [Icon(icon, size: 13, color: color), const SizedBox(width: 4), Text(label, style: TextStyle(color: color, fontSize: 12))]);

  String fmtDate(String? d) {
    if (d == null) return '';
    try { return DateFormat('dd MMM yyyy').format(DateTime.parse(d)); } catch (_) { return d; }
  }

  void _showDetail(dynamic p) {
    showModalBottomSheet(context: context, isScrollControlled: true, backgroundColor: Colors.transparent, builder: (_) => _PurchaseDetail(purchase: p));
  }
}

class _PurchaseDetail extends StatefulWidget {
  final dynamic purchase;
  const _PurchaseDetail({required this.purchase});
  @override State<_PurchaseDetail> createState() => _PurchaseDetailState();
}

class _PurchaseDetailState extends State<_PurchaseDetail> {
  dynamic _detail;
  bool _loading = true;
  final fmt = NumberFormat('#,##0.00');

  @override void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    try {
      final data = await ApiService.get('/purchases/${widget.purchase['id']}');
      setState(() { _detail = data; _loading = false; });
    } catch (_) { setState(() { _detail = widget.purchase; _loading = false; }); }
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: const BoxDecoration(color: AppColors.surface, borderRadius: BorderRadius.vertical(top: Radius.circular(24))),
      child: _loading ? const Padding(padding: EdgeInsets.all(60), child: LoadingWidget())
        : SingleChildScrollView(padding: const EdgeInsets.fromLTRB(24, 16, 24, 32), child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            Center(child: Container(width: 40, height: 4, decoration: BoxDecoration(color: AppColors.border, borderRadius: BorderRadius.circular(4)))),
            const SizedBox(height: 20),
            Row(children: [
              const Icon(Icons.shopping_cart_outlined, color: AppColors.primary, size: 28),
              const SizedBox(width: 12),
              Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                Text(_detail!['reference'] ?? '', style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w700)),
                Text(_detail!['supplier'] != null ? _detail!['supplier']['name'] ?? '' : 'No Supplier', style: const TextStyle(color: AppColors.textSec, fontSize: 13)),
              ])),
              StatusBadge.fromStatus(_detail!['status'] ?? ''),
            ]),
            const SizedBox(height: 20), const Divider(),
            const SizedBox(height: 16), const Text('Items', style: TextStyle(fontSize: 15, fontWeight: FontWeight.w700)), const SizedBox(height: 10),
            ...(_detail!['items'] ?? []).map<Widget>((item) => Padding(padding: const EdgeInsets.only(bottom: 8), child: Row(children: [
              Expanded(child: Text(item['product_name'] ?? '', style: const TextStyle(fontSize: 14))),
              Text('${item['quantity']} x ${fmt.format((item['unit_cost'] ?? 0).toDouble())}', style: const TextStyle(color: AppColors.textSec, fontSize: 13)),
              const SizedBox(width: 12),
              Text('${AppConstants.currency} ${fmt.format((item['total'] ?? 0).toDouble())}', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 13)),
            ]))),
            const Divider(), const SizedBox(height: 10),
            _row('Subtotal', fmt.format((_detail!['subtotal'] ?? 0).toDouble())),
            _row('Total', fmt.format((_detail!['total'] ?? 0).toDouble()), bold: true),
            const SizedBox(height: 6),
            _row('Payment Status', _detail!['payment_status'] ?? ''),
            _row('Date', fmtDate(_detail!['purchase_date'])),
            if (_detail!['notes'] != null) ...[const SizedBox(height: 6), _row('Notes', _detail!['notes'])],
          ])),
    );
  }

  Widget _row(String l, String v, {bool bold = false, Color? color}) => Padding(padding: const EdgeInsets.only(bottom: 6), child: Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [Text(l, style: const TextStyle(color: AppColors.textSec, fontSize: 14)), Text(v, style: TextStyle(fontWeight: bold ? FontWeight.w700 : FontWeight.w600, fontSize: 14, color: color ?? AppColors.textPri))]));

  String fmtDate(String? d) {
    if (d == null) return '';
    try { return DateFormat('dd MMM yyyy').format(DateTime.parse(d)); } catch (_) { return d; }
  }
}

class _PurchaseForm extends StatefulWidget {
  final VoidCallback onSaved;
  const _PurchaseForm({required this.onSaved});
  @override State<_PurchaseForm> createState() => _PurchaseFormState();
}

class _PurchaseFormState extends State<_PurchaseForm> {
  final _form = GlobalKey<FormState>();
  final _dateCtrl = TextEditingController(text: DateFormat('yyyy-MM-dd').format(DateTime.now()));
  final _notesCtrl = TextEditingController();
  bool _saving = false;
  String? _err;
  int? _supplierId;
  String _status = 'received';
  String _paymentStatus = 'pending';
  List<Map<String, dynamic>> _items = [];
  List<dynamic> _products = [];
  List<dynamic> _suppliers = [];
  final fmt = NumberFormat('#,##0.00');

  @override void initState() { super.initState(); _loadFormData(); }
  @override void dispose() { _dateCtrl.dispose(); _notesCtrl.dispose(); super.dispose(); }

  Future<void> _loadFormData() async {
    try {
      final products = await ApiService.get('/products');
      final suppliers = await ApiService.get('/suppliers');
      setState(() { _products = (products as List); _suppliers = (suppliers as List); });
    } catch (_) {}
  }

  void _addItem() {
    setState(() { _items.add({'product_id': 0, 'quantity': 1, 'unit_cost': 0.0}); });
  }

  Future<void> _save() async {
    if (!_form.currentState!.validate()) return;
    if (_items.isEmpty) { _snack('Add at least one item', error: true); return; }
    setState(() { _saving = true; _err = null; });
    try {
      await ApiService.post('/purchases', {
        'supplier_id': _supplierId,
        'purchase_date': _dateCtrl.text.trim(),
        'status': _status,
        'payment_status': _paymentStatus,
        'notes': _notesCtrl.text.trim(),
        'items': _items.map((i) => i).toList(),
      });
      widget.onSaved();
      if (mounted) Navigator.pop(context);
    } on ApiException catch (e) { setState(() { _err = e.message; _saving = false; }); }
    catch (_) { setState(() { _err = 'Save failed'; _saving = false; }); }
  }

  void _snack(String msg, {bool error = false}) => ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(msg), backgroundColor: error ? AppColors.danger : AppColors.success, behavior: SnackBarBehavior.floating, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10))));

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: const BoxDecoration(color: AppColors.surface, borderRadius: BorderRadius.vertical(top: Radius.circular(24))),
      padding: EdgeInsets.only(bottom: MediaQuery.of(context).viewInsets.bottom),
      child: SingleChildScrollView(padding: const EdgeInsets.fromLTRB(24, 16, 24, 32), child: Form(key: _form, child: Column(crossAxisAlignment: CrossAxisAlignment.stretch, children: [
        Center(child: Container(width: 40, height: 4, decoration: BoxDecoration(color: AppColors.border, borderRadius: BorderRadius.circular(4)))),
        const SizedBox(height: 20),
        Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [const Text('New Purchase', style: TextStyle(fontSize: 20, fontWeight: FontWeight.w700)), IconButton(icon: const Icon(Icons.close), onPressed: () => Navigator.pop(context))]),
        if (_err != null) ...[const SizedBox(height: 12), Container(padding: const EdgeInsets.all(12), decoration: BoxDecoration(color: AppColors.dangerLt, borderRadius: BorderRadius.circular(10)), child: Text(_err!, style: const TextStyle(color: AppColors.danger)))],
        const SizedBox(height: 16),
        DropdownButtonFormField<int>(decoration: const InputDecoration(labelText: 'Supplier'), items: _suppliers.map((s) => DropdownMenuItem(value: s['id'], child: Text(s['name'] ?? ''))).toList(), onChanged: (v) => _supplierId = v),
        const SizedBox(height: 12),
        TextFormField(controller: _dateCtrl, decoration: const InputDecoration(labelText: 'Date *', prefixIcon: Icon(Icons.calendar_today_outlined, size: 18)), validator: (v) => v!.isNotEmpty ? null : 'Required'),
        const SizedBox(height: 12),
        DropdownButtonFormField<String>(decoration: const InputDecoration(labelText: 'Status'), value: _status, items: ['received','pending','draft','cancelled'].map((s) => DropdownMenuItem(value: s, child: Text(s))).toList(), onChanged: (v) => _status = v!),
        const SizedBox(height: 12),
        DropdownButtonFormField<String>(decoration: const InputDecoration(labelText: 'Payment'), value: _paymentStatus, items: ['pending','partial','paid'].map((s) => DropdownMenuItem(value: s, child: Text(s))).toList(), onChanged: (v) => _paymentStatus = v!),
        const SizedBox(height: 12),
        TextFormField(controller: _notesCtrl, maxLines: 2, decoration: const InputDecoration(labelText: 'Notes')),
        const SizedBox(height: 16),
        Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [const Text('Items', style: TextStyle(fontSize: 15, fontWeight: FontWeight.w700)), TextButton.icon(icon: const Icon(Icons.add, size: 18), onPressed: _addItem, label: const Text('Add Item'))]),
        ..._items.asMap().entries.map((e) => _itemRow(e.key)),
        const SizedBox(height: 24),
        SizedBox(height: 52, child: ElevatedButton(onPressed: _saving ? null : _save, child: _saving ? const SizedBox(width: 22, height: 22, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5)) : const Text('Create Purchase', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700)))),
      ]))),
    );
  }

  Widget _itemRow(int idx) {
    final item = _items[idx];
    return Container(margin: const EdgeInsets.only(bottom: 10), padding: const EdgeInsets.all(12), decoration: BoxDecoration(color: AppColors.surfaceVariant, borderRadius: BorderRadius.circular(12)), child: Column(children: [
      Row(children: [
        Expanded(child: DropdownButtonFormField<int>(decoration: const InputDecoration(labelText: 'Product', isDense: true), items: _products.map((p) => DropdownMenuItem(value: p['id'], child: Text(p['name'] ?? ''))).toList(), onChanged: (v) => setState(() { _items[idx]['product_id'] = v; }))),
        const SizedBox(width: 8),
        IconButton(icon: const Icon(Icons.close, size: 18, color: AppColors.danger), onPressed: () => setState(() => _items.removeAt(idx))),
      ]),
      const SizedBox(height: 8),
      Row(children: [
        Expanded(child: TextFormField(decoration: const InputDecoration(labelText: 'Qty', isDense: true), keyboardType: TextInputType.number, initialValue: '1', onChanged: (v) => _items[idx]['quantity'] = double.tryParse(v) ?? 0)),
        const SizedBox(width: 8),
        Expanded(child: TextFormField(decoration: const InputDecoration(labelText: 'Unit Cost', isDense: true), keyboardType: TextInputType.number, initialValue: '0', onChanged: (v) => _items[idx]['unit_cost'] = double.tryParse(v) ?? 0)),
      ]),
    ]));
  }
}
