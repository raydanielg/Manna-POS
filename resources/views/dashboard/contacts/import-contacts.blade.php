@extends('layouts.dashboard')
@section('page_title','Import Contacts')
@section('content')
<div class="dash-content">
<div class="page-card" style="max-width:860px;">
  <div class="card-header">
    <div class="card-title">Import Contacts from CSV</div>
  </div>
  <div class="modal-body">
    <form method="POST" action="{{ route('dashboard.contacts.import-contacts.post') }}" enctype="multipart/form-data" id="importForm">
      @csrf
      <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;">
        <div style="font-size:0.85rem;font-weight:700;color:#1d4ed8;margin-bottom:0.5rem;">CSV Format Requirements</div>
        <div style="font-size:0.82rem;color:#1e293b;">Required: <code>name</code></div>
        <div style="font-size:0.82rem;color:#64748b;margin-top:0.25rem;">Optional: <code>email, phone, address, city, country, notes</code></div>
        <a href="#" onclick="downloadTemplate()" class="btn btn-secondary" style="margin-top:0.75rem;font-size:0.8rem;padding:0.35rem 0.75rem;">Download Template</a>
      </div>

      <div class="form-group" style="margin-bottom:1.25rem;">
        <label class="form-label" style="font-size:0.78rem;font-weight:700;color:#475569;margin-bottom:0.4rem;display:block;text-transform:uppercase;letter-spacing:0.05em;">Contact Type</label>
        <select name="contact_type" id="contactType" class="form-control" style="width:180px;">
          <option value="customers">Customers</option>
          <option value="suppliers">Suppliers</option>
        </select>
      </div>

      {{-- PC File Upload --}}
      <div class="form-group" style="margin-bottom:1.25rem;">
        <label class="form-label" style="font-size:0.78rem;font-weight:700;color:#475569;margin-bottom:0.4rem;display:block;text-transform:uppercase;letter-spacing:0.05em;">Upload CSV from Computer</label>
        <div style="display:flex;align-items:center;gap:0.75rem;">
          <label class="btn btn-primary" style="cursor:pointer;padding:0.55rem 1.1rem;font-size:0.85rem;font-weight:600;display:inline-flex;align-items:center;gap:0.4rem;margin:0;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l-3 3m3-3l3 3M6.75 19.5h10.5a2.25 2.25 0 002.25-2.25v-5.25a2.25 2.25 0 00-2.25-2.25h-1.5a2.25 2.25 0 00-2.25 2.25v.75m-3 0V5.25A2.25 2.25 0 0111.25 3h1.5a2.25 2.25 0 012.25 2.25v1.5"/></svg>
            Choose File
            <input type="file" name="csv_file" id="csvFile" accept=".csv" style="display:none;" onchange="handleFile(this.files[0])" required>
          </label>
          <span id="fileLabel" style="font-size:0.82rem;color:#64748b;">No file selected</span>
        </div>
        <div style="font-size:0.75rem;color:#94a3b8;margin-top:0.4rem;">Supported: .csv files only</div>
      </div>

      {{-- Drag & Drop Zone --}}
      <div id="uploadZone" style="border:2px dashed #cbd5e1;border-radius:12px;padding:2rem;text-align:center;cursor:pointer;transition:all 0.2s;background:#f8fafc;" ondragover="event.preventDefault();this.style.borderColor='#2563eb';this.style.background='#eff6ff'" ondragleave="this.style.borderColor='#cbd5e1';this.style.background='#f8fafc'" ondrop="handleDrop(event)">
        <svg width="36" height="36" fill="none" stroke="#94a3b8" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 0.75rem;display:block;"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
        <div style="font-size:0.85rem;font-weight:600;color:#64748b;">Or drag & drop your CSV file here</div>
      </div>

      <div id="previewSection" style="display:none;margin-top:1.5rem;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.75rem;">
          <div style="font-weight:700;color:#1e293b;" id="previewTitle"></div>
          <button type="button" class="btn btn-secondary" onclick="clearFile()">Clear</button>
        </div>
        <div style="overflow-x:auto;max-height:300px;overflow-y:auto;border:1px solid #e2e8f0;border-radius:10px;"><table class="tbl" id="previewTable" style="margin:0;"></table></div>
      </div>

      <div style="margin-top:1.5rem;display:flex;gap:0.75rem;align-items:center;">
        <button type="submit" class="btn btn-success" id="importBtn" style="padding:0.65rem 1.5rem;font-size:0.9rem;font-weight:700;">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:inline;vertical-align:middle;margin-right:0.35rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L12 4m4 4v12"/></svg>
          Import All
        </button>
        <a href="{{ route('dashboard.contacts.customers') }}" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>
</div>
@endsection
@section('scripts')
<script>
let parsedData=[];
function downloadTemplate(){
  const type=document.getElementById('contactType').value;
  const csv=type==='suppliers'?'name,company,email,phone,address,city,country,notes\nSupplier A,ACME Corp,supplier@example.com,+1234567890,123 Main St,Nairobi,Kenya,Main supplier':'name,email,phone,address,city,country,notes\nJohn Doe,john@example.com,+1234567890,456 Oak Ave,Mombasa,Kenya,Regular customer';
  const blob=new Blob([csv],{type:'text/csv'});const a=document.createElement('a');a.href=URL.createObjectURL(blob);a.download=type+'_template.csv';a.click();
}
function handleDrop(e){e.preventDefault();document.getElementById('uploadZone').style.borderColor='#cbd5e1';document.getElementById('uploadZone').style.background='';handleFile(e.dataTransfer.files[0]);}
function handleFile(file){
  if(!file||!file.name.endsWith('.csv')){showToast('Please upload a CSV file','error');return;}
  document.getElementById('fileLabel').textContent=file.name;
  const reader=new FileReader();reader.onload=e=>parseCSV(e.target.result);reader.readAsText(file);
}
function parseCSV(text){
  const lines=text.trim().split('\n');if(lines.length<2){showToast('CSV is empty or has no data','error');return;}
  const headers=lines[0].split(',').map(h=>h.trim().replace(/"/g,''));
  parsedData=lines.slice(1).filter(l=>l.trim()).map(line=>{const vals=line.split(',').map(v=>v.trim().replace(/"/g,''));const obj={};headers.forEach((h,i)=>obj[h]=vals[i]||'');return obj;});
  document.getElementById('previewTitle').textContent=`Preview: ${parsedData.length} contact(s) to import`;
  const shown=['name','email','phone','city','country'];const cols=headers.filter(h=>shown.includes(h));
  document.getElementById('previewTable').innerHTML=`<thead><tr>${cols.map(h=>`<th>${h}</th>`).join('')}</tr></thead><tbody>${parsedData.slice(0,10).map(r=>`<tr>${cols.map(h=>`<td>${r[h]||'-'}</td>`).join('')}</tr>`).join('')}${parsedData.length>10?`<tr><td colspan="${cols.length}" class="tbl-empty text-xs">... and ${parsedData.length-10} more</td></tr>`:''}</tbody>`;
  document.getElementById('previewSection').style.display='block';
}
function clearFile(){parsedData=[];document.getElementById('previewSection').style.display='none';document.getElementById('csvFile').value='';document.getElementById('fileLabel').textContent='No file selected';}
</script>
@endsection
