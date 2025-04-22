<?php
namespace App\Services\Order;

use App\Events\FileClaimed;
use App\Models\UserClaim;
use App\Models\UserClaimLog;
use Illuminate\Http\Request;

class UpdateStatusService
{
    public function handle(Request $request, UserClaim $claim)
    {
        $request->validate([
            'file_id' => 'required|exists:files,id',
            'status' => 'required|in:claimed,in_progress,completed',
        ]);

        if ($claim->file_id != $request->file_id || $claim->user_id != auth()->id()) {
            abort(403, 'You haven’t claimed this file');
        }

        $claim->update(['status' => $request->status]);

        UserClaimLog::create([
            'user_id' => auth()->id(),
            'order_id' => $claim->order_id,
            'file_id' => $claim->file_id,
            'action' => $request->status,
        ]);

        event(new FileClaimed($claim->file, auth()->user()));
    }
}
