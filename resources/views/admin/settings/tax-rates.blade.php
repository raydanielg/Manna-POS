@extends('admin.layouts.app')
@section('page_title', 'Tax Rates')
@section('content')
<div class="page-card"><div class="card-header"><div class="card-title">Tax Rates</div></div><div style="padding:1.5rem;"><p style="color:#64748b;">Manage tax rates from the main system configuration page.</p><a href="{{ route('admin.system.config') }}" class="btn btn-primary">Go to System Config</a></div></div>
@endsection
