<?php

namespace App\Services\Order;

use App\Models\File;
use App\Models\Order;
use App\Models\User;
use App\Models\UserClaim;
use App\Models\UserClaimLog;
use App\Notifications\FileClaimedNotification;
use Illuminate\Support\Facades\Log;

class ClaimFilesService
{
    public function handle(array $validated, Order $order): void
    {
        Log::info('Claim Files Request', [
            'user' => auth()->id(),
            'order_id' => $order->id,
            'file_ids' => $validated['file_ids'] ?? [],
            'folder_paths' => $validated['folder_paths'] ?? [],
        ]);

        $userId = auth()->id();
        $admins = User::where('role', 'admin')->get();

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

                UserClaimLog::create([
                    'user_id' => $userId,
                    'order_id' => $order->id,
                    'file_id' => $fileId,
                    'action' => UserClaimLog::ACTION_CLAIMED,
                ]);

                $file = File::find($fileId);
                if (auth()->user()->role !== 'admin') {
                    foreach ($admins as $admin) {
                        $admin->notify(new FileClaimedNotification($file, auth()->user()));
                    }
                }
            }
        }

        if (!empty($validated['folder_paths'] ?? [])) {
            foreach ($validated['folder_paths'] as $folderPath) {
                $files = $order->files()->where('path', $folderPath)->get();

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

                        UserClaimLog::create([
                            'user_id' => $userId,
                            'order_id' => $order->id,
                            'file_id' => $file->id,
                            'action' => UserClaimLog::ACTION_CLAIMED,
                        ]);

                        if (auth()->user()->role !== 'admin') {
                            foreach ($admins as $admin) {
                                $admin->notify(new FileClaimedNotification($file, auth()->user()));
                            }
                        }
                    }
                }
            }
        }
    }
}
