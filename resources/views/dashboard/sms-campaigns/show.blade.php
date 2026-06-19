@extends('layouts.dashboard')
@section('page_title','Campaign Details')
@section('content')
<div class="dash-content">
<div class="sms-wrap">
  <div class="page-card" style="max-width:900px;margin:0 auto;">
    <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;">
      <div>
        <div class="card-title">{{ $campaign->name }}</div>
        <p style="font-size:.75rem;color:#64748b;margin-top:.25rem;"><span class="status {{ $campaign->status }}">{{ ucfirst($campaign->status) }}</span> &middot; {{ $campaign->created_at->format('M d, Y H:i') }}</p>
      </div>
      <div style="display:flex;gap:.5rem;">
        @if($campaign->status === 'draft')
        <form method="POST" action="{{ route('dashboard.sms-campaigns.send', $campaign) }}" style="display:inline;">@csrf
          <button type="submit" class="btn btn-primary btn-sm" style="gap:.35rem;">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg> Send Now
          </button>
        </form>
        @endif
        <a href="{{ route('dashboard.sms-campaigns') }}" class="btn btn-secondary btn-sm">Back</a>
      </div>
    </div>
    <div class="card-body">
      <div style="background:#f8fafc;border-radius:10px;padding:1rem;margin-bottom:1.5rem;border:1px solid #e2e8f0;">
        <div style="font-size:.68rem;font-weight:700;text-transform:uppercase;color:#94a3b8;margin-bottom:.35rem;">Message</div>
        <div style="font-size:.9rem;color:#0f172a;white-space:pre-wrap;">{{ $campaign->message }}</div>
      </div>

      <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-bottom:1.5rem;">
        <div style="text-align:center;padding:.75rem;background:#fff;border:1px solid #eef2f6;border-radius:10px;"><div style="font-size:.65rem;color:#94a3b8;font-weight:700;uppercase;">Total</div><div style="font-size:1.2rem;font-weight:800;color:#0f172a;">{{ $campaign->recipient_count }}</div></div>
        <div style="text-align:center;padding:.75rem;background:#fff;border:1px solid #eef2f6;border-radius:10px;"><div style="font-size:.65rem;color:#94a3b8;font-weight:700;uppercase;">Sent</div><div style="font-size:1.2rem;font-weight:800;color:#22c55e;">{{ $campaign->sent_count }}</div></div>
        <div style="text-align:center;padding:.75rem;background:#fff;border:1px solid #eef2f6;border-radius:10px;"><div style="font-size:.65rem;color:#94a3b8;font-weight:700;uppercase;">Failed</div><div style="font-size:1.2rem;font-weight:800;color:#ef4444;">{{ $campaign->failed_count }}</div></div>
        <div style="text-align:center;padding:.75rem;background:#fff;border:1px solid #eef2f6;border-radius:10px;"><div style="font-size:.65rem;color:#94a3b8;font-weight:700;uppercase;">Pending</div><div style="font-size:1.2rem;font-weight:800;color:#f59e0b;">{{ $campaign->recipient_count - $campaign->sent_count - $campaign->failed_count }}</div></div>
      </div>

      <h4 style="font-size:.85rem;font-weight:800;color:#0f172a;margin-bottom:.75rem;">Recipients</h4>
      @if($campaign->recipients->count())
      <table class="mf-table">
        <thead><tr><th>Name</th><th>Phone</th><th>Status</th><th>Sent At</th></tr></thead>
        <tbody>
          @foreach($campaign->recipients as $r)
          <tr>
            <td>{{ $r->name ?? 'N/A' }}</td>
            <td>{{ $r->phone }}</td>
            <td><span class="status {{ $r->status }}">{{ ucfirst($r->status) }}</span></td>
            <td style="font-size:.72rem;color:#64748b;">{{ $r->sent_at ? $r->sent_at->format('M d, Y H:i') : '-' }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
      @else
      <div style="padding:2rem;text-align:center;color:#94a3b8;">No recipients found.</div>
      @endif
    </div>
  </div>
</div>
</div>
@endsection
