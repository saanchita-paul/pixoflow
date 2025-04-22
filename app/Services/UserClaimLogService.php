<?php

namespace App\Services;

use App\Models\UserClaimLog;

class UserClaimLogService
{
    public function getLatestLogs(int $perPage = 20)
    {
        return UserClaimLog::with(['user', 'file', 'order'])
            ->latest()
            ->paginate($perPage);
    }
}
