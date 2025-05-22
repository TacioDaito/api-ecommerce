<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function accessOrder(User $user, Order $order): bool
    {
        return $user->id === $order->user_id;
    }

    public function createOrder(User $user, $request_user_id): bool
    {
        return $user->id === $request_user_id;
    }
}
