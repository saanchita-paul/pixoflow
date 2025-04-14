<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use ZipArchive;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::withCount('files')->latest()->get();
        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        return view('orders.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'zip_file' => 'required|file|mimes:zip',
        ]);

        $order = Order::create([
            'title' => $request->title,
            'description' => $request->description,
            'status' => 'pending',
            'created_by' => auth()->id(),
        ]);

        $zipFile = $request->file('zip_file');
        $zipFileName = $zipFile->getClientOriginalName();

        $tempPath = storage_path("app/temp");
        $zipFilePath = $tempPath . '/' . $zipFileName;
        $zipFile->move($tempPath, $zipFileName);

        $zip = new ZipArchive;
        if ($zip->open($zipFilePath) === TRUE) {
            // Slugify folder name and add order ID to avoid collision
            $folderName = Str::slug($request->title) . '-' . $order->id;
            $extractPath = storage_path("app/public/orders/$folderName");

            if (!file_exists($extractPath)) {
                mkdir($extractPath, 0755, true);
            }

            $zip->extractTo($extractPath);
            $zip->close();

            // Save extracted files recursively
            $this->storeFiles($extractPath, $order->id, '');

            // Remove temp ZIP file
            unlink($zipFilePath);

            return redirect()->route('orders.index')
                ->with('success', 'Order created and ZIP extracted successfully!');
        } else {
            return back()->withErrors(['zip_file' => 'Failed to open the ZIP file.']);
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

    public function show(Order $order, Request $request)
    {
        $currentFolder = $request->get('folder', '');

        $files = $order->files()
            ->where('path', $currentFolder)
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

        return view('orders.show', compact('order', 'files', 'subFolders', 'currentFolder'));
    }
}

