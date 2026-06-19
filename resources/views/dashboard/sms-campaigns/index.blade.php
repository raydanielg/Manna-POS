@extends('layouts.dashboard')
@section('page_title','SMS Campaigns')
@section('page_styles')
<style>
.sms-wrap{max-width:1100px;margin:0 auto;}
.sms-hero{background:linear-gradient(135deg,#0f172a,#1e3a8a);border-radius:16px;padding:1.5rem 2rem;color:#fff;margin-bottom:1.5rem;position:relative;overflow:hidden;}
.sms-hero h1{font-size:1.2rem;font-weight:800;position:relative;z-index:1;}
.sms-hero p{font-size:.78rem;opacity:.8;margin-top:.25rem;position:relative;z-index:1;}
.recipient-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:.6rem;max-height:220px;overflow-y:auto;padding:.5rem;border:1px solid #e2e8f0;border-radius:10px;background:#fafbff;}
.recipient-chip{display:flex;align-items:center;gap:.5rem;padding:.35rem .65rem;background:#fff;border:1px solid #e2e8f0;border-radius:8px;font-size:.78rem;}
.recipient-chip input{margin:0;}
.char-count{font-size:.68rem;color:#94a3b8;text-align:right;margin-top:.25rem;}
.char-count.warning{color:#f59e0b;}
.char-count.danger{color:#ef4444;}
</style>
@endsection
@section('content')
<div class="dash-content">
<div class="sms-wrap">

  <div class="sms-hero">
    <h1>SMS Campaigns</h1>
    <p>Send bulk messages to your customers</p>
  </div>

  <div class="mf-grid" style="grid-template-columns:1fr 1.4fr;align-items:start;">
    {{-- Create Campaign --}}
    <div class="page-card">
      <div class="card-header"><div class="card-title">New Campaign</div></div>
      <div class="card-body">
        <form method="POST" action="{{ route('dashboard.sms-campaigns.store') }}" id="campaignForm">
          @csrf
          <div class="form-group">
            <label class="form-label">Campaign Name *</label>
            <input name="name" class="form-control" placeholder="e.g. New Year Sale" required>
          </div>
          <div class="form-group">
            <label class="form-label">Message *</label>
            <textarea name="message" id="smsMessage" class="form-control" rows="4" maxlength="1600" placeholder="Type your message here..." required oninput="countChars()"></textarea>
            <div class="char-count" id="charCount">0 / 1600</div>
          </div>
          <div class="form-group">
            <label class="form-label">Select Template (optional)</label>
            <select class="form-control" onchange="document.getElementById('smsMessage').value = this.value;countChars();">
              <option value="">-- Choose template --</option>
              @foreach($templates as $t)
              <option value="{{ $t->message }}">{{ $t->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Select Recipients *</label>
            <div style="display:flex;gap:.5rem;margin-bottom:.5rem;">
              <button type="button" class="btn btn-sm btn-secondary" onclick="document.querySelectorAll('.rec-check').forEach(c=>c.checked=true)">Select All</button>
              <button type="button" class="btn btn-sm btn-secondary" onclick="document.querySelectorAll('.rec-check').forEach(c=>c.checked=false)">Clear</button>
            </div>
            <div class="recipient-grid">
              @foreach($customers as $c)
              <label class="recipient-chip" style="cursor:pointer;">
                <input type="checkbox" name="customer_ids[]" value="{{ $c->id }}" class="rec-check" style="cursor:pointer;">
                <span>{{ $c->name }} <span style="color:#94a3b8;">({{ $c->mobile }})</span></span>
              </label>
              @endforeach
            </div>
          </div>
          <div style="display:flex;justify-content:flex-end;">
            <button type="submit" class="btn btn-primary" style="gap:.35rem;">
              <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
              Create Campaign
            </button>
          </div>
        </form>
      </div>
    </div>

    {{-- Campaign List --}}
    <div class="page-card">
      <div class="card-header"><div class="card-title">Campaign History</div></div>
      <div class="card-body" style="padding:0;">
        @if($campaigns->count())
        <table class="mf-table">
          <thead>
            <tr><th>Name</th><th>Status</th><th>Recipients</th><th>Sent</th><th>Date</th><th style="text-align:right;">Actions</th></tr>
          </thead>
          <tbody>
            @foreach($campaigns as $c)
            <tr>
              <td><strong style="color:#0f172a;">{{ $c->name }}</strong><br><span style="font-size:.7rem;color:#94a3b8;">{{ Str::limit($c->message, 40) }}</span></td>
              <td><span class="status {{ $c->status }}">{{ ucfirst($c->status) }}</span></td>
              <td>{{ $c->recipient_count }}</td>
              <td>{{ $c->sent_count }}</td>
              <td style="font-size:.72rem;color:#64748b;">{{ $c->created_at->format('M d, Y') }}</td>
              <td style="text-align:right;">
                <a href="{{ route('dashboard.sms-campaigns.show', $c) }}" class="btn btn-view btn-sm">View</a>
                @if($c->status === 'draft')
                <form method="POST" action="{{ route('dashboard.sms-campaigns.send', $c) }}" style="display:inline;" onsubmit="return confirm('Send this campaign now?');">@csrf
                  <button type="submit" class="btn btn-edit btn-sm">Send</button>
                </form>
                @endif
                <form method="POST" action="{{ route('dashboard.sms-campaigns.destroy', $c) }}" style="display:inline;" onsubmit="return confirm('Delete this campaign?');">@csrf @method('DELETE')
                  <button type="submit" class="btn btn-delete btn-sm">Delete</button>
                </form>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
        @else
        <div style="padding:3rem;text-align:center;color:#94a3b8;">
          <svg width="36" height="36" fill="none" stroke="#cbd5e1" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto .75rem;display:block;"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8 8 0 0 1-8-8c0-4.418 3.582-8 8-8s8 3.582 8 8z"/></svg>
          <p style="font-weight:600;color:#64748b;">No campaigns yet</p>
        </div>
        @endif
      </div>
    </div>
  </div>

</div>
</div>

<script>
function countChars() {
  const len = document.getElementById('smsMessage').value.length;
  const el = document.getElementById('charCount');
  el.textContent = len + ' / 1600';
  el.className = 'char-count' + (len > 1400 ? ' danger' : len > 1200 ? ' warning' : '');
}
countChars();
</script>
@endsection
