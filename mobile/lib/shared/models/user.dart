class AppUser {
  final int id;
  final String name;
  final String email;
  final String? phone;
  final String role;
  final String? businessName;
  final String? businessType;
  final String? businessAddress;
  final String? businessCity;
  final String businessCountry;
  final String currency;
  final double taxPercentage;
  final String fiscalYearStart;

  const AppUser({
    required this.id, required this.name, required this.email,
    this.phone, required this.role,
    this.businessName, this.businessType, this.businessAddress, this.businessCity,
    this.businessCountry = 'Tanzania', this.currency = 'TZS',
    this.taxPercentage = 18.0, this.fiscalYearStart = 'January',
  });

  factory AppUser.fromJson(Map<String, dynamic> j) => AppUser(
    id: j['id'],
    name: j['name'] ?? '',
    email: j['email'] ?? '',
    phone: j['phone'],
    role: j['role'] ?? 'user',
    businessName: j['business_name'],
    businessType: j['business_type'],
    businessAddress: j['business_address'],
    businessCity: j['business_city'],
    businessCountry: j['business_country'] ?? 'Tanzania',
    currency: j['currency'] ?? 'TZS',
    taxPercentage: double.tryParse(j['tax_percentage']?.toString() ?? '18') ?? 18.0,
    fiscalYearStart: j['fiscal_year_start'] ?? 'January',
  );

  String get initials => name.trim().split(' ').where((w) => w.isNotEmpty).map((w) => w[0]).take(2).join().toUpperCase();
  String get displayBusiness => businessName ?? 'My Business';
  String get currencySymbol => const {'TZS': 'TSh', 'USD': '\$', 'EUR': '€', 'KES': 'KSh', 'UGX': 'USh', 'GBP': '£'}[currency] ?? currency;
}