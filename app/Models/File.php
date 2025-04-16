<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = [
        'order_id',
        'name',
        'path',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function userClaims()
    {
        return $this->hasMany(UserClaim::class);
    }


}
