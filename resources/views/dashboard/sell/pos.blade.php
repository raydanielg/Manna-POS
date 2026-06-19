@extends('layouts.dashboard')
@section('page_title','Point of Sale')
@section('page_styles')
<style>
:root{--pos-primary:#2563eb;--pos-success:#10b981;--pos-danger:#ef4444;--pos-warning:#f59e0b;}

/* ── Layout ────────────────────────────── */
.pos-layout{display:grid;grid-template-columns:1fr 380px;gap:1rem;padding:1.25rem;height:calc(100vh - 90px);}
@media(max-width:1024px){.pos-layout{grid-template-columns:1fr;height:auto;}}

/* ── Products Panel ────────────────────── */
.prod-panel{display:flex;flex-direction:column;gap:.75rem;overflow:hidden;}
.prod-search{background:#fff;border-radius:14px;border:1px solid #e9edf5;padding:.85rem 1rem;display:flex;align-items:center;gap:.6rem;}
.prod-search input{flex:1;border:none;outline:none;font-size:.92rem;font-family:inherit;background:transparent;color:#0f172a;}
.prod-search input::placeholder{color:#94a3b8;}
.prod-grid{flex:1;overflow-y:auto;display:grid;grid-template-columns:repeat(auto-fill,minmax(165px,1fr));gap:.75rem;align-content:start;padding:2px;}

/* Product card */
.prod-card{background:#fff;border:2px solid #e9edf5;border-radius:14px;padding:.85rem;cursor:pointer;transition:all .2s;position:relative;overflow:hidden;}
.prod-card:hover{transform:translateY(-3px);box-shadow:0 8px 24px rgba(15,23,42,.1);border-color:#d1d9e6;}
.prod-card.in-cart{border-color:var(--pos-primary);box-shadow:0 4px 16px rgba(37,99,235,.15);}
.prod-card .name{font-size:.82rem;font-weight:700;color:#0f172a;margin-bottom:.2rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.prod-card .sku{font-size:.62rem;color:#94a3b8;margin-bottom:.4rem;}
.prod-card .price{font-size:1.05rem;font-weight:800;color:var(--pos-primary);}
.prod-card .stock{font-size:.65rem;font-weight:600;margin-top:.2rem;}
.prod-card .stock.low{color:var(--pos-warning);}
.prod-card .stock.out{color:var(--pos-danger);}
.prod-card .stock.ok{color:#64748b;}
.prod-card .cart-badge{position:absolute;top:6px;right:6px;background:var(--pos-primary);color:#fff;font-size:.6rem;font-weight:800;padding:.12rem .45rem;border-radius:6px;min-width:20px;text-align:center;}
.prod-card .sold-out-overlay{position:absolute;inset:0;background:rgba(255,255,255,.7);display:flex;align-items:center;justify-content:center;font-size:.7rem;font-weight:700;color:var(--pos-danger);text-transform:uppercase;letter-spacing:.05em;}

/* No products */
.no-prods{text-align:center;padding:3rem 1rem;grid-column:1/-1;}
.no-prods svg{margin:0 auto 1rem;display:block;}
.no-prods p{color:#94a3b8;font-size:.85rem;margin-bottom:.75rem;}

/* ── Cart Panel ────────────────────────── */
.cart-panel{background:#fff;border-radius:16px;border:1px solid #e9edf5;display:flex;flex-direction:column;overflow:hidden;position:relative;}
.cart-head{padding:1rem 1.25rem;border-bottom:1px solid #f1f5f9;flex-shrink:0;}
.cart-head .title{font-size:.95rem;font-weight:800;color:#0f172a;display:flex;align-items:center;justify-content:space-between;}
.cart-head .title span{font-size:.72rem;font-weight:600;color:#94a3b8;}
.cust-row{display:flex;align-items:center;gap:.4rem;margin-top:.55rem;}
.cust-row select{flex:1;font-size:.78rem;padding:.35rem .5rem;border-radius:8px;border:1px solid #e2e8f0;outline:none;background:#fff;font-family:inherit;color:#0f172a;}
.cust-row .add-cust-btn{width:32px;height:32px;border-radius:8px;border:1px dashed #cbd5e1;background:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#94a3b8;transition:all .15s;flex-shrink:0;}
.cust-row .add-cust-btn:hover{background:#eff6ff;border-color:var(--pos-primary);color:var(--pos-primary);}

.cart-items{flex:1;overflow-y:auto;padding:.5rem 0;min-height:100px;}
.cart-item{display:flex;align-items:center;gap:.5rem;padding:.5rem 1.25rem;border-bottom:1px solid #f8fafc;transition:background .12s;}
.cart-item:hover{background:#fafbff;}
.cart-item .ci-name{flex:1;min-width:0;}
.cart-item .ci-name .ci-title{font-size:.8rem;font-weight:600;color:#0f172a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.cart-item .ci-name .ci-price{font-size:.65rem;color:#94a3b8;}
.cart-item .ci-qty{display:flex;align-items:center;gap:2px;}
.cart-item .ci-qty button{width:24px;height:24px;border-radius:6px;border:1px solid #e2e8f0;background:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:.7rem;font-weight:700;color:#475569;transition:all .12s;}
.cart-item .ci-qty button:hover{background:#f1f5f9;border-color:#cbd5e1;}
.cart-item .ci-qty input{width:32px;text-align:center;border:1px solid #e2e8f0;border-radius:6px;padding:.15rem;font-size:.78rem;font-weight:600;outline:none;}
.cart-item .ci-qty input:focus{border-color:var(--pos-primary);}
.cart-item .ci-total{width:64px;text-align:right;font-size:.82rem;font-weight:700;color:#0f172a;}
.cart-item .ci-remove{background:none;border:none;color:#cbd5e1;cursor:pointer;padding:2px;transition:color .15s;display:flex;}
.cart-item .ci-remove:hover{color:var(--pos-danger);}

.cart-empty{text-align:center;padding:2.5rem 1rem;color:#94a3b8;}
.cart-empty svg{margin:0 auto .5rem;display:block;}

/* ── Cart Summary ──────────────────────── */
.cart-summary{padding:1rem 1.25rem;border-top:1px solid #f1f5f9;background:#fafbff;flex-shrink:0;}
.sum-row{display:flex;justify-content:space-between;align-items:center;font-size:.82rem;margin-bottom:.3rem;}
.sum-row .lbl{color:#64748b;}
.sum-row .val{font-weight:600;color:#0f172a;}
.sum-row.total{font-size:1.1rem;font-weight:800;color:#0f172a;border-top:1px solid #e2e8f0;padding-top:.45rem;margin-top:.35rem;margin-bottom:.6rem;}
.sum-row.total .val{color:var(--pos-primary);}
.disc-row{display:flex;align-items:center;gap:.35rem;}
.disc-row input{width:70px;text-align:right;border:1px solid #e2e8f0;border-radius:6px;padding:.2rem .35rem;font-size:.78rem;outline:none;}
.disc-row input:focus{border-color:var(--pos-primary);}

/* ── Payment Section ───────────────────── */
.pay-row{display:flex;gap:.4rem;margin-bottom:.5rem;}
.pay-row select{flex:1;font-size:.8rem;padding:.4rem .5rem;border-radius:8px;border:1px solid #e2e8f0;outline:none;background:#fff;font-family:inherit;color:#0f172a;appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%2394a3b8' stroke-width='2' viewBox='0 0 24 24'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M6 9l6 6 6-6'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right .5rem center;}
.pay-row input{flex:1;font-size:.8rem;padding:.4rem .5rem;border-radius:8px;border:1px solid #e2e8f0;outline:none;font-family:inherit;color:#0f172a;}
.pay-row input:focus{border-color:var(--pos-primary);}

.change-row{display:flex;justify-content:space-between;align-items:center;font-size:.85rem;margin-bottom:.6rem;padding:.35rem .5rem;border-radius:8px;background:#f0fdf4;}
.change-row .lbl{color:#065f46;font-weight:600;}
.change-row .val{font-size:1.05rem;font-weight:800;color:#065f46;}

.action-row{display:flex;gap:.45rem;}
.action-row .btn{flex:1;padding:.6rem;border-radius:10px;font-size:.85rem;font-weight:700;border:none;cursor:pointer;font-family:inherit;transition:all .2s;display:flex;align-items:center;justify-content:center;gap:.35rem;}
.action-row .btn:active{transform:scale(.97);}
.btn-clear{background:#f1f5f9;color:#64748b;flex:0 0 auto;padding:.6rem .85rem;}
.btn-clear:hover{background:#e2e8f0;}
.btn-pay{background:linear-gradient(135deg,var(--pos-success),#059669);color:#fff;box-shadow:0 4px 14px rgba(16,185,129,.3);}
.btn-pay:hover{box-shadow:0 6px 20px rgba(16,185,129,.4);transform:translateY(-1px);}
.btn-pay:disabled{opacity:.5;cursor:not-allowed;transform:none;}

/* ── Mobile Money Modal ────────────────── */
.momo-overlay{position:fixed;inset:0;background:rgba(15,23,42,.55);backdrop-filter:blur(6px);z-index:200;display:none;align-items:center;justify-content:center;padding:1rem;}
.momo-overlay.open{display:flex;}
.momo-modal{background:#fff;border-radius:24px;width:100%;max-width:420px;overflow:hidden;box-shadow:0 30px 60px rgba(0,0,0,.25);animation:modalPop .3s ease;}
@keyframes modalPop{from{opacity:0;transform:scale(.9) translateY(20px);}to{opacity:1;transform:scale(1) translateY(0);}}
.momo-head{background:linear-gradient(135deg,#7c3aed,#6d28d9);color:#fff;padding:1.25rem 1.5rem;text-align:center;}
.momo-head .ico{width:48px;height:48px;border-radius:50%;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;margin:0 auto .5rem;}
.momo-head h3{font-size:1.05rem;font-weight:800;}
.momo-head p{font-size:.78rem;opacity:.85;margin-top:.2rem;}
.momo-body{padding:1.5rem;}
.momo-field{margin-bottom:1rem;}
.momo-field label{display:block;font-size:.72rem;font-weight:700;color:#475569;margin-bottom:.3rem;}
.momo-field .inp{width:100%;padding:.6rem .75rem;border-radius:10px;border:1.5px solid #e2e8f0;font-size:.88rem;font-family:inherit;outline:none;transition:border-color .2s;background:#fff;}
.momo-field .inp:focus{border-color:#7c3aed;box-shadow:0 0 0 3px rgba(124,58,237,.1);}
.momo-field .inp::placeholder{color:#cbd5e1;}
.momo-network{display:flex;gap:.5rem;margin-bottom:1rem;}
.momo-network button{flex:1;padding:.5rem;border-radius:10px;border:2px solid #e2e8f0;background:#fff;cursor:pointer;text-align:center;transition:all .15s;font-family:inherit;}
.momo-network button:hover{border-color:#cbd5e1;}
.momo-network button.active{border-color:#7c3aed;background:#faf5ff;}
.momo-network button .net-name{font-size:.72rem;font-weight:700;color:#0f172a;}
.momo-network button .net-desc{font-size:.6rem;color:#94a3b8;}
.momo-amount{text-align:center;padding:.75rem;background:#f8fafc;border-radius:12px;margin-bottom:1rem;}
.momo-amount .lbl{font-size:.72rem;color:#94a3b8;}
.momo-amount .val{font-size:1.6rem;font-weight:900;color:#0f172a;letter-spacing:-.03em;}
.momo-footer{display:flex;gap:.5rem;padding:0 1.5rem 1.5rem;}
.momo-footer button{flex:1;padding:.65rem;border-radius:12px;font-size:.85rem;font-weight:700;cursor:pointer;border:none;font-family:inherit;transition:all .2s;}
.momo-footer .btn-cancel{background:#f1f5f9;color:#64748b;}
.momo-footer .btn-cancel:hover{background:#e2e8f0;}
.momo-footer .btn-pay-now{background:linear-gradient(135deg,#7c3aed,#6d28d9);color:#fff;box-shadow:0 4px 14px rgba(124,58,237,.3);}
.momo-footer .btn-pay-now:hover{box-shadow:0 6px 20px rgba(124,58,237,.4);transform:translateY(-1px);}
.momo-footer .btn-pay-now:disabled{opacity:.5;cursor:not-allowed;transform:none;}

.momo-status{text-align:center;padding:2rem 1.5rem;}
.momo-status .spinner{width:48px;height:48px;border:4px solid #e2e8f0;border-top-color:#7c3aed;border-radius:50%;animation:spin .8s linear infinite;margin:0 auto 1rem;}
@keyframes spin{to{transform:rotate(360deg);}}
.momo-status .msg{font-size:.9rem;font-weight:600;color:#0f172a;}
.momo-status .sub{font-size:.78rem;color:#64748b;margin-top:.25rem;}
.momo-status .check{width:56px;height:56px;border-radius:50%;background:#dcfce7;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;}

/* ── Receipt Modal ─────────────────────── */
.receipt-overlay{position:fixed;inset:0;background:rgba(15,23,42,.5);backdrop-filter:blur(4px);z-index:200;display:none;align-items:center;justify-content:center;padding:1rem;}
.receipt-overlay.open{display:flex;}
.receipt-modal{background:#fff;border-radius:20px;width:100%;max-width:380px;max-height:90vh;overflow-y:auto;box-shadow:0 25px 50px rgba(0,0,0,.2);animation:modalPop .3s ease;padding:1.5rem;}
.receipt-header{text-align:center;border-bottom:2px dashed #e2e8f0;padding-bottom:1rem;margin-bottom:1rem;}
.receipt-header .r-brand{font-size:1.1rem;font-weight:800;color:#0f172a;}
.receipt-header .r-info{font-size:.72rem;color:#94a3b8;margin-top:.15rem;}
.receipt-items{margin-bottom:1rem;}
.r-item{display:flex;justify-content:space-between;font-size:.78rem;padding:.2rem 0;}
.r-item .r-name{color:#475569;}
.r-item .r-qty{color:#94a3b8;}
.r-item .r-total{font-weight:600;color:#0f172a;}
.receipt-totals{border-top:2px dashed #e2e8f0;padding-top:.6rem;}
.rt-row{display:flex;justify-content:space-between;font-size:.78rem;margin-bottom:.15rem;}
.rt-row.total{font-size:1rem;font-weight:800;color:#0f172a;border-top:1px solid #e2e8f0;padding-top:.4rem;margin-top:.3rem;}
.receipt-footer{text-align:center;margin-top:1rem;padding-top:1rem;border-top:2px dashed #e2e8f0;}
.receipt-footer p{font-size:.7rem;color:#94a3b8;}
.receipt-actions{display:flex;gap:.5rem;margin-top:1rem;flex-wrap:wrap;}
.receipt-actions button,.receipt-actions a{flex:1;min-width:90px;padding:.5rem;border-radius:8px;font-size:.78rem;font-weight:700;cursor:pointer;border:none;font-family:inherit;transition:all .15s;text-align:center;display:inline-flex;align-items:center;justify-content:center;}
.receipt-actions .btn-print{background:var(--pos-primary);color:#fff;}
.receipt-actions .btn-print:hover{background:#1d4ed8;}
.receipt-actions .btn-invoice{background:#7c3aed;color:#fff;}
.receipt-actions .btn-invoice:hover{background:#6d28d9;}
.receipt-actions .btn-whatsapp{background:#22c55e;color:#fff;}
.receipt-actions .btn-whatsapp:hover{background:#16a34a;}
.receipt-actions .btn-email{background:#2563eb;color:#fff;}
.receipt-actions .btn-email:hover{background:#1d4ed8;}
.receipt-actions .btn-new{background:#f1f5f9;color:#475569;}
.receipt-actions .btn-new:hover{background:#e2e8f0;}

/* ── Quick Customer Modal ──────────────── */
.qcust-overlay{position:fixed;inset:0;background:rgba(15,23,42,.5);backdrop-filter:blur(4px);z-index:200;display:none;align-items:center;justify-content:center;padding:1rem;}
.qcust-overlay.open{display:flex;}
.qcust-modal{background:#fff;border-radius:20px;width:100%;max-width:400px;box-shadow:0 25px 50px rgba(0,0,0,.2);animation:modalPop .3s ease;overflow:hidden;}
.qcust-head{display:flex;align-items:center;justify-content:space-between;padding:1.25rem 1.5rem;border-bottom:1px solid #f1f5f9;}
.qcust-head h3{font-size:.95rem;font-weight:800;color:#0f172a;}
.qcust-body{padding:1.25rem 1.5rem;}
.qcust-body .fg{margin-bottom:.85rem;}
.qcust-body .fg label{display:block;font-size:.7rem;font-weight:700;color:#475569;margin-bottom:.25rem;}
.qcust-body .fg input{width:100%;padding:.5rem .65rem;border-radius:8px;border:1px solid #e2e8f0;font-size:.82rem;outline:none;font-family:inherit;}
.qcust-body .fg input:focus{border-color:var(--pos-primary);box-shadow:0 0 0 3px rgba(37,99,235,.08);}
.qcust-foot{display:flex;gap:.5rem;padding:1rem 1.5rem;border-top:1px solid #f1f5f9;}
</style>
@endsection

@section('content')
@php
$user = auth()->user();
$currency = $user->currency ?? 'TZS';
@endphp

<div class="pos-layout">

  {{-- ═══ LEFT: Products Panel ═══════════════════ --}}
  <div class="prod-panel">
    <div class="prod-search">
      <svg width="16" height="16" fill="none" stroke="#94a3b8" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
      <input type="text" id="posSearch" placeholder="Search product name or scan barcode..." oninput="searchProducts()">
      <a href="{{ route('dashboard.inventory.add-product') }}" style="font-size:.68rem;font-weight:600;color:var(--pos-primary);text-decoration:none;white-space:nowrap;">+ Add Product</a>
    </div>
    <div class="prod-grid" id="productsGrid">
      <div style="color:#94a3b8;text-align:center;grid-column:1/-1;padding:3rem;">Loading products...</div>
    </div>
  </div>

  {{-- ═══ RIGHT: Cart Panel ═══════════════════════ --}}
  <div class="cart-panel">

    {{-- Header --}}
    <div class="cart-head">
      <div class="title">
        <span>Current Order</span>
        <span id="cartCount">0 items</span>
      </div>
      <div class="cust-row">
        <select id="posCustomer">
          <option value="">Walk-in Customer</option>
        </select>
        <button class="add-cust-btn" onclick="openQuickCustomer()" title="Add new customer">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
        </button>
      </div>
    </div>

    {{-- Cart Items --}}
    <div class="cart-items" id="cartItems">
      <div class="cart-empty">
        <svg width="36" height="36" fill="none" stroke="#cbd5e1" stroke-width="1.2" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/></svg>
        <p>Cart is empty</p>
        <p style="font-size:.72rem;margin-top:.15rem;">Click products to add them</p>
      </div>
    </div>

    {{-- Summary --}}
    <div class="cart-summary" id="cartSummary" style="display:none;">
      <div class="sum-row">
        <span class="lbl">Subtotal</span>
        <span class="val" id="posSubtotal">0.00</span>
      </div>
      <div class="sum-row disc-row">
        <span class="lbl">Discount</span>
        <input type="number" id="posDiscount" min="0" step="0.01" value="0" oninput="updateCart()">
      </div>
      <div class="sum-row total">
        <span>Total</span>
        <span class="val" id="posTotal">0.00</span>
      </div>

      {{-- Payment --}}
      <div class="pay-row">
        <select id="posPayMethod" onchange="onPayMethodChange()">
          <option value="cash">💵 Cash</option>
          <option value="card">💳 Card</option>
          <option value="mobile_money">📱 Mobile Money</option>
          <option value="credit">📋 Credit</option>
        </select>
        <input type="number" id="posPaid" min="0" step="0.01" placeholder="Amount paid" oninput="updateChange()">
      </div>
      <div class="change-row" id="changeRow">
        <span class="lbl">Change Due</span>
        <span class="val" id="posChange">0.00</span>
      </div>

      {{-- Actions --}}
      <div class="action-row">
        <button class="btn btn-clear" onclick="clearCart()">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
          Clear
        </button>
        <button class="btn btn-pay" id="btnPay" onclick="onPay()">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
          Complete Sale
        </button>
      </div>
    </div>
  </div>
</div>

{{-- ═══ MOBILE MONEY MODAL ═══════════════════════ --}}
<div class="momo-overlay" id="momoModal">
  <div class="momo-modal">
    <div class="momo-head">
      <div class="ico">
        <svg width="24" height="24" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
      </div>
      <h3>Mobile Money Payment</h3>
      <p>Enter customer details to process payment</p>
    </div>
    <div id="momoBody">
      <div class="momo-body">
        <div class="momo-amount">
          <div class="lbl">Amount to Pay</div>
          <div class="val" id="momoAmount">0.00</div>
        </div>
        <div class="momo-network">
          <button class="active" data-net="mpesa" onclick="selectNetwork('mpesa',this)">
            <div class="net-name">M-Pesa</div>
            <div class="net-desc">Vodacom</div>
          </button>
          <button data-net="tigo" onclick="selectNetwork('tigo',this)">
            <div class="net-name">Tigo Pesa</div>
            <div class="net-desc">Tigo</div>
          </button>
          <button data-net="airtel" onclick="selectNetwork('airtel',this)">
            <div class="net-name">Airtel Money</div>
            <div class="net-desc">Airtel</div>
          </button>
          <button data-net="halopesa" onclick="selectNetwork('halopesa',this)">
            <div class="net-name">HaloPesa</div>
            <div class="net-desc">TTCL</div>
          </button>
        </div>
        <div class="momo-field">
          <label>Phone Number</label>
          <input class="inp" id="momoPhone" type="tel" placeholder="e.g. 0712345678" maxlength="13" oninput="formatPhone(this)">
        </div>
        <div class="momo-field">
          <label>Transaction Reference (optional)</label>
          <input class="inp" id="momoRef" type="text" placeholder="Reference or name">
        </div>
      </div>
      <div class="momo-footer">
        <button class="btn-cancel" onclick="closeMomo()">Cancel</button>
        <button class="btn-pay-now" id="btnMomoPay" onclick="processMomo()">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="display:inline;vertical-align:middle;margin-right:.25rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
          Pay {{ $currency }} <span id="momoBtnAmount">0</span>
        </button>
      </div>
    </div>
    {{-- Processing / Success view --}}
    <div id="momoStatus" style="display:none;"></div>
  </div>
</div>

{{-- ═══ RECEIPT MODAL ════════════════════════════ --}}
<div class="receipt-overlay" id="receiptModal">
  <div class="receipt-modal" id="receiptContent">
    <div style="text-align:center;padding:2rem;color:#94a3b8;">Loading receipt...</div>
  </div>
</div>

{{-- ═══ QUICK CUSTOMER MODAL ═════════════════════ --}}
<div class="qcust-overlay" id="qcustModal">
  <div class="qcust-modal">
    <div class="qcust-head">
      <h3>Add New Customer</h3>
      <button onclick="closeQuickCustomer()" style="background:none;border:none;cursor:pointer;color:#94a3b8;padding:4px;">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <div class="qcust-body">
      <div class="fg">
        <label>Customer Name *</label>
        <input type="text" id="qcName" placeholder="Enter customer name">
      </div>
      <div class="fg">
        <label>Phone Number</label>
        <input type="tel" id="qcPhone" placeholder="e.g. 0712345678">
      </div>
      <div class="fg">
        <label>Email</label>
        <input type="email" id="qcEmail" placeholder="customer@example.com">
      </div>
    </div>
    <div class="qcust-foot">
      <button class="btn-todo-secondary" onclick="closeQuickCustomer()" style="flex:1;">Cancel</button>
      <button class="btn-todo-primary" onclick="saveQuickCustomer()" style="flex:1;">Add Customer</button>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
let products=[], cart={}, customers=[], saleNetwork='mpesa';
const CURRENCY = '{{ $currency }}';

// ── Init ────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', init);

async function init(){
  try{
    [products, customers] = await Promise.all([
      apiFetch('/api/dashboard/products'),
      apiFetch('/api/dashboard/customers')
    ]);
    populateCustomers();
    renderProducts(products);
    updateCart();
    document.getElementById('posSearch').focus();
  }catch(e){
    document.getElementById('productsGrid').innerHTML = `
      <div class="no-prods">
        <svg width="56" height="56" fill="none" stroke="#e03057" stroke-width="1.2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <p>Failed to load data. Please refresh.</p>
      </div>`;
  }
}

// ── Customers ───────────────────────────────────────
function populateCustomers(){
  const sel = document.getElementById('posCustomer');
  sel.innerHTML = '<option value="">Walk-in Customer</option>' +
    customers.map(c => `<option value="${c.id}">${c.name}${c.phone ? ' — '+c.phone : ''}</option>`).join('');
}

async function saveQuickCustomer(){
  const name = document.getElementById('qcName').value.trim();
  if(!name){ showToast('Customer name is required','error'); return; }

  try {
    const res = await apiFetch('/api/dashboard/customers', {
      method: 'POST',
      body: JSON.stringify({
        name: name,
        phone: document.getElementById('qcPhone').value.trim(),
        email: document.getElementById('qcEmail').value.trim(),
        status: 'active'
      })
    });
    customers.push(res.customer || res);
    populateCustomers();
    document.getElementById('posCustomer').value = res.customer ? res.customer.id : (res.id || '');
    closeQuickCustomer();
    showToast('Customer added: ' + name);
  } catch(e) {
    showToast(e.message || 'Failed to add customer','error');
  }
}

function openQuickCustomer(){
  document.getElementById('qcName').value = '';
  document.getElementById('qcPhone').value = '';
  document.getElementById('qcEmail').value = '';
  document.getElementById('qcustModal').classList.add('open');
  setTimeout(() => document.getElementById('qcName').focus(), 200);
}

function closeQuickCustomer(){
  document.getElementById('qcustModal').classList.remove('open');
}

// ── Products ────────────────────────────────────────
function renderProducts(list){
  const grid = document.getElementById('productsGrid');
  if(!list.length){
    grid.innerHTML = `
      <div class="no-prods">
        <svg width="64" height="64" fill="none" stroke="#cbd5e1" stroke-width="1" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
          <path stroke-linecap="round" stroke-linejoin="round" d="M16 2l-4 2-4-2" opacity=".4"/>
        </svg>
        <p style="font-weight:600;color:#64748b;font-size:.95rem;">No products yet</p>
        <p style="font-size:.8rem;color:#94a3b8;">Add your first product to start selling</p>
        <a href="{{ route('dashboard.inventory.add-product') }}" style="display:inline-flex;align-items:center;gap:.35rem;margin-top:.75rem;padding:.5rem 1.25rem;border-radius:10px;background:linear-gradient(135deg,var(--pos-primary),#1d4ed8);color:#fff;font-size:.82rem;font-weight:700;text-decoration:none;transition:all .2s;box-shadow:0 4px 12px rgba(37,99,235,.3);"
           onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform=''">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
          Add Product
        </a>
      </div>`;
    return;
  }
  grid.innerHTML = list.map(p => {
    const inCart = cart[p.id];
    const stock = parseInt(p.stock_quantity || 0);
    const isOut = stock <= 0;
    const isLow = stock <= 5 && stock > 0;
    return `<div class="prod-card ${inCart ? 'in-cart' : ''} ${isOut ? 'out-of-stock' : ''}" onclick="${isOut ? '' : 'addToCart('+p.id+')'}" style="${isOut ? 'opacity:.55;cursor:not-allowed;' : ''}">
      <div class="name">${p.name}</div>
      ${p.sku ? `<div class="sku">SKU: ${p.sku}</div>` : ''}
      <div class="price">${CURRENCY} ${parseFloat(p.selling_price||0).toLocaleString('en')}</div>
      <div class="stock ${isOut ? 'out' : (isLow ? 'low' : 'ok')}">${isOut ? 'Out of Stock' : (stock + ' in stock')}</div>
      ${inCart ? `<div class="cart-badge">${inCart.qty}</div>` : ''}
      ${isOut ? '<div class="sold-out-overlay">Sold Out</div>' : ''}
    </div>`;
  }).join('');
}

function searchProducts(){
  const s = document.getElementById('posSearch').value.toLowerCase();
  renderProducts(s ? products.filter(p =>
    p.name.toLowerCase().includes(s) || (p.sku && p.sku.toLowerCase().includes(s))
  ) : products);
}

// ── Cart ────────────────────────────────────────────
function addToCart(id){
  const p = products.find(x => x.id == id);
  if(!p || parseInt(p.stock_quantity||0) <= 0) return;
  if(!cart[id]) cart[id] = {product: p, qty: 1, price: parseFloat(p.selling_price||0)};
  else if(cart[id].qty < parseInt(p.stock_quantity||0)) cart[id].qty++;
  else { showToast('Not enough stock','error'); return; }
  updateCart();
  searchProducts();
}

function removeFromCart(id){ delete cart[id]; updateCart(); searchProducts(); }

function changeQty(id, val){
  if(!cart[id]) return;
  const p = products.find(x => x.id == id);
  const maxQty = parseInt(p?.stock_quantity || 999);
  cart[id].qty = Math.min(maxQty, Math.max(1, parseInt(val) || 1));
  updateCart();
}

function updateCart(){
  const keys = Object.keys(cart);
  const cartEl = document.getElementById('cartItems');
  const summaryEl = document.getElementById('cartSummary');
  document.getElementById('cartCount').textContent = keys.length + ' item' + (keys.length !== 1 ? 's' : '');

  if(!keys.length){
    cartEl.innerHTML = `<div class="cart-empty">
      <svg width="36" height="36" fill="none" stroke="#cbd5e1" stroke-width="1.2" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/></svg>
      <p>Cart is empty</p>
      <p style="font-size:.72rem;margin-top:.15rem;">Click products to add them</p>
    </div>`;
    summaryEl.style.display = 'none';
    return;
  }

  summaryEl.style.display = 'block';
  const sub = keys.reduce((a,id) => a + (cart[id].qty * cart[id].price), 0);
  const disc = parseFloat(document.getElementById('posDiscount').value || 0);
  const total = Math.max(0, sub - disc);

  document.getElementById('posSubtotal').textContent = CURRENCY + ' ' + sub.toLocaleString('en',{minimumFractionDigits:2});
  document.getElementById('posTotal').textContent = CURRENCY + ' ' + total.toLocaleString('en',{minimumFractionDigits:2});

  cartEl.innerHTML = keys.map(id => {
    const item = cart[id];
    return `<div class="cart-item">
      <div class="ci-name">
        <div class="ci-title">${item.product.name}</div>
        <div class="ci-price">${CURRENCY} ${item.price.toLocaleString('en',{minimumFractionDigits:2})} each</div>
      </div>
      <div class="ci-qty">
        <button onclick="changeQty(${id}, ${item.qty - 1})">−</button>
        <input type="number" value="${item.qty}" min="1" max="${item.product.stock_quantity||999}" onchange="changeQty(${id}, this.value)">
        <button onclick="changeQty(${id}, ${item.qty + 1})">+</button>
      </div>
      <div class="ci-total">${CURRENCY} ${(item.qty * item.price).toLocaleString('en',{minimumFractionDigits:2})}</div>
      <button class="ci-remove" onclick="removeFromCart(${id})">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>`;
  }).join('');
  updateChange();
}

function updateChange(){
  const total = parseFloat(document.getElementById('posTotal').textContent.replace(/[^0-9.-]/g,'') || 0);
  const paid = parseFloat(document.getElementById('posPaid').value || 0);
  const change = Math.max(0, paid - total);
  document.getElementById('posChange').textContent = CURRENCY + ' ' + change.toLocaleString('en',{minimumFractionDigits:2});
  document.getElementById('changeRow').style.display = paid > 0 ? '' : 'none';
}

function onPayMethodChange(){
  const method = document.getElementById('posPayMethod').value;
  const paidInput = document.getElementById('posPaid');
  if(method === 'cash'){
    paidInput.placeholder = 'Amount paid';
    paidInput.disabled = false;
  } else if(method === 'mobile_money'){
    paidInput.placeholder = 'Amount';
    paidInput.disabled = false;
  } else {
    paidInput.placeholder = method === 'credit' ? 'Amount (optional)' : 'Amount';
    paidInput.disabled = false;
  }
}

// ── Payment Flow ─────────────────────────────────────
function onPay(){
  const keys = Object.keys(cart);
  if(!keys.length){ showToast('Cart is empty','error'); return; }
  const total = parseFloat(document.getElementById('posTotal').textContent.replace(/[^0-9.-]/g,'') || 0);
  if(total <= 0){ showToast('Total must be greater than 0','error'); return; }

  const method = document.getElementById('posPayMethod').value;

  if(method === 'mobile_money'){
    openMomo(total);
  } else if(method === 'cash'){
    const paid = parseFloat(document.getElementById('posPaid').value || 0);
    if(paid <= 0){ showToast('Enter amount paid','error'); return; }
    if(paid < total){ showToast('Amount paid is less than total','error'); return; }
    doCompleteSale();
  } else {
    doCompleteSale();
  }
}

// ── Mobile Money Modal ───────────────────────────────
function openMomo(amount){
  document.getElementById('momoAmount').textContent = CURRENCY + ' ' + amount.toLocaleString('en',{minimumFractionDigits:2});
  document.getElementById('momoBtnAmount').textContent = amount.toLocaleString('en',{minimumFractionDigits:2});
  document.getElementById('momoPhone').value = '';
  document.getElementById('momoRef').value = '';
  document.getElementById('momoBody').style.display = '';
  document.getElementById('momoStatus').style.display = 'none';
  document.getElementById('btnMomoPay').disabled = false;
  document.getElementById('momoModal').classList.add('open');
  setTimeout(() => document.getElementById('momoPhone').focus(), 300);
}

function closeMomo(){
  document.getElementById('momoModal').classList.remove('open');
}

function selectNetwork(net, btn){
  saleNetwork = net;
  document.querySelectorAll('.momo-network button').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
}

function formatPhone(inp){
  inp.value = inp.value.replace(/[^0-9+]/g,'');
}

async function processMomo(){
  const phone = document.getElementById('momoPhone').value.trim();
  if(!phone || phone.length < 8){ showToast('Enter a valid phone number','error'); return; }

  document.getElementById('btnMomoPay').disabled = true;

  // Show processing
  document.getElementById('momoBody').style.display = 'none';
  document.getElementById('momoStatus').style.display = '';
  document.getElementById('momoStatus').innerHTML = `
    <div class="momo-status">
      <div class="spinner"></div>
      <div class="msg">Processing Payment...</div>
      <div class="sub">Please wait while we process the payment</div>
    </div>`;

  // Simulate payment processing
  await new Promise(r => setTimeout(r, 2000));

  // Show success
  const ref = 'TXN' + Date.now().toString().slice(-8);
  document.getElementById('momoStatus').innerHTML = `
    <div class="momo-status">
      <div class="check">
        <svg width="28" height="28" fill="none" stroke="#16a34a" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
      </div>
      <div class="msg">Payment Successful!</div>
      <div class="sub">Transaction: ${ref}<br>Amount: ${document.getElementById('momoAmount').textContent}</div>
    </div>`;

  await new Promise(r => setTimeout(r, 800));

  // Save sale with mobile money details
  await doCompleteSale(ref);

  closeMomo();
}

// ── Complete Sale ────────────────────────────────────
async function doCompleteSale(txnRef = null){
  const keys = Object.keys(cart);
  const sub = keys.reduce((a,id) => a + (cart[id].qty * cart[id].price), 0);
  const disc = parseFloat(document.getElementById('posDiscount').value || 0);
  const total = Math.max(0, sub - disc);
  const paid = parseFloat(document.getElementById('posPaid').value || 0) || total;
  const method = document.getElementById('posPayMethod').value;

  const data = {
    customer_id: document.getElementById('posCustomer').value || null,
    sale_date: new Date().toISOString().split('T')[0],
    status: 'completed',
    payment_method: method,
    discount: disc,
    paid: paid,
    subtotal: sub,
    tax: 0,
    total: total,
    notes: txnRef ? 'Mobile Money Ref: ' + txnRef : '',
    items: keys.map(id => ({
      product_id: id,
      product_name: cart[id].product?.name || 'Unknown',
      unit_price: cart[id].price,
      quantity: cart[id].qty,
      discount: 0,
      total: (cart[id].qty * cart[id].price)
    }))
  };

  const btn = document.getElementById('btnPay');
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner" style="display:inline-block;width:16px;height:16px;border:2px solid rgba(255,255,255,.3);border-top-color:#fff;border-radius:50%;animation:spin .6s linear infinite;margin-right:.3rem;vertical-align:middle;"></span> Processing...';

  try {
    const res = await apiFetch('/api/dashboard/sales', {
      method: 'POST',
      body: JSON.stringify(data)
    });
    showToast('✅ Sale completed successfully!');

    // Show receipt
    showReceipt(data, res, txnRef);

    // Reset cart
    cart = {};
    document.getElementById('posDiscount').value = 0;
    document.getElementById('posPaid').value = '';
    document.getElementById('posPayMethod').value = 'cash';
    updateCart();
    searchProducts();
  } catch(e) {
    showToast(e.message || 'Sale failed','error');
  } finally {
    btn.disabled = false;
    btn.innerHTML = '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Complete Sale';
  }
}

function clearCart(){
  if(Object.keys(cart).length === 0){
    showToast('Cart is already empty','info');
    return;
  }
  cart = {};
  document.getElementById('posDiscount').value = 0;
  document.getElementById('posPaid').value = '';
  document.getElementById('posCustomer').value = '';
  document.getElementById('changeRow').style.display = 'none';
  updateCart();
  searchProducts();
  showToast('Cart cleared','success');
}

// ── Receipt ──────────────────────────────────────────
function showReceipt(data, res, txnRef){
  const customerName = document.getElementById('posCustomer').options[document.getElementById('posCustomer').selectedIndex]?.text || 'Walk-in Customer';
  const itemsHtml = data.items.map((item, i) => `
    <div class="r-item">
      <span class="r-name">${item.product_name}</span>
      <span class="r-qty">×${item.quantity}</span>
      <span class="r-total">${CURRENCY} ${(item.unit_price * item.quantity).toLocaleString('en',{minimumFractionDigits:2})}</span>
    </div>`).join('');

  const now = new Date();
  const invoiceNo = res?.reference || 'INV-' + now.getTime().toString().slice(-6);

  document.getElementById('receiptContent').innerHTML = `
    <div class="receipt-header">
      <div class="r-brand">{{ config('app.name','MannaPOS') }}</div>
      <div class="r-info">${now.toLocaleDateString('en-GB',{day:'2-digit',month:'short',year:'numeric'})} &middot; ${now.toLocaleTimeString('en-US',{hour:'2-digit',minute:'2-digit'})}</div>
      <div class="r-info" style="font-size:.68rem;">Invoice: ${invoiceNo}</div>
    </div>
    <div style="margin-bottom:.75rem;font-size:.75rem;color:#64748b;">
      <div>Customer: <strong>${customerName}</strong></div>
      ${txnRef ? `<div>Payment Ref: ${txnRef}</div>` : ''}
    </div>
    <div class="receipt-items">${itemsHtml}</div>
    <div class="receipt-totals">
      <div class="rt-row"><span>Subtotal</span><span>${CURRENCY} ${data.subtotal.toLocaleString('en',{minimumFractionDigits:2})}</span></div>
      ${data.discount > 0 ? `<div class="rt-row"><span>Discount</span><span>-${CURRENCY} ${data.discount.toLocaleString('en',{minimumFractionDigits:2})}</span></div>` : ''}
      <div class="rt-row total"><span>Total</span><span>${CURRENCY} ${data.total.toLocaleString('en',{minimumFractionDigits:2})}</span></div>
      <div class="rt-row"><span>Paid</span><span>${CURRENCY} ${data.paid.toLocaleString('en',{minimumFractionDigits:2})}</span></div>
      <div class="rt-row"><span>Change</span><span>${CURRENCY} ${Math.max(0, data.paid - data.total).toLocaleString('en',{minimumFractionDigits:2})}</span></div>
      <div class="rt-row" style="font-size:.7rem;color:#94a3b8;"><span>Payment</span><span style="text-transform:capitalize;">${data.payment_method.replace(/_/g,' ')}</span></div>
    </div>
    <div class="receipt-footer">
      <p>Thank you for your business!</p>
    </div>
    <div class="receipt-actions">
      <button class="btn-print" onclick="printReceipt()">🖨 Print</button>
      <a class="btn-invoice" href="/invoice/${invoiceNo}" target="_blank" style="text-decoration:none;">📄 Full Invoice</a>
      <a class="btn-whatsapp" href="https://wa.me/?text=${encodeURIComponent('Invoice: ' + invoiceNo + ' - View: ' + window.location.origin + '/invoice/' + invoiceNo)}" target="_blank" style="text-decoration:none;">💬 WhatsApp</a>
      <a class="btn-email" href="mailto:?subject=Invoice ${invoiceNo}&body=Please find your invoice here: ${encodeURIComponent(window.location.origin + '/invoice/' + invoiceNo)}" target="_blank" style="text-decoration:none;">✉ Email</a>
      <button class="btn-new" onclick="closeReceipt()">New Sale</button>
    </div>`;

  document.getElementById('receiptModal').classList.add('open');
}

function closeReceipt(){
  document.getElementById('receiptModal').classList.remove('open');
}

function printReceipt(){
  const content = document.getElementById('receiptContent').innerHTML;
  const w = window.open('','_blank','width=400,height=600');
  w.document.write(`
    <html><head><title>Receipt</title>
    <style>body{font-family:monospace;font-size:13px;padding:20px;max-width:350px;margin:0 auto;}
    .receipt-header{text-align:center;border-bottom:2px dashed #ccc;padding-bottom:10px;margin-bottom:10px;}
    .r-brand{font-size:18px;font-weight:bold;}
    .r-info{font-size:11px;color:#666;}
    .receipt-items{margin-bottom:10px;}
    .r-item{display:flex;justify-content:space-between;padding:2px 0;font-size:12px;}
    .r-name{flex:1;}.r-qty{width:40px;text-align:center;color:#666;}.r-total{width:70px;text-align:right;font-weight:bold;}
    .receipt-totals{border-top:2px dashed #ccc;padding-top:5px;}
    .rt-row{display:flex;justify-content:space-between;font-size:12px;padding:1px 0;}
    .rt-row.total{font-size:15px;font-weight:bold;border-top:1px solid #ccc;padding-top:5px;margin-top:3px;}
    .receipt-footer{text-align:center;margin-top:10px;padding-top:10px;border-top:2px dashed #ccc;}
    .receipt-footer p{font-size:11px;color:#666;}
  </style></head><body>${content.replace('receipt-actions','receipt-actions" style="display:none')}</body></html>`);
  w.document.close();
  setTimeout(() => { w.print(); w.close(); }, 300);
}
</script>
<style>
@keyframes spin{to{transform:rotate(360deg);}}
</style>
@endsection
