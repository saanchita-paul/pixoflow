<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserClaim extends Model
{
    public const STATUS_CLAIME ='claimed';
    public const STATUS_IN_PROGRESS ='in_progress';
    public const STATUS_COMPLETED ='completed';

    protected $fillable = [
        'user_id',
        'order_id',
        'file_id',
        'status'
    ];

    public function file()
    {
        return $this->belongsTo(File::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
