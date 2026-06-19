<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $settings['store_title'] ?? ($user->business_name ?? 'My Store') }} — Online Store</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>tailwind.config={theme:{extend:{fontFamily:{sans:'Inter, sans-serif'}}}}</script>
  <style>
    body{font-family:'Inter',sans-serif;background:#f8fafc;}
    .store-header{background:linear-gradient(135deg,#0f172a,#1e293b);color:#fff;padding:2rem 1rem;text-align:center;position:relative;overflow:hidden;}
    .store-header::before{content:'';position:absolute;top:-50%;left:-10%;width:300px;height:300px;background:radial-gradient(circle,rgba(255,255,255,.06) 0%,transparent 70%);border-radius:50%;}
    .store-header h1{font-size:1.6rem;font-weight:800;letter-spacing:-.02em;position:relative;z-index:1;}
    .store-header p{font-size:.82rem;color:#94a3b8;margin-top:.35rem;position:relative;z-index:1;}

    .product-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:1.25rem;padding:1.5rem;max-width:1200px;margin:0 auto;}
    .product-card{background:#fff;border-radius:16px;border:1.5px solid #eef2f6;overflow:hidden;transition:all .3s cubic-bezier(.4,0,.2,1);display:flex;flex-direction:column;}
    .product-card:hover{transform:translateY(-4px);box-shadow:0 16px 40px -8px rgba(15,23,42,.12);border-color:#dbeafe;}
    .product-img{aspect-ratio:4/3;background:#f1f5f9;display:flex;align-items:center;justify-content:center;position:relative;overflow:hidden;}
    .product-img img{width:100%;height:100%;object-fit:cover;}
    .product-img .no-img{color:#cbd5e1;font-size:.75rem;font-weight:500;}
    .product-body{padding:1rem 1.1rem;flex:1;display:flex;flex-direction:column;}
    .product-name{font-size:.92rem;font-weight:700;color:#0f172a;line-height:1.3;margin-bottom:.3rem;}
    .product-desc{font-size:.75rem;color:#64748b;line-height:1.45;margin-bottom:.6rem;flex:1;}
    .product-price{font-size:1.1rem;font-weight:800;color:#2563eb;}
    .product-currency{font-size:.75rem;color:#94a3b8;font-weight:600;}
    .add-btn{width:100%;margin-top:.75rem;padding:.55rem;border-radius:10px;border:none;background:linear-gradient(135deg,#2563eb,#1d4ed8);color:#fff;font-weight:700;font-size:.82rem;cursor:pointer;transition:all .2s;display:flex;align-items:center;justify-content:center;gap:.35rem;}
    .add-btn:hover{box-shadow:0 4px 14px rgba(37,99,235,.3);transform:translateY(-1px);}
    .add-btn:active{transform:scale(.97);}

    .cart-bar{position:fixed;bottom:0;left:0;right:0;background:#fff;border-top:1.5px solid #e2e8f0;padding:.85rem 1.25rem;display:flex;align-items:center;justify-content:space-between;gap:1rem;z-index:50;box-shadow:0 -4px 20px rgba(0,0,0,.06);}
    .cart-info{font-size:.85rem;color:#475569;font-weight:600;}
    .cart-info strong{color:#0f172a;}
    .checkout-btn{background:linear-gradient(135deg,#10b981,#059669);color:#fff;border:none;border-radius:10px;padding:.6rem 1.25rem;font-weight:700;font-size:.85rem;cursor:pointer;transition:all .2s;white-space:nowrap;}
    .checkout-btn:hover{box-shadow:0 4px 14px rgba(16,185,129,.3);}

    .modal-overlay{display:none;position:fixed;inset:0;background:rgba(15,23,42,.5);z-index:100;align-items:center;justify-content:center;padding:1rem;}
    .modal-overlay.open{display:flex;}
    .modal-bg{background:#fff;border-radius:20px;max-width:480px;width:100%;max-height:90vh;overflow-y:auto;padding:1.5rem;box-shadow:0 24px 48px rgba(0,0,0,.2);}
    .modal-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;}
    .modal-header h2{font-size:1.1rem;font-weight:800;color:#0f172a;}
    .modal-close{background:none;border:none;color:#94a3b8;cursor:pointer;padding:.25rem;border-radius:6px;}
    .modal-close:hover{color:#475569;background:#f1f5f9;}

    .order-item{display:flex;align-items:center;gap:.75rem;padding:.6rem 0;border-bottom:1px solid #f1f5f9;}
    .order-item:last-child{border-bottom:none;}
    .order-qty{background:#eff6ff;color:#2563eb;font-weight:800;font-size:.72rem;padding:.15rem .45rem;border-radius:6px;min-width:28px;text-align:center;}
    .order-name{flex:1;font-size:.85rem;color:#0f172a;font-weight:600;}
    .order-sub{font-size:.82rem;color:#2563eb;font-weight:700;}

    .form-input{width:100%;padding:.65rem .85rem;border:1.5px solid #e2e8f0;border-radius:10px;font-size:.85rem;transition:all .2s;}
    .form-input:focus{outline:none;border-color:#2563eb;box-shadow:0 0 0 3px rgba(37,99,235,.08);}
    .form-label{display:block;font-size:.78rem;font-weight:700;color:#475569;margin-bottom:.3rem;}

    .receipt{background:#fff;border:1.5px dashed #cbd5e1;border-radius:12px;padding:1.25rem;margin-top:1rem;font-family:'Courier New',monospace;}
    .receipt h4{text-align:center;font-size:1rem;font-weight:800;color:#0f172a;margin-bottom:.75rem;border-bottom:1px dashed #cbd5e1;padding-bottom:.5rem;}
    .receipt-row{display:flex;justify-content:space-between;font-size:.8rem;color:#475569;margin-bottom:.35rem;}
    .receipt-row.total{font-weight:800;color:#0f172a;font-size:.95rem;border-top:1px dashed #cbd5e1;margin-top:.5rem;padding-top:.5rem;}

    @media(max-width:640px){
      .store-header h1{font-size:1.3rem;}
      .product-grid{grid-template-columns:repeat(2,1fr);gap:.85rem;padding:1rem;}
      .product-name{font-size:.82rem;}
      .product-price{font-size:1rem;}
    }
  </style>
</head>
<body>

<header class="store-header">
  <h1>{{ $settings['store_title'] ?? ($user->business_name ?? 'My Store') }}</h1>
  <p>{{ $settings['store_description'] ?? 'Browse our products and place your order' }}</p>
  @if($user->phone)
  <p style="margin-top:.5rem;font-size:.75rem;display:inline-flex;align-items:center;gap:.35rem;background:rgba(255,255,255,.1);padding:.25rem .7rem;border-radius:50px;">
    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
    {{ $user->phone }}
  </p>
  @endif
</header>

<main class="product-grid" id="productGrid">
  @forelse($products as $product)
  <div class="product-card animate__animated animate__fadeInUp" data-id="{{ $product->id }}" data-name="{{ $product->name }}" data-price="{{ $product->selling_price }}">
    @if($showImages)
    <div class="product-img">
      @if($product->image)
      <img src="{{ $product->image }}" alt="{{ $product->name }}" loading="lazy">
      @else
      <span class="no-img">{{ $product->name }}</span>
      @endif
    </div>
    @endif
    <div class="product-body">
      <div class="product-name">{{ $product->name }}</div>
      @if($product->description)
      <div class="product-desc">{{ Str::limit($product->description, 60) }}</div>
      @endif
      <div style="display:flex;align-items:baseline;gap:.3rem;">
        <span class="product-price">{{ number_format($product->price, 0) }}</span>
        <span class="product-currency">{{ $user->currency ?? 'TZS' }}</span>
      </div>
      @if($product->sku)
      <div style="font-size:.68rem;color:#94a3b8;margin-top:.2rem;">SKU: {{ $product->sku }}</div>
      @endif
      <button class="add-btn" onclick="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }})">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Add to Cart
      </button>
    </div>
  </div>
  @empty
  <div style="grid-column:1/-1;text-align:center;padding:3rem;color:#94a3b8;">
    <svg width="48" height="48" fill="none" stroke="#cbd5e1" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 1rem;display:block;"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
    <p style="font-weight:600;color:#64748b;">No products available yet</p>
    <p style="font-size:.8rem;margin-top:.25rem;">Please check back soon!</p>
  </div>
  @endforelse
</main>

{{-- Cart Bar --}}
<div class="cart-bar" id="cartBar" style="display:none;">
  <div class="cart-info"><strong id="cartCount">0</strong> item(s) · <strong id="cartTotal">0</strong> {{ $user->currency ?? 'TZS' }}</div>
  <button class="checkout-btn" onclick="openCheckout()">Place Order</button>
</div>

{{-- Checkout Modal --}}
<div class="modal-overlay" id="checkoutModal" onclick="if(event.target===this)closeCheckout()">
  <div class="modal-bg">
    <div class="modal-header">
      <h2>Your Order</h2>
      <button class="modal-close" onclick="closeCheckout()">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <div id="orderItems"></div>
    <div style="margin-top:1rem;">
      <div class="form-group" style="margin-bottom:.75rem;">
        <label class="form-label">Your Name *</label>
        <input type="text" class="form-input" id="custName" placeholder="John Doe">
      </div>
      <div class="form-group" style="margin-bottom:.75rem;">
        <label class="form-label">Phone Number *</label>
        <input type="tel" class="form-input" id="custPhone" placeholder="+255 7xx xxx xxx">
      </div>
      <div class="form-group" style="margin-bottom:.75rem;">
        <label class="form-label">Email (optional)</label>
        <input type="email" class="form-input" id="custEmail" placeholder="john@example.com">
      </div>
      <div class="form-group" style="margin-bottom:1rem;">
        <label class="form-label">Notes (optional)</label>
        <textarea class="form-input" id="custNotes" rows="2" placeholder="Any special requests..."></textarea>
      </div>
      <button class="checkout-btn" style="width:100%;padding:.75rem;font-size:.92rem;" onclick="submitOrder()">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:inline;vertical-align:middle;margin-right:.35rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        Confirm Order
      </button>
    </div>
  </div>
</div>

{{-- Receipt Modal --}}
<div class="modal-overlay" id="receiptModal" onclick="if(event.target===this)closeReceipt()">
  <div class="modal-bg" style="max-width:380px;">
    <div class="modal-header">
      <h2>Order Receipt</h2>
      <button class="modal-close" onclick="closeReceipt()">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <div id="receiptContent"></div>
    <button class="checkout-btn" style="width:100%;margin-top:1rem;" onclick="closeReceipt();cart={};updateCart();">Place Another Order</button>
  </div>
</div>

<script>
const CURRENCY = '{{ $user->currency ?? "TZS" }}';
const STORE_SLUG = '{{ $user->store_slug }}';
let cart = {};

function addToCart(id, name, price) {
  if (cart[id]) { cart[id].qty++; }
  else { cart[id] = { name, price, qty: 1 }; }
  updateCart();
  Swal.fire({ toast:true, position:'top-end', icon:'success', title:`${name} added`, showConfirmButton:false, timer:1500 });
}

function updateCart() {
  const ids = Object.keys(cart);
  const count = ids.reduce((s,id)=>s+cart[id].qty,0);
  const total = ids.reduce((s,id)=>s+cart[id].price*cart[id].qty,0);
  document.getElementById('cartCount').textContent = count;
  document.getElementById('cartTotal').textContent = total.toLocaleString();
  document.getElementById('cartBar').style.display = count > 0 ? 'flex' : 'none';
}

function openCheckout() {
  const itemsDiv = document.getElementById('orderItems');
  let html = '';
  let total = 0;
  Object.entries(cart).forEach(([id, item]) => {
    const sub = item.price * item.qty;
    total += sub;
    html += `<div class="order-item"><span class="order-qty">${item.qty}x</span><span class="order-name">${item.name}</span><span class="order-sub">${sub.toLocaleString()}</span></div>`;
  });
  html += `<div class="order-item" style="border-top:2px solid #e2e8f0;margin-top:.5rem;padding-top:.5rem;"><span></span><span style="font-size:.85rem;font-weight:700;color:#0f172a;">Total</span><span class="order-sub" style="font-size:1rem;">${total.toLocaleString()} ${CURRENCY}</span></div>`;
  itemsDiv.innerHTML = html;
  document.getElementById('checkoutModal').classList.add('open');
}
function closeCheckout(){document.getElementById('checkoutModal').classList.remove('open');}
function closeReceipt(){document.getElementById('receiptModal').classList.remove('open');}

async function submitOrder() {
  const name = document.getElementById('custName').value.trim();
  const phone = document.getElementById('custPhone').value.trim();
  if (!name || !phone) {
    Swal.fire({ icon:'warning', title:'Missing Info', text:'Please enter your name and phone number', confirmButtonColor:'#2563eb' });
    return;
  }

  const items = Object.entries(cart).map(([id,item])=>({id:parseInt(id),qty:item.qty}));
  const payload = {
    customer_name: name,
    customer_phone: phone,
    customer_email: document.getElementById('custEmail').value.trim() || null,
    items,
    notes: document.getElementById('custNotes').value.trim() || null
  };

  Swal.fire({ title:'Placing order…', allowOutsideClick:false, didOpen:()=>Swal.showLoading() });

  try {
    const res = await fetch(`/store/${STORE_SLUG}/order`, {
      method:'POST',
      headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
      body: JSON.stringify(payload)
    });
    const data = await res.json();
    Swal.close();

    if (res.ok) {
      closeCheckout();
      showReceipt(data);
      Swal.fire({ toast:true, position:'top-end', icon:'success', title:data.message||'Order placed!', showConfirmButton:false, timer:3000 });
    } else {
      Swal.fire({ icon:'error', title:'Failed', text:data.message||'Could not place order', confirmButtonColor:'#2563eb' });
    }
  } catch(err) {
    Swal.close();
    Swal.fire({ icon:'error', title:'Network Error', text:'Please check your connection', confirmButtonColor:'#2563eb' });
  }
}

function showReceipt(data) {
  const items = Object.values(cart);
  let html = `<div class="receipt"><h4>{{ $settings['store_title'] ?? ($user->business_name ?? 'My Store') }}</h4>`;
  html += `<div class="receipt-row"><span>Order ID</span><span>${data.order_id}</span></div>`;
  html += `<div class="receipt-row"><span>Date</span><span>${new Date().toLocaleDateString()}</span></div>`;
  html += `<div style="border-bottom:1px dashed #cbd5e1;margin:.5rem 0;"></div>`;
  items.forEach(item => {
    html += `<div class="receipt-row"><span>${item.qty}x ${item.name}</span><span>${(item.price*item.qty).toLocaleString()}</span></div>`;
  });
  html += `<div class="receipt-row total"><span>TOTAL</span><span>${data.total.toLocaleString()} ${CURRENCY}</span></div></div>`;
  document.getElementById('receiptContent').innerHTML = html;
  document.getElementById('receiptModal').classList.add('open');
}
</script>

</body>
</html>
