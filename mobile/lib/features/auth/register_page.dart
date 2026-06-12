import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:provider/provider.dart';
import '../../core/auth_provider.dart';
import '../../core/api_service.dart';
import '../../shared/theme/app_theme.dart';

class RegisterPage extends StatefulWidget {
  const RegisterPage({super.key});
  @override State<RegisterPage> createState() => _RegisterPageState();
}

class _RegisterPageState extends State<RegisterPage> {
  final _pageCtrl = PageController();
  int _step = 0;

  // Step 1: Business Details
  final _bizName   = TextEditingController();
  String _bizType  = 'retail';
  final _bizCity   = TextEditingController();
  final _bizCountry = TextEditingController(text: 'Tanzania');
  final _bizAddr   = TextEditingController();

  // Step 2: Business Settings
  String _currency    = 'TZS';
  final _taxPct       = TextEditingController(text: '18');
  String _fiscalStart = 'January';

  // Step 3: Owner Info
  final _name        = TextEditingController();
  final _phone       = TextEditingController();
  final _email       = TextEditingController();
  final _pass        = TextEditingController();
  final _confirmPass = TextEditingController();
  bool _showPass     = false;
  bool _showConfirm  = false;
  String? _error;

  static const _bizTypes = [
    ('retail', Icons.store, 'Retail'),
    ('wholesale', Icons.warehouse, 'Wholesale'),
    ('restaurant', Icons.restaurant, 'Restaurant'),
    ('service', Icons.build, 'Service'),
    ('other', Icons.category, 'Other'),
  ];

  static const _currencies = [
    ('TZS', 'Tanzanian Shilling'),
    ('USD', 'US Dollar'),
    ('EUR', 'Euro'),
    ('KES', 'Kenyan Shilling'),
    ('UGX', 'Ugandan Shilling'),
    ('GBP', 'British Pound'),
    ('ZAR', 'South African Rand'),
  ];

  static const _months = ['January','February','March','April','May','June','July','August','September','October','November','December'];

  @override
  void dispose() {
    _pageCtrl.dispose();
    for (final c in [_bizName, _bizCity, _bizCountry, _bizAddr, _taxPct, _name, _phone, _email, _pass, _confirmPass]) c.dispose();
    super.dispose();
  }

  bool _canNext() {
    switch (_step) {
      case 0: return _bizName.text.trim().length >= 2 && _bizCity.text.trim().length >= 2;
      case 1: return true;
      case 2: return _name.text.trim().length >= 2 &&
                     _phone.text.trim().length == 9 &&
                     _email.text.contains('@') &&
                     _pass.text.length >= 8 &&
                     _pass.text == _confirmPass.text;
      default: return false;
    }
  }

  void _next() {
    if (!_canNext()) return;
    if (_step < 2) {
      _pageCtrl.nextPage(duration: const Duration(milliseconds: 350), curve: Curves.easeInOut);
      setState(() { _step++; _error = null; });
    } else {
      _register();
    }
  }

  void _prev() {
    if (_step > 0) {
      _pageCtrl.previousPage(duration: const Duration(milliseconds: 350), curve: Curves.easeInOut);
      setState(() { _step--; _error = null; });
    } else {
      Navigator.pushReplacementNamed(context, '/login');
    }
  }

  String _normalizePhone(String p) {
    p = p.trim();
    if (p.startsWith('0')) p = p.substring(1);
    return '+255$p';
  }

  Future<void> _register() async {
    setState(() => _error = null);
    try {
      await context.read<AuthProvider>().register({
        'name'             : _name.text.trim(),
        'email'            : _email.text.trim(),
        'password'         : _pass.text,
        'phone'            : _normalizePhone(_phone.text),
        'business_name'    : _bizName.text.trim(),
        'business_type'    : _bizType,
        'business_city'    : _bizCity.text.trim(),
        'business_country' : _bizCountry.text.trim().isEmpty ? 'Tanzania' : _bizCountry.text.trim(),
        'business_address' : _bizAddr.text.trim(),
        'currency'         : _currency,
        'tax_percentage'   : double.tryParse(_taxPct.text) ?? 18.0,
        'fiscal_year_start': _fiscalStart,
      });
      if (mounted) Navigator.pushReplacementNamed(context, '/dashboard');
    } on ApiException catch (e) {
      setState(() => _error = e.message);
    } catch (_) {
      setState(() => _error = 'Connection error. Check your network.');
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.bg,
      body: SafeArea(
        child: Column(children: [
          _header(),
          Expanded(
            child: PageView(
              controller: _pageCtrl,
              physics: const NeverScrollableScrollPhysics(),
              children: [_step1(), _step2(), _step3()],
            ),
          ),
          _bottomNav(),
        ]),
      ),
    );
  }

  Widget _header() {
    const titles = ['Business Details', 'Business Settings', 'Owner Information'];
    const subs   = ['Basic information about your business', 'Tax and financial settings', 'Your personal details'];
    const icons  = [Icons.store_outlined, Icons.tune_outlined, Icons.person_outlined];
    return Container(
      decoration: const BoxDecoration(
        gradient: LinearGradient(colors: [Color(0xFF2563EB), Color(0xFF7C3AED)], begin: Alignment.topLeft, end: Alignment.bottomRight),
      ),
      child: Column(children: [
        Padding(
          padding: const EdgeInsets.fromLTRB(8, 8, 16, 0),
          child: Row(children: [
            IconButton(icon: const Icon(Icons.arrow_back_ios, color: Colors.white, size: 20), onPressed: _prev),
            const Spacer(),
            TextButton(
              onPressed: () => Navigator.pushReplacementNamed(context, '/login'),
              child: const Text('Sign In', style: TextStyle(color: Colors.white70, fontWeight: FontWeight.w600, fontSize: 14)),
            ),
          ]),
        ),
        Padding(
          padding: const EdgeInsets.fromLTRB(24, 4, 24, 24),
          child: Column(children: [
            _stepIndicator(),
            const SizedBox(height: 20),
            Row(children: [
              Container(
                width: 48, height: 48,
                decoration: BoxDecoration(color: Colors.white.withValues(alpha: 0.15), borderRadius: BorderRadius.circular(14)),
                child: Icon(icons[_step], color: Colors.white, size: 24),
              ),
              const SizedBox(width: 14),
              Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                Text(titles[_step], style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w800, fontSize: 18, letterSpacing: -0.3)),
                Text(subs[_step], style: const TextStyle(color: Colors.white70, fontSize: 13)),
              ]),
            ]),
          ]),
        ),
      ]),
    );
  }

  Widget _stepIndicator() {
    const labels = ['Business', 'Settings', 'Owner'];
    return Row(children: [
      for (int i = 0; i < 3; i++) ...[
        if (i > 0) Expanded(child: AnimatedContainer(duration: const Duration(milliseconds: 300), height: 2, margin: const EdgeInsets.only(bottom: 22), color: _step > i - 1 ? Colors.white : Colors.white30)),
        Column(children: [
          AnimatedContainer(
            duration: const Duration(milliseconds: 300),
            width: 34, height: 34,
            decoration: BoxDecoration(
              color: _step >= i ? Colors.white : Colors.transparent,
              border: Border.all(color: _step >= i ? Colors.white : Colors.white54, width: 2),
              shape: BoxShape.circle,
            ),
            child: Center(child: _step > i
              ? Icon(Icons.check_rounded, color: AppColors.primary, size: 18)
              : Text('${i+1}', style: TextStyle(color: _step == i ? AppColors.primary : Colors.white60, fontWeight: FontWeight.w800, fontSize: 13)),
            ),
          ),
          const SizedBox(height: 6),
          Text(labels[i], style: TextStyle(color: _step >= i ? Colors.white : Colors.white54, fontSize: 11, fontWeight: FontWeight.w600)),
        ]),
      ],
    ]);
  }

  Widget _step1() => SingleChildScrollView(
    padding: const EdgeInsets.all(20),
    child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
      _label('Business Name', required: true),
      TextFormField(
        controller: _bizName,
        decoration: const InputDecoration(labelText: 'e.g. Duka la Mama Pita', prefixIcon: Icon(Icons.storefront_outlined, size: 20)),
        onChanged: (_) => setState(() {}),
      ),
      const SizedBox(height: 20),
      _label('Business Type', required: true),
      const SizedBox(height: 10),
      Wrap(spacing: 10, runSpacing: 10, children: _bizTypes.map((t) {
        final (val, icon, lab) = t;
        final sel = _bizType == val;
        return GestureDetector(
          onTap: () => setState(() => _bizType = val),
          child: AnimatedContainer(
            duration: const Duration(milliseconds: 200),
            padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 11),
            decoration: BoxDecoration(
              color: sel ? AppColors.primaryLt : Colors.white,
              borderRadius: BorderRadius.circular(12),
              border: Border.all(color: sel ? AppColors.primary : AppColors.border, width: sel ? 2 : 1),
              boxShadow: sel ? [BoxShadow(color: AppColors.primary.withValues(alpha: 0.15), blurRadius: 8)] : [],
            ),
            child: Row(mainAxisSize: MainAxisSize.min, children: [
              Icon(icon, size: 18, color: sel ? AppColors.primary : AppColors.textSec),
              const SizedBox(width: 8),
              Text(lab, style: TextStyle(color: sel ? AppColors.primary : AppColors.textPri, fontWeight: sel ? FontWeight.w700 : FontWeight.w500, fontSize: 14)),
            ]),
          ),
        );
      }).toList()),
      const SizedBox(height: 20),
      _label('City', required: true),
      TextFormField(controller: _bizCity, decoration: const InputDecoration(labelText: 'e.g. Dar es Salaam', prefixIcon: Icon(Icons.location_city_outlined, size: 20)), onChanged: (_) => setState(() {})),
      const SizedBox(height: 20),
      _label('Country'),
      TextFormField(controller: _bizCountry, decoration: const InputDecoration(prefixIcon: Icon(Icons.public, size: 20))),
      const SizedBox(height: 20),
      _label('Business Address (optional)'),
      TextFormField(controller: _bizAddr, decoration: const InputDecoration(labelText: 'Street, building...', prefixIcon: Icon(Icons.place_outlined, size: 20))),
    ]),
  );

  Widget _step2() => SingleChildScrollView(
    padding: const EdgeInsets.all(20),
    child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
      _label('Currency'),
      DropdownButtonFormField<String>(
        value: _currency,
        decoration: const InputDecoration(prefixIcon: Icon(Icons.payments_outlined, size: 20)),
        isExpanded: true,
        items: _currencies.map((c) {
          final (code, name) = c;
          return DropdownMenuItem(value: code, child: Text('$code — $name', overflow: TextOverflow.ellipsis));
        }).toList(),
        onChanged: (v) => setState(() => _currency = v!),
      ),
      const SizedBox(height: 20),
      _label('VAT / Tax Rate'),
      TextFormField(
        controller: _taxPct,
        keyboardType: const TextInputType.numberWithOptions(decimal: true),
        decoration: const InputDecoration(
          labelText: 'Percentage (%)',
          hintText: '18',
          prefixIcon: Icon(Icons.percent_outlined, size: 20),
          helperText: 'Tanzania standard VAT is 18%',
        ),
      ),
      const SizedBox(height: 20),
      _label('Fiscal Year Starts'),
      DropdownButtonFormField<String>(
        value: _fiscalStart,
        decoration: const InputDecoration(prefixIcon: Icon(Icons.calendar_month_outlined, size: 20)),
        items: _months.map((m) => DropdownMenuItem(value: m, child: Text(m))).toList(),
        onChanged: (v) => setState(() => _fiscalStart = v!),
      ),
    ]),
  );

  Widget _step3() => SingleChildScrollView(
    padding: const EdgeInsets.all(20),
    child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
      if (_error != null) ...[
        Container(
          padding: const EdgeInsets.all(14),
          margin: const EdgeInsets.only(bottom: 14),
          decoration: BoxDecoration(color: AppColors.dangerLt, borderRadius: BorderRadius.circular(12), border: Border.all(color: AppColors.danger.withValues(alpha: 0.3))),
          child: Row(children: [
            const Icon(Icons.error_outline, color: AppColors.danger, size: 20),
            const SizedBox(width: 10),
            Expanded(child: Text(_error!, style: const TextStyle(color: AppColors.danger, fontSize: 13))),
          ]),
        ),
      ],
      _label('Full Name', required: true),
      TextFormField(
        controller: _name,
        decoration: const InputDecoration(labelText: 'Your full name', prefixIcon: Icon(Icons.person_outline, size: 20)),
        onChanged: (_) => setState(() {}),
      ),
      const SizedBox(height: 20),
      _label('Phone Number', required: true),
      _tanzaniaPhone(),
      const SizedBox(height: 4),
      const Text('9 digits after +255  •  e.g. 712 345 678', style: TextStyle(color: AppColors.textSec, fontSize: 11)),
      const SizedBox(height: 20),
      _label('Email Address', required: true),
      TextFormField(
        controller: _email,
        keyboardType: TextInputType.emailAddress,
        decoration: const InputDecoration(labelText: 'you@example.com', prefixIcon: Icon(Icons.email_outlined, size: 20)),
        onChanged: (_) => setState(() {}),
      ),
      const SizedBox(height: 20),
      _label('Password', required: true),
      TextFormField(
        controller: _pass,
        obscureText: !_showPass,
        decoration: InputDecoration(
          labelText: 'Min. 8 characters',
          prefixIcon: const Icon(Icons.lock_outline, size: 20),
          suffixIcon: IconButton(
            icon: Icon(_showPass ? Icons.visibility_off_outlined : Icons.visibility_outlined, color: AppColors.textSec, size: 20),
            onPressed: () => setState(() => _showPass = !_showPass),
          ),
        ),
        onChanged: (_) => setState(() {}),
      ),
      if (_pass.text.isNotEmpty) ...[
        const SizedBox(height: 8),
        _strengthBar(_pass.text),
      ],
      const SizedBox(height: 20),
      _label('Confirm Password', required: true),
      TextFormField(
        controller: _confirmPass,
        obscureText: !_showConfirm,
        decoration: InputDecoration(
          labelText: 'Re-enter your password',
          prefixIcon: const Icon(Icons.lock_outline, size: 20),
          suffixIcon: IconButton(
            icon: Icon(_showConfirm ? Icons.visibility_off_outlined : Icons.visibility_outlined, color: AppColors.textSec, size: 20),
            onPressed: () => setState(() => _showConfirm = !_showConfirm),
          ),
          errorText: _confirmPass.text.isNotEmpty && _pass.text != _confirmPass.text ? 'Passwords do not match' : null,
          suffixIconConstraints: const BoxConstraints(minWidth: 48),
        ),
        onChanged: (_) => setState(() {}),
      ),
      const SizedBox(height: 20),
      Center(child: Wrap(children: [
        const Text('Already have an account? ', style: TextStyle(color: AppColors.textSec, fontSize: 13)),
        GestureDetector(
          onTap: () => Navigator.pushReplacementNamed(context, '/login'),
          child: const Text('Sign In', style: TextStyle(color: AppColors.primary, fontWeight: FontWeight.w700, fontSize: 13)),
        ),
      ])),
    ]),
  );

  Widget _tanzaniaPhone() => Container(
    decoration: BoxDecoration(
      color: Colors.white,
      borderRadius: BorderRadius.circular(12),
      border: Border.all(color: _phone.text.length == 9 ? AppColors.primary : AppColors.border, width: _phone.text.length == 9 ? 2 : 1),
    ),
    child: Row(children: [
      Container(
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 14),
        decoration: const BoxDecoration(
          color: AppColors.primaryLt,
          borderRadius: BorderRadius.only(topLeft: Radius.circular(11), bottomLeft: Radius.circular(11)),
        ),
        child: Row(mainAxisSize: MainAxisSize.min, children: const [
          Icon(Icons.phone_android, size: 20, color: AppColors.primary),
          SizedBox(width: 8),
          Text('+255', style: TextStyle(fontWeight: FontWeight.w800, color: AppColors.primary, fontSize: 15)),
        ]),
      ),
      Container(width: 1, height: 30, color: AppColors.border),
      Expanded(
        child: TextField(
          controller: _phone,
          keyboardType: TextInputType.number,
          inputFormatters: [FilteringTextInputFormatter.digitsOnly, LengthLimitingTextInputFormatter(9)],
          style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w600, letterSpacing: 2),
          decoration: const InputDecoration(
            hintText: '7XX XXX XXX',
            hintStyle: TextStyle(letterSpacing: 1, fontWeight: FontWeight.w400, color: AppColors.textSec),
            border: InputBorder.none,
            enabledBorder: InputBorder.none,
            focusedBorder: InputBorder.none,
            contentPadding: EdgeInsets.symmetric(horizontal: 14, vertical: 14),
          ),
          onChanged: (_) => setState(() {}),
        ),
      ),
      if (_phone.text.length == 9)
        const Padding(padding: EdgeInsets.only(right: 12), child: Icon(Icons.check_circle, color: AppColors.success, size: 22)),
    ]),
  );

  Widget _strengthBar(String p) {
    int score = 0;
    if (p.length >= 8) score++;
    if (p.contains(RegExp(r'[A-Z]'))) score++;
    if (p.contains(RegExp(r'[0-9]'))) score++;
    if (p.contains(RegExp(r'[^A-Za-z0-9]'))) score++;
    const labels = ['', 'Weak', 'Fair', 'Good', 'Strong'];
    const colors = [Colors.transparent, AppColors.danger, AppColors.warning, AppColors.success, AppColors.success];
    return Row(children: [
      ...List.generate(4, (i) => Expanded(child: Container(
        height: 4, margin: const EdgeInsets.only(right: 4),
        decoration: BoxDecoration(
          color: i < score ? colors[score] : AppColors.border,
          borderRadius: BorderRadius.circular(4),
        ),
      ))),
      const SizedBox(width: 8),
      Text(labels[score], style: TextStyle(fontSize: 11, fontWeight: FontWeight.w600, color: colors[score])),
    ]);
  }

  Widget _bottomNav() {
    final loading = context.watch<AuthProvider>().loading;
    final canProceed = _canNext() && !loading;
    return Container(
      padding: const EdgeInsets.fromLTRB(20, 12, 20, 24),
      decoration: const BoxDecoration(
        color: Colors.white,
        border: Border(top: BorderSide(color: AppColors.border, width: 1)),
      ),
      child: Row(children: [
        if (_step > 0) ...[
          Expanded(
            flex: 2,
            child: OutlinedButton(
              onPressed: loading ? null : _prev,
              child: const Text('← Back'),
            ),
          ),
          const SizedBox(width: 12),
        ],
        Expanded(
          flex: 3,
          child: SizedBox(
            height: 52,
            child: ElevatedButton(
              onPressed: canProceed ? _next : null,
              style: ElevatedButton.styleFrom(
                backgroundColor: canProceed ? AppColors.primary : AppColors.border,
                foregroundColor: Colors.white,
              ),
              child: loading
                ? const SizedBox(width: 22, height: 22, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5))
                : Text(_step == 2 ? 'Create Account' : 'Continue →', style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w700)),
            ),
          ),
        ),
      ]),
    );
  }

  Widget _label(String text, {bool required = false}) => Padding(
    padding: const EdgeInsets.only(bottom: 8),
    child: RichText(text: TextSpan(
      text: text,
      style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w700, color: AppColors.textPri),
      children: required ? const [TextSpan(text: '  *', style: TextStyle(color: AppColors.danger))] : [],
    )),
  );
}