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
          _navTile(context, Icons.local_offer_outlined, 'Discounts', 'Manage promotions', AppColors.secondary, const Color(0xFFF5F3FF), const _DiscountsPage()),
          const Divider(height: 1),
          _navTile(context, Icons.category_outlined, 'Categories', 'Product categories', AppColors.success, AppColors.successLt, const _CategoriesPage()),
          const Divider(height: 1),
          _navTile(context, Icons.branding_watermark_outlined, 'Brands', 'Product brands', AppColors.primary, AppColors.primaryLt, const _BrandsPage()),
        ])),

        const SizedBox(height: 20),
        _sectionLabel('System'),
        const SizedBox(height: 8),

        AppCard(child: Column(children: [
          _navTile(context, Icons.group_outlined, 'User Management', 'Manage app users', AppColors.primary, AppColors.primaryLt, null),
          const Divider(height: 1),
          _navTile(context, Icons.business_outlined, 'Suppliers', 'Manage suppliers', AppColors.warning, AppColors.warningLt, const _SuppliersPage()),
          const Divider(height: 1),
          _navTile(context, Icons.help_outline, 'Help & Support', 'Get help using MannaPOS', AppColors.textSec, AppColors.border, null),
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

// ── Discounts Page ────────────────────────────────────────
class _DiscountsPage extends StatefulWidget {
  const _DiscountsPage();
  @override State<_DiscountsPage> createState() => _DiscountsPageState();
}
class _DiscountsPageState extends State<_DiscountsPage> {
  List<dynamic> _items = [];
  bool _loading = true;
  String? _error;

  @override
  void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    setState(() { _loading = true; _error = null; });
    try {
      final data = await ApiService.get('/discounts');
      setState(() { _items = data as List; _loading = false; });
    } catch (e) { setState(() { _error = e.toString(); _loading = false; }); }
  }

  @override
  Widget build(BuildContext context) => Scaffold(
    appBar: AppBar(title: const Text('Discounts'), actions: [IconButton(icon: const Icon(Icons.refresh, color: Colors.white), onPressed: _load)]),
    body: _loading ? const Center(child: CircularProgressIndicator(color: AppColors.primary))
      : _error != null ? Center(child: Text(_error!, style: const TextStyle(color: AppColors.danger)))
      : _items.isEmpty ? const Center(child: Text('No discounts found', style: TextStyle(color: AppColors.textSec)))
      : ListView.separated(
          padding: const EdgeInsets.all(16),
          itemCount: _items.length,
          separatorBuilder: (_, __) => const Divider(height: 1),
          itemBuilder: (_, i) {
            final item = _items[i];
            return ListTile(
              leading: Container(width: 36, height: 36, decoration: BoxDecoration(color: const Color(0xFFF5F3FF), borderRadius: BorderRadius.circular(9)), child: Icon(Icons.local_offer_outlined, color: AppColors.secondary, size: 20)),
              title: Text(item['name'] ?? '', style: const TextStyle(fontWeight: FontWeight.w600)),
              subtitle: Text('${item['discount_type'] ?? 'fixed'} · ${item['discount'] ?? 0}', style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
            );
          }),
  );
}