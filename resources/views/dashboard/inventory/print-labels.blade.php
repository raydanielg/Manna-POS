@extends('layouts.dashboard')
@section('page_title','Print Labels')
@section('head_scripts')
<style>
@media print {
  .sidebar,.topbar,#labelPrintArea~*{display:none!important;}
  #labelPrintArea{display:block!important;position:fixed;left:0;top:0;width:100%;background:#fff;}
  body,html{background:#fff;}
}
.label-card{border:1px dashed #94a3b8;border-radius:6px;padding:0.6rem 0.8rem;text-align:center;background:#fff;cursor:pointer;transition:all 0.15s;}
.label-card.selected{border-color:#2563eb;background:#eff6ff;}
</style>
@endsection
@section('content')
<div class="dash-content">
<div style="display:grid;grid-template-columns:1fr 340px;gap:1.25rem;">
  <div class="page-card">
    <div class="card-header">
      <div class="card-title">Select Products</div>
      <div class="filters-row">
        <div class="search-wrap">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
          <input type="text" id="searchInput" placeholder="Search products..." oninput="loadProducts()">
        </div>
      </div>
    </div>
    <div style="padding:1rem;overflow-y:auto;max-height:60vh;">
      <div id="productList" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:0.75rem;">
        <div style="color:#94a3b8;grid-column:1/-1;text-align:center;padding:2rem;">Loading...</div>
      </div>
    </div>
  </div>
  <div class="page-card" style="display:flex;flex-direction:column;">
    <div class="card-header"><div class="card-title">Label Preview</div></div>
    <div style="padding:1rem;border-bottom:1px solid #e9edf5;">
      <div class="form-group"><label class="form-label">Copies per label</label><input type="number" id="copies" value="1" min="1" max="100" class="form-control"></div>
      <div class="form-group"><label class="form-label">Label size</label>
        <select id="labelSize" class="form-control">
          <option value="small">Small (2×1 inch)</option>
          <option value="medium" selected>Medium (3×2 inch)</option>
          <option value="large">Large (4×3 inch)</option>
        </select>
      </div>
      <div style="display:flex;gap:0.5rem;">
        <button class="btn btn-secondary" style="flex:1;" onclick="clearSelection()">Clear</button>
        <button class="btn btn-primary" style="flex:1;" onclick="printLabels()">Print Labels</button>
      </div>
    </div>
    <div style="flex:1;padding:1rem;overflow-y:auto;" id="selectedList">
      <div style="color:#94a3b8;text-align:center;padding:2rem;font-size:0.85rem;">Select products to see preview</div>
    </div>
  </div>
</div>
</div>
<div id="labelPrintArea" style="display:none;"></div>
@endsection
@section('scripts')
<script>
let selected={};
async function loadProducts(){
  const s=document.getElementById('searchInput').value;
  const list=document.getElementById('productList');
  list.innerHTML='<div style="color:#94a3b8;grid-column:1/-1;text-align:center;padding:2rem;">Loading...</div>';
  try{
    const products=await apiFetch(`/api/dashboard/products?search=${encodeURIComponent(s)}`);
    if(!products.length){list.innerHTML='<div style="color:#94a3b8;grid-column:1/-1;text-align:center;padding:2rem;">No products found.</div>';return;}
    list.innerHTML=products.map(p=>`<div class="label-card ${selected[p.id]?'selected':''}" onclick="toggleSelect(${p.id},${JSON.stringify(p.name)},${JSON.stringify(p.sku||String(p.id))},${JSON.stringify(parseFloat(p.selling_price||0).toFixed(2))})">
      <div style="font-size:0.8rem;font-weight:700;color:#1e293b;">${p.name}</div>
      <div style="font-size:1rem;font-weight:700;color:#2563eb;margin:0.2rem 0;">${parseFloat(p.selling_price||0).toFixed(2)}</div>
      <div style="font-family:monospace;font-size:0.65rem;letter-spacing:0.1em;color:#1e293b;">${p.sku||p.id}</div>
      ${selected[p.id]?'<div style="font-size:0.7rem;color:#2563eb;margin-top:0.25rem;">✓ Selected</div>':''}
    </div>`).join('');
  }catch(e){list.innerHTML='<div style="color:#e03057;grid-column:1/-1;text-align:center;padding:2rem;">Error loading products.</div>';}
}
function toggleSelect(id,name,sku,price){
  if(selected[id])delete selected[id]; else selected[id]={name,sku,price};
  loadProducts(); renderSelected();
}
function renderSelected(){
  const keys=Object.keys(selected);
  const div=document.getElementById('selectedList');
  if(!keys.length){div.innerHTML='<div style="color:#94a3b8;text-align:center;padding:2rem;font-size:0.85rem;">Select products to see preview</div>';return;}
  div.innerHTML=keys.map(id=>{
    const p=selected[id];
    return `<div style="border:1px solid #e9edf5;border-radius:8px;padding:0.75rem;margin-bottom:0.5rem;background:#f8fafc;">
      <div style="font-size:0.8rem;font-weight:700;">${p.name}</div>
      <div style="font-size:0.85rem;color:#2563eb;font-weight:600;">${p.price}</div>
      <div style="font-size:0.7rem;color:#94a3b8;">${p.sku}</div>
      <button onclick="toggleSelect(${id},${JSON.stringify(p.name)},${JSON.stringify(p.sku)},${JSON.stringify(p.price)})" style="background:none;border:none;color:#e03057;font-size:0.75rem;cursor:pointer;padding:0;margin-top:0.25rem;">Remove</button>
    </div>`;
  }).join('');
}
function clearSelection(){selected={};renderSelected();loadProducts();}
function printLabels(){
  const keys=Object.keys(selected);
  if(!keys.length){showToast('Select at least one product','error');return;}
  const copies=parseInt(document.getElementById('copies').value)||1;
  const size=document.getElementById('labelSize').value;
  const dims={small:'width:144px;height:72px;',medium:'width:216px;height:144px;',large:'width:288px;height:216px;'};
  const fsize={small:'10',medium:'12',large:'14'};
  const labelHtml=keys.flatMap(id=>{
    const p=selected[id];
    return Array(copies).fill(`<div style="display:inline-block;${dims[size]}border:1px solid #000;margin:4px;padding:8px;text-align:center;vertical-align:top;page-break-inside:avoid;">
      <div style="font-size:${fsize[size]}px;font-weight:700;margin-bottom:4px;">${p.name}</div>
      <div style="font-size:${parseInt(fsize[size])+6}px;font-weight:800;">${p.price}</div>
      <div style="font-family:monospace;font-size:9px;letter-spacing:0.1em;margin-top:4px;">${p.sku}</div>
    </div>`);
  }).join('');
  const area=document.getElementById('labelPrintArea');
  area.style.display='block';
  area.innerHTML=labelHtml;
  setTimeout(()=>{window.print();setTimeout(()=>{area.style.display='none';},500);},100);
}
loadProducts();
</script>
@endsection
