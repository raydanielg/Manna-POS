@extends('layouts.dashboard')
@section('page_title','Invoice Settings')
@section('content')
<div class="dash-content">
<div class="page-card" style="max-width:860px;">
  <div class="card-header"><div class="card-title">Invoice Settings</div></div>
  <div class="modal-body">
    <form id="invoiceForm" onsubmit="saveSettings(event)">
      <h3 style="font-size:0.85rem;font-weight:700;color:#2563eb;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.75rem;">Invoice Header</h3>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Invoice Title</label><input name="invoice_title" class="form-control" placeholder="INVOICE" value="INVOICE"><div class="invalid-feedback"></div></div>
        <div class="form-group"><label class="form-label">Invoice Number Prefix</label><input name="invoice_prefix" class="form-control" placeholder="INV-" value="INV-"><div class="invalid-feedback"></div></div>
      </div>
      <div class="form-group"><label class="form-label">Header Note</label><textarea name="invoice_header" class="form-control" rows="2" placeholder="Custom text shown at the top of the invoice..."></textarea></div>
      <hr style="margin:1.5rem 0;border-color:#e9edf5;">
      <h3 style="font-size:0.85rem;font-weight:700;color:#2563eb;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.75rem;">Invoice Footer</h3>
      <div class="form-group"><label class="form-label">Footer Text</label><textarea name="invoice_footer" class="form-control" rows="2" placeholder="e.g. Thank you for your business! All prices are inclusive of VAT."></textarea></div>
      <div class="form-group"><label class="form-label">Payment Terms</label><input name="payment_terms" class="form-control" placeholder="e.g. Net 30, Payment due on receipt"></div>
      <hr style="margin:1.5rem 0;border-color:#e9edf5;">
      <h3 style="font-size:0.85rem;font-weight:700;color:#2563eb;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.75rem;">Display Options</h3>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Show Logo</label>
          <select name="show_logo" class="form-control">
            <option value="1">Yes</option><option value="0">No</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Show Tax Number</label>
          <select name="show_tax_number" class="form-control">
            <option value="1">Yes</option><option value="0">No</option>
          </select>
        </div>
      </div>
      <div style="display:flex;justify-content:flex-end;padding-top:1rem;">
        <button type="submit" class="btn btn-primary" id="saveBtn">Save Invoice Settings</button>
      </div>
    </form>
    <div id="previewBox" style="margin-top:1.5rem;border:1px solid #e9edf5;border-radius:10px;padding:1.5rem;background:#f8fafc;">
      <div style="font-size:0.85rem;font-weight:700;color:#64748b;margin-bottom:1rem;">Invoice Preview</div>
      <div style="border:1px solid #e2e8f0;border-radius:8px;padding:1.5rem;background:#fff;max-width:500px;">
        <div style="display:flex;justify-content:space-between;margin-bottom:1rem;">
          <div>
            <div style="font-size:1.2rem;font-weight:800;color:#1e293b;" id="prevTitle">INVOICE</div>
            <div style="font-size:0.8rem;color:#94a3b8;" id="prevPrefix">INV-000001</div>
          </div>
          <div style="text-align:right;font-size:0.8rem;color:#64748b;">
            <div>Date: Today</div>
          </div>
        </div>
        <div style="font-size:0.8rem;color:#64748b;margin-bottom:1rem;" id="prevHeader"></div>
        <div style="font-size:0.75rem;font-style:italic;color:#94a3b8;border-top:1px solid #f1f5f9;padding-top:0.75rem;margin-top:1rem;" id="prevFooter">Thank you for your business!</div>
      </div>
    </div>
  </div>
</div>
</div>
@endsection
@section('scripts')
<script>
document.querySelector('[name="invoice_title"]').addEventListener('input',function(){document.getElementById('prevTitle').textContent=this.value||'INVOICE';});
document.querySelector('[name="invoice_prefix"]').addEventListener('input',function(){document.getElementById('prevPrefix').textContent=(this.value||'INV-')+'000001';});
document.querySelector('[name="invoice_header"]').addEventListener('input',function(){document.getElementById('prevHeader').textContent=this.value;});
document.querySelector('[name="invoice_footer"]').addEventListener('input',function(){document.getElementById('prevFooter').textContent=this.value||'Thank you for your business!';});
async function saveSettings(e){
  e.preventDefault();
  const btn=document.getElementById('saveBtn');btn.disabled=true;btn.textContent='Saving...';
  try{
    const data=Object.fromEntries(new FormData(document.getElementById('invoiceForm')));
    await apiFetch('/api/dashboard/settings',{method:'PUT',body:JSON.stringify(data)});
    showToast('Invoice settings saved!');
  }catch(err){showToast(err.message||'Failed to save','error');}
  finally{btn.disabled=false;btn.textContent='Save Invoice Settings';}
}
</script>
@endsection
