<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessOrderZipJob;
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
            'status' => Order::STATUS_PROCESSING,
            'created_by' => auth()->id(),
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

        return redirect()->route('orders.index')
            ->with('success', 'Order created! Files are being processed. This may take a little time.');
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

