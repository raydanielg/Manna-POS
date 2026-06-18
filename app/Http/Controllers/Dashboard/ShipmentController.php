<?php
namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
class ShipmentController extends Controller {
    public function index(Request $req) {
        $q = Shipment::forCurrentUser($this->currentBusinessId());
        if ($req->search) $q->where(function($sq) use($req){ $sq->where('reference','like',"%{$req->search}%")->orWhere('recipient_name','like',"%{$req->search}%")->orWhere('tracking_number','like',"%{$req->search}%"); });
        if ($req->status) $q->where('status',$req->status);
        return response()->json($q->latest()->get());
    }
    public function store(Request $req) {
        $data = $req->validate(['recipient_name'=>'nullable|string|max:191','shipping_address'=>'nullable|string','carrier'=>'nullable|string|max:100','tracking_number'=>'nullable|string|max:100','ship_date'=>'nullable|date','expected_delivery'=>'nullable|date','status'=>'in:pending,shipped,delivered,cancelled','sale_id'=>'nullable|integer','notes'=>'nullable|string']);
        $data['reference'] = 'SHP-'.strtoupper(Str::random(8));
        $data['created_by'] = $this->currentBusinessId();
        return response()->json(['success'=>true,'shipment'=>Shipment::create($data)], 201);
    }
    public function show(Shipment $shipment) { $this->ensureOwns($shipment); return response()->json($shipment); }
    public function update(Request $req, Shipment $shipment) {
        $this->ensureOwns($shipment);
        $shipment->update($req->validate(['recipient_name'=>'nullable|string|max:191','shipping_address'=>'nullable|string','carrier'=>'nullable|string|max:100','tracking_number'=>'nullable|string|max:100','ship_date'=>'nullable|date','expected_delivery'=>'nullable|date','status'=>'in:pending,shipped,delivered,cancelled','notes'=>'nullable|string']));
        return response()->json(['success'=>true,'shipment'=>$shipment]);
    }
    public function destroy(Shipment $shipment) { $this->ensureOwns($shipment); $shipment->delete(); return response()->json(['success'=>true]); }
}
