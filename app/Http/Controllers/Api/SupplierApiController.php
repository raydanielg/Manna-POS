<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierApiController extends Controller {
    use UserIdTrait;
    public function index(Request $req) {
        $q = Supplier::where('created_by', $this->userId());
        if ($req->search) $q->where(function($qq) use ($req) { $qq->where('name','like','%'.$req->search.'%')->orWhere('company','like','%'.$req->search.'%')->orWhere('phone','like','%'.$req->search.'%'); });
        return response()->json($q->orderBy('name')->get());
    }
    public function store(Request $req) {
        $data = $req->validate([
            'name' => 'required|string|max:191',
            'company' => 'nullable|string|max:191',
            'email' => 'nullable|email|max:191',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'tax_number' => 'nullable|string|max:100',
            'credit_limit' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:active,inactive',
            'notes' => 'nullable|string',
        ]);
        $data['created_by'] = $this->userId();
        return response()->json(Supplier::create($data), 201);
    }
    public function show($id) {
        return response()->json(Supplier::where('created_by', $this->userId())->findOrFail($id));
    }
    public function update(Request $req, $id) {
        $s = Supplier::where('created_by', $this->userId())->findOrFail($id);
        $data = $req->validate([
            'name' => 'required|string|max:191',
            'company' => 'nullable|string|max:191',
            'email' => 'nullable|email|max:191',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'tax_number' => 'nullable|string|max:100',
            'credit_limit' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:active,inactive',
            'notes' => 'nullable|string',
        ]);
        $s->update($data);
        return response()->json($s->fresh());
    }
    public function destroy($id) {
        Supplier::where('created_by', $this->userId())->where('id',$id)->delete();
        return response()->json(['message'=>'Supplier deleted']);
    }
}
