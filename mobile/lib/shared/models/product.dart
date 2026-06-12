class Product {
  final int id;
  final String name;
  final String? sku;
  final String? barcode;
  final double sellingPrice;
  final double costPrice;
  final int stockQuantity;
  final int reorderLevel;
  final String? description;
  final String status;
  final String? imageUrl;
  final int? categoryId;
  final int? brandId;
  final int? unitId;
  final Map<String, dynamic>? category;
  final Map<String, dynamic>? brand;
  final Map<String, dynamic>? unit;

  const Product({
    required this.id, required this.name, this.sku, this.barcode,
    required this.sellingPrice, required this.costPrice,
    required this.stockQuantity, required this.reorderLevel,
    this.description, required this.status, this.imageUrl,
    this.categoryId, this.brandId, this.unitId,
    this.category, this.brand, this.unit,
  });

  factory Product.fromJson(Map<String, dynamic> j) => Product(
    id: j['id'],
    name: j['name'] ?? '',
    sku: j['sku'],
    barcode: j['barcode'],
    sellingPrice: double.tryParse(j['selling_price']?.toString() ?? '0') ?? 0,
    costPrice: double.tryParse(j['cost_price']?.toString() ?? '0') ?? 0,
    stockQuantity: j['stock_quantity'] is int ? j['stock_quantity'] : int.tryParse(j['stock_quantity']?.toString() ?? '0') ?? 0,
    reorderLevel: j['reorder_level'] is int ? j['reorder_level'] : int.tryParse(j['reorder_level']?.toString() ?? '0') ?? 0,
    description: j['description'],
    status: j['status'] ?? 'active',
    imageUrl: j['image_url'],
    categoryId: j['product_category_id'],
    brandId: j['brand_id'],
    unitId: j['unit_id'],
    category: j['category'] != null ? Map<String,dynamic>.from(j['category']) : null,
    brand: j['brand'] != null ? Map<String,dynamic>.from(j['brand']) : null,
    unit: j['unit'] != null ? Map<String,dynamic>.from(j['unit']) : null,
  );

  bool get isLowStock => stockQuantity > 0 && stockQuantity <= reorderLevel;
  bool get isOutOfStock => stockQuantity <= 0;
  String get categoryName => category?['name'] ?? 'Uncategorized';
  String get brandName => brand?['name'] ?? '';
  String get unitShort => unit?['short_name'] ?? 'pcs';
}
