import 'package:flutter/material.dart';
import '../../shared/theme/app_theme.dart';

class SalesPage extends StatefulWidget {
  const SalesPage({super.key});

  @override
  State<SalesPage> createState() => _SalesPageState();
}

class _SalesPageState extends State<SalesPage> with SingleTickerProviderStateMixin {
  late TabController _tabController;
  final List<Sale> _sales = _sampleSales;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 3, vsync: this);
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Sales'),
        bottom: TabBar(
          controller: _tabController,
          tabs: const [
            Tab(text: 'All Sales'),
            Tab(text: 'Today'),
            Tab(text: 'This Week'),
          ],
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.filter_list),
            onPressed: () {},
          ),
          IconButton(
            icon: const Icon(Icons.download),
            onPressed: () {},
          ),
        ],
      ),
      body: TabBarView(
        controller: _tabController,
        children: [
          _buildSalesList(_sales),
          _buildSalesList(_sales.where((sale) => sale.isToday).toList()),
          _buildSalesList(_sales.where((sale) => sale.isThisWeek).toList()),
        ],
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () {},
        icon: const Icon(Icons.add),
        label: const Text('New Sale'),
      ),
    );
  }

  Widget _buildSalesList(List<Sale> sales) {
    if (sales.isEmpty) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(
              Icons.receipt_long_outlined,
              size: 64,
              color: AppTheme.textTertiary,
            ),
            const SizedBox(height: 16),
            Text(
              'No sales found',
              style: TextStyle(
                color: AppTheme.textSecondary,
                fontSize: 16,
              ),
            ),
          ],
        ),
      );
    }

    return ListView.builder(
      padding: const EdgeInsets.all(16),
      itemCount: sales.length,
      itemBuilder: (context, index) {
        final sale = sales[index];
        return _buildSaleCard(sale);
      },
    );
  }

  Widget _buildSaleCard(Sale sale) {
    return Card(
      margin: const EdgeInsets.only(bottom: 12),
      elevation: 0,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(16),
      ),
      child: InkWell(
        onTap: () {},
        borderRadius: BorderRadius.circular(16),
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text(
                    sale.invoiceNumber,
                    style: const TextStyle(
                      fontSize: 16,
                      fontWeight: FontWeight.w700,
                      color: AppTheme.textPrimary,
                    ),
                  ),
                  _buildStatusChip(sale.status),
                ],
              ),
              const SizedBox(height: 8),
              Row(
                children: [
                  Icon(
                    Icons.person_outlined,
                    size: 16,
                    color: AppTheme.textTertiary,
                  ),
                  const SizedBox(width: 4),
                  Text(
                    sale.customerName,
                    style: const TextStyle(
                      fontSize: 14,
                      color: AppTheme.textSecondary,
                    ),
                  ),
                  const SizedBox(width: 16),
                  Icon(
                    Icons.access_time,
                    size: 16,
                    color: AppTheme.textTertiary,
                  ),
                  const SizedBox(width: 4),
                  Text(
                    sale.time,
                    style: const TextStyle(
                      fontSize: 14,
                      color: AppTheme.textTertiary,
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 12),
              const Divider(),
              const SizedBox(height: 12),
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text(
                    '${sale.items} items',
                    style: const TextStyle(
                      fontSize: 14,
                      color: AppTheme.textSecondary,
                    ),
                  ),
                  Text(
                    'TSh ${sale.total.toStringAsFixed(2)}',
                    style: const TextStyle(
                      fontSize: 20,
                      fontWeight: FontWeight.w800,
                      color: AppTheme.primaryColor,
                    ),
                  ),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildStatusChip(String status) {
    Color color;
    String label;
    
    switch (status.toLowerCase()) {
      case 'completed':
        color = AppTheme.successColor;
        label = 'Completed';
        break;
      case 'pending':
        color = AppTheme.warningColor;
        label = 'Pending';
        break;
      case 'cancelled':
        color = AppTheme.errorColor;
        label = 'Cancelled';
        break;
      default:
        color = AppTheme.textTertiary;
        label = status;
    }
    
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
      decoration: BoxDecoration(
        color: color.withOpacity(0.1),
        borderRadius: BorderRadius.circular(12),
      ),
      child: Text(
        label,
        style: TextStyle(
          fontSize: 12,
          fontWeight: FontWeight.w600,
          color: color,
        ),
      ),
    );
  }
}

class Sale {
  final String id;
  final String invoiceNumber;
  final String customerName;
  final String time;
  final int items;
  final double total;
  final String status;
  final bool isToday;
  final bool isThisWeek;

  Sale({
    required this.id,
    required this.invoiceNumber,
    required this.customerName,
    required this.time,
    required this.items,
    required this.total,
    required this.status,
    this.isToday = false,
    this.isThisWeek = false,
  });
}

final List<Sale> _sampleSales = [
  Sale(
    id: '1',
    invoiceNumber: 'INV-001',
    customerName: 'John Doe',
    time: '10:30 AM',
    items: 5,
    total: 15000.0,
    status: 'Completed',
    isToday: true,
    isThisWeek: true,
  ),
  Sale(
    id: '2',
    invoiceNumber: 'INV-002',
    customerName: 'Jane Smith',
    time: '11:45 AM',
    items: 3,
    total: 8500.0,
    status: 'Completed',
    isToday: true,
    isThisWeek: true,
  ),
  Sale(
    id: '3',
    invoiceNumber: 'INV-003',
    customerName: 'Mike Johnson',
    time: '2:15 PM',
    items: 8,
    total: 25000.0,
    status: 'Pending',
    isToday: true,
    isThisWeek: true,
  ),
  Sale(
    id: '4',
    invoiceNumber: 'INV-004',
    customerName: 'Sarah Williams',
    time: 'Yesterday',
    items: 2,
    total: 4500.0,
    status: 'Completed',
    isToday: false,
    isThisWeek: true,
  ),
  Sale(
    id: '5',
    invoiceNumber: 'INV-005',
    customerName: 'David Brown',
    time: '2 days ago',
    items: 4,
    total: 12000.0,
    status: 'Cancelled',
    isToday: false,
    isThisWeek: true,
  ),
  Sale(
    id: '6',
    invoiceNumber: 'INV-006',
    customerName: 'Emily Davis',
    time: '1 week ago',
    items: 6,
    total: 18000.0,
    status: 'Completed',
    isToday: false,
    isThisWeek: false,
  ),
];
