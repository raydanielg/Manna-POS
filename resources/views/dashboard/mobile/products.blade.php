@extends('layouts.dashboard')
@section('page_title', 'Products')
@section('page_styles')
<style>
.mob-header{display:flex;align-items:center;gap:10px;margin-bottom:14px;}
.mob-search{flex:1;position:relative;}
.mob-search input{width:100%;padding:10px 36px 10px 14px;border-radius:12px;border:1.5px solid #e9edf5;background:#fff;font-size:.85rem;font-family:inherit;outline:none;transition:border-color .2s;}
.mob-search input:focus{border-color:#2563eb;box-shadow:0 0 0 3px rgba(37,99,235,.1);}
.mob-search svg{position:absolute;right:12px;top:50%;transform:translateY(-50%);width:18px;height:18px;color:#94a3b8;}
.mob-add-btn{width:42px;height:42px;border-radius:12px;background:linear-gradient(135deg,#2563eb,#1d4ed8);border:none;color:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 4px 12px rgba(37,99,235,.3);flex-shrink:0;transition:transform .2s;}
.mob-add-btn:active{transform:scale(.94);}
.mob-add-btn svg{width:20px;height:20px;}

.mob-prod-list{display:flex;flex-direction:column;gap:10px;}
.mob-prod-card{background:#fff;border-radius:14px;border:1.5px solid #eef2f6;padding:12px 14px;display:flex;align-items:center;gap:12px;cursor:pointer;transition:all .2s;-webkit-tap-highlight-color:transparent;position:relative;}
.mob-prod-card:active{transform:scale(.98);border-color:#2563eb;}
.mob-prod-card .prod-img{width:48px;height:48px;border-radius:10px;background:linear-gradient(135deg,#f1f5f9,#e2e8f0);flex-shrink:0;display:flex;align-items:center;justify-content:center;overflow:hidden;}
.mob-prod-card .prod-img img{width:100%;height:100%;object-fit:cover;}
.mob-prod-card .prod-info{flex:1;min-width:0;}
.mob-prod-card .prod-name{font-weight:700;font-size:.85rem;color:#0f172a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.mob-prod-card .prod-sku{font-size:.68rem;color:#94a3b8;margin-top:1px;}
.mob-prod-card .prod-bottom{display:flex;align-items:center;gap:8px;margin-top:4px;}
.mob-prod-card .prod-price{font-weight:800;font-size:1rem;color:#2563eb;}
.mob-prod-card .prod-stock{font-size:.65rem;font-weight:600;padding:2px 8px;border-radius:50px;}
.mob-prod-card .prod-action{position:absolute;right:14px;top:14px;color:#94a3b8;width:24px;height:24px;cursor:pointer;}
.mob-empty{text-align:center;padding:3rem 1rem;color:#94a3b8;}
.mob-empty svg{width:56px;height:56px;margin:0 auto 1rem;display:block;color:#cbd5e1;}
.mob-empty p{font-weight:600;font-size:.9rem;}
.mob-empty span{font-size:.78rem;color:#cbd5e1;}

.mob-filter-bar{display:flex;gap:6px;margin-bottom:12px;overflow-x:auto;padding-bottom:4px;-webkit-overflow-scrolling:touch;}
.mob-filter-bar::-webkit-scrollbar{display:none;}
.mob-filter-chip{padding:6px 14px;border-radius:50px;font-size:.72rem;font-weight:600;white-space:nowrap;cursor:pointer;border:1.5px solid #e9edf5;background:#fff;color:#64748b;transition:all .2s;-webkit-tap-highlight-color:transparent;}
.mob-filter-chip.active{background:#2563eb;color:#fff;border-color:#2563eb;}

/* Skeleton loading */
.skel{background:linear-gradient(90deg,#f1f5f9 25%,#e2e8f0 50%,#f1f5f9 75%);background-size:200% 100%;animation:skel 1.4s infinite;border-radius:8px;}
@keyframes skel{0%{background-position:200% 0}100%{background-position:-200% 0}}
.skel-card{display:flex;align-items:center;gap:12px;padding:12px 14px;}
.skel-img{width:48px;height:48px;border-radius:10px;}
.skel-lines{flex:1;display:flex;flex-direction:column;gap:6px;}
.skel-line{height:12px;border-radius:6px;}
.skel-line.w60{width:60%}.skel-line.w40{width:40%}.skel-line.w30{width:30%}
</style>
@endsection
@section('content')
<div class="dash-content" id="mobProductsApp">
  <div class="mob-header">
    <div class="mob-search">
      <input type="text" placeholder="Search products..." id="mobSearchInput" oninput="debouncedSearch()">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
    </div>
    <button class="mob-add-btn" onclick="openAddProduct()">
      <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
    </button>
  </div>

  <div class="mob-filter-bar" id="mobFilterBar">
    <span class="mob-filter-chip active" data-filter="" onclick="setFilter(this,'')">All</span>
    <span class="mob-filter-chip" data-filter="active" onclick="setFilter(this,'active')">Active</span>
    <span class="mob-filter-chip" data-filter="low" onclick="setFilter(this,'low')">Low Stock</span>
    <span class="mob-filter-chip" data-filter="out" onclick="setFilter(this,'out')">Out of Stock</span>
  </div>

  <div class="mob-prod-list" id="mobProdList">
    <div class="skel-card"><div class="skel-img skel"></div><div class="skel-lines"><div class="skel-line w60 skel"></div><div class="skel-line w40 skel"></div><div class="skel-line w30 skel"></div></div></div>
    <div class="skel-card"><div class="skel-img skel"></div><div class="skel-lines"><div class="skel-line w60 skel"></div><div class="skel-line w40 skel"></div><div class="skel-line w30 skel"></div></div></div>
    <div class="skel-card"><div class="skel-img skel"></div><div class="skel-lines"><div class="skel-line w60 skel"></div><div class="skel-line w40 skel"></div><div class="skel-line w30 skel"></div></div></div>
  </div>
</div>

{{-- Add Product Bottom Sheet --}}
<div id="addProdSheet" class="mob-sheet" style="display:none;">
  <div class="mob-sheet-backdrop" onclick="closeAddProduct()"></div>
  <div class="mob-sheet-panel">
    <div class="mob-sheet-handle"></div>
    <div class="mob-sheet-title">Add Product</div>
    <form id="addProdForm" onsubmit="return saveProduct(event)">
      <div class="mob-field">
        <label>Product Name</label>
        <input name="name" required placeholder="Enter product name">
      </div>
      <div class="mob-field">
        <label>SKU</label>
        <input name="sku" placeholder="Auto-generated" id="skuField">
      </div>
      <div class="mob-row">
        <div class="mob-field" style="flex:1;">
          <label>Price</label>
          <input name="price" type="number" step="0.01" required placeholder="0.00">
        </div>
        <div class="mob-field" style="flex:1;">
          <label>Stock</label>
          <input name="stock_quantity" type="number" placeholder="0">
        </div>
      </div>
      <div class="mob-field">
        <label>Category</label>
        <select name="category_id" id="catSelect"><option value="">Select category</option></select>
      </div>
      <button type="submit" class="mob-submit-btn">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        Save Product
      </button>
    </form>
  </div>
</div>
@endsection
@section('scripts')
<script>
const Toast = Swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:3000,timerProgressBar:true,didOpen:t=>{t.addEventListener('mouseenter',Swal.stopTimer);t.addEventListener('mouseleave',Swal.resumeTimer);}});
let allProducts = [];
let currentFilter = '';

function debounce(fn,ms){let t;return(...a)=>{clearTimeout(t);t=setTimeout(()=>fn(...a),ms);}}
const debouncedSearch = debounce(() => renderProducts(), 300);

function setFilter(el,f){
  document.querySelectorAll('.mob-filter-chip').forEach(c=>c.classList.remove('active'));
  el.classList.add('active');
  currentFilter = f;
  renderProducts();
}

function renderProducts(){
  const q = (document.getElementById('mobSearchInput').value || '').toLowerCase();
  let list = allProducts;
  if (q) list = list.filter(p => (p.name||'').toLowerCase().includes(q) || (p.sku||'').toLowerCase().includes(q));
  if (currentFilter === 'low') list = list.filter(p => (p.stock_quantity||0) > 0 && (p.stock_quantity||0) <= (p.reorder_level||5));
  else if (currentFilter === 'out') list = list.filter(p => (p.stock_quantity||0) <= 0);
  else if (currentFilter === 'active') list = list.filter(p => p.is_active !== false);

  const container = document.getElementById('mobProdList');
  if (!list.length){
    container.innerHTML = `<div class="mob-empty">
      <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414a1 1 0 00-.707-.293H4"/></svg>
      <p>No products found</p>
      <span>${q ? 'Try a different search' : 'Add your first product to get started'}</span>
    </div>`;
    return;
  }

  container.innerHTML = list.map(p => {
    const stock = p.stock_quantity || 0;
    const reorder = p.reorder_level || 5;
    let stockClass = 'color:#10b981;background:#f0fdf4;';
    if (stock <= 0) stockClass = 'color:#ef4444;background:#fef2f2;';
    else if (stock <= reorder) stockClass = 'color:#f59e0b;background:#fffbeb;';
    return `<div class="mob-prod-card" onclick="viewProduct(${p.id})">
      <div class="prod-img">${p.image ? `<img src="${p.image}" alt="">` : `<svg width="24" height="24" fill="none" stroke="#94a3b8" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>`}</div>
      <div class="prod-info">
        <div class="prod-name">${esc(p.name)}</div>
        <div class="prod-sku">${esc(p.sku||'No SKU')}</div>
        <div class="prod-bottom">
          <span class="prod-price">${window.__USER_CURRENCY||'TZS'} ${Number(p.selling_price||0).toLocaleString()}</span>
          <span class="prod-stock" style="${stockClass}">${stock <= 0 ? 'Out' : stock}</span>
        </div>
      </div>
    </div>`;
  }).join('');
}

function esc(s){return String(s||'').replace(/[&<>"']/g,function(m){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m];});}

async function loadProducts(){
  try {
    const r = await apiFetch('/api/dashboard/products');
    allProducts = r || [];
    renderProducts();
  } catch(e){document.getElementById('mobProdList').innerHTML='<div class="mob-empty"><p>Failed to load products</p></div>';}
}

function openAddProduct(){
  document.getElementById('addProdSheet').style.display='block';
  setTimeout(()=>document.getElementById('addProdSheet').querySelector('.mob-sheet-panel').classList.add('open'),10);
  loadCategories();
}
function closeAddProduct(){
  document.getElementById('addProdSheet').querySelector('.mob-sheet-panel').classList.remove('open');
  setTimeout(()=>document.getElementById('addProdSheet').style.display='none',300);
}

async function loadCategories(){
  try {
    const r = await apiFetch('/api/dashboard/categories');
    const sel = document.getElementById('catSelect');
    sel.innerHTML = '<option value="">Select category</option>' + (r||[]).map(c => `<option value="${c.id}">${esc(c.name)}</option>`).join('');
  } catch(e){}
}

async function saveProduct(e){
  e.preventDefault();
  const fd = new FormData(e.target);
  const data = Object.fromEntries(fd);
  const btn = e.target.querySelector('button[type="submit"]');
  btn.disabled = true; const orig = btn.innerHTML;
  btn.innerHTML = 'Saving...';
  try {
    await apiFetch('/api/dashboard/products',{method:'POST',body:JSON.stringify(data)});
    Toast.fire({icon:'success',title:'Product added!'});
    closeAddProduct(); e.target.reset();
    loadProducts();
  } catch(err){
    Toast.fire({icon:'error',title:err.message||'Failed to save'});
  } finally { btn.disabled=false; btn.innerHTML=orig; }
}

function viewProduct(id){
  const p = allProducts.find(x=>x.id==id);
  if (!p) return;
  Swal.fire({
    title: esc(p.name),
    html: `<div style="text-align:left;font-size:.85rem;color:#475569;line-height:1.8;">
      <div style="display:flex;gap:8px;margin-bottom:10px;">
        <span style="font-weight:600;color:#0f172a;min-width:60px;">SKU:</span> ${esc(p.sku||'-')}
      </div>
      <div style="display:flex;gap:8px;">
        <span style="font-weight:600;color:#0f172a;min-width:60px;">Price:</span> ${window.__USER_CURRENCY||'TZS'} ${Number(p.selling_price||0).toLocaleString()}
      </div>
      <div style="display:flex;gap:8px;">
        <span style="font-weight:600;color:#0f172a;min-width:60px;">Stock:</span> ${p.stock_quantity||0}
      </div>
      ${p.category ? `<div style="display:flex;gap:8px;"><span style="font-weight:600;color:#0f172a;min-width:60px;">Category:</span> ${esc(p.category.name||'')}</div>` : ''}
    </div>`,
    icon: 'info',
    showCancelButton: true,
    confirmButtonText: 'Edit',
    cancelButtonText: 'Close',
    reverseButtons: true,
    confirmButtonColor:'#2563eb',
  }).then(r => {
    if (r.isConfirmed) window.location.href = '/dashboard/inventory/add-product?id='+id;
  });
}

loadProducts();
</script>
<style>
.mob-sheet{position:fixed;inset:0;z-index:9999;}
.mob-sheet-backdrop{position:absolute;inset:0;background:rgba(15,23,42,.5);}
.mob-sheet-panel{position:absolute;bottom:0;left:0;right:0;background:#fff;border-radius:20px 20px 0 0;padding:20px 20px 32px;transform:translateY(100%);transition:transform .35s cubic-bezier(.32,.72,0,1);max-height:85vh;overflow-y:auto;}
.mob-sheet-panel.open{transform:translateY(0);}
.mob-sheet-handle{width:36px;height:4px;background:#e2e8f0;border-radius:4px;margin:0 auto 16px;}
.mob-sheet-title{font-size:1.1rem;font-weight:800;color:#0f172a;margin-bottom:16px;}
.mob-field{margin-bottom:12px;}
.mob-field label{font-size:.75rem;font-weight:600;color:#64748b;display:block;margin-bottom:4px;}
.mob-field input,.mob-field select{width:100%;padding:10px 12px;border:1.5px solid #e9edf5;border-radius:10px;font-size:.85rem;font-family:inherit;outline:none;background:#fff;transition:border-color .2s;}
.mob-field input:focus,.mob-field select:focus{border-color:#2563eb;box-shadow:0 0 0 3px rgba(37,99,235,.1);}
.mob-row{display:flex;gap:10px;}
.mob-submit-btn{width:100%;padding:12px;border-radius:12px;background:linear-gradient(135deg,#2563eb,#1d4ed8);color:#fff;font-weight:700;font-size:.9rem;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px;margin-top:8px;box-shadow:0 4px 14px rgba(37,99,235,.3);transition:all .2s;}
.mob-submit-btn:active{transform:scale(.97);}
.mob-submit-btn:disabled{opacity:.6;}
</style>
@endsection
