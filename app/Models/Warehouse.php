<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'address_photo',
        'type',
        'mitra_id',
    ];

    public function mitra()
    {
        return $this->belongsTo(Mitra::class);
    }
}