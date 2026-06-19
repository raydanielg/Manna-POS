@extends('layouts.dashboard')
@section('page_title',$recipe->name)
@section('content')
<div class="dash-content">
<div class="loan-detail">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;">
    <div>
      <h1 style="font-size:1.2rem;font-weight:800;color:#0f172a;">{{ $recipe->name }}</h1>
      <p style="font-size:.8rem;color:#64748b;margin-top:.25rem;">{{ $recipe->product->name ?? 'N/A' }} &middot; Output: {{ $recipe->output_quantity }} {{ $recipe->output_unit ?? 'units' }} &middot; <span class="status {{ $recipe->status }}">{{ ucfirst($recipe->status) }}</span></p>
    </div>
    <a href="{{ route('dashboard.manufacturing.recipes') }}" class="btn btn-secondary btn-sm">Back</a>
  </div>

  <div class="detail-grid" style="margin-bottom:1.5rem;">
    <div class="detail-item"><div class="label">Labor Cost</div><div class="value">{{ number_format($recipe->labor_cost, 2) }}</div></div>
    <div class="detail-item"><div class="label">Overhead</div><div class="value">{{ number_format($recipe->overhead_cost, 2) }}</div></div>
    <div class="detail-item"><div class="label">Material Cost</div><div class="value">{{ number_format($totalMaterialCost, 2) }}</div></div>
    <div class="detail-item"><div class="label">Total Cost</div><div class="value" style="color:#2563eb;font-weight:800;">{{ number_format($recipe->labor_cost + $recipe->overhead_cost + $totalMaterialCost, 2) }}</div></div>
  </div>

  <div class="mf-grid" style="grid-template-columns:1fr 1fr;align-items:start;">
    <div class="page-card">
      <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;">
        <div class="card-title">Ingredients / BOM</div>
        <button class="btn btn-primary btn-sm" onclick="document.getElementById('addItemModal').classList.add('open')">Add</button>
      </div>
      <div class="card-body" style="padding:0;">
        @if($recipe->items->count())
        <table class="mf-table">
          <thead><tr><th>Material</th><th>Qty</th><th>Unit</th><th>Cost</th><th style="text-align:right;">Actions</th></tr></thead>
          <tbody>
            @foreach($recipe->items as $item)
            <tr>
              <td>{{ $item->product->name ?? 'N/A' }}</td>
              <td>{{ $item->quantity }}</td>
              <td>{{ $item->unit ?? '-' }}</td>
              <td>{{ number_format($item->cost ?? 0, 2) }}</td>
              <td style="text-align:right;">
                <form method="POST" action="{{ route('dashboard.manufacturing.recipe.item.destroy', $item) }}" style="display:inline;" onsubmit="return confirm('Remove?');">@csrf @method('DELETE')
                  <button type="submit" class="btn btn-delete btn-sm">Remove</button>
                </form>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
        @else
        <div style="padding:2rem;text-align:center;color:#94a3b8;">No ingredients added yet.</div>
        @endif
      </div>
    </div>

    <div class="page-card">
      <div class="card-header"><div class="card-title">Description</div></div>
      <div class="card-body">
        <p style="font-size:.85rem;color:#475569;line-height:1.6;">{{ $recipe->description ?? 'No description.' }}</p>
      </div>
    </div>
  </div>
</div>
</div>

<div class="modal-overlay" id="addItemModal" onclick="if(event.target===this)this.classList.remove('open')">
  <div class="modal-box" style="max-width:480px;" onclick="event.stopPropagation()">
    <div class="modal-header"><h3 class="modal-title">Add Ingredient</h3><button class="modal-close" onclick="document.getElementById('addItemModal').classList.remove('open')">&times;</button></div>
    <form method="POST" action="{{ route('dashboard.manufacturing.recipe.item.store', $recipe) }}">@csrf
      <div class="form-group"><label class="form-label">Raw Material *</label>
        <select name="product_id" class="form-control" required>
          <option value="">Select product…</option>
          @foreach($products as $p)
          <option value="{{ $p->id }}">{{ $p->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Quantity *</label><input name="quantity" type="number" step="0.0001" class="form-control" required></div>
        <div class="form-group"><label class="form-label">Unit</label><input name="unit" class="form-control" placeholder="kg, g, ml"></div>
      </div>
      <div class="form-group"><label class="form-label">Unit Cost</label><input name="cost" type="number" step="0.01" class="form-control"></div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" onclick="document.getElementById('addItemModal').classList.remove('open')">Cancel</button><button type="submit" class="btn btn-primary">Add</button></div>
    </form>
  </div>
</div>
@endsection
