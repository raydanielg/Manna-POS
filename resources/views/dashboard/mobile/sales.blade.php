@extends('layouts.dashboard')
@section('page_title', 'Sales')
@section('page_styles')
<style>
.mob-hdr{display:flex;align-items:center;gap:10px;margin-bottom:14px;}
.mob-hdr h1{font-size:1.2rem;font-weight:800;color:#0f172a;flex:1;}
.mob-hdr .mob-hdr-actions{display:flex;gap:8px;}
.mob-hdr-btn{width:42px;height:42px;border-radius:12px;border:1.5px solid #e9edf5;background:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;color:#64748b;}
.mob-hdr-btn:active{transform:scale(.94);}

.mob-stats-row{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:14px;}
.mob-stat{background:#fff;border-radius:14px;border:1.5px solid #eef2f6;padding:12px 14px;}
.mob-stat .val{font-size:1.1rem;font-weight:800;color:#0f172a;}
.mob-stat .lbl{font-size:.65rem;color:#94a3b8;font-weight:600;text-transform:uppercase;margin-top:2px;}
.mob-stat .trend{font-size:.6rem;font-weight:700;margin-top:4px;}

.mob-tabs{display:flex;gap:4px;margin-bottom:12px;background:#f1f5f9;border-radius:12px;padding:3px;}
.mob-tab{flex:1;text-align:center;padding:8px;border-radius:10px;font-size:.78rem;font-weight:600;color:#64748b;cursor:pointer;transition:all .2s;-webkit-tap-highlight-color:transparent;}
.mob-tab.active{background:#fff;color:#0f172a;box-shadow:0 2px 8px rgba(0,0,0,.06);}

.mob-sale-list{display:flex;flex-direction:column;gap:8px;}
.mob-sale{background:#fff;border-radius:14px;border:1.5px solid #eef2f6;padding:12px 14px;cursor:pointer;-webkit-tap-highlight-color:transparent;transition:all .2s;}
.mob-sale:active{transform:scale(.98);}
.mob-sale .s-top{display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;}
.mob-sale .s-ref{font-weight:700;font-size:.82rem;color:#0f172a;}
.mob-sale .s-date{font-size:.68rem;color:#94a3b8;}
.mob-sale .s-bot{display:flex;justify-content:space-between;align-items:center;}
.mob-sale .s-amt{font-weight:800;font-size:1rem;color:#059669;}
.mob-sale .s-cust{font-size:.72rem;color:#64748b;}
.mob-sale .s-badge{font-size:.6rem;font-weight:700;padding:3px 10px;border-radius:50px;text-transform:uppercase;}

.mob-empty{text-align:center;padding:2.5rem 1rem;color:#94a3b8;}
.mob-empty svg{width:48px;height:48px;margin:0 auto 10px;display:block;color:#cbd5e1;}
</style>
@endsection
@section('content')
<div class="dash-content" id="mobSalesApp">
  <div class="mob-hdr">
    <h1>Sales</h1>
    <div class="mob-hdr-actions">
      <button class="mob-hdr-btn" onclick="window.location.href='{{ route('dashboard.sell.pos') }}'" title="New Sale">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:20px;height:20px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
      </button>
      <button class="mob-hdr-btn" onclick="location.reload()" title="Refresh">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:20px;height:20px;"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
      </button>
    </div>
  </div>

  <div class="mob-stats-row">
    <div class="mob-stat">
      <div class="val" id="todaySales">TZS 0</div>
      <div class="lbl">Today</div>
    </div>
    <div class="mob-stat">
      <div class="val" id="weekSales">TZS 0</div>
      <div class="lbl">This Week</div>
    </div>
    <div class="mob-stat">
      <div class="val" id="monthSales">TZS 0</div>
      <div class="lbl">This Month</div>
    </div>
    <div class="mob-stat">
      <div class="val" id="totalOrders">0</div>
      <div class="lbl">Orders</div>
    </div>
  </div>

  <div class="mob-tabs">
    <span class="mob-tab active" onclick="switchTab(this,'today')">Today</span>
    <span class="mob-tab" onclick="switchTab(this,'week')">Week</span>
    <span class="mob-tab" onclick="switchTab(this,'month')">Month</span>
    <span class="mob-tab" onclick="switchTab(this,'all')">All</span>
  </div>

  <div class="mob-sale-list" id="saleList">
    <div style="text-align:center;padding:2rem;color:#94a3b8;">Loading...</div>
  </div>
</div>
@endsection
@section('scripts')
<script>
const Toast = Swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:3000});
let allSales = [];
const currency = window.__USER_CURRENCY || 'TZS';

async function loadSales(){
  try {
    const r = await apiFetch('/api/dashboard/sales');
    allSales = (r||[]).sort((a,b)=>new Date(b.created_at||b.sale_date)-new Date(a.created_at||a.sale_date));
    calcStats();
    renderSales('today');
  } catch(e){
    document.getElementById('saleList').innerHTML='<div class="mob-empty"><p>Failed to load sales</p></div>';
  }
}

function calcStats(){
  const now = new Date(); const today = now.toDateString();
  const weekStart = new Date(now); weekStart.setDate(now.getDate()-now.getDay());
  let todayTotal=0, weekTotal=0, monthTotal=0;
  const curMonth = now.getMonth(); const curYear = now.getFullYear();
  allSales.forEach(s => {
    const d = new Date(s.created_at||s.sale_date);
    const amt = Number(s.total_amount||s.total||s.grand_total||0);
    if (d.toDateString()===today) todayTotal+=amt;
    if (d>=weekStart) weekTotal+=amt;
    if (d.getMonth()===curMonth && d.getFullYear()===curYear) monthTotal+=amt;
  });
  const fmt = n => currency + ' ' + Number(n).toLocaleString();
  document.getElementById('todaySales').textContent = fmt(todayTotal);
  document.getElementById('weekSales').textContent = fmt(weekTotal);
  document.getElementById('monthSales').textContent = fmt(monthTotal);
  document.getElementById('totalOrders').textContent = allSales.length;
}

function switchTab(el,period){
  document.querySelectorAll('.mob-tab').forEach(t=>t.classList.remove('active'));
  el.classList.add('active');
  renderSales(period);
}

function renderSales(period){
  const now = new Date(); const today = now.toDateString();
  const weekStart = new Date(now); weekStart.setDate(now.getDate()-now.getDay());
  const curMonth = now.getMonth(); const curYear = now.getFullYear();
  let list = allSales.filter(s => {
    const d = new Date(s.created_at||s.sale_date);
    if (period==='today') return d.toDateString()===today;
    if (period==='week') return d>=weekStart;
    if (period==='month') return d.getMonth()===curMonth && d.getFullYear()===curYear;
    return true;
  });
  const el = document.getElementById('saleList');
  if (!list.length){
    el.innerHTML = `<div class="mob-empty">
      <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414a1 1 0 00-.707-.293H4"/></svg>
      <p style="font-weight:600;">No sales found</p>
    </div>`;
    return;
  }
  el.innerHTML = list.map(s => {
    const d = new Date(s.created_at||s.sale_date);
    const dateStr = d.toLocaleDateString('en-GB',{day:'2-digit',month:'short',hour:'2-digit',minute:'2-digit'});
    const amt = Number(s.total_amount||s.total||s.grand_total||0).toLocaleString();
    const cust = s.customer?.name || 'Walk-in';
    const ref = s.reference || s.invoice_number || '#';
    const status = s.status || 'completed';
    const badgeColor = status==='completed' ? 'color:#10b981;background:#f0fdf4;' : 'color:#f59e0b;background:#fffbeb;';
    return `<div class="mob-sale" onclick="viewSale(${s.id})">
      <div class="s-top">
        <span class="s-ref">${ref}</span>
        <span class="s-date">${dateStr}</span>
      </div>
      <div class="s-bot">
        <span class="s-amt">${currency} ${amt}</span>
        <span class="s-cust">${cust}</span>
        <span class="s-badge" style="${badgeColor}">${status}</span>
      </div>
    </div>`;
  }).join('');
}

function viewSale(id){
  const s = allSales.find(x=>x.id==id);
  if (!s) return;
  const items = (s.items||[]).map(i => `<div style="display:flex;justify-content:space-between;padding:4px 0;border-bottom:1px solid #f1f5f9;font-size:.8rem;">
    <span>${i.product?.name||i.name||'Item'} x${i.quantity||1}</span>
    <span style="font-weight:600;">${currency} ${Number(i.total||i.price||0).toLocaleString()}</span>
  </div>`).join('');
  const amt = Number(s.total_amount||s.total||s.grand_total||0).toLocaleString();
  Swal.fire({
    title: s.reference||'Sale #'+id,
    html: `<div style="text-align:left;font-size:.85rem;line-height:1.8;">
      <div style="margin-bottom:8px;">
        <div style="display:flex;gap:8px;"><span style="font-weight:600;min-width:60px;">Date:</span> ${new Date(s.created_at||s.sale_date).toLocaleString()}</div>
        <div style="display:flex;gap:8px;"><span style="font-weight:600;min-width:60px;">Customer:</span> ${s.customer?.name||'Walk-in'}</div>
        <div style="display:flex;gap:8px;"><span style="font-weight:600;min-width:60px;">Payment:</span> ${s.payment_method||s.payment_type||'-'}</div>
      </div>
      <div style="border-top:1px solid #e2e8f0;padding-top:8px;margin-bottom:4px;font-weight:700;font-size:.78rem;color:#64748b;">ITEMS</div>
      ${items||'<div style="color:#94a3b8;font-size:.78rem;">No items</div>'}
      <div style="border-top:2px solid #0f172a;margin-top:8px;padding-top:8px;display:flex;justify-content:space-between;font-weight:800;font-size:1rem;">
        <span>Total</span>
        <span style="color:#059669;">${currency} ${amt}</span>
      </div>
    </div>`,
    confirmButtonColor:'#2563eb',
    confirmButtonText:'Close',
  });
}

loadSales();
</script>
@endsection
