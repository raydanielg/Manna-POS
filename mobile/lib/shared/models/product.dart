class Product {
  final int id;
  final String name;
  final String? sku;
  final double sellingPrice;
  final double purchasePrice;
  final double stockQuantity;
  final double reorderLevel;
  final String status;
  final String? description;
  final Map<String, dynamic>? category;
  final Map<String, dynamic>? brand;
  final Map<String, dynamic>? unit;

  Product({required this.id, required this.name, this.sku, required this.sellingPrice,
      required this.purchasePrice, required this.stockQuantity, required this.reorderLevel,
      required this.status, this.description, this.category, this.brand, this.unit});

  factory Product.fromJson(Map<String, dynamic> j) => Product(
    id: j['id'], name: j['name'], sku: j['sku'],
    sellingPrice: double.tryParse(j['selling_price']?.toString() ?? '0') ?? 0,
    purchasePrice: double.tryParse(j['purchase_price']?.toString() ?? '0') ?? 0,
    stockQuantity: double.tryParse(j['stock_quantity']?.toString() ?? '0') ?? 0,
    reorderLevel: double.tryParse(j['reorder_level']?.toString() ?? '0') ?? 0,
    status: j['status'] ?? 'active', description: j['description'],
    category: j['category'] != null ? Map<String, dynamic>.from(j['category']) : null,
    brand: j['brand'] != null ? Map<String, dynamic>.from(j['brand']) : null,
    unit: j['unit'] != null ? Map<String, dynamic>.from(j['unit']) : null,
  );

  bool get isLowStock => stockQuantity <= reorderLevel && stockQuantity > 0;
  bool get isOutOfStock => stockQuantity <= 0;
  String get categoryName => category?['name'] ?? '';
  String get brandName => brand?['name'] ?? '';
  String get unitShort => unit?['short_name'] ?? 'pcs';
}