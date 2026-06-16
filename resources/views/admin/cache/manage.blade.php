@extends('admin.layouts.app')
@section('page_title', 'Cache Management')
@section('content')
<div style="display:flex;gap:0.75rem;justify-content:flex-end;margin-bottom:1.25rem;">
    <button class="btn btn-warning" onclick="optimize()">Optimize</button>
    <button class="btn btn-danger" onclick="clearAll()">Clear All</button>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:1rem;">
    <div class="page-card" id="card-app">
        <div class="card-header"><div class="card-title">Application Cache</div></div>
        <div style="padding:1.25rem;text-align:center;">
            <div style="font-size:0.85rem;margin-bottom:1rem;"><span class="badge" id="badge-app">Checking...</span></div>
            <button class="btn btn-danger btn-sm" onclick="clearType('app')">Clear</button>
        </div>
    </div>
    <div class="page-card" id="card-view">
        <div class="card-header"><div class="card-title">View Cache</div></div>
        <div style="padding:1.25rem;text-align:center;">
            <div style="font-size:0.85rem;margin-bottom:1rem;"><span class="badge" id="badge-view">Checking...</span></div>
            <button class="btn btn-danger btn-sm" onclick="clearType('view')">Clear</button>
        </div>
    </div>
    <div class="page-card" id="card-config">
        <div class="card-header"><div class="card-title">Config Cache</div></div>
        <div style="padding:1.25rem;text-align:center;">
            <div style="font-size:0.85rem;margin-bottom:1rem;"><span class="badge" id="badge-config">Checking...</span></div>
            <button class="btn btn-danger btn-sm" onclick="clearType('config')">Clear</button>
        </div>
    </div>
    <div class="page-card" id="card-route">
        <div class="card-header"><div class="card-title">Route Cache</div></div>
        <div style="padding:1.25rem;text-align:center;">
            <div style="font-size:0.85rem;margin-bottom:1rem;"><span class="badge" id="badge-route">Checking...</span></div>
            <button class="btn btn-danger btn-sm" onclick="clearType('route')">Clear</button>
        </div>
    </div>
    <div class="page-card" id="card-event">
        <div class="card-header"><div class="card-title">Event Cache</div></div>
        <div style="padding:1.25rem;text-align:center;">
            <div style="font-size:0.85rem;margin-bottom:1rem;"><span class="badge" id="badge-event">Checking...</span></div>
            <button class="btn btn-danger btn-sm" onclick="clearType('event')">Clear</button>
        </div>
    </div>
</div>
@endsection
@section('scripts')
const API = '/api/admin/cache';
const types = ['app','view','config','route','event'];

async function loadStatus() {
    try {
        const data = await apiFetch(API + '/status');
        types.forEach(t => {
            const badge = document.getElementById('badge-' + t);
            const cached = data[t] || data[t + '_cached'];
            badge.textContent = cached ? 'Cached' : 'Not cached';
            badge.className = 'badge ' + (cached ? 'badge-success' : 'badge-default');
        });
    } catch (e) {
        types.forEach(t => {
            document.getElementById('badge-' + t).textContent = 'Unknown';
            document.getElementById('badge-' + t).className = 'badge badge-default';
        });
    }
}

function clearType(type) {
    Swal.fire({ title: 'Clear ' + type + ' cache?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc2626', confirmButtonText: 'Clear' })
    .then(async (r) => {
        if (!r.isConfirmed) return;
        Swal.fire({ title: 'Clearing...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        try {
            await apiFetch(API + '/clear', { method: 'POST', body: { type } });
            Swal.fire({ icon: 'success', title: type + ' cache cleared!', timer: 1500, showConfirmButton: false });
            loadStatus();
        } catch (e) {
            Swal.fire({ icon: 'error', title: 'Failed', text: e.data?.message || 'Error clearing cache' });
        }
    });
}

function clearAll() {
    Swal.fire({ title: 'Clear all cache?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc2626', confirmButtonText: 'Clear All' })
    .then(async (r) => {
        if (!r.isConfirmed) return;
        Swal.fire({ title: 'Clearing all...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        try {
            await apiFetch(API + '/clear', { method: 'POST', body: { type: 'all' } });
            Swal.fire({ icon: 'success', title: 'All cache cleared!', timer: 1500, showConfirmButton: false });
            loadStatus();
        } catch (e) {
            Swal.fire({ icon: 'error', title: 'Failed', text: e.data?.message || 'Error clearing cache' });
        }
    });
}

function optimize() {
    Swal.fire({ title: 'Optimize?', text: 'Run php artisan optimize?', icon: 'question', showCancelButton: true, confirmButtonText: 'Optimize' })
    .then(async (r) => {
        if (!r.isConfirmed) return;
        Swal.fire({ title: 'Optimizing...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        try {
            await apiFetch(API + '/optimize', { method: 'POST' });
            Swal.fire({ icon: 'success', title: 'Optimized!', timer: 1500, showConfirmButton: false });
            loadStatus();
        } catch (e) {
            Swal.fire({ icon: 'error', title: 'Failed', text: e.data?.message || 'Optimization failed' });
        }
    });
}

loadStatus();
@endsection