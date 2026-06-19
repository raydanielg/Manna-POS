@extends('layouts.dashboard')
@section('page_title', $customer->name)

@section('page_styles')
<style>
    .cv-wrap { max-width: 860px; margin: 0 auto; }

    /* Header card */
    .cv-header {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 50%, #1e3a8a 100%);
        border-radius: 24px;
        padding: 2.5rem 2.5rem;
        color: #fff;
        position: relative;
        overflow: hidden;
        margin-bottom: 1.5rem;
        box-shadow: 0 20px 60px rgba(15,23,42,0.35), 0 0 0 1px rgba(255,255,255,0.06) inset;
    }
    .cv-header::before {
        content: ''; position: absolute; top: -60%; right: -15%;
        width: 400px; height: 400px;
        background: radial-gradient(circle, rgba(59,130,246,0.15) 0%, transparent 60%);
        border-radius: 50%; pointer-events: none;
    }
    .cv-header::after {
        content: ''; position: absolute; bottom: -40%; left: -10%;
        width: 300px; height: 300px;
        background: radial-gradient(circle, rgba(16,185,129,0.08) 0%, transparent 60%);
        border-radius: 50%; pointer-events: none;
    }
    .cv-header-inner {
        position: relative; z-index: 1;
        display: flex; align-items: center; gap: 1.5rem;
    }
    .cv-avatar {
        width: 80px; height: 80px; border-radius: 24px;
        background: linear-gradient(135deg, rgba(59,130,246,0.3), rgba(124,58,237,0.3));
        backdrop-filter: blur(12px);
        border: 2px solid rgba(255,255,255,0.2);
        display: flex; align-items: center; justify-content: center;
        font-size: 2rem; font-weight: 800; flex-shrink: 0;
        box-shadow: 0 8px 32px rgba(37,99,235,0.3);
    }
    .cv-header-info h1 {
        font-size: 1.6rem; font-weight: 800; letter-spacing: -0.02em;
        text-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }
    .cv-header-meta {
        display: flex; align-items: center; gap: 0.7rem;
        margin-top: 0.5rem; font-size: 0.85rem; opacity: 0.75;
    }
    .cv-status {
        background: rgba(16,185,129,0.25); padding: 0.25rem 0.85rem;
        border-radius: 20px; font-size: 0.72rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.06em;
        color: #34d399; border: 1px solid rgba(16,185,129,0.3);
    }
    .cv-status.inactive {
        background: rgba(239,68,68,0.2); color: #f87171;
        border-color: rgba(239,68,68,0.3);
    }

    /* Info grid */
    .cv-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.25rem;
        margin-bottom: 1.5rem;
    }
    .cv-stat {
        background: rgba(255,255,255,0.95); border-radius: 20px;
        padding: 1.5rem 1.75rem;
        border: 1px solid rgba(226,232,240,0.6);
        box-shadow: 0 4px 20px rgba(15,23,42,0.04), 0 1px 3px rgba(15,23,42,0.03);
        transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
        position: relative; overflow: hidden;
    }
    .cv-stat::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
        background: linear-gradient(90deg, #3b82f6, #8b5cf6);
        border-radius: 20px 20px 0 0;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .cv-stat:hover {
        box-shadow: 0 12px 40px rgba(15,23,42,0.08), 0 1px 3px rgba(15,23,42,0.05);
        transform: translateY(-4px);
    }
    .cv-stat:hover::before { opacity: 1; }
    .cv-stat:nth-child(2)::before { background: linear-gradient(90deg, #10b981, #34d399); }
    .cv-stat:nth-child(3)::before { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
    .cv-stat-icon {
        width: 40px; height: 40px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        margin-bottom: 0.75rem;
    }
    .cv-stat-icon svg { width: 20px; height: 20px; }
    .cv-stat-label {
        font-size: 0.7rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.08em; color: #94a3b8; margin-bottom: 0.5rem;
    }
    .cv-stat-val {
        font-size: 1.35rem; font-weight: 800; color: #0f172a;
        letter-spacing: -0.02em;
    }
    .cv-stat-val.green { color: #059669; }
    .cv-stat-val.red { color: #dc2626; }
    .cv-stat-val.blue { color: #2563eb; }

    /* Details card */
    .cv-card {
        background: rgba(255,255,255,0.95); border-radius: 20px;
        border: 1px solid rgba(226,232,240,0.6);
        box-shadow: 0 4px 20px rgba(15,23,42,0.04), 0 1px 3px rgba(15,23,42,0.03);
        overflow: hidden;
        transition: box-shadow 0.3s ease;
    }
    .cv-card:hover {
        box-shadow: 0 8px 30px rgba(15,23,42,0.06), 0 1px 3px rgba(15,23,42,0.04);
    }
    .cv-card-head {
        padding: 1.25rem 1.75rem;
        border-bottom: 1px solid rgba(241,245,249,0.8);
        display: flex; align-items: center; justify-content: space-between;
        background: linear-gradient(135deg, #fafbff, #f8fafc);
    }
    .cv-card-title {
        font-size: 0.92rem; font-weight: 800; color: #0f172a;
        display: flex; align-items: center; gap: 0.6rem;
    }
    .cv-card-title svg { width: 20px; height: 20px; color: #3b82f6; }
    .cv-card-body { padding: 0.5rem 1.75rem 1.25rem; }

    .cv-detail-row {
        display: grid;
        grid-template-columns: 150px 1fr;
        gap: 1.25rem; padding: 1rem 0;
        border-bottom: 1px solid rgba(241,245,249,0.6);
        align-items: flex-start;
        transition: background 0.15s ease;
    }
    .cv-detail-row:hover {
        background: rgba(248,250,252,0.4);
        margin: 0 -1.75rem; padding-left: 1.75rem; padding-right: 1.75rem;
    }
    .cv-detail-row:last-child { border-bottom: none; }
    .cv-detail-label {
        font-size: 0.72rem; font-weight: 800; text-transform: uppercase;
        letter-spacing: 0.07em; color: #94a3b8;
        display: flex; align-items: center; gap: 0.4rem;
        padding-top: 0.1rem;
    }
    .cv-detail-val {
        font-size: 0.88rem; font-weight: 600; color: #1e293b;
        word-break: break-word; line-height: 1.5;
    }
    .cv-detail-val.muted { color: #94a3b8; font-weight: 500; font-style: italic; }

    /* Notes */
    .cv-notes {
        background: linear-gradient(135deg, #f8fafc, #f0f4ff);
        border-radius: 14px;
        padding: 1.25rem 1.5rem;
        font-size: 0.85rem; color: #475569; line-height: 1.7;
        border: 1px solid rgba(226,232,240,0.6);
        position: relative;
    }
    .cv-notes::before {
        content: ''; position: absolute; top: 0; left: 0; width: 3px; height: 100%;
        background: linear-gradient(180deg, #3b82f6, #8b5cf6);
        border-radius: 14px 0 0 14px;
    }

    /* Actions */
    .cv-actions {
        display: flex; gap: 0.75rem;
        margin-top: 1.5rem;
        padding-top: 1rem;
        border-top: 1px solid rgba(226,232,240,0.6);
    }
    .cv-actions .btn {
        font-size: 0.82rem; font-weight: 700;
        padding: 0.65rem 1.25rem; border-radius: 12px;
        display: inline-flex; align-items: center; gap: 0.5rem;
        transition: all 0.2s ease;
    }
    .cv-actions .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .cv-grid { grid-template-columns: repeat(2, 1fr); gap: 0.875rem; }
        .cv-header { padding: 1.75rem; border-radius: 20px; }
        .cv-header-inner { flex-direction: column; align-items: flex-start; }
        .cv-avatar { width: 64px; height: 64px; font-size: 1.6rem; }
        .cv-detail-row {
            grid-template-columns: 1fr; gap: 0.35rem;
            padding: 0.875rem 0;
        }
        .cv-detail-row:hover { margin: 0 -1rem; padding-left: 1rem; padding-right: 1rem; }
        .cv-detail-label { font-size: 0.65rem; }
        .cv-card-body { padding: 0.5rem 1.25rem 1rem; }
        .cv-actions { flex-wrap: wrap; }
    }
    @media (max-width: 480px) {
        .cv-grid { grid-template-columns: 1fr; }
        .cv-header { padding: 1.5rem; }
        .cv-stat { padding: 1.25rem; }
        .cv-stat-val { font-size: 1.15rem; }
    }
</style>
@endsection

@section('content')
<div class="dash-content">
<div class="cv-wrap">

    {{-- Header Card --}}
    <div class="cv-header">
        <div class="cv-header-inner">
            <div class="cv-avatar">
                {{ strtoupper(substr($customer->name, 0, 1)) }}
            </div>
            <div class="cv-header-info">
                <h1>{{ $customer->name }}</h1>
                <div class="cv-header-meta">
                    <span class="cv-status">{{ $customer->status }}</span>
                    @if($customer->group)
                        <span>&middot;</span>
                        <span>{{ $customer->group->name }}</span>
                    @endif
                    <span>&middot;</span>
                    <span>ID #{{ $customer->id }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Stats --}}
    <div class="cv-grid">
        <div class="cv-stat">
            <div class="cv-stat-label">Balance</div>
            <div class="cv-stat-val {{ $customer->balance > 0 ? 'red' : 'blue' }}">
                TSh {{ number_format($customer->balance ?? 0, 2) }}
            </div>
        </div>
        <div class="cv-stat">
            <div class="cv-stat-label">Loyalty Points</div>
            <div class="cv-stat-val green">
                {{ number_format($customer->loyalty_points ?? 0, 2) }}
            </div>
        </div>
        <div class="cv-stat">
            <div class="cv-stat-label">Credit Limit</div>
            <div class="cv-stat-val blue">
                TSh {{ number_format($customer->credit_limit ?? 0, 2) }}
            </div>
        </div>
    </div>

    {{-- Contact Details --}}
    <div class="cv-card" style="margin-bottom: 1.25rem;">
        <div class="cv-card-head">
            <div class="cv-card-title">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Contact Information
            </div>
        </div>
        <div class="cv-card-body">
            <div class="cv-detail-row">
                <div class="cv-detail-label">Email</div>
                <div class="cv-detail-val {{ $customer->email ? '' : 'muted' }}">
                    {{ $customer->email ?: 'Not provided' }}
                </div>
            </div>
            <div class="cv-detail-row">
                <div class="cv-detail-label">Phone</div>
                <div class="cv-detail-val {{ $customer->phone ? '' : 'muted' }}">
                    {{ $customer->phone ?: 'Not provided' }}
                </div>
            </div>
            <div class="cv-detail-row">
                <div class="cv-detail-label">Address</div>
                <div class="cv-detail-val {{ $customer->address ? '' : 'muted' }}">
                    {{ $customer->address ?: 'Not provided' }}
                </div>
            </div>
            <div class="cv-detail-row">
                <div class="cv-detail-label">City</div>
                <div class="cv-detail-val {{ $customer->city ? '' : 'muted' }}">
                    {{ $customer->city ?: 'Not provided' }}
                </div>
            </div>
            <div class="cv-detail-row">
                <div class="cv-detail-label">Country</div>
                <div class="cv-detail-val {{ $customer->country ? '' : 'muted' }}">
                    {{ $customer->country ?: 'Not provided' }}
                </div>
            </div>
        </div>
    </div>

    {{-- Additional Info --}}
    <div class="cv-card" style="margin-bottom: 1.25rem;">
        <div class="cv-card-head">
            <div class="cv-card-title">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Additional Details
            </div>
        </div>
        <div class="cv-card-body">
            <div class="cv-detail-row">
                <div class="cv-detail-label">Customer Group</div>
                <div class="cv-detail-val {{ $customer->group ? '' : 'muted' }}">
                    {{ $customer->group?->name ?? 'No group assigned' }}
                </div>
            </div>
            <div class="cv-detail-row">
                <div class="cv-detail-label">Lead Source</div>
                <div class="cv-detail-val {{ $customer->lead_source ? '' : 'muted' }}">
                    {{ $customer->lead_source ?: 'Not recorded' }}
                </div>
            </div>
            <div class="cv-detail-row">
                <div class="cv-detail-label">Last Contact</div>
                <div class="cv-detail-val {{ $customer->last_contact_date ? '' : 'muted' }}">
                    {{ $customer->last_contact_date ? \Carbon\Carbon::parse($customer->last_contact_date)->format('M d, Y') : 'Never contacted' }}
                </div>
            </div>
            <div class="cv-detail-row">
                <div class="cv-detail-label">Created</div>
                <div class="cv-detail-val">
                    {{ $customer->created_at->format('M d, Y \a\t g:ia') }}
                </div>
            </div>
        </div>
    </div>

    {{-- Notes --}}
    @if($customer->notes)
    <div class="cv-card" style="margin-bottom: 1.25rem;">
        <div class="cv-card-head">
            <div class="cv-card-title">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Notes
            </div>
        </div>
        <div class="cv-card-body">
            <div class="cv-notes">{{ $customer->notes }}</div>
        </div>
    </div>
    @endif

    {{-- Actions --}}
    <div class="cv-actions">
        <a href="{{ route('dashboard.contacts.customers') }}" class="btn btn-secondary">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to List
        </a>
        <button class="btn btn-primary" onclick="editCustomer({{ $customer->id }})">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Edit Customer
        </button>
        <button class="btn btn-danger" onclick="deleteCustomer({{ $customer->id }}, '{{ $customer->name }}')">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            Delete
        </button>
    </div>

</div>
</div>
@endsection

@section('scripts')
<script>
const API = '/api/dashboard/customers';

async function editCustomer(id) {
    try {
        const c = await apiFetch(`${API}/${id}`);
        // Redirect to customers list and open edit modal
        window.location.href = '{{ route('dashboard.contacts.customers') }}?edit=' + id;
    } catch(e) { showToast('Failed to load customer','error'); }
}

function deleteCustomer(id, name) {
    showConfirm('Delete Customer', `Delete "${name}"? This cannot be undone.`, async() => {
        try {
            await apiFetch(`${API}/${id}`, {method: 'DELETE'});
            showToast('Customer deleted!');
            setTimeout(() => window.location.href = '{{ route('dashboard.contacts.customers') }}', 600);
        } catch(e) { showToast('Delete failed','error'); }
    });
}
</script>
@endsection
