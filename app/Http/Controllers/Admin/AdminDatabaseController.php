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
            $db = config('database.connections.mysql.database');
            $tables = DB::select("
                SELECT
                    TABLE_NAME AS name,
                    ENGINE AS engine,
                    TABLE_ROWS AS rows,
                    ROUND((data_length + index_length) / 1024, 2) AS size_kb,
                    TABLE_COLLATION AS collation
                FROM information_schema.tables
                WHERE table_schema = ?
                ORDER BY TABLE_NAME
            ", [$db]);
            return response()->json(array_map(fn($t) => [
                'name' => $t->name,
                'engine' => $t->engine,
                'rows' => $t->rows ?? 0,
                'size' => $this->formatBytes(($t->size_kb ?? 0) * 1024),
                'collation' => $t->collation,
            ], $tables));
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
            $columns = DB::select("SHOW FULL COLUMNS FROM `{$table}`");
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
            DB::statement("OPTIMIZE TABLE `{$table}`");
            return response()->json(['success' => true, 'message' => "Table `{$table}` optimized successfully."]);
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
