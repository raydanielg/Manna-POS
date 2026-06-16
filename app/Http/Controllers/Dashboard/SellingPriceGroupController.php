<?php
namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\Models\SellingPriceGroup;
use Illuminate\Http\Request;
class SellingPriceGroupController extends Controller {
    public function index(Request $req) {
        $q = SellingPriceGroup::query();
        if ($req->search) $q->where('name','like',"%{$req->search}%");
        if ($req->status) $q->where('status',$req->status);
        return response()->json($q->latest()->get());
    }
    public function store(Request $req) {
        $data = $req->validate(['name'=>'required|string|max:191|unique:selling_price_groups,name','description'=>'nullable|string','percentage'=>'required|numeric|min:0|max:100','type'=>'in:markup,discount','status'=>'in:active,inactive']);
        return response()->json(['success'=>true,'group'=>SellingPriceGroup::create($data)], 201);
    }
    public function show(SellingPriceGroup $sellingPriceGroup) { return response()->json($sellingPriceGroup); }
    public function update(Request $req, SellingPriceGroup $sellingPriceGroup) {
        $sellingPriceGroup->update($req->validate(['name'=>"required|string|max:191|unique:selling_price_groups,name,{$sellingPriceGroup->id}",'description'=>'nullable|string','percentage'=>'required|numeric|min:0|max:100','type'=>'in:markup,discount','status'=>'in:active,inactive']));
        return response()->json(['success'=>true,'group'=>$sellingPriceGroup]);
    }
    public function destroy(SellingPriceGroup $sellingPriceGroup) { $sellingPriceGroup->delete(); return response()->json(['success'=>true]); }
}
