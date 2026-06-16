<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCategoryApiController extends Controller {
    public function index() {
        return response()->json(ProductCategory::with('parent:id,name')->orderBy('name')->get());
    }
    public function store(Request $req) {
        $data = $req->validate([
            'name' => 'required|string|max:191|unique:product_categories,name',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:product_categories,id',
            'status' => 'in:active,inactive',
        ]);
        return response()->json(ProductCategory::create($data), 201);
    }
    public function show(ProductCategory $productCategory) {
        return response()->json($productCategory->load('parent:id,name'));
    }
    public function update(Request $req, ProductCategory $productCategory) {
        $data = $req->validate([
            'name' => 'required|string|max:191|unique:product_categories,name,'.$productCategory->id,
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:product_categories,id',
            'status' => 'in:active,inactive',
        ]);
        $productCategory->update($data);
        return response()->json($productCategory);
    }
    public function destroy(ProductCategory $productCategory) {
        $productCategory->delete();
        return response()->json(['message'=>'Category deleted']);
    }
}
