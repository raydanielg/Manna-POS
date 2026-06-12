@extends('layouts.dashboard')
@section('page_title','Add Product')
@section('content')
<div class="dash-content">
<div class="page-card" style="max-width:600px;">
  <div class="card-header"><div class="card-title">Add Product</div></div>
  <div style="padding:2rem;text-align:center;">
    <p style="color:#64748b;margin-bottom:1.5rem;">Products are managed from the product list page.</p>
    <a href="/dashboard/inventory/list-products" class="btn btn-primary">Go to Products List</a>
  </div>
</div>
</div>
@endsection
