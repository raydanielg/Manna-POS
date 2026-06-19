<?php
namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
class ProductVariationController extends Controller {
    public function index(Request $req) {
        $q = ProductVariation::with('product')->forCurrentUser($this->currentBusinessId());
        if ($req->search) $q->where(function($q2) use($req){ $q2->where('attribute_value','like',"%{$req->search}%")->orWhere('sku','like',"%{$req->search}%")->orWhereHas('product',fn($q3)=>$q3->where('name','like',"%{$req->search}%")); });
        if ($req->product_id) $q->where('product_id',$req->product_id);
        if ($req->status) $q->where('status',$req->status);
        return response()->json($q->latest()->get());
    }
    public function store(Request $req) {
        $data = $req->validate(['product_id'=>'required|exists:products,id','attribute_name'=>'required|string|max:100','attribute_value'=>'required|string|max:191','additional_price'=>'nullable|numeric|min:0','sku'=>'nullable|string|max:100','stock_quantity'=>'nullable|integer|min:0','status'=>'in:active,inactive']);
        if (empty($data['sku'])) $data['sku'] = 'VAR-'.strtoupper(Str::random(6));
        $data['created_by'] = $this->currentBusinessId();
        return response()->json(['success'=>true,'variation'=>ProductVariation::create($data)->load('product')], 201);
    }
    public function show(ProductVariation $productVariation) { $this->ensureOwns($productVariation); return response()->json($productVariation->load('product')); }
    public function update(Request $req, ProductVariation $productVariation) {
        $this->ensureOwns($productVariation);
        $productVariation->update($req->validate(['product_id'=>'required|exists:products,id','attribute_name'=>'required|string|max:100','attribute_value'=>'required|string|max:191','additional_price'=>'nullable|numeric|min:0','sku'=>'nullable|string|max:100','stock_quantity'=>'nullable|integer|min:0','status'=>'in:active,inactive']));
        return response()->json(['success'=>true,'variation'=>$productVariation->load('product')]);
    }
    public function destroy(ProductVariation $productVariation) { $this->ensureOwns($productVariation); $productVariation->delete(); return response()->json(['success'=>true]); }

    public function importLibrary()
    {
        $library = [
            ['attribute_name'=>'Color','attribute_value'=>'Red','additional_price'=>0],
            ['attribute_name'=>'Color','attribute_value'=>'Blue','additional_price'=>0],
            ['attribute_name'=>'Color','attribute_value'=>'Black','additional_price'=>0],
            ['attribute_name'=>'Color','attribute_value'=>'White','additional_price'=>0],
            ['attribute_name'=>'Color','attribute_value'=>'Green','additional_price'=>0],
            ['attribute_name'=>'Size','attribute_value'=>'Small','additional_price'=>0],
            ['attribute_name'=>'Size','attribute_value'=>'Medium','additional_price'=>0],
            ['attribute_name'=>'Size','attribute_value'=>'Large','additional_price'=>5],
            ['attribute_name'=>'Size','attribute_value'=>'XL','additional_price'=>10],
            ['attribute_name'=>'Size','attribute_value'=>'XXL','additional_price'=>15],
            ['attribute_name'=>'Material','attribute_value'=>'Cotton','additional_price'=>0],
            ['attribute_name'=>'Material','attribute_value'=>'Polyester','additional_price'=>0],
            ['attribute_name'=>'Material','attribute_value'=>'Leather','additional_price'=>25],
            ['attribute_name'=>'Material','attribute_value'=>'Plastic','additional_price'=>0],
            ['attribute_name'=>'Material','attribute_value'=>'Metal','additional_price'=>15],
            ['attribute_name'=>'Storage','attribute_value'=>'64GB','additional_price'=>0],
            ['attribute_name'=>'Storage','attribute_value'=>'128GB','additional_price'=>20],
            ['attribute_name'=>'Storage','attribute_value'=>'256GB','additional_price'=>40],
            ['attribute_name'=>'Storage','attribute_value'=>'512GB','additional_price'=>80],
            ['attribute_name'=>'Weight','attribute_value'=>'1kg','additional_price'=>0],
            ['attribute_name'=>'Weight','attribute_value'=>'2kg','additional_price'=>5],
            ['attribute_name'=>'Weight','attribute_value'=>'5kg','additional_price'=>15],
        ];
        return response()->json($library);
    }

    public function import(Request $req)
    {
        $data = $req->validate([
            'product_id'=>'required|exists:products,id',
            'items'=>'required|array|min:1',
            'items.*.attribute_name'=>'required|string|max:100',
            'items.*.attribute_value'=>'required|string|max:191',
        ]);
        $businessId = $this->currentBusinessId();
        $product = Product::where('id', $data['product_id'])->where(function($q) use($businessId){ $q->where('user_id', $businessId)->orWhere('created_by', $businessId); })->first();
        if (!$product) return response()->json(['success'=>false,'message'=>'Product not found'], 404);

        $imported = 0; $skipped = 0;
        foreach ($data['items'] as $item) {
            $exists = ProductVariation::where('created_by', $businessId)
                ->where('product_id', $data['product_id'])
                ->where('attribute_name', $item['attribute_name'])
                ->where('attribute_value', $item['attribute_value'])
                ->exists();
            if ($exists) { $skipped++; continue; }
            ProductVariation::create([
                'product_id' => $data['product_id'],
                'attribute_name' => $item['attribute_name'],
                'attribute_value' => $item['attribute_value'],
                'additional_price' => $item['additional_price'] ?? 0,
                'sku' => 'VAR-' . strtoupper(Str::random(6)),
                'stock_quantity' => $item['stock_quantity'] ?? 0,
                'status' => 'active',
                'created_by' => $businessId,
            ]);
            $imported++;
        }
        return response()->json(['success'=>true,'imported'=>$imported,'skipped'=>$skipped,'message'=>"Imported {$imported} variations, skipped {$skipped} duplicates."]);
    }
}
