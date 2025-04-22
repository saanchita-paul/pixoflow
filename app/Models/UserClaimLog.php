<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserClaimLog extends Model
{
    public const ACTION_ORDER_CREATED='order_created';
    public const ACTION_CLAIMED='claimed';
    public const ACTION_IN_PROGRESS='in_progress';
    public const ACTION_COMPLETED='completed';

    protected $fillable = [
        'user_id', 'order_id', 'file_id', 'action'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
    public function order() {
        return $this->belongsTo(Order::class);
    }
    public function file() {
        return $this->belongsTo(File::class);
    }
}
