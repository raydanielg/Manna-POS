<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Get the business-scoped user ID (owner for staff, self for owner).
     */
    protected function currentBusinessId(): int
    {
        return auth()->user()->businessId();
    }

    /**
     * Ensure a model belongs to the current business user.
     */
    protected function ensureOwns(Model $model, string $column = 'created_by'): void
    {
        $bizId = $this->currentBusinessId();
        $ownerId = $model->getAttribute($column);
        if ($ownerId && $ownerId != $bizId) {
            abort(403, 'Unauthorized');
        }
    }
}
