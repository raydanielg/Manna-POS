<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    public function handle(Request $request, Closure $next, ...$permissions)
    {
        $user = $request->user();

        if (!$user) {
            abort(401);
        }

        // Owners (no owner_id) have all permissions
        if ($user->isOwner()) {
            return $next($request);
        }

        foreach ($permissions as $permission) {
            if ($user->hasPermission($permission)) {
                return $next($request);
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Unauthorized. You do not have permission: ' . implode(', ', $permissions),
                'can_request_approval' => true,
                'required_permissions' => $permissions,
            ], 403);
        }

        abort(403, 'You do not have permission to perform this action.');
    }
}
