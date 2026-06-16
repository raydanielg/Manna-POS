@extends('layouts.dashboard')
@section('page_title','Barcode Settings')
@section('content')
<style>
.barcode-preview{display:flex;flex-direction:column;align-items:center;justify-content:center;border:1px solid #e2e8f0;border-radius:8px;padding:1.5rem;background:#fff;min-height:120px;}
.barcode-lines{display:flex;align-items:flex-end;gap:2px;margin-bottom:8px;}
.barcode-lines span{display:inline-block;background:#1e293b;height:var(--h,50px);width:var(--w,2px);}
.label-size-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:0.75rem;margin-top:0.5rem;}
.label-size-card{border:2px solid #e9edf5;border-radius:8px;padding:0.75rem;cursor:pointer;text-align:center;transition:all .15s;}
.label-size-card.selected{border-color:#2563eb;background:#eff6ff;}
.label-size-card:hover:not(.selected){border-color:#93c5fd;}
.label-size-card .size-name{font-weight:700;font-size:0.85rem;color:#1e293b;}
.label-size-card .size-dims{font-size:0.75rem;color:#64748b;margin-top:2px;}
</style>
<div class="dash-content">
<div class="page-card" style="max-width:860px;">
  <div class="card-header"><div class="card-title">Barcode Settings</div></div>
  <div class="modal-body">
    <form id="barcodeForm" onsubmit="saveSettings(event)">

      <h3 style="font-size:0.85rem;font-weight:700;color:#2563eb;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.75rem;">Barcode Type</h3>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Default Barcode Type</label>
          <select name="barcode_type" class="form-control" id="barcodeType" onchange="updatePreview()">
            <option value="CODE128">CODE128 (recommended)</option>
            <option value="CODE39">CODE39</option>
            <option value="EAN13">EAN-13</option>
            <option value="EAN8">EAN-8</option>
            <option value="UPCA">UPC-A</option>
            <option value="QR">QR Code</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Barcode Height (px)</label>
          <input name="barcode_height" type="number" class="form-control" value="50" min="20" max="120" id="barcodeHeight" oninput="updatePreview()">
        </div>
      </div>

      <hr style="margin:1.5rem 0;border-color:#e9edf5;">
      <h3 style="font-size:0.85rem;font-weight:700;color:#2563eb;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.75rem;">Label Size</h3>
      <div class="label-size-grid">
        <div class="label-size-card selected" onclick="selectSize(this,'small','38x25')" data-size="small">
          <div class="size-name">Small</div><div class="size-dims">38 × 25 mm</div>
        </div>
        <div class="label-size-card" onclick="selectSize(this,'medium','50x30')" data-size="medium">
          <div class="size-name">Medium</div><div class="size-dims">50 × 30 mm</div>
        </div>
        <div class="label-size-card" onclick="selectSize(this,'large','75x50')" data-size="large">
          <div class="size-name">Large</div><div class="size-dims">75 × 50 mm</div>
        </div>
        <div class="label-size-card" onclick="selectSize(this,'receipt','80x40')" data-size="receipt">
          <div class="size-name">Receipt</div><div class="size-dims">80 × 40 mm</div>
        </div>
        <div class="label-size-card" onclick="selectSize(this,'a4','210x297')" data-size="a4">
          <div class="size-name">A4 Sheet</div><div class="size-dims">A4 (multiple)</div>
        </div>
        <div class="label-size-card" onclick="selectSize(this,'custom','')" data-size="custom">
          <div class="size-name">Custom</div><div class="size-dims">Set own size</div>
        </div>
      </div>
      <input type="hidden" name="label_size" id="labelSizeInput" value="small">
      <div id="customSizeRow" style="display:none;margin-top:0.75rem;" class="form-row">
        <div class="form-group"><label class="form-label">Width (mm)</label><input name="label_width" type="number" class="form-control" placeholder="50" min="10" max="300"></div>
        <div class="form-group"><label class="form-label">Height (mm)</label><input name="label_height" type="number" class="form-control" placeholder="30" min="10" max="300"></div>
      </div>

      <hr style="margin:1.5rem 0;border-color:#e9edf5;">
      <h3 style="font-size:0.85rem;font-weight:700;color:#2563eb;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.75rem;">Label Content</h3>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Show Product Name</label>
          <select name="label_show_name" class="form-control">
            <option value="1">Yes</option><option value="0">No</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Show Price</label>
          <select name="label_show_price" class="form-control">
            <option value="1">Yes</option><option value="0">No</option>
          </select>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Show SKU</label>
          <select name="label_show_sku" class="form-control">
            <option value="1">Yes</option><option value="0">No</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Copies per Print</label>
          <input name="barcode_copies" type="number" class="form-control" value="1" min="1" max="100">
        </div>
      </div>

      <hr style="margin:1.5rem 0;border-color:#e9edf5;">
      <h3 style="font-size:0.85rem;font-weight:700;color:#2563eb;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.75rem;">Preview</h3>
      <div class="barcode-preview" id="barcodePreview">
        <div class="barcode-lines" id="previewBars"></div>
        <div style="font-size:0.75rem;color:#64748b;letter-spacing:0.1em;" id="previewType">CODE128</div>
        <div style="font-size:0.8rem;font-weight:600;color:#1e293b;margin-top:4px;">Sample Product</div>
      </div>

      <div style="display:flex;justify-content:flex-end;padding-top:1.5rem;">
        <button type="submit" class="btn btn-primary" id="saveBtn">Save Barcode Settings</button>
      </div>
    </form>
  </div>
</div>
</div>
@endsection
@section('scripts')
<script>
function updatePreview(){
  const type=document.getElementById('barcodeType').value;
  const h=parseInt(document.getElementById('barcodeHeight').value)||50;
  document.getElementById('previewType').textContent=type;
  const bars=document.getElementById('previewBars');
  if(type==='QR'){
    bars.innerHTML='<svg width="'+h+'" height="'+h+'" viewBox="0 0 10 10" xmlns="http://www.w3.org/2000/svg"><rect x="0" y="0" width="3" height="3" fill="#1e293b"/><rect x="4" y="0" width="1" height="1" fill="#1e293b"/><rect x="6" y="0" width="1" height="1" fill="#1e293b"/><rect x="7" y="0" width="3" height="3" fill="#1e293b"/><rect x="0" y="4" width="1" height="1" fill="#1e293b"/><rect x="2" y="4" width="2" height="1" fill="#1e293b"/><rect x="5" y="4" width="1" height="2" fill="#1e293b"/><rect x="7" y="4" width="1" height="1" fill="#1e293b"/><rect x="9" y="4" width="1" height="1" fill="#1e293b"/><rect x="0" y="7" width="3" height="3" fill="#1e293b"/><rect x="4" y="7" width="1" height="1" fill="#1e293b"/><rect x="6" y="8" width="2" height="1" fill="#1e293b"/><rect x="8" y="7" width="2" height="3" fill="#1e293b"/></svg>';
    return;
  }
  const pattern=[3,1,2,1,2,3,1,1,3,2,1,2,3,1,2,1,1,3,2,2,1,1,3,1,2,2];
  let html='';
  pattern.forEach(function(w,i){
    const isBold=(i%2===0);
    if(isBold) html+='<span style="--h:'+h+'px;--w:'+(w*2)+'px;"></span>';
    else html+='<span style="--h:'+h+'px;--w:'+(w)+'px;background:transparent;"></span>';
  });
  bars.innerHTML=html;
}

function selectSize(el,size,dims){
  document.querySelectorAll('.label-size-card').forEach(c=>c.classList.remove('selected'));
  el.classList.add('selected');
  document.getElementById('labelSizeInput').value=size;
  document.getElementById('customSizeRow').style.display=size==='custom'?'grid':'none';
}

async function saveSettings(e){
  e.preventDefault();
  const btn=document.getElementById('saveBtn');btn.disabled=true;btn.textContent='Saving...';
  try{
    const data=Object.fromEntries(new FormData(document.getElementById('barcodeForm')));
    await apiFetch('/api/dashboard/settings',{method:'PUT',body:JSON.stringify(data)});
    showToast('Barcode settings saved!');
  }catch(err){showToast(err.message||'Failed to save','error');}
  finally{btn.disabled=false;btn.textContent='Save Barcode Settings';}
}

async function loadSettings(){
  try{
    const d=await apiFetch('/api/dashboard/settings');
    const fields=['barcode_type','barcode_height','label_show_name','label_show_price','label_show_sku','barcode_copies','label_width','label_height'];
    fields.forEach(function(f){
      const el=document.querySelector('[name="'+f+'"]');
      if(el&&d[f]!=null){el.value=d[f];}
    });
    if(d.label_size){
      document.querySelectorAll('.label-size-card').forEach(c=>c.classList.remove('selected'));
      const card=document.querySelector('[data-size="'+d.label_size+'"]');
      if(card){card.classList.add('selected');document.getElementById('labelSizeInput').value=d.label_size;}
      document.getElementById('customSizeRow').style.display=d.label_size==='custom'?'grid':'none';
    }
    updatePreview();
  }catch(e){}
}
loadSettings();
</script>
@endsection
