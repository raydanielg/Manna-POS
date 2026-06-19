@extends('layouts.dashboard')
@section('page_title','New Recipe')
@section('content')
<div class="dash-content">
<div class="loan-form">
  <h2><svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 14h6m-3-3v6m-7 4v-16a2 2 0 012-2h10a2 2 0 012 2v16l-3-2l-2 2l-2-2l-2 2l-2-2l-3 2"/></svg> New Recipe / BOM</h2>
  <form method="POST" action="{{ route('dashboard.manufacturing.recipe.store') }}">@csrf
    <div class="form-group"><label class="form-label">Recipe Name *</label><input name="name" class="form-control" placeholder="e.g. Bread Recipe A" required></div>
    <div class="form-group"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="2"></textarea></div>
    <div class="form-row">
      <div class="form-group"><label class="form-label">Final Product *</label>
        <select name="product_id" class="form-control" required>
          <option value="">Select product…</option>
          @foreach($products as $p)
          <option value="{{ $p->id }}">{{ $p->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="form-group"><label class="form-label">Output Quantity *</label><input name="output_quantity" type="number" step="0.0001" class="form-control" value="1" required></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label class="form-label">Unit</label><input name="output_unit" class="form-control" placeholder="e.g. kg, pcs, bottles"></div>
      <div class="form-group"><label class="form-label">Status</label>
        <select name="status" class="form-control"><option value="active">Active</option><option value="inactive">Inactive</option></select>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group"><label class="form-label">Labor Cost</label><input name="labor_cost" type="number" step="0.01" class="form-control" value="0"></div>
      <div class="form-group"><label class="form-label">Overhead Cost</label><input name="overhead_cost" type="number" step="0.01" class="form-control" value="0"></div>
    </div>
    <div style="display:flex;justify-content:flex-end;gap:.75rem;margin-top:1.5rem;">
      <a href="{{ route('dashboard.manufacturing.recipes') }}" class="btn btn-secondary">Cancel</a>
      <button type="submit" class="btn btn-primary">Create Recipe</button>
    </div>
  </form>
</div>
</div>
@endsection
