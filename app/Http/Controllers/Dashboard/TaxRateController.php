<?php
namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\Models\TaxRate;
use Illuminate\Http\Request;
class TaxRateController extends Controller {
    public function index(Request $req) {
        $q = TaxRate::forCurrentUser($this->currentBusinessId());
        if ($req->search) $q->where("name","like","%{$req->search}%");
        if ($req->status) $q->where("status",$req->status);
        return response()->json($q->latest()->get());
    }
    public function store(Request $req) {
        $data = $req->validate(["name"=>"required|string|max:191","rate"=>"required|numeric|min:0","type"=>"in:percentage,fixed","status"=>"in:active,inactive"]);
        $data["created_by"] = $this->currentBusinessId();
        return response()->json(["success"=>true,"taxRate"=>TaxRate::create($data)], 201);
    }
    public function show(TaxRate $taxRate) { $this->ensureOwns($taxRate); return response()->json($taxRate); }
    public function update(Request $req, TaxRate $taxRate) {
        $this->ensureOwns($taxRate);
        $taxRate->update($req->validate(["name"=>"required|string|max:191","rate"=>"required|numeric|min:0","type"=>"in:percentage,fixed","status"=>"in:active,inactive"]));
        return response()->json(["success"=>true,"taxRate"=>$taxRate]);
    }
    public function destroy(TaxRate $taxRate) { $this->ensureOwns($taxRate); $taxRate->delete(); return response()->json(["success"=>true]); }
}
