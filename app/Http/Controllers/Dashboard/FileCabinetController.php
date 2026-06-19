<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\FileCabinet;
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
        $query = FileCabinet::where('user_id', auth()->id());
        if ($req->category) $query->where('category', $req->category);
        if ($req->search) $query->where('title', 'like', '%' . $req->search . '%');
        $files = $query->latest()->paginate(24)->withQueryString();
        $categories = FileCabinet::where('user_id', auth()->id())->distinct()->pluck('category')->filter();
        $stats = [
            'total' => FileCabinet::where('user_id', auth()->id())->count(),
            'total_size' => FileCabinet::where('user_id', auth()->id())->sum('file_size'),
        ];
        return view('dashboard.file-cabinet.index', compact('files', 'categories', 'stats'));
    }

    public function store(Request $req)
    {
        $data = $req->validate([
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:50',
            'file' => 'required|file|max:51200|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif,webp,zip,rar,7z,txt,csv', // 50MB
        ]);

        try {
            $uploaded = $req->file('file');
            $path = $uploaded->store('file-cabinet/' . auth()->id(), 'public');

            $file = FileCabinet::create([
                'user_id' => auth()->id(),
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'category' => $data['category'] ?? 'General',
                'file_path' => $path,
                'file_name' => $uploaded->getClientOriginalName(),
                'file_type' => $uploaded->getMimeType(),
                'file_extension' => strtolower($uploaded->getClientOriginalExtension()),
                'file_size' => $uploaded->getSize(),
            ]);

            Log::info('File uploaded', ['user_id' => auth()->id(), 'file_id' => $file->id, 'size' => $uploaded->getSize()]);
            if ($req->ajax() || $req->wantsJson()) {
                return $this->jsonSuccess('File uploaded successfully', ['file' => $file]);
            }
            return redirect()->route('dashboard.file-cabinet')->with('success', 'File uploaded successfully');
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('File upload failed', ['error' => $e->getMessage()]);
            if ($req->ajax() || $req->wantsJson()) {
                return $this->jsonError('Upload failed: ' . $e->getMessage(), 500);
            }
            return redirect()->route('dashboard.file-cabinet')->with('error', 'Upload failed');
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
