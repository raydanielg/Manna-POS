@extends('layouts.dashboard')
@section('page_title','Manufacturing')
@section('content')
<div class="dash-content">
<div class="mf-wrap">

  <div class="mf-hero">
    <h1>Manufacturing & Production</h1>
    <p>Manage recipes, BOMs, and production runs</p>
    <div class="actions">
      <a href="{{ route('dashboard.manufacturing.recipe.create') }}" class="btn btn-primary">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        New Recipe
      </a>
      <a href="{{ route('dashboard.manufacturing.production.create') }}" class="btn">New Production Run</a>
      <a href="{{ route('dashboard.manufacturing.recipes') }}" class="btn">Recipes</a>
      <a href="{{ route('dashboard.manufacturing.production') }}" class="btn">Production</a>
    </div>
  </div>

  <div class="mf-stats" style="margin-bottom:1.5rem;">
    <div class="mf-stat"><div class="bar blue"></div><div class="label">Recipes</div><div class="value">{{ $stats['recipes'] }}</div></div>
    <div class="mf-stat"><div class="bar green"></div><div class="label">Active</div><div class="value">{{ $stats['active_recipes'] }}</div></div>
    <div class="mf-stat"><div class="bar amber"></div><div class="label">Production Runs</div><div class="value">{{ $stats['production_runs'] }}</div></div>
    <div class="mf-stat"><div class="bar violet"></div><div class="label">Completed</div><div class="value">{{ $stats['completed_runs'] }}</div></div>
  </div>

  <div class="mf-card">
    <div class="mf-card-head"><h3>Recent Production Runs</h3><a href="{{ route('dashboard.manufacturing.production') }}" style="font-size:.75rem;color:#2563eb;font-weight:700;">View All</a></div>
    <div class="mf-card-body" style="padding:0;">
      @if($recentRuns->count())
      <table class="mf-table">
        <thead><tr><th>Batch</th><th>Recipe</th><th>Planned</th><th>Actual</th><th>Status</th><th>Start</th></tr></thead>
        <tbody>
          @foreach($recentRuns as $r)
          <tr>
            <td><a href="{{ route('dashboard.manufacturing.production.show', $r) }}" style="color:#2563eb;font-weight:700;text-decoration:none;">{{ $r->batch_number }}</a></td>
            <td>{{ $r->recipe->name ?? 'N/A' }}</td>
            <td>{{ $r->planned_quantity }}</td>
            <td>{{ $r->actual_quantity ?? '-' }}</td>
            <td><span class="status {{ $r->status }}">{{ ucfirst(str_replace('_',' ',$r->status)) }}</span></td>
            <td style="font-size:.72rem;color:#64748b;">{{ $r->start_date->format('M d, Y') }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
      @else
      <div style="padding:2rem;text-align:center;color:#94a3b8;">No production runs yet. <a href="{{ route('dashboard.manufacturing.production.create') }}" style="color:#2563eb;">Create one</a>.</div>
      @endif
    </div>
  </div>

</div>
</div>
@endsection
