<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class AdminCacheController extends Controller
{
    public function manage()
    {
        return view('admin.cache.manage');
    }

    public function list()
    {
        try {
            return response()->json([
                'opcache_enabled' => function_exists('opcache_get_status') && ($status = opcache_get_status(false)) && $status['opcache_enabled'],
                'app_cached' => app()->configurationIsCached(),
                'routes_cached' => app()->routesAreCached(),
                'events_cached' => file_exists(base_path('bootstrap/cache/events.php')),
                'views_cached' => count(glob(storage_path('framework/views/*.php'))) > 0,
                'cache_total_size' => $this->getCacheSize(),
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function clear(Request $req)
    {
        try {
            $type = $req->type ?? 'all';
            switch ($type) {
                case 'app':
                    Artisan::call('cache:clear');
                    break;
                case 'views':
                    Artisan::call('view:clear');
                    break;
                case 'config':
                    Artisan::call('config:clear');
                    break;
                case 'routes':
                    Artisan::call('route:clear');
                    break;
                case 'events':
                    Artisan::call('event:clear');
                    break;
                case 'all':
                    Artisan::call('cache:clear');
                    Artisan::call('view:clear');
                    Artisan::call('config:clear');
                    Artisan::call('route:clear');
                    Artisan::call('event:clear');
                    break;
                default:
                    return response()->json(['message' => 'Invalid cache type'], 400);
            }
            return response()->json(['success' => true, 'message' => ucfirst($type) . ' cache cleared successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function optimize()
    {
        try {
            Artisan::call('optimize');
            return response()->json(['success' => true, 'message' => 'Application optimized successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    private function getCacheSize()
    {
        $path = storage_path('framework/cache/data');
        if (!is_dir($path)) return '0 B';
        $size = 0;
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS));
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
