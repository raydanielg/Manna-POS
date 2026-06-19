@extends('layouts.dashboard')
@section('page_title','Subscription Plans')
@section('page_styles')
<style>
/* ── Stats Row ───────────────────────────────────────── */
.stat-row { display:grid; grid-template-columns:repeat(5,1fr); gap:1rem; margin-bottom:1.75rem; }
.stat-card {
    background:#fff; border-radius:16px; border:1.5px solid #eef2f6;
    padding:1.25rem 1.25rem 1.1rem; display:flex; align-items:center; gap:1rem;
    transition:all 0.25s ease;
}
.stat-card:hover { box-shadow:0 8px 24px rgba(15,23,42,0.06); transform:translateY(-2px); border-color:#e2e8f0; }
.stat-icon {
    width:44px; height:44px; border-radius:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0;
}
.stat-icon svg { width:22px; height:22px; }
.stat-body { min-width:0; }
.stat-val { font-size:1.5rem; font-weight:800; color:#0f172a; line-height:1; letter-spacing:-0.02em; }
.stat-lbl { font-size:0.72rem; color:#94a3b8; margin-top:0.2rem; font-weight:600; text-transform:uppercase; letter-spacing:0.06em; }

/* ── Plan Cards ───────────────────────────────────────── */
.plan-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(300px,1fr)); gap:1.5rem; margin-bottom:1.5rem; }
.plan-card {
    background:#fff; border-radius:20px; border:1.5px solid #eef2f6;
    overflow:hidden; transition:all 0.3s cubic-bezier(0.4,0,0.2,1); position:relative;
    display:flex; flex-direction:column;
}
.plan-card:hover { box-shadow:0 20px 40px rgba(15,23,42,0.1); transform:translateY(-5px); border-color:#dbeafe; }
.plan-card.featured { border-color:#2563eb; box-shadow:0 8px 24px rgba(37,99,235,0.12); }
.plan-card.featured:hover { box-shadow:0 24px 48px rgba(37,99,235,0.18); }
.plan-card.inactive { opacity:0.75; border-color:#e2e8f0; }

/* Top accent bar */
.plan-accent { height:4px; width:100%; }

/* Badges */
.plan-badge-wrap { position:absolute; top:1.1rem; right:1.1rem; display:flex; flex-direction:column; gap:0.35rem; align-items:flex-end; }
.plan-badge {
    font-size:0.62rem; font-weight:800; padding:0.3rem 0.75rem;
    border-radius:9999px; letter-spacing:0.1em; text-transform:uppercase;
    display:inline-flex; align-items:center; gap:0.3rem;
}

/* Header */
.plan-header { padding:1.5rem 1.5rem 1.1rem; }
.plan-icon {
    width:52px; height:52px; border-radius:14px;
    display:flex; align-items:center; justify-content:center;
    margin-bottom:0.9rem;
}
.plan-icon svg { width:26px; height:26px; }
.plan-name { font-size:1.1rem; font-weight:800; color:#0f172a; letter-spacing:-0.02em; }
.plan-desc { font-size:0.8rem; color:#64748b; margin-top:0.35rem; line-height:1.5; }

/* Price area */
.plan-price-wrap { padding:1.1rem 1.5rem; background:linear-gradient(180deg,#fafbff 0%,#f8fafc 100%); border-top:1px solid #f1f5f9; border-bottom:1px solid #f1f5f9; }
.plan-price { display:flex; align-items:baseline; gap:0.3rem; }
.plan-currency { font-size:0.9rem; font-weight:700; color:#94a3b8; }
.plan-amount { font-size:2.2rem; font-weight:900; color:#0f172a; letter-spacing:-0.04em; line-height:1; }
.plan-period { font-size:0.8rem; color:#94a3b8; font-weight:500; }
.plan-yearly { font-size:0.75rem; color:#16a34a; font-weight:700; margin-top:0.4rem; display:flex; align-items:center; gap:0.35rem; }
.plan-yearly::before { content:'↓'; font-size:0.65rem; }

/* Limits */
.plan-limits { padding:1rem 1.5rem; display:grid; grid-template-columns:1fr 1fr; gap:0.5rem 1rem; }
.plan-limit-row { display:flex; justify-content:space-between; align-items:center; font-size:0.78rem; }
.plan-limit-label { color:#64748b; display:flex; align-items:center; gap:0.35rem; }
.plan-limit-val { font-weight:700; color:#0f172a; }

/* Features */
.plan-features { padding:1rem 1.5rem; flex:1; border-bottom:1px solid #f1f5f9; }
.plan-feature { display:flex; align-items:center; gap:0.6rem; font-size:0.82rem; color:#374151; margin-bottom:0.55rem; }
.plan-feature svg { width:16px; height:16px; flex-shrink:0; }
.plan-feature.has { color:#15803d; }
.plan-feature.no  { color:#cbd5e1; text-decoration:line-through; }

/* Footer */
.plan-footer { padding:1.1rem 1.5rem; display:flex; gap:0.6rem; }
.plan-footer .btn { border-radius:10px; font-weight:600; font-size:0.82rem; }

/* Subscribers pill */
.sub-pill {
    display:inline-flex; align-items:center; gap:0.35rem;
    font-size:0.72rem; font-weight:700; padding:0.25rem 0.6rem;
    border-radius:9999px; background:#f1f5f9; color:#475569;
}

/* Empty state */
.empty-plans { grid-column:1/-1; text-align:center; padding:4rem 2rem; background:#fff; border-radius:20px; border:1.5px solid #eef2f6; }
.empty-plans svg { margin:0 auto 1rem; display:block; color:#cbd5e1; }
.empty-plans h3 { font-size:1rem; font-weight:700; color:#475569; margin-bottom:0.35rem; }
.empty-plans p { font-size:0.82rem; color:#94a3b8; margin-bottom:1.25rem; }

.color-preview { width:12px; height:12px; border-radius:3px; display:inline-block; margin-right:4px; }
.toggle-wrap { display:flex; align-items:center; gap:0.75rem; }
.toggle { position:relative; width:40px; height:22px; }
.toggle input { opacity:0; width:0; height:0; }
.toggle-slider {
    position:absolute; inset:0;
    background:#e2e8f0; border-radius:9999px;
    cursor:pointer; transition:background 0.2s;
}
.toggle-slider:before {
    content:''; position:absolute; left:3px; top:3px;
    width:16px; height:16px; border-radius:50%;
    background:#fff; transition:transform 0.2s;
    box-shadow:0 1px 4px rgba(0,0,0,0.15);
}
.toggle input:checked + .toggle-slider { background:#10b981; }
.toggle input:checked + .toggle-slider:before { transform:translateX(18px); }

.features-input-wrap { position:relative; }
.feature-tag { display:inline-flex; align-items:center; gap:0.35rem; padding:0.25rem 0.55rem; background:#eff6ff; color:#2563eb; border-radius:6px; font-size:0.75rem; font-weight:600; margin:0.2rem; }
.feature-tag button { background:none; border:none; color:#93c5fd; cursor:pointer; display:flex; padding:0; font-size:0.9rem; line-height:1; }
.feature-tag button:hover { color:#2563eb; }
.features-display { min-height:44px; padding:0.45rem; border:1px solid #e2e8f0; border-radius:8px; display:flex; flex-wrap:wrap; align-items:center; gap:0.25rem; cursor:text; }
.features-display:focus-within { border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,0.08); }
.features-display input { border:none; outline:none; font-size:0.82rem; min-width:120px; flex:1; padding:0.1rem; background:transparent; }
</style>
@endsection
@section('content')
<div class="dash-content">

{{-- Stats Row --}}
<div class="stat-row" id="statsRow">
    <div class="stat-card">
        <div class="stat-icon" style="background:#eff6ff; color:#2563eb;">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 14h6m-3-3v6m-7 4v-16a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16l-3-2l-2 2l-2-2l-2 2l-2-2l-3 2"/><path d="M14.8 8a2 2 0 0 0-1.8-1h-2a2 2 0 1 0 0 4h2a2 2 0 1 1 0 4h-2a2 2 0 0 1-1.8-1"/><path d="M12 6v1m0 10v1"/></svg>
        </div>
        <div class="stat-body">
            <div class="stat-val" id="st-plans">—</div>
            <div class="stat-lbl">Total Plans</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#f0fdf4; color:#16a34a;">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div class="stat-body">
            <div class="stat-val" id="st-active-plans">—</div>
            <div class="stat-lbl">Active Plans</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fdf4ff; color:#7c3aed;">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        </div>
        <div class="stat-body">
            <div class="stat-val" id="st-subscribers">—</div>
            <div class="stat-lbl">Total Subscribers</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fff7ed; color:#ea580c;">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        </div>
        <div class="stat-body">
            <div class="stat-val" id="st-active-subs">—</div>
            <div class="stat-lbl">Active Subscribers</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fff1f2; color:#e03057;">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div class="stat-body">
            <div class="stat-val" id="st-revenue">—</div>
            <div class="stat-lbl">Est. MRR (TZS)</div>
        </div>
    </div>
</div>

{{-- Header --}}
<div class="page-card" style="margin-bottom:1.25rem;">
    <div class="card-header">
        <div>
            <div class="card-title">Subscription Plans</div>
            <div style="font-size:0.75rem;color:#64748b;margin-top:0.2rem;">Manage pricing tiers for MannaPOS customers</div>
        </div>
        <div class="filters-row">
            <div class="search-wrap">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                <input type="text" id="searchInput" placeholder="Search plans..." oninput="loadPlans()">
            </div>
            <button class="btn btn-primary" onclick="openAddPlan()">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                New Plan
            </button>
        </div>
    </div>
</div>

{{-- Plan Cards Grid --}}
<div id="planGrid" class="plan-grid">
    <div style="grid-column:1/-1;text-align:center;padding:3rem;color:#94a3b8;">
        <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 0.75rem;display:block;"><path stroke-linecap="round" stroke-linejoin="round" d="M4 20h16M4 4h16M9 4v16M15 4v16"/></svg>
        Loading plans...
    </div>
</div>

</div>

{{-- ══ Add / Edit Plan Modal ══════════════════════════════════════════ --}}
<div class="modal-overlay" id="modal-plan">
    <div class="modal modal-lg">
        <div class="modal-header">
            <div class="modal-title" id="modal-plan-title">New Subscription Plan</div>
            <button class="modal-close" onclick="closeModal('modal-plan')">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="modal-body">
            <form id="planForm">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Plan Name *</label>
                        <input name="name" class="form-control" required placeholder="e.g. Pro, Enterprise…">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Badge Color</label>
                        <select name="badge_color" class="form-control">
                            <option value="blue">Blue</option>
                            <option value="green">Green</option>
                            <option value="purple">Purple</option>
                            <option value="orange">Orange</option>
                            <option value="red">Red</option>
                            <option value="gray">Gray</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="2" placeholder="Short plan description…"></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Monthly Price (TZS) *</label>
                        <input name="price_monthly" type="number" step="0.01" class="form-control" required placeholder="0">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Yearly Price (TZS)</label>
                        <input name="price_yearly" type="number" step="0.01" class="form-control" placeholder="Leave blank for 10× monthly">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Max Users</label>
                        <input name="max_users" type="number" class="form-control" placeholder="1" min="1">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Max Products</label>
                        <input name="max_products" type="number" class="form-control" placeholder="100" min="1">
                    </div>
                    <div class="form-group" style="grid-column:1/-1;">
                        <label class="form-label">Max Business Locations</label>
                        <input name="max_locations" type="number" class="form-control" placeholder="1" min="1">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Features <span style="color:#94a3b8;font-weight:400;">(type & press Enter)</span></label>
                    <div class="features-display" id="featuresDisplay" onclick="document.getElementById('featureInput').focus()">
                        <input type="text" id="featureInput" placeholder="Add feature…" onkeydown="handleFeatureKey(event)">
                    </div>
                    <input type="hidden" name="features_json" id="featuresJson">
                </div>
                <div class="form-row" style="gap:2rem;">
                    <div class="form-group">
                        <label class="form-label" style="margin-bottom:0.65rem;">Active</label>
                        <label class="toggle-wrap">
                            <label class="toggle"><input type="checkbox" name="is_active" id="is_active" checked><span class="toggle-slider"></span></label>
                            <span style="font-size:0.82rem;color:#475569;" id="activeLabel">Plan is Active</span>
                        </label>
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="margin-bottom:0.65rem;">Featured Plan</label>
                        <label class="toggle-wrap">
                            <label class="toggle"><input type="checkbox" name="is_featured" id="is_featured"><span class="toggle-slider"></span></label>
                            <span style="font-size:0.82rem;color:#475569;" id="featuredLabel">Not Featured</span>
                        </label>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Sort Order</label>
                        <input name="sort_order" type="number" class="form-control" placeholder="0" min="0" style="width:80px;">
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('modal-plan')">Cancel</button>
            <button class="btn btn-primary" id="savePlanBtn" onclick="savePlan()">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Save Plan
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const API_PLANS = '/api/dashboard/plans';
const API_STATS = '/api/dashboard/plans/stats';

let editPlanId = null;
let planFeatures = [];

const colors = {
    blue:   { bg:'#eff6ff', text:'#2563eb', light:'#dbeafe' },
    green:  { bg:'#f0fdf4', text:'#16a34a', light:'#dcfce7' },
    purple: { bg:'#faf5ff', text:'#7c3aed', light:'#ede9fe' },
    orange: { bg:'#fff7ed', text:'#ea580c', light:'#fed7aa' },
    red:    { bg:'#fff1f2', text:'#e03057', light:'#ffe4e6' },
    gray:   { bg:'#f1f5f9', text:'#475569', light:'#e2e8f0' },
};

const fmtNum = n => Number(n).toLocaleString('en');

// ── Load stats ──────────────────────────────────────────
async function loadStats() {
    try {
        const d = await apiFetch(API_STATS);
        document.getElementById('st-plans').textContent         = d.total_plans;
        document.getElementById('st-active-plans').textContent  = d.active_plans;
        document.getElementById('st-subscribers').textContent   = d.total_subscribers;
        document.getElementById('st-active-subs').textContent   = d.active_subscribers;
        document.getElementById('st-revenue').textContent       = fmtNum(Math.round(d.monthly_revenue || 0));
    } catch {}
}

// ── Load plans ──────────────────────────────────────────
async function loadPlans() {
    const s = document.getElementById('searchInput').value;
    const grid = document.getElementById('planGrid');
    grid.innerHTML = `<div style="grid-column:1/-1;text-align:center;padding:3rem;color:#94a3b8;">
        <div class="animate-spin" style="width:28px;height:28px;border:3px solid #e2e8f0;border-top-color:#2563eb;border-radius:50%;margin:0 auto 0.75rem;"></div>
        Loading plans…
    </div>`;
    try {
        const plans = await apiFetch(`${API_PLANS}?search=${encodeURIComponent(s)}`);
        if (!plans.length) {
            grid.innerHTML = `<div style="grid-column:1/-1;text-align:center;padding:3rem 1rem;">
                <svg width="48" height="48" fill="none" stroke="#cbd5e1" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 1rem;display:block;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 14h6m-3-3v6m-7 4v-16a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16l-3-2l-2 2l-2-2l-2 2l-2-2l-3 2"/></svg>
                <div style="font-size:0.92rem;font-weight:600;color:#64748b;">No plans yet</div>
                <div style="font-size:0.78rem;color:#94a3b8;margin-top:0.25rem;">Click <strong>New Plan</strong> to create your first subscription plan</div>
                <button class="btn btn-primary" style="margin-top:1rem;" onclick="openAddPlan()">Create First Plan</button>
            </div>`;
            return;
        }
        grid.innerHTML = plans.map(p => renderPlanCard(p)).join('');
    } catch (e) {
        grid.innerHTML = `<div style="grid-column:1/-1;text-align:center;padding:2rem;color:#ef4444;">Failed to load plans.</div>`;
    }
}

function renderPlanCard(p) {
    const c = colors[p.badge_color] || colors.blue;
    const feats = Array.isArray(p.features) ? p.features : [];
    return `
    <div class="plan-card ${p.is_featured ? 'featured' : ''}" id="plan-${p.id}">
        ${p.is_featured ? `<span class="plan-badge" style="background:${c.bg};color:${c.text};">Featured</span>` : ''}
        ${!p.is_active ? `<span class="plan-badge" style="background:#f1f5f9;color:#94a3b8;top:${p.is_featured?'2.5rem':'1rem'}">Inactive</span>` : ''}
        <div class="plan-header">
            <div class="plan-icon" style="background:${c.bg};">
                <svg width="26" height="26" fill="none" stroke="${c.text}" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 14h6m-3-3v6m-7 4v-16a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16l-3-2l-2 2l-2-2l-2 2l-2-2l-3 2"/><path d="M14.8 8a2 2 0 0 0-1.8-1h-2a2 2 0 1 0 0 4h2a2 2 0 1 1 0 4h-2a2 2 0 0 1-1.8-1"/><path d="M12 6v1m0 10v1"/></svg>
            </div>
            <div class="plan-name">${p.name}</div>
            ${p.description ? `<div class="plan-desc">${p.description}</div>` : ''}
        </div>
        <div class="plan-price-wrap">
            <div class="plan-price">
                <span class="plan-currency">TZS</span>
                <span class="plan-amount">${fmtNum(p.price_monthly)}</span>
                <span class="plan-period">/mo</span>
            </div>
            ${p.price_yearly > 0 ? `<div class="plan-yearly">TZS ${fmtNum(p.price_yearly)}/yr — save ${Math.round(100-(p.price_yearly/(p.price_monthly*12))*100)}%</div>` : ''}
        </div>
        <div class="plan-limits">
            <div class="plan-limit-row">
                <span class="plan-limit-label">👥 Users</span>
                <span class="plan-limit-val">${p.max_users === -1 ? '∞ Unlimited' : p.max_users}</span>
            </div>
            <div class="plan-limit-row">
                <span class="plan-limit-label">📦 Products</span>
                <span class="plan-limit-val">${p.max_products === -1 ? '∞ Unlimited' : fmtNum(p.max_products)}</span>
            </div>
            <div class="plan-limit-row">
                <span class="plan-limit-label">🏪 Locations</span>
                <span class="plan-limit-val">${p.max_locations === -1 ? '∞ Unlimited' : p.max_locations}</span>
            </div>
            <div class="plan-limit-row">
                <span class="plan-limit-label">📊 Active Subs</span>
                <span class="plan-limit-val" style="color:${c.text}">${p.active_subscriptions_count || 0}</span>
            </div>
        </div>
        ${feats.length ? `
        <div class="plan-features">
            ${feats.slice(0,5).map(f=>`<div class="plan-feature has">
                <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                ${f}
            </div>`).join('')}
            ${feats.length > 5 ? `<div style="font-size:0.72rem;color:#94a3b8;margin-top:0.25rem;">+${feats.length-5} more features</div>` : ''}
        </div>` : ''}
        <div class="plan-footer">
            <button class="btn btn-edit btn-sm" style="flex:1;" onclick="editPlan(${p.id})">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit
            </button>
            <button class="btn btn-view btn-sm" style="flex:1;" onclick="window.location='/dashboard/plan-management/subscriptions?plan_id=${p.id}'">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
                Subscribers
            </button>
            <button class="btn btn-delete btn-icon btn-sm" onclick="deletePlan(${p.id},'${p.name}')">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
        </div>
    </div>`;
}

// ── Feature tags ─────────────────────────────────────────
function handleFeatureKey(e) {
    if (e.key === 'Enter' || e.key === ',') {
        e.preventDefault();
        const val = e.target.value.trim();
        if (val) { addFeature(val); e.target.value = ''; }
    }
}

function addFeature(text) {
    if (!planFeatures.includes(text)) {
        planFeatures.push(text);
        renderFeatureTags();
    }
}

function removeFeature(idx) {
    planFeatures.splice(idx, 1);
    renderFeatureTags();
}

function renderFeatureTags() {
    const display = document.getElementById('featuresDisplay');
    const input   = document.getElementById('featureInput');
    const tags    = planFeatures.map((f,i) => `
        <span class="feature-tag">
            ${f}
            <button type="button" onclick="removeFeature(${i})">×</button>
        </span>`).join('');
    display.innerHTML = tags;
    display.appendChild(input);
    document.getElementById('featuresJson').value = JSON.stringify(planFeatures);
}

// ── Toggle labels ────────────────────────────────────────
document.getElementById('is_active').addEventListener('change', function() {
    document.getElementById('activeLabel').textContent = this.checked ? 'Plan is Active' : 'Plan is Inactive';
});
document.getElementById('is_featured').addEventListener('change', function() {
    document.getElementById('featuredLabel').textContent = this.checked ? 'Featured Plan' : 'Not Featured';
});

// ── Open modals ──────────────────────────────────────────
function openAddPlan() {
    editPlanId = null;
    planFeatures = [];
    document.getElementById('planForm').reset();
    document.getElementById('featuresDisplay').innerHTML = '';
    document.getElementById('featuresDisplay').appendChild(document.getElementById('featureInput'));
    document.getElementById('featureInput').value = '';
    document.getElementById('modal-plan-title').textContent = 'New Subscription Plan';
    document.getElementById('savePlanBtn').textContent = 'Save Plan';
    document.getElementById('is_active').checked = true;
    document.getElementById('is_featured').checked = false;
    document.getElementById('activeLabel').textContent = 'Plan is Active';
    document.getElementById('featuredLabel').textContent = 'Not Featured';
    openModal('modal-plan');
}

async function editPlan(id) {
    try {
        const plans = await apiFetch(API_PLANS);
        const p = plans.find(x => x.id === id);
        if (!p) return;

        editPlanId = id;
        planFeatures = Array.isArray(p.features) ? [...p.features] : [];

        const f = document.getElementById('planForm');
        f.querySelector('[name=name]').value          = p.name || '';
        f.querySelector('[name=description]').value   = p.description || '';
        f.querySelector('[name=price_monthly]').value = p.price_monthly || '';
        f.querySelector('[name=price_yearly]').value  = p.price_yearly || '';
        f.querySelector('[name=max_users]').value     = p.max_users || '';
        f.querySelector('[name=max_products]').value  = p.max_products || '';
        f.querySelector('[name=max_locations]').value = p.max_locations || '';
        f.querySelector('[name=sort_order]').value    = p.sort_order || 0;
        f.querySelector('[name=badge_color]').value   = p.badge_color || 'blue';
        document.getElementById('is_active').checked   = !!p.is_active;
        document.getElementById('is_featured').checked = !!p.is_featured;
        document.getElementById('activeLabel').textContent   = p.is_active ? 'Plan is Active' : 'Plan is Inactive';
        document.getElementById('featuredLabel').textContent = p.is_featured ? 'Featured Plan' : 'Not Featured';

        renderFeatureTags();
        document.getElementById('modal-plan-title').textContent = `Edit Plan — ${p.name}`;
        document.getElementById('savePlanBtn').textContent = 'Update Plan';
        openModal('modal-plan');
    } catch(e) { showToast('Failed to load plan details','error'); }
}

// ── Save ─────────────────────────────────────────────────
async function savePlan() {
    clearFormErrors('planForm');
    const btn = document.getElementById('savePlanBtn');
    const f   = document.getElementById('planForm');

    const payload = {
        name:          f.querySelector('[name=name]').value.trim(),
        description:   f.querySelector('[name=description]').value.trim(),
        price_monthly: parseFloat(f.querySelector('[name=price_monthly]').value) || 0,
        price_yearly:  parseFloat(f.querySelector('[name=price_yearly]').value)  || null,
        max_users:     parseInt(f.querySelector('[name=max_users]').value)        || 1,
        max_products:  parseInt(f.querySelector('[name=max_products]').value)     || 100,
        max_locations: parseInt(f.querySelector('[name=max_locations]').value)    || 1,
        features:      planFeatures,
        is_active:     document.getElementById('is_active').checked,
        is_featured:   document.getElementById('is_featured').checked,
        sort_order:    parseInt(f.querySelector('[name=sort_order]').value)       || 0,
        badge_color:   f.querySelector('[name=badge_color]').value,
    };

    btn.disabled = true;
    btn.textContent = editPlanId ? 'Updating…' : 'Saving…';

    try {
        if (editPlanId) {
            await apiFetch(`${API_PLANS}/${editPlanId}`, { method:'PUT', body: JSON.stringify(payload) });
            showToast('Plan updated successfully');
        } else {
            await apiFetch(API_PLANS, { method:'POST', body: JSON.stringify(payload) });
            showToast('Plan created successfully');
        }
        closeModal('modal-plan');
        loadPlans();
        loadStats();
    } catch(e) {
        showFormErrors('planForm', e.errors);
        showToast(e.message || 'Failed to save plan','error');
    } finally {
        btn.disabled = false;
        btn.textContent = editPlanId ? 'Update Plan' : 'Save Plan';
    }
}

// ── Delete ────────────────────────────────────────────────
function deletePlan(id, name) {
    showConfirm('Delete Plan?', `This will permanently delete the plan "${name}". This cannot be undone.`, async () => {
        try {
            await apiFetch(`${API_PLANS}/${id}`, { method:'DELETE' });
            showToast(`Plan "${name}" deleted`);
            loadPlans();
            loadStats();
        } catch(e) {
            showToast(e.message || 'Failed to delete plan','error');
        }
    });
}

// ── Init ──────────────────────────────────────────────────
loadPlans();
loadStats();
</script>
@endsection
