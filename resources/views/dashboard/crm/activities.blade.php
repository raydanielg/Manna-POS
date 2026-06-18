@extends('layouts.dashboard')
@section('page_title','CRM Activities')
@section('content')
<div class="dash-content">
<div class="page-card">
  <div class="card-header">
    <div class="card-title">CRM Activities</div>
    <div class="filters-row">
      <div class="search-wrap">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
        <input type="text" id="searchInput" placeholder="Search customer or subject..." oninput="loadActivities()">
      </div>
      <select id="typeFilter" class="form-control" style="width:130px;" onchange="loadActivities()">
        <option value="">All Types</option>
        <option value="call">Call</option>
        <option value="email">Email</option>
        <option value="meeting">Meeting</option>
        <option value="note">Note</option>
        <option value="task">Task</option>
        <option value="sms">SMS</option>
        <option value="visit">Visit</option>
      </select>
      <select id="statusFilter" class="form-control" style="width:130px;" onchange="loadActivities()">
        <option value="">All Status</option>
        <option value="pending">Pending</option>
        <option value="completed">Completed</option>
        <option value="cancelled">Cancelled</option>
      </select>
      <button class="btn btn-primary" onclick="openModal()">+ New Activity</button>
    </div>
  </div>
  <div style="overflow-x:auto;">
    <table class="tbl">
      <thead><tr><th>#</th><th>Customer</th><th>Type</th><th>Subject</th><th>Description</th><th>Follow-up</th><th>Status</th><th>Created</th></tr></thead>
      <tbody id="tableBody"><tr><td colspan="8" class="tbl-empty">Loading...</td></tr></tbody>
    </table>
  </div>
</div>
</div>

{{-- Modal --}}
<div class="modal-overlay" id="modal">
  <div class="modal">
    <div class="modal-header"><div class="modal-title">New CRM Activity</div><button class="modal-close" onclick="closeModal()">&times;</button></div>
    <div class="modal-body">
      <div class="form-group"><label class="form-label">Customer</label><select id="mCustomer" class="form-control"></select></div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Type</label><select id="mType" class="form-control"><option value="call">Call</option><option value="email">Email</option><option value="meeting">Meeting</option><option value="note">Note</option><option value="task">Task</option><option value="sms">SMS</option><option value="visit">Visit</option></select></div>
        <div class="form-group"><label class="form-label">Status</label><select id="mStatus" class="form-control"><option value="pending">Pending</option><option value="completed">Completed</option><option value="cancelled">Cancelled</option></select></div>
      </div>
      <div class="form-group"><label class="form-label">Subject</label><input type="text" id="mSubject" class="form-control" placeholder="Subject..."></div>
      <div class="form-group"><label class="form-label">Description</label><textarea id="mDescription" class="form-control" placeholder="Description..."></textarea></div>
      <div class="form-group"><label class="form-label">Follow-up Date</label><input type="datetime-local" id="mFollowUp" class="form-control"></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal()">Cancel</button>
      <button class="btn btn-primary" onclick="saveActivity()">Save Activity</button>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script>
async function loadActivities(){
  const params=new URLSearchParams();
  const s=document.getElementById('searchInput').value; if(s) params.append('search',s);
  const t=document.getElementById('typeFilter').value; if(t) params.append('type',t);
  const st=document.getElementById('statusFilter').value; if(st) params.append('status',st);
  const res=await fetch('/api/dashboard/crm-activities?'+params,{headers:{'Accept':'application/json'}});
  const data=await res.json();
  const tbody=document.getElementById('tableBody');
  if(!data||!data.length){tbody.innerHTML='<tr><td colspan="8" class="tbl-empty">No activities found.</td></tr>';return;}
  const types={'call':'bg-blue-100 text-blue-700','email':'bg-indigo-100 text-indigo-700','meeting':'bg-violet-100 text-violet-700','note':'bg-slate-100 text-slate-700','task':'bg-amber-100 text-amber-700','sms':'bg-emerald-100 text-emerald-700','visit':'bg-cyan-100 text-cyan-700'};
  const statuses={'pending':'badge-warning','completed':'badge-success','cancelled':'badge-gray'};
  tbody.innerHTML=data.map((a,i)=>`<tr>
    <td>${i+1}</td>
    <td><strong>${esc(a.customer?.name||'N/A')}</strong><br><span style="font-size:0.7rem;color:#94a3b8">${esc(a.customer?.phone||'')}</span></td>
    <td><span class="badge ${types[a.type]||'badge-gray'}">${a.type}</span></td>
    <td>${esc(a.subject||'-')}</td>
    <td>${esc((a.description||'').substring(0,60))}${(a.description||'').length>60?'...':''}</td>
    <td>${a.follow_up_date?fmtDate(a.follow_up_date):'-'}</td>
    <td><span class="badge ${statuses[a.status]||'badge-gray'}">${a.status}</span></td>
    <td>${fmtDate(a.created_at)}</td>
  </tr>`).join('');
}
async function loadCustomers(){
  const res=await fetch('/api/dashboard/customers',{headers:{'Accept':'application/json'}});
  const data=await res.json();
  const sel=document.getElementById('mCustomer');
  sel.innerHTML=data.map(c=>`<option value="${c.id}">${esc(c.name)} ${c.phone?'('+esc(c.phone)+')':''}</option>`).join('');
}
function openModal(){document.getElementById('modal').classList.add('open'); loadCustomers();}
function closeModal(){document.getElementById('modal').classList.remove('open');}
async function saveActivity(){
  const body={customer_id:document.getElementById('mCustomer').value,type:document.getElementById('mType').value,status:document.getElementById('mStatus').value,subject:document.getElementById('mSubject').value,description:document.getElementById('mDescription').value,follow_up_date:document.getElementById('mFollowUp').value||null};
  const res=await fetch('/api/dashboard/crm-activities',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content},body:JSON.stringify(body)});
  if(res.ok){closeModal();loadActivities();showToast('Activity saved successfully','success');}else{showToast('Failed to save activity','error');}
}
function esc(s){return(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');}
function fmtDate(d){return new Date(d).toLocaleDateString('en-GB',{day:'2-digit',month:'short',year:'numeric'});}
function showToast(msg,type){const t=document.createElement('div');t.className='toast toast-'+type;t.textContent=msg;document.getElementById('toast-container').appendChild(t);setTimeout(()=>t.remove(),3000);}
loadActivities();
</script>
@endsection
