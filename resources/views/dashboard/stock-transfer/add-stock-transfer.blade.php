@extends('layouts.dashboard')
@section('page_title','Add Stock Transfer')
@section('content')
<div class="dash-content">
<div class="page-card" style="max-width:600px;">
  <div class="card-header"><div class="card-title">Add Stock Transfer</div></div>
  <div style="padding:2rem;text-align:center;">
    <p style="color:#64748b;margin-bottom:1.5rem;">Stock transfers are managed from the transfers list.</p>
    <a href="/dashboard/stock-transfer/list-stock-transfer" class="btn btn-primary">Go to Stock Transfers</a>
  </div>
</div>
</div>
@endsection
