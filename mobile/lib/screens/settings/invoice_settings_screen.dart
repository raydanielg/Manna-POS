import 'package:flutter/material.dart';
import '../../shared/theme/app_colors.dart';
import '../../widgets/glass_card.dart';
import '../../core/api_service.dart';

class InvoiceSettingsScreen extends StatefulWidget {
  const InvoiceSettingsScreen({super.key});
  @override State<InvoiceSettingsScreen> createState() => _InvoiceSettingsScreenState();
}

class _InvoiceSettingsScreenState extends State<InvoiceSettingsScreen> {
  final _prefixCtrl = TextEditingController(text: 'INV-');
  final _footerCtrl = TextEditingController(text: 'Thank you for your business!');
  final _termsCtrl = TextEditingController(text: 'Net 30');
  String _currency = 'TZS';
  int _dueDays = 30;
  bool _showLogo = true;
  bool _showTax = true;
  bool _saving = false;
  bool _loading = true;

  static const _currencies = ['TZS', 'USD', 'EUR', 'KES', 'UGX'];

  @override
  void initState() { super.initState(); _load(); }
  @override void dispose() { _prefixCtrl.dispose(); _footerCtrl.dispose(); _termsCtrl.dispose(); super.dispose(); }

  Future<void> _load() async {
    try {
      final d = await ApiService.get('/settings/invoice');
      if (mounted) {
        setState(() {
          _prefixCtrl.text = d['prefix'] ?? 'INV-';
          _footerCtrl.text = d['footer'] ?? '';
          _termsCtrl.text = d['payment_terms'] ?? 'Net 30';
          _currency = d['currency'] ?? 'TZS';
          _dueDays = d['due_days'] ?? 30;
          _showLogo = d['show_logo'] ?? true;
          _showTax = d['show_tax'] ?? true;
          _loading = false;
        });
      }
    } catch (_) { if (mounted) setState(() => _loading = false); }
  }

  Future<void> _save() async {
    setState(() => _saving = true);
    try {
      await ApiService.post('/settings/invoice', {
        'prefix': _prefixCtrl.text,
        'footer': _footerCtrl.text,
        'payment_terms': _termsCtrl.text,
        'currency': _currency,
        'due_days': _dueDays,
        'show_logo': _showLogo,
        'show_tax': _showTax,
      });
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Invoice settings saved'), backgroundColor: AppColors.success));
        Navigator.pop(context);
      }
    } catch (e) {
      if (mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Error: $e'), backgroundColor: AppColors.danger));
    }
    if (mounted) setState(() => _saving = false);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(title: const Text('Invoice Settings')),
      body: _loading
          ? const Center(child: CircularProgressIndicator())
          : SingleChildScrollView(
              padding: const EdgeInsets.all(16),
              child: GlassCard(
                child: Padding(
                  padding: const EdgeInsets.all(16),
                  child: Column(children: [
                    TextFormField(controller: _prefixCtrl, decoration: const InputDecoration(labelText: 'Invoice Prefix', prefixIcon: Icon(Icons.tag, size: 20), helperText: 'e.g. INV-')),
                    const SizedBox(height: 14),
                    TextFormField(controller: _termsCtrl, decoration: const InputDecoration(labelText: 'Default Payment Terms', prefixIcon: Icon(Icons.credit_card, size: 20))),
                    const SizedBox(height: 14),
                    TextFormField(controller: _footerCtrl, decoration: const InputDecoration(labelText: 'Invoice Footer', prefixIcon: Icon(Icons.text_fields, size: 20)), maxLines: 2),
                    const SizedBox(height: 14),
                    DropdownButtonFormField<String>(
                      value: _currency,
                      decoration: const InputDecoration(labelText: 'Default Currency', prefixIcon: Icon(Icons.monetization_on, size: 20)),
                      isExpanded: true,
                      items: _currencies.map((c) => DropdownMenuItem(value: c, child: Text(c))).toList(),
                      onChanged: (v) => setState(() => _currency = v!),
                    ),
                    const SizedBox(height: 14),
                    TextFormField(
                      decoration: const InputDecoration(labelText: 'Due Days', prefixIcon: Icon(Icons.calendar_today, size: 20)),
                      keyboardType: TextInputType.number,
                      initialValue: '$_dueDays',
                      onChanged: (v) => _dueDays = int.tryParse(v) ?? 30,
                    ),
                    const SizedBox(height: 14),
                    SwitchListTile(
                      title: const Text('Show Logo on Invoice'),
                      value: _showLogo,
                      onChanged: (v) => setState(() => _showLogo = v),
                      contentPadding: EdgeInsets.zero,
                    ),
                    SwitchListTile(
                      title: const Text('Show Tax on Invoice'),
                      value: _showTax,
                      onChanged: (v) => setState(() => _showTax = v),
                      contentPadding: EdgeInsets.zero,
                    ),
                    const SizedBox(height: 24),
                    SizedBox(
                      width: double.infinity, height: 52,
                      child: ElevatedButton(
                        onPressed: _saving ? null : _save,
                        child: _saving ? const SizedBox(width: 22, height: 22, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5)) : const Text('Save Settings', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700)),
                      ),
                    ),
                  ]),
                ),
              ),
            ),
    );
  }
}
