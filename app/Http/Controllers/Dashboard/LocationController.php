<?php
namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\Models\BusinessLocation;
use Illuminate\Http\Request;

class LocationController extends Controller {

    public function index(Request $req) {
        $q = BusinessLocation::withCount('staff');
        if ($req->search) {
            $q->where('name','like',"%{$req->search}%")
              ->orWhere('city','like',"%{$req->search}%")
              ->orWhere('code','like',"%{$req->search}%");
        }
        return response()->json($q->latest()->get());
    }

    public function store(Request $req) {
        $data = $req->validate([
            'name'      => 'required|string|max:191',
            'code'      => 'nullable|string|max:30',
            'address'   => 'nullable|string|max:255',
            'city'      => 'nullable|string|max:100',
            'phone'     => 'nullable|string|max:30',
            'email'     => 'nullable|email|max:191',
            'is_active' => 'boolean',
            'notes'     => 'nullable|string',
        ]);
        $data['owner_id'] = auth()->id();
        $loc = BusinessLocation::create($data);
        return response()->json(['success' => true, 'location' => $loc->loadCount('staff')], 201);
    }

    public function show(BusinessLocation $location) {
        return response()->json($location->loadCount('staff'));
    }

    public function update(Request $req, BusinessLocation $location) {
        $data = $req->validate([
            'name'      => 'required|string|max:191',
            'code'      => 'nullable|string|max:30',
            'address'   => 'nullable|string|max:255',
            'city'      => 'nullable|string|max:100',
            'phone'     => 'nullable|string|max:30',
            'email'     => 'nullable|email|max:191',
            'is_active' => 'boolean',
            'notes'     => 'nullable|string',
        ]);
        $location->update($data);
        return response()->json(['success' => true, 'location' => $location->loadCount('staff')]);
    }

    public function destroy(BusinessLocation $location) {
        if ($location->staff()->count() > 0) {
            return response()->json(['success' => false, 'message' => 'Cannot delete location with assigned staff. Reassign them first.'], 422);
        }
        $location->delete();
        return response()->json(['success' => true]);
    }
}
