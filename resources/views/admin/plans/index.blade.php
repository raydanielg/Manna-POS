@extends('admin.layouts.app')
@section('page_title', 'Subscription Plans')
@section('content')
<div class="page-card">
    <div class="card-header">
        <div class="card-title">Subscription Plans</div>
        <div class="filters-row">
            <div class="search-wrap">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                <input type="text" id="searchInput" placeholder="Search plans..." oninput="loadPlans()">
            </div>
            <button class="btn btn-success" onclick="openPlanModal()">+ Add Plan</button>
        </div>
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:1rem;padding:1.25rem;" id="plansGrid">
        <div class="tbl-empty">Loading...</div>
    </div>
</div>

<div class="modal-overlay" id="planModal">
    <div class="modal" style="max-width:680px;">
        <div class="modal-header">
            <div class="modal-title" id="planModalTitle">Add Plan</div>
            <button class="modal-close" onclick="closeModal('planModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="planForm">
                <div class="form-row">
                    <div class="form-group"><label>Plan Name *</label><input name="name" class="form-control" required><div class="invalid-feedback"></div></div>
                    <div class="form-group"><label>Currency</label><input name="currency" class="form-control" value="TZS" maxlength="3"><div class="invalid-feedback"></div></div>
                </div>
                <div class="form-group"><label>Description</label><textarea name="description" class="form-control" rows="2"></textarea><div class="invalid-feedback"></div></div>
                <div class="form-row">
                    <div class="form-group"><label>Monthly Price *</label><input type="number" step="0.01" name="price_monthly" class="form-control" required><div class="invalid-feedback"></div></div>
                    <div class="form-group"><label>Yearly Price</label><input type="number" step="0.01" name="price_yearly" class="form-control"><div class="invalid-feedback"></div></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Max Users</label><input type="number" name="max_users" class="form-control" value="1"><div class="invalid-feedback"></div></div>
                    <div class="form-group"><label>Max Products</label><input type="number" name="max_products" class="form-control" value="100"><div class="invalid-feedback"></div></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Max Locations</label><input type="number" name="max_locations" class="form-control" value="1"><div class="invalid-feedback"></div></div>
                    <div class="form-group"><label>Sort Order</label><input type="number" name="sort_order" class="form-control" value="0"><div class="invalid-feedback"></div></div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label style="display:flex;align-items:center;gap:0.5rem;cursor:pointer;">
                            <input type="checkbox" name="is_featured" value="1" style="width:16px;height:16px;"> Featured Plan
                        </label>
                    </div>
                    <div class="form-group">
                        <label style="display:flex;align-items:center;gap:0.5rem;cursor:pointer;">
                            <input type="checkbox" name="is_active" value="1" checked style="width:16px;height:16px;"> Active
                        </label>
                    </div>
                </div>
                <input type="hidden" name="edit_id" id="editPlanId" value="">
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('planModal')">Cancel</button>
            <button class="btn btn-primary" id="savePlanBtn" onclick="savePlan()">Save Plan</button>
        </div>
    </div>
</div>
@endsection
@section('scripts')
const API_PLANS = '/api/dashboard/plans';
let editPlanId = null;

async function loadPlans() {
    const s = document.getElementById('searchInput').value;
    const grid = document.getElementById('plansGrid');
    try {
        const plans = await apiFetch(`${API_PLANS}?search=${encodeURIComponent(s)}`);
        if (!plans.length) { grid.innerHTML = '<div class="tbl-empty" style="grid-column:1/-1;">No plans found</div>'; return; }
        grid.innerHTML = plans.map(p => `<div class="page-card" style="${p.is_featured ? 'border-color:#2563eb;box-shadow:0 4px 20px rgba(37,99,235,0.15);' : ''}">
            <div style="padding:1.25rem 1.25rem 0.75rem;border-bottom:1px solid #f1f5f9;">
                ${p.is_featured ? '<span class="badge badge-info" style="float:right;">Featured</span>' : ''}
                <div style="font-size:1.1rem;font-weight:800;color:#0f172a;">${p.name}</div>
                <div style="font-size:0.78rem;color:#64748b;margin-top:0.25rem;">${p.description || ''}</div>
            </div>
            <div style="padding:0.75rem 1.25rem;background:#fafbff;">
                <div style="font-size:1.5rem;font-weight:900;color:#0f172a;">${p.currency || 'TZS'} ${Number(p.price_monthly).toLocaleString()}<span style="font-size:0.78rem;font-weight:400;color:#94a3b8;">/mo</span></div>
                ${p.price_yearly ? `<div style="font-size:0.72rem;color:#16a34a;font-weight:600;">${p.currency} ${Number(p.price_yearly).toLocaleString()}/yr</div>` : ''}
            </div>
            <div style="padding:0.75rem 1.25rem;display:grid;grid-template-columns:1fr 1fr;gap:0.5rem;font-size:0.75rem;color:#64748b;">
                <span>Users: <strong>${p.max_users ?? '∞'}</strong></span>
                <span>Products: <strong>${p.max_products ?? '∞'}</strong></span>
                <span>Locations: <strong>${p.max_locations ?? '∞'}</strong></span>
                <span>Subscribers: <strong>${p.active_subscriptions_count || 0}</strong></span>
            </div>
            <div style="padding:0.75rem 1.25rem;border-top:1px solid #f1f5f9;display:flex;gap:0.5rem;">
                <button class="btn btn-primary btn-xs" onclick="editPlan(${p.id})">Edit</button>
                <button class="btn btn-danger btn-xs" onclick="deletePlan(${p.id},'${p.name}')">Delete</button>
            </div>
        </div>`).join('');
    } catch (e) { grid.innerHTML = '<div class="tbl-empty" style="grid-column:1/-1;">Error loading plans</div>'; }
}

function openPlanModal() {
    editPlanId = null;
    document.getElementById('planModalTitle').textContent = 'Add Plan';
    document.getElementById('planForm').reset();
    document.querySelector('#planForm [name="is_active"]').checked = true;
    document.querySelector('#planForm [name="currency"]').value = 'TZS';
    clearFormErrors('planForm');
    openModal('planModal');
}

async function editPlan(id) {
    try {
        const plans = await apiFetch(API_PLANS);
        const p = plans.find(x => x.id === id);
        if (!p) throw new Error('Plan not found');
        editPlanId = id;
        document.getElementById('planModalTitle').textContent = 'Edit Plan';
        const form = document.getElementById('planForm');
        form.querySelector('[name="name"]').value = p.name || '';
        form.querySelector('[name="description"]').value = p.description || '';
        form.querySelector('[name="price_monthly"]').value = p.price_monthly || '';
        form.querySelector('[name="price_yearly"]').value = p.price_yearly || '';
        form.querySelector('[name="currency"]').value = p.currency || 'TZS';
        form.querySelector('[name="max_users"]').value = p.max_users || '';
        form.querySelector('[name="max_products"]').value = p.max_products || '';
        form.querySelector('[name="max_locations"]').value = p.max_locations || '';
        form.querySelector('[name="sort_order"]').value = p.sort_order || 0;
        form.querySelector('[name="is_featured"]').checked = !!p.is_featured;
        form.querySelector('[name="is_active"]').checked = p.is_active !== false;
        clearFormErrors('planForm');
        openModal('planModal');
    } catch (e) { Swal.fire('Error', 'Failed to load plan', 'error'); }
}

async function savePlan() {
    clearFormErrors('planForm');
    const form = document.getElementById('planForm');
    const data = {
        name: form.querySelector('[name="name"]').value,
        description: form.querySelector('[name="description"]').value,
        price_monthly: form.querySelector('[name="price_monthly"]').value,
        price_yearly: form.querySelector('[name="price_yearly"]').value,
        currency: form.querySelector('[name="currency"]').value,
        max_users: form.querySelector('[name="max_users"]').value || null,
        max_products: form.querySelector('[name="max_products"]').value || null,
        max_locations: form.querySelector('[name="max_locations"]').value || null,
        sort_order: form.querySelector('[name="sort_order"]').value || 0,
        is_featured: form.querySelector('[name="is_featured"]').checked,
        is_active: form.querySelector('[name="is_active"]').checked,
    };
    const btn = document.getElementById('savePlanBtn');
    btn.disabled = true; btn.textContent = 'Saving...';
    try {
        if (editPlanId) await apiFetch(`${API_PLANS}/${editPlanId}`, { method: 'PUT', body: JSON.stringify(data) });
        else await apiFetch(API_PLANS, { method: 'POST', body: JSON.stringify(data) });
        closeModal('planModal');
        Swal.fire('Success', editPlanId ? 'Plan updated!' : 'Plan created!', 'success');
        loadPlans();
    } catch (e) {
        if (e.errors) showFormErrors('planForm', e.errors);
        else Swal.fire('Error', e.message || 'Save failed', 'error');
    } finally { btn.disabled = false; btn.textContent = 'Save Plan'; }
}

function deletePlan(id, name) {
    Swal.fire({
        title: 'Delete Plan', text: `Delete plan "${name}"?`, icon: 'warning',
        showCancelButton: true, confirmButtonColor: '#dc3545', confirmButtonText: 'Delete',
    }).then(async (r) => {
        if (!r.isConfirmed) return;
        try { await apiFetch(`${API_PLANS}/${id}`, { method: 'DELETE' }); Swal.fire('Deleted', 'Plan deleted!', 'success'); loadPlans(); }
        catch (e) { Swal.fire('Error', e.message || 'Delete failed', 'error'); }
    });
}

loadPlans();
@endsection
