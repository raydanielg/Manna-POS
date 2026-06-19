@extends('layouts.dashboard')
@section('page_title','File Cabinet')
@section('page_styles')
<style>
.fc-wrap{max-width:1200px;margin:0 auto;}
.fc-hero{
    display:flex;align-items:center;justify-content:space-between;
    padding:1.75rem 2.25rem;border-radius:20px;margin-bottom:1.5rem;color:#fff;
    background:linear-gradient(135deg,rgba(15,23,42,0.96) 0%,rgba(30,58,138,0.93) 50%,rgba(37,99,235,0.88) 100%);
    backdrop-filter:blur(14px);-webkit-backdrop-filter:blur(14px);
    border:1px solid rgba(255,255,255,0.08);box-shadow:0 16px 40px rgba(15,23,42,0.18),inset 0 1px 0 rgba(255,255,255,0.1);
    position:relative;overflow:hidden;
}
.fc-hero::before{content:'';position:absolute;inset:0;background:radial-gradient(circle at 85% 20%,rgba(59,130,246,0.22) 0%,transparent 55%);pointer-events:none;}
.fc-hero::after{content:'';position:absolute;right:-20px;bottom:-30px;width:180px;height:180px;background:url('{{ asset('images/foldericons.png') }}') no-repeat center;background-size:contain;opacity:0.12;pointer-events:none;}
.fc-hero h1{font-size:1.4rem;font-weight:800;position:relative;letter-spacing:-0.03em;margin:0;}
.fc-hero p{font-size:.82rem;opacity:.82;margin:.35rem 0 0;position:relative;}
.fc-hero .btn{position:relative;box-shadow:0 4px 14px rgba(0,0,0,0.18);border:1px solid rgba(255,255,255,0.15);}
.fc-stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:1rem;margin-bottom:1.5rem;}
.fc-stat{
    background:rgba(255,255,255,0.85);border-radius:16px;border:1px solid rgba(226,232,240,0.5);
    padding:1.25rem 1.5rem;position:relative;overflow:hidden;transition:all .35s ease;
    backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px);box-shadow:0 2px 8px rgba(15,23,42,0.03);
}
.fc-stat:hover{transform:translateY(-4px);box-shadow:0 12px 32px rgba(15,23,42,0.08);background:rgba(255,255,255,0.95);border-color:rgba(59,130,246,0.2);}
.fc-stat .fcs-icon{width:40px;height:40px;border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:.75rem;font-size:1.1rem;}
.fc-stat .fcs-icon.blue{background:linear-gradient(135deg,#dbeafe,#bfdbfe);color:#2563eb;}
.fc-stat .fcs-icon.green{background:linear-gradient(135deg,#d1fae5,#bbf7d0);color:#059669;}
.fc-stat .fcs-icon.amber{background:linear-gradient(135deg,#fef3c7,#fde68a);color:#d97706;}
.fc-stat .fcs-label{font-size:.68rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:#64748b;margin-bottom:.3rem;}
.fc-stat .fcs-value{font-size:1.45rem;font-weight:800;color:#0f172a;line-height:1.15;letter-spacing:-0.02em;}
.fc-toolbar{display:flex;gap:1rem;align-items:center;flex-wrap:wrap;margin-bottom:1.5rem;}
.fc-search{position:relative;flex:1;min-width:220px;}
.fc-search svg{position:absolute;left:.85rem;top:50%;transform:translateY(-50%);width:16px;height:16px;color:#94a3b8;pointer-events:none;}
.fc-search input{width:100%;border:1px solid rgba(226,232,240,0.8);border-radius:12px;padding:.65rem .9rem .65rem 2.4rem;font-size:.85rem;color:#0f172a;background:rgba(248,250,252,0.8);outline:none;transition:all .2s ease;box-shadow:inset 0 1px 2px rgba(0,0,0,0.02);}
.fc-search input:focus{border-color:#3b82f6;background:#fff;box-shadow:0 0 0 3px rgba(59,130,246,0.1),inset 0 1px 2px rgba(0,0,0,0.02);}
.fc-categories{display:flex;gap:.4rem;flex-wrap:wrap;align-items:center;}
.fc-chip{display:inline-flex;align-items:center;gap:.3rem;padding:.4rem .85rem;border-radius:20px;font-size:.78rem;font-weight:700;color:#475569;background:rgba(241,245,249,0.8);border:1px solid rgba(226,232,240,0.6);cursor:pointer;transition:all .2s ease;text-decoration:none;}
.fc-chip:hover{background:#fff;border-color:rgba(59,130,246,0.3);box-shadow:0 2px 8px rgba(0,0,0,0.05);transform:translateY(-1px);}
.fc-chip.active{background:linear-gradient(135deg,#2563eb,#1d4ed8);color:#fff;border-color:transparent;box-shadow:0 4px 12px rgba(37,99,235,0.25);}
.file-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:1rem;}
.file-card{
    background:rgba(255,255,255,0.88);border-radius:18px;border:1px solid rgba(226,232,240,0.5);padding:1.25rem;
    transition:all .35s cubic-bezier(0.4,0,0.2,1);position:relative;overflow:hidden;
    backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px);box-shadow:0 2px 10px rgba(15,23,42,0.03);
}
.file-card:hover{transform:translateY(-6px);box-shadow:0 16px 40px rgba(15,23,42,0.1);border-color:rgba(59,130,246,0.18);background:rgba(255,255,255,0.96);}
.file-card .folder-img{position:absolute;top:0;right:0;width:80px;height:80px;opacity:0.05;pointer-events:none;}
.file-icon-wrap{display:flex;align-items:center;gap:.75rem;margin-bottom:1rem;}
.file-icon{width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.file-icon.pdf{background:linear-gradient(135deg,#fee2e2,#fecaca);color:#dc2626;}
.file-icon.doc{background:linear-gradient(135deg,#dbeafe,#bfdbfe);color:#2563eb;}
.file-icon.xls{background:linear-gradient(135deg,#d1fae5,#bbf7d0);color:#059669;}
.file-icon.image{background:linear-gradient(135deg,#fef3c7,#fde68a);color:#d97706;}
.file-icon.zip{background:linear-gradient(135deg,#f3e8ff,#e9d5ff);color:#7c3aed;}
.file-icon.file{background:linear-gradient(135deg,#f1f5f9,#e2e8f0);color:#475569;}
.file-ext{font-size:.65rem;font-weight:800;text-transform:uppercase;color:#94a3b8;letter-spacing:.06em;}
.file-title{font-size:.88rem;font-weight:700;color:#0f172a;line-height:1.35;margin-bottom:.3rem;word-break:break-word;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
.file-meta{font-size:.72rem;color:#94a3b8;margin-bottom:1rem;display:flex;align-items:center;gap:.5rem;}
.file-actions{display:flex;gap:.4rem;}
.file-actions .btn{flex:1;padding:.45rem .5rem;font-size:.72rem;border-radius:10px;font-weight:700;display:inline-flex;align-items:center;justify-content:center;gap:.25rem;transition:all .2s ease;}
.file-actions .btn:hover{transform:translateY(-1px);box-shadow:0 4px 10px rgba(0,0,0,0.1);}
.fc-empty{padding:4rem;text-align:center;color:#94a3b8;}
.fc-empty .fe-icon{width:56px;height:56px;margin:0 auto 1.25rem;display:block;color:#cbd5e1;}
.fc-empty .fe-title{font-weight:800;color:#64748b;font-size:1rem;margin-bottom:.4rem;}
.fc-empty .fe-desc{font-size:.82rem;color:#94a3b8;}
.fc-pagination{display:flex;justify-content:center;margin-top:1.5rem;gap:.3rem;flex-wrap:wrap;}
.fc-pagination .page-link{padding:.5rem .9rem;border-radius:10px;font-size:.82rem;font-weight:700;color:#475569;background:rgba(241,245,249,0.8);border:1px solid rgba(226,232,240,0.6);text-decoration:none;transition:all .2s ease;}
.fc-pagination .page-link:hover{background:#fff;border-color:rgba(59,130,246,0.3);box-shadow:0 2px 8px rgba(0,0,0,0.05);}
.fc-pagination .page-link.active{background:linear-gradient(135deg,#2563eb,#1d4ed8);color:#fff;border-color:transparent;box-shadow:0 4px 12px rgba(37,99,235,0.25);}
.fc-pagination .page-link.disabled{opacity:.5;pointer-events:none;}
.drop-zone{border:2px dashed rgba(203,213,225,0.6);border-radius:16px;padding:2.25rem;text-align:center;background:rgba(248,250,252,0.6);transition:all .25s ease;cursor:pointer;}
.drop-zone:hover{border-color:#3b82f6;background:rgba(239,246,255,0.6);box-shadow:0 0 0 4px rgba(59,130,246,0.06);}
.drop-zone.dragover{border-color:#3b82f6;background:rgba(239,246,255,0.8);box-shadow:0 0 0 4px rgba(59,130,246,0.1);}
.drop-zone .dz-icon{width:40px;height:40px;margin:0 auto .75rem;color:#94a3b8;}
.drop-zone .dz-title{font-size:.88rem;color:#475569;font-weight:700;margin:0;}
.drop-zone .dz-sub{font-size:.72rem;color:#94a3b8;margin:.3rem 0 0;}
</style>
@endsection
@section('content')
<div class="dash-content">
<div class="fc-wrap">

  <div class="fc-hero">
    <h1>File Cabinet</h1>
    <p>Store, organize, and manage your business documents</p>
  </div>

  <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:1.5rem;">
    <div style="background:#fff;border-radius:14px;border:1.5px solid #eef2f6;padding:1.25rem;text-align:center;">
      <div style="font-size:.65rem;color:#94a3b8;font-weight:700;uppercase;">Total Files</div>
      <div style="font-size:1.4rem;font-weight:800;color:#0f172a;">{{ number_format($stats['total']) }}</div>
    </div>
    <div style="background:#fff;border-radius:14px;border:1.5px solid #eef2f6;padding:1.25rem;text-align:center;">
      <div style="font-size:.65rem;color:#94a3b8;font-weight:700;uppercase;">Storage Used</div>
      <div style="font-size:1.4rem;font-weight:800;color:#0f172a;">
        @if($stats['total_size'] >= 1048576)
          {{ number_format($stats['total_size']/1048576, 2) }} MB
        @elseif($stats['total_size'] >= 1024)
          {{ number_format($stats['total_size']/1024, 2) }} KB
        @else
          {{ $stats['total_size'] }} B
        @endif
      </div>
    </div>
    <div style="background:#fff;border-radius:14px;border:1.5px solid #eef2f6;padding:1.25rem;text-align:center;display:flex;align-items:center;justify-content:center;">
      <button class="btn btn-primary" style="gap:.35rem;" onclick="document.getElementById('uploadModal').classList.add('open')">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Upload File
      </button>
    </div>
  </div>

  <div class="filter-row">
    <form method="GET" action="{{ route('dashboard.file-cabinet') }}" style="display:flex;gap:.75rem;flex-wrap:wrap;flex:1;">
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Search files...">
      <select name="category" onchange="this.form.submit()">
        <option value="">All Categories</option>
        @foreach($categories as $cat)
        <option value="{{ $cat }}" {{ request('category')==$cat?'selected':'' }}>{{ $cat }}</option>
        @endforeach
      </select>
      @if(request('search') || request('category'))
      <a href="{{ route('dashboard.file-cabinet') }}" class="btn btn-secondary btn-sm">Clear</a>
      @endif
    </form>
  </div>

  @if($files->count())
  <div class="file-grid">
    @foreach($files as $f)
    <div class="file-card">
      <div class="file-icon {{ $f->icon }}">
        @if($f->icon === 'image')
        <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 0 1 2.828 0L16 16m-2-2l1.586-1.586a2 2 0 0 1 2.828 0L20 14m-6-6h.01M6 20h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2z"/></svg>
        @elseif($f->icon === 'pdf')
        <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 0 0 2-2V9.414a1 1 0 0 0-.293-.707l-5.414-5.414A1 1 0 0 0 12.586 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2z"/></svg>
        @elseif($f->icon === 'doc')
        <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2z"/></svg>
        @elseif($f->icon === 'xls')
        <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"/><path d="M5 17v-2a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"/><path d="M3 7a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><path d="M12 3v4"/></svg>
        @else
        <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 0 0 2-2V9.414a1 1 0 0 0-.293-.707l-5.414-5.414A1 1 0 0 0 12.586 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2z"/></svg>
        @endif
      </div>
      <div class="file-title" title="{{ $f->title }}">{{ $f->title }}</div>
      <div class="file-meta">{{ strtoupper($f->file_extension) }} &middot; {{ $f->file_size_formatted }} &middot; {{ $f->created_at->format('M d, Y') }}</div>
      <div class="file-actions">
        <a href="{{ route('dashboard.file-cabinet.download', $f) }}" class="btn btn-edit btn-sm">Download</a>
        <form method="POST" action="{{ route('dashboard.file-cabinet.destroy', $f) }}" style="display:inline;" onsubmit="return confirm('Delete this file?');">@csrf @method('DELETE')
          <button type="submit" class="btn btn-delete btn-sm">Delete</button>
        </form>
      </div>
    </div>
    @endforeach
  </div>
  @else
  <div style="padding:4rem;text-align:center;color:#94a3b8;">
    <svg width="48" height="48" fill="none" stroke="#cbd5e1" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 1rem;display:block;"><path stroke-linecap="round" stroke-linejoin="round" d="M5 21v-16a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16l-3-2l-2 2l-2-2l-2 2l-2-2l-3 2"/><path d="M9 9l2 2l4-4"/></svg>
    <p style="font-weight:600;color:#64748b;">No files yet</p>
    <p style="font-size:.8rem;margin-top:.25rem;">Upload your first document to get started</p>
  </div>
  @endif

</div>
</div>

{{-- Upload Modal --}}
<div class="modal-overlay" id="uploadModal" onclick="if(event.target===this)this.classList.remove('open')">
  <div class="modal-box" style="max-width:520px;" onclick="event.stopPropagation()">
    <div class="modal-header"><h3 class="modal-title">Upload File</h3><button class="modal-close" onclick="document.getElementById('uploadModal').classList.remove('open')">&times;</button></div>
    <form method="POST" action="{{ route('dashboard.file-cabinet.store') }}" enctype="multipart/form-data">@csrf
      <div class="form-group"><label class="form-label">Title *</label><input name="title" class="form-control" required></div>
      <div class="form-group"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="2"></textarea></div>
      <div class="form-group"><label class="form-label">Category</label><input name="category" class="form-control" placeholder="e.g. Invoice, Receipt, Contract"></div>
      <div class="form-group">
        <label class="form-label">File * (max 50MB)</label>
        <div class="drop-zone" onclick="document.getElementById('fileInput').click()" ondragover="event.preventDefault();this.classList.add('dragover')" ondragleave="this.classList.remove('dragover')" ondrop="event.preventDefault();this.classList.remove('dragover');const dt=event.dataTransfer;document.getElementById('fileInput').files=dt.files;updateFileName()">
          <svg width="32" height="32" fill="none" stroke="#94a3b8" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto .5rem;display:block;"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
          <p style="font-size:.85rem;color:#64748b;font-weight:600;margin:0;">Click or drag file here</p>
          <p style="font-size:.7rem;color:#94a3b8;margin:.25rem 0 0;" id="fileName">No file selected</p>
        </div>
        <input type="file" name="file" id="fileInput" style="display:none;" required onchange="updateFileName()">
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" onclick="document.getElementById('uploadModal').classList.remove('open')">Cancel</button><button type="submit" class="btn btn-primary">Upload</button></div>
    </form>
  </div>
</div>

<script>
function updateFileName() {
  const input = document.getElementById('fileInput');
  document.getElementById('fileName').textContent = input.files.length ? input.files[0].name : 'No file selected';
}
</script>
@endsection
