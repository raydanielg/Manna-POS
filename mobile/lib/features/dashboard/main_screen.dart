import 'package:flutter/material.dart';
import '../../shared/theme/app_colors.dart';
import '../dashboard/home_page.dart';
import '../products/products_page.dart';
import '../customers/customers_page.dart';
import '../sales/sales_page.dart';
import '../payroll/payroll_page.dart';

class MainScreen extends StatefulWidget {
  final int initialIndex;
  const MainScreen({super.key, this.initialIndex = 0});
  @override
  State<MainScreen> createState() => _MainScreenState();
}

class _MainScreenState extends State<MainScreen> {
  late int _currentIndex;
  @override
  void initState() { super.initState(); _currentIndex = widget.initialIndex; }

  static const _screens = [
    HomePage(),
    ProductsPage(),
    CustomersPage(),
    SalesPage(),
    PayrollPage(),
  ];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: IndexedStack(index: _currentIndex, children: _screens),
      bottomNavigationBar: _BottomNav(currentIndex: _currentIndex, onTap: (i) => setState(() => _currentIndex = i)),
    );
  }
}

class _BottomNav extends StatelessWidget {
  final int currentIndex;
  final ValueChanged<int> onTap;
  const _BottomNav({required this.currentIndex, required this.onTap});

  static const _items = [
    _NavDef(Icons.home_outlined, Icons.home_rounded, 'Home'),
    _NavDef(Icons.inventory_2_outlined, Icons.inventory_2_rounded, 'Products'),
    _NavDef(Icons.people_outline_rounded, Icons.people_rounded, 'Customers'),
    _NavDef(Icons.receipt_long_outlined, Icons.receipt_long_rounded, 'Sales'),
    _NavDef(Icons.account_balance_wallet_outlined, Icons.account_balance_wallet_rounded, 'Payroll'),
  ];

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: const BoxDecoration(
        color: Colors.white,
        border: Border(top: BorderSide(color: Color(0xFFEEEEEE), width: 1)),
        boxShadow: [BoxShadow(color: Color(0x0D000000), blurRadius: 12, offset: Offset(0, -2))],
      ),
      child: SafeArea(
        top: false,
        child: SizedBox(
          height: 58,
          child: Row(
            children: List.generate(_items.length, (i) => _NavItem(
              def: _items[i], active: currentIndex == i, onTap: () => onTap(i),
            )),
          ),
        ),
      ),
    );
  }
}

class _NavDef {
  final IconData icon, activeIcon;
  final String label;
  const _NavDef(this.icon, this.activeIcon, this.label);
}

class _NavItem extends StatelessWidget {
  final _NavDef def;
  final bool active;
  final VoidCallback onTap;
  const _NavItem({required this.def, required this.active, required this.onTap});

  @override
  Widget build(BuildContext context) {
    return Expanded(
      child: GestureDetector(
        onTap: onTap, behavior: HitTestBehavior.opaque,
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            AnimatedContainer(
              duration: const Duration(milliseconds: 200),
              height: 2.5, width: active ? 28 : 0,
              margin: const EdgeInsets.only(bottom: 6),
              decoration: BoxDecoration(
                color: AppColors.primary, borderRadius: BorderRadius.circular(2),
              ),
            ),
            Icon(active ? def.activeIcon : def.icon, size: 22,
              color: active ? AppColors.primary : const Color(0xFFAAAAAA)),
            const SizedBox(height: 3),
            Text(def.label, style: TextStyle(
              fontSize: 10, fontWeight: active ? FontWeight.w600 : FontWeight.w400,
              color: active ? AppColors.primary : const Color(0xFFAAAAAA),
            )),
          ],
        ),
      ),
    );
  }
}
