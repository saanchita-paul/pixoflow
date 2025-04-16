<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\UserClaim;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function orderProgress(Order $order)
    {
        $claims = UserClaim::with(['user', 'file'])
            ->where('order_id', $order->id)
            ->get()
            ->groupBy('user_id');

        return view('admin.order-progress', compact('order', 'claims'));
    }

}
