@extends('layouts.dashboard')
@section('page_title','Add Stock Adjustment')
@section('content')
<div class="dash-content">
<div class="page-card" style="max-width:600px;">
  <div class="card-header"><div class="card-title">Add Stock Adjustment</div></div>
  <div style="padding:2rem;text-align:center;">
    <p style="color:#64748b;margin-bottom:1.5rem;">Stock adjustments are managed from the adjustments list.</p>
    <a href="/dashboard/stock-adjustment/list-stock-adjustment" class="btn btn-primary">Go to Stock Adjustments</a>
  </div>
</div>
</div>
@endsection
