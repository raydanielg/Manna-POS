import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../../providers/product_provider.dart';
import '../../models/product.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/status_badge.dart';
import '../../widgets/search_bar_widget.dart';
import '../../widgets/shimmer_loading.dart';
import '../../widgets/empty_state.dart';
import '../../widgets/section_header.dart';
import '../../widgets/toast_helper.dart';
import '../../constants/app_constants.dart';

class ProductsScreen extends StatefulWidget {
  const ProductsScreen({super.key});
  @override State<ProductsScreen> createState() => _ProductsScreenState();
}

class _ProductsScreenState extends State<ProductsScreen> with SingleTickerProviderStateMixin {
  late TabController _tabController;
  final _searchCtrl = TextEditingController();
  final _fmt = NumberFormat('#,##0.00');
  String _search = '';

  final _tabs = ['Products', 'Categories', 'Brands', 'Units', 'Variations', 'Price Groups', 'Warranties'];

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: _tabs.length, vsync: this);
    _tabController.addListener(() {
      if (!_tabController.indexIsChanging) setState(() {});
    });
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<ProductProvider>().fetchProducts();
    });
  }

  @override
  void dispose() {
    _tabController.dispose();
    _searchCtrl.dispose();
    super.dispose();
  }

  Future<void> _refresh() async {
    await context.read<ProductProvider>().fetchProducts();
  }

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    return Scaffold(
      backgroundColor: theme.scaffoldBackgroundColor,
      appBar: AppBar(
        title: const Text('Products'),
        actions: [
          IconButton(icon: const Icon(Icons.refresh), onPressed: _refresh),
        ],
        bottom: TabBar(
          controller: _tabController,
          isScrollable: true,
          labelColor: theme.colorScheme.primary,
          unselectedLabelColor: theme.colorScheme.onSurface.withValues(alpha: 0.6),
          indicatorColor: theme.colorScheme.primary,
          indicatorWeight: 3,
          tabs: _tabs.map((t) => Tab(text: t)).toList(),
        ),
      ),
      body: Column(
        children: [
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 12, 16, 0),
            child: AppSearchBar(
              hint: 'Search products...',
              controller: _searchCtrl,
              onChanged: (v) {
                _search = v;
                context.read<ProductProvider>().search(v);
              },
            ),
          ),
          const SizedBox(height: 12),
          Expanded(child: _buildTabContent()),
        ],
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const AddProductScreen())),
        icon: const Icon(Icons.add),
        label: const Text('Add Product'),
      ),
    );
  }

  Widget _buildTabContent() {
    switch (_tabController.index) {
      case 0: return _buildProductsTab();
      case 1: return _buildCategoriesTab();
      case 2: return _buildBrandsTab();
      case 3: return _buildUnitsTab();
      case 4: return _buildVariationsTab();
      case 5: return _buildPriceGroupsTab();
      case 6: return _buildWarrantiesTab();
      default: return const SizedBox.shrink();
    }
  }

  Widget _buildProductsTab() {
    return Consumer<ProductProvider>(
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
        final products = provider.filteredProducts ?? provider.products;
        if (products.isEmpty) {
          return EmptyState(
            icon: Icons.inventory_2_outlined,
            title: 'No Products',
            subtitle: 'Add your first product to get started',
            actionLabel: 'Add Product',
            onAction: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const AddProductScreen())),
          );
        }
        return RefreshIndicator(
          onRefresh: _refresh,
          child: GridView.builder(
            padding: const EdgeInsets.fromLTRB(16, 0, 16, 100),
            gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
              crossAxisCount: 2,
              childAspectRatio: 0.72,
              crossAxisSpacing: 12,
              mainAxisSpacing: 12,
            ),
            itemCount: products.length,
            itemBuilder: (_, i) => _productCard(products[i]),
          ),
        );
      },
    );
  }

  Widget _productCard(Product p) {
    final isLow = p.isLowStock;
    final isOut = p.isOutOfStock;
    final theme = Theme.of(context);
    return GlassCard(
      onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => AddProductScreen(product: p))),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Expanded(
            child: Container(
              width: double.infinity,
              decoration: BoxDecoration(
                color: isOut ? AppColors.dangerLt : isLow ? AppColors.warningLt : AppColors.primaryLt,
                borderRadius: const BorderRadius.vertical(top: Radius.circular(12)),
              ),
              child: Center(
                child: p.imageUrl != null
                    ? ClipRRect(
                        borderRadius: const BorderRadius.vertical(top: Radius.circular(12)),
                        child: Image.network(p.imageUrl!, fit: BoxFit.cover, width: double.infinity, height: double.infinity,
                          errorBuilder: (_, __, ___) => Icon(Icons.inventory_2_outlined, size: 40, color: isOut ? AppColors.danger : isLow ? AppColors.warning : AppColors.primary)),
                      )
                    : Icon(Icons.inventory_2_outlined, size: 40, color: isOut ? AppColors.danger : isLow ? AppColors.warning : AppColors.primary),
              ),
            ),
          ),
          Padding(
            padding: const EdgeInsets.all(10),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(p.name, style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 13), maxLines: 1, overflow: TextOverflow.ellipsis),
                const SizedBox(height: 4),
                Text('${AppConstants.currency} ${_fmt.format(p.sellingPrice)}', style: TextStyle(color: theme.colorScheme.primary, fontWeight: FontWeight.w800, fontSize: 14)),
                const SizedBox(height: 6),
                Row(
                  children: [
                    StatusBadge(
                      label: isOut ? 'Out of Stock' : isLow ? 'Low Stock' : 'In Stock',
                      color: isOut ? AppColors.danger : isLow ? AppColors.warning : AppColors.success,
                      bgColor: isOut ? AppColors.dangerLt : isLow ? AppColors.warningLt : AppColors.successLt,
                    ),
                    const Spacer(),
                    Text('${p.stockQuantity}', style: TextStyle(color: AppColors.textSec, fontSize: 11)),
                  ],
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildCategoriesTab() {
    return Consumer<ProductProvider>(
      builder: (context, provider, _) {
        if (provider.isLoading) return const ShimmerLoading();
        if (provider.categories.isEmpty) {
          return const EmptyState(icon: Icons.category_outlined, title: 'No Categories', subtitle: 'Categories will appear here');
        }
        return RefreshIndicator(
          onRefresh: _refresh,
          child: ListView.separated(
            padding: const EdgeInsets.fromLTRB(16, 8, 16, 100),
            itemCount: provider.categories.length,
            separatorBuilder: (_, __) => const SizedBox(height: 8),
            itemBuilder: (_, i) {
              final c = provider.categories[i];
              return GlassCard(
                child: ListTile(
                  leading: Container(width: 12, height: 12, decoration: BoxDecoration(color: c.color ?? AppColors.primary, shape: BoxShape.circle)),
                  title: Text(c.name, style: const TextStyle(fontWeight: FontWeight.w600)),
                  trailing: Text('${c.productCount} products', style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
                ),
              );
            },
          ),
        );
      },
    );
  }

  Widget _buildBrandsTab() {
    return Consumer<ProductProvider>(
      builder: (context, provider, _) {
        if (provider.isLoading) return const ShimmerLoading();
        if (provider.brands.isEmpty) {
          return const EmptyState(icon: Icons.bookmark_outline, title: 'No Brands', subtitle: 'Brands will appear here');
        }
        return RefreshIndicator(
          onRefresh: _refresh,
          child: ListView.separated(
            padding: const EdgeInsets.fromLTRB(16, 8, 16, 100),
            itemCount: provider.brands.length,
            separatorBuilder: (_, __) => const SizedBox(height: 8),
            itemBuilder: (_, i) {
              final b = provider.brands[i];
              return GlassCard(
                child: ListTile(
                  leading: Container(
                    width: 40, height: 40,
                    decoration: BoxDecoration(color: AppColors.accent.withValues(alpha: 0.12), borderRadius: BorderRadius.circular(10)),
                    child: const Icon(Icons.bookmark_outline, color: AppColors.accent, size: 20),
                  ),
                  title: Text(b.name ?? '', style: const TextStyle(fontWeight: FontWeight.w600)),
                  subtitle: b.description != null ? Text(b.description!, style: const TextStyle(fontSize: 11)) : null,
                  trailing: Text('${b.productCount ?? 0} products', style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
                ),
              );
            },
          ),
        );
      },
    );
  }

  Widget _buildUnitsTab() {
    return Consumer<ProductProvider>(
      builder: (context, provider, _) {
        if (provider.isLoading) return const ShimmerLoading();
        if (provider.units.isEmpty) {
          return const EmptyState(icon: Icons.straighten_outlined, title: 'No Units', subtitle: 'Units will appear here');
        }
        return RefreshIndicator(
          onRefresh: _refresh,
          child: ListView.separated(
            padding: const EdgeInsets.fromLTRB(16, 8, 16, 100),
            itemCount: provider.units.length,
            separatorBuilder: (_, __) => const SizedBox(height: 8),
            itemBuilder: (_, i) {
              final u = provider.units[i];
              return GlassCard(
                child: ListTile(
                  leading: Container(
                    width: 40, height: 40,
                    decoration: BoxDecoration(color: AppColors.cyan.withValues(alpha: 0.12), borderRadius: BorderRadius.circular(10)),
                    child: const Icon(Icons.straighten_outlined, color: AppColors.cyan, size: 20),
                  ),
                  title: Text(u.name ?? '', style: const TextStyle(fontWeight: FontWeight.w600)),
                  subtitle: u.shortName != null ? Text('Short: ${u.shortName}', style: const TextStyle(fontSize: 11)) : null,
                ),
              );
            },
          ),
        );
      },
    );
  }

  Widget _buildVariationsTab() {
    return Consumer<ProductProvider>(
      builder: (context, provider, _) {
        if (provider.isLoading) return const ShimmerLoading();
        if (provider.variations.isEmpty) {
          return const EmptyState(icon: Icons.dynamic_feed_outlined, title: 'No Variations', subtitle: 'Variation groups will appear here');
        }
        return RefreshIndicator(
          onRefresh: _refresh,
          child: ListView.separated(
            padding: const EdgeInsets.fromLTRB(16, 8, 16, 100),
            itemCount: provider.variations.length,
            separatorBuilder: (_, __) => const SizedBox(height: 8),
            itemBuilder: (_, i) {
              final v = provider.variations[i];
              return GlassCard(
                child: ListTile(
                  leading: Container(
                    width: 40, height: 40,
                    decoration: BoxDecoration(color: AppColors.purple.withValues(alpha: 0.12), borderRadius: BorderRadius.circular(10)),
                    child: const Icon(Icons.dynamic_feed_outlined, color: AppColors.purple, size: 20),
                  ),
                  title: Text(v.name ?? '', style: const TextStyle(fontWeight: FontWeight.w600)),
                  subtitle: v.values != null ? Text('${v.values!.length} values', style: const TextStyle(fontSize: 11)) : null,
                ),
              );
            },
          ),
        );
      },
    );
  }

  Widget _buildPriceGroupsTab() {
    return Consumer<ProductProvider>(
      builder: (context, provider, _) {
        if (provider.isLoading) return const ShimmerLoading();
        if (provider.priceGroups.isEmpty) {
          return const EmptyState(icon: Icons.pricing_outlined, title: 'No Price Groups', subtitle: 'Price groups will appear here');
        }
        return RefreshIndicator(
          onRefresh: _refresh,
          child: ListView.separated(
            padding: const EdgeInsets.fromLTRB(16, 8, 16, 100),
            itemCount: provider.priceGroups.length,
            separatorBuilder: (_, __) => const SizedBox(height: 8),
            itemBuilder: (_, i) {
              final pg = provider.priceGroups[i];
              return GlassCard(
                child: ListTile(
                  leading: Container(
                    width: 40, height: 40,
                    decoration: BoxDecoration(color: AppColors.warningLt, borderRadius: BorderRadius.circular(10)),
                    child: const Icon(Icons.pricing_outlined, color: AppColors.warning, size: 20),
                  ),
                  title: Text(pg.name ?? '', style: const TextStyle(fontWeight: FontWeight.w600)),
                  subtitle: pg.type != null ? Text('Type: ${pg.type}', style: const TextStyle(fontSize: 11)) : null,
                ),
              );
            },
          ),
        );
      },
    );
  }

  Widget _buildWarrantiesTab() {
    return Consumer<ProductProvider>(
      builder: (context, provider, _) {
        if (provider.isLoading) return const ShimmerLoading();
        if (provider.warranties.isEmpty) {
          return const EmptyState(icon: Icons.verified_outlined, title: 'No Warranties', subtitle: 'Warranties will appear here');
        }
        return RefreshIndicator(
          onRefresh: _refresh,
          child: ListView.separated(
            padding: const EdgeInsets.fromLTRB(16, 8, 16, 100),
            itemCount: provider.warranties.length,
            separatorBuilder: (_, __) => const SizedBox(height: 8),
            itemBuilder: (_, i) {
              final w = provider.warranties[i];
              return GlassCard(
                child: ListTile(
                  leading: Container(
                    width: 40, height: 40,
                    decoration: BoxDecoration(color: AppColors.purple.withValues(alpha: 0.12), borderRadius: BorderRadius.circular(10)),
                    child: const Icon(Icons.verified_outlined, color: AppColors.purple, size: 20),
                  ),
                  title: Text(w.name ?? '', style: const TextStyle(fontWeight: FontWeight.w600)),
                  subtitle: w.duration != null ? Text('${w.duration} ${w.durationUnit ?? ''}', style: const TextStyle(fontSize: 11)) : null,
                ),
              );
            },
          ),
        );
      },
    );
  }
}
