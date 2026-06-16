<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandApiController extends Controller {
    public function index() {
        return response()->json(Brand::orderBy('name')->get());
    }
    public function store(Request $req) {
        $data = $req->validate([
            'name' => 'required|string|max:191|unique:brands,name',
            'description' => 'nullable|string',
            'status' => 'in:active,inactive',
        ]);
        return response()->json(Brand::create($data), 201);
    }
    public function show(Brand $brand) {
        return response()->json($brand);
    }
    public function update(Request $req, Brand $brand) {
        $data = $req->validate([
            'name' => 'required|string|max:191|unique:brands,name,'.$brand->id,
            'description' => 'nullable|string',
            'status' => 'in:active,inactive',
        ]);
        $brand->update($data);
        return response()->json($brand);
    }
    public function destroy(Brand $brand) {
        $brand->delete();
        return response()->json(['message'=>'Brand deleted']);
    }
}
