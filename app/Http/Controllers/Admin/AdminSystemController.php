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
            $q->where('action','like',"%{$req->search}%")
              ->orWhere('description','like',"%{$req->search}%");
        }
        if ($req->user_id) $q->where('user_id', $req->user_id);
        if ($req->action) $q->where('action', $req->action);
        return response()->json($q->latest()->paginate(50));
    }

    // System Configuration
    public function config()
    {
        return view('admin.system.config');
    }

    public function configList()
    {
        return response()->json(SystemConfig::orderBy('group')->orderBy('key')->get());
    }

    public function configUpdate(Request $req)
    {
        $data = $req->validate([
            'key' => 'required|string|max:191',
            'value' => 'nullable',
            'group' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);
        SystemConfig::setValue($data['key'], $data['value'], $data['group'] ?? 'general');
        return response()->json(['success'=>true]);
    }

    public function configDelete(SystemConfig $config)
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
        return response()->json(Backup::with('creator:id,name')->latest()->get());
    }

    public function backupsCreate(Request $req)
    {
        $data = $req->validate([
            'name' => 'required|string|max:191',
            'type' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
        ]);
        $data['created_by'] = auth()->id();
        $data['type'] = $data['type'] ?? 'manual';
        $data['file_path'] = 'backups/' . date('Y-m-d_H-i-s') . '_' . \Illuminate\Support\Str::slug($data['name']) . '.sql';
        $data['size'] = '0 B';
        $data['status'] = 'completed';
        $backup = Backup::create($data);
        return response()->json(['success'=>true,'backup'=>$backup], 201);
    }

    public function backupsDestroy(Backup $backup)
    {
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
        $storage = round(disk_free_space(base_path()) / 1073741824, 2) . ' GB free / ' . round(disk_total_space(base_path()) / 1073741824, 2) . ' GB total';
        return response()->json([
            'php_version' => phpversion(),
            'laravel_version' => app()->version(),
            'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'N/A',
            'database' => 'MySQL',
            'storage' => $storage,
            'memory_limit' => ini_get('memory_limit'),
            'max_upload' => ini_get('upload_max_filesize'),
            'max_execution' => ini_get('max_execution_time') . 's',
            'app_debug' => config('app.debug') ? 'Enabled' : 'Disabled',
            'app_env' => config('app.env'),
            'timezone' => config('app.timezone'),
        ]);
    }

    // Login History
    public function loginHistory()
    {
        return view('admin.system.login-history');
    }

    public function loginHistoryList(Request $req)
    {
        $q = ActivityLog::with('user:id,name')->where('action', 'login');
        if ($req->user_id) $q->where('user_id', $req->user_id);
        return response()->json($q->latest()->paginate(50));
    }
}
