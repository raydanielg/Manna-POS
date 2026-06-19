<?php
namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;
class UnitController extends Controller {
    public function index(Request $req) {
        $q = Unit::withCount("products")->forCurrentUser($this->currentBusinessId());
        if ($req->search) $q->where(function($sq) use($req){ $sq->where("name","like","%{$req->search}%")->orWhere("short_name","like","%{$req->search}%"); });
        if ($req->allow_decimal !== null && $req->allow_decimal !== '') $q->where('allow_decimal', $req->allow_decimal);
        return response()->json($q->latest()->get());
    }
    public function store(Request $req) {
        $data = $req->validate(["name"=>"required|string|max:191","short_name"=>"required|string|max:20","allow_decimal"=>"boolean"]);
        $data["created_by"] = $this->currentBusinessId();
        return response()->json(["success"=>true,"unit"=>Unit::create($data)], 201);
    }
    public function show(Unit $unit) { $this->ensureOwns($unit); return response()->json($unit); }
    public function update(Request $req, Unit $unit) {
        $this->ensureOwns($unit);
        $unit->update($req->validate(["name"=>"required|string|max:191","short_name"=>"required|string|max:20","allow_decimal"=>"boolean"]));
        return response()->json(["success"=>true,"unit"=>$unit]);
    }
    public function destroy(Unit $unit) { $this->ensureOwns($unit); $unit->delete(); return response()->json(["success"=>true]); }

    public function importLibrary()
    {
        $library = [
            ['name'=>'Kilogram','short_name'=>'kg','allow_decimal'=>1],
            ['name'=>'Gram','short_name'=>'g','allow_decimal'=>1],
            ['name'=>'Liter','short_name'=>'L','allow_decimal'=>1],
            ['name'=>'Milliliter','short_name'=>'ml','allow_decimal'=>1],
            ['name'=>'Meter','short_name'=>'m','allow_decimal'=>1],
            ['name'=>'Centimeter','short_name'=>'cm','allow_decimal'=>1],
            ['name'=>'Piece','short_name'=>'pc','allow_decimal'=>0],
            ['name'=>'Box','short_name'=>'box','allow_decimal'=>0],
            ['name'=>'Pack','short_name'=>'pack','allow_decimal'=>0],
            ['name'=>'Dozen','short_name'=>'dz','allow_decimal'=>0],
            ['name'=>'Pair','short_name'=>'pr','allow_decimal'=>0],
            ['name'=>'Set','short_name'=>'set','allow_decimal'=>0],
            ['name'=>'Roll','short_name'=>'roll','allow_decimal'=>1],
            ['name'=>'Bottle','short_name'=>'btl','allow_decimal'=>0],
            ['name'=>'Bag','short_name'=>'bag','allow_decimal'=>0],
            ['name'=>'Carton','short_name'=>'ctn','allow_decimal'=>0],
        ];
        return response()->json($library);
    }

    public function import(Request $req)
    {
        $data = $req->validate(['items'=>'required|array|min:1','items.*.name'=>'required|string|max:191','items.*.short_name'=>'required|string|max:20']);
        $businessId = $this->currentBusinessId();
        $imported = 0; $skipped = 0;
        foreach ($data['items'] as $item) {
            $exists = Unit::where('created_by', $businessId)->where(function($q) use($item){ $q->where('name', $item['name'])->orWhere('short_name', $item['short_name']); })->exists();
            if ($exists) { $skipped++; continue; }
            Unit::create([
                'name' => $item['name'],
                'short_name' => $item['short_name'],
                'allow_decimal' => $item['allow_decimal'] ?? 0,
                'created_by' => $businessId,
            ]);
            $imported++;
        }
        return response()->json(['success'=>true,'imported'=>$imported,'skipped'=>$skipped,'message'=>"Imported {$imported} units, skipped {$skipped} duplicates."]);
    }
}
