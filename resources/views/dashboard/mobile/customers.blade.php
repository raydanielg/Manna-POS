@extends('layouts.dashboard')
@section('page_title', 'Customers')
@section('page_styles')
<style>
.mob-hdr{display:flex;align-items:center;gap:10px;margin-bottom:14px;}
.mob-srch{flex:1;position:relative;}
.mob-srch input{width:100%;padding:10px 36px 10px 14px;border-radius:12px;border:1.5px solid #e9edf5;background:#fff;font-size:.85rem;font-family:inherit;outline:none;}
.mob-srch input:focus{border-color:#8b5cf6;box-shadow:0 0 0 3px rgba(139,92,246,.1);}
.mob-srch svg{position:absolute;right:12px;top:50%;transform:translateY(-50%);width:18px;height:18px;color:#94a3b8;}
.mob-add{width:42px;height:42px;border-radius:12px;background:linear-gradient(135deg,#8b5cf6,#7c3aed);border:none;color:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 4px 12px rgba(139,92,246,.3);flex-shrink:0;}
.mob-add:active{transform:scale(.94);}
.mob-add svg{width:20px;height:20px;}

.mob-cust-list{display:flex;flex-direction:column;gap:8px;}
.mob-cust{background:#fff;border-radius:14px;border:1.5px solid #eef2f6;padding:10px 14px;display:flex;align-items:center;gap:12px;cursor:pointer;-webkit-tap-highlight-color:transparent;transition:all .2s;}
.mob-cust:active{transform:scale(.98);border-color:#8b5cf6;}
.mob-cust .c-init{width:40px;height:40px;border-radius:12px;background:linear-gradient(135deg,#8b5cf6,#a78bfa);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:.9rem;flex-shrink:0;}
.mob-cust .c-info{flex:1;min-width:0;}
.mob-cust .c-name{font-weight:700;font-size:.85rem;color:#0f172a;}
.mob-cust .c-phone{font-size:.7rem;color:#94a3b8;margin-top:1px;}
.mob-cust .c-amt{text-align:right;}
.mob-cust .c-amt .val{font-weight:800;font-size:.85rem;color:#0f172a;}
.mob-cust .c-amt .lbl{font-size:.6rem;color:#94a3b8;text-transform:uppercase;}

.mob-sht{position:fixed;inset:0;z-index:9999;}
.mob-sht-bk{position:absolute;inset:0;background:rgba(15,23,42,.5);}
.mob-sht-pnl{position:absolute;bottom:0;left:0;right:0;background:#fff;border-radius:20px 20px 0 0;padding:20px 20px 32px;transform:translateY(100%);transition:transform .35s cubic-bezier(.32,.72,0,1);max-height:85vh;overflow-y:auto;}
.mob-sht-pnl.open{transform:translateY(0);}
.mob-sht-h{width:36px;height:4px;background:#e2e8f0;border-radius:4px;margin:0 auto 16px;}
.mob-sht-t{font-size:1.1rem;font-weight:800;color:#0f172a;margin-bottom:16px;}
.mob-f{margin-bottom:12px;}
.mob-f label{font-size:.75rem;font-weight:600;color:#64748b;display:block;margin-bottom:4px;}
.mob-f input{width:100%;padding:10px 12px;border:1.5px solid #e9edf5;border-radius:10px;font-size:.85rem;font-family:inherit;outline:none;}
.mob-f input:focus{border-color:#8b5cf6;box-shadow:0 0 0 3px rgba(139,92,246,.1);}
.mob-btn{width:100%;padding:12px;border-radius:12px;background:linear-gradient(135deg,#8b5cf6,#7c3aed);color:#fff;font-weight:700;font-size:.9rem;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px;margin-top:8px;box-shadow:0 4px 14px rgba(139,92,246,.3);}
.mob-btn:active{transform:scale(.97);}
</style>
@endsection
@section('content')
<div class="dash-content" id="mobCustApp">
  <div class="mob-hdr">
    <div class="mob-srch">
      <input type="text" placeholder="Search customers..." id="custSearch" oninput="searchCust()">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
    </div>
    <button class="mob-add" onclick="openAddCust()">
      <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
    </button>
  </div>

  <div class="mob-cust-list" id="custList">
    <div style="text-align:center;padding:2rem;color:#94a3b8;font-size:.85rem;">Loading...</div>
  </div>
</div>

<div id="addCustSheet" class="mob-sht" style="display:none;">
  <div class="mob-sht-bk" onclick="closeAddCust()"></div>
  <div class="mob-sht-pnl">
    <div class="mob-sht-h"></div>
    <div class="mob-sht-t">Add Customer</div>
    <form id="addCustForm" onsubmit="return saveCust(event)">
      <div class="mob-f"><label>Full Name</label><input name="name" required placeholder="Enter name"></div>
      <div class="mob-f"><label>Phone Number</label><input name="phone" type="tel" required placeholder="+255 7XX XXX XXX"></div>
      <div class="mob-f"><label>Email</label><input name="email" type="email" placeholder="email@example.com"></div>
      <button type="submit" class="mob-btn">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        Save Customer
      </button>
    </form>
  </div>
</div>
@endsection
@section('scripts')
<script>
const Toast = Swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:3000});
let allCust = [];

function esc(s){return String(s||'').replace(/[&<>]/g,function(m){return{'&':'&amp;','<':'&lt;','>':'&gt;'}[m];});}

async function loadCust(){
  try {
    const r = await apiFetch('/api/dashboard/customers');
    allCust = r || [];
    renderCust('');
  } catch(e){
    document.getElementById('custList').innerHTML='<div style="text-align:center;padding:2rem;color:#ef4444;font-size:.85rem;">Failed to load</div>';
  }
}

function renderCust(q){
  let list = allCust;
  if (q) list = list.filter(c => (c.name||'').toLowerCase().includes(q) || (c.phone||'').includes(q));
  const el = document.getElementById('custList');
  if (!list.length){
    el.innerHTML = `<div style="text-align:center;padding:2.5rem;color:#94a3b8;">
      <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="width:48px;height:48px;margin:0 auto 10px;display:block;color:#cbd5e1;"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
      <p style="font-weight:600;font-size:.9rem;">${q ? 'No customers found' : 'No customers yet'}</p>
      <span style="font-size:.78rem;">${q ? 'Try a different search' : 'Add your first customer'}</span>
    </div>`;
    return;
  }
  el.innerHTML = list.map(c => {
    const init = (c.name||'?').charAt(0).toUpperCase();
    const total = Number(c.total_sales||0).toLocaleString();
    return `<div class="mob-cust" onclick="viewCust(${c.id})">
      <div class="c-init">${init}</div>
      <div class="c-info">
        <div class="c-name">${esc(c.name)}</div>
        <div class="c-phone">${esc(c.phone||'No phone')}</div>
      </div>
      <div class="c-amt">
        <div class="val">${window.__USER_CURRENCY||'TZS'} ${total}</div>
        <div class="lbl">Sales</div>
      </div>
    </div>`;
  }).join('');
}

function searchCust(){
  const q = document.getElementById('custSearch').value.toLowerCase();
  renderCust(q);
}

function openAddCust(){
  document.getElementById('addCustSheet').style.display='block';
  setTimeout(()=>document.getElementById('addCustSheet').querySelector('.mob-sht-pnl').classList.add('open'),10);
}
function closeAddCust(){
  const p = document.getElementById('addCustSheet');
  p.querySelector('.mob-sht-pnl').classList.remove('open');
  setTimeout(()=>p.style.display='none',300);
}

async function saveCust(e){
  e.preventDefault();
  const data = Object.fromEntries(new FormData(e.target));
  const btn = e.target.querySelector('button');
  btn.disabled=true; btn.innerHTML='Saving...';
  try {
    await apiFetch('/api/dashboard/customers',{method:'POST',body:JSON.stringify(data)});
    Toast.fire({icon:'success',title:'Customer added!'});
    closeAddCust(); e.target.reset();
    loadCust();
  } catch(err){
    Toast.fire({icon:'error',title:err.message||'Failed'});
  } finally { btn.disabled=false; btn.innerHTML='Save Customer'; }
}

function viewCust(id){
  const c = allCust.find(x=>x.id==id);
  if (!c) return;
  Swal.fire({
    title: esc(c.name),
    html: `<div style="text-align:left;font-size:.85rem;line-height:1.8;">
      <div style="display:flex;gap:8px;"><span style="font-weight:600;min-width:60px;">Phone:</span> ${esc(c.phone||'-')}</div>
      <div style="display:flex;gap:8px;"><span style="font-weight:600;min-width:60px;">Email:</span> ${esc(c.email||'-')}</div>
      <div style="display:flex;gap:8px;"><span style="font-weight:600;min-width:60px;">Total:</span> ${window.__USER_CURRENCY||'TZS'} ${Number(c.total_sales||0).toLocaleString()}</div>
    </div>`,
    icon: 'info',
    confirmButtonColor:'#8b5cf6',
    confirmButtonText:'OK',
  });
}

loadCust();
</script>
@endsection
