<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessOrderZipJob;
use App\Models\File;
use App\Models\Order;
use App\Models\UserClaim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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

        return view('orders.show', compact('order', 'files', 'subFolders', 'currentFolder','claim'));
    }

    public function claimFiles(Request $request, Order $order)
    {
        Log::info('Claim Files Request', [
            'user' => auth()->id(),
            'order_id' => $order->id,
            'file_ids' => $request->file_ids,
            'folder_paths' => $request->folder_paths,
        ]);

        $validated = $request->validate([
            'file_ids' => 'array',
            'file_ids.*' => 'exists:files,id',
            'folder_paths' => 'array',
            'folder_paths.*' => 'string',
        ]);

        $userId = auth()->id();


        if (!empty($validated['file_ids'])) {
            $claimedByOthers = UserClaim::whereIn('file_id', $validated['file_ids'])
                ->where('order_id', $order->id)
                ->where('user_id', '!=', $userId)
                ->pluck('file_id')
                ->toArray();

            foreach ($validated['file_ids'] as $fileId) {
                if (!in_array($fileId, $claimedByOthers)) {
                    UserClaim::firstOrCreate([
                        'user_id' => $userId,
                        'order_id' => $order->id,
                        'file_id' => $fileId,
                    ]);
                }
            }
        }

        if (!empty($validated['folder_paths'])) {
            foreach ($validated['folder_paths'] as $folderPath) {
                $files = $order->files()
                    ->where('path', $folderPath)
                    ->get();

                if ($files->isEmpty()) {
                    continue;
                }

                $fileIds = $files->pluck('id')->toArray();
                $claimedByOthers = UserClaim::whereIn('file_id', $fileIds)
                    ->where('order_id', $order->id)
                    ->where('user_id', '!=', $userId)
                    ->pluck('file_id')
                    ->toArray();

                foreach ($files as $file) {
                    if (!in_array($file->id, $claimedByOthers)) {
                        UserClaim::firstOrCreate([
                            'user_id' => $userId,
                            'order_id' => $order->id,
                            'file_id' => $file->id,
                        ]);
                    }
                }
            }
        }

        return back()->with('success', 'Files and folders successfully claimed.');
    }


    public function updateStatus(Request $request, UserClaim $claim)
    {
        $request->validate([
            'file_id' => 'required|exists:files,id',
            'status' => 'required|in:claimed,in_progress,completed',
        ]);

        if ($claim->file_id != $request->file_id || $claim->user_id != auth()->id()) {
            return response()->json(['message' => 'You haven’t claimed this file'], 403);
        }

        $claim->update(['status' => $request->status]);

        return back()->with('success', 'Status updated!');
    }



}

