import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../core/api_service.dart';
import '../../core/auth_provider.dart';
import '../../shared/theme/app_theme.dart';
import '../../shared/widgets/app_card.dart';
import '../customers/customers_page.dart';
import '../expenses/expenses_page.dart';
import '../reports/reports_page.dart';
import '../settings/settings_page.dart';
import 'staff_management_page.dart';

class MorePage extends StatelessWidget {
  const MorePage({super.key});

  @override
  Widget build(BuildContext context) {
    final user = context.watch<AuthProvider>().user;
    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(title: const Text('More')),
      body: SingleChildScrollView(padding: const EdgeInsets.all(16), child: Column(children: [
        // Mini Profile
        AppCard(child: Padding(padding: const EdgeInsets.all(16), child: Row(children: [
          Container(width: 54, height: 54, decoration: const BoxDecoration(color: AppColors.primaryLt, shape: BoxShape.circle),
            child: Center(child: Text(user?.initials ?? '?', style: const TextStyle(color: AppColors.primary, fontWeight: FontWeight.w800, fontSize: 20)))),
          const SizedBox(width: 14),
          Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            Text(user?.name ?? 'User', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 16)),
            Text(user?.displayBusiness ?? 'My Business', style: const TextStyle(color: AppColors.primary, fontSize: 12, fontWeight: FontWeight.w600)),
            Text(user?.email ?? '', style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
          ])),
          Container(padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4), decoration: BoxDecoration(color: AppColors.successLt, borderRadius: BorderRadius.circular(20)),
            child: Text(user?.currencySymbol ?? 'TSh', style: const TextStyle(color: AppColors.success, fontWeight: FontWeight.w700, fontSize: 11))),
        ]))),

        const SizedBox(height: 20),
        _sectionLabel('Management'),
        const SizedBox(height: 8),

        _grid(context, [
          _NavItem(icon: Icons.people_outlined, label: 'Customers', color: AppColors.primary, bg: AppColors.primaryLt, page: const CustomersPage()),
          _NavItem(icon: Icons.receipt_outlined, label: 'Expenses', color: AppColors.danger, bg: AppColors.dangerLt, page: const ExpensesPage()),
          _NavItem(icon: Icons.bar_chart_outlined, label: 'Reports', color: AppColors.success, bg: AppColors.successLt, page: const ReportsPage()),
          _NavItem(icon: Icons.settings_outlined, label: 'Settings', color: AppColors.secondary, bg: const Color(0xFFF5F3FF), page: const SettingsPage()),
        ]),

        const SizedBox(height: 20),
        _sectionLabel('Quick Links'),
        const SizedBox(height: 8),

        AppCard(child: Column(children: [
          _navTile(context, Icons.inventory_2_outlined, 'Stock Adjustment', 'Adjust inventory levels', AppColors.warning, AppColors.warningLt, const _StockAdjPage()),
          const Divider(height: 1),
          _navTile(context, Icons.local_offer_outlined, 'Discounts', 'Manage promotions', AppColors.secondary, const Color(0xFFF5F3FF), const _DiscountsCrudPage()),
          const Divider(height: 1),
          _navTile(context, Icons.category_outlined, 'Categories', 'Product categories', AppColors.success, AppColors.successLt, const _CategoriesPage()),
          const Divider(height: 1),
          _navTile(context, Icons.branding_watermark_outlined, 'Brands', 'Product brands', AppColors.primary, AppColors.primaryLt, const _BrandsPage()),
        ])),

        const SizedBox(height: 20),
        _sectionLabel('Tools'),
        const SizedBox(height: 8),

        _grid(context, [
          _NavItem(icon: Icons.calculate_outlined, label: 'Calculator', color: const Color(0xFF0891B2), bg: const Color(0xFFE0F2FE), page: const _CalculatorPage()),
          _NavItem(icon: Icons.receipt_long_outlined, label: 'All Reports', color: AppColors.success, bg: AppColors.successLt, page: const ReportsPage()),
        ]),

        const SizedBox(height: 20),
        _sectionLabel('System'),
        const SizedBox(height: 8),

        AppCard(child: Column(children: [
          _navTile(context, Icons.group_outlined, 'Staff Management', 'Add & manage your staff', AppColors.primary, AppColors.primaryLt, const StaffManagementPage()),
          const Divider(height: 1),
          _navTile(context, Icons.business_outlined, 'Suppliers', 'Manage suppliers', AppColors.warning, AppColors.warningLt, const _SuppliersPage()),
          const Divider(height: 1),
          _navTile(context, Icons.help_outline, 'Help & Support', 'Get help using MannaPOS', AppColors.textSec, AppColors.border, const _HelpSupportPage()),
        ])),

        const SizedBox(height: 32),
      ])),
    );
  }

  Widget _sectionLabel(String t) => Align(alignment: Alignment.centerLeft, child: Text(t, style: const TextStyle(color: AppColors.textSec, fontSize: 12, fontWeight: FontWeight.w700, letterSpacing: 0.8)));

  Widget _grid(BuildContext context, List<_NavItem> items) => GridView.count(
    crossAxisCount: 2, crossAxisSpacing: 12, mainAxisSpacing: 12,
    shrinkWrap: true, physics: const NeverScrollableScrollPhysics(), childAspectRatio: 1.2,
    children: items.map((item) => AppCard(onTap: () => item.page != null ? Navigator.push(context, MaterialPageRoute(builder: (_) => item.page!)) : null,
      child: Padding(padding: const EdgeInsets.all(16), child: Column(mainAxisAlignment: MainAxisAlignment.center, crossAxisAlignment: CrossAxisAlignment.start, children: [
        Container(width: 44, height: 44, decoration: BoxDecoration(color: item.bg, borderRadius: BorderRadius.circular(12)), child: Icon(item.icon, color: item.color, size: 22)),
        const SizedBox(height: 12),
        Text(item.label, style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 15)),
      ])))).toList());

  Widget _navTile(BuildContext context, IconData icon, String title, String sub, Color color, Color bg, Widget? page) => ListTile(
    leading: Container(width: 36, height: 36, decoration: BoxDecoration(color: bg, borderRadius: BorderRadius.circular(9)), child: Icon(icon, color: color, size: 20)),
    title: Text(title, style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 14)),
    subtitle: Text(sub, style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
    trailing: const Icon(Icons.arrow_forward_ios, size: 14, color: AppColors.textSec),
    onTap: page != null ? () => Navigator.push(context, MaterialPageRoute(builder: (_) => page)) : null,
  );
}

class _NavItem {
  final IconData icon;
  final String label;
  final Color color, bg;
  final Widget? page;
  const _NavItem({required this.icon, required this.label, required this.color, required this.bg, this.page});
}

// ── Categories Page ───────────────────────────────────────
class _CategoriesPage extends StatefulWidget {
  const _CategoriesPage();
  @override State<_CategoriesPage> createState() => _CategoriesPageState();
}
class _CategoriesPageState extends State<_CategoriesPage> {
  List<dynamic> _items = [];
  bool _loading = true;
  String? _error;

  @override
  void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    setState(() { _loading = true; _error = null; });
    try {
      final data = await ApiService.get('/categories');
      setState(() { _items = data as List; _loading = false; });
    } catch (e) { setState(() { _error = e.toString(); _loading = false; }); }
  }

  @override
  Widget build(BuildContext context) => Scaffold(
    appBar: AppBar(title: const Text('Categories'), actions: [IconButton(icon: const Icon(Icons.refresh, color: Colors.white), onPressed: _load)]),
    body: _loading ? const Center(child: CircularProgressIndicator(color: AppColors.primary))
      : _error != null ? Center(child: Text(_error!, style: const TextStyle(color: AppColors.danger)))
      : _items.isEmpty ? const Center(child: Text('No categories found', style: TextStyle(color: AppColors.textSec)))
      : ListView.separated(
          padding: const EdgeInsets.all(16),
          itemCount: _items.length,
          separatorBuilder: (_, __) => const Divider(height: 1),
          itemBuilder: (_, i) {
            final item = _items[i];
            return ListTile(
              leading: Container(width: 36, height: 36, decoration: BoxDecoration(color: AppColors.successLt, borderRadius: BorderRadius.circular(9)), child: const Icon(Icons.category_outlined, color: AppColors.success, size: 20)),
              title: Text(item['name'] ?? '', style: const TextStyle(fontWeight: FontWeight.w600)),
              subtitle: item['description'] != null ? Text(item['description'], style: const TextStyle(color: AppColors.textSec, fontSize: 12)) : null,
            );
          }),
  );
}

// ── Brands Page ───────────────────────────────────────────
class _BrandsPage extends StatefulWidget {
  const _BrandsPage();
  @override State<_BrandsPage> createState() => _BrandsPageState();
}
class _BrandsPageState extends State<_BrandsPage> {
  List<dynamic> _items = [];
  bool _loading = true;
  String? _error;

  @override
  void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    setState(() { _loading = true; _error = null; });
    try {
      final data = await ApiService.get('/brands');
      setState(() { _items = data as List; _loading = false; });
    } catch (e) { setState(() { _error = e.toString(); _loading = false; }); }
  }

  @override
  Widget build(BuildContext context) => Scaffold(
    appBar: AppBar(title: const Text('Brands'), actions: [IconButton(icon: const Icon(Icons.refresh, color: Colors.white), onPressed: _load)]),
    body: _loading ? const Center(child: CircularProgressIndicator(color: AppColors.primary))
      : _error != null ? Center(child: Text(_error!, style: const TextStyle(color: AppColors.danger)))
      : _items.isEmpty ? const Center(child: Text('No brands found', style: TextStyle(color: AppColors.textSec)))
      : ListView.separated(
          padding: const EdgeInsets.all(16),
          itemCount: _items.length,
          separatorBuilder: (_, __) => const Divider(height: 1),
          itemBuilder: (_, i) {
            final item = _items[i];
            return ListTile(
              leading: Container(width: 36, height: 36, decoration: BoxDecoration(color: AppColors.primaryLt, borderRadius: BorderRadius.circular(9)), child: const Icon(Icons.branding_watermark_outlined, color: AppColors.primary, size: 20)),
              title: Text(item['name'] ?? '', style: const TextStyle(fontWeight: FontWeight.w600)),
              subtitle: item['description'] != null ? Text(item['description'], style: const TextStyle(color: AppColors.textSec, fontSize: 12)) : null,
            );
          }),
  );
}

// ── Suppliers Page ────────────────────────────────────────
class _SuppliersPage extends StatefulWidget {
  const _SuppliersPage();
  @override State<_SuppliersPage> createState() => _SuppliersPageState();
}
class _SuppliersPageState extends State<_SuppliersPage> {
  List<dynamic> _items = [];
  bool _loading = true;
  String? _error;

  @override
  void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    setState(() { _loading = true; _error = null; });
    try {
      final data = await ApiService.get('/suppliers');
      setState(() { _items = data as List; _loading = false; });
    } catch (e) { setState(() { _error = e.toString(); _loading = false; }); }
  }

  @override
  Widget build(BuildContext context) => Scaffold(
    appBar: AppBar(title: const Text('Suppliers'), actions: [IconButton(icon: const Icon(Icons.refresh, color: Colors.white), onPressed: _load)]),
    body: _loading ? const Center(child: CircularProgressIndicator(color: AppColors.primary))
      : _error != null ? Center(child: Text(_error!, style: const TextStyle(color: AppColors.danger)))
      : _items.isEmpty ? const Center(child: Text('No suppliers found', style: TextStyle(color: AppColors.textSec)))
      : ListView.separated(
          padding: const EdgeInsets.all(16),
          itemCount: _items.length,
          separatorBuilder: (_, __) => const Divider(height: 1),
          itemBuilder: (_, i) {
            final item = _items[i];
            return ListTile(
              leading: Container(width: 36, height: 36, decoration: BoxDecoration(color: AppColors.warningLt, borderRadius: BorderRadius.circular(9)), child: const Icon(Icons.business_outlined, color: AppColors.warning, size: 20)),
              title: Text(item['name'] ?? '', style: const TextStyle(fontWeight: FontWeight.w600)),
              subtitle: Text(item['company'] ?? item['email'] ?? '', style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
            );
          }),
  );
}

// ── Stock Adjustment Page ─────────────────────────────────
class _StockAdjPage extends StatefulWidget {
  const _StockAdjPage();
  @override State<_StockAdjPage> createState() => _StockAdjPageState();
}
class _StockAdjPageState extends State<_StockAdjPage> {
  List<dynamic> _items = [];
  bool _loading = true;
  String? _error;

  @override
  void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    setState(() { _loading = true; _error = null; });
    try {
      final data = await ApiService.get('/stock-adjustments');
      setState(() { _items = data as List; _loading = false; });
    } catch (e) { setState(() { _error = e.toString(); _loading = false; }); }
  }

  @override
  Widget build(BuildContext context) => Scaffold(
    appBar: AppBar(title: const Text('Stock Adjustments'), actions: [IconButton(icon: const Icon(Icons.refresh, color: Colors.white), onPressed: _load)]),
    body: _loading ? const Center(child: CircularProgressIndicator(color: AppColors.primary))
      : _error != null ? Center(child: Text(_error!, style: const TextStyle(color: AppColors.danger)))
      : _items.isEmpty ? const Center(child: Text('No adjustments found', style: TextStyle(color: AppColors.textSec)))
      : ListView.separated(
          padding: const EdgeInsets.all(16),
          itemCount: _items.length,
          separatorBuilder: (_, __) => const Divider(height: 1),
          itemBuilder: (_, i) {
            final item = _items[i];
            return ListTile(
              leading: Container(width: 36, height: 36, decoration: BoxDecoration(color: AppColors.warningLt, borderRadius: BorderRadius.circular(9)), child: const Icon(Icons.inventory_2_outlined, color: AppColors.warning, size: 20)),
              title: Text(item['reference_no'] ?? item['id']?.toString() ?? '', style: const TextStyle(fontWeight: FontWeight.w600)),
              subtitle: Text(item['date'] ?? item['created_at'] ?? '', style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
            );
          }),
  );
}

// ── Discounts CRUD Page ───────────────────────────────────
class _DiscountsCrudPage extends StatefulWidget {
  const _DiscountsCrudPage();
  @override State<_DiscountsCrudPage> createState() => _DiscountsCrudPageState();
}
class _DiscountsCrudPageState extends State<_DiscountsCrudPage> {
  List<dynamic> _items = [];
  bool _loading = true;
  String? _error;

  @override void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    setState(() { _loading = true; _error = null; });
    try { final d = await ApiService.get('/discounts'); setState(() { _items = d as List; _loading = false; }); }
    catch (e) { setState(() { _error = e.toString(); _loading = false; }); }
  }

  Future<void> _delete(dynamic item) async {
    final ok = await showDialog<bool>(context: context, builder: (_) => AlertDialog(
      title: const Text('Delete Discount'), content: Text('Delete "${item['name']}"?'),
      actions: [TextButton(onPressed: () => Navigator.pop(context, false), child: const Text('Cancel')),
        ElevatedButton(style: ElevatedButton.styleFrom(backgroundColor: AppColors.danger), onPressed: () => Navigator.pop(context, true), child: const Text('Delete'))],
    ));
    if (ok != true) return;
    try { await ApiService.delete('/discounts/${item['id']}'); _load(); }
    catch (e) { _snack('Delete failed', error: true); }
  }

  void _showForm([Map<String, dynamic>? item]) {
    showModalBottomSheet(context: context, isScrollControlled: true, backgroundColor: Colors.transparent,
      builder: (_) => _DiscountForm(item: item, onSaved: _load));
  }

  void _snack(String msg, {bool error = false}) => ScaffoldMessenger.of(context).showSnackBar(SnackBar(
    content: Text(msg), backgroundColor: error ? AppColors.danger : AppColors.success,
    behavior: SnackBarBehavior.floating, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10))));

  @override
  Widget build(BuildContext context) => Scaffold(
    backgroundColor: AppColors.bg,
    appBar: AppBar(backgroundColor: AppColors.secondary, foregroundColor: Colors.white,
      title: const Text('Discounts', style: TextStyle(color: Colors.white)),
      actions: [IconButton(icon: const Icon(Icons.refresh, color: Colors.white), onPressed: _load)]),
    floatingActionButton: FloatingActionButton.extended(
      backgroundColor: AppColors.secondary,
      onPressed: () => _showForm(),
      icon: const Icon(Icons.add), label: const Text('Add Discount')),
    body: _loading ? const Center(child: CircularProgressIndicator(color: AppColors.secondary))
      : _error != null ? Center(child: Text(_error!, style: const TextStyle(color: AppColors.danger)))
      : _items.isEmpty ? Center(child: Column(mainAxisSize: MainAxisSize.min, children: [
          const Icon(Icons.local_offer_outlined, size: 48, color: AppColors.textSec),
          const SizedBox(height: 12),
          const Text('No discounts yet', style: TextStyle(color: AppColors.textSec, fontWeight: FontWeight.w600)),
          const SizedBox(height: 16),
          ElevatedButton(style: ElevatedButton.styleFrom(backgroundColor: AppColors.secondary), onPressed: () => _showForm(), child: const Text('Add Discount')),
        ]))
      : ListView.separated(padding: const EdgeInsets.all(16), itemCount: _items.length,
          separatorBuilder: (_, __) => const SizedBox(height: 10),
          itemBuilder: (_, i) {
            final item = _items[i];
            final isPerc = (item['discount_type'] ?? 'percentage') == 'percentage';
            return Container(
              decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(14), border: Border.all(color: AppColors.border), boxShadow: const [BoxShadow(color: Color(0x05000000), blurRadius: 8, offset: Offset(0, 2))]),
              child: ListTile(
                contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 6),
                leading: Container(width: 44, height: 44, decoration: BoxDecoration(color: const Color(0xFFF5F3FF), borderRadius: BorderRadius.circular(12)),
                  child: Center(child: Text(isPerc ? '%' : 'T', style: const TextStyle(color: AppColors.secondary, fontWeight: FontWeight.w900, fontSize: 16)))),
                title: Text(item['name'] ?? '', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 15)),
                subtitle: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                  const SizedBox(height: 2),
                  Text('${isPerc ? "${item['discount']}%" : "TSh ${item['discount']}"} off · ${item['discount_type']}', style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
                  if (item['starts_at'] != null) Text('${item['starts_at']} → ${item['ends_at'] ?? 'No expiry'}', style: const TextStyle(color: AppColors.textSec, fontSize: 11)),
                ]),
                trailing: Row(mainAxisSize: MainAxisSize.min, children: [
                  IconButton(icon: const Icon(Icons.edit_outlined, size: 18, color: AppColors.primary), onPressed: () => _showForm(Map<String, dynamic>.from(item))),
                  IconButton(icon: const Icon(Icons.delete_outline, size: 18, color: AppColors.danger), onPressed: () => _delete(item)),
                ]),
              ),
            );
          }),
  );
}

class _DiscountForm extends StatefulWidget {
  final Map<String, dynamic>? item;
  final VoidCallback onSaved;
  const _DiscountForm({this.item, required this.onSaved});
  @override State<_DiscountForm> createState() => _DiscountFormState();
}
class _DiscountFormState extends State<_DiscountForm> {
  final _form = GlobalKey<FormState>();
  late TextEditingController _name, _amount;
  String _type = 'percentage';
  bool _saving = false;
  String? _err;

  @override void initState() {
    super.initState();
    _name = TextEditingController(text: widget.item?['name']);
    _amount = TextEditingController(text: widget.item?['discount']?.toString());
    _type = widget.item?['discount_type'] ?? 'percentage';
  }

  @override void dispose() { _name.dispose(); _amount.dispose(); super.dispose(); }

  Future<void> _save() async {
    if (!_form.currentState!.validate()) return;
    setState(() { _saving = true; _err = null; });
    final body = {'name': _name.text.trim(), 'discount': _amount.text, 'discount_type': _type};
    try {
      if (widget.item != null) await ApiService.put('/discounts/${widget.item!['id']}', body);
      else await ApiService.post('/discounts', body);
      widget.onSaved();
      if (mounted) Navigator.pop(context);
    } on ApiException catch (e) { setState(() { _err = e.message; _saving = false; }); }
    catch (_) { setState(() { _err = 'Save failed'; _saving = false; }); }
  }

  @override
  Widget build(BuildContext context) => Container(
    decoration: const BoxDecoration(color: Colors.white, borderRadius: BorderRadius.vertical(top: Radius.circular(24))),
    padding: EdgeInsets.only(bottom: MediaQuery.of(context).viewInsets.bottom),
    child: SingleChildScrollView(padding: const EdgeInsets.fromLTRB(24, 16, 24, 32), child: Form(key: _form, child: Column(crossAxisAlignment: CrossAxisAlignment.stretch, children: [
      Center(child: Container(width: 40, height: 4, decoration: BoxDecoration(color: AppColors.border, borderRadius: BorderRadius.circular(4)))),
      const SizedBox(height: 20),
      Text(widget.item != null ? 'Edit Discount' : 'Add Discount', style: const TextStyle(fontSize: 20, fontWeight: FontWeight.w700)),
      if (_err != null) ...[const SizedBox(height: 12), Container(padding: const EdgeInsets.all(12), decoration: BoxDecoration(color: AppColors.dangerLt, borderRadius: BorderRadius.circular(10)), child: Text(_err!, style: const TextStyle(color: AppColors.danger)))],
      const SizedBox(height: 20),
      TextFormField(controller: _name, decoration: const InputDecoration(labelText: 'Discount Name *'), validator: (v) => v!.isNotEmpty ? null : 'Required'),
      const SizedBox(height: 12),
      Row(children: [
        Expanded(child: TextFormField(controller: _amount, keyboardType: TextInputType.number, decoration: const InputDecoration(labelText: 'Amount *'), validator: (v) => v!.isNotEmpty ? null : 'Required')),
        const SizedBox(width: 12),
        Expanded(child: DropdownButtonFormField<String>(value: _type,
          decoration: const InputDecoration(labelText: 'Type'),
          items: const [DropdownMenuItem(value: 'percentage', child: Text('Percentage (%)')), DropdownMenuItem(value: 'fixed', child: Text('Fixed (TSh)'))],
          onChanged: (v) => setState(() => _type = v!))),
      ]),
      const SizedBox(height: 24),
      SizedBox(height: 52, child: ElevatedButton(
        style: ElevatedButton.styleFrom(backgroundColor: AppColors.secondary),
        onPressed: _saving ? null : _save,
        child: _saving ? const SizedBox(width: 22, height: 22, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5)) : Text(widget.item != null ? 'Update Discount' : 'Add Discount', style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w700)))),
    ]))),
  );
}

// ── Calculator Page ───────────────────────────────────────
class _CalculatorPage extends StatefulWidget {
  const _CalculatorPage();
  @override State<_CalculatorPage> createState() => _CalculatorPageState();
}
class _CalculatorPageState extends State<_CalculatorPage> {
  String _display = '0';
  String _expression = '';
  String _prevVal = '';
  String _operator = '';
  bool _newNum = false;

  void _press(String key) {
    setState(() {
      if (key == 'AC') { _display = '0'; _expression = ''; _prevVal = ''; _operator = ''; _newNum = false; }
      else if (key == '⌫') { _display = _display.length > 1 ? _display.substring(0, _display.length - 1) : '0'; }
      else if (key == '±') { _display = _display.startsWith('-') ? _display.substring(1) : '-$_display'; }
      else if (key == '%') { _display = (double.tryParse(_display) ?? 0) / 100 == 0 ? '0' : ((double.tryParse(_display) ?? 0) / 100).toString(); }
      else if (['+', '-', '×', '÷'].contains(key)) {
        _prevVal = _display; _operator = key;
        _expression = '$_display $key'; _newNum = true;
      } else if (key == '=') {
        if (_operator.isEmpty) return;
        final a = double.tryParse(_prevVal) ?? 0;
        final b = double.tryParse(_display) ?? 0;
        double r = a;
        if (_operator == '+') r = a + b;
        else if (_operator == '-') r = a - b;
        else if (_operator == '×') r = a * b;
        else if (_operator == '÷') r = b != 0 ? a / b : 0;
        _expression = '$_prevVal $_operator $_display =';
        _display = r % 1 == 0 ? r.toInt().toString() : r.toStringAsFixed(8).replaceAll(RegExp(r'0+$'), '');
        _operator = ''; _prevVal = ''; _newNum = false;
      } else if (key == '.') {
        if (_newNum) { _display = '0.'; _newNum = false; }
        else if (!_display.contains('.')) { _display += '.'; }
      } else {
        if (_newNum || _display == '0') { _display = key; _newNum = false; }
        else { _display += key; }
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF1E293B),
      appBar: AppBar(
        backgroundColor: const Color(0xFF1E293B),
        foregroundColor: Colors.white,
        title: const Text('Calculator', style: TextStyle(color: Colors.white)),
        elevation: 0,
      ),
      body: Column(children: [
        // Display
        Expanded(child: Container(
          padding: const EdgeInsets.fromLTRB(24, 16, 24, 24),
          alignment: Alignment.bottomRight,
          child: Column(mainAxisAlignment: MainAxisAlignment.end, crossAxisAlignment: CrossAxisAlignment.end, children: [
            Text(_expression, style: const TextStyle(color: Color(0xFF94A3B8), fontSize: 16)),
            const SizedBox(height: 8),
            Text(_display, style: const TextStyle(color: Colors.white, fontSize: 52, fontWeight: FontWeight.w300, letterSpacing: -1), maxLines: 1, overflow: TextOverflow.ellipsis),
          ]),
        )),
        // Buttons
        Container(
          color: const Color(0xFF0F172A),
          padding: const EdgeInsets.fromLTRB(12, 12, 12, 32),
          child: Column(children: [
            _row(['AC', '±', '%', '÷']),
            _row(['7', '8', '9', '×']),
            _row(['4', '5', '6', '-']),
            _row(['1', '2', '3', '+']),
            _row(['⌫', '0', '.', '=']),
          ]),
        ),
      ]),
    );
  }

  Widget _row(List<String> keys) => Padding(
    padding: const EdgeInsets.only(bottom: 10),
    child: Row(children: keys.map((k) {
      Color bg = const Color(0xFF1E293B);
      Color fg = Colors.white;
      if (['+', '-', '×', '÷'].contains(k)) { bg = AppColors.primary; fg = Colors.white; }
      else if (['AC', '±', '%'].contains(k)) { bg = const Color(0xFF334155); fg = Colors.white; }
      else if (k == '=') { bg = AppColors.primary; fg = Colors.white; }
      else if (k == '⌫') { bg = const Color(0xFF334155); fg = AppColors.danger; }
      return Expanded(child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 4),
        child: GestureDetector(
          onTap: () => _press(k),
          child: Container(
            height: 64,
            decoration: BoxDecoration(color: bg, borderRadius: BorderRadius.circular(16)),
            alignment: Alignment.center,
            child: Text(k, style: TextStyle(color: fg, fontSize: 22, fontWeight: FontWeight.w500)),
          ),
        ),
      ));
    }).toList()),
  );
}

// ── User Management Page ──────────────────────────────────
class _UserManagementPage extends StatefulWidget {
  const _UserManagementPage();
  @override State<_UserManagementPage> createState() => _UserManagementPageState();
}
class _UserManagementPageState extends State<_UserManagementPage> {
  List<dynamic> _users = [];
  bool _loading = true;
  String? _error;

  @override void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    setState(() { _loading = true; _error = null; });
    try { final d = await ApiService.get('/users'); setState(() { _users = d as List; _loading = false; }); }
    catch (e) { setState(() { _error = e.toString(); _loading = false; }); }
  }

  Color _roleColor(String? role) => role == 'admin' ? AppColors.primary : role == 'manager' ? AppColors.secondary : AppColors.success;
  Color _roleBg(String? role) => role == 'admin' ? AppColors.primaryLt : role == 'manager' ? const Color(0xFFF5F3FF) : AppColors.successLt;

  @override
  Widget build(BuildContext context) => Scaffold(
    backgroundColor: AppColors.bg,
    appBar: AppBar(backgroundColor: AppColors.primary, foregroundColor: Colors.white,
      title: const Text('User Management', style: TextStyle(color: Colors.white)),
      actions: [IconButton(icon: const Icon(Icons.refresh, color: Colors.white), onPressed: _load)]),
    body: _loading ? const Center(child: CircularProgressIndicator(color: AppColors.primary))
      : _error != null ? Center(child: Column(mainAxisSize: MainAxisSize.min, children: [
          const Icon(Icons.wifi_off_rounded, size: 48, color: AppColors.textSec),
          const SizedBox(height: 12),
          const Text('Failed to load users', style: TextStyle(color: AppColors.textSec)),
          const SizedBox(height: 16),
          ElevatedButton(onPressed: _load, child: const Text('Retry')),
        ]))
      : _users.isEmpty ? const Center(child: Text('No users found', style: TextStyle(color: AppColors.textSec)))
      : ListView.separated(padding: const EdgeInsets.all(16), itemCount: _users.length,
          separatorBuilder: (_, __) => const SizedBox(height: 10),
          itemBuilder: (_, i) {
            final u = _users[i];
            final role = (u['role'] ?? 'user').toString();
            final initials = (u['name'] as String? ?? 'U').trim().split(' ').where((w) => w.isNotEmpty).map((w) => w[0]).take(2).join().toUpperCase();
            return Container(
              decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(14), border: Border.all(color: AppColors.border)),
              child: ListTile(
                contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                leading: Container(width: 46, height: 46, decoration: BoxDecoration(gradient: const LinearGradient(colors: [AppColors.primary, AppColors.secondary], begin: Alignment.topLeft, end: Alignment.bottomRight), borderRadius: BorderRadius.circular(14)),
                  child: Center(child: Text(initials, style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w800, fontSize: 16)))),
                title: Text(u['name'] ?? 'Unknown', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 15)),
                subtitle: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                  Text(u['email'] ?? '', style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
                  if (u['business_name'] != null) Text(u['business_name'], style: const TextStyle(color: AppColors.primary, fontSize: 11, fontWeight: FontWeight.w600)),
                ]),
                trailing: Container(padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                  decoration: BoxDecoration(color: _roleBg(role), borderRadius: BorderRadius.circular(20)),
                  child: Text(role.toUpperCase(), style: TextStyle(color: _roleColor(role), fontSize: 10, fontWeight: FontWeight.w800))),
              ),
            );
          }),
  );
}

// ── Help & Support Page ───────────────────────────────────
class _HelpSupportPage extends StatelessWidget {
  const _HelpSupportPage();

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(title: const Text('Help & Support'), backgroundColor: AppColors.primary, foregroundColor: Colors.white,
        titleTextStyle: const TextStyle(color: Colors.white, fontWeight: FontWeight.w700, fontSize: 18)),
      body: SingleChildScrollView(padding: const EdgeInsets.all(16), child: Column(crossAxisAlignment: CrossAxisAlignment.stretch, children: [
        // Header card
        Container(
          decoration: BoxDecoration(gradient: const LinearGradient(colors: [AppColors.primary, AppColors.secondary], begin: Alignment.topLeft, end: Alignment.bottomRight), borderRadius: BorderRadius.circular(16)),
          padding: const EdgeInsets.all(24),
          child: const Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            Icon(Icons.support_agent, color: Colors.white, size: 36),
            SizedBox(height: 12),
            Text('How can we help?', style: TextStyle(color: Colors.white, fontSize: 22, fontWeight: FontWeight.w800)),
            SizedBox(height: 6),
            Text('Find answers to common questions below', style: TextStyle(color: Colors.white70, fontSize: 14)),
          ]),
        ),
        const SizedBox(height: 20),
        const Text('Frequently Asked Questions', style: TextStyle(fontWeight: FontWeight.w800, fontSize: 16, color: AppColors.textPri)),
        const SizedBox(height: 12),
        ..._faqs().map((faq) => _FaqCard(q: faq[0], a: faq[1])),
        const SizedBox(height: 24),
        const Text('Contact Us', style: TextStyle(fontWeight: FontWeight.w800, fontSize: 16, color: AppColors.textPri)),
        const SizedBox(height: 12),
        _contactCard(Icons.email_outlined, 'Email Support', 'support@mannapos.com', AppColors.primary, AppColors.primaryLt),
        const SizedBox(height: 10),
        _contactCard(Icons.phone_outlined, 'Phone Support', '+255 700 000 000', AppColors.success, AppColors.successLt),
        const SizedBox(height: 10),
        _contactCard(Icons.web_outlined, 'Website', 'www.mannapos.com', AppColors.secondary, const Color(0xFFF5F3FF)),
        const SizedBox(height: 32),
      ])),
    );
  }

  static List<List<String>> _faqs() => [
    ['How do I add a product?', 'Go to Products tab → tap the + button → fill in the product details (name, price, stock) → tap Add Product.'],
    ['How do I make a sale?', 'Go to POS tab → search or browse products → tap a product to add to cart → tap Cart tab → choose payment method → tap Complete Sale.'],
    ['How do I scan a barcode?', 'In the POS tab, tap the barcode scanner icon next to the search bar. Point your camera at the barcode and it will automatically detect the product.'],
    ['How do I add a product image?', 'When adding/editing a product, tap the image area at the top of the form to pick a photo from your gallery.'],
    ['Can I edit a sale after it\'s made?', 'Sales can be edited from the Sales tab. Tap on a sale and choose Edit. Note: completed sales should be reviewed before editing.'],
    ['How does the dashboard auto-update?', 'The dashboard refreshes automatically every 60 seconds. You can also tap the refresh icon to update manually.'],
    ['How do I change my password?', 'Go to More → Settings → scroll to Personal Information → tap Edit → scroll down to Change Password section.'],
  ];

  Widget _contactCard(IconData icon, String title, String value, Color color, Color bg) => Container(
    padding: const EdgeInsets.all(16),
    decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(12), border: Border.all(color: AppColors.border)),
    child: Row(children: [
      Container(width: 44, height: 44, decoration: BoxDecoration(color: bg, borderRadius: BorderRadius.circular(12)), child: Icon(icon, color: color, size: 22)),
      const SizedBox(width: 14),
      Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
        Text(title, style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14)),
        Text(value, style: TextStyle(color: color, fontSize: 13, fontWeight: FontWeight.w500)),
      ]),
    ]),
  );
}

class _FaqCard extends StatefulWidget {
  final String q, a;
  const _FaqCard({required this.q, required this.a});
  @override State<_FaqCard> createState() => _FaqCardState();
}
class _FaqCardState extends State<_FaqCard> {
  bool _open = false;
  @override
  Widget build(BuildContext context) => Container(
    margin: const EdgeInsets.only(bottom: 8),
    decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(12), border: Border.all(color: _open ? AppColors.primary : AppColors.border, width: _open ? 2 : 1)),
    child: Column(children: [
      ListTile(onTap: () => setState(() => _open = !_open),
        title: Text(widget.q, style: TextStyle(fontWeight: FontWeight.w700, fontSize: 14, color: _open ? AppColors.primary : AppColors.textPri)),
        trailing: Icon(_open ? Icons.keyboard_arrow_up : Icons.keyboard_arrow_down, color: _open ? AppColors.primary : AppColors.textSec)),
      if (_open) Padding(padding: const EdgeInsets.fromLTRB(16, 0, 16, 16),
        child: Text(widget.a, style: const TextStyle(color: AppColors.textSec, fontSize: 13, height: 1.5))),
    ]),
  );
}
