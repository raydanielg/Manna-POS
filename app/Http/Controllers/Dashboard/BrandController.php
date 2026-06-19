<?php
namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
class BrandController extends Controller {
    public function index(Request $req) {
        $q = Brand::withCount("products")->forCurrentUser($this->currentBusinessId());
        if ($req->search) $q->where("name","like","%{$req->search}%");
        if ($req->status) $q->where("status",$req->status);
        return response()->json($q->latest()->get());
    }
    public function store(Request $req) {
        $data = $req->validate(["name"=>"required|string|max:191","description"=>"nullable|string","status"=>"in:active,inactive"]);
        $data["created_by"] = $this->currentBusinessId();
        return response()->json(["success"=>true,"brand"=>Brand::create($data)], 201);
    }
    public function show(Brand $brand) { $this->ensureOwns($brand); return response()->json($brand); }
    public function update(Request $req, Brand $brand) {
        $this->ensureOwns($brand);
        $brand->update($req->validate(["name"=>"required|string|max:191","description"=>"nullable|string","status"=>"in:active,inactive"]));
        return response()->json(["success"=>true,"brand"=>$brand]);
    }
    public function destroy(Brand $brand) { $this->ensureOwns($brand); $brand->delete(); return response()->json(["success"=>true]); }

    public function importLibrary()
    {
        $library = [
            ['name'=>'Samsung','description'=>'Korean electronics and appliances','status'=>'active'],
            ['name'=>'Apple','description'=>'American consumer electronics','status'=>'active'],
            ['name'=>'Nike','description'=>'American sportswear and footwear','status'=>'active'],
            ['name'=>'Adidas','description'=>'German sportswear and footwear','status'=>'active'],
            ['name'=>'Sony','description'=>'Japanese electronics and entertainment','status'=>'active'],
            ['name'=>'LG','description'=>'Korean electronics and appliances','status'=>'active'],
            ['name'=>'HP','description'=>'American computers and printers','status'=>'active'],
            ['name'=>'Dell','description'=>'American computers and technology','status'=>'active'],
            ['name'=>'Lenovo','description'=>'Chinese computers and electronics','status'=>'active'],
            ['name'=>'Canon','description'=>'Japanese cameras and printers','status'=>'active'],
            ['name'=>'Nokia','description'=>'Finnish telecommunications','status'=>'active'],
            ['name'=>'Panasonic','description'=>'Japanese electronics','status'=>'active'],
            ['name'=>'Gucci','description'=>'Italian luxury fashion','status'=>'active'],
            ['name'=>'Rolex','description'=>'Swiss luxury watches','status'=>'active'],
            ['name'=>'Toyota','description'=>'Japanese automotive','status'=>'active'],
        ];
        return response()->json($library);
    }

    public function import(Request $req)
    {
        $data = $req->validate(['items'=>'required|array|min:1','items.*.name'=>'required|string|max:191']);
        $businessId = $this->currentBusinessId();
        $imported = 0; $skipped = 0;
        foreach ($data['items'] as $item) {
            $exists = Brand::where('created_by', $businessId)->where('name', $item['name'])->exists();
            if ($exists) { $skipped++; continue; }
            Brand::create([
                'name' => $item['name'],
                'description' => $item['description'] ?? null,
                'status' => $item['status'] ?? 'active',
                'created_by' => $businessId,
            ]);
            $imported++;
        }
        return response()->json(['success'=>true,'imported'=>$imported,'skipped'=>$skipped,'message'=>"Imported {$imported} brands, skipped {$skipped} duplicates."]);
    }
}
