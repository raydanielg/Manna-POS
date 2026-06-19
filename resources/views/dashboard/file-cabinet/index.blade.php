@extends('layouts.dashboard')
@section('page_title','File Cabinet')
@section('page_styles')
<style>
.fc-wrap{max-width:1200px;margin:0 auto;}
.fc-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;}
.fc-header h1{font-size:1.3rem;font-weight:800;color:#0f172a;margin:0;letter-spacing:-0.02em;}
.fc-header p{font-size:.8rem;color:#64748b;margin:.25rem 0 0;}
.fc-actions{display:flex;gap:.5rem;}
.fc-actions .btn{border-radius:10px;padding:.55rem 1rem;font-size:.8rem;font-weight:700;}
.fc-breadcrumb{display:flex;align-items:center;gap:.4rem;margin-bottom:1.25rem;font-size:.8rem;color:#64748b;flex-wrap:wrap;}
.fc-breadcrumb a{color:#2563eb;text-decoration:none;font-weight:600;}
.fc-breadcrumb a:hover{text-decoration:underline;}
.fc-breadcrumb .sep{color:#cbd5e1;}
.fc-breadcrumb .current{color:#0f172a;font-weight:700;}
.fc-dropzone{border:2px dashed #cbd5e1;border-radius:14px;padding:2rem;text-align:center;background:#f8fafc;transition:all .2s;cursor:pointer;margin-bottom:1.5rem;}
.fc-dropzone:hover{border-color:#2563eb;background:#eff6ff;}
.fc-dropzone.dragover{border-color:#2563eb;background:#eff6ff;}
.fc-dropzone .dz-icon{width:36px;height:36px;margin:0 auto .5rem;color:#94a3b8;}
.fc-dropzone .dz-title{font-size:.85rem;color:#475569;font-weight:700;margin:0;}
.fc-dropzone .dz-sub{font-size:.72rem;color:#94a3b8;margin:.25rem 0 0;}
.fc-upload-progress{display:none;margin-top:1rem;text-align:left;}
.fc-upload-progress.active{display:block;}
.fc-upload-progress .progress-bar{height:6px;background:#e2e8f0;border-radius:3px;overflow:hidden;}
.fc-upload-progress .progress-fill{height:100%;width:0;background:#2563eb;border-radius:3px;transition:width .3s ease;}
.fc-upload-progress .progress-text{font-size:.72rem;color:#64748b;margin-top:.4rem;}
.fc-section-title{font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#94a3b8;margin-bottom:.75rem;}
.fc-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:1rem;margin-bottom:1.5rem;}
.fc-folder{background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:1rem;cursor:pointer;transition:all .2s;display:flex;align-items:center;gap:.75rem;}
.fc-folder:hover{border-color:#2563eb;box-shadow:0 4px 12px rgba(0,0,0,0.06);transform:translateY(-2px);}
.fc-folder .f-icon{width:40px;height:40px;border-radius:10px;background:#fef3c7;color:#d97706;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.fc-folder .f-info{flex:1;min-width:0;}
.fc-folder .f-name{font-size:.85rem;font-weight:700;color:#0f172a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.fc-folder .f-meta{font-size:.7rem;color:#94a3b8;margin-top:.15rem;}
.fc-folder .f-del{color:#94a3b8;padding:.25rem;border-radius:6px;transition:all .15s;opacity:0;}
.fc-folder:hover .f-del{opacity:1;}
.fc-folder .f-del:hover{color:#ef4444;background:#fee2e2;}
.fc-file{background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:1rem;transition:all .2s;}
.fc-file:hover{border-color:#2563eb;box-shadow:0 4px 12px rgba(0,0,0,0.06);transform:translateY(-2px);}
.fc-file .fi-top{display:flex;align-items:flex-start;gap:.75rem;margin-bottom:.75rem;}
.fc-file .fi-icon{width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;background:#f1f5f9;color:#64748b;}
.fc-file .fi-icon.pdf{background:#fee2e2;color:#ef4444;}
.fc-file .fi-icon.doc{background:#dbeafe;color:#2563eb;}
.fc-file .fi-icon.xls{background:#d1fae5;color:#059669;}
.fc-file .fi-icon.image{background:#fef3c7;color:#d97706;}
.fc-file .fi-icon.zip{background:#f3e8ff;color:#7c3aed;}
.fc-file .fi-info{flex:1;min-width:0;}
.fc-file .fi-name{font-size:.85rem;font-weight:700;color:#0f172a;line-height:1.3;word-break:break-word;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
.fc-file .fi-meta{font-size:.7rem;color:#94a3b8;margin-top:.2rem;}
.fc-file .fi-actions{display:flex;gap:.35rem;}
.fc-file .fi-actions .btn{flex:1;padding:.35rem;font-size:.7rem;border-radius:8px;font-weight:700;}
.fc-empty{padding:3rem;text-align:center;}
.fc-empty .fe-icon{width:48px;height:48px;margin:0 auto 1rem;display:block;color:#cbd5e1;}
.fc-empty .fe-title{font-weight:700;color:#64748b;font-size:.9rem;}
.fc-empty .fe-desc{font-size:.75rem;color:#94a3b8;margin-top:.25rem;}
.fc-pagination{display:flex;justify-content:center;margin-top:1rem;gap:.25rem;}
.fc-pagination a,.fc-pagination span{padding:.45rem .75rem;border-radius:8px;font-size:.8rem;font-weight:600;color:#475569;background:#f1f5f9;border:1px solid #e2e8f0;text-decoration:none;}
.fc-pagination a:hover{background:#fff;border-color:#cbd5e1;}
.fc-pagination .active{background:#2563eb;color:#fff;border-color:#2563eb;}
.fc-modal-box{border-radius:16px;overflow:hidden;}
.fc-modal-box .modal-header{padding:1.25rem 1.5rem;border-bottom:1px solid #f1f5f9;}
.fc-modal-box .modal-title{font-size:1rem;font-weight:800;color:#0f172a;}
.fc-modal-box .modal-body{padding:1.25rem 1.5rem;}
.fc-modal-box .modal-footer{padding:1rem 1.5rem;border-top:1px solid #f1f5f9;display:flex;justify-content:flex-end;gap:.5rem;}
.fc-modal-box .form-label{font-size:.78rem;font-weight:700;color:#475569;margin-bottom:.4rem;display:block;}
.fc-modal-box .form-control{width:100%;border:1px solid #e2e8f0;border-radius:10px;padding:.6rem .85rem;font-size:.85rem;outline:none;transition:all .2s;}
.fc-modal-box .form-control:focus{border-color:#2563eb;box-shadow:0 0 0 3px rgba(37,99,235,0.08);}
</style>
@endsection
@section('content')
<div class="dash-content animate__animated animate__fadeInUp">
<div class="fc-wrap">

    <div class="fc-hero" data-aos="fade-down">
        <div>
            <h1>File Cabinet</h1>
            <p>Store, organize, and manage your business documents securely</p>
        </div>
        <button class="btn btn-primary" style="gap:.4rem;padding:.65rem 1.1rem;font-size:.85rem;" onclick="document.getElementById('uploadModal').classList.add('open')">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Upload File
        </button>
    </div>

    <div class="fc-stats" data-aos="fade-up" data-aos-delay="50">
        <div class="fc-stat">
            <div class="fcs-icon blue"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 21v-16a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16l-3-2l-2 2l-2-2l-2 2l-2-2l-3 2"/></svg></div>
            <div class="fcs-label">Total Files</div>
            <div class="fcs-value">{{ number_format($stats['total']) }}</div>
        </div>
        <div class="fc-stat">
            <div class="fcs-icon green"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7V4h3M4 17v3h3M20 7V4h-3M20 17v3h-3M9 9h6v6H9z"/></svg></div>
            <div class="fcs-label">Storage Used</div>
            <div class="fcs-value">
                @if($stats['total_size'] >= 1048576) {{ number_format($stats['total_size']/1048576, 1) }} MB
                @elseif($stats['total_size'] >= 1024) {{ number_format($stats['total_size']/1024, 1) }} KB
                @else {{ $stats['total_size'] }} B @endif
            </div>
        </div>
        <div class="fc-stat">
            <div class="fcs-icon amber"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg></div>
            <div class="fcs-label">Upload New</div>
            <div style="padding-top:.35rem;">
                <button class="btn btn-primary" style="font-size:.78rem;padding:.4rem .85rem;border-radius:10px;" onclick="document.getElementById('uploadModal').classList.add('open')">Choose File</button>
            </div>
        </div>
    </div>

    <div class="fc-toolbar" data-aos="fade-up" data-aos-delay="100">
        <div class="fc-search">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
            <input type="text" name="search" id="fcSearch" value="{{ request('search') }}" placeholder="Search files by name..." oninput="debounceSearch()">
        </div>
        <div class="fc-categories">
            <a href="{{ route('dashboard.file-cabinet') }}" class="fc-chip {{ !request('category') ? 'active' : '' }}">All</a>
            @foreach($categories as $cat)
            <a href="{{ route('dashboard.file-cabinet', ['category'=>$cat,'search'=>request('search')]) }}" class="fc-chip {{ request('category')==$cat ? 'active' : '' }}">{{ $cat }}</a>
            @endforeach
        </div>
    </div>

    @if($files->count())
    <div class="file-grid" data-aos="fade-up" data-aos-delay="150">
        @foreach($files as $f)
        <div class="file-card">
            <img src="{{ asset('images/foldericons.png') }}" class="folder-img" alt="">
            <div class="file-icon-wrap">
                <div class="file-icon {{ $f->icon }}">
                    @if($f->icon === 'image')
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 0 1 2.828 0L16 16m-2-2l1.586-1.586a2 2 0 0 1 2.828 0L20 14m-6-6h.01M6 20h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2z"/></svg>
                    @elseif($f->icon === 'pdf')
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 0 0 2-2V9.414a1 1 0 0 0-.293-.707l-5.414-5.414A1 1 0 0 0 12.586 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2z"/></svg>
                    @elseif($f->icon === 'doc')
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2z"/></svg>
                    @elseif($f->icon === 'xls')
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"/><path d="M5 17v-2a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"/><path d="M3 7a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><path d="M12 3v4"/></svg>
                    @else
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 0 0 2-2V9.414a1 1 0 0 0-.293-.707l-5.414-5.414A1 1 0 0 0 12.586 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2z"/></svg>
                    @endif
                </div>
                <span class="file-ext">{{ strtoupper($f->file_extension) }}</span>
            </div>
            <div class="file-title" title="{{ $f->title }}">{{ $f->title }}</div>
            <div class="file-meta">{{ $f->file_size_formatted }} <span class="dot"></span> {{ $f->created_at->format('M d, Y') }}</div>
            <div class="file-actions">
                <a href="{{ route('dashboard.file-cabinet.download', $f) }}" class="btn btn-edit btn-sm">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Download
                </a>
                <button type="button" class="btn btn-delete btn-sm" onclick="confirmDelete('{{ route('dashboard.file-cabinet.destroy', $f) }}')">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3M4 7h16"/></svg>
                    Delete
                </button>
            </div>
        </div>
        @endforeach
    </div>

    @if($files->hasPages())
    <div class="fc-pagination" data-aos="fade-up" data-aos-delay="200">
        {{ $files->onEachSide(1)->links('pagination::simple-tailwind') }}
    </div>
    @endif

    @else
    <div class="fc-empty" data-aos="fade-up" data-aos-delay="150">
        <svg class="fe-icon" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 21v-16a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16l-3-2l-2 2l-2-2l-2 2l-2-2l-3 2"/><path d="M9 9l2 2l4-4"/></svg>
        <div class="fe-title">No files yet</div>
        <div class="fe-desc">Upload your first document to get started</div>
    </div>
    @endif

</div>
</div>

{{-- Upload Modal --}}
<div class="modal-overlay" id="uploadModal" onclick="if(event.target===this)this.classList.remove('open')">
  <div class="modal-box" style="max-width:520px;border-radius:20px;" onclick="event.stopPropagation()">
    <div class="modal-header" style="padding:1.25rem 1.5rem;">
      <h3 class="modal-title" style="font-size:1.05rem;font-weight:800;letter-spacing:-0.02em;">Upload File</h3>
      <button class="modal-close" style="width:32px;height:32px;border-radius:10px;" onclick="document.getElementById('uploadModal').classList.remove('open')">&times;</button>
    </div>
    <form method="POST" action="{{ route('dashboard.file-cabinet.store') }}" enctype="multipart/form-data" id="uploadForm">@csrf
      <div style="padding:1.25rem 1.5rem;">
        <div class="form-group"><label class="form-label">Title *</label><input name="title" class="form-control" style="border-radius:12px;" required></div>
        <div class="form-group"><label class="form-label">Description</label><textarea name="description" class="form-control" style="border-radius:12px;" rows="2"></textarea></div>
        <div class="form-group"><label class="form-label">Category</label><input name="category" class="form-control" style="border-radius:12px;" placeholder="e.g. Invoice, Receipt, Contract"></div>
        <div class="form-group">
          <label class="form-label">File * (max 50MB)</label>
          <div class="drop-zone" onclick="document.getElementById('fileInput').click()" ondragover="event.preventDefault();this.classList.add('dragover')" ondragleave="this.classList.remove('dragover')" ondrop="event.preventDefault();this.classList.remove('dragover');const dt=event.dataTransfer;document.getElementById('fileInput').files=dt.files;updateFileName()">
            <svg class="dz-icon" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
            <div class="dz-title">Click or drag file here</div>
            <div class="dz-sub" id="fileName">No file selected</div>
          </div>
          <input type="file" name="file" id="fileInput" style="display:none;" required onchange="updateFileName()">
        </div>
      </div>
      <div class="modal-footer" style="padding:1rem 1.5rem;">
        <button type="button" class="btn btn-secondary" style="border-radius:10px;font-weight:700;" onclick="document.getElementById('uploadModal').classList.remove('open')">Cancel</button>
        <button type="submit" class="btn btn-primary" style="border-radius:10px;font-weight:700;gap:.35rem;">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
          Upload
        </button>
      </div>
    </form>
  </div>
</div>

<form method="POST" id="deleteForm" style="display:none;">@csrf @method('DELETE')</form>

<script>
let _fcTimer;
function debounceSearch() {
  clearTimeout(_fcTimer);
  _fcTimer = setTimeout(() => {
    const s = document.getElementById('fcSearch').value;
    const url = new URL(window.location.href);
    if (s) url.searchParams.set('search', s); else url.searchParams.delete('search');
    window.location.href = url.toString();
  }, 500);
}
function updateFileName() {
  const input = document.getElementById('fileInput');
  const name = input.files.length ? input.files[0].name : 'No file selected';
  document.getElementById('fileName').textContent = name;
}
function confirmDelete(actionUrl) {
  Swal.fire({
    title: 'Delete file?',
    text: 'This action cannot be undone.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Delete',
    cancelButtonText: 'Cancel',
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#64748b',
    reverseButtons: true
  }).then((result) => {
    if (result.isConfirmed) {
      const form = document.getElementById('deleteForm');
      form.action = actionUrl;
      form.submit();
    }
  });
}
</script>
@endsection
