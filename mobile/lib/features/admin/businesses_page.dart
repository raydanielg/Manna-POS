import 'package:flutter/material.dart';
import '../../core/api_service.dart';
import '../../shared/theme/app_colors.dart';
import '../../shared/widgets/empty_state.dart';

class BusinessesPage extends StatefulWidget {
  const BusinessesPage({super.key});
  @override
  State<BusinessesPage> createState() => _BusinessesPageState();
}

class _BusinessesPageState extends State<BusinessesPage> {
  List<dynamic> _businesses = [];
  bool _loading = true;
  String? _error;

  @override
  void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    setState(() => _loading = true);
    try {
      final data = await ApiService.get('/businesses');
      setState(() { _businesses = data is List ? data : []; _loading = false; });
    } catch (e) { setState(() { _error = e.toString(); _loading = false; }); }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(title: const Text('Businesses')),
      body: RefreshIndicator(
        onRefresh: _load,
        child: _loading
          ? const Center(child: CircularProgressIndicator())
          : _error != null
            ? ErrorWidget2(message: _error!, onRetry: _load)
            : _businesses.isEmpty
              ? const EmptyState(icon: Icons.business_outlined, title: 'No businesses found')
              : ListView.separated(
                  padding: const EdgeInsets.all(12),
                  itemCount: _businesses.length,
                  separatorBuilder: (_, __) => const SizedBox(height: 8),
                  itemBuilder: (_, i) {
                    final b = _businesses[i];
                    final status = b['status'] ?? 'pending';
                    return Container(
                      padding: const EdgeInsets.all(14),
                      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(12)),
                      child: Row(
                        children: [
                          Container(
                            width: 42, height: 42,
                            decoration: BoxDecoration(color: AppColors.primaryLt, borderRadius: BorderRadius.circular(10)),
                            child: const Icon(Icons.store_rounded, size: 22, color: AppColors.primary),
                          ),
                          const SizedBox(width: 12),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(b['business_name'] ?? '', style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w600, color: Colors.black)),
                                Text(b['business_city'] ?? '', style: const TextStyle(fontSize: 12, color: AppColors.textSec)),
                              ],
                            ),
                          ),
                          _StatusChip(status: status),
                        ],
                      ),
                    );
                  },
                ),
      ),
    );
  }
}

class _StatusChip extends StatelessWidget {
  final String status;
  const _StatusChip({required this.status});
  @override
  Widget build(BuildContext context) {
    Color bg, fg;
    switch (status) {
      case 'active': bg = const Color(0xFFDFF7EE); fg = AppColors.primary; break;
      case 'pending': bg = const Color(0xFFFFFBEB); fg = AppColors.warning; break;
      case 'suspended': bg = const Color(0xFFFFE8ED); fg = AppColors.secondary; break;
      default: bg = const Color(0xFFF2F2F7); fg = AppColors.textSec;
    }
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
      decoration: BoxDecoration(color: bg, borderRadius: BorderRadius.circular(6)),
      child: Text(status, style: TextStyle(fontSize: 11, fontWeight: FontWeight.w600, color: fg)),
    );
  }
}
