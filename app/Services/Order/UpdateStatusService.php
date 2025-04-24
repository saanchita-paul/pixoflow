<?php
namespace App\Services\Order;

use App\Models\File;
use App\Models\User;
use App\Models\UserClaim;
use App\Models\UserClaimLog;
use App\Notifications\FileStatusUpdatedNotification;

class UpdateStatusService
{
    public function handle(UserClaim $claim, int $fileId, string $status): void
    {
        if ($claim->file_id !== $fileId || $claim->user_id !== auth()->id()) {
            abort(403, 'You haven’t claimed this file');
        }

        $claim->update(['status' => $status]);

        UserClaimLog::create([
            'user_id' => auth()->id(),
            'order_id' => $claim->order_id,
            'file_id' => $claim->file_id,
            'action' => $status,
        ]);

        $admins = User::where('role', 'admin')->get();
        $file = File::find($claim->file_id);

        if (auth()->user()->role !== 'admin') {
            foreach ($admins as $admin) {
                $admin->notify(new FileStatusUpdatedNotification($file, auth()->user(), $status));
            }
        }
    }
}

