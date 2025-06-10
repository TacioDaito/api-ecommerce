<?php
namespace App\Policies;
use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrderPolicy
{
    public function accessOrder(User $user, Order $order): Response
    {
        return ($user->id === $order->user_id
        || $user->role->name === 'admin')
        ? Response::allow()
        : Response::deny();
    }
}
