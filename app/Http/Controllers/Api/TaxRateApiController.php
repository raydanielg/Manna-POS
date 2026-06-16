<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\TaxRate;
use Illuminate\Http\Request;

class TaxRateApiController extends Controller {
    public function index() {
        return response()->json(TaxRate::orderBy('name')->get());
    }
    public function store(Request $req) {
        $data = $req->validate([
            'name' => 'required|string|max:191',
            'rate' => 'required|numeric|min:0|max:100',
            'type' => 'required|in:inclusive,exclusive',
            'status' => 'in:active,inactive',
        ]);
        return response()->json(TaxRate::create($data), 201);
    }
    public function show(TaxRate $taxRate) {
        return response()->json($taxRate);
    }
    public function update(Request $req, TaxRate $taxRate) {
        $data = $req->validate([
            'name' => 'required|string|max:191',
            'rate' => 'required|numeric|min:0|max:100',
            'type' => 'required|in:inclusive,exclusive',
            'status' => 'in:active,inactive',
        ]);
        $taxRate->update($data);
        return response()->json($taxRate);
    }
    public function destroy(TaxRate $taxRate) {
        $taxRate->delete();
        return response()->json(['message'=>'Tax rate deleted']);
    }
}
