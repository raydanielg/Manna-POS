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
<div class="dash-content">
<div class="fc-wrap">

    <div class="fc-header">
        <div>
            <h1>File Cabinet</h1>
            <p>{{ number_format($stats['total']) }} files &middot; {{ $stats['folder_count'] }} folders</p>
        </div>
        <div class="fc-actions">
            <button class="btn btn-secondary" onclick="document.getElementById('folderModal').classList.add('open')">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                New Folder
            </button>
            <button class="btn btn-primary" onclick="triggerFileSelect()">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Upload
            </button>
        </div>
    </div>

    <div class="fc-breadcrumb">
        <a href="{{ route('dashboard.file-cabinet') }}">Files</a>
        @foreach($breadcrumb as $b)
        <span class="sep">/</span>
        <a href="{{ route('dashboard.file-cabinet', ['folder'=>$b->id]) }}">{{ $b->name }}</a>
        @endforeach
        @if($currentFolder)
        <span class="sep">/</span>
        <span class="current">{{ $currentFolder->name }}</span>
        @endif
    </div>

    <div class="fc-dropzone" id="dropzone" onclick="triggerFileSelect()"
        ondragover="event.preventDefault();this.classList.add('dragover')"
        ondragleave="this.classList.remove('dragover')"
        ondrop="handleDrop(event)">
        <svg class="dz-icon" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
        <div class="dz-title">Drag & drop files here, or click to browse</div>
        <div class="dz-sub">Max 50MB per file</div>
        <div class="fc-upload-progress" id="uploadProgress">
            <div class="progress-bar"><div class="progress-fill" id="progressFill"></div></div>
            <div class="progress-text" id="progressText">Uploading...</div>
        </div>
    </div>

    @if($folders->count())
    <div class="fc-section-title">Folders</div>
    <div class="fc-grid">
        @foreach($folders as $folder)
        <div class="fc-folder" onclick="location.href='{{ route('dashboard.file-cabinet', ['folder'=>$folder->id]) }}'">
            <div class="f-icon">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 21v-16a2 2 0 0 1 2-2h6l3 3h6a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H5z"/></svg>
            </div>
            <div class="f-info">
                <div class="f-name">{{ $folder->name }}</div>
                <div class="f-meta">{{ $folder->files_count }} files</div>
            </div>
            <div class="f-del" onclick="event.stopPropagation();confirmFolderDelete('{{ route('dashboard.file-cabinet.folders.destroy', $folder) }}')">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3M4 7h16"/></svg>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    @if($files->count())
    <div class="fc-section-title">Files</div>
    <div class="fc-grid">
        @foreach($files as $f)
        <div class="fc-file">
            <div class="fi-top">
                <div class="fi-icon {{ $f->icon }}">
                    @if($f->icon === 'image')
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 0 1 2.828 0L16 16m-2-2l1.586-1.586a2 2 0 0 1 2.828 0L20 14m-6-6h.01M6 20h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2z"/></svg>
                    @elseif($f->icon === 'pdf')
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 0 0 2-2V9.414a1 1 0 0 0-.293-.707l-5.414-5.414A1 1 0 0 0 12.586 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2z"/></svg>
                    @elseif($f->icon === 'doc')
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2z"/></svg>
                    @elseif($f->icon === 'xls')
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"/><path d="M5 17v-2a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"/><path d="M3 7a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><path d="M12 3v4"/></svg>
                    @else
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 0 0 2-2V9.414a1 1 0 0 0-.293-.707l-5.414-5.414A1 1 0 0 0 12.586 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2z"/></svg>
                    @endif
                </div>
                <div class="fi-info">
                    <div class="fi-name" title="{{ $f->title }}">{{ $f->title }}</div>
                    <div class="fi-meta">{{ strtoupper($f->file_extension) }} &middot; {{ $f->file_size_formatted }} &middot; {{ $f->created_at->format('M d') }}</div>
                </div>
            </div>
            <div class="fi-actions">
                <a href="{{ route('dashboard.file-cabinet.download', $f) }}" class="btn btn-edit btn-sm">Download</a>
                <button type="button" class="btn btn-delete btn-sm" onclick="confirmDelete('{{ route('dashboard.file-cabinet.destroy', $f) }}')">Delete</button>
            </div>
        </div>
        @endforeach
    </div>
    @if($files->hasPages())
    <div class="fc-pagination">{{ $files->onEachSide(1)->links() }}</div>
    @endif
    @elseif(!$folders->count())
    <div class="fc-empty">
        <svg class="fe-icon" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 21v-16a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16l-3-2l-2 2l-2-2l-2 2l-2-2l-3 2"/><path d="M9 9l2 2l4-4"/></svg>
        <div class="fe-title">This folder is empty</div>
        <div class="fe-desc">Drag files here or click Upload to add documents</div>
    </div>
    @endif

</div>
</div>

<input type="file" id="ajaxFileInput" style="display:none;" multiple onchange="handleFiles(this.files)">

<div class="modal-overlay" id="folderModal" onclick="if(event.target===this)this.classList.remove('open')">
    <div class="modal-box fc-modal-box" onclick="event.stopPropagation()">
        <div class="modal-header"><h3 class="modal-title">New Folder</h3><button class="modal-close" onclick="document.getElementById('folderModal').classList.remove('open')">&times;</button></div>
        <form method="POST" action="{{ route('dashboard.file-cabinet.folders.store') }}">@csrf
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Folder Name</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Invoices" required autofocus>
                </div>
                <input type="hidden" name="parent_id" value="{{ $currentFolder?->id }}">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('folderModal').classList.remove('open')">Cancel</button>
                <button type="submit" class="btn btn-primary">Create</button>
            </div>
        </form>
    </div>
</div>

<form method="POST" id="deleteForm" style="display:none;">@csrf @method('DELETE')</form>
<form method="POST" id="folderDeleteForm" style="display:none;">@csrf @method('DELETE')</form>

<script>
function triggerFileSelect() { document.getElementById('ajaxFileInput').click(); }
function handleDrop(e) { e.preventDefault(); document.getElementById('dropzone').classList.remove('dragover'); handleFiles(e.dataTransfer.files); }
function handleFiles(files) { if (!files.length) return; Array.from(files).forEach(f => uploadFile(f)); }
function uploadFile(file) {
    const p = document.getElementById('uploadProgress'), fill = document.getElementById('progressFill'), txt = document.getElementById('progressText');
    p.classList.add('active'); fill.style.width = '0%'; txt.textContent = 'Uploading ' + file.name + '...';
    const fd = new FormData();
    fd.append('file', file); fd.append('title', file.name.replace(/\.[^/.]+$/, '')); fd.append('_token', '{{ csrf_token() }}'); fd.append('folder_id', '{{ $currentFolder?->id }}');
    const xhr = new XMLHttpRequest();
    xhr.upload.addEventListener('progress', e => { if (e.lengthComputable) { const pct = Math.round((e.loaded/e.total)*100); fill.style.width = pct+'%'; txt.textContent = 'Uploading '+file.name+' ('+pct+'%)'; }});
    xhr.addEventListener('load', () => { if (xhr.status===200) { const r=JSON.parse(xhr.responseText); if(r.success){txt.textContent=file.name+' uploaded!'; setTimeout(()=>{p.classList.remove('active'); fill.style.width='0%'; location.reload();},800);} else {txt.textContent='Error'; Swal.fire({icon:'error',title:'Upload failed',text:r.message});}} else {txt.textContent='Failed'; Swal.fire({icon:'error',title:'Upload failed'});}});
    xhr.addEventListener('error', () => { txt.textContent='Failed'; Swal.fire({icon:'error',title:'Upload failed'}); });
    xhr.open('POST','{{ route('dashboard.file-cabinet.store') }}'); xhr.send(fd);
}
function confirmDelete(url) {
    Swal.fire({title:'Delete file?',text:'This cannot be undone.',icon:'warning',showCancelButton:true,confirmButtonText:'Delete',cancelButtonText:'Cancel',confirmButtonColor:'#ef4444',reverseButtons:true})
    .then(r=>{if(r.isConfirmed){const f=document.getElementById('deleteForm');f.action=url;f.submit();}});
}
function confirmFolderDelete(url) {
    Swal.fire({title:'Delete folder?',text:'All files inside will be deleted too.',icon:'warning',showCancelButton:true,confirmButtonText:'Delete',cancelButtonText:'Cancel',confirmButtonColor:'#ef4444',reverseButtons:true})
    .then(r=>{if(r.isConfirmed){const f=document.getElementById('folderDeleteForm');f.action=url;f.submit();}});
}
</script>
@endsection
