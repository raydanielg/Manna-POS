import 'package:flutter/material.dart';
import '../../core/api_service.dart';
import '../../shared/theme/app_colors.dart';
import '../../shared/widgets/empty_state.dart';

class UsersPage extends StatefulWidget {
  const UsersPage({super.key});
  @override
  State<UsersPage> createState() => _UsersPageState();
}

class _UsersPageState extends State<UsersPage> {
  List<dynamic> _users = [];
  bool _loading = true;
  String? _error;

  @override
  void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    setState(() => _loading = true);
    try {
      final data = await ApiService.get('/users');
      setState(() { _users = data is List ? data : []; _loading = false; });
    } catch (e) { setState(() { _error = e.toString(); _loading = false; }); }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(title: const Text('Users')),
      body: RefreshIndicator(
        onRefresh: _load,
        child: _loading
          ? const Center(child: CircularProgressIndicator())
          : _error != null
            ? ErrorWidget2(message: _error!, onRetry: _load)
            : _users.isEmpty
              ? const EmptyState(icon: Icons.people_outline, title: 'No users found')
              : ListView.separated(
                  padding: const EdgeInsets.all(12),
                  itemCount: _users.length,
                  separatorBuilder: (_, __) => const SizedBox(height: 8),
                  itemBuilder: (_, i) {
                    final u = _users[i];
                    return Container(
                      padding: const EdgeInsets.all(14),
                      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(12)),
                      child: Row(
                        children: [
                          Container(
                            width: 40, height: 40,
                            decoration: BoxDecoration(color: AppColors.primaryLt, borderRadius: BorderRadius.circular(10)),
                            child: Center(child: Text(
                              (u['name'] ?? 'U').toString().split(' ').map((w) => w[0]).take(2).join().toUpperCase(),
                              style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w700, color: AppColors.primary),
                            )),
                          ),
                          const SizedBox(width: 12),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(u['name'] ?? '', style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w600, color: Colors.black)),
                                Text(u['email'] ?? '', style: const TextStyle(fontSize: 12, color: AppColors.textSec)),
                              ],
                            ),
                          ),
                          Container(
                            padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                            decoration: BoxDecoration(
                              color: u['role'] == 'admin' ? AppColors.orange.withValues(alpha: 0.1) : AppColors.primaryLt,
                              borderRadius: BorderRadius.circular(6),
                            ),
                            child: Text(u['role'] ?? 'user', style: TextStyle(
                              fontSize: 11, fontWeight: FontWeight.w600,
                              color: u['role'] == 'admin' ? AppColors.orange : AppColors.primary,
                            )),
                          ),
                        ],
                      ),
                    );
                  },
                ),
      ),
    );
  }
}
