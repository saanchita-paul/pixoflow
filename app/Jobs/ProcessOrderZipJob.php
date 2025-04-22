<?php

namespace App\Jobs;

use App\Models\File;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use ZipArchive;

class ProcessOrderZipJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;
    protected $zipFilePath;

    public function __construct(Order $order, $zipFilePath)
    {
        $this->order = $order;
        $this->zipFilePath = $zipFilePath;
    }

    public function handle()
    {
        $zip = new ZipArchive;
        if ($zip->open($this->zipFilePath) === TRUE) {
            $folderName = Str::slug($this->order->title) . '-' . $this->order->id;
            $extractPath = storage_path("app/public/orders/$folderName");

            if (!file_exists($extractPath)) {
                mkdir($extractPath, 0755, true);
            }

            $zip->extractTo($extractPath);
            $zip->close();

            $this->storeFiles($extractPath, $this->order->id, '');

            unlink($this->zipFilePath);

            $this->order->update([
                'status' => Order::STATUS_PENDING,
            ]);
        }
    }

    private function storeFiles($directory, $orderId, $relativePath = '')
    {
        foreach (scandir($directory) as $item) {
            if ($item === '.' || $item === '..') continue;

            $fullPath = $directory . DIRECTORY_SEPARATOR . $item;
            $newRelativePath = $relativePath ? "$relativePath/$item" : $item;

            if (is_dir($fullPath)) {
                $this->storeFiles($fullPath, $orderId, $newRelativePath);
            } elseif (is_file($fullPath)) {
                File::create([
                    'order_id' => $orderId,
                    'name' => $item,
                    'path' => $relativePath,
                ]);

            }
        }
    }
}

