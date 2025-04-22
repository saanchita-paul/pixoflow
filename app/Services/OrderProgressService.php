<?php

namespace App\Services;

use App\Models\Order;
use App\Models\UserClaim;

class OrderProgressService
{
    public function getProgressByOrder(Order $order)
    {
        return UserClaim::with(['user', 'file'])
            ->where('order_id', $order->id)
            ->get()
            ->groupBy('user_id');
    }
}
