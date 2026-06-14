@extends('layouts.dashboard')
@section('page_title','Subscriptions')
@section('page_styles')
<style>
.sub-stats { display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; margin-bottom:1.5rem; }
.sub-stat { background:#fff; border-radius:14px; border:1px solid #e9edf5; padding:1.25rem 1.5rem; display:flex; align-items:center; gap:1rem; transition:box-shadow 0.2s; }
.sub-stat:hover { box-shadow:0 6px 20px rgba(15,23,42,0.07); }
.sub-stat-icon { width:46px; height:46px; border-radius:13px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.sub-stat-val { font-size:1.5rem; font-weight:800; color:#0f172a; line-height:1; }
.sub-stat-lbl { font-size:0.72rem; color:#94a3b8; margin-top:0.2rem; }

.plan-pill {
    display:inline-flex; align-items:center; gap:0.4rem;
    font-size:0.7rem; font-weight:700; padding:0.22rem 0.65rem;
    border-radius:9999px;
}
.status-dot { width:7px; height:7px; border-radius:50%; display:inline-block; margin-right:3px; }

.pagination-wrap { display:flex; align-items:center; justify-content:space-between; padding:0.85rem 1.25rem; border-top:1px solid #f1f5f9; }
.pagination-info { font-size:0.78rem; color:#64748b; }
.pagination-btns { display:flex; gap:0.4rem; }
.page-btn { width:32px; height:32px; border-radius:7px; border:1px solid #e2e8f0; background:#fff; display:flex; align-items:center; justify-content:center; cursor:pointer; font-size:0.8rem; font-weight:600; color:#475569; transition:all 0.15s; }
.page-btn:hover { background:#f1f5f9; }
.page-btn.active { background:#2563eb; color:#fff; border-color:#2563eb; }
.page-btn:disabled { opacity:0.4; cursor:not-allowed; }
</style>
@endsection
@section('content')
<div class="dash-content">

{{-- Stat cards --}}
<div class="sub-stats" id="subStats">
    <div class="sub-stat">
        <div class="sub-stat-icon" style="background:#eff6ff;">
            <svg width="22" height="22" fill="none" stroke="#2563eb" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
        </div>
        <div><div class="sub-stat-val" id="st-total">—</div><div class="sub-stat-lbl">Total Subscriptions</div></div>
    </div>
    <div class="sub-stat">
        <div class="sub-stat-icon" style="background:#f0fdf4;">
            <svg width="22" height="22" fill="none" stroke="#16a34a" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div><div class="sub-stat-val" id="st-active">—</div><div class="sub-stat-lbl">Active</div></div>
    </div>
    <div class="sub-stat">
        <div class="sub-stat-icon" style="background:#fff7ed;">
            <svg width="22" height="22" fill="none" stroke="#ea580c" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div><div class="sub-stat-val" id="st-expired">—</div><div class="sub-stat-lbl">Expired / Cancelled</div></div>
    </div>
    <div class="sub-stat">
        <div class="sub-stat-icon" style="background:#faf5ff;">
            <svg width="22" height="22" fill="none" stroke="#7c3aed" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div><div class="sub-stat-val" id="st-mrr">—</div><div class="sub-stat-lbl">Est. MRR (TZS)</div></div>
    </div>
</div>

{{-- Main card --}}
<div class="page-card">
    <div class="card-header">
        <div>
            <div class="card-title">All Subscriptions</div>
            <div style="font-size:0.75rem;color:#64748b;margin-top:0.2rem;">Manage customer plan subscriptions</div>
        </div>
        <div class="filters-row">
            <div class="search-wrap">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                <input type="text" id="searchInput" placeholder="Search by user or email…" oninput="debounceLoad()">
            </div>
            <select class="form-control" id="filterStatus" onchange="loadList()" style="width:140px;">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="trial">Trial</option>
                <option value="pending">Pending</option>
                <option value="expired">Expired</option>
                <option value="cancelled">Cancelled</option>
            </select>
            <select class="form-control" id="filterPlan" onchange="loadList()" style="width:160px;">
                <option value="">All Plans</option>
            </select>
            <button class="btn btn-primary" onclick="openAddSub()">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Add Subscription
            </button>
        </div>
    </div>
    <div style="overflow-x:auto;">
        <table class="tbl">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Customer</th>
                    <th>Plan</th>
                    <th>Billing</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Started</th>
                    <th>Expires</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                <tr><td colspan="9" class="tbl-empty">Loading...</td></tr>
            </tbody>
        </table>
    </div>
    <div class="pagination-wrap" id="paginationWrap" style="display:none;">
        <div class="pagination-info" id="paginationInfo"></div>
        <div class="pagination-btns" id="paginationBtns"></div>
    </div>
</div>

</div>

{{-- ══ Add / Edit Subscription Modal ════════════════════════════ --}}
<div class="modal-overlay" id="modal-sub">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title" id="modal-sub-title">Add Subscription</div>
            <button class="modal-close" onclick="closeModal('modal-sub')">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="modal-body">
            <form id="subForm">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Customer (User) *</label>
                        <select name="user_id" class="form-control" id="userSelect" required>
                            <option value="">Select user…</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Subscription Plan *</label>
                        <select name="subscription_plan_id" class="form-control" id="planSelect" required onchange="onPlanChange()">
                            <option value="">Select plan…</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Billing Cycle</label>
                        <select name="billing_cycle" class="form-control" id="billingCycle" onchange="onCycleChange()">
                            <option value="monthly">Monthly</option>
                            <option value="yearly">Yearly</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Amount Paid (TZS)</label>
                        <input name="amount_paid" type="number" step="0.01" class="form-control" id="amountPaid" placeholder="Auto-filled from plan">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            <option value="active">Active</option>
                            <option value="trial">Trial</option>
                            <option value="pending">Pending</option>
                            <option value="expired">Expired</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Transaction Ref</label>
                        <input name="transaction_ref" class="form-control" placeholder="Payment reference…">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Starts At</label>
                        <input name="starts_at" type="date" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Expires At</label>
                        <input name="expires_at" type="date" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="2" placeholder="Optional notes…"></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('modal-sub')">Cancel</button>
            <button class="btn btn-primary" id="saveSubBtn" onclick="saveSub()">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Save Subscription
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const API_SUBS  = '/api/dashboard/subscriptions';
const API_PLANS = '/api/dashboard/plans';
const API_USERS = '/api/dashboard/users';
const API_STATS = '/api/dashboard/plans/stats';

let editSubId   = null;
let currentPage = 1;
let allPlans    = [];
let _debounce;

const statusConfig = {
    active:    { color:'#16a34a', bg:'#dcfce7', label:'Active' },
    trial:     { color:'#7c3aed', bg:'#ede9fe', label:'Trial' },
    pending:   { color:'#ca8a04', bg:'#fef9c3', label:'Pending' },
    expired:   { color:'#ea580c', bg:'#fed7aa', label:'Expired' },
    cancelled: { color:'#64748b', bg:'#f1f5f9', label:'Cancelled' },
};

const planColors = {
    blue:'#2563eb', green:'#16a34a', purple:'#7c3aed',
    orange:'#ea580c', red:'#e03057', gray:'#64748b',
};

function fmtNum(n) { return Number(n||0).toLocaleString('en'); }
function fmtDate(d) { return d ? new Date(d).toLocaleDateString('en-GB',{day:'2-digit',month:'short',year:'numeric'}) : '—'; }

function debounceLoad() {
    clearTimeout(_debounce);
    _debounce = setTimeout(() => { currentPage=1; loadList(); }, 300);
}

// ── Stats ─────────────────────────────────────────────────
async function loadStats() {
    try {
        const d = await apiFetch(API_STATS);
        document.getElementById('st-total').textContent   = d.total_subscribers;
        document.getElementById('st-active').textContent  = d.active_subscribers;
        document.getElementById('st-expired').textContent = (d.total_subscribers - d.active_subscribers);
        document.getElementById('st-mrr').textContent     = fmtNum(Math.round(d.monthly_revenue||0));
    } catch {}
}

// ── Load subscriptions ────────────────────────────────────
async function loadList() {
    const s      = document.getElementById('searchInput').value;
    const status = document.getElementById('filterStatus').value;
    const planId = document.getElementById('filterPlan').value;
    const tbody  = document.getElementById('tableBody');

    tbody.innerHTML = '<tr><td colspan="9" class="tbl-empty">Loading...</td></tr>';

    try {
        const params = new URLSearchParams({ page: currentPage, search: s });
        if (status) params.append('status', status);
        if (planId) params.append('plan_id', planId);

        const res = await apiFetch(`${API_SUBS}?${params}`);
        const subs = res.data || [];

        if (!subs.length) {
            tbody.innerHTML = '<tr><td colspan="9" class="tbl-empty">No subscriptions found.</td></tr>';
            document.getElementById('paginationWrap').style.display = 'none';
            return;
        }

        tbody.innerHTML = subs.map((sub, i) => {
            const sc = statusConfig[sub.status] || statusConfig.pending;
            const pc = sub.plan ? (planColors[sub.plan.badge_color] || '#2563eb') : '#2563eb';
            const expDate = sub.expires_at ? new Date(sub.expires_at) : null;
            const isExpiringSoon = expDate && expDate > new Date() && (expDate - new Date()) < 7 * 24 * 60 * 60 * 1000;
            return `<tr>
                <td class="text-slate-400 text-xs">${(currentPage-1)*20+i+1}</td>
                <td>
                    <div style="display:flex;align-items:center;gap:0.6rem;">
                        <div style="width:32px;height:32px;border-radius:9px;background:linear-gradient(135deg,#2563eb,#7c3aed);display:flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:700;color:#fff;flex-shrink:0;">
                            ${(sub.user?.name||'?').charAt(0).toUpperCase()}
                        </div>
                        <div>
                            <div class="font-semibold" style="font-size:0.82rem;">${sub.user?.name||'—'}</div>
                            <div style="font-size:0.72rem;color:#94a3b8;">${sub.user?.email||''}</div>
                        </div>
                    </div>
                </td>
                <td>
                    ${sub.plan ? `<span class="plan-pill" style="background:${pc}18;color:${pc};">${sub.plan.name}</span>` : '<span style="color:#94a3b8;">—</span>'}
                </td>
                <td>
                    <span class="badge ${sub.billing_cycle==='yearly'?'badge-info':'badge-gray'}" style="text-transform:capitalize;">
                        ${sub.billing_cycle||'monthly'}
                    </span>
                </td>
                <td class="font-semibold" style="font-size:0.82rem;">TZS ${fmtNum(sub.amount_paid)}</td>
                <td>
                    <span style="display:inline-flex;align-items:center;gap:0.35rem;font-size:0.7rem;font-weight:700;padding:0.22rem 0.65rem;border-radius:9999px;background:${sc.bg};color:${sc.color};">
                        <span style="width:6px;height:6px;border-radius:50%;background:${sc.color};flex-shrink:0;"></span>
                        ${sc.label}
                    </span>
                </td>
                <td style="font-size:0.78rem;color:#64748b;">${fmtDate(sub.starts_at)}</td>
                <td style="font-size:0.78rem;${isExpiringSoon?'color:#ea580c;font-weight:600;':'color:#64748b;'}">
                    ${expDate ? (isExpiringSoon ? '⚠ ' : '') + fmtDate(sub.expires_at) : '—'}
                </td>
                <td>
                    <div style="display:flex;gap:0.35rem;">
                        <button class="btn btn-sm btn-edit btn-icon" onclick="editSub(${sub.id})" title="Edit">
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>
                        <button class="btn btn-sm btn-delete btn-icon" onclick="deleteSub(${sub.id})" title="Delete">
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </td>
            </tr>`;
        }).join('');

        // Pagination
        const meta = res.meta || {};
        if (meta.last_page > 1) {
            const info = document.getElementById('paginationInfo');
            const btns = document.getElementById('paginationBtns');
            info.textContent = `Showing ${meta.from}–${meta.to} of ${meta.total} subscriptions`;
            let html = `<button class="page-btn" onclick="goPage(${meta.current_page-1})" ${meta.current_page===1?'disabled':''}>‹</button>`;
            for (let p = Math.max(1,meta.current_page-2); p <= Math.min(meta.last_page,meta.current_page+2); p++) {
                html += `<button class="page-btn ${p===meta.current_page?'active':''}" onclick="goPage(${p})">${p}</button>`;
            }
            html += `<button class="page-btn" onclick="goPage(${meta.current_page+1})" ${meta.current_page===meta.last_page?'disabled':''}>›</button>`;
            btns.innerHTML = html;
            document.getElementById('paginationWrap').style.display = 'flex';
        } else {
            document.getElementById('paginationWrap').style.display = 'none';
        }
    } catch(e) {
        tbody.innerHTML = '<tr><td colspan="9" class="tbl-empty" style="color:#ef4444;">Failed to load subscriptions.</td></tr>';
    }
}

function goPage(p) { currentPage = p; loadList(); }

// ── Load plans & users for selects ────────────────────────
async function loadFilters() {
    try {
        allPlans = await apiFetch(API_PLANS);
        const planFilter = document.getElementById('filterPlan');
        const planSelect = document.getElementById('planSelect');
        allPlans.forEach(p => {
            planFilter.innerHTML += `<option value="${p.id}">${p.name}</option>`;
            planSelect.innerHTML += `<option value="${p.id}" data-monthly="${p.price_monthly}" data-yearly="${p.price_yearly}">${p.name} — TZS ${Number(p.price_monthly).toLocaleString()}/mo</option>`;
        });

        const users = await apiFetch(`${API_USERS}?limit=200`);
        const userSelect = document.getElementById('userSelect');
        if (Array.isArray(users)) {
            users.forEach(u => { userSelect.innerHTML += `<option value="${u.id}">${u.name} (${u.email})</option>`; });
        }

        // Check if plan_id filter is pre-set from URL
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('plan_id')) {
            planFilter.value = urlParams.get('plan_id');
        }
    } catch {}
}

// ── Auto-fill amount when plan/cycle changes ──────────────
function onPlanChange() {
    const sel = document.getElementById('planSelect');
    const opt = sel.options[sel.selectedIndex];
    onCycleChange(opt);
}
function onCycleChange(opt) {
    const planOpt = opt || document.getElementById('planSelect').options[document.getElementById('planSelect').selectedIndex];
    if (!planOpt || !planOpt.dataset.monthly) return;
    const cycle = document.getElementById('billingCycle').value;
    const val   = cycle === 'yearly' ? planOpt.dataset.yearly : planOpt.dataset.monthly;
    document.getElementById('amountPaid').value = val || '';
}

// ── Open modals ───────────────────────────────────────────
function openAddSub() {
    editSubId = null;
    document.getElementById('subForm').reset();
    document.getElementById('modal-sub-title').textContent = 'Add Subscription';
    document.getElementById('saveSubBtn').innerHTML = `<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Save Subscription`;
    // Set default start date to today
    document.querySelector('[name=starts_at]').value = new Date().toISOString().split('T')[0];
    openModal('modal-sub');
}

async function editSub(id) {
    try {
        const res = await apiFetch(`${API_SUBS}?page=1`);
        const all = res.data || [];
        const sub = all.find(x => x.id === id);
        if (!sub) { showToast('Subscription not found','error'); return; }

        editSubId = id;
        const f = document.getElementById('subForm');
        f.querySelector('[name=user_id]').value              = sub.user_id;
        f.querySelector('[name=subscription_plan_id]').value = sub.subscription_plan_id;
        f.querySelector('[name=billing_cycle]').value        = sub.billing_cycle || 'monthly';
        f.querySelector('[name=amount_paid]').value          = sub.amount_paid || '';
        f.querySelector('[name=status]').value               = sub.status || 'pending';
        f.querySelector('[name=transaction_ref]').value      = sub.transaction_ref || '';
        f.querySelector('[name=notes]').value                = sub.notes || '';
        f.querySelector('[name=starts_at]').value   = sub.starts_at ? sub.starts_at.substring(0,10) : '';
        f.querySelector('[name=expires_at]').value  = sub.expires_at ? sub.expires_at.substring(0,10) : '';

        document.getElementById('modal-sub-title').textContent = 'Edit Subscription';
        document.getElementById('saveSubBtn').innerHTML = `<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Update`;
        openModal('modal-sub');
    } catch(e) { showToast('Failed to load subscription','error'); }
}

// ── Save ──────────────────────────────────────────────────
async function saveSub() {
    clearFormErrors('subForm');
    const btn = document.getElementById('saveSubBtn');
    const f   = document.getElementById('subForm');

    const payload = {
        user_id:              parseInt(f.querySelector('[name=user_id]').value),
        subscription_plan_id: parseInt(f.querySelector('[name=subscription_plan_id]').value),
        billing_cycle:        f.querySelector('[name=billing_cycle]').value,
        amount_paid:          parseFloat(f.querySelector('[name=amount_paid]').value) || 0,
        status:               f.querySelector('[name=status]').value,
        starts_at:            f.querySelector('[name=starts_at]').value || null,
        expires_at:           f.querySelector('[name=expires_at]').value || null,
        transaction_ref:      f.querySelector('[name=transaction_ref]').value.trim() || null,
        notes:                f.querySelector('[name=notes]').value.trim() || null,
    };

    btn.disabled = true;

    try {
        if (editSubId) {
            await apiFetch(`${API_SUBS}/${editSubId}`, { method:'PUT', body: JSON.stringify(payload) });
            showToast('Subscription updated');
        } else {
            await apiFetch(API_SUBS, { method:'POST', body: JSON.stringify(payload) });
            showToast('Subscription created');
        }
        closeModal('modal-sub');
        loadList();
        loadStats();
    } catch(e) {
        showFormErrors('subForm', e.errors);
        showToast(e.message || 'Failed to save','error');
    } finally {
        btn.disabled = false;
    }
}

// ── Delete ────────────────────────────────────────────────
function deleteSub(id) {
    showConfirm('Remove Subscription?', 'This will remove the subscription record. The user will lose access.', async () => {
        try {
            await apiFetch(`${API_SUBS}/${id}`, { method:'DELETE' });
            showToast('Subscription removed');
            loadList();
            loadStats();
        } catch(e) { showToast(e.message||'Failed','error'); }
    });
}

// ── Init ──────────────────────────────────────────────────
loadFilters().then(() => loadList());
loadStats();
</script>
@endsection
