<?php
namespace App\Policies;
use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrderPolicy
{
    public function accessOrder(User $user, Order $order): Response
    {
        return $user->id === $order->user_id ?
        Response::allow() :
        Response::denyWithStatus(404, 'Order not found.');
    }

    public function createOrder(User $user, $request_user_id): Response
    {
        return $user->id === $request_user_id ?
        Response::allow() :
        Response::denyWithStatus(403, 'You do not have permission to create this order.');
    }
}
