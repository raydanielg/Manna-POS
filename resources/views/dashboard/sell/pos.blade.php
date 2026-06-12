@extends('layouts.dashboard')
@section('page_title','Point of Sale')
@section('content')
<div style="display:grid;grid-template-columns:1fr 380px;gap:1rem;padding:1.5rem;height:calc(100vh - 90px);">
  <!-- Products Panel -->
  <div style="display:flex;flex-direction:column;gap:0.75rem;overflow:hidden;">
    <div style="background:#fff;border-radius:12px;border:1px solid #e9edf5;padding:1rem;">
      <div class="search-wrap">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
        <input type="text" id="posSearch" placeholder="Search product name or scan barcode..." oninput="searchProducts()" style="font-size:0.95rem;">
      </div>
    </div>
    <div style="flex:1;overflow-y:auto;display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:0.75rem;align-content:start;" id="productsGrid">
      <div style="color:#94a3b8;text-align:center;grid-column:1/-1;padding:3rem;">Loading products...</div>
    </div>
  </div>
  <!-- Cart Panel -->
  <div style="background:#fff;border-radius:12px;border:1px solid #e9edf5;display:flex;flex-direction:column;overflow:hidden;">
    <div style="padding:1rem;border-bottom:1px solid #e9edf5;">
      <div style="font-size:0.95rem;font-weight:700;color:#1e293b;">Current Order</div>
      <div style="display:flex;align-items:center;gap:0.5rem;margin-top:0.5rem;">
        <select id="posCustomer" class="form-control" style="font-size:0.8rem;">
          <option value="">Walk-in Customer</option>
        </select>
      </div>
    </div>
    <div style="flex:1;overflow-y:auto;padding:0.75rem;" id="cartItems">
      <div style="color:#94a3b8;text-align:center;padding:2rem;font-size:0.85rem;">Cart is empty. Add products.</div>
    </div>
    <div style="padding:1rem;border-top:1px solid #e9edf5;background:#f8fafc;">
      <div style="display:flex;justify-content:space-between;margin-bottom:0.4rem;font-size:0.85rem;"><span style="color:#64748b;">Subtotal</span><span id="posSubtotal" style="font-weight:600;">0.00</span></div>
      <div style="display:flex;justify-content:space-between;margin-bottom:0.4rem;font-size:0.85rem;">
        <span style="color:#64748b;">Discount</span>
        <input type="number" id="posDiscount" min="0" step="0.01" value="0" style="width:80px;text-align:right;border:1px solid #e2e8f0;border-radius:6px;padding:0.2rem 0.4rem;font-size:0.8rem;" oninput="updateCart()">
      </div>
      <div style="display:flex;justify-content:space-between;margin-bottom:0.75rem;font-size:1.1rem;font-weight:700;color:#1e293b;border-top:1px solid #e2e8f0;padding-top:0.5rem;"><span>Total</span><span id="posTotal">0.00</span></div>
      <div style="display:flex;gap:0.5rem;margin-bottom:0.5rem;">
        <select id="posPayMethod" class="form-control" style="font-size:0.85rem;">
          <option value="cash">Cash</option><option value="card">Card</option><option value="mobile_money">Mobile Money</option><option value="credit">Credit</option>
        </select>
        <input type="number" id="posPaid" min="0" step="0.01" placeholder="Amount paid" class="form-control" style="font-size:0.85rem;" oninput="updateChange()">
      </div>
      <div style="display:flex;justify-content:space-between;font-size:0.85rem;margin-bottom:0.75rem;">
        <span style="color:#64748b;">Change</span><span id="posChange" style="font-weight:600;color:#15803d;">0.00</span>
      </div>
      <div style="display:flex;gap:0.5rem;">
        <button onclick="clearCart()" class="btn btn-secondary" style="flex:0 0 auto;">Clear</button>
        <button onclick="completeSale()" class="btn btn-success" style="flex:1;font-size:1rem;padding:0.75rem;">Complete Sale</button>
      </div>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script>
let products=[], cart={}, customers=[];
async function init(){
  try{
    [products, customers]=await Promise.all([apiFetch('/api/dashboard/products'),apiFetch('/api/dashboard/customers')]);
    const sel=document.getElementById('posCustomer');
    sel.innerHTML='<option value="">Walk-in Customer</option>'+customers.map(c=>`<option value="${c.id}">${c.name}</option>`).join('');
    renderProducts(products);
  }catch(e){document.getElementById('productsGrid').innerHTML='<div style="color:#e03057;grid-column:1/-1;padding:2rem;text-align:center;">Error loading products.</div>';}
}
function renderProducts(list){
  const grid=document.getElementById('productsGrid');
  if(!list.length){grid.innerHTML='<div style="color:#94a3b8;text-align:center;grid-column:1/-1;padding:2rem;">No products found.</div>';return;}
  grid.innerHTML=list.map(p=>`<div onclick="addToCart(${p.id})" style="background:#fff;border:2px solid ${cart[p.id]?'#2563eb':'#e9edf5'};border-radius:10px;padding:0.75rem;cursor:pointer;transition:border-color 0.15s;">
    <div style="font-size:0.8rem;font-weight:700;color:#1e293b;margin-bottom:0.25rem;">${p.name}</div>
    <div style="font-size:1rem;font-weight:700;color:#2563eb;">${parseFloat(p.selling_price||0).toFixed(2)}</div>
    <div style="font-size:0.7rem;color:${p.stock_quantity<=0?'#e03057':p.stock_quantity<=5?'#d97706':'#64748b'};">Stock: ${p.stock_quantity}</div>
    ${cart[p.id]?`<div style="font-size:0.7rem;background:#eff6ff;color:#2563eb;border-radius:4px;text-align:center;padding:0.1rem;margin-top:0.25rem;">In cart: ${cart[p.id].qty}</div>`:''}
  </div>`).join('');
}
function searchProducts(){
  const s=document.getElementById('posSearch').value.toLowerCase();
  renderProducts(s?products.filter(p=>p.name.toLowerCase().includes(s)||(p.sku&&p.sku.toLowerCase().includes(s))):products);
}
function addToCart(id){
  const p=products.find(x=>x.id==id);if(!p)return;
  if(!cart[id])cart[id]={product:p,qty:1,price:parseFloat(p.selling_price||0)};
  else cart[id].qty++;
  updateCart();searchProducts();
}
function removeFromCart(id){delete cart[id];updateCart();searchProducts();}
function changeQty(id,val){
  if(!cart[id])return;
  cart[id].qty=Math.max(1,parseInt(val)||1);
  updateCart();
}
function updateCart(){
  const keys=Object.keys(cart);
  const cartEl=document.getElementById('cartItems');
  if(!keys.length){cartEl.innerHTML='<div style="color:#94a3b8;text-align:center;padding:2rem;font-size:0.85rem;">Cart is empty.</div>';document.getElementById('posSubtotal').textContent='0.00';document.getElementById('posTotal').textContent='0.00';return;}
  const sub=keys.reduce((a,id)=>a+(cart[id].qty*cart[id].price),0);
  const disc=parseFloat(document.getElementById('posDiscount').value||0);
  const total=Math.max(0,sub-disc);
  document.getElementById('posSubtotal').textContent=sub.toFixed(2);
  document.getElementById('posTotal').textContent=total.toFixed(2);
  cartEl.innerHTML=keys.map(id=>{
    const item=cart[id];
    return `<div style="display:flex;align-items:center;gap:0.5rem;padding:0.5rem 0;border-bottom:1px solid #f1f5f9;">
      <div style="flex:1;font-size:0.82rem;font-weight:600;color:#1e293b;">${item.product.name}</div>
      <input type="number" value="${item.qty}" min="1" style="width:48px;text-align:center;border:1px solid #e2e8f0;border-radius:6px;padding:0.2rem;" onchange="changeQty(${id},this.value)">
      <div style="width:60px;text-align:right;font-size:0.82rem;font-weight:700;">${(item.qty*item.price).toFixed(2)}</div>
      <button onclick="removeFromCart(${id})" style="background:none;border:none;color:#e03057;cursor:pointer;font-size:1.1rem;">×</button>
    </div>`;
  }).join('');
  updateChange();
}
function updateChange(){
  const total=parseFloat(document.getElementById('posTotal').textContent||0);
  const paid=parseFloat(document.getElementById('posPaid').value||0);
  document.getElementById('posChange').textContent=Math.max(0,paid-total).toFixed(2);
}
async function completeSale(){
  const keys=Object.keys(cart);if(!keys.length){showToast('Cart is empty','error');return;}
  const sub=keys.reduce((a,id)=>a+(cart[id].qty*cart[id].price),0);
  const disc=parseFloat(document.getElementById('posDiscount').value||0);
  const total=Math.max(0,sub-disc);
  const paid=parseFloat(document.getElementById('posPaid').value||0)||total;
  const data={
    customer_id:document.getElementById('posCustomer').value||null,
    sale_date:new Date().toISOString().split('T')[0],
    status:'completed',
    payment_method:document.getElementById('posPayMethod').value,
    discount:disc, paid, subtotal:sub, tax:0, total,
    items:keys.map(id=>({product_id:id,unit_price:cart[id].price,quantity:cart[id].qty,discount:0}))
  };
  try{await apiFetch('/api/dashboard/sales',{method:'POST',body:JSON.stringify(data)});
  showToast('Sale completed!');cart={};document.getElementById('posDiscount').value=0;document.getElementById('posPaid').value='';updateCart();}
  catch(e){showToast(e.message||'Sale failed','error');}
}
function clearCart(){cart={};document.getElementById('posDiscount').value=0;document.getElementById('posPaid').value='';updateCart();searchProducts();}
init();
</script>
@endsection
