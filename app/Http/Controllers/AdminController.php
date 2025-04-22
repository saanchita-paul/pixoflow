<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\UserClaim;
use App\Models\UserClaimLog;
use App\Services\OrderProgressService;
use App\Services\UserClaimLogService;

class AdminController extends Controller
{

    protected $orderProgressService;
    protected $userClaimLogService;

    public function __construct(
        OrderProgressService $orderProgressService,
        UserClaimLogService $userClaimLogService
    ) {
        $this->orderProgressService = $orderProgressService;
        $this->userClaimLogService = $userClaimLogService;
    }

    public function orderProgress(Order $order)
    {
        $claims = $this->orderProgressService->getProgressByOrder($order);
        return view('admin.order-progress', compact('order', 'claims'));
    }

    public function logs()
    {
        $logs = $this->userClaimLogService->getLatestLogs();
        return view('admin.logs', compact('logs'));
    }

}
