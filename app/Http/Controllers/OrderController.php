<?php

namespace App\Http\Controllers;

use App\Events\FileClaimed;
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

    public function store(Request $request, CreateOrderService $service)
    {
        $service->handle($request);
        return redirect()->route('orders.index')->with('success', 'Order created! Files are being processed. This may take a little time.');
    }

    public function show(Order $order, Request $request, ShowOrderService $service)
    {
        $data = $service->handle($order, $request);
        return view('orders.show', array_merge(['order' => $order], $data));
    }

    public function claimFiles(Request $request, Order $order, ClaimFilesService $service)
    {
        $service->handle($request, $order);
        return back()->with('success', 'Files and folders successfully claimed.');
    }


    public function updateStatus(Request $request, UserClaim $claim, UpdateStatusService $service)
    {
        $service->handle($request, $claim);
        return back()->with('success', 'Status updated!');
    }



}

