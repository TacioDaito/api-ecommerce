<?php
namespace App\Policies;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AdminPolicy
{
    public function onlyAllowAdmin(User $user): Response
    {
        return $user->role->name === 'admin'
        ? Response::allow()
        : Response::denyWithStatus(404, 'Route not found.');
    }
}
