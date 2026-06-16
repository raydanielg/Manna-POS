<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDatabaseController extends Controller
{
    public function index()
    {
        return view('admin.database.index');
    }

    public function tables()
    {
        try {
            $tables = [];
            $rows = DB::select("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name");
            foreach ($rows as $r) {
                $tables[] = [
                    'name' => $r->name,
                    'engine' => 'SQLite',
                    'rows' => DB::table($r->name)->count(),
                    'size' => $this->formatBytes(0),
                    'collation' => 'N/A',
                ];
            }
            return response()->json($tables);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function structure(Request $req, $table)
    {
        try {
            if (!DB::getSchemaBuilder()->hasTable($table)) {
                return response()->json(['message' => 'Table not found'], 404);
            }
            $columns = DB::select("PRAGMA table_info('{$table}')");
            return response()->json($columns);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function optimize($table)
    {
        try {
            if (!DB::getSchemaBuilder()->hasTable($table)) {
                return response()->json(['message' => 'Table not found'], 404);
            }
            DB::statement("VACUUM");
            return response()->json(['success' => true, 'message' => "Database optimized successfully."]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function query(Request $req)
    {
        try {
            $sql = $req->input('query');
            if (!$sql) return response()->json(['message' => 'Query is required'], 400);
            $lower = strtolower(trim($sql));
            if (!str_starts_with($lower, 'select')) {
                return response()->json(['message' => 'Only SELECT queries are allowed'], 403);
            }
            $results = DB::select($sql);
            return response()->json(['data' => $results, 'count' => count($results)]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
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
