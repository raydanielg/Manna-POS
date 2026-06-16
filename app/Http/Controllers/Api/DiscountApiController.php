<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Discount;
use Illuminate\Http\Request;

class DiscountApiController extends Controller {
    public function index(Request $req) {
        $q = Discount::query();
        if ($req->status) $q->where('status',$req->status);
        return response()->json($q->orderBy('name')->get());
    }
    public function store(Request $req) {
        $data = $req->validate([
            'name' => 'required|string|max:191',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:fixed,percentage',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'status' => 'in:active,inactive',
        ]);
        return response()->json(Discount::create($data), 201);
    }
    public function show(Discount $discount) {
        return response()->json($discount);
    }
    public function update(Request $req, Discount $discount) {
        $data = $req->validate([
            'name' => 'required|string|max:191',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:fixed,percentage',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'status' => 'in:active,inactive',
        ]);
        $discount->update($data);
        return response()->json($discount);
    }
    public function destroy(Discount $discount) {
        $discount->delete();
        return response()->json(['message'=>'Discount deleted']);
    }
}
