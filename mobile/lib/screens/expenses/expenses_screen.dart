import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../../providers/expense_provider.dart';
import '../../models/expense.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/status_badge.dart';
import '../../widgets/search_bar_widget.dart';
import '../../widgets/filter_chip_row.dart';
import '../../widgets/shimmer_loading.dart';
import '../../widgets/empty_state.dart';
import '../../widgets/section_header.dart';
import '../../widgets/toast_helper.dart';
import '../../constants/app_constants.dart';

class ExpensesScreen extends StatefulWidget {
  const ExpensesScreen({super.key});
  @override State<ExpensesScreen> createState() => _ExpensesScreenState();
}

class _ExpensesScreenState extends State<ExpensesScreen> with SingleTickerProviderStateMixin {
  late TabController _tabController;
  final _searchCtrl = TextEditingController();
  final _fmt = NumberFormat('#,##0.00');
  String _search = '';

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 2, vsync: this);
    _tabController.addListener(() {
      if (!_tabController.indexIsChanging) setState(() {});
    });
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _loadData();
    });
  }

  @override
  void dispose() {
    _tabController.dispose();
    _searchCtrl.dispose();
    super.dispose();
  }

  Future<void> _loadData() async {
    await Future.wait([
      context.read<ExpenseProvider>().fetchExpenses(),
      context.read<ExpenseProvider>().fetchCategories(),
    ]);
  }

  Future<void> _refresh() async {
    await _loadData();
  }

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    return Scaffold(
      backgroundColor: theme.scaffoldBackgroundColor,
      appBar: AppBar(
        title: const Text('Expenses'),
        actions: [IconButton(icon: const Icon(Icons.refresh), onPressed: _refresh)],
        bottom: TabBar(
          controller: _tabController,
          labelColor: theme.colorScheme.primary,
          unselectedLabelColor: theme.colorScheme.onSurface.withValues(alpha: 0.6),
          indicatorColor: theme.colorScheme.primary,
          indicatorWeight: 3,
          tabs: const [Tab(text: 'All Expenses'), Tab(text: 'Categories')],
        ),
      ),
      body: Column(
        children: [
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 12, 16, 0),
            child: AppSearchBar(
              hint: 'Search expenses...',
              controller: _searchCtrl,
              onChanged: (v) {
                _search = v;
                context.read<ExpenseProvider>().fetchExpenses();
              },
            ),
          ),
          const SizedBox(height: 12),
          Expanded(child: _tabController.index == 0 ? _buildExpensesTab() : _buildCategoriesTab()),
        ],
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const AddExpenseScreen())),
        icon: const Icon(Icons.add),
        label: const Text('Add Expense'),
      ),
    );
  }

  Widget _buildExpensesTab() {
    return Consumer<ExpenseProvider>(
      builder: (context, provider, _) {
        if (provider.isLoading) return const ShimmerLoading();
        if (provider.error != null) {
          return Center(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                const Icon(Icons.error_outline, size: 48, color: AppColors.error),
                const SizedBox(height: 12),
                Text(provider.error!, style: const TextStyle(color: AppColors.textSec)),
                const SizedBox(height: 16),
                ElevatedButton(onPressed: _refresh, child: const Text('Retry')),
              ],
            ),
          );
        }
        final expenses = provider.expenses;
        if (expenses.isEmpty) {
          return const EmptyState(
            icon: Icons.receipt_long_outlined,
            title: 'No Expenses',
            subtitle: 'Track your business expenses',
            actionLabel: 'Add Expense',
          );
        }
        final total = expenses.fold<double>(0, (s, e) => s + e.amount);
        return RefreshIndicator(
          onRefresh: _refresh,
          child: ListView.separated(
            padding: const EdgeInsets.fromLTRB(16, 0, 16, 100),
            itemCount: expenses.length + 1,
            separatorBuilder: (_, __) => const SizedBox(height: 10),
            itemBuilder: (_, i) {
              if (i == 0) {
                return GlassCard(
                  color: AppColors.dangerLt,
                  child: Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 14),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        const Text('Total Expenses', style: TextStyle(color: AppColors.danger, fontWeight: FontWeight.w600)),
                        Text('${AppConstants.currency} ${_fmt.format(total)}', style: const TextStyle(color: AppColors.danger, fontWeight: FontWeight.w800, fontSize: 18)),
                      ],
                    ),
                  ),
                );
              }
              return _expenseCard(expenses[i - 1]);
            },
          ),
        );
      },
    );
  }

  Widget _expenseCard(Expense e) {
    return GlassCard(
      onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => AddExpenseScreen(expense: e))),
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Row(
          children: [
            Container(
              width: 48, height: 48,
              decoration: BoxDecoration(color: AppColors.dangerLt, borderRadius: BorderRadius.circular(12)),
              child: const Icon(Icons.receipt_outlined, color: AppColors.danger),
            ),
            const SizedBox(width: 14),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(e.reference, style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 15)),
                  const SizedBox(height: 3),
                  Text(e.categoryName, style: const TextStyle(color: AppColors.textSec, fontSize: 13)),
                  const SizedBox(height: 2),
                  Text(e.expenseDate, style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
                ],
              ),
            ),
            Column(
              crossAxisAlignment: CrossAxisAlignment.end,
              children: [
                Text('${AppConstants.currency} ${_fmt.format(e.amount)}', style: const TextStyle(color: AppColors.danger, fontWeight: FontWeight.w800, fontSize: 15)),
                const SizedBox(height: 6),
                StatusBadge.fromStatus('paid'),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildCategoriesTab() {
    return Consumer<ExpenseProvider>(
      builder: (context, provider, _) {
        if (provider.isLoading) return const ShimmerLoading();
        final categories = provider.categories;
        if (categories.isEmpty) {
          return const EmptyState(icon: Icons.category_outlined, title: 'No Categories', subtitle: 'Expense categories will appear here');
        }
        return RefreshIndicator(
          onRefresh: _refresh,
          child: ListView.separated(
            padding: const EdgeInsets.fromLTRB(16, 8, 16, 100),
            itemCount: categories.length,
            separatorBuilder: (_, __) => const SizedBox(height: 8),
            itemBuilder: (_, i) {
              final c = categories[i];
              return GlassCard(
                child: ListTile(
                  leading: Container(width: 14, height: 14, decoration: BoxDecoration(color: c.color ?? AppColors.secondary, shape: BoxShape.circle)),
                  title: Text(c.name, style: const TextStyle(fontWeight: FontWeight.w600)),
                  subtitle: c.description != null ? Text(c.description!, style: const TextStyle(fontSize: 11)) : null,
                  trailing: Text('${AppConstants.currency} ${_fmt.format(c.totalExpenses)}', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 13)),
                ),
              );
            },
          ),
        );
      },
    );
  }
}
