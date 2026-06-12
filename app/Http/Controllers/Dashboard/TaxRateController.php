<?php
namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\Models\TaxRate;
use Illuminate\Http\Request;
class TaxRateController extends Controller {
    public function index(Request $req) {
        if ($req->ajax()) {
            $q = TaxRate::query();
            if ($req->search) $q->where("name","like","%{$req->search}%");
            return response()->json($q->latest()->get());
        }
        return view("dashboard.settings.tax-rates");
    }
    public function store(Request $req) {
        $data = $req->validate(["name"=>"required|string|max:191","rate"=>"required|numeric|min:0","type"=>"in:percentage,fixed","status"=>"in:active,inactive"]);
        return response()->json(["success"=>true,"taxRate"=>TaxRate::create($data)], 201);
    }
    public function show(TaxRate $taxRate) { return response()->json($taxRate); }
    public function update(Request $req, TaxRate $taxRate) {
        $taxRate->update($req->validate(["name"=>"required|string|max:191","rate"=>"required|numeric|min:0","type"=>"in:percentage,fixed","status"=>"in:active,inactive"]));
        return response()->json(["success"=>true,"taxRate"=>$taxRate]);
    }
    public function destroy(TaxRate $taxRate) { $taxRate->delete(); return response()->json(["success"=>true]); }
}
