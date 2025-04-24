<?php

namespace App\Http\Controllers;

use App\Events\FileClaimed;
use App\Http\Requests\ClaimFilesRequest;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateStatusRequest;
use App\Jobs\ProcessOrderZipJob;
use App\Models\File;
use App\Models\Order;
use App\Models\UserClaim;
use App\Models\UserClaimLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use ZipArchive;
use App\Services\Order\{
    CreateOrderService,
    ListOrdersService,
    ShowOrderService,
    ClaimFilesService,
    UpdateStatusService
};

class OrderController extends Controller
{
    public function index(ListOrdersService $service)
    {
        $orders = $service->handle();
        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        return view('orders.create');
    }

    public function store(StoreOrderRequest $request, CreateOrderService $service)
    {
        $service->handle(
            $request->input('title'),
            $request->input('description'),
            $request->file('zip_file'),
            auth()->id()
        );

        return redirect()->route('orders.index')
            ->with('success', 'Order created! Files are being processed. This may take a little time.');
    }

    public function show(Order $order, Request $request, ShowOrderService $service)
    {
        $folder = $request->query('folder', '');
        $data = $service->handle($order, $folder);

        return view('orders.show', array_merge(['order' => $order], $data));
    }


    public function claimFiles(ClaimFilesRequest $request, Order $order, ClaimFilesService $service)
    {
        $service->handle($request->validated(), $order);
        return back()->with('success', 'Files and folders successfully claimed.');
    }

    public function updateStatus(UpdateStatusRequest $request, UserClaim $claim, UpdateStatusService $service)
    {
        $validated = $request->validated();

        $service->handle($claim, $validated['file_id'], $validated['status']);

        return back()->with('success', 'Status updated!');
    }




}
