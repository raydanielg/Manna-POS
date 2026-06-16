<?php
namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\Models\Discount;
use Illuminate\Http\Request;
class DiscountController extends Controller {
    public function index(Request $req) {
        $q = Discount::query();
        if ($req->search) $q->where("name","like","%{$req->search}%");
        if ($req->status) $q->where("status",$req->status);
        return response()->json($q->latest()->get());
    }
    public function store(Request $req) {
        $data = $req->validate(["name"=>"required|string|max:191","amount"=>"required|numeric|min:0","type"=>"in:percentage,fixed","starts_at"=>"nullable|date","ends_at"=>"nullable|date|after_or_equal:starts_at","status"=>"in:active,inactive"]);
        return response()->json(["success"=>true,"discount"=>Discount::create($data)], 201);
    }
    public function show(Discount $discount) { return response()->json($discount); }
    public function update(Request $req, Discount $discount) {
        $discount->update($req->validate(["name"=>"required|string|max:191","amount"=>"required|numeric|min:0","type"=>"in:percentage,fixed","starts_at"=>"nullable|date","ends_at"=>"nullable|date|after_or_equal:starts_at","status"=>"in:active,inactive"]));
        return response()->json(["success"=>true,"discount"=>$discount]);
    }
    public function destroy(Discount $discount) { $discount->delete(); return response()->json(["success"=>true]); }
}
