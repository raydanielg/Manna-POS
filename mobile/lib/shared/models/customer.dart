class Customer {
  final int id;
  final String name;
  final String? email;
  final String? phone;
  final String? address;
  final String? city;
  final String status;
  final double balance;
  final Map<String, dynamic>? group;

  Customer({required this.id, required this.name, this.email, this.phone,
      this.address, this.city, required this.status, required this.balance, this.group});

  factory Customer.fromJson(Map<String, dynamic> j) => Customer(
    id: j['id'], name: j['name'], email: j['email'], phone: j['phone'],
    address: j['address'], city: j['city'], status: j['status'] ?? 'active',
    balance: double.tryParse(j['balance']?.toString() ?? '0') ?? 0,
    group: j['group'] != null ? Map<String, dynamic>.from(j['group']) : null,
  );

  String get initials => name.trim().split(' ').map((w) => w.isNotEmpty ? w[0] : '').take(2).join().toUpperCase();
}