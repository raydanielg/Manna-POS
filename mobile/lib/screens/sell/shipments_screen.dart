import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/status_badge.dart';
import '../../widgets/search_bar_widget.dart';
import '../../widgets/shimmer_loading.dart';
import '../../widgets/empty_state.dart';
import '../../widgets/section_header.dart';
import '../../widgets/toast_helper.dart';
import '../../widgets/loading_overlay.dart';
import '../../providers/sale_provider.dart';
import '../../models/sale.dart';

class ShipmentsScreen extends StatefulWidget {
  const ShipmentsScreen({super.key});
  @override State<ShipmentsScreen> createState() => _ShipmentsScreenState();
}

class _ShipmentsScreenState extends State<ShipmentsScreen> {
  List<Map<String, dynamic>> _shipments = [];
  bool _loading = true;
  final _fmt = NumberFormat('#,##0');

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() => _loading = true);
    try {
      await context.read<SaleProvider>().fetchSales(status: 'shipped');
      if (mounted) {
        setState(() {
          _shipments = context.read<SaleProvider>().sales
              .map((s) => {
                'sale': s,
                'tracking': 'SHIP-${s.reference}',
                'carrier': 'Courier',
                'status': s.status,
              })
              .toList();
          _loading = false;
        });
      }
    } catch (_) {
      if (mounted) setState(() => _loading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    return Scaffold(
      backgroundColor: theme.scaffoldBackgroundColor,
      appBar: AppBar(
        backgroundColor: theme.colorScheme.primary,
        foregroundColor: Colors.white,
        elevation: 0,
        title: const Text('Shipments',
          style: TextStyle(fontWeight: FontWeight.w700)),
        actions: [
          IconButton(icon: const Icon(Icons.refresh), onPressed: _load),
        ],
      ),
      body: _loading
          ? const ShimmerLoading(child: ShimmerCard())
          : _shipments.isEmpty
              ? EmptyState(
                  icon: Icons.local_shipping_outlined,
                  title: 'No Shipments',
                  subtitle: 'Shipped orders will appear here',
                )
              : RefreshIndicator(
                  onRefresh: _load,
                  child: ListView.separated(
                    padding: const EdgeInsets.fromLTRB(16, 16, 16, 100),
                    itemCount: _shipments.length,
                    separatorBuilder: (_, __) => const SizedBox(height: 10),
                    itemBuilder: (_, i) => _shipmentCard(_shipments[i], theme),
                  ),
                ),
    );
  }

  Widget _shipmentCard(Map<String, dynamic> shipment, ThemeData theme) {
    final sale = shipment['sale'] as Sale;
    final tracking = shipment['tracking'] as String;
    final carrier = shipment['carrier'] as String;

    return GlassCard(
      child: InkWell(
        onTap: () => _showShipmentDetail(shipment, theme),
        borderRadius: BorderRadius.circular(14),
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  Container(
                    width: 46, height: 46,
                    decoration: BoxDecoration(
                      color: Colors.blue.withValues(alpha: 0.15),
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: const Icon(Icons.local_shipping,
                      color: Colors.blue, size: 22),
                  ),
                  const SizedBox(width: 14),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(sale.reference,
                          style: const TextStyle(
                            fontWeight: FontWeight.w700, fontSize: 14)),
                        const SizedBox(height: 3),
                        Text('Tracking: $tracking',
                          style: TextStyle(
                            color: theme.colorScheme.onSurface.withValues(alpha: 0.6),
                            fontSize: 12)),
                      ],
                    ),
                  ),
                  StatusBadge.fromStatus(sale.status),
                ],
              ),
              const SizedBox(height: 12),
              Row(
                children: [
                  Icon(Icons.business, size: 14,
                    color: theme.colorScheme.onSurface.withValues(alpha: 0.5)),
                  const SizedBox(width: 4),
                  Text(carrier,
                    style: TextStyle(
                      fontSize: 12,
                      color: theme.colorScheme.onSurface.withValues(alpha: 0.6))),
                  const Spacer(),
                  Text(sale.customerName,
                    style: TextStyle(
                      fontSize: 12,
                      color: theme.colorScheme.onSurface.withValues(alpha: 0.6))),
                ],
              ),
              const SizedBox(height: 12),
              // Status timeline
              _statusTimeline(theme),
            ],
          ),
        ),
      ),
    );
  }

  Widget _statusTimeline(ThemeData theme) {
    final steps = ['Packed', 'Shipped', 'In Transit', 'Delivered'];
    return Row(
      children: List.generate(steps.length, (i) {
        return Expanded(
          child: Column(
            children: [
              Container(
                width: 12, height: 12,
                decoration: BoxDecoration(
                  color: i == 0
                      ? Colors.blue
                      : i <= 1
                          ? Colors.blue.withValues(alpha: 0.5)
                          : theme.colorScheme.outline.withValues(alpha: 0.3),
                  shape: BoxShape.circle,
                ),
              ),
              const SizedBox(height: 4),
              Text(steps[i],
                textAlign: TextAlign.center,
                style: TextStyle(
                  fontSize: 9,
                  color: i <= 1
                      ? Colors.blue
                      : theme.colorScheme.onSurface.withValues(alpha: 0.5))),
            ],
          ),
        );
      }),
    );
  }

  void _showShipmentDetail(Map<String, dynamic> shipment, ThemeData theme) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (_) => _ShipmentDetailSheet(shipment: shipment),
    );
  }
}

class _ShipmentDetailSheet extends StatelessWidget {
  final Map<String, dynamic> shipment;
  const _ShipmentDetailSheet({required this.shipment});

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    final sale = shipment['sale'] as Sale;
    return Container(
      decoration: BoxDecoration(
        color: theme.scaffoldBackgroundColor,
        borderRadius: const BorderRadius.vertical(top: Radius.circular(24)),
      ),
      padding: const EdgeInsets.fromLTRB(24, 16, 24, 32),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Center(child: Container(
            width: 40, height: 4,
            decoration: BoxDecoration(
              color: theme.colorScheme.outline.withValues(alpha: 0.3),
              borderRadius: BorderRadius.circular(4)))),
          const SizedBox(height: 20),
          Row(
            children: [
              Icon(Icons.local_shipping, color: theme.colorScheme.primary),
              const SizedBox(width: 8),
              Text('Shipment Detail',
                style: TextStyle(
                  fontWeight: FontWeight.w700, fontSize: 18,
                  color: theme.colorScheme.onSurface)),
            ],
          ),
          const SizedBox(height: 16),
          _detailLine('Sale Reference', sale.reference),
          _detailLine('Tracking No.', shipment['tracking']),
          _detailLine('Carrier', shipment['carrier']),
          _detailLine('Customer', sale.customerName),
          _detailLine('Status', sale.status),
          _detailLine('Date', sale.saleDate),
          const SizedBox(height: 16),
          const Text('Tracking Timeline',
            style: TextStyle(fontWeight: FontWeight.w700, fontSize: 15)),
          const SizedBox(height: 8),
          _timelineStep('Order Packed', 'Completed', true),
          _timelineStep('Handed to Courier', 'Completed', true),
          _timelineStep('In Transit', 'In Progress', false),
          _timelineStep('Delivered', 'Pending', false),
        ],
      ),
    );
  }

  Widget _detailLine(String label, String value) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 8),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(label, style: const TextStyle(color: Colors.grey, fontSize: 13)),
          Text(value,
            style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13)),
        ],
      ),
    );
  }

  Widget _timelineStep(String title, String status, bool done) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 8),
      child: Row(
        children: [
          Container(
            width: 20, height: 20,
            decoration: BoxDecoration(
              color: done ? Colors.green : Colors.grey.shade200,
              shape: BoxShape.circle,
            ),
            child: done
                ? const Icon(Icons.check, size: 12, color: Colors.white)
                : null,
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(title,
                  style: TextStyle(
                    fontWeight: FontWeight.w600,
                    color: done ? Colors.green : Colors.grey)),
                Text(status,
                  style: TextStyle(fontSize: 12, color: Colors.grey)),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
