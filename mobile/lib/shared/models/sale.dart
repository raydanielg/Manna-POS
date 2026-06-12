class Sale {
  final int id;
  final String reference;
  final double total;
  final double paid;
  final String paymentStatus;
  final String paymentMethod;
  final String status;
  final String saleDate;
  final Map<String, dynamic>? customer;
  final List<dynamic>? items;

  Sale({required this.id, required this.reference, required this.total, required this.paid,
      required this.paymentStatus, required this.paymentMethod, required this.status,
      required this.saleDate, this.customer, this.items});

  factory Sale.fromJson(Map<String, dynamic> j) => Sale(
    id: j['id'], reference: j['reference'],
    total: double.tryParse(j['total']?.toString() ?? '0') ?? 0,
    paid: double.tryParse(j['paid']?.toString() ?? '0') ?? 0,
    paymentStatus: j['payment_status'] ?? 'unpaid',
    paymentMethod: j['payment_method'] ?? 'cash',
    status: j['status'] ?? 'completed', saleDate: j['sale_date'] ?? '',
    customer: j['customer'] != null ? Map<String, dynamic>.from(j['customer']) : null,
    items: j['items'],
  );

  String get customerName => customer?['name'] ?? 'Walk-in';
  double get outstanding => total - paid;
}