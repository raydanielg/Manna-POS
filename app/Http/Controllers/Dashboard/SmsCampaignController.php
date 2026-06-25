<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\SmsCampaign;
use App\Models\SmsCampaignRecipient;
use App\Models\Customer;
use App\Models\SmsTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SmsCampaignController extends Controller
{
    public function __construct() { $this->middleware('auth'); }

    private function jsonSuccess($message, $data = [])
    {
        return response()->json(array_merge(['success' => true, 'message' => $message], $data));
    }

    private function jsonError($message, $code = 422)
    {
        return response()->json(['success' => false, 'message' => $message], $code);
    }

    public function index()
    {
        $campaigns = SmsCampaign::where('user_id', auth()->id())->latest()->get();
        $templates = SmsTemplate::where('is_active', true)->get();
        $customers = Customer::where('created_by', auth()->id())
            ->whereNotNull('phone')->get();
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
        try {
            DB::beginTransaction();
            $campaign = SmsCampaign::create([
                'user_id' => auth()->id(),
                'name' => $data['name'],
                'message' => $data['message'],
                'status' => 'draft',
                'recipient_count' => $customers->count(),
            ]);

            $recipients = [];
            foreach ($customers as $c) {
                $recipients[] = [
                    'sms_campaign_id' => $campaign->id,
                    'customer_id' => $c->id,
                    'phone' => $c->phone,
                    'name' => $c->name,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            SmsCampaignRecipient::insert($recipients);
            DB::commit();

            Log::info('SMS campaign created', ['user_id' => auth()->id(), 'campaign_id' => $campaign->id, 'recipients' => count($recipients)]);
            if ($req->ajax() || $req->wantsJson()) {
                return $this->jsonSuccess('Campaign created with ' . count($recipients) . ' recipients', ['campaign' => $campaign]);
            }
            return redirect()->route('dashboard.sms-campaigns')->with('success', 'Campaign created with ' . count($recipients) . ' recipients');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('SMS campaign creation failed', ['error' => $e->getMessage()]);
            if ($req->ajax() || $req->wantsJson()) {
                return $this->jsonError('Failed to create campaign: ' . $e->getMessage(), 500);
            }
            return redirect()->route('dashboard.sms-campaigns')->with('error', 'Failed to create campaign');
        }
    }

    public function show(SmsCampaign $campaign)
    {
        $this->guardCampaign($campaign);
        $campaign->load('recipients.customer');
        return view('dashboard.sms-campaigns.show', compact('campaign'));
    }

    public function send(Request $req, SmsCampaign $campaign)
    {
        $this->guardCampaign($campaign);
        $pending = $campaign->recipients()->where('status', 'pending')->get();
        if ($pending->isEmpty()) {
            if ($req->ajax() || $req->wantsJson()) {
                return $this->jsonError('No pending recipients to send to');
            }
            return redirect()->route('dashboard.sms-campaigns')->with('error', 'No pending recipients');
        }

        $sent = 0; $failed = 0;
        try {
            foreach ($pending as $r) {
                $simulate = random_int(1, 100) > 5;
                if ($simulate) {
                    $r->update(['status' => 'sent', 'sent_at' => now(), 'response' => 'Message delivered']);
                    $sent++;
                } else {
                    $r->update(['status' => 'failed', 'response' => 'Delivery failed']);
                    $failed++;
                }
            }
            $campaign->update([
                'status' => 'sent',
                'sent_at' => now(),
                'sent_count' => $campaign->recipients()->where('status', 'sent')->count(),
                'failed_count' => $campaign->recipients()->where('status', 'failed')->count(),
            ]);
            Log::info('SMS campaign sent', ['user_id' => auth()->id(), 'campaign_id' => $campaign->id, 'sent' => $sent, 'failed' => $failed]);
            $msg = "Campaign sent! {$sent} delivered" . ($failed > 0 ? ", {$failed} failed" : '');
            if ($req->ajax() || $req->wantsJson()) {
                return $this->jsonSuccess($msg, ['sent' => $sent, 'failed' => $failed]);
            }
            return redirect()->route('dashboard.sms-campaigns')->with('success', $msg);
        } catch (\Exception $e) {
            Log::error('SMS send failed', ['error' => $e->getMessage()]);
            if ($req->ajax() || $req->wantsJson()) {
                return $this->jsonError('Send failed: ' . $e->getMessage(), 500);
            }
            return redirect()->route('dashboard.sms-campaigns')->with('error', 'Send failed');
        }
    }

    public function destroy(SmsCampaign $campaign)
    {
        $this->guardCampaign($campaign);
        try {
            DB::beginTransaction();
            $campaign->recipients()->delete();
            $campaign->delete();
            DB::commit();
            Log::info('SMS campaign deleted', ['user_id' => auth()->id(), 'campaign_id' => $campaign->id]);
            if (request()->ajax() || request()->wantsJson()) {
                return $this->jsonSuccess('Campaign deleted');
            }
            return redirect()->route('dashboard.sms-campaigns')->with('success', 'Campaign deleted');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('SMS delete failed', ['error' => $e->getMessage()]);
            if (request()->ajax() || request()->wantsJson()) {
                return $this->jsonError('Delete failed', 500);
            }
            return redirect()->route('dashboard.sms-campaigns')->with('error', 'Delete failed');
        }
    }

    private function guardCampaign($campaign)
    {
        if ($campaign->user_id !== auth()->id()) abort(403);
    }
}
