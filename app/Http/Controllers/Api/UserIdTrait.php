<?php
namespace App\Http\Controllers\Api;

trait UserIdTrait {
    /**
     * Get the effective user ID for data scoping.
     * Staff users see data created by their owner.
     */
    protected function userId(): int {
        $user = auth()->user();
        if (!$user) return 0;
        return $user->owner_id ?? $user->id;
    }
}
