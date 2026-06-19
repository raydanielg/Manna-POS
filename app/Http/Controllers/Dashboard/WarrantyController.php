<?php
namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\Models\Warranty;
use Illuminate\Http\Request;
class WarrantyController extends Controller {
    public function index(Request $req) {
        $q = Warranty::forCurrentUser($this->currentBusinessId());
        if ($req->search) $q->where("name","like","%{$req->search}%");
        return response()->json($q->latest()->get());
    }
    public function store(Request $req) {
        $data = $req->validate(["name"=>"required|string|max:191","duration"=>"required|integer|min:1","duration_unit"=>"in:days,months,years","description"=>"nullable|string"]);
        $data["created_by"] = $this->currentBusinessId();
        return response()->json(["success"=>true,"warranty"=>Warranty::create($data)], 201);
    }
    public function show(Warranty $warranty) { $this->ensureOwns($warranty); return response()->json($warranty); }
    public function update(Request $req, Warranty $warranty) {
        $this->ensureOwns($warranty);
        $warranty->update($req->validate(["name"=>"required|string|max:191","duration"=>"required|integer|min:1","duration_unit"=>"in:days,months,years","description"=>"nullable|string"]));
        return response()->json(["success"=>true,"warranty"=>$warranty]);
    }
    public function destroy(Warranty $warranty) { $this->ensureOwns($warranty); $warranty->delete(); return response()->json(["success"=>true]); }

    public function importLibrary()
    {
        $library = [
            ['name'=>'1 Month Warranty','duration'=>1,'duration_unit'=>'months','description'=>'Standard 30-day coverage'],
            ['name'=>'3 Month Warranty','duration'=>3,'duration_unit'=>'months','description'=>'Quarter-year coverage'],
            ['name'=>'6 Month Warranty','duration'=>6,'duration_unit'=>'months','description'=>'Half-year coverage'],
            ['name'=>'1 Year Warranty','duration'=>1,'duration_unit'=>'years','description'=>'Full year manufacturer coverage'],
            ['name'=>'2 Year Warranty','duration'=>2,'duration_unit'=>'years','description'=>'Extended two-year coverage'],
            ['name'=>'3 Year Warranty','duration'=>3,'duration_unit'=>'years','description'=>'Extended three-year coverage'],
            ['name'=>'5 Year Warranty','duration'=>5,'duration_unit'=>'years','description'=>'Long-term five-year coverage'],
            ['name'=>'Lifetime Warranty','duration'=>99,'duration_unit'=>'years','description'=>'Lifetime limited coverage'],
            ['name'=>'30 Day Money Back','duration'=>30,'duration_unit'=>'days','description'=>'Satisfaction guarantee return policy'],
            ['name'=>'90 Day Warranty','duration'=>90,'duration_unit'=>'days','description'=>'Three-month coverage'],
        ];
        return response()->json($library);
    }

    public function import(Request $req)
    {
        $data = $req->validate(['items'=>'required|array|min:1','items.*.name'=>'required|string|max:191','items.*.duration'=>'required|integer|min:1','items.*.duration_unit'=>'in:days,months,years']);
        $businessId = $this->currentBusinessId();
        $imported = 0; $skipped = 0;
        foreach ($data['items'] as $item) {
            $exists = Warranty::where('created_by', $businessId)->where('name', $item['name'])->exists();
            if ($exists) { $skipped++; continue; }
            Warranty::create([
                'name' => $item['name'],
                'duration' => $item['duration'],
                'duration_unit' => $item['duration_unit'],
                'description' => $item['description'] ?? null,
                'created_by' => $businessId,
            ]);
            $imported++;
        }
        return response()->json(['success'=>true,'imported'=>$imported,'skipped'=>$skipped,'message'=>"Imported {$imported} warranties, skipped {$skipped} duplicates."]);
    }
}
