<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\BusinessCategory;
use App\Models\BusinessVerification;
use Illuminate\Http\Request;

class AdminBusinessController extends Controller
{
    public function index()
    {
        return view('admin.business.index');
    }

    public function list(Request $req)
    {
        $q = Business::with(['user:id,name,email', 'category:id,name']);
        if ($req->search) {
            $q->where(function($q) use ($req) {
                $q->where('business_name','like',"%{$req->search}%")
                  ->orWhere('email','like',"%{$req->search}%")
                  ->orWhere('phone','like',"%{$req->search}%")
                  ->orWhere('registration_number','like',"%{$req->search}%");
            });
        }
        if ($req->status) $q->where('status', $req->status);
        if ($req->is_verified !== null) $q->where('is_verified', $req->is_verified);
        if ($req->category_id) $q->where('business_category_id', $req->category_id);
        return response()->json($q->latest()->get()->map(fn($b) => [
            'id' => $b->id, 'business_name' => $b->business_name, 'business_type' => $b->business_type,
            'category' => $b->category->name ?? 'N/A', 'email' => $b->email, 'phone' => $b->phone,
            'city' => $b->business_city, 'country' => $b->business_country,
            'owner' => $b->user->name ?? 'N/A', 'status' => $b->status,
            'is_verified' => $b->is_verified, 'created_at' => $b->created_at->format('Y-m-d'),
        ]));
    }

    public function show(Business $business)
    {
        return response()->json($business->load(['user:id,name,email,phone', 'category', 'verifications']));
    }

    public function update(Request $req, Business $business)
    {
        $data = $req->validate([
            'business_name'    => 'required|string|max:191',
            'business_type'    => 'nullable|string|max:50',
            'business_category_id' => 'nullable|exists:business_categories,id',
            'business_address' => 'nullable|string|max:255',
            'business_city'    => 'nullable|string|max:100',
            'business_country' => 'nullable|string|max:100',
            'phone'            => 'nullable|string|max:20',
            'email'            => 'nullable|email',
            'website'          => 'nullable|string|max:191',
            'registration_number' => 'nullable|string|max:100',
            'tax_number'       => 'nullable|string|max:100',
            'status'           => 'nullable|string|max:20',
            'notes'            => 'nullable|string',
        ]);
        $business->update($data);
        return response()->json(['success'=>true,'business'=>$business]);
    }

    public function verify(Request $req, Business $business)
    {
        $business->update([
            'is_verified'  => true,
            'verified_at'  => now(),
            'verified_by'  => auth()->id(),
            'status'       => 'active',
        ]);
        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(), 'action' => 'verified_business',
            'resource_type' => 'business', 'resource_id' => $business->id,
            'description' => "Verified business: {$business->business_name}",
        ]);
        return response()->json(['success'=>true,'message'=>'Business verified successfully']);
    }

    public function reject(Request $req, Business $business)
    {
        $req->validate(['notes' => 'nullable|string']);
        $business->update(['status' => 'rejected', 'notes' => $req->notes]);
        return response()->json(['success'=>true,'message'=>'Business rejected']);
    }

    public function destroy(Business $business)
    {
        $business->delete();
        return response()->json(['success'=>true,'message'=>'Business deleted']);
    }

    // Categories
    public function categories()
    {
        return view('admin.business.categories');
    }

    public function categoriesList(Request $req)
    {
        return response()->json(BusinessCategory::orderBy('sort_order')->get());
    }

    public function categoriesStore(Request $req)
    {
        $data = $req->validate([
            'name' => 'required|string|max:191|unique:business_categories,name',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);
        $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
        $data['is_active'] = $data['is_active'] ?? true;
        return response()->json(['success'=>true,'category'=>BusinessCategory::create($data)], 201);
    }

    public function categoriesUpdate(Request $req, BusinessCategory $category)
    {
        $data = $req->validate([
            'name' => "required|string|max:191|unique:business_categories,name,{$category->id}",
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);
        $category->update($data);
        return response()->json(['success'=>true,'category'=>$category]);
    }

    public function categoriesDestroy(BusinessCategory $category)
    {
        $category->delete();
        return response()->json(['success'=>true]);
    }

    // Verifications
    public function verifications()
    {
        return view('admin.business.verifications');
    }

    public function verificationsList(Request $req)
    {
        $q = BusinessVerification::with(['business:id,business_name', 'reviewer:id,name']);
        if ($req->status) $q->where('status', $req->status);
        return response()->json($q->latest()->get());
    }

    public function verificationsApprove(BusinessVerification $verification)
    {
        $verification->update(['status'=>'approved','reviewed_by'=>auth()->id(),'reviewed_at'=>now()]);
        $verification->business->update(['is_verified'=>true,'verified_at'=>now(),'verified_by'=>auth()->id()]);
        return response()->json(['success'=>true]);
    }

    public function verificationsReject(Request $req, BusinessVerification $verification)
    {
        $req->validate(['notes'=>'nullable|string']);
        $verification->update(['status'=>'rejected','notes'=>$req->notes,'reviewed_by'=>auth()->id(),'reviewed_at'=>now()]);
        return response()->json(['success'=>true]);
    }
}
