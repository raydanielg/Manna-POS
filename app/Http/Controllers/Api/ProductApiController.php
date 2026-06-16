<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductApiController extends Controller {
    use UserIdTrait;
    public function index(Request $req) {
        $q = Product::with(['category','brand','unit'])->where('created_by', $this->userId());
        if ($req->search) $q->where(function($qq) use ($req) { $qq->where('name','like','%'.$req->search.'%')->orWhere('sku','like','%'.$req->search.'%')->orWhere('barcode','like','%'.$req->search.'%'); });
        if ($req->category_id) $q->where('product_category_id',$req->category_id);
        if ($req->status) $q->where('status',$req->status);
        return response()->json($q->orderBy('name')->get()->map(fn($p) => $this->productArr($p)));
    }
    public function store(Request $req) {
        $data = $req->validate([
            'name' => 'required|string|max:191',
            'product_category_id' => 'nullable|exists:product_categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'unit_id' => 'nullable|exists:units,id',
            'selling_price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
            'reorder_level' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
            'barcode' => 'nullable|string|max:100',
            'image' => 'nullable|image|max:2048',
        ]);
        $data['sku'] = 'SKU-'.strtoupper(Str::random(6));
        $data['created_by'] = $this->userId();
        if ($req->hasFile('image')) $data['image'] = $req->file('image')->store('products','public');
        $product = Product::create($data);
        return response()->json($this->productArr($product->load(['category','brand','unit'])), 201);
    }
    public function show($id) {
        return response()->json($this->productArr(Product::with(['category','brand','unit'])->where('created_by', $this->userId())->findOrFail($id)));
    }
    public function update(Request $req, $id) {
        $p = Product::where('created_by', $this->userId())->findOrFail($id);
        $data = $req->validate([
            'name' => 'required|string|max:191',
            'product_category_id' => 'nullable|exists:product_categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'unit_id' => 'nullable|exists:units,id',
            'selling_price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
            'reorder_level' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
            'barcode' => 'nullable|string|max:100',
            'image' => 'nullable|image|max:2048',
        ]);
        if ($req->hasFile('image')) { if ($p->image) Storage::disk('public')->delete($p->image); $data['image'] = $req->file('image')->store('products','public'); }
        $p->update($data);
        return response()->json($this->productArr($p->fresh(['category','brand','unit'])));
    }
    public function destroy($id) {
        $p = Product::where('created_by', $this->userId())->findOrFail($id);
        if ($p->image) Storage::disk('public')->delete($p->image);
        $p->delete();
        return response()->json(['message'=>'Product deleted']);
    }
    public function formData() {
        return response()->json([
            'categories' => \App\Models\ProductCategory::select('id','name')->orderBy('name')->get(),
            'brands' => \App\Models\Brand::select('id','name')->orderBy('name')->get(),
            'units' => \App\Models\Unit::select('id','name','short_name')->orderBy('name')->get(),
        ]);
    }
    private function productArr($p): array {
        return [
            'id' => $p->id, 'name' => $p->name, 'sku' => $p->sku, 'barcode' => $p->barcode,
            'selling_price' => $p->selling_price, 'cost_price' => $p->cost_price, 'stock_quantity' => $p->stock_quantity ?? 0,
            'reorder_level' => $p->reorder_level ?? 0, 'description' => $p->description, 'status' => $p->status ?? 'active',
            'image' => $p->image, 'image_url' => $p->image ? asset('storage/'.$p->image) : null,
            'product_category_id' => $p->product_category_id, 'brand_id' => $p->brand_id, 'unit_id' => $p->unit_id,
            'category' => $p->category ? ['id'=>$p->category->id,'name'=>$p->category->name] : null,
            'brand' => $p->brand ? ['id'=>$p->brand->id,'name'=>$p->brand->name] : null,
            'unit' => $p->unit ? ['id'=>$p->unit->id,'name'=>$p->unit->name,'short_name'=>$p->unit->short_name] : null,
        ];
    }
}
