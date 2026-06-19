<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\FileCabinet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileCabinetController extends Controller
{
    public function __construct() { $this->middleware('auth'); }

    public function index(Request $req)
    {
        $query = FileCabinet::where('user_id', auth()->id());
        if ($req->category) $query->where('category', $req->category);
        if ($req->search) $query->where('title', 'like', '%' . $req->search . '%');
        $files = $query->latest()->get();
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
            'file' => 'required|file|max:51200', // 50MB
        ]);

        $uploaded = $req->file('file');
        $path = $uploaded->store('file-cabinet/' . auth()->id(), 'public');

        FileCabinet::create([
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

        return redirect()->route('dashboard.file-cabinet')->with('success', 'File uploaded successfully');
    }

    public function download(FileCabinet $file)
    {
        $this->authorize($file);
        return Storage::disk('public')->download($file->file_path, $file->file_name);
    }

    public function destroy(FileCabinet $file)
    {
        $this->authorize($file);
        Storage::disk('public')->delete($file->file_path);
        $file->delete();
        return redirect()->route('dashboard.file-cabinet')->with('success', 'File deleted');
    }

    private function authorize($file)
    {
        if ($file->user_id !== auth()->id()) abort(403);
    }
}
