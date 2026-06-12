class Expense {
  final int id;
  final String reference;
  final double amount;
  final String expenseDate;
  final String? paymentMethod;
  final String? notes;
  final Map<String, dynamic>? category;

  Expense({required this.id, required this.reference, required this.amount,
      required this.expenseDate, this.paymentMethod, this.notes, this.category});

  factory Expense.fromJson(Map<String, dynamic> j) => Expense(
    id: j['id'], reference: j['reference'] ?? '',
    amount: double.tryParse(j['amount']?.toString() ?? '0') ?? 0,
    expenseDate: j['expense_date'] ?? '',
    paymentMethod: j['payment_method'],
    notes: j['notes'],
    category: j['category'] != null ? Map<String, dynamic>.from(j['category']) : null,
  );

  String get title => reference;
  int? get categoryId => category?['id'] as int?;
  String get categoryName => category?['name'] ?? 'Uncategorized';
}