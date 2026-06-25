@extends('admin.layouts.app')
@section('page_title', 'Cache Management')
@section('content')

{{-- ── Summary Header ── --}}
<div class="cache-summary">
    <div class="cache-summary-left">
        <div class="cache-summary-icon">
            <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/></svg>
        </div>
        <div>
            <div class="cache-summary-label">Total Cache Size</div>
            <div class="cache-summary-value" id="totalCacheSize">—</div>
        </div>
    </div>
    <div class="cache-summary-right">
        <div class="cache-summary-stat">
            <div class="cache-stat-num" id="opcacheStatus">—</div>
            <div class="cache-stat-label">OPcache</div>
        </div>
        <div class="cache-summary-stat">
            <div class="cache-stat-num" id="cachedItemsCount">—</div>
            <div class="cache-stat-label">Cached Items</div>
        </div>
        <button class="btn btn-warning btn-sm" onclick="optimize()">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right:4px"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            Optimize
        </button>
        <button class="btn btn-danger btn-sm" onclick="clearAll()">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right:4px"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
            Clear All
        </button>
    </div>
</div>

{{-- ── Cache Cards Grid ── --}}
<div class="cache-grid" id="cacheGrid">
    <div class="cache-loading">
        <svg width="32" height="32" fill="none" stroke="#cbd5e1" stroke-width="2" viewBox="0 0 24 24" style="animation:spin 1s linear infinite"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h5M20 20v-5h-5"/><path d="M4 9a9 9 0 0 1 15.36-5.36M20 15a9 9 0 0 1-15.36 5.36"/></svg>
        <span>Loading cache data...</span>
    </div>
</div>

<style>
    @keyframes spin { to { transform: rotate(360deg); } }

    /* ── Summary Header ── */
    .cache-summary {
        display:flex;align-items:center;justify-content:space-between;gap:1.5rem;
        background:linear-gradient(135deg,#0f2748 0%,#1a365d 100%);
        color:#fff;border-radius:18px;padding:1.5rem 1.75rem;margin-bottom:1.5rem;
        position:relative;overflow:hidden;flex-wrap:wrap;
    }
    .cache-summary::before {
        content:'';position:absolute;top:-60%;right:-10%;width:280px;height:280px;
        background:radial-gradient(circle,rgba(124,58,237,0.15) 0%,transparent 70%);
        border-radius:50%;pointer-events:none;
    }
    .cache-summary-left { display:flex;align-items:center;gap:1rem;position:relative;z-index:1; }
    .cache-summary-icon {
        width:52px;height:52px;border-radius:14px;background:rgba(255,255,255,0.1);
        display:flex;align-items:center;justify-content:center;color:#a78bfa;flex-shrink:0;
    }
    .cache-summary-label { font-size:0.72rem;font-weight:600;color:rgba(255,255,255,0.5);text-transform:uppercase;letter-spacing:0.06em; }
    .cache-summary-value { font-size:1.75rem;font-weight:800;letter-spacing:-0.03em;line-height:1.1; }
    .cache-summary-right { display:flex;align-items:center;gap:1.5rem;position:relative;z-index:1;flex-wrap:wrap; }
    .cache-summary-stat { text-align:center; }
    .cache-stat-num { font-size:1.1rem;font-weight:800;color:#fff; }
    .cache-stat-label { font-size:0.62rem;font-weight:600;color:rgba(255,255,255,0.4);text-transform:uppercase;letter-spacing:0.06em; }
    .cache-summary .btn { white-space:nowrap; }
    .btn-warning { background:linear-gradient(135deg,#d97706,#f59e0b);color:#fff;border:none;padding:0.5rem 1rem;border-radius:10px;font-size:0.78rem;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;transition:all 0.2s; }
    .btn-warning:hover { transform:translateY(-1px);box-shadow:0 4px 12px rgba(217,119,6,0.3); }
    .btn-danger { background:linear-gradient(135deg,#dc2626,#ef4444);color:#fff;border:none;padding:0.5rem 1rem;border-radius:10px;font-size:0.78rem;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;transition:all 0.2s; }
    .btn-danger:hover { transform:translateY(-1px);box-shadow:0 4px 12px rgba(220,38,38,0.3); }

    /* ── Grid ── */
    .cache-grid { display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:1rem; }
    .cache-loading { grid-column:1/-1;display:flex;flex-direction:column;align-items:center;gap:0.75rem;padding:3rem;color:#94a3b8;font-size:0.85rem; }

    /* ── Cache Card ── */
    .cache-card {
        background:#fff;border-radius:16px;border:1px solid #e9edf5;padding:1.25rem;
        transition:box-shadow 0.25s,transform 0.25s;position:relative;overflow:hidden;
    }
    .cache-card::before {
        content:'';position:absolute;top:0;left:0;right:0;height:3px;border-radius:16px 16px 0 0;
        background:var(--card-color,#7c3aed);
    }
    .cache-card:hover { box-shadow:0 8px 28px rgba(15,23,42,0.06);transform:translateY(-2px); }

    .cache-card-header { display:flex;align-items:center;gap:0.75rem;margin-bottom:1rem; }
    .cache-card-icon {
        width:40px;height:40px;border-radius:12px;display:flex;align-items:center;justify-content:center;
        flex-shrink:0;
    }
    .cache-card-icon svg { width:20px;height:20px; }
    .cache-card-title { font-size:0.88rem;font-weight:800;color:#0f172a;letter-spacing:-0.01em; }
    .cache-card-status { font-size:0.62rem;font-weight:700;padding:0.15rem 0.5rem;border-radius:99px;margin-top:0.15rem;display:inline-block; }
    .cache-card-status.active { background:rgba(22,163,74,0.12);color:#16a34a; }
    .cache-card-status.inactive { background:rgba(148,163,184,0.12);color:#94a3b8; }

    .cache-size-display { text-align:center;margin-bottom:0.75rem; }
    .cache-size-value { font-size:1.85rem;font-weight:800;color:#0f172a;letter-spacing:-0.03em;line-height:1; }
    .cache-size-unit { font-size:0.75rem;font-weight:600;color:#94a3b8;margin-left:0.15rem; }

    .cache-bar-wrap { margin-bottom:1rem; }
    .cache-bar-bg { height:6px;background:#f1f5f9;border-radius:99px;overflow:hidden; }
    .cache-bar-fill { height:100%;border-radius:99px;transition:width 0.6s ease; }

    .cache-card-footer { display:flex;align-items:center;justify-content:space-between; }
    .cache-card-bytes { font-size:0.68rem;font-weight:600;color:#94a3b8; }
    .cache-clear-btn {
        background:#fef2f2;color:#dc2626;border:1px solid #fecaca;padding:0.35rem 0.85rem;
        border-radius:8px;font-size:0.72rem;font-weight:700;cursor:pointer;transition:all 0.2s;
        display:inline-flex;align-items:center;gap:0.3rem;
    }
    .cache-clear-btn:hover { background:#dc2626;color:#fff;border-color:#dc2626; }
    .cache-clear-btn:disabled { opacity:0.4;cursor:not-allowed; }

    @media (max-width:768px) {
        .cache-summary { flex-direction:column;align-items:flex-start; }
        .cache-summary-right { width:100%;justify-content:space-between; }
    }
</style>
@endsection
@section('scripts')
const API = '/api/admin/cache';

const iconMap = {
    app: '<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>',
    views: '<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>',
    config: '<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>',
    routes: '<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>',
    events: '<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>',
    sessions: '<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>',
    logs: '<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>',
};

const typeLabels = {
    app: 'Application Cache',
    views: 'View Cache',
    config: 'Config Cache',
    routes: 'Route Cache',
    events: 'Event Cache',
    sessions: 'Session Data',
    logs: 'Log Files',
};

function formatBytes(bytes) {
    if (!bytes || bytes === 0) return { value: '0', unit: 'B' };
    const units = ['B', 'KB', 'MB', 'GB'];
    const pow = Math.min(Math.floor(Math.log(bytes) / Math.log(1024)), units.length - 1);
    const val = (bytes / Math.pow(1024, pow)).toFixed(2);
    return { value: val, unit: units[pow] };
}

async function loadStatus() {
    try {
        const data = await apiFetch(API);
        const grid = document.getElementById('cacheGrid');

        // Update summary
        document.getElementById('totalCacheSize').textContent = data.cache_total_size || '0 B';
        document.getElementById('opcacheStatus').textContent = data.opcache_enabled ? 'ON' : 'OFF';
        document.getElementById('opcacheStatus').style.color = data.opcache_enabled ? '#34d399' : '#fca5a5';

        const items = data.items;
        const totalBytes = data.cache_total_bytes || 0;
        let cachedCount = 0;
        Object.values(items).forEach(i => { if (i.cached) cachedCount++; });
        document.getElementById('cachedItemsCount').textContent = cachedCount + '/' + Object.keys(items).length;

        // Render cards
        grid.innerHTML = Object.entries(items).map(([key, item]) => {
            const fmt = formatBytes(item.bytes);
            const pct = totalBytes > 0 ? Math.min((item.bytes / totalBytes) * 100, 100) : 0;
            const hasData = item.bytes > 0;

            return `
            <div class="cache-card" style="--card-color:${item.color}">
                <div class="cache-card-header">
                    <div class="cache-card-icon" style="background:${item.color}15;color:${item.color}">
                        ${iconMap[key] || iconMap.app}
                    </div>
                    <div>
                        <div class="cache-card-title">${item.label}</div>
                        <span class="cache-card-status ${item.cached ? 'active' : 'inactive'}">${item.cached ? 'Active' : 'Empty'}</span>
                    </div>
                </div>
                <div class="cache-size-display">
                    <span class="cache-size-value">${fmt.value}</span>
                    <span class="cache-size-unit">${fmt.unit}</span>
                </div>
                <div class="cache-bar-wrap">
                    <div class="cache-bar-bg">
                        <div class="cache-bar-fill" style="width:${pct}%;background:linear-gradient(90deg,${item.color},${item.color}aa)"></div>
                    </div>
                </div>
                <div class="cache-card-footer">
                    <span class="cache-card-bytes">${item.bytes.toLocaleString()} bytes</span>
                    <button class="cache-clear-btn" onclick="clearType('${key}')" ${!hasData && key !== 'sessions' ? 'disabled' : ''}>
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22"/></svg>
                        Clear
                    </button>
                </div>
            </div>`;
        }).join('');

    } catch (e) {
        document.getElementById('cacheGrid').innerHTML = `
            <div class="cache-loading" style="color:#ef4444">
                <svg width="32" height="32" fill="none" stroke="#ef4444" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>Failed to load cache data</span>
            </div>`;
    }
}

function clearType(type) {
    Swal.fire({
        title: 'Clear ' + (typeLabels[type] || type) + '?',
        text: 'This will remove all cached data for this type.',
        icon: 'warning', showCancelButton: true,
        confirmButtonColor: '#dc2626', confirmButtonText: 'Yes, Clear',
    }).then(async (r) => {
        if (!r.isConfirmed) return;
        Swal.fire({ title: 'Clearing...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        try {
            await apiFetch(API + '/clear', { method: 'POST', body: { type } });
            Swal.fire({ icon: 'success', title: 'Cache cleared!', timer: 1500, showConfirmButton: false });
            loadStatus();
        } catch (e) {
            Swal.fire({ icon: 'error', title: 'Failed', text: e.data?.message || 'Error clearing cache' });
        }
    });
}

function clearAll() {
    Swal.fire({
        title: 'Clear ALL cache?',
        text: 'This will clear all cache types including sessions and logs.',
        icon: 'warning', showCancelButton: true,
        confirmButtonColor: '#dc2626', confirmButtonText: 'Clear Everything',
    }).then(async (r) => {
        if (!r.isConfirmed) return;
        Swal.fire({ title: 'Clearing all cache...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
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
    Swal.fire({
        title: 'Optimize Application?',
        text: 'This will cache config, routes, and views for better performance.',
        icon: 'question', showCancelButton: true, confirmButtonText: 'Optimize',
    }).then(async (r) => {
        if (!r.isConfirmed) return;
        Swal.fire({ title: 'Optimizing...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        try {
            await apiFetch(API + '/optimize', { method: 'POST' });
            Swal.fire({ icon: 'success', title: 'Optimized successfully!', timer: 1500, showConfirmButton: false });
            loadStatus();
        } catch (e) {
            Swal.fire({ icon: 'error', title: 'Failed', text: e.data?.message || 'Optimization failed' });
        }
    });
}

loadStatus();
@endsection