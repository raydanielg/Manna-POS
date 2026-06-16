<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\SystemConfig;
use App\Models\Backup;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class AdminSystemController extends Controller
{
    // Activity Logs
    public function activityLogs()
    {
        return view('admin.system.activity-logs');
    }

    public function activityLogsList(Request $req)
    {
        $q = ActivityLog::with('user:id,name');
        if ($req->search) {
            $q->where(function($q) use ($req) {
                $q->where('action','like',"%{$req->search}%")
                  ->orWhere('description','like',"%{$req->search}%")
                  ->orWhere('ip_address','like',"%{$req->search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name','like',"%{$req->search}%"));
            });
        }
        if ($req->user_id) $q->where('user_id', $req->user_id);
        if ($req->event) $q->where('action', $req->event);
        $logs = $q->latest()->paginate(50)->through(function($log) {
            return [
                'id' => $log->id,
                'user_name' => $log->user?->name,
                'event' => $log->action,
                'description' => $log->description,
                'ip_address' => $log->ip_address,
                'user_agent' => $log->user_agent,
                'created_at' => $log->created_at,
            ];
        });
        return response()->json($logs);
    }

    public function activityLogsClear()
    {
        ActivityLog::truncate();
        return response()->json(['success'=>true]);
    }

    // System Configuration
    public function config()
    {
        return view('admin.system.config');
    }

    public function configList()
    {
        return response()->json(SystemConfig::orderBy('group')->orderBy('key')->get()->map(fn($c) => [
            'id' => $c->id,
            'key' => $c->key,
            'value' => $c->value,
            'group' => $c->group,
            'type' => $c->type,
            'description' => $c->description,
        ]));
    }

    public function configStore(Request $req)
    {
        $data = $req->validate([
            'key' => 'required|string|max:191|unique:system_configs,key',
            'value' => 'nullable|string',
            'group' => 'nullable|string|max:50',
            'type' => 'nullable|string|max:20',
        ]);
        $config = SystemConfig::create([
            'key' => $data['key'],
            'value' => $data['value'] ?? '',
            'group' => $data['group'] ?? 'general',
            'type' => $data['type'] ?? 'text',
        ]);
        return response()->json(['success'=>true,'config'=>$config], 201);
    }

    public function configShow(SystemConfig $config)
    {
        return response()->json($config);
    }

    public function configUpdate(Request $req, SystemConfig $config)
    {
        $data = $req->validate([
            'key' => "required|string|max:191|unique:system_configs,key,{$config->id}",
            'value' => 'nullable|string',
            'group' => 'nullable|string|max:50',
            'type' => 'nullable|string|max:20',
        ]);
        $config->update($data);
        return response()->json(['success'=>true,'config'=>$config]);
    }

    public function configDestroy(SystemConfig $config)
    {
        $config->delete();
        return response()->json(['success'=>true]);
    }

    // Backups
    public function backups()
    {
        return view('admin.system.backups');
    }

    public function backupsList()
    {
        return response()->json(Backup::with('creator:id,name')->latest()->get()->map(fn($b) => [
            'id' => $b->id,
            'filename' => $b->file_path ? basename($b->file_path) : $b->name,
            'name' => $b->name,
            'size' => $b->size ?? '0 B',
            'status' => $b->status,
            'created_at' => $b->created_at,
            'created_by' => $b->creator?->name,
        ]));
    }

    public function backupsCreate(Request $req)
    {
        $data = $req->validate([
            'name' => 'nullable|string|max:191',
        ]);
        $name = $data['name'] ?? ('backup_' . date('Y-m-d_H-i-s'));
        $backup = Backup::create([
            'name' => $name,
            'created_by' => auth()->id(),
            'type' => 'manual',
            'file_path' => 'backups/' . date('Y-m-d_H-i-s') . '_' . \Illuminate\Support\Str::slug($name) . '.sql',
            'size' => '0 B',
            'status' => 'completed',
        ]);
        return response()->json(['success'=>true,'backup'=>$backup], 201);
    }

    public function backupsDownload(Backup $backup)
    {
        $path = storage_path('app/' . $backup->file_path);
        if (!file_exists($path)) {
            return response()->json(['message'=>'Backup file not found'], 404);
        }
        return response()->download($path, basename($backup->file_path));
    }

    public function backupsDestroy(Backup $backup)
    {
        $path = storage_path('app/' . $backup->file_path);
        if (file_exists($path)) unlink($path);
        $backup->delete();
        return response()->json(['success'=>true,'message'=>'Backup deleted']);
    }

    // System Health
    public function health()
    {
        return view('admin.system.health');
    }

    public function healthData()
    {
        $storageWritable = is_writable(storage_path());
        $debugMode = config('app.debug');
        $env = config('app.env');
        return response()->json([
            'php_version' => phpversion(),
            'laravel_version' => app()->version(),
            'db_connection' => config('database.default'),
            'db_size' => $this->getDbSize(),
            'server_uptime' => function_exists('exec') ? @exec('uptime -p') ?: 'N/A' : 'N/A',
            'memory_usage' => round(memory_get_usage(true) / 1048576, 2) . ' MB',
            'storage_writable' => $storageWritable,
            'environment' => $env,
            'debug_mode' => $debugMode,
            'schedule_running' => $this->isScheduleRunning(),
            'queue_worker' => $this->getQueueWorkerStatus(),
            'cache_accessible' => cache()->has('app.health') || cache()->put('app.health', true, 60),
        ]);
    }

    private function getDbSize()
    {
        try {
            $db = config('database.connections.mysql.database');
            $size = \DB::select("SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb FROM information_schema.tables WHERE table_schema = ?", [$db]);
            return ($size[0]->size_mb ?? 0) . ' MB';
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }

    private function isScheduleRunning()
    {
        try {
            $last = \DB::table('activity_logs')->where('action', 'schedule.run')->latest()->first();
            return $last && $last->created_at->gt(now()->subMinutes(5));
        } catch (\Exception $e) {
            return false;
        }
    }

    private function getQueueWorkerStatus()
    {
        try {
            $last = \DB::table('activity_logs')->where('action', 'queue.work')->latest()->first();
            return $last && $last->created_at->gt(now()->subMinutes(5)) ? 'Running' : 'Not Running';
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }

    // Login History
    public function loginHistory()
    {
        return view('admin.system.login-history');
    }

    public function loginHistoryList(Request $req)
    {
        $q = ActivityLog::with('user:id,name,email')->where('action', 'login');
        if ($req->search) {
            $q->where(function($q) use ($req) {
                $q->where('ip_address','like',"%{$req->search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name','like',"%{$req->search}%")->orWhere('email','like',"%{$req->search}%"));
            });
        }
        if ($req->user_id) $q->where('user_id', $req->user_id);
        $logs = $q->latest()->paginate(50)->through(function($log) {
            return [
                'id' => $log->id,
                'user_name' => $log->user?->name,
                'email' => $log->user?->email,
                'ip_address' => $log->ip_address,
                'user_agent' => $log->user_agent,
                'success' => true,
                'created_at' => $log->created_at,
            ];
        });
        return response()->json($logs);
    }

    public function loginHistoryClear()
    {
        ActivityLog::where('action', 'login')->delete();
        return response()->json(['success'=>true]);
    }
}
