import 'package:flutter/material.dart';
import '../../shared/theme/app_colors.dart';
import '../../widgets/glass_card.dart';
import '../../core/api_service.dart';

class BusinessSettingsScreen extends StatefulWidget {
  const BusinessSettingsScreen({super.key});
  @override State<BusinessSettingsScreen> createState() => _BusinessSettingsScreenState();
}

class _BusinessSettingsScreenState extends State<BusinessSettingsScreen> {
  final _nameCtrl = TextEditingController();
  final _addressCtrl = TextEditingController();
  final _cityCtrl = TextEditingController();
  final _countryCtrl = TextEditingController(text: 'Tanzania');
  final _phoneCtrl = TextEditingController();
  final _emailCtrl = TextEditingController();
  final _taxIdCtrl = TextEditingController();
  String _currency = 'TZS';
  String _language = 'English';
  String _timezone = 'Africa/Dar_es_Salaam';
  bool _saving = false;
  bool _loading = true;

  static const _currencies = ['TZS', 'USD', 'EUR', 'KES', 'UGX', 'GBP'];
  static const _languages = ['English', 'Swahili', 'French'];
  static const _timezones = ['Africa/Dar_es_Salaam', 'Africa/Nairobi', 'Africa/Kampala', 'Africa/Lagos', 'UTC'];

  @override
  void initState() { super.initState(); _load(); }
  @override void dispose() {
    for (final c in [_nameCtrl, _addressCtrl, _cityCtrl, _countryCtrl, _phoneCtrl, _emailCtrl, _taxIdCtrl]) c.dispose();
    super.dispose();
  }

  Future<void> _load() async {
    try {
      final d = await ApiService.get('/settings/business');
      if (mounted) {
        setState(() {
          _nameCtrl.text = d['business_name'] ?? '';
          _addressCtrl.text = d['address'] ?? '';
          _cityCtrl.text = d['city'] ?? '';
          _countryCtrl.text = d['country'] ?? 'Tanzania';
          _phoneCtrl.text = d['phone'] ?? '';
          _emailCtrl.text = d['email'] ?? '';
          _taxIdCtrl.text = d['tax_id'] ?? '';
          _currency = d['currency'] ?? 'TZS';
          _language = d['language'] ?? 'English';
          _timezone = d['timezone'] ?? 'Africa/Dar_es_Salaam';
          _loading = false;
        });
      }
    } catch (_) { if (mounted) setState(() => _loading = false); }
  }

  Future<void> _save() async {
    setState(() => _saving = true);
    try {
      await ApiService.post('/settings/business', {
        'business_name': _nameCtrl.text,
        'address': _addressCtrl.text,
        'city': _cityCtrl.text,
        'country': _countryCtrl.text,
        'phone': _phoneCtrl.text,
        'email': _emailCtrl.text,
        'currency': _currency,
        'language': _language,
        'timezone': _timezone,
        'tax_id': _taxIdCtrl.text,
      });
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Settings saved'), backgroundColor: AppColors.success));
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
      appBar: AppBar(title: const Text('Business Settings')),
      body: _loading
          ? const Center(child: CircularProgressIndicator())
          : SingleChildScrollView(
              padding: const EdgeInsets.all(16),
              child: Column(children: [
                GlassCard(
                  child: Padding(
                    padding: const EdgeInsets.all(16),
                    child: Column(children: [
                      const SizedBox(height: 20),
                      GestureDetector(
                        onTap: () => ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Logo upload placeholder'))),
                        child: Container(
                          width: 80, height: 80,
                          decoration: BoxDecoration(color: AppColors.primaryLt, shape: BoxShape.circle, border: Border.all(color: AppColors.primary, width: 2)),
                          child: const Icon(Icons.business, color: AppColors.primary, size: 36),
                        ),
                      ),
                      const SizedBox(height: 8),
                      TextButton(onPressed: () => ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Logo upload placeholder'))), child: const Text('Upload Logo')),
                      const SizedBox(height: 20),
                      _field('Business Name', _nameCtrl, Icons.store),
                      const SizedBox(height: 14),
                      _field('Address', _addressCtrl, Icons.location_on),
                      const SizedBox(height: 14),
                      Row(children: [
                        Expanded(child: _field('City', _cityCtrl, Icons.location_city)),
                        const SizedBox(width: 12),
                        Expanded(child: _field('Country', _countryCtrl, Icons.flag)),
                      ]),
                      const SizedBox(height: 14),
                      _field('Phone', _phoneCtrl, Icons.phone, TextInputType.phone),
                      const SizedBox(height: 14),
                      _field('Email', _emailCtrl, Icons.email, TextInputType.emailAddress),
                      const SizedBox(height: 14),
                      _dropdown('Currency', _currency, _currencies, (v) => setState(() => _currency = v!)),
                      const SizedBox(height: 14),
                      _dropdown('Language', _language, _languages, (v) => setState(() => _language = v!)),
                      const SizedBox(height: 14),
                      _dropdown('Timezone', _timezone, _timezones, (v) => setState(() => _timezone = v!)),
                      const SizedBox(height: 14),
                      _field('Tax ID / VAT Number', _taxIdCtrl, Icons.badge),
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
                const SizedBox(height: 30),
              ]),
            ),
    );
  }

  Widget _field(String label, TextEditingController ctrl, IconData icon, [TextInputType? keyboardType]) {
    return TextFormField(
      controller: ctrl,
      keyboardType: keyboardType,
      decoration: InputDecoration(labelText: label, prefixIcon: Icon(icon, size: 20)),
    );
  }

  Widget _dropdown(String label, String value, List<String> items, ValueChanged<String?> onChanged) {
    return DropdownButtonFormField<String>(
      value: value,
      decoration: InputDecoration(labelText: label),
      isExpanded: true,
      items: items.map((i) => DropdownMenuItem(value: i, child: Text(i))).toList(),
      onChanged: onChanged,
    );
  }
}
