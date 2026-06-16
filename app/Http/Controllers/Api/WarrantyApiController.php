<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Warranty;
use Illuminate\Http\Request;

class WarrantyApiController extends Controller {
    public function index() {
        return response()->json(Warranty::orderBy('name')->get());
    }
    public function store(Request $req) {
        $data = $req->validate([
            'name' => 'required|string|max:191',
            'duration' => 'required|integer|min:1',
            'duration_unit' => 'required|in:days,months,years',
            'description' => 'nullable|string',
        ]);
        return response()->json(Warranty::create($data), 201);
    }
    public function show(Warranty $warranty) {
        return response()->json($warranty);
    }
    public function update(Request $req, Warranty $warranty) {
        $data = $req->validate([
            'name' => 'required|string|max:191',
            'duration' => 'required|integer|min:1',
            'duration_unit' => 'required|in:days,months,years',
            'description' => 'nullable|string',
        ]);
        $warranty->update($data);
        return response()->json($warranty);
    }
    public function destroy(Warranty $warranty) {
        $warranty->delete();
        return response()->json(['message'=>'Warranty deleted']);
    }
}
