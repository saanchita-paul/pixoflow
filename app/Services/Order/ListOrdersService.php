<?php

namespace App\Services\Order;

use App\Models\Order;

class ListOrdersService
{
    public function handle()
    {
        return Order::withCount('files')->latest()->get();
    }
}
