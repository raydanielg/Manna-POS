@extends('layouts.dashboard')
@section('page_title','Add Purchase')
@section('content')
<div class="dash-content">
<div class="page-card" style="max-width:600px;">
  <div class="card-header"><div class="card-title">Add Purchase</div></div>
  <div style="padding:2rem;text-align:center;">
    <p style="color:#64748b;margin-bottom:1.5rem;">Purchase orders are managed from the purchases list.</p>
    <a href="/dashboard/purchases/list-purchases" class="btn btn-primary">Go to Purchases List</a>
  </div>
</div>
</div>
@endsection
