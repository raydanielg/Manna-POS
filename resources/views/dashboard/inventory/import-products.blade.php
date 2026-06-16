@extends('layouts.dashboard')
@section('page_title','Import Products')
@section('content')
<div class="dash-content">
<div class="page-card" style="max-width:860px;">
  <div class="card-header"><div class="card-title">Import Products from CSV</div></div>
  <div class="modal-body">
    <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;">
      <div style="font-size:0.85rem;font-weight:700;color:#1d4ed8;margin-bottom:0.5rem;">CSV Format Requirements</div>
      <div style="font-size:0.82rem;color:#1e293b;">Required columns: <code>name, purchase_price, selling_price</code></div>
      <div style="font-size:0.82rem;color:#64748b;margin-top:0.25rem;">Optional: <code>sku, barcode, description, stock_quantity, reorder_level</code></div>
      <a href="#" onclick="downloadTemplate()" class="btn btn-secondary" style="margin-top:0.75rem;font-size:0.8rem;padding:0.35rem 0.75rem;">Download Template CSV</a>
    </div>
    <div id="uploadZone" style="border:2px dashed #cbd5e1;border-radius:12px;padding:3rem;text-align:center;cursor:pointer;transition:all 0.2s;" ondragover="event.preventDefault();this.style.borderColor='#2563eb'" ondragleave="this.style.borderColor='#cbd5e1'" ondrop="handleDrop(event)">
      <svg width="48" height="48" fill="none" stroke="#94a3b8" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 1rem;display:block;"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
      <div style="font-size:0.95rem;font-weight:600;color:#1e293b;margin-bottom:0.5rem;">Drop CSV file here or click to browse</div>
      <div style="font-size:0.8rem;color:#94a3b8;">Supports .csv files only</div>
      <input type="file" id="csvFile" accept=".csv" style="display:none;" onchange="handleFile(this.files[0])">
      <button onclick="document.getElementById('csvFile').click()" class="btn btn-primary" style="margin-top:1rem;">Browse File</button>
    </div>
    <div id="previewSection" style="display:none;margin-top:1.5rem;">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.75rem;">
        <div style="font-weight:700;color:#1e293b;" id="previewTitle"></div>
        <div style="display:flex;gap:0.5rem;">
          <button class="btn btn-secondary" onclick="clearFile()">Clear</button>
          <button class="btn btn-success" id="importBtn" onclick="importData()">Import All</button>
        </div>
      </div>
      <div style="overflow-x:auto;max-height:400px;overflow-y:auto;">
        <table class="tbl" id="previewTable"></table>
      </div>
      <div id="importProgress" style="display:none;margin-top:1rem;"></div>
    </div>
  </div>
</div>
</div>
@endsection
@section('scripts')
<script>
let parsedData=[];
function downloadTemplate(){
  const csv='name,sku,purchase_price,selling_price,stock_quantity,reorder_level,description\nProduct A,SKU001,10.00,15.00,100,10,Sample product\nProduct B,SKU002,20.00,30.00,50,5,Another product';
  const blob=new Blob([csv],{type:'text/csv'});const a=document.createElement('a');a.href=URL.createObjectURL(blob);a.download='products_template.csv';a.click();
}
function handleDrop(e){e.preventDefault();document.getElementById('uploadZone').style.borderColor='#cbd5e1';handleFile(e.dataTransfer.files[0]);}
function handleFile(file){
  if(!file||!file.name.endsWith('.csv')){showToast('Please upload a CSV file','error');return;}
  const reader=new FileReader();
  reader.onload=function(e){parseCSV(e.target.result);};
  reader.readAsText(file);
}
function parseCSV(text){
  const lines=text.trim().split('\n');
  if(lines.length<2){showToast('CSV file is empty or has no data rows','error');return;}
  const headers=lines[0].split(',').map(h=>h.trim().replace(/"/g,''));
  parsedData=lines.slice(1).filter(l=>l.trim()).map(line=>{
    const values=line.split(',').map(v=>v.trim().replace(/"/g,''));
    const obj={};headers.forEach((h,i)=>obj[h]=values[i]||'');
    return obj;
  });
  showPreview(headers,parsedData);
}
function showPreview(headers,data){
  document.getElementById('previewTitle').textContent=`Preview: ${data.length} product(s) to import`;
  const allowed=['name','sku','barcode','purchase_price','selling_price','stock_quantity','reorder_level','description'];
  const filteredH=headers.filter(h=>allowed.includes(h));
  document.getElementById('previewTable').innerHTML=`<thead><tr>${filteredH.map(h=>`<th>${h}</th>`).join('')}</tr></thead><tbody>${data.slice(0,10).map(row=>`<tr>${filteredH.map(h=>`<td class="text-sm">${row[h]||'-'}</td>`).join('')}</tr>`).join('')}${data.length>10?`<tr><td colspan="${filteredH.length}" class="tbl-empty text-xs">... and ${data.length-10} more rows</td></tr>`:''}</tbody>`;
  document.getElementById('previewSection').style.display='block';
}
function clearFile(){parsedData=[];document.getElementById('previewSection').style.display='none';document.getElementById('csvFile').value='';}
async function importData(){
  if(!parsedData.length){showToast('No data to import','error');return;}
  const btn=document.getElementById('importBtn');btn.disabled=true;btn.textContent='Importing...';
  const progress=document.getElementById('importProgress');progress.style.display='block';
  let success=0,failed=0,errors=[];
  for(const row of parsedData){
    if(!row.name){failed++;errors.push(`Row missing name: ${JSON.stringify(row)}`);continue;}
    try{
      await apiFetch('/api/dashboard/products',{method:'POST',body:JSON.stringify({
        name:row.name,sku:row.sku||null,barcode:row.barcode||null,
        purchase_price:parseFloat(row.purchase_price)||0,selling_price:parseFloat(row.selling_price)||0,
        stock_quantity:parseFloat(row.stock_quantity)||0,reorder_level:parseFloat(row.reorder_level)||0,
        description:row.description||null,status:'active'
      })});
      success++;
    }catch(e){failed++;errors.push(row.name+': '+(e.message||'Error'));}
    progress.innerHTML=`<div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:0.75rem;">Processed: ${success+failed}/${parsedData.length} — Success: ${success}, Failed: ${failed}</div>`;
  }
  btn.disabled=false;btn.textContent='Import All';
  if(failed===0)showToast(`Successfully imported ${success} products!`);
  else showToast(`Imported ${success}, failed ${failed}. Check console.`,'error');
  if(errors.length)console.warn('Import errors:',errors);
}
</script>
@endsection
