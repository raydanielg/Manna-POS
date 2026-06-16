<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Discount;
use Illuminate\Http\Request;

class DiscountApiController extends Controller {
    use UserIdTrait;
    public function index() {
        return response()->json(Discount::where('created_by', $this->userId())->orderByDesc('created_at')->get());
    }
    public function store(Request $req) {
        $data = $req->validate([
            'name' => 'required|string|max:191',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:percentage,fixed',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'status' => 'nullable|in:active,inactive',
        ]);
        $data['created_by'] = $this->userId();
        return response()->json(Discount::create($data), 201);
    }
    public function show($id) {
        return response()->json(Discount::where('created_by', $this->userId())->findOrFail($id));
    }
    public function update(Request $req, $id) {
        $d = Discount::where('created_by', $this->userId())->findOrFail($id);
        $data = $req->validate([
            'name' => 'required|string|max:191',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:percentage,fixed',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'status' => 'nullable|in:active,inactive',
        ]);
        $d->update($data);
        return response()->json($d->fresh());
    }
    public function destroy($id) {
        Discount::where('created_by', $this->userId())->where('id',$id)->delete();
        return response()->json(['message'=>'Discount deleted']);
    }
}
