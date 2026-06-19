import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../../core/api_service.dart';
import '../../shared/theme/app_colors.dart';
import '../../shared/utils/formatters.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/shimmer_loading.dart';
import '../../widgets/toast_helper.dart';
import '../../widgets/confirm_dialog.dart';

class SystemHealthScreen extends StatefulWidget {
  const SystemHealthScreen({super.key});
  @override State<SystemHealthScreen> createState() => _SystemHealthScreenState();
}

class _SystemHealthScreenState extends State<SystemHealthScreen> {
  Map<String, dynamic>? _health;
  bool _loading = true;
  String? _error;
  bool _maintenanceMode = false;

  @override
  void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    setState(() { _loading = true; _error = null; });
    try {
      final data = await ApiService.get('/api/admin/system-health');
      if (data is Map) setState({ _health = Map<String, dynamic>.from(data), _loading = false, _maintenanceMode = data['maintenance_mode'] == true });
    } catch (e) { setState(() { _error = e.toString(); _loading = false; }); }
  }

  Future<void> _clearCache() async {
    try {
      await ApiService.post('/api/admin/settings/clear-cache', {});
      if (mounted) ToastHelper.show(context, message: 'Cache cleared successfully');
    } catch (e) { if (mounted) ToastHelper.show(context, message: 'Failed to clear cache', error: true); }
  }

  Future<void> _runBackup() async {
    try {
      await ApiService.post('/api/admin/settings/backup', {});
      if (mounted) ToastHelper.show(context, message: 'Backup started');
    } catch (e) { if (mounted) ToastHelper.show(context, message: 'Failed to start backup', error: true); }
  }

  Future<void> _toggleMaintenance() async {
    final newVal = !_maintenanceMode;
    final confirmed = await ConfirmDialog.show(context,
      title: 'Maintenance Mode',
      message: newVal ? 'Enable maintenance mode? Users will be unable to access the system.' : 'Disable maintenance mode?',
    );
    if (confirmed != true) return;
    try {
      await ApiService.post('/api/admin/settings/maintenance', {'enabled': newVal});
      setState(() => _maintenanceMode = newVal);
      if (mounted) ToastHelper.show(context, message: newVal ? 'Maintenance mode enabled' : 'Maintenance mode disabled');
    } catch (e) { if (mounted) ToastHelper.show(context, message: 'Failed to update', error: true); }
  }

  Color _statusColor(String? status) {
    switch (status?.toLowerCase()) {
      case 'healthy': case 'ok': case 'connected': return AppColors.success;
      case 'warning': case 'degraded': return AppColors.warning;
      case 'critical': case 'error': case 'disconnected': return AppColors.danger;
      default: return AppColors.textSec;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        title: const Text('System Health'),
        actions: [IconButton(icon: const Icon(Icons.refresh), onPressed: _load)],
      ),
      body: _loading
          ? const ShimmerLoading(itemCount: 8)
          : _error != null
              ? Center(child: Column(mainAxisSize: MainAxisSize.min, children: [
                  const Icon(Icons.error_outline, size: 48, color: AppColors.error),
                  const SizedBox(height: 12),
                  Text(_error!, style: const TextStyle(color: AppColors.textSec)),
                  const SizedBox(height: 16),
                  ElevatedButton(onPressed: _load, child: const Text('Retry')),
                ]))
              : RefreshIndicator(
                  onRefresh: _load,
                  child: SingleChildScrollView(
                    padding: const EdgeInsets.fromLTRB(16, 8, 16, 100),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        _buildHealthStatus(),
                        const SizedBox(height: 16),
                        _buildServerInfo(),
                        const SizedBox(height: 16),
                        _buildErrorLogs(),
                        const SizedBox(height: 16),
                        _buildQuickActions(),
                      ],
                    ),
                  ),
                ),
    );
  }

  Widget _buildHealthStatus() {
    final services = _health?['services'] is Map ? _health!['services'] as Map<String, dynamic> : <String, dynamic>{};
    if (services.isEmpty) return const SizedBox.shrink();
    return GlassCard(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text('Service Status', style: TextStyle(fontSize: 15, fontWeight: FontWeight.w700)),
          const SizedBox(height: 12),
          ...services.entries.map((entry) {
            final status = entry.value?.toString() ?? 'unknown';
            final color = _statusColor(status);
            return Padding(
              padding: const EdgeInsets.only(bottom: 10),
              child: Row(
                children: [
                  Container(width: 10, height: 10, decoration: BoxDecoration(color: color, shape: BoxShape.circle, boxShadow: [
                    BoxShadow(color: color.withValues(alpha: 0.4), blurRadius: 6),
                  ])),
                  const SizedBox(width: 12),
                  Expanded(child: Text(_serviceLabel(entry.key), style: const TextStyle(fontWeight: FontWeight.w500, fontSize: 14))),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 3),
                    decoration: BoxDecoration(color: color.withValues(alpha: 0.12), borderRadius: BorderRadius.circular(20)),
                    child: Text(status, style: TextStyle(color: color, fontSize: 11, fontWeight: FontWeight.w700)),
                  ),
                ],
              ),
            );
          }),
          const Divider(height: 20),
          _usageRow('Storage', (_health?['storage_usage'] ?? 0).toDouble(), 100),
          const SizedBox(height: 8),
          _usageRow('Memory', (_health?['memory_usage'] ?? 0).toDouble(), 100),
          if (_health?['uptime'] != null) ...[
            const SizedBox(height: 12),
            Row(children: [
              const Icon(Icons.timer_outlined, size: 16, color: AppColors.textSec),
              const SizedBox(width: 8),
              Text('Uptime: ${_health!['uptime']}', style: const TextStyle(color: AppColors.textSec, fontSize: 13)),
            ]),
          ],
        ],
      ),
    );
  }

  String _serviceLabel(String key) {
    switch (key) {
      case 'database': return 'Database';
      case 'cache': return 'Cache (Redis)';
      case 'queue': return 'Queue';
      case 'mail': return 'Mail Service';
      case 'sms': return 'SMS Service';
      default: return key;
    }
  }

  Widget _usageRow(String label, double value, double max) {
    final pct = (value / max * 100).clamp(0, 100);
    final color = pct > 80 ? AppColors.danger : pct > 60 ? AppColors.warning : AppColors.success;
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
          Text(label, style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
          Text('${pct.toStringAsFixed(0)}%', style: TextStyle(fontWeight: FontWeight.w600, fontSize: 12, color: color)),
        ]),
        const SizedBox(height: 4),
        ClipRRect(borderRadius: BorderRadius.circular(4), child: LinearProgressIndicator(
          value: pct / 100,
          minHeight: 6,
          backgroundColor: AppColors.background,
          valueColor: AlwaysStoppedAnimation(color),
        )),
      ],
    );
  }

  Widget _buildServerInfo() {
    final server = _health?['server'] is Map ? _health!['server'] as Map<String, dynamic> : <String, dynamic>{};
    if (server.isEmpty) return const SizedBox.shrink();
    return GlassCard(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text('Server Information', style: TextStyle(fontSize: 15, fontWeight: FontWeight.w700)),
          const SizedBox(height: 12),
          _serverRow('PHP Version', server['php_version'] ?? '-'),
          _serverRow('Laravel Version', server['laravel_version'] ?? '-'),
          _serverRow('Server Software', server['server_software'] ?? '-'),
          _serverRow('Database', server['database'] ?? '-'),
          _serverRow('Environment', server['environment'] ?? '-'),
        ],
      ),
    );
  }

  Widget _serverRow(String label, String value) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 8),
      child: Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
        Text(label, style: const TextStyle(color: AppColors.textSec, fontSize: 13)),
        Text(value, style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13)),
      ]),
    );
  }

  Widget _buildErrorLogs() {
    final logs = _health?['recent_errors'] is List ? _health!['recent_errors'] as List : [];
    return GlassCard(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
            const Text('Recent Error Logs', style: TextStyle(fontSize: 15, fontWeight: FontWeight.w700)),
            Text('${logs.length} entries', style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
          ]),
          const SizedBox(height: 12),
          if (logs.isEmpty)
            const Padding(padding: EdgeInsets.all(8), child: Row(children: [
              Icon(Icons.check_circle, color: AppColors.success, size: 18),
              SizedBox(width: 8),
              Text('No recent errors', style: TextStyle(color: AppColors.textSec)),
            ]))
          else
            ...List.generate(logs.length > 10 ? 10 : logs.length, (i) {
              final log = logs[i];
              final isError = (log['type'] ?? '').toString().toLowerCase().contains('error');
              return Padding(
                padding: EdgeInsets.only(bottom: i < (logs.length > 10 ? 10 : logs.length) - 1 ? 8 : 0),
                child: Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Icon(isError ? Icons.error : Icons.warning_amber_rounded, size: 16, color: isError ? AppColors.danger : AppColors.warning),
                    const SizedBox(width: 8),
                    Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                      Text(log['message'] ?? '', style: const TextStyle(fontSize: 12), maxLines: 2, overflow: TextOverflow.ellipsis),
                      if (log['created_at'] != null) Text(fmtDate(log['created_at']), style: const TextStyle(color: AppColors.textSec, fontSize: 10)),
                    ])),
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                      decoration: BoxDecoration(color: (log['type'] ?? '').toString().contains('error') ? AppColors.dangerLt : AppColors.warningLt, borderRadius: BorderRadius.circular(4)),
                      child: Text(log['type'] ?? 'info', style: TextStyle(fontSize: 9, fontWeight: FontWeight.w600, color: isError ? AppColors.danger : AppColors.warning)),
                    ),
                  ],
                ),
              );
            }),
        ],
      ),
    );
  }

  Widget _buildQuickActions() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Padding(padding: EdgeInsets.only(left: 4, bottom: 8), child: Text('Quick Actions', style: TextStyle(fontSize: 13, fontWeight: FontWeight.w700, color: AppColors.textSec))),
        Row(
          children: [
            Expanded(child: _actionButton(Icons.cache_rounded, 'Clear Cache', AppColors.warning, _clearCache)),
            const SizedBox(width: 12),
            Expanded(child: _actionButton(Icons.backup_rounded, 'Run Backup', AppColors.primary, _runBackup)),
            const SizedBox(width: 12),
            Expanded(child: _actionButton(
              _maintenanceMode ? Icons.visibility_rounded : Icons.engineering_rounded,
              _maintenanceMode ? 'Disable Maint.' : 'Maintenance',
              _maintenanceMode ? AppColors.success : AppColors.danger,
              _toggleMaintenance,
            )),
          ],
        ),
      ],
    );
  }

  Widget _actionButton(IconData icon, String label, Color color, VoidCallback onTap) {
    return GestureDetector(
      onTap: onTap,
      child: GlassCard(
        padding: const EdgeInsets.symmetric(vertical: 16, horizontal: 8),
        child: Column(
          children: [
            Container(
              width: 36, height: 36,
              decoration: BoxDecoration(color: color.withValues(alpha: 0.12), borderRadius: BorderRadius.circular(10)),
              child: Icon(icon, color: color, size: 18),
            ),
            const SizedBox(height: 6),
            Text(label, style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w600, color: AppColors.textPri), textAlign: TextAlign.center),
          ],
        ),
      ),
    );
  }
}
