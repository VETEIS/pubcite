<?php

namespace App\Policies;

use App\Models\Signature;
use App\Models\User;

class SignaturePolicy
{
    /**
     * Determine whether the user can view the signature.
     */
    public function view(User $user, Signature $signature): bool
    {
        return $user->id === $signature->user_id;
    }

    /**
     * Determine whether the user can update the signature.
     */
    public function update(User $user, Signature $signature): bool
    {
        return $user->id === $signature->user_id;
    }

    /**
     * Determine whether the user can delete the signature.
     */
    public function delete(User $user, Signature $signature): bool
    {
        return $user->id === $signature->user_id;
    }
}
