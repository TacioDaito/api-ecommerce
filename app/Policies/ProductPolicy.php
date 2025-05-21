<?php
namespace App\Policies;
use App\Models\User;

class ProductPolicy
{
    public function onlyAllowAdmin(User $user): bool
    {
        return $user->roles()->name === 'admin';
    }
}
