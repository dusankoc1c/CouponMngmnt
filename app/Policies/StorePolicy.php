<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Store;
use App\Models\User;

class StorePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function workWith(User $user, Store $store): bool
    {
        if($user->hasRole('superadmin')){
            return true;
        }
        return $store->user->is($user);
    }

}
