<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\SmsCampaign;
use App\Models\SmsCampaignRecipient;
use App\Models\Customer;
use App\Models\SmsTemplate;
use Illuminate\Http\Request;

class SmsCampaignController extends Controller
{
    public function __construct() { $this->middleware('auth'); }

    public function index()
    {
        $campaigns = SmsCampaign::where('user_id', auth()->id())->latest()->get();
        $templates = SmsTemplate::where('is_active', true)->get();
        $customers = Customer::where(function($q) {
            $q->where('created_by', auth()->id())->orWhere('user_id', auth()->id());
        })->whereNotNull('mobile')->get();
        return view('dashboard.sms-campaigns.index', compact('campaigns', 'templates', 'customers'));
    }

    public function store(Request $req)
    {
        $data = $req->validate([
            'name' => 'required|string|max:150',
            'message' => 'required|string|max:1600',
            'customer_ids' => 'required|array|min:1',
            'customer_ids.*' => 'exists:customers,id',
        ]);

        $customers = Customer::whereIn('id', $data['customer_ids'])->get();
        $campaign = SmsCampaign::create([
            'user_id' => auth()->id(),
            'name' => $data['name'],
            'message' => $data['message'],
            'status' => 'draft',
            'recipient_count' => $customers->count(),
        ]);

        foreach ($customers as $c) {
            SmsCampaignRecipient::create([
                'sms_campaign_id' => $campaign->id,
                'customer_id' => $c->id,
                'phone' => $c->mobile,
                'name' => $c->name,
            ]);
        }

        return redirect()->route('dashboard.sms-campaigns')->with('success', 'Campaign created with ' . $customers->count() . ' recipients');
    }

    public function show(SmsCampaign $campaign)
    {
        $this->authorize($campaign);
        $campaign->load('recipients.customer');
        return view('dashboard.sms-campaigns.show', compact('campaign'));
    }

    public function send(Request $req, SmsCampaign $campaign)
    {
        $this->authorize($campaign);
        // Simulate sending - in real app, integrate with SMS gateway
        $sent = 0;
        foreach ($campaign->recipients()->where('status', 'pending')->get() as $r) {
            // TODO: Integrate actual SMS gateway here
            $r->update(['status' => 'sent', 'sent_at' => now()]);
            $sent++;
        }
        $campaign->update([
            'status' => 'sent',
            'sent_at' => now(),
            'sent_count' => $campaign->recipients()->where('status', 'sent')->count(),
        ]);
        return redirect()->route('dashboard.sms-campaigns')->with('success', "Campaign sent! {$sent} messages delivered.");
    }

    public function destroy(SmsCampaign $campaign)
    {
        $this->authorize($campaign);
        $campaign->delete();
        return redirect()->route('dashboard.sms-campaigns')->with('success', 'Campaign deleted');
    }

    private function authorize($campaign)
    {
        if ($campaign->user_id !== auth()->id()) abort(403);
    }
}
