@extends('layouts.dashboard')
@section('page_title','Import Results')
@section('content')
<div class="dash-content">

{{-- Hero Summary Banner --}}
<div style="background:linear-gradient(135deg,#0f172a 0%,#1e3a8a 50%,#2563eb 100%);border-radius:16px;padding:2rem 2.5rem;margin-bottom:1.5rem;color:#fff;position:relative;overflow:hidden;">
  <div style="position:absolute;top:-30%;right:-5%;width:300px;height:300px;background:radial-gradient(circle,rgba(255,255,255,0.08) 0%,transparent 70%);border-radius:50%;pointer-events:none;"></div>
  <div style="position:relative;z-index:1;">
    <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.5rem;">
      <span style="background:rgba(255,255,255,0.15);backdrop-filter:blur(8px);padding:0.25rem 0.75rem;border-radius:50px;font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;">Import Complete</span>
    </div>
    <h1 style="font-size:1.5rem;font-weight:800;margin:0 0 0.35rem;letter-spacing:-0.02em;">
      {{ ucfirst($summary['type']) }} Import Report
    </h1>
    <p style="font-size:0.88rem;color:rgba(255,255,255,0.8);margin:0;">
      {{ $summary['total'] }} record(s) processed · {{ now()->format('M d, Y \a\t H:i') }}
    </p>
  </div>
</div>

{{-- Stats Cards --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:1rem;margin-bottom:1.5rem;">
  {{-- Success --}}
  <div style="background:#fff;border-radius:14px;padding:1.25rem 1.5rem;border:1px solid #f1f5f9;position:relative;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.03);transition:all 0.3s ease;">
    <div style="position:absolute;left:0;top:0;bottom:0;width:4px;background:linear-gradient(180deg,#4ade80,#22c55e);border-radius:14px 0 0 14px;"></div>
    <div style="font-size:0.7rem;font-weight:800;text-transform:uppercase;letter-spacing:0.08em;color:#64748b;margin-bottom:0.4rem;">Successfully Imported</div>
    <div style="font-size:1.8rem;font-weight:800;color:#0f172a;line-height:1.1;letter-spacing:-0.02em;">{{ number_format($summary['success']) }}</div>
    <div style="font-size:0.78rem;color:#94a3b8;margin-top:0.3rem;">records added</div>
  </div>
  {{-- Failed --}}
  <div style="background:#fff;border-radius:14px;padding:1.25rem 1.5rem;border:1px solid #f1f5f9;position:relative;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.03);transition:all 0.3s ease;">
    <div style="position:absolute;left:0;top:0;bottom:0;width:4px;background:linear-gradient(180deg,#f87171,#ef4444);border-radius:14px 0 0 14px;"></div>
    <div style="font-size:0.7rem;font-weight:800;text-transform:uppercase;letter-spacing:0.08em;color:#64748b;margin-bottom:0.4rem;">Failed / Skipped</div>
    <div style="font-size:1.8rem;font-weight:800;color:#0f172a;line-height:1.1;letter-spacing:-0.02em;">{{ number_format($summary['failed']) }}</div>
    <div style="font-size:0.78rem;color:#94a3b8;margin-top:0.3rem;">records failed</div>
  </div>
  {{-- Total --}}
  <div style="background:#fff;border-radius:14px;padding:1.25rem 1.5rem;border:1px solid #f1f5f9;position:relative;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.03);transition:all 0.3s ease;">
    <div style="position:absolute;left:0;top:0;bottom:0;width:4px;background:linear-gradient(180deg,#60a5fa,#3b82f6);border-radius:14px 0 0 14px;"></div>
    <div style="font-size:0.7rem;font-weight:800;text-transform:uppercase;letter-spacing:0.08em;color:#64748b;margin-bottom:0.4rem;">Total Processed</div>
    <div style="font-size:1.8rem;font-weight:800;color:#0f172a;line-height:1.1;letter-spacing:-0.02em;">{{ number_format($summary['total']) }}</div>
    <div style="font-size:0.78rem;color:#94a3b8;margin-top:0.3rem;">rows in CSV</div>
  </div>
  {{-- Success Rate --}}
  <div style="background:#fff;border-radius:14px;padding:1.25rem 1.5rem;border:1px solid #f1f5f9;position:relative;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.03);transition:all 0.3s ease;">
    <div style="position:absolute;left:0;top:0;bottom:0;width:4px;background:linear-gradient(180deg,#fbbf24,#f59e0b);border-radius:14px 0 0 14px;"></div>
    <div style="font-size:0.7rem;font-weight:800;text-transform:uppercase;letter-spacing:0.08em;color:#64748b;margin-bottom:0.4rem;">Success Rate</div>
    <div style="font-size:1.8rem;font-weight:800;color:#0f172a;line-height:1.1;letter-spacing:-0.02em;">
      {{ $summary['total'] > 0 ? round(($summary['success'] / $summary['total']) * 100, 1) : 0 }}%
    </div>
    <div style="font-size:0.78rem;color:#94a3b8;margin-top:0.3rem;">completion rate</div>
  </div>
</div>

{{-- Success Progress Bar --}}
<div style="background:#fff;border-radius:14px;padding:1.25rem 1.5rem;border:1px solid #f1f5f9;margin-bottom:1.5rem;box-shadow:0 2px 8px rgba(0,0,0,0.03);">
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.75rem;">
    <span style="font-size:0.85rem;font-weight:700;color:#0f172a;">Import Progress</span>
    <span style="font-size:0.78rem;font-weight:700;color:#22c55e;">{{ $summary['success'] }} of {{ $summary['total'] }} imported</span>
  </div>
  <div style="height:10px;background:#f1f5f9;border-radius:50px;overflow:hidden;">
    @php $pct = $summary['total'] > 0 ? ($summary['success'] / $summary['total']) * 100 : 0; @endphp
    <div style="width:{{ $pct }}%;height:100%;background:linear-gradient(90deg,#22c55e,#4ade80);border-radius:50px;transition:width 1s ease;"></div>
  </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:1.5rem;">
  {{-- Imported Records Table --}}
  <div style="background:#fff;border-radius:14px;border:1px solid #f1f5f9;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.03);">
    <div style="padding:1rem 1.25rem;border-bottom:1px solid #f1f5f9;background:#fcfdfe;display:flex;align-items:center;justify-content:space-between;">
      <div style="font-size:0.9rem;font-weight:800;color:#0f172a;">
        <svg width="16" height="16" fill="none" stroke="#22c55e" stroke-width="2.5" viewBox="0 0 24 24" style="display:inline;vertical-align:middle;margin-right:0.35rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        Imported Successfully
      </div>
      <span style="background:#dcfce7;color:#15803d;font-size:0.7rem;font-weight:800;padding:0.2rem 0.6rem;border-radius:6px;">{{ count($summary['imported']) }}</span>
    </div>
    <div style="max-height:380px;overflow-y:auto;">
      @if(count($summary['imported']) > 0)
      <table class="tbl" style="margin:0;width:100%;">
        <thead>
          <tr>
            <th style="font-size:0.68rem;text-transform:uppercase;letter-spacing:0.06em;color:#64748b;background:#fafbff;border-bottom:1px solid #f1f5f9;padding:0.65rem 1rem;">Name</th>
            <th style="font-size:0.68rem;text-transform:uppercase;letter-spacing:0.06em;color:#64748b;background:#fafbff;border-bottom:1px solid #f1f5f9;padding:0.65rem 1rem;">Email</th>
            <th style="font-size:0.68rem;text-transform:uppercase;letter-spacing:0.06em;color:#64748b;background:#fafbff;border-bottom:1px solid #f1f5f9;padding:0.65rem 1rem;">Phone</th>
          </tr>
        </thead>
        <tbody>
          @foreach($summary['imported'] as $record)
          <tr>
            <td style="padding:0.65rem 1rem;font-size:0.82rem;color:#0f172a;font-weight:600;border-bottom:1px solid #f8fafc;">{{ $record['name'] }}</td>
            <td style="padding:0.65rem 1rem;font-size:0.78rem;color:#64748b;border-bottom:1px solid #f8fafc;">{{ $record['email'] ?? '-' }}</td>
            <td style="padding:0.65rem 1rem;font-size:0.78rem;color:#64748b;border-bottom:1px solid #f8fafc;">{{ $record['phone'] ?? '-' }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
      @else
      <div style="padding:2rem;text-align:center;color:#94a3b8;font-size:0.85rem;">
        <svg width="40" height="40" fill="none" stroke="#cbd5e1" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 0.75rem;display:block;"><path stroke-linecap="round" stroke-linejoin="round" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        No records were imported successfully
      </div>
      @endif
    </div>
  </div>

  {{-- Errors Table --}}
  <div style="background:#fff;border-radius:14px;border:1px solid #f1f5f9;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.03);">
    <div style="padding:1rem 1.25rem;border-bottom:1px solid #f1f5f9;background:#fcfdfe;display:flex;align-items:center;justify-content:space-between;">
      <div style="font-size:0.9rem;font-weight:800;color:#0f172a;">
        <svg width="16" height="16" fill="none" stroke="#ef4444" stroke-width="2.5" viewBox="0 0 24 24" style="display:inline;vertical-align:middle;margin-right:0.35rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        Failed Records
      </div>
      <span style="background:#fee2e2;color:#b91c1c;font-size:0.7rem;font-weight:800;padding:0.2rem 0.6rem;border-radius:6px;">{{ count($summary['errors']) }}</span>
    </div>
    <div style="max-height:380px;overflow-y:auto;">
      @if(count($summary['errors']) > 0)
      <table class="tbl" style="margin:0;width:100%;">
        <thead>
          <tr>
            <th style="font-size:0.68rem;text-transform:uppercase;letter-spacing:0.06em;color:#64748b;background:#fafbff;border-bottom:1px solid #f1f5f9;padding:0.65rem 1rem;">Row</th>
            <th style="font-size:0.68rem;text-transform:uppercase;letter-spacing:0.06em;color:#64748b;background:#fafbff;border-bottom:1px solid #f1f5f9;padding:0.65rem 1rem;">Name</th>
            <th style="font-size:0.68rem;text-transform:uppercase;letter-spacing:0.06em;color:#64748b;background:#fafbff;border-bottom:1px solid #f1f5f9;padding:0.65rem 1rem;">Reason</th>
          </tr>
        </thead>
        <tbody>
          @foreach($summary['errors'] as $error)
          <tr>
            <td style="padding:0.65rem 1rem;font-size:0.78rem;color:#64748b;border-bottom:1px solid #f8fafc;">#{{ $error['row'] }}</td>
            <td style="padding:0.65rem 1rem;font-size:0.82rem;color:#0f172a;font-weight:600;border-bottom:1px solid #f8fafc;">{{ $error['name'] ?: '—' }}</td>
            <td style="padding:0.65rem 1rem;font-size:0.78rem;color:#dc2626;border-bottom:1px solid #f8fafc;">{{ $error['reason'] }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
      @else
      <div style="padding:2rem;text-align:center;color:#94a3b8;font-size:0.85rem;">
        <svg width="40" height="40" fill="none" stroke="#cbd5e1" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 0.75rem;display:block;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        No errors — everything imported perfectly!
      </div>
      @endif
    </div>
  </div>
</div>

{{-- Action Buttons --}}
<div style="display:flex;gap:0.75rem;flex-wrap:wrap;">
  @if($summary['type'] === 'customers')
  <a href="{{ route('dashboard.contacts.customers') }}" class="btn btn-primary" style="padding:0.65rem 1.25rem;font-weight:700;">
    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:inline;vertical-align:middle;margin-right:0.35rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
    View Customers
  </a>
  @else
  <a href="{{ route('dashboard.contacts.suppliers') }}" class="btn btn-primary" style="padding:0.65rem 1.25rem;font-weight:700;">
    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:inline;vertical-align:middle;margin-right:0.35rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
    View Suppliers
  </a>
  @endif
  <a href="{{ route('dashboard.contacts.import-contacts') }}" class="btn btn-secondary" style="padding:0.65rem 1.25rem;font-weight:700;">
    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:inline;vertical-align:middle;margin-right:0.35rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L12 4m4 4v12"/></svg>
    Import More
  </a>
</div>

</div>
@endsection
