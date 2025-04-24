<?php

namespace App\Services\Order;

use App\Jobs\ProcessOrderZipJob;
use App\Models\Order;
use App\Models\UserClaimLog;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class CreateOrderService
{
    public function handle(string $title, ?string $description, UploadedFile $zipFile, int $userId): Order
    {
        $order = Order::create([
            'title' => $title,
            'description' => $description,
            'status' => Order::STATUS_PROCESSING,
            'created_by' => $userId,
        ]);

        UserClaimLog::create([
            'user_id' => $userId,
            'order_id' => $order->id,
            'file_id' => null,
            'action' => UserClaimLog::ACTION_ORDER_CREATED,
        ]);

        $zipFileName = Str::random(10) . '_' . $zipFile->getClientOriginalName();
        $tempPath = storage_path('app/temp');

        if (!file_exists($tempPath)) {
            mkdir($tempPath, 0755, true);
        }

        $zipFilePath = $tempPath . '/' . $zipFileName;
        $zipFile->move($tempPath, $zipFileName);

        ProcessOrderZipJob::dispatch($order, $zipFilePath);

        return $order;
    }
}

