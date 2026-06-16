<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;

class UnitApiController extends Controller {
    public function index() {
        return response()->json(Unit::orderBy('name')->get());
    }
    public function store(Request $req) {
        $data = $req->validate([
            'name' => 'required|string|max:191|unique:units,name',
            'short_name' => 'nullable|string|max:20',
            'allow_decimal' => 'boolean',
        ]);
        return response()->json(Unit::create($data), 201);
    }
    public function show(Unit $unit) {
        return response()->json($unit);
    }
    public function update(Request $req, Unit $unit) {
        $data = $req->validate([
            'name' => 'required|string|max:191|unique:units,name,'.$unit->id,
            'short_name' => 'nullable|string|max:20',
            'allow_decimal' => 'boolean',
        ]);
        $unit->update($data);
        return response()->json($unit);
    }
    public function destroy(Unit $unit) {
        $unit->delete();
        return response()->json(['message'=>'Unit deleted']);
    }
}
