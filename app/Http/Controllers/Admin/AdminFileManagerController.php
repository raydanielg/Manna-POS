<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminFileManagerController extends Controller
{
    public function index()
    {
        return view('admin.file.index');
    }

    public function list(Request $req)
    {
        try {
            $path = $req->path ?? '';
            $base = storage_path('app');
            $fullPath = realpath($base . ($path ? DIRECTORY_SEPARATOR . $path : ''));
            if (!$fullPath || !str_starts_with($fullPath, $base)) {
                return response()->json(['message' => 'Invalid path'], 400);
            }
            $items = [];
            $dirs = glob($fullPath . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR);
            sort($dirs);
            foreach ($dirs as $dir) {
                $name = basename($dir);
                $items[] = [
                    'name' => $name,
                    'type' => 'folder',
                    'path' => ($path ? $path . '/' : '') . $name,
                    'size' => $this->getDirSize($dir),
                    'modified' => date('Y-m-d H:i:s', filemtime($dir)),
                ];
            }
            $files = glob($fullPath . DIRECTORY_SEPARATOR . '*.*');
            sort($files);
            foreach ($files as $file) {
                if (is_dir($file)) continue;
                $name = basename($file);
                $items[] = [
                    'name' => $name,
                    'type' => 'file',
                    'path' => ($path ? $path . '/' : '') . $name,
                    'size' => $this->formatBytes(filesize($file)),
                    'extension' => pathinfo($name, PATHINFO_EXTENSION),
                    'modified' => date('Y-m-d H:i:s', filemtime($file)),
                ];
            }
            $parentPath = $path ? dirname($path) : null;
            if ($parentPath === '.') $parentPath = '';
            return response()->json([
                'items' => $items,
                'current_path' => $path,
                'parent_path' => $parentPath,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function upload(Request $req)
    {
        try {
            $req->validate(['file' => 'required|file']);
            $path = $req->path ?? '';
            $dest = storage_path('app' . ($path ? DIRECTORY_SEPARATOR . $path : ''));
            if (!is_dir($dest)) {
                return response()->json(['message' => 'Directory not found'], 404);
            }
            $file = $req->file('file');
            $file->move($dest, $file->getClientOriginalName());
            return response()->json(['success' => true, 'message' => 'File uploaded successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function createFolder(Request $req)
    {
        try {
            $data = $req->validate(['name' => 'required|string|max:255']);
            $path = $req->path ?? '';
            $fullPath = storage_path('app' . ($path ? DIRECTORY_SEPARATOR . $path : '') . DIRECTORY_SEPARATOR . $data['name']);
            if (is_dir($fullPath)) {
                return response()->json(['message' => 'Folder already exists'], 409);
            }
            if (!mkdir($fullPath, 0755, true) && !is_dir($fullPath)) {
                return response()->json(['message' => 'Failed to create folder'], 500);
            }
            return response()->json(['success' => true, 'message' => 'Folder created successfully.'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function delete(Request $req)
    {
        try {
            $path = $req->input('path');
            if (!$path) return response()->json(['message' => 'Path is required'], 400);
            $fullPath = realpath(storage_path('app' . DIRECTORY_SEPARATOR . $path));
            $base = realpath(storage_path('app'));
            if (!$fullPath || !str_starts_with($fullPath, $base)) {
                return response()->json(['message' => 'Invalid path'], 400);
            }
            if (is_dir($fullPath)) {
                $this->rmdirRecursive($fullPath);
            } elseif (is_file($fullPath)) {
                unlink($fullPath);
            } else {
                return response()->json(['message' => 'Path not found'], 404);
            }
            return response()->json(['success' => true, 'message' => 'Deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function download($path)
    {
        try {
            $fullPath = realpath(storage_path('app' . DIRECTORY_SEPARATOR . $path));
            $base = realpath(storage_path('app'));
            if (!$fullPath || !str_starts_with($fullPath, $base) || !is_file($fullPath)) {
                return response()->json(['message' => 'File not found'], 404);
            }
            return response()->download($fullPath, basename($fullPath));
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    private function rmdirRecursive($dir)
    {
        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($items as $item) {
            $item->isDir() ? rmdir($item->getRealPath()) : unlink($item->getRealPath());
        }
        rmdir($dir);
    }

    private function getDirSize($dir)
    {
        $size = 0;
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS));
        foreach ($files as $file) $size += $file->getSize();
        return $this->formatBytes($size);
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        return round($bytes / pow(1024, $pow), $precision) . ' ' . $units[$pow];
    }
}
