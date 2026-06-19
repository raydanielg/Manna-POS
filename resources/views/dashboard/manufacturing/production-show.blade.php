@extends('layouts.dashboard')
@section('page_title',$run->batch_number)
@section('content')
<div class="dash-content">
<div class="loan-detail">
  <div class="loan-header">
    <h1>{{ $run->batch_number }}</h1>
    <div class="meta">{{ $run->recipe->name ?? 'N/A' }} &middot; Start: {{ $run->start_date->format('M d, Y') }} @if($run->end_date)&middot; End: {{ $run->end_date->format('M d, Y') }}@endif</div>
    <div class="actions">
      @if($run->status === 'planned')
      <form method="POST" action="{{ route('dashboard.manufacturing.production.status', $run) }}" style="display:inline;">@csrf
        <input type="hidden" name="status" value="in_progress"><button type="submit" class="btn btn-success">Start Production</button>
      </form>
      @endif
      @if($run->status === 'in_progress')
      <form method="POST" action="{{ route('dashboard.manufacturing.production.status', $run) }}" style="display:inline;">@csrf
        <input type="hidden" name="status" value="completed"><button type="submit" class="btn btn-success">Complete</button>
      </form>
      @endif
      @if(in_array($run->status, ['planned','in_progress']))
      <form method="POST" action="{{ route('dashboard.manufacturing.production.status', $run) }}" style="display:inline;">@csrf
        <input type="hidden" name="status" value="cancelled"><button type="submit" class="btn btn-danger">Cancel</button>
      </form>
      @endif
      <a href="{{ route('dashboard.manufacturing.production') }}" class="btn">Back</a>
    </div>
  </div>

  <div class="detail-grid" style="margin-bottom:1.5rem;">
    <div class="detail-item"><div class="label">Recipe</div><div class="value">{{ $run->recipe->name ?? 'N/A' }}</div></div>
    <div class="detail-item"><div class="label">Product</div><div class="value">{{ $run->recipe->product->name ?? 'N/A' }}</div></div>
    <div class="detail-item"><div class="label">Planned Qty</div><div class="value">{{ $run->planned_quantity }}</div></div>
    <div class="detail-item"><div class="label">Actual Qty</div><div class="value">{{ $run->actual_quantity ?? '-' }}</div></div>
    <div class="detail-item"><div class="label">Total Cost</div><div class="value">{{ $run->total_cost ? number_format($run->total_cost, 2) : '-' }}</div></div>
    <div class="detail-item"><div class="label">Status</div><div class="value"><span class="status {{ $run->status }}">{{ ucfirst(str_replace('_',' ',$run->status)) }}</span></div></div>
  </div>

  <div class="page-card" style="max-width:1000px;margin:0 auto;">
    <div class="card-header"><div class="card-title">Material Usage</div></div>
    <div class="card-body" style="padding:0;">
      @if($run->usages->count())
      <table class="mf-table">
        <thead><tr><th>Material</th><th>Planned</th><th>Actual</th><th>Unit Cost</th><th>Total Cost</th><th style="text-align:right;">Actions</th></tr></thead>
        <tbody>
          @foreach($run->usages as $u)
          <tr>
            <td>{{ $u->product->name ?? 'N/A' }}</td>
            <td>{{ $u->planned_quantity }}</td>
            <td>{{ $u->actual_quantity ?? '-' }}</td>
            <td>{{ $u->unit_cost ? number_format($u->unit_cost, 2) : '-' }}</td>
            <td>{{ $u->actual_quantity && $u->unit_cost ? number_format($u->actual_quantity * $u->unit_cost, 2) : '-' }}</td>
            <td style="text-align:right;">
              @if($run->status === 'in_progress')
              <button class="btn btn-edit btn-sm" onclick="recordUsage({{ $u->id }}, {{ $u->product->name ?? '' }})">Record</button>
              @endif
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
      @else
      <div style="padding:2rem;text-align:center;color:#94a3b8;">No material usage records.</div>
      @endif
    </div>
  </div>

  @if($run->notes)
  <div class="page-card" style="max-width:1000px;margin:1.5rem auto 0;">
    <div class="card-body">
      <div style="font-size:.68rem;font-weight:700;text-transform:uppercase;color:#94a3b8;margin-bottom:.35rem;">Notes</div>
      <div style="font-size:.85rem;color:#475569;">{{ $run->notes }}</div>
    </div>
  </div>
  @endif

</div>
</div>

<div class="modal-overlay" id="usageModal" onclick="if(event.target===this)this.classList.remove('open')">
  <div class="modal-box" style="max-width:400px;" onclick="event.stopPropagation()">
    <div class="modal-header"><h3 class="modal-title">Record Usage</h3><button class="modal-close" onclick="document.getElementById('usageModal').classList.remove('open')">&times;</button></div>
    <form method="POST" action="{{ route('dashboard.manufacturing.production.usage', $run) }}">@csrf
      <input type="hidden" name="usage_id" id="usageId">
      <div class="form-group"><label class="form-label">Actual Quantity Used *</label><input name="actual_quantity" type="number" step="0.0001" class="form-control" required></div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" onclick="document.getElementById('usageModal').classList.remove('open')">Cancel</button><button type="submit" class="btn btn-primary">Save</button></div>
    </form>
  </div>
</div>

<script>
function recordUsage(id) {
  document.getElementById('usageId').value = id;
  document.getElementById('usageModal').classList.add('open');
}
</script>
@endsection
