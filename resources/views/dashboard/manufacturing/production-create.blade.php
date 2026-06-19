@extends('layouts.dashboard')
@section('page_title','New Production Run')
@section('content')
<div class="dash-content">
<div class="loan-form">
  <h2><svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> New Production Run</h2>
  <form method="POST" action="{{ route('dashboard.manufacturing.production.store') }}">@csrf
    <div class="form-group"><label class="form-label">Recipe *</label>
      <select name="recipe_id" class="form-control" required>
        <option value="">Select recipe…</option>
        @foreach($recipes as $r)
        <option value="{{ $r->id }}">{{ $r->name }} ({{ $r->product->name ?? 'N/A' }})</option>
        @endforeach
      </select>
    </div>
    <div class="form-row">
      <div class="form-group"><label class="form-label">Planned Quantity *</label><input name="planned_quantity" type="number" step="0.0001" class="form-control" required></div>
      <div class="form-group"><label class="form-label">Start Date *</label><input name="start_date" type="date" class="form-control" value="{{ date('Y-m-d') }}" required></div>
    </div>
    <div class="form-group"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
    <div style="display:flex;justify-content:flex-end;gap:.75rem;margin-top:1.5rem;">
      <a href="{{ route('dashboard.manufacturing.production') }}" class="btn btn-secondary">Cancel</a>
      <button type="submit" class="btn btn-primary">Create Run</button>
    </div>
  </form>
</div>
</div>
@endsection
