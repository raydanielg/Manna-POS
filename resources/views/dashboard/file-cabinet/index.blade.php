@extends('layouts.dashboard')
@section('page_title','File Cabinet')
@section('page_styles')
<style>
.fc-wrap{max-width:1100px;margin:0 auto;}
.fc-hero{background:linear-gradient(135deg,#0f172a,#1e3a8a);border-radius:16px;padding:1.5rem 2rem;color:#fff;margin-bottom:1.5rem;position:relative;overflow:hidden;}
.fc-hero h1{font-size:1.2rem;font-weight:800;position:relative;z-index:1;}
.fc-hero p{font-size:.78rem;opacity:.8;margin-top:.25rem;position:relative;z-index:1;}
.file-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:1rem;}
.file-card{background:#fff;border-radius:14px;border:1.5px solid #eef2f6;padding:1.25rem;transition:all .2s;position:relative;}
.file-card:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(15,23,42,.08);}
.file-icon{width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:1rem;}
.file-icon.pdf{background:#fee2e2;color:#ef4444;}
.file-icon.doc{background:#dbeafe;color:#2563eb;}
.file-icon.xls{background:#d1fae5;color:#059669;}
.file-icon.image{background:#fef3c7;color:#d97706;}
.file-icon.zip{background:#f3e8ff;color:#7c3aed;}
.file-icon.file{background:#f1f5f9;color:#64748b;}
.file-title{font-size:.85rem;font-weight:700;color:#0f172a;line-height:1.3;margin-bottom:.25rem;word-break:break-word;}
.file-meta{font-size:.7rem;color:#94a3b8;margin-bottom:.75rem;}
.file-actions{display:flex;gap:.35rem;}
.file-actions .btn{flex:1;padding:.35rem;font-size:.7rem;border-radius:8px;}
.drop-zone{border:2px dashed #cbd5e1;border-radius:14px;padding:2rem;text-align:center;background:#f8fafc;transition:all .2s;cursor:pointer;}
.drop-zone:hover{border-color:#2563eb;background:#eff6ff;}
.drop-zone.dragover{border-color:#2563eb;background:#eff6ff;}
.filter-row{display:flex;gap:.75rem;flex-wrap:wrap;margin-bottom:1.5rem;}
.filter-row input, .filter-row select{border:1px solid #e2e8f0;border-radius:10px;padding:.55rem .85rem;font-size:.85rem;background:#fff;outline:none;}
.filter-row input:focus, .filter-row select:focus{border-color:#2563eb;}
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
