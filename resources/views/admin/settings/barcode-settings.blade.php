@extends('admin.layouts.app')
@section('page_title', 'Barcode Settings')
@section('content')
<div class="page-card"><div class="card-header"><div class="card-title">Barcode Settings</div></div><div style="padding:1.5rem;"><p style="color:#64748b;">Barcode configuration settings are available in the main system configuration page.</p><a href="{{ route('admin.system.config') }}" class="btn btn-primary">Go to System Config</a></div></div>
@endsection
