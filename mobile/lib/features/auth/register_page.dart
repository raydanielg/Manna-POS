import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:provider/provider.dart';
import '../../core/auth_provider.dart';
import '../../core/api_service.dart';
import '../../shared/theme/app_colors.dart';

class RegisterPage extends StatefulWidget {
  const RegisterPage({super.key});
  @override
  State<RegisterPage> createState() => _RegisterPageState();
}

class _RegisterPageState extends State<RegisterPage> {
  final PageController _ctrl = PageController();
  int _step = 0;
  String? _error;
  bool _loading = false;

  final _nameCtrl = TextEditingController();
  final _emailCtrl = TextEditingController();
  final _phoneCtrl = TextEditingController();
  final _passCtrl = TextEditingController();
  final _confirmCtrl = TextEditingController();
  final _businessCtrl = TextEditingController();
  final _cityCtrl = TextEditingController();
  final _addressCtrl = TextEditingController();
  final _taxPctCtrl = TextEditingController(text: '18');

  String _currency = 'TZS';
  String? _businessType;
  bool _obscure = true;
  bool _obscure2 = true;

  @override
  void dispose() {
    _ctrl.dispose(); _nameCtrl.dispose(); _emailCtrl.dispose(); _phoneCtrl.dispose();
    _passCtrl.dispose(); _confirmCtrl.dispose(); _businessCtrl.dispose();
    _cityCtrl.dispose(); _addressCtrl.dispose(); _taxPctCtrl.dispose();
    super.dispose();
  }

  Future<void> _register() async {
    if (_passCtrl.text != _confirmCtrl.text) {
      setState(() => _error = 'Passwords do not match');
      return;
    }
    setState(() { _loading = true; _error = null; });
    try {
      await context.read<AuthProvider>().register({
        'name': _nameCtrl.text.trim(),
        'email': _emailCtrl.text.trim(),
        'password': _passCtrl.text,
        'phone': _phoneCtrl.text.trim(),
        'business_name': _businessCtrl.text.trim(),
        'business_type': _businessType,
        'business_city': _cityCtrl.text.trim(),
        'business_address': _addressCtrl.text.trim(),
        'business_country': 'Tanzania',
        'currency': _currency,
        'tax_percentage': double.tryParse(_taxPctCtrl.text) ?? 18,
        'fiscal_year_start': 'January',
      });
      if (!mounted) return;
      context.go('/home');
    } on ApiException catch (e) {
      setState(() => _error = e.message);
    } catch (e) {
      setState(() => _error = 'Connection error. Check your network.');
    } finally { setState(() => _loading = false); }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(title: const Text('Create Account')),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(24),
        child: Column(
          children: [
            _buildStepIndicator(),
            const SizedBox(height: 24),
            if (_error != null)
              Container(
                width: double.infinity, margin: const EdgeInsets.only(bottom: 16),
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(color: AppColors.error.withValues(alpha: 0.1), borderRadius: BorderRadius.circular(12)),
                child: Text(_error!, style: const TextStyle(color: AppColors.error, fontSize: 13)),
              ),
            if (_step == 0) _buildStep1(),
            if (_step == 1) _buildStep2(),
            if (_step == 2) _buildStep3(),
            const SizedBox(height: 24),
            Row(
              children: [
                if (_step > 0)
                  Expanded(
                    child: OutlinedButton(
                      onPressed: () => _ctrl.previousPage(duration: const Duration(milliseconds: 300), curve: Curves.easeInOut),
                      child: const Text('Back'),
                    ),
                  ),
                if (_step > 0) const SizedBox(width: 12),
                Expanded(
                  flex: 2,
                  child: ElevatedButton(
                    onPressed: _step == 2 ? _register : () {
                      if (_step < 2) {
                        _ctrl.nextPage(duration: const Duration(milliseconds: 300), curve: Curves.easeInOut);
                      }
                    },
                    child: _loading
                      ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                      : Text(_step == 2 ? 'Create Account' : 'Continue', style: const TextStyle(fontSize: 15, fontWeight: FontWeight.w600)),
                  ),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildStepIndicator() {
    return Row(
      mainAxisAlignment: MainAxisAlignment.center,
      children: List.generate(3, (i) {
        final isActive = i <= _step;
        return Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Container(
              width: 32, height: 32,
              decoration: BoxDecoration(
                color: isActive ? AppColors.primary : AppColors.border,
                shape: BoxShape.circle,
              ),
              child: Center(child: Text('${i + 1}', style: TextStyle(
                fontSize: 13, fontWeight: FontWeight.w700,
                color: isActive ? Colors.white : AppColors.textSec,
              ))),
            ),
            if (i < 2) Container(width: 40, height: 2, color: i < _step ? AppColors.primary : AppColors.border),
          ],
        );
      }),
    );
  }

  Widget _buildStep1() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text('Business Details', style: TextStyle(fontSize: 20, fontWeight: FontWeight.w700, color: AppColors.textPri)),
        const SizedBox(height: 6),
        const Text('Tell us about your business', style: TextStyle(fontSize: 14, color: AppColors.textSec)),
        const SizedBox(height: 20),
        TextField(controller: _businessCtrl, decoration: const InputDecoration(labelText: 'Business Name', prefixIcon: Icon(Icons.store_outlined))),
        const SizedBox(height: 14),
        const Text('Business Type', style: TextStyle(fontSize: 14, fontWeight: FontWeight.w600, color: AppColors.textPri)),
        const SizedBox(height: 8),
        Wrap(
          spacing: 8, runSpacing: 8,
          children: ['retail', 'wholesale', 'restaurant', 'service', 'other'].map((t) => ChoiceChip(
            label: Text(t[0].toUpperCase() + t.substring(1), style: TextStyle(fontSize: 13,
              color: _businessType == t ? Colors.white : AppColors.textSec)),
            selected: _businessType == t,
            selectedColor: AppColors.primary,
            backgroundColor: Colors.white,
            side: BorderSide(color: _businessType == t ? AppColors.primary : AppColors.border),
            onSelected: (v) => setState(() => _businessType = t),
          )).toList(),
        ),
        const SizedBox(height: 14),
        TextField(controller: _cityCtrl, decoration: const InputDecoration(labelText: 'City', prefixIcon: Icon(Icons.location_city_outlined))),
        const SizedBox(height: 14),
        TextField(controller: _addressCtrl, decoration: const InputDecoration(labelText: 'Address (optional)', prefixIcon: Icon(Icons.map_outlined))),
      ],
    );
  }

  Widget _buildStep2() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text('Business Settings', style: TextStyle(fontSize: 20, fontWeight: FontWeight.w700, color: AppColors.textPri)),
        const SizedBox(height: 6),
        const Text('Configure your preferences', style: TextStyle(fontSize: 14, color: AppColors.textSec)),
        const SizedBox(height: 20),
        DropdownButtonFormField<String>(
          value: _currency,
          decoration: const InputDecoration(labelText: 'Currency', prefixIcon: Icon(Icons.monetization_on_outlined)),
          items: ['TZS', 'USD', 'EUR', 'GBP', 'KES', 'UGX', 'RWF'].map((c) => DropdownMenuItem(value: c, child: Text(c))).toList(),
          onChanged: (v) => setState(() => _currency = v ?? 'TZS'),
        ),
        const SizedBox(height: 14),
        TextField(controller: _taxPctCtrl, decoration: const InputDecoration(labelText: 'Tax Percentage (%)', prefixIcon: Icon(Icons.percent))),
      ],
    );
  }

  Widget _buildStep3() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text('Owner Information', style: TextStyle(fontSize: 20, fontWeight: FontWeight.w700, color: AppColors.textPri)),
        const SizedBox(height: 6),
        const Text('Your personal details', style: TextStyle(fontSize: 14, color: AppColors.textSec)),
        const SizedBox(height: 20),
        TextField(controller: _nameCtrl, decoration: const InputDecoration(labelText: 'Full Name', prefixIcon: Icon(Icons.person_outline))),
        const SizedBox(height: 14),
        TextField(controller: _phoneCtrl, decoration: const InputDecoration(labelText: 'Phone (+255...)', prefixIcon: Icon(Icons.phone_outlined))),
        const SizedBox(height: 14),
        TextField(controller: _emailCtrl, decoration: const InputDecoration(labelText: 'Email', prefixIcon: Icon(Icons.email_outlined)), keyboardType: TextInputType.emailAddress),
        const SizedBox(height: 14),
        TextField(controller: _passCtrl, obscureText: _obscure,
          decoration: InputDecoration(
            labelText: 'Password', prefixIcon: const Icon(Icons.lock_outlined),
            suffixIcon: IconButton(icon: Icon(_obscure ? Icons.visibility_outlined : Icons.visibility_off_outlined),
              onPressed: () => setState(() => _obscure = !_obscure)),
          )),
        const SizedBox(height: 14),
        TextField(controller: _confirmCtrl, obscureText: _obscure2,
          decoration: InputDecoration(
            labelText: 'Confirm Password', prefixIcon: const Icon(Icons.lock_outlined),
            suffixIcon: IconButton(icon: Icon(_obscure2 ? Icons.visibility_outlined : Icons.visibility_off_outlined),
              onPressed: () => setState(() => _obscure2 = !_obscure2)),
          )),
      ],
    );
  }
}
