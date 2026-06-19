import 'package:flutter/material.dart';
import '../../shared/theme/app_colors.dart';
import '../../shared/widgets/app_card.dart';
import '../../widgets/glass_card.dart';
import '../../core/api_service.dart';

class ImportContactsScreen extends StatefulWidget {
  const ImportContactsScreen({super.key});
  @override State<ImportContactsScreen> createState() => _ImportContactsScreenState();
}

class _ImportContactsScreenState extends State<ImportContactsScreen> {
  String _fileType = 'csv';
  List<Map<String, String>> _previewData = [];
  bool _importing = false;
  double _progress = 0;
  int _importedCount = 0;
  int _errorCount = 0;
  bool _complete = false;

  final _columnMap = <String, String>{
    'Column A': 'name',
    'Column B': 'phone',
    'Column C': 'email',
  };

  static const _targetFields = ['name', 'phone', 'email', 'address', 'city'];
  static const _fieldLabels = ['Name', 'Phone', 'Email', 'Address', 'City'];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(title: const Text('Import Contacts')),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(children: [
          GlassCard(
            child: Padding(
              padding: const EdgeInsets.all(16),
              child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                const Text('File Type', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 16)),
                const SizedBox(height: 12),
                Row(children: [
                  _typeChip('csv', 'CSV'),
                  const SizedBox(width: 10),
                  _typeChip('excel', 'Excel'),
                ]),
                const SizedBox(height: 20),
                SizedBox(
                  width: double.infinity,
                  child: OutlinedButton.icon(
                    onPressed: () => ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('File picker placeholder'))),
                    icon: const Icon(Icons.file_upload_outlined),
                    label: const Text('Choose File'),
                    style: OutlinedButton.styleFrom(padding: const EdgeInsets.symmetric(vertical: 16)),
                  ),
                ),
              ]),
            ),
          ),
          const SizedBox(height: 16),
          GlassCard(
            child: Padding(
              padding: const EdgeInsets.all(16),
              child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                const Text('Column Mapping', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 16)),
                const SizedBox(height: 4),
                const Text('Match file columns to contact fields', style: TextStyle(color: AppColors.textSec, fontSize: 12)),
                const SizedBox(height: 12),
                ..._targetFields.asMap().entries.map((entry) {
                  final i = entry.key;
                  final field = entry.value;
                  return Padding(
                    padding: const EdgeInsets.only(bottom: 10),
                    child: Row(children: [
                      SizedBox(width: 80, child: Text('${_fieldLabels[i]}:', style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13))),
                      Expanded(
                        child: DropdownButtonFormField<String>(
                          value: _columnMap.entries.firstWhere(
                            (e) => e.value == field,
                            orElse: () => MapEntry('Skip', ''),
                          ).key,
                          isExpanded: true,
                          decoration: const InputDecoration(isDense: true, contentPadding: EdgeInsets.symmetric(horizontal: 12, vertical: 10)),
                          items: ['Skip', 'Column A', 'Column B', 'Column C', 'Column D', 'Column E'].map((c) => DropdownMenuItem(value: c, child: Text(c, style: const TextStyle(fontSize: 13)))).toList(),
                          onChanged: (v) {
                            if (v == null) return;
                            setState(() {
                              _columnMap.removeWhere((k, v) => v == field);
                              _columnMap[v] = field;
                            });
                          },
                        ),
                      ),
                    ]),
                  );
                }),
              ]),
            ),
          ),
          if (_previewData.isNotEmpty) ...[
            const SizedBox(height: 16),
            GlassCard(
              child: Padding(
                padding: const EdgeInsets.all(16),
                child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                  const Text('Preview', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 16)),
                  const SizedBox(height: 12),
                  SingleChildScrollView(
                    scrollDirection: Axis.horizontal,
                    child: DataTable(
                      columnSpacing: 16,
                      columns: const [
                        DataColumn(label: Text('Name', style: TextStyle(fontWeight: FontWeight.w700))),
                        DataColumn(label: Text('Phone', style: TextStyle(fontWeight: FontWeight.w700))),
                        DataColumn(label: Text('Email', style: TextStyle(fontWeight: FontWeight.w700))),
                      ],
                      rows: _previewData.take(5).map((row) => DataRow(cells: [
                        DataCell(Text(row['name'] ?? '')),
                        DataCell(Text(row['phone'] ?? '')),
                        DataCell(Text(row['email'] ?? '')),
                      ])).toList(),
                    ),
                  ),
                  if (_previewData.length > 5)
                    Padding(
                      padding: const EdgeInsets.only(top: 8),
                      child: Text('... and ${_previewData.length - 5} more rows', style: const TextStyle(color: AppColors.textSec, fontSize: 12)),
                    ),
                ]),
              ),
            ),
          ],
          const SizedBox(height: 20),
          if (_importing) ...[
            GlassCard(
              child: Padding(
                padding: const EdgeInsets.all(16),
                child: Column(children: [
                  LinearProgressIndicator(value: _progress, backgroundColor: AppColors.border, color: AppColors.primary, minHeight: 8),
                  const SizedBox(height: 12),
                  Text('Importing... ${(_progress * 100).toStringAsFixed(0)}%', style: const TextStyle(fontWeight: FontWeight.w600)),
                ]),
              ),
            ),
          ] else if (_complete) ...[
            GlassCard(
              child: Padding(
                padding: const EdgeInsets.all(16),
                child: Column(children: [
                  const Icon(Icons.check_circle, color: AppColors.success, size: 48),
                  const SizedBox(height: 12),
                  Text('Import Complete', style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 18)),
                  const SizedBox(height: 8),
                  Text('$_importedCount contacts imported', style: const TextStyle(color: AppColors.success, fontWeight: FontWeight.w600)),
                  if (_errorCount > 0) Text('$_errorCount errors', style: const TextStyle(color: AppColors.danger)),
                ]),
              ),
            ),
          ] else ...[
            SizedBox(
              width: double.infinity, height: 52,
              child: ElevatedButton.icon(
                onPressed: _importing
                    ? null
                    : () {
                        setState(() {
                          _importing = true;
                          _progress = 0;
                          _complete = false;
                        });
                        Future.delayed(const Duration(seconds: 2), () {
                          if (mounted) setState(() {
                            _importing = false;
                            _complete = true;
                            _importedCount = 42;
                            _errorCount = 3;
                            _progress = 1;
                          });
                        });
                      },
                icon: const Icon(Icons.upload),
                label: const Text('Import Contacts', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700)),
              ),
            ),
          ],
          const SizedBox(height: 30),
        ]),
      ),
    );
  }

  Widget _typeChip(String val, String label) {
    final sel = _fileType == val;
    return GestureDetector(
      onTap: () => setState(() => _fileType = val),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 10),
        decoration: BoxDecoration(
          color: sel ? AppColors.primary : Colors.white,
          borderRadius: BorderRadius.circular(10),
          border: Border.all(color: sel ? AppColors.primary : AppColors.border),
        ),
        child: Text(label, style: TextStyle(color: sel ? Colors.white : AppColors.textSec, fontWeight: FontWeight.w600)),
      ),
    );
  }
}
