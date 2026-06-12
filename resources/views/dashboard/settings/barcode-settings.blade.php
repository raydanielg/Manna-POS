@extends('layouts.dashboard')
@section('page_title','Barcode Settings')
@section('content')
<div class="dash-content">
<div class="page-card" style="max-width:700px;">
  <div class="card-header"><div class="card-title">Barcode Settings</div></div>
  <div style="padding:3rem;text-align:center;">
    <svg width="64" height="64" fill="none" stroke="#94a3b8" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 1.5rem;display:block;"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
    <h3 style="font-size:1.1rem;font-weight:700;color:#1e293b;margin-bottom:0.5rem;">Barcode Settings</h3>
    <p style="color:#64748b;font-size:0.9rem;margin-bottom:1.5rem;">Configure barcode type and label printing preferences. Feature coming soon.</p>
    <a href="javascript:history.back()" class="btn btn-secondary">Go Back</a>
  </div>
</div>
</div>
@endsection
