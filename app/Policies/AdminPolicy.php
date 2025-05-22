<?php
namespace App\Policies;
use App\Models\User;

class AdminPolicy
{
    public function onlyAllowAdmin(User $user): bool
    {
        return $user->role->name === 'admin';
    }
}
