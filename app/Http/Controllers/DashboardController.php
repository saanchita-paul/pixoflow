<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\UserClaim;

class DashboardController extends Controller
{
    public function index()
    {
        $orders = Order::with('files')->get();
        $users = User::with(['claims.file'])->get();

        $orderStats = $orders->map(function ($order) {
            $totalFiles = $order->files->count();
            $claimed = $order->files->filter(fn($file) => $file->claim)->count();
            $completed = $order->files->filter(fn($file) => optional($file->claim)->status === 'Completed')->count();
            $inProgress = $order->files->filter(fn($file) => optional($file->claim)->status === 'In Progress')->count();

            return [
                'order' => $order,
                'total' => $totalFiles,
                'claimed' => $claimed,
                'completed' => $completed,
                'inProgress' => $inProgress,
                'remaining' => $totalFiles - $claimed,
            ];
        });

        $userStats = $users->map(function ($user) {
            return [
                'name' => $user->name,
                'total' => $user->claims->count(),
                'completed' => $user->claims->where('status', 'Completed')->count(),
                'inProgress' => $user->claims->where('status', 'In Progress')->count(),
            ];
        });

        return view('admin.progress_dashboard', compact('orderStats', 'userStats'));
    }
}

