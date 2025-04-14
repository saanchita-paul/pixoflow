<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public const STATUS_PENDING='pending';
    public const STATUS_IN_PROGRESS='in_progress';
    public const STATUS_COMPLETED='completed';

    protected $fillable = [
        'title',
        'description',
        'status',
        'created_by'
    ];
    public function files()
    {
        return $this->hasMany(File::class);
    }

}
