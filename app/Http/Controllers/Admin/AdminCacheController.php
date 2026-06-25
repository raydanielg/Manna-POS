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
            $sizes = $this->getAllCacheSizes();
            return response()->json([
                'opcache_enabled' => function_exists('opcache_get_status') && ($status = opcache_get_status(false)) && $status['opcache_enabled'],
                'app_cached' => app()->configurationIsCached(),
                'routes_cached' => app()->routesAreCached(),
                'events_cached' => file_exists(base_path('bootstrap/cache/events.php')),
                'views_cached' => count(glob(storage_path('framework/views/*.php'))) > 0,
                'cache_total_size' => $this->formatBytes($sizes['total']),
                'cache_total_bytes' => $sizes['total'],
                'items' => [
                    'app' => [
                        'label' => 'Application Cache',
                        'cached' => app()->configurationIsCached(),
                        'bytes' => $sizes['app'],
                        'size' => $this->formatBytes($sizes['app']),
                        'icon' => 'storage',
                        'color' => '#7c3aed',
                    ],
                    'views' => [
                        'label' => 'View Cache',
                        'cached' => count(glob(storage_path('framework/views/*.php'))) > 0,
                        'bytes' => $sizes['views'],
                        'size' => $this->formatBytes($sizes['views']),
                        'icon' => 'visibility',
                        'color' => '#2563eb',
                    ],
                    'config' => [
                        'label' => 'Config Cache',
                        'cached' => app()->configurationIsCached(),
                        'bytes' => $sizes['config'],
                        'size' => $this->formatBytes($sizes['config']),
                        'icon' => 'settings',
                        'color' => '#059669',
                    ],
                    'routes' => [
                        'label' => 'Route Cache',
                        'cached' => app()->routesAreCached(),
                        'bytes' => $sizes['routes'],
                        'size' => $this->formatBytes($sizes['routes']),
                        'icon' => 'route',
                        'color' => '#d97706',
                    ],
                    'events' => [
                        'label' => 'Event Cache',
                        'cached' => file_exists(base_path('bootstrap/cache/events.php')),
                        'bytes' => $sizes['events'],
                        'size' => $this->formatBytes($sizes['events']),
                        'icon' => 'event',
                        'color' => '#dc2626',
                    ],
                    'sessions' => [
                        'label' => 'Session Data',
                        'cached' => true,
                        'bytes' => $sizes['sessions'],
                        'size' => $this->formatBytes($sizes['sessions']),
                        'icon' => 'session',
                        'color' => '#0891b2',
                    ],
                    'logs' => [
                        'label' => 'Log Files',
                        'cached' => $sizes['logs'] > 0,
                        'bytes' => $sizes['logs'],
                        'size' => $this->formatBytes($sizes['logs']),
                        'icon' => 'description',
                        'color' => '#64748b',
                    ],
                ],
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
                case 'view':
                    Artisan::call('view:clear');
                    break;
                case 'config':
                    Artisan::call('config:clear');
                    break;
                case 'routes':
                case 'route':
                    Artisan::call('route:clear');
                    break;
                case 'events':
                case 'event':
                    Artisan::call('event:clear');
                    break;
                case 'sessions':
                    $this->clearSessions();
                    break;
                case 'logs':
                    $this->clearLogs();
                    break;
                case 'all':
                    Artisan::call('cache:clear');
                    Artisan::call('view:clear');
                    Artisan::call('config:clear');
                    Artisan::call('route:clear');
                    Artisan::call('event:clear');
                    $this->clearSessions();
                    $this->clearLogs();
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
