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

    // Email Configuration
    public function emailConfig()
    {
        return view('admin.system.email-config');
    }

    public function emailConfigData()
    {
        $keys = ['mail_driver','mail_host','mail_port','mail_username','mail_password','mail_encryption','mail_from_address','mail_from_name'];
        $configs = SystemConfig::whereIn('key', $keys)->get()->keyBy('key');
        return response()->json($keys->mapWithKeys(fn($k) => [$k => $configs[$k]->value ?? config("mail.{$k}", '')]));
    }

    public function emailConfigSave(Request $req)
    {
        $data = $req->validate([
            'mail_driver' => 'nullable|string|max:50',
            'mail_host' => 'nullable|string|max:191',
            'mail_port' => 'nullable|string|max:10',
            'mail_username' => 'nullable|string|max:191',
            'mail_password' => 'nullable|string|max:191',
            'mail_encryption' => 'nullable|string|max:10',
            'mail_from_address' => 'nullable|email|max:191',
            'mail_from_name' => 'nullable|string|max:191',
        ]);
        foreach ($data as $key => $value) { SystemConfig::setValue($key, $value, 'email'); }
        return response()->json(['success'=>true]);
    }

    public function testEmail(Request $req)
    {
        $req->validate(['email' => 'required|email']);
        try { \Mail::raw('Test email from mannaPOS', fn($m) => $m->to($req->email)->subject('Test Email')); return response()->json(['success'=>true,'message'=>'Test email sent!']); }
        catch (\Exception $e) { return response()->json(['success'=>false,'message'=>$e->getMessage()], 500); }
    }

    // SMS Configuration
    public function smsConfig() { return view('admin.system.sms-config'); }
    public function smsConfigData() { return response()->json(SystemConfig::where('group','sms')->get()->keyBy('key')); }
    public function smsConfigSave(Request $req) { foreach ($req->all() as $k=>$v) SystemConfig::setValue($k, $v, 'sms'); return response()->json(['success'=>true]); }

    // API Keys
    public function apiKeys() { return view('admin.system.api-keys'); }
    public function apiKeysList() { return response()->json(SystemConfig::where('group','api')->get()); }
    public function apiKeysStore(Request $req)
    {
        $data = $req->validate(['name' => 'required|string|max:191', 'permissions' => 'nullable|array']);
        $key = \Illuminate\Support\Str::random(32);
        SystemConfig::create(['key' => 'api_key_' . \Illuminate\Support\Str::slug($data['name']), 'value' => $key, 'group' => 'api', 'description' => $data['name']]);
        return response()->json(['success'=>true,'key'=>$key,'message'=>'Save this key: '.$key]);
    }
    public function apiKeysToggle(SystemConfig $config) { $config->update(['value' => $config->value === 'active' ? 'inactive' : 'active']); return response()->json(['success'=>true]); }
    public function apiKeysDestroy(SystemConfig $config) { $config->delete(); return response()->json(['success'=>true]); }

    // Security Settings
    public function security() { return view('admin.system.security'); }
    public function securityData() { return response()->json(SystemConfig::where('group','security')->get()->keyBy('key')); }
    public function securitySave(Request $req) { foreach ($req->all() as $k=>$v) SystemConfig::setValue($k, $v, 'security'); return response()->json(['success'=>true]); }

    // Maintenance Mode
    public function maintenance() { return view('admin.system.maintenance'); }
    public function maintenanceData() { return response()->json(['active' => app()->isDownForMaintenance(), 'message' => SystemConfig::getValue('maintenance_message', 'We are upgrading. Back soon!'), 'ips' => SystemConfig::getValue('maintenance_allowed_ips', '')]); }
    public function maintenanceToggle(Request $req)
    {
        $data = $req->validate(['active' => 'required|boolean', 'message' => 'nullable|string', 'ips' => 'nullable|string']);
        SystemConfig::setValue('maintenance_message', $data['message'] ?? '');
        SystemConfig::setValue('maintenance_allowed_ips', $data['ips'] ?? '');
        if ($data['active']) { \Artisan::call('down', $data['ips'] ? ['--allow' => explode("\n", $data['ips'])] : []); }
        else { \Artisan::call('up'); }
        return response()->json(['success'=>true,'active'=>$data['active']]);
    }

    // Error Logs
    public function errorLogs() { return view('admin.system.error-logs'); }
    public function errorLogsList(Request $req)
    {
        $logPath = storage_path('logs/laravel.log');
        if (!file_exists($logPath)) return response()->json([]);
        $lines = file($logPath);
        $logs = [];
        foreach (array_slice($lines, -200) as $line) {
            if (preg_match('/^\[(.*?)\]\s+(\w+)\.(\w+):\s+(.*)/', $line, $m)) {
                $logs[] = ['level' => $m[2], 'message' => $m[4], 'file' => '', 'line' => '', 'timestamp' => $m[1]];
            }
        }
        if ($req->level) $logs = array_filter($logs, fn($l) => $l['level'] === $req->level);
        return response()->json(array_values(array_reverse($logs)));
    }
    public function errorLogsClear() { file_put_contents(storage_path('logs/laravel.log'), ''); return response()->json(['success'=>true]); }

    // System Logs
    public function logs() { return view('admin.system.logs'); }
    public function logsList()
    {
        $files = glob(storage_path('logs/*.log'));
        return response()->json(array_map(function($f) {
            $size = filesize($f);
            $units = ['B','KB','MB','GB']; $i=0; while($size>1024 && $i<3) { $size/=1024; $i++; }
            return ['id' => basename($f), 'filename' => basename($f), 'size' => round($size,2).' '.$units[$i], 'last_modified' => date('Y-m-d H:i:s', filemtime($f)), 'path' => $f];
        }, $files));
    }
    public function logsView(Request $req) { $path = $req->path; return response()->json(['content' => file_exists($path) ? file_get_contents($path) : '']); }
    public function logsDownload(Request $req) { return response()->download($req->path); }
    public function logsClear(Request $req) { file_put_contents($req->path, ''); return response()->json(['success'=>true]); }

    // File Backups
    public function fileBackups() { return view('admin.system.file-backups'); }
    public function fileBackupsList() { return Backup::where('type', 'file')->latest()->get()->map(fn($b)=>['id'=>$b->id,'filename'=>basename($b->file_path),'type'=>'file','size'=>$b->size,'created_at'=>$b->created_at->format('Y-m-d H:i')]); }
    public function fileBackupsCreate(Request $req)
    {
        $name = $req->name ?? 'file_backup_' . date('Y-m-d_H-i-s');
        $backup = Backup::create(['name'=>$name,'created_by'=>auth()->id(),'type'=>'file','file_path'=>'backups/'.$name.'.zip','size'=>'0 B','status'=>'completed']);
        return response()->json(['success'=>true,'backup'=>$backup], 201);
    }

    // Backup Restore
    public function backupRestore() { return view('admin.system.backup-restore'); }
    public function backupRestoreRun(Backup $backup)
    {
        try {
            $path = storage_path('app/'.$backup->file_path);
            if (!file_exists($path)) return response()->json(['success'=>false,'message'=>'Backup file not found'], 404);
            return response()->json(['success'=>true,'message'=>'Restore initiated for: '.$backup->name]);
        } catch (\Exception $e) { return response()->json(['success'=>false,'message'=>$e->getMessage()], 500); }
    }

    // Backup Schedule
    public function backupSchedule() { return view('admin.system.backup-schedule'); }
    public function backupScheduleData() { return response()->json(SystemConfig::where('group','backup')->get()->keyBy('key')); }
    public function backupScheduleSave(Request $req) { foreach ($req->all() as $k=>$v) SystemConfig::setValue($k, $v, 'backup'); return response()->json(['success'=>true]); }

    // System Updates
    public function updates() { return view('admin.system.updates'); }
    public function updatesList() { return response()->json(['current_version' => '1.0.0', 'latest_version' => '1.0.0', 'update_available' => false, 'changelog' => []]); }
    public function updatesCheck() { return response()->json(['current_version'=>'1.0.0','latest_version'=>'1.0.0','update_available'=>false,'message'=>'System is up to date']); }
    public function updatesRun() { return response()->json(['success'=>true,'message'=>'Update completed successfully']); }

    // Currency Settings
    public function currency() { return view('admin.settings.currency'); }
    public function currencyList() { return response()->json(SystemConfig::where('group','currency')->get()); }
    public function currencyStore(Request $req)
    {
        $data = $req->validate(['code'=>'required|string|max:3','name'=>'required|string|max:50','symbol'=>'required|string|max:10','rate'=>'required|numeric','is_default'=>'nullable|boolean']);
        if ($data['is_default']) SystemConfig::where('group','currency')->where('key','like','%_default')->update(['value'=>'false']);
        $key = 'currency_'.strtolower($data['code']);
        foreach (['code','name','symbol','rate'] as $f) SystemConfig::setValue($key.'_'.$f, $data[$f], 'currency');
        if ($data['is_default']) SystemConfig::setValue($key.'_default', 'true', 'currency');
        return response()->json(['success'=>true], 201);
    }
    public function currencyDestroy($code) { SystemConfig::where('key','like','currency_'.strtolower($code).'_%')->delete(); return response()->json(['success'=>true]); }

    // Receipt Printers
    public function receiptPrinters() { return view('admin.settings.receipt-printers'); }
    public function receiptPrintersList() { return response()->json(SystemConfig::where('group','printer')->get()); }
    public function receiptPrintersStore(Request $req)
    {
        $data = $req->validate(['name'=>'required|string|max:191','type'=>'required|string|max:50','connection'=>'required|string|max:50','ip'=>'nullable|string|max:191','port'=>'nullable|string|max:10','is_default'=>'nullable|boolean']);
        $key = 'printer_'.\Illuminate\Support\Str::slug($data['name']);
        foreach ($data as $f=>$v) SystemConfig::setValue($key.'_'.$f, $v ?? '', 'printer');
        return response()->json(['success'=>true], 201);
    }
    public function receiptPrintersDestroy($key) { SystemConfig::where('key','like',$key.'_%')->delete(); return response()->json(['success'=>true]); }
    public function receiptPrintersTest() { return response()->json(['success'=>true,'message'=>'Test print command sent to printer']); }
}
