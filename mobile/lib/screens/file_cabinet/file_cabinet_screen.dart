import 'package:flutter/material.dart';
import '../../shared/theme/app_colors.dart';
import '../../shared/widgets/search_bar_widget.dart';
import '../../shared/widgets/empty_state.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/app_bottom_sheet.dart';
import '../../core/api_service.dart';

class FileCabinetScreen extends StatefulWidget {
  const FileCabinetScreen({super.key});
  @override State<FileCabinetScreen> createState() => _FileCabinetScreenState();
}

class _FileCabinetScreenState extends State<FileCabinetScreen> {
  List<dynamic> _items = [];
  bool _loading = true;
  bool _gridView = false;
  String _currentPath = '/';
  final _search = TextEditingController();

  @override void initState() { super.initState(); _load(); }
  @override void dispose() { _search.dispose(); super.dispose(); }

  Future<void> _load() async {
    setState(() => _loading = true);
    try {
      final d = await ApiService.get('/files?path=${Uri.encodeComponent(_currentPath)}');
      if (mounted) setState(() { _items = d['data'] ?? d as List? ?? []; _loading = false; });
    } catch (_) { if (mounted) setState(() => _loading = false); }
  }

  void _createFolder() {
    final ctrl = TextEditingController();
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (ctx) => Container(
        decoration: const BoxDecoration(color: AppColors.surface, borderRadius: BorderRadius.vertical(top: Radius.circular(24))),
        padding: EdgeInsets.only(bottom: MediaQuery.of(ctx).viewInsets.bottom),
        child: Padding(
          padding: const EdgeInsets.fromLTRB(24, 16, 24, 32),
          child: Column(mainAxisSize: MainAxisSize.min, children: [
            Center(child: Container(width: 40, height: 4, decoration: BoxDecoration(color: AppColors.border, borderRadius: BorderRadius.circular(4)))),
            const SizedBox(height: 20),
            const Text('Create Folder', style: TextStyle(fontSize: 19, fontWeight: FontWeight.w800)),
            const SizedBox(height: 16),
            TextFormField(controller: ctrl, decoration: const InputDecoration(labelText: 'Folder Name', prefixIcon: Icon(Icons.folder, size: 20)), autofocus: true),
            const SizedBox(height: 20),
            SizedBox(
              width: double.infinity, height: 52,
              child: ElevatedButton(
                onPressed: () async {
                  if (ctrl.text.trim().isEmpty) return;
                  try {
                    await ApiService.post('/files/folder', {'name': ctrl.text.trim(), 'path': _currentPath});
                    if (ctx.mounted) Navigator.pop(ctx);
                    _load();
                  } catch (e) {
                    if (ctx.mounted) ScaffoldMessenger.of(ctx).showSnackBar(SnackBar(content: Text('Error: $e'), backgroundColor: AppColors.danger));
                  }
                },
                child: const Text('Create'),
              ),
            ),
          ]),
        ),
      ),
    );
  }

  IconData _iconForFile(String name) {
    final ext = name.split('.').last.toLowerCase();
    switch (ext) {
      case 'pdf': return Icons.picture_as_pdf;
      case 'doc': case 'docx': return Icons.description;
      case 'xls': case 'xlsx': return Icons.table_chart;
      case 'jpg': case 'jpeg': case 'png': case 'gif': return Icons.image;
      default: return Icons.insert_drive_file;
    }
  }

  Color _colorForFile(String name) {
    final ext = name.split('.').last.toLowerCase();
    switch (ext) {
      case 'pdf': return AppColors.danger;
      case 'doc': case 'docx': return AppColors.primary;
      case 'xls': case 'xlsx': return AppColors.success;
      case 'jpg': case 'jpeg': case 'png': case 'gif': return AppColors.purple;
      default: return AppColors.textSec;
    }
  }

  String _sizeString(dynamic size) {
    if (size == null) return '';
    final s = size is int ? size.toDouble() : double.tryParse(size.toString()) ?? 0;
    if (s < 1024) return '${s.toStringAsFixed(0)} B';
    if (s < 1024 * 1024) return '${(s / 1024).toStringAsFixed(1)} KB';
    return '${(s / (1024 * 1024)).toStringAsFixed(1)} MB';
  }

  void _showOptions(Map<String, dynamic> item) {
    showModalBottomSheet(
      context: context,
      backgroundColor: Colors.transparent,
      builder: (ctx) => Container(
        decoration: const BoxDecoration(color: AppColors.surface, borderRadius: BorderRadius.vertical(top: Radius.circular(24))),
        padding: const EdgeInsets.fromLTRB(24, 16, 24, 32),
        child: Column(mainAxisSize: MainAxisSize.min, children: [
          Center(child: Container(width: 40, height: 4, decoration: BoxDecoration(color: AppColors.border, borderRadius: BorderRadius.circular(4)))),
          const SizedBox(height: 20),
          Text(item['name'] ?? '', style: const TextStyle(fontSize: 17, fontWeight: FontWeight.w700)),
          const SizedBox(height: 16),
          ListTile(leading: const Icon(Icons.edit), title: const Text('Rename'), onTap: () { Navigator.pop(ctx); }),
          ListTile(leading: const Icon(Icons.share, color: AppColors.primary), title: const Text('Share'), onTap: () { Navigator.pop(ctx); }),
          ListTile(leading: const Icon(Icons.delete, color: AppColors.danger), title: const Text('Delete', style: TextStyle(color: AppColors.danger)), onTap: () { Navigator.pop(ctx); _deleteItem(item); }),
        ]),
      ),
    );
  }

  Future<void> _deleteItem(Map<String, dynamic> item) async {
    final ok = await showDialog<bool>(context: context, builder: (_) => AlertDialog(
      title: const Text('Delete'),
      content: Text('Delete ${item['name']}?'),
      actions: [
        TextButton(onPressed: () => Navigator.pop(context, false), child: const Text('Cancel')),
        ElevatedButton(onPressed: () => Navigator.pop(context, true), style: ElevatedButton.styleFrom(backgroundColor: AppColors.danger), child: const Text('Delete')),
      ],
    ));
    if (ok == true) {
      try {
        await ApiService.delete('/files/${item['id']}');
        _load();
      } catch (_) {}
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(
        title: const Text('File Cabinet'),
        actions: [
          IconButton(
            icon: Icon(_gridView ? Icons.view_list : Icons.grid_view),
            onPressed: () => setState(() => _gridView = !_gridView),
          ),
        ],
      ),
      body: Column(children: [
        Padding(
          padding: const EdgeInsets.fromLTRB(16, 8, 16, 0),
          child: SearchBarWidget(hint: 'Search files...', onChanged: (v) {}),
        ),
        Padding(
          padding: const EdgeInsets.fromLTRB(16, 8, 16, 4),
          child: Row(children: [
            GestureDetector(onTap: () { _currentPath = '/'; _load(); }, child: const Text('Root', style: TextStyle(color: AppColors.primary, fontWeight: FontWeight.w600, fontSize: 13))),
            if (_currentPath != '/') ...[
              const Text(' / ', style: TextStyle(color: AppColors.textSec)),
              Text(_currentPath.replaceAll('/', ' > ').replaceAll('> ', ''), style: const TextStyle(color: AppColors.textSec, fontSize: 13)),
            ],
          ]),
        ),
        Expanded(
          child: _loading
              ? const Center(child: CircularProgressIndicator())
              : _items.isEmpty
                  ? const EmptyState(icon: Icons.folder_open, title: 'Empty', subtitle: 'Tap + to upload or create folder')
                  : _gridView
                      ? GridView.builder(
                          padding: const EdgeInsets.all(16),
                          gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(crossAxisCount: 3, crossAxisSpacing: 10, mainAxisSpacing: 10, childAspectRatio: 0.9),
                          itemCount: _items.length,
                          itemBuilder: (context, i) {
                            final item = _items[i];
                            final isFolder = item['type'] == 'folder';
                            return GestureDetector(
                              onTap: isFolder ? () { _currentPath = '${_currentPath}${item['name']}/'; _load(); } : () => _showOptions(item),
                              onLongPress: () => _showOptions(item),
                              child: GlassCard(
                                child: Column(mainAxisAlignment: MainAxisAlignment.center, children: [
                                  Icon(isFolder ? Icons.folder : _iconForFile(item['name'] ?? ''), size: 36, color: isFolder ? AppColors.warning : _colorForFile(item['name'] ?? '')),
                                  const SizedBox(height: 6),
                                  Text(item['name'] ?? '', maxLines: 2, overflow: TextOverflow.ellipsis, textAlign: TextAlign.center, style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w600)),
                                  Text(isFolder ? '${item['file_count'] ?? 0} files' : _sizeString(item['size']), style: const TextStyle(color: AppColors.textSec, fontSize: 10)),
                                ]),
                              ),
                            );
                          },
                        )
                      : ListView.builder(
                          padding: const EdgeInsets.all(16),
                          itemCount: _items.length,
                          itemBuilder: (context, i) {
                            final item = _items[i];
                            final isFolder = item['type'] == 'folder';
                            return Padding(
                              padding: const EdgeInsets.only(bottom: 8),
                              child: GlassCard(
                                child: ListTile(
                                  leading: Icon(isFolder ? Icons.folder : _iconForFile(item['name'] ?? ''), color: isFolder ? AppColors.warning : _colorForFile(item['name'] ?? ''), size: 28),
                                  title: Text(item['name'] ?? '', style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 14)),
                                  subtitle: Text(isFolder ? '${item['file_count'] ?? 0} files' : _sizeString(item['size'])),
                                  trailing: Row(mainAxisSize: MainAxisSize.min, children: [
                                    if (!isFolder) IconButton(icon: const Icon(Icons.download, size: 20, color: AppColors.primary), onPressed: () => ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Download placeholder')))),
                                    IconButton(icon: const Icon(Icons.more_vert, size: 20), onPressed: () => _showOptions(item)),
                                  ]),
                                  onTap: isFolder ? () { _currentPath = '${_currentPath}${item['name']}/'; _load(); } : () => _showOptions(item),
                                  onLongPress: () => _showOptions(item),
                                ),
                              ),
                            );
                          },
                        ),
        ),
      ]),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () => ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Upload file placeholder'))),
        backgroundColor: AppColors.primary,
        icon: const Icon(Icons.upload_file, color: Colors.white),
        label: const Text('Upload', style: TextStyle(color: Colors.white)),
      ),
    );
  }
}
