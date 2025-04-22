<?php

namespace App\Services\Order;

use App\Jobs\ProcessOrderZipJob;
use App\Models\Order;
use App\Models\UserClaimLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CreateOrderService
{
    public function handle(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'zip_file' => 'required|file|mimes:zip',
        ]);

        $order = Order::create([
            'title' => $request->title,
            'description' => $request->description,
            'status' => Order::STATUS_PROCESSING,
            'created_by' => auth()->id(),
        ]);

        UserClaimLog::create([
            'user_id' => auth()->id(),
            'order_id' => $order->id,
            'file_id' => null,
            'action' => UserClaimLog::ACTION_ORDER_CREATED,
        ]);

        $zipFile = $request->file('zip_file');
        $zipFileName = Str::random(10) . '_' . $zipFile->getClientOriginalName();
        $tempPath = storage_path("app/temp");

        if (!file_exists($tempPath)) {
            mkdir($tempPath, 0755, true);
        }

        $zipFilePath = $tempPath . '/' . $zipFileName;
        $zipFile->move($tempPath, $zipFileName);

        ProcessOrderZipJob::dispatch($order, $zipFilePath);

        return $order;
    }
}
