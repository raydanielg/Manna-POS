@extends('layouts.dashboard')

@section('title', 'Online Orders')

@section('content')
<style>
.order-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}
.order-stat-card {
    background: rgba(255,255,255,0.85);
    border-radius: 14px;
    padding: 1.1rem 1.25rem;
    border: 1px solid rgba(226,232,240,0.6);
    box-shadow: 0 2px 10px rgba(15,23,42,0.03);
    backdrop-filter: blur(6px);
    transition: all 0.25s ease;
    display: flex;
    align-items: center;
    gap: 0.9rem;
}
.order-stat-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(15,23,42,0.06); }
.order-stat-card .kpi-icon {
    width: 42px; height: 42px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.order-stat-card .kpi-icon svg { width: 20px; height: 20px; }
.order-stat-card.total .kpi-icon { background: #eff6ff; color: #2563eb; }
.order-stat-card.pending .kpi-icon { background: #fffbeb; color: #d97706; }
.order-stat-card.completed .kpi-icon { background: #ecfdf5; color: #059669; }
.order-stat-card.cancelled .kpi-icon { background: #fef2f2; color: #dc2626; }
.order-stat-card.revenue .kpi-icon { background: #f5f3ff; color: #7c3aed; }
.order-stat-card .kpi-body { flex: 1; }
.order-stat-card .label { font-size: 0.72rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 0.2rem; }
.order-stat-card .value { font-size: 1.3rem; font-weight: 800; color: #0f172a; letter-spacing: -0.02em; }

.order-table-wrap {
    background: rgba(255,255,255,0.85);
    border-radius: 16px;
    border: 1px solid rgba(226,232,240,0.5);
    overflow: hidden;
    box-shadow: 0 2px 12px rgba(15,23,42,0.03);
}
.order-table-wrap .otw-head {
    padding: 1.1rem 1.5rem;
    border-bottom: 1px solid rgba(241,245,249,0.6);
    background: rgba(252,253,254,0.6);
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.order-table-wrap .otw-head .otw-title { font-size: 0.92rem; font-weight: 800; color: #0f172a; letter-spacing: -0.01em; }
.order-table { width: 100%; border-collapse: collapse; }
.order-table thead th {
    font-size: 0.68rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em;
    color: #64748b; padding: 0.85rem 1.25rem; text-align: left;
    border-bottom: 1px solid rgba(241,245,249,0.6); background: rgba(250,251,255,0.6);
}
.order-table tbody td {
    font-size: 0.82rem; color: #374151; padding: 0.85rem 1.25rem;
    border-bottom: 1px solid rgba(248,250,252,0.6); vertical-align: middle;
}
.order-table tbody tr:last-child td { border-bottom: none; }
.order-table tbody tr:hover td { background: rgba(250,251,255,0.8); }
.order-table .text-right { text-align: right; }

.status-badge {
    display: inline-flex; align-items: center; gap: 0.3rem;
    padding: 0.25rem 0.65rem; border-radius: 99px; font-size: 0.7rem; font-weight: 700; text-transform: capitalize;
}
.status-badge.pending { background: #fef3c7; color: #b45309; }
.status-badge.completed { background: #d1fae5; color: #047857; }
.status-badge.cancelled { background: #fee2e2; color: #b91c1c; }
.status-dot { width: 6px; height: 6px; border-radius: 50%; display: inline-block; }
.status-dot.pending { background: #f59e0b; }
.status-dot.completed { background: #10b981; }
.status-dot.cancelled { background: #ef4444; }

.action-btn {
    background: none; border: none; cursor: pointer; padding: 0.35rem; border-radius: 6px;
    color: #94a3b8; transition: all 0.15s;
}
.action-btn:hover { background: #f1f5f9; color: #475569; }
.action-btn svg { width: 15px; height: 15px; }

.order-items-preview {
    font-size: 0.75rem; color: #64748b; max-width: 280px;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}

.empty-state {
    text-align: center; padding: 3.5rem 1rem; color: #94a3b8;
}
.empty-state svg { margin-bottom: 1rem; opacity: 0.5; }
.empty-state h3 { font-size: 1rem; color: #64748b; margin-bottom: 0.25rem; }
.empty-state p { font-size: 0.82rem; }

/* Order Detail Modal */
.modal-overlay {
    position: fixed; inset: 0; background: rgba(15,23,42,0.65); backdrop-filter: blur(6px);
    z-index: 1000; display: none; align-items: center; justify-content: center; padding: 1rem;
}
.modal-overlay.open { display: flex; }
.modal-box {
    background: #fff; border-radius: 16px; width: 100%; max-width: 520px; max-height: 85vh;
    overflow-y: auto; box-shadow: 0 20px 60px rgba(15,23,42,0.2);
}
.modal-header {
    padding: 1.1rem 1.5rem; border-bottom: 1px solid #f1f5f9;
    display: flex; align-items: center; justify-content: space-between;
}
.modal-header h3 { font-size: 1rem; font-weight: 700; color: #0f172a; }
.modal-close { background: none; border: none; cursor: pointer; color: #94a3b8; padding: 0.3rem; border-radius: 6px; }
.modal-close:hover { background: #f1f5f9; color: #475569; }
.modal-body { padding: 1.25rem 1.5rem; }
.modal-footer {
    padding: 1rem 1.5rem; border-top: 1px solid #f1f5f9;
    display: flex; gap: 0.5rem; justify-content: flex-end;
}

.order-detail-row {
    display: flex; justify-content: space-between; padding: 0.5rem 0;
    border-bottom: 1px solid #f8fafc; font-size: 0.82rem;
}
.order-detail-row .label { color: #64748b; font-weight: 500; }
.order-detail-row .value { color: #0f172a; font-weight: 600; }

.order-items-table {
    width: 100%; border-collapse: collapse; margin-top: 0.75rem;
}
.order-items-table th {
    font-size: 0.7rem; font-weight: 700; color: #94a3b8; text-transform: uppercase;
    letter-spacing: 0.05em; padding: 0.5rem; text-align: left; border-bottom: 1px solid #f1f5f9;
}
.order-items-table td {
    font-size: 0.8rem; color: #374151; padding: 0.5rem; border-bottom: 1px solid #f8fafc;
}
.order-items-table .text-right { text-align: right; }
.order-total-row {
    display: flex; justify-content: space-between; padding: 0.75rem 0;
    font-size: 0.9rem; font-weight: 800; color: #0f172a; border-top: 2px solid #f1f5f9; margin-top: 0.5rem;
}

.status-select {
    padding: 0.35rem 0.7rem; border-radius: 8px; border: 1px solid #e2e8f0;
    font-size: 0.78rem; font-weight: 600; color: #374151; cursor: pointer;
    background: #fff;
}
.status-select:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
</style>

<div class="order-stats-grid">
    <div class="order-stat-card total">
        <div class="label">Total Orders</div>
        <div class="value">{{ number_format($stats['total']) }}</div>
    </div>
    <div class="order-stat-card pending">
        <div class="label">Pending</div>
        <div class="value" style="color:#b45309;">{{ number_format($stats['pending']) }}</div>
    </div>
    <div class="order-stat-card completed">
        <div class="label">Completed</div>
        <div class="value" style="color:#047857;">{{ number_format($stats['completed']) }}</div>
    </div>
    <div class="order-stat-card cancelled">
        <div class="label">Cancelled</div>
        <div class="value" style="color:#b91c1c;">{{ number_format($stats['cancelled']) }}</div>
    </div>
    <div class="order-stat-card revenue">
        <div class="label">Revenue</div>
        <div class="value" style="color:#7c3aed;">{{ $userCurrency }} {{ number_format($stats['revenue'], 2) }}</div>
    </div>
</div>

<div class="order-table-wrap">
    <div class="otw-head">
        <div class="otw-title">Orders</div>
        @if($orders->count() > 0)
        <span style="font-size:0.75rem;color:#64748b;">{{ $orders->count() }} total</span>
        @endif
    </div>
    <table class="order-table">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Items</th>
                <th class="text-right">Total</th>
                <th>Status</th>
                <th>Date</th>
                <th style="text-align:center;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
            <tr>
                <td>
                    <span style="font-weight:700;color:#0f172a;font-size:0.78rem;">{{ $order['id'] }}</span>
                </td>
                <td>
                    <div style="font-weight:600;color:#0f172a;font-size:0.82rem;">{{ $order['customer_name'] }}</div>
                    <div style="font-size:0.72rem;color:#94a3b8;">{{ $order['customer_phone'] }}</div>
                    @if(!empty($order['customer_email']))
                    <div style="font-size:0.72rem;color:#94a3b8;">{{ $order['customer_email'] }}</div>
                    @endif
                </td>
                <td>
                    <div class="order-items-preview" title="{{ collect($order['items'] ?? [])->pluck('name')->implode(', ') }}">
                        {{ collect($order['items'] ?? [])->pluck('name')->implode(', ') }}
                    </div>
                    <div style="font-size:0.7rem;color:#94a3b8;margin-top:0.15rem;">
                        {{ count($order['items'] ?? []) }} item(s)
                    </div>
                </td>
                <td class="text-right">
                    <span style="font-weight:700;color:#0f172a;">{{ $userCurrency }} {{ number_format($order['total'] ?? 0, 2) }}</span>
                </td>
                <td>
                    <span class="status-badge {{ $order['status'] ?? 'pending' }}">
                        <span class="status-dot {{ $order['status'] ?? 'pending' }}"></span>
                        {{ $order['status'] ?? 'pending' }}
                    </span>
                </td>
                <td>
                    <span style="font-size:0.78rem;color:#64748b;">
                        {{ isset($order['created_at']) ? \Carbon\Carbon::parse($order['created_at'])->format('M d, Y H:i') : '—' }}
                    </span>
                </td>
                <td style="text-align:center;">
                    <div style="display:flex;gap:0.3rem;justify-content:center;">
                        <button class="action-btn" onclick="viewOrder({{ $order['index'] }})" title="View Details">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                        <button class="action-btn" onclick="deleteOrder('{{ $order['id'] }}')" title="Delete">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7">
                    <div class="empty-state">
                        <svg width="56" height="56" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <h3>No orders yet</h3>
                        <p>Orders from your public store will appear here.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Order Detail Modal --}}
<div class="modal-overlay" id="orderModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3>Order Details</h3>
            <button class="modal-close" onclick="closeModal()">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="modal-body" id="modalBody">
            {{-- Filled by JS --}}
        </div>
        <div class="modal-footer" id="modalFooter">
            {{-- Filled by JS --}}
        </div>
    </div>
</div>

@php
$ordersJson = $orders->values()->toJson();
@endphp

<script>
const orders = {!! $ordersJson !!};
const userCurrency = '{{ $userCurrency }}';

function viewOrder(index) {
    const order = orders[index];
    if (!order) return;

    const items = order.items || [];
    const itemsHtml = items.length ? `
        <table class="order-items-table">
            <thead><tr><th>Product</th><th class="text-right">Price</th><th class="text-right">Qty</th><th class="text-right">Subtotal</th></tr></thead>
            <tbody>
                ${items.map(i => `
                    <tr>
                        <td><strong>${escapeHtml(i.name)}</strong></td>
                        <td class="text-right">${userCurrency} ${parseFloat(i.price).toFixed(2)}</td>
                        <td class="text-right">${i.qty}</td>
                        <td class="text-right"><strong>${userCurrency} ${parseFloat(i.subtotal).toFixed(2)}</strong></td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
        <div class="order-total-row">
            <span>Total</span>
            <span>${userCurrency} ${parseFloat(order.total).toFixed(2)}</span>
        </div>
    ` : '<p style="color:#94a3b8;font-size:0.82rem;">No items.</p>';

    document.getElementById('modalBody').innerHTML = `
        <div class="order-detail-row"><span class="label">Order ID</span><span class="value">${order.id}</span></div>
        <div class="order-detail-row"><span class="label">Customer</span><span class="value">${escapeHtml(order.customer_name)}</span></div>
        <div class="order-detail-row"><span class="label">Phone</span><span class="value">${escapeHtml(order.customer_phone)}</span></div>
        ${order.customer_email ? `<div class="order-detail-row"><span class="label">Email</span><span class="value">${escapeHtml(order.customer_email)}</span></div>` : ''}
        <div class="order-detail-row"><span class="label">Date</span><span class="value">${order.created_at ? new Date(order.created_at).toLocaleString() : '—'}</span></div>
        <div class="order-detail-row">
            <span class="label">Status</span>
            <span class="value">
                <select class="status-select" id="statusSelect" onchange="updateStatus('${order.id}')">
                    <option value="pending" ${order.status === 'pending' ? 'selected' : ''}>Pending</option>
                    <option value="completed" ${order.status === 'completed' ? 'selected' : ''}>Completed</option>
                    <option value="cancelled" ${order.status === 'cancelled' ? 'selected' : ''}>Cancelled</option>
                </select>
            </span>
        </div>
        ${order.notes ? `<div class="order-detail-row" style="align-items:flex-start;"><span class="label">Notes</span><span class="value" style="max-width:300px;word-break:break-word;">${escapeHtml(order.notes)}</span></div>` : ''}
        <h4 style="font-size:0.85rem;font-weight:700;color:#0f172a;margin:1rem 0 0.3rem 0;">Items</h4>
        ${itemsHtml}
    `;

    document.getElementById('modalFooter').innerHTML = `
        <button class="btn btn-secondary" onclick="closeModal()">Close</button>
    `;

    document.getElementById('orderModal').classList.add('open');
}

function closeModal() {
    document.getElementById('orderModal').classList.remove('open');
}

function updateStatus(orderId) {
    const status = document.getElementById('statusSelect').value;
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch(`/dashboard/online-orders/${orderId}/status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrf,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ status })
    })
    .then(r => r.json())
    .then(data => {
        if (data.message) {
            Swal.fire({
                toast: true, position: 'top-end', timer: 2500, timerProgressBar: true,
                icon: 'success', title: data.message, showConfirmButton: false
            });
            setTimeout(() => window.location.reload(), 1200);
        }
    })
    .catch(() => {
        Swal.fire({ icon: 'error', title: 'Failed to update status', timer: 2000, showConfirmButton: false });
    });
}

function deleteOrder(orderId) {
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    Swal.fire({
        title: 'Delete Order?',
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#94a3b8',
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (!result.isConfirmed) return;

        fetch(`/dashboard/online-orders/${orderId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(r => r.json())
        .then(data => {
            Swal.fire({
                toast: true, position: 'top-end', timer: 2500, timerProgressBar: true,
                icon: 'success', title: data.message, showConfirmButton: false
            });
            setTimeout(() => window.location.reload(), 1200);
        })
        .catch(() => {
            Swal.fire({ icon: 'error', title: 'Delete failed', timer: 2000, showConfirmButton: false });
        });
    });
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Close modal on backdrop click
document.getElementById('orderModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>
@endsection
