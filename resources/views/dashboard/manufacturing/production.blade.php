@extends('layouts.dashboard')
@section('page_title','Production Runs')
@section('content')
<div class="dash-content">
<div class="mf-wrap">
  <div class="page-card" style="max-width:1100px;margin:0 auto;">
    <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;">
      <div><div class="card-title">Production Runs</div></div>
      <a href="{{ route('dashboard.manufacturing.production.create') }}" class="btn btn-primary btn-sm" style="gap:.35rem;">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        New Run
      </a>
    </div>
    <div class="card-body" style="padding:0;">
      @if($runs->count())
      <table class="mf-table">
        <thead><tr><th>Batch</th><th>Recipe</th><th>Planned</th><th>Actual</th><th>Cost</th><th>Status</th><th style="text-align:right;">Actions</th></tr></thead>
        <tbody>
          @foreach($runs as $r)
          <tr>
            <td><a href="{{ route('dashboard.manufacturing.production.show', $r) }}" style="color:#2563eb;font-weight:700;text-decoration:none;">{{ $r->batch_number }}</a></td>
            <td>{{ $r->recipe->name ?? 'N/A' }}</td>
            <td>{{ $r->planned_quantity }}</td>
            <td>{{ $r->actual_quantity ?? '-' }}</td>
            <td>{{ $r->total_cost ? number_format($r->total_cost, 2) : '-' }}</td>
            <td><span class="status {{ $r->status }}">{{ ucfirst(str_replace('_',' ',$r->status)) }}</span></td>
            <td style="text-align:right;"><a href="{{ route('dashboard.manufacturing.production.show', $r) }}" class="btn btn-view btn-sm">View</a></td>
          </tr>
          @endforeach
        </tbody>
      </table>
      @else
      <div style="padding:3rem;text-align:center;color:#94a3b8;"><p style="font-weight:600;color:#64748b;">No production runs yet</p></div>
      @endif
    </div>
  </div>
</div>
</div>
@endsection
