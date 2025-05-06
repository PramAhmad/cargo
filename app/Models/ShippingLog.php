<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingLog extends Model
{
    protected $fillable = [
        'shipping_id',
        'user_id',
        'status',
        'notes',
    ];

    public function shipping()
    {
        return $this->belongsTo(Shipping::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
