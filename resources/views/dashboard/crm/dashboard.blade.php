@extends('layouts.dashboard')
@section('page_title','CRM Dashboard')
@section('content')
<div class="dash-content">
  <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-bottom:1.5rem;">
    <div style="background:#fff;border:1px solid #e9edf5;border-radius:14px;padding:1.25rem;">
      <div style="font-size:0.72rem;font-weight:600;color:#2563eb;text-transform:uppercase;">Total Activities</div>
      <div style="font-size:1.8rem;font-weight:700;color:#1d4ed8;" id="totalActivities">-</div>
    </div>
    <div style="background:#fff;border:1px solid #e9edf5;border-radius:14px;padding:1.25rem;">
      <div style="font-size:0.72rem;font-weight:600;color:#d97706;text-transform:uppercase;">Pending Follow-ups</div>
      <div style="font-size:1.8rem;font-weight:700;color:#b45309;" id="pendingFollowUps">-</div>
    </div>
    <div style="background:#fff;border:1px solid #e9edf5;border-radius:14px;padding:1.25rem;">
      <div style="font-size:0.72rem;font-weight:600;color:#dc2626;text-transform:uppercase;">Overdue Tasks</div>
      <div style="font-size:1.8rem;font-weight:700;color:#be123c;" id="overdueTasks">-</div>
    </div>
    <div style="background:#fff;border:1px solid #e9edf5;border-radius:14px;padding:1.25rem;">
      <div style="font-size:0.72rem;font-weight:600;color:#16a34a;text-transform:uppercase;">Recent Interactions</div>
      <div style="font-size:1.8rem;font-weight:700;color:#15803d;" id="recentInteractions">-</div>
    </div>
  </div>

  <div style="display:grid;grid-template-columns:2fr 1fr;gap:1.5rem;">
    <div class="page-card">
      <div class="card-header"><div class="card-title">Upcoming Follow-ups (7 Days)</div></div>
      <div style="overflow-x:auto;">
        <table class="tbl">
          <thead><tr><th>Customer</th><th>Type</th><th>Subject</th><th>Follow-up Date</th><th>Status</th></tr></thead>
          <tbody id="followupsBody"><tr><td colspan="5" class="tbl-empty">Loading...</td></tr></tbody>
        </table>
      </div>
    </div>
    <div class="page-card">
      <div class="card-header"><div class="card-title">Activities by Type</div></div>
      <div style="padding:1.5rem;" id="activitiesByType"><div style="color:#94a3b8;text-align:center;">Loading...</div></div>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script>
async function loadCrmDashboard(){
  const res=await fetch('/api/dashboard/crm/dashboard',{headers:{'Accept':'application/json'}});
  const d=await res.json();
  document.getElementById('totalActivities').textContent=d.total_activities||0;
  document.getElementById('pendingFollowUps').textContent=d.pending_followups||0;
  document.getElementById('overdueTasks').textContent=d.overdue_tasks||0;
  document.getElementById('recentInteractions').textContent=d.recent_interactions||0;

  const tbody=document.getElementById('followupsBody');
  const statuses={'pending':'badge-warning','completed':'badge-success','cancelled':'badge-gray'};
  if(!d.upcoming_followups||!d.upcoming_followups.length){tbody.innerHTML='<tr><td colspan="5" class="tbl-empty">No upcoming follow-ups.</td></tr>';}
  else{tbody.innerHTML=d.upcoming_followups.map(f=>`<tr><td><strong>${esc(f.customer?.name||'N/A')}</strong><br><span style="font-size:0.7rem;color:#94a3b8">${esc(f.customer?.phone||'')}</span></td><td>${f.type}</td><td>${esc(f.subject||'-')}</td><td>${fmtDate(f.follow_up_date)}</td><td><span class="badge ${statuses[f.status]||'badge-gray'}">${f.status}</span></td></tr>`).join('');}

  const abt=document.getElementById('activitiesByType');
  if(d.activities_by_type&&Object.keys(d.activities_by_type).length){
    const colors={'call':'#3b82f6','email':'#6366f1','meeting':'#8b5cf6','note':'#64748b','task':'#f59e0b','sms':'#10b981','visit':'#06b6d4'};
    abt.innerHTML=Object.entries(d.activities_by_type).map(([type,count])=>`<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.6rem;padding:0.5rem 0.75rem;background:#f8fafc;border-radius:8px;"><div style="display:flex;align-items:center;gap:0.5rem;"><div style="width:10px;height:10px;border-radius:50%;background:${colors[type]||'#94a3b8'}"></div><span style="font-size:0.82rem;font-weight:500;color:#374151;text-transform:capitalize;">${type}</span></div><span style="font-size:0.82rem;font-weight:700;color:#0f172a;">${count}</span></div>`).join('');
  }else{abt.innerHTML='<div style="color:#94a3b8;text-align:center;">No data available.</div>';}
}
function esc(s){return(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');}
function fmtDate(d){return new Date(d).toLocaleDateString('en-GB',{day:'2-digit',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'});}
loadCrmDashboard();
</script>
@endsection
