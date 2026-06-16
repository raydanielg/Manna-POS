<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\BusinessLocation;
use Illuminate\Http\Request;

class BusinessLocationApiController extends Controller {
    public function index() {
        return response()->json(BusinessLocation::orderBy('name')->get());
    }
    public function store(Request $req) {
        $data = $req->validate([
            'name' => 'required|string|max:191',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:30',
            'status' => 'in:active,inactive',
        ]);
        return response()->json(BusinessLocation::create($data), 201);
    }
    public function show(BusinessLocation $businessLocation) {
        return response()->json($businessLocation);
    }
    public function update(Request $req, BusinessLocation $businessLocation) {
        $data = $req->validate([
            'name' => 'required|string|max:191',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:30',
            'status' => 'in:active,inactive',
        ]);
        $businessLocation->update($data);
        return response()->json($businessLocation);
    }
    public function destroy(BusinessLocation $businessLocation) {
        $businessLocation->delete();
        return response()->json(['message'=>'Location deleted']);
    }
}
