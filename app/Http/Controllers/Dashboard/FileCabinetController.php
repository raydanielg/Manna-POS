<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\FileCabinet;
use App\Models\FileCabinetFolder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class FileCabinetController extends Controller
{
    public function __construct() { $this->middleware('auth'); }

    private function jsonSuccess($message, $data = [])
    {
        return response()->json(array_merge(['success' => true, 'message' => $message], $data));
    }

    private function jsonError($message, $code = 422)
    {
        return response()->json(['success' => false, 'message' => $message], $code);
    }

    public function index(Request $req)
    {
        $folderId = $req->folder;
        $currentFolder = $folderId ? FileCabinetFolder::where('user_id', auth()->id())->find($folderId) : null;

        // Folders in current directory
        $folders = FileCabinetFolder::where('user_id', auth()->id())
            ->where('parent_id', $folderId ?: null)
            ->withCount('files')
            ->orderBy('name')
            ->get();

        // Files in current directory
        $fileQuery = FileCabinet::where('user_id', auth()->id())->where('folder_id', $folderId ?: null);
        if ($req->search) $fileQuery->where('title', 'like', '%' . $req->search . '%');
        $files = $fileQuery->latest()->paginate(24)->withQueryString();

        $stats = [
            'total' => FileCabinet::where('user_id', auth()->id())->count(),
            'total_size' => FileCabinet::where('user_id', auth()->id())->sum('file_size'),
            'folder_count' => FileCabinetFolder::where('user_id', auth()->id())->count(),
        ];

        // Breadcrumb
        $breadcrumb = [];
        if ($currentFolder) {
            $f = $currentFolder;
            while ($f) {
                $breadcrumb[] = $f;
                $f = $f->parent;
            }
            $breadcrumb = array_reverse($breadcrumb);
        }

        return view('dashboard.file-cabinet.index', compact('folders', 'files', 'stats', 'currentFolder', 'breadcrumb'));
    }

    public function storeFolder(Request $req)
    {
        $data = $req->validate([
            'name' => 'required|string|max:100',
            'parent_id' => 'nullable|exists:file_cabinet_folders,id',
        ]);
        $data['user_id'] = auth()->id();
        $folder = FileCabinetFolder::create($data);
        return redirect()->route('dashboard.file-cabinet', ['folder' => $folder->parent_id])->with('success', 'Folder created');
    }

    public function destroyFolder(FileCabinetFolder $folder)
    {
        if ($folder->user_id !== auth()->id()) abort(403);
        $parentId = $folder->parent_id;
        $folder->files()->delete();
        $folder->children()->delete();
        $folder->delete();
        return redirect()->route('dashboard.file-cabinet', ['folder' => $parentId])->with('success', 'Folder deleted');
    }

    public function store(Request $req)
    {
        $data = $req->validate([
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'folder_id' => 'nullable|exists:file_cabinet_folders,id',
            'file' => 'required|file|max:51200|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif,webp,zip,rar,7z,txt,csv',
        ]);

        try {
            $uploaded = $req->file('file');
            $path = $uploaded->store('file-cabinet/' . auth()->id(), 'public');

            $file = FileCabinet::create([
                'user_id' => auth()->id(),
                'folder_id' => $data['folder_id'] ?? null,
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'category' => 'General',
                'file_path' => $path,
                'file_name' => $uploaded->getClientOriginalName(),
                'file_type' => $uploaded->getMimeType(),
                'file_extension' => strtolower($uploaded->getClientOriginalExtension()),
                'file_size' => $uploaded->getSize(),
            ]);

            Log::info('File uploaded', ['user_id' => auth()->id(), 'file_id' => $file->id]);
            if ($req->ajax() || $req->wantsJson()) {
                return $this->jsonSuccess('File uploaded', ['file' => $file->load('folder')]);
            }
            return redirect()->route('dashboard.file-cabinet', ['folder' => $data['folder_id'] ?? null])->with('success', 'File uploaded');
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('File upload failed', ['error' => $e->getMessage()]);
            if ($req->ajax() || $req->wantsJson()) {
                return $this->jsonError('Upload failed: ' . $e->getMessage(), 500);
            }
            return redirect()->back()->with('error', 'Upload failed');
        }
    }

    public function download(FileCabinet $file)
    {
        $this->guardFile($file);
        return Storage::disk('public')->download($file->file_path, $file->file_name);
    }

    public function destroy(FileCabinet $file)
    {
        $this->guardFile($file);
        try {
            Storage::disk('public')->delete($file->file_path);
            $file->delete();
            Log::info('File deleted', ['user_id' => auth()->id(), 'file_id' => $file->id]);
            if (request()->ajax() || request()->wantsJson()) {
                return $this->jsonSuccess('File deleted');
            }
            return redirect()->route('dashboard.file-cabinet')->with('success', 'File deleted');
        } catch (\Exception $e) {
            Log::error('File delete failed', ['error' => $e->getMessage()]);
            if (request()->ajax() || request()->wantsJson()) {
                return $this->jsonError('Delete failed', 500);
            }
            return redirect()->route('dashboard.file-cabinet')->with('error', 'Delete failed');
        }
    }

    private function guardFile($file)
    {
        if ($file->user_id !== auth()->id()) abort(403);
    }
}
