import 'dart:io';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:image_picker/image_picker.dart';
import '../../providers/product_provider.dart';
import '../../models/product.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/toast_helper.dart';
import '../../constants/app_constants.dart';

class AddProductScreen extends StatefulWidget {
  final Product? product;
  const AddProductScreen({super.key, this.product});
  @override State<AddProductScreen> createState() => _AddProductScreenState();
}

class _AddProductScreenState extends State<AddProductScreen> {
  final _form = GlobalKey<FormState>();
  late TextEditingController _nameCtrl, _skuCtrl, _barcodeCtrl;
  late TextEditingController _sellingPriceCtrl, _purchasePriceCtrl;
  late TextEditingController _stockQtyCtrl, _reorderCtrl;
  late TextEditingController _taxCtrl, _discountCtrl, _descCtrl;
  int? _categoryId, _brandId, _unitId, _warrantyId;
  bool _isActive = true;
  bool _saving = false;
  XFile? _imageFile;
  bool _isEdit = false;

  @override
  void initState() {
    super.initState();
    final p = widget.product;
    _isEdit = p != null;
    _nameCtrl = TextEditingController(text: p?.name);
    _skuCtrl = TextEditingController(text: p?.sku);
    _barcodeCtrl = TextEditingController(text: p?.barcode);
    _sellingPriceCtrl = TextEditingController(text: p?.sellingPrice.toString());
    _purchasePriceCtrl = TextEditingController(text: p?.purchasePrice.toString());
    _stockQtyCtrl = TextEditingController(text: p?.stockQuantity.toString() ?? '0');
    _reorderCtrl = TextEditingController(text: p?.reorderLevel.toString() ?? '5');
    _taxCtrl = TextEditingController(text: p?.taxAmount.toString() ?? '0');
    _discountCtrl = TextEditingController(text: p?.discountAmount.toString() ?? '0');
    _descCtrl = TextEditingController(text: p?.description);
    _categoryId = p?.categoryId;
    _brandId = p?.brandId;
    _unitId = p?.unitId;
    _warrantyId = p?.warrantyId;
    _isActive = p?.isActive ?? true;
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final pp = context.read<ProductProvider>();
      if (pp.categories.isEmpty || pp.brands.isEmpty || pp.units.isEmpty) {
        pp.fetchProducts();
      }
    });
  }

  @override
  void dispose() {
    for (final c in [_nameCtrl, _skuCtrl, _barcodeCtrl, _sellingPriceCtrl, _purchasePriceCtrl, _stockQtyCtrl, _reorderCtrl, _taxCtrl, _discountCtrl, _descCtrl]) {
      c.dispose();
    }
    super.dispose();
  }

  Future<void> _pickImage(ImageSource source) async {
    final picked = await ImagePicker().pickImage(source: source, maxWidth: 800, imageQuality: 80);
    if (picked != null) setState(() => _imageFile = picked);
  }

  Future<void> _save() async {
    if (!_form.currentState!.validate()) return;
    setState(() => _saving = true);
    try {
      final provider = context.read<ProductProvider>();
      final data = {
        'name': _nameCtrl.text.trim(),
        'sku': _skuCtrl.text.trim(),
        'barcode': _barcodeCtrl.text.trim(),
        'selling_price': double.tryParse(_sellingPriceCtrl.text) ?? 0,
        'purchase_price': double.tryParse(_purchasePriceCtrl.text) ?? 0,
        'stock_quantity': int.tryParse(_stockQtyCtrl.text) ?? 0,
        'reorder_level': int.tryParse(_reorderCtrl.text) ?? 5,
        'tax_amount': double.tryParse(_taxCtrl.text) ?? 0,
        'discount_amount': double.tryParse(_discountCtrl.text) ?? 0,
        'description': _descCtrl.text.trim(),
        'is_active': _isActive,
        if (_categoryId != null) 'product_category_id': _categoryId,
        if (_brandId != null) 'brand_id': _brandId,
        if (_unitId != null) 'unit_id': _unitId,
        if (_warrantyId != null) 'warranty_id': _warrantyId,
      };
      if (_isEdit) {
        await provider.updateProduct(widget.product!.id, data, imagePath: _imageFile?.path);
      } else {
        await provider.createProduct(data, imagePath: _imageFile?.path);
      }
      if (mounted) {
        ToastHelper.showSuccess(context, _isEdit ? 'Product updated' : 'Product created');
        Navigator.pop(context, true);
      }
    } catch (e) {
      if (mounted) ToastHelper.showError(context, e.toString());
      setState(() => _saving = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    final pp = context.watch<ProductProvider>();
    return Scaffold(
      backgroundColor: theme.scaffoldBackgroundColor,
      appBar: AppBar(
        title: Text(_isEdit ? 'Edit Product' : 'Add Product'),
        actions: [
          TextButton(
            onPressed: _saving ? null : _save,
            child: _saving
                ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(strokeWidth: 2))
                : Text(_isEdit ? 'Update' : 'Save', style: const TextStyle(fontWeight: FontWeight.w700)),
          ),
        ],
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.fromLTRB(16, 8, 16, 100),
        child: Form(
          key: _form,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              GestureDetector(
                onTap: () => _showImagePicker(),
                child: Container(
                  height: 140,
                  decoration: BoxDecoration(
                    color: AppColors.surfaceVariant,
                    borderRadius: BorderRadius.circular(14),
                    border: Border.all(color: AppColors.border),
                  ),
                  child: _imageFile != null
                      ? ClipRRect(borderRadius: BorderRadius.circular(13), child: Image.file(File(_imageFile!.path), fit: BoxFit.cover, width: double.infinity))
                      : widget.product?.imageUrl != null
                          ? ClipRRect(borderRadius: BorderRadius.circular(13), child: Image.network(widget.product!.imageUrl!, fit: BoxFit.cover, width: double.infinity,
                              errorBuilder: (_, __, ___) => _imagePlaceholder()))
                          : _imagePlaceholder(),
                ),
              ),
              const SizedBox(height: 16),
              TextFormField(controller: _nameCtrl, decoration: const InputDecoration(labelText: 'Product Name *', prefixIcon: Icon(Icons.label_outline, size: 20)), validator: (v) => v!.trim().isEmpty ? 'Required' : null),
              const SizedBox(height: 12),
              Row(children: [
                Expanded(child: TextFormField(controller: _skuCtrl, decoration: const InputDecoration(labelText: 'SKU', prefixIcon: Icon(Icons.qr_code, size: 20)))),
                const SizedBox(width: 12),
                Expanded(child: TextFormField(controller: _barcodeCtrl, decoration: const InputDecoration(labelText: 'Barcode', prefixIcon: Icon(Icons.scanner, size: 20)))),
              ]),
              const SizedBox(height: 12),
              DropdownButtonFormField<int>(
                decoration: const InputDecoration(labelText: 'Category', prefixIcon: Icon(Icons.category_outlined, size: 20)),
                value: _categoryId,
                items: pp.categories.map((c) => DropdownMenuItem(value: c.id, child: Text(c.name))).toList(),
                onChanged: (v) => setState(() => _categoryId = v),
              ),
              const SizedBox(height: 12),
              Row(children: [
                Expanded(child: DropdownButtonFormField<int>(
                  decoration: const InputDecoration(labelText: 'Brand', prefixIcon: Icon(Icons.bookmark_outline, size: 20)),
                  value: _brandId,
                  items: pp.brands.map((b) => DropdownMenuItem(value: b.id, child: Text(b.name ?? ''))).toList(),
                  onChanged: (v) => setState(() => _brandId = v),
                )),
                const SizedBox(width: 12),
                Expanded(child: DropdownButtonFormField<int>(
                  decoration: const InputDecoration(labelText: 'Unit', prefixIcon: Icon(Icons.straighten, size: 20)),
                  value: _unitId,
                  items: pp.units.map((u) => DropdownMenuItem(value: u.id, child: Text(u.name ?? ''))).toList(),
                  onChanged: (v) => setState(() => _unitId = v),
                )),
              ]),
              const SizedBox(height: 12),
              DropdownButtonFormField<int>(
                decoration: const InputDecoration(labelText: 'Warranty', prefixIcon: Icon(Icons.verified_outlined, size: 20)),
                value: _warrantyId,
                items: pp.warranties.map((w) => DropdownMenuItem(value: w.id, child: Text(w.name ?? ''))).toList(),
                onChanged: (v) => setState(() => _warrantyId = v),
              ),
              const SizedBox(height: 12),
              Row(children: [
                Expanded(child: TextFormField(controller: _sellingPriceCtrl, keyboardType: TextInputType.number, decoration: const InputDecoration(labelText: 'Selling Price *', prefixIcon: Icon(Icons.attach_money, size: 20)), validator: (v) => v!.trim().isEmpty ? 'Required' : null)),
                const SizedBox(width: 12),
                Expanded(child: TextFormField(controller: _purchasePriceCtrl, keyboardType: TextInputType.number, decoration: const InputDecoration(labelText: 'Purchase Price', prefixIcon: Icon(Icons.shopping_cart_outlined, size: 20)))),
              ]),
              const SizedBox(height: 12),
              Row(children: [
                Expanded(child: TextFormField(controller: _stockQtyCtrl, keyboardType: TextInputType.number, decoration: const InputDecoration(labelText: 'Stock Qty', prefixIcon: Icon(Icons.inventory_2_outlined, size: 20)))),
                const SizedBox(width: 12),
                Expanded(child: TextFormField(controller: _reorderCtrl, keyboardType: TextInputType.number, decoration: const InputDecoration(labelText: 'Reorder Level', prefixIcon: Icon(Icons.low_priority, size: 20)))),
              ]),
              const SizedBox(height: 12),
              Row(children: [
                Expanded(child: TextFormField(controller: _taxCtrl, keyboardType: TextInputType.number, decoration: const InputDecoration(labelText: 'Tax Amount', prefixIcon: Icon(Icons.receipt, size: 20)))),
                const SizedBox(width: 12),
                Expanded(child: TextFormField(controller: _discountCtrl, keyboardType: TextInputType.number, decoration: const InputDecoration(labelText: 'Discount', prefixIcon: Icon(Icons.discount_outlined, size: 20)))),
              ]),
              const SizedBox(height: 12),
              TextFormField(controller: _descCtrl, maxLines: 3, decoration: const InputDecoration(labelText: 'Description', prefixIcon: Icon(Icons.description_outlined, size: 20), alignLabelWithHint: true)),
              const SizedBox(height: 16),
              GlassCard(
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    const Text('Active', style: TextStyle(fontWeight: FontWeight.w600)),
                    Switch(value: _isActive, onChanged: (v) => setState(() => _isActive = v), activeColor: AppColors.success),
                  ],
                ),
              ),
              const SizedBox(height: 32),
              SizedBox(
                height: 54,
                child: ElevatedButton(
                  onPressed: _saving ? null : _save,
                  child: _saving
                      ? const SizedBox(width: 24, height: 24, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5))
                      : Text(_isEdit ? 'Update Product' : 'Create Product', style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w700)),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _imagePlaceholder() {
    return Column(
      mainAxisAlignment: MainAxisAlignment.center,
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            IconButton(icon: const Icon(Icons.camera_alt_outlined, size: 28, color: AppColors.textSec), onPressed: () => _pickImage(ImageSource.camera)),
            const SizedBox(width: 24),
            IconButton(icon: const Icon(Icons.photo_library_outlined, size: 28, color: AppColors.textSec), onPressed: () => _pickImage(ImageSource.gallery)),
          ],
        ),
        const SizedBox(height: 4),
        const Text('Tap to add image', style: TextStyle(color: AppColors.textSec, fontSize: 12)),
      ],
    );
  }

  void _showImagePicker() {
    showModalBottomSheet(
      context: context,
      backgroundColor: Colors.transparent,
      builder: (_) => Container(
        padding: const EdgeInsets.all(24),
        decoration: const BoxDecoration(color: Colors.white, borderRadius: BorderRadius.vertical(top: Radius.circular(24))),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Container(width: 40, height: 4, decoration: BoxDecoration(color: AppColors.border, borderRadius: BorderRadius.circular(4))),
            const SizedBox(height: 20),
            const Text('Select Image', style: TextStyle(fontSize: 18, fontWeight: FontWeight.w700)),
            const SizedBox(height: 20),
            ListTile(
              leading: const Icon(Icons.camera_alt_outlined, color: AppColors.primary),
              title: const Text('Camera'),
              onTap: () { Navigator.pop(context); _pickImage(ImageSource.camera); },
            ),
            ListTile(
              leading: const Icon(Icons.photo_library_outlined, color: AppColors.primary),
              title: const Text('Gallery'),
              onTap: () { Navigator.pop(context); _pickImage(ImageSource.gallery); },
            ),
          ],
        ),
      ),
    );
  }
}
