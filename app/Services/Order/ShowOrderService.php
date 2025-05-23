<?php

namespace App\Services\Order;

use App\Models\Order;
use App\Models\UserClaim;

class ShowOrderService
{
    public function handle(Order $order, string $currentFolder = ''): array
    {
        $files = $order->files()
            ->where('path', $currentFolder)
            ->with('userClaims')
            ->get();

        $subFolders = $order->files()
            ->where('path', 'LIKE', $currentFolder ? "$currentFolder/%" : '%')
            ->get()
            ->pluck('path')
            ->map(function ($path) use ($currentFolder) {
                $relative = trim(str_replace($currentFolder, '', $path), '/');
                return explode('/', $relative)[0] ?? null;
            })
            ->unique()
            ->filter()
            ->values();

        $claim = UserClaim::where('order_id', $order->id)->first();

        return compact('files', 'subFolders', 'currentFolder', 'claim');
    }
}

