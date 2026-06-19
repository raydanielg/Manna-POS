import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/status_badge.dart';
import '../../widgets/search_bar_widget.dart';
import '../../widgets/shimmer_loading.dart';
import '../../widgets/empty_state.dart';
import '../../widgets/section_header.dart';
import '../../widgets/confirm_dialog.dart';
import '../../widgets/toast_helper.dart';
import '../../widgets/loading_overlay.dart';
import '../../providers/sale_provider.dart';

class ImportSalesScreen extends StatefulWidget {
  const ImportSalesScreen({super.key});
  @override State<ImportSalesScreen> createState() => _ImportSalesScreenState();
}

class _ImportSalesScreenState extends State<ImportSalesScreen> {
  String? _selectedFile;
  bool _loading = false;
  bool _imported = false;
  List<Map<String, String>> _previewData = [];
  final Map<String, String> _columnMapping = {
    'reference': 'Reference',
    'customer': 'Customer Name',
    'total': 'Total',
    'date': 'Date',
  };

  @override
  void initState() {
    super.initState();
  }

  Future<void> _pickFile() async {
    setState(() {
      _selectedFile = 'sales_import_2026.csv';
      _previewData = [
        {'reference': 'SALE-001', 'customer': 'John Doe', 'total': '150000', 'date': '2026-06-01'},
        {'reference': 'SALE-002', 'customer': 'Jane Smith', 'total': '250000', 'date': '2026-06-02'},
        {'reference': 'SALE-003', 'customer': 'Bob Johnson', 'total': '85000', 'date': '2026-06-03'},
        {'reference': 'SALE-004', 'customer': 'Alice Brown', 'total': '420000', 'date': '2026-06-04'},
        {'reference': 'SALE-005', 'customer': 'Charlie Davis', 'total': '95000', 'date': '2026-06-05'},
      ];
    });
    ToastHelper.success(context, 'File loaded: $_selectedFile');
  }

  Future<void> _import() async {
    setState(() => _loading = true);
    try {
      await Future.delayed(const Duration(seconds: 2));
      if (mounted) {
        setState(() {
          _loading = false;
          _imported = true;
        });
        ToastHelper.success(context, '${_previewData.length} sales imported successfully');
      }
    } catch (e) {
      if (mounted) {
        setState(() => _loading = false);
        ToastHelper.error(context, 'Import failed: $e');
      }
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
        title: const Text('Import Sales', style: TextStyle(fontWeight: FontWeight.w700)),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            // Step 1: File picker
            SectionHeader(title: '1. Select File'),
            const SizedBox(height: 8),
            GlassCard(
              child: InkWell(
                onTap: _pickFile,
                borderRadius: BorderRadius.circular(14),
                child: Container(
                  padding: const EdgeInsets.all(24),
                  child: Column(
                    children: [
                      Container(
                        width: 56, height: 56,
                        decoration: BoxDecoration(
                          color: theme.colorScheme.primary.withValues(alpha: 0.15),
                          borderRadius: BorderRadius.circular(16),
                        ),
                        child: Icon(Icons.upload_file, size: 28, color: theme.colorScheme.primary),
                      ),
                      const SizedBox(height: 12),
                      Text(
                        _selectedFile ?? 'Tap to select CSV or Excel file',
                        style: TextStyle(
                          fontWeight: FontWeight.w600,
                          color: _selectedFile != null ? theme.colorScheme.primary : theme.colorScheme.onSurface.withValues(alpha: 0.6),
                        ),
                      ),
                      const SizedBox(height: 4),
                      Text('Supports .csv, .xlsx, .xls',
                        style: TextStyle(fontSize: 12, color: theme.colorScheme.onSurface.withValues(alpha: 0.5))),
                      if (_selectedFile != null)
                        Padding(
                          padding: const EdgeInsets.only(top: 8),
                          child: Row(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              Icon(Icons.check_circle, size: 16, color: Colors.green),
                              const SizedBox(width: 4),
                              Text('File loaded', style: TextStyle(fontSize: 12, color: Colors.green)),
                            ],
                          ),
                        ),
                    ],
                  ),
                ),
              ),
            ),
            const SizedBox(height: 24),
            // Step 2: Column mapping
            if (_selectedFile != null) ...[
              SectionHeader(title: '2. Column Mapping'),
              const SizedBox(height: 8),
              GlassCard(
                child: Padding(
                  padding: const EdgeInsets.all(16),
                  child: Column(
                    children: _columnMapping.entries.map((entry) {
                      return Padding(
                        padding: const EdgeInsets.only(bottom: 8),
                        child: Row(
                          children: [
                            SizedBox(
                              width: 100,
                              child: Text(entry.key,
                                style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13)),
                            ),
                            const Icon(Icons.arrow_forward, size: 16, color: Colors.grey),
                            const SizedBox(width: 8),
                            Expanded(
                              child: Container(
                                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 8),
                                decoration: BoxDecoration(
                                  color: theme.colorScheme.surfaceVariant,
                                  borderRadius: BorderRadius.circular(8),
                                ),
                                child: Text(entry.value,
                                  style: TextStyle(fontSize: 13, color: theme.colorScheme.onSurface)),
                              ),
                            ),
                          ],
                        ),
                      );
                    }).toList(),
                  ),
                ),
              ),
              const SizedBox(height: 24),
              // Step 3: Preview
              SectionHeader(title: '3. Preview (${_previewData.length} rows)'),
              const SizedBox(height: 8),
              GlassCard(
                child: SingleChildScrollView(
                  scrollDirection: Axis.horizontal,
                  child: DataTable(
                    columnSpacing: 20,
                    columns: const [
                      DataColumn(label: Text('Reference', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 12))),
                      DataColumn(label: Text('Customer', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 12))),
                      DataColumn(label: Text('Total', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 12))),
                      DataColumn(label: Text('Date', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 12))),
                    ],
                    rows: _previewData.map((row) => DataRow(cells: [
                      DataCell(Text(row['reference'] ?? '', style: const TextStyle(fontSize: 12))),
                      DataCell(Text(row['customer'] ?? '', style: const TextStyle(fontSize: 12))),
                      DataCell(Text(row['total'] ?? '', style: const TextStyle(fontSize: 12))),
                      DataCell(Text(row['date'] ?? '', style: const TextStyle(fontSize: 12))),
                    ])).toList(),
                  ),
                ),
              ),
              const SizedBox(height: 24),
              // Import button
              SizedBox(
                height: 54,
                child: AnimatedButton(
                  onPressed: _loading || _imported ? null : _import,
                  loading: _loading,
                  child: Text(
                    _imported
                        ? 'Imported Successfully'
                        : _loading
                            ? 'Importing...'
                            : 'Import ${_previewData.length} Sales',
                    style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w700),
                  ),
                ),
              ),
              if (_imported)
                Padding(
                  padding: const EdgeInsets.only(top: 12),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      const Icon(Icons.check_circle, color: Colors.green, size: 20),
                      const SizedBox(width: 8),
                      Text('${_previewData.length} sales imported successfully',
                        style: const TextStyle(color: Colors.green, fontWeight: FontWeight.w600)),
                    ],
                  ),
                ),
            ],
            const SizedBox(height: 32),
          ],
        ),
      ),
    );
  }
}
