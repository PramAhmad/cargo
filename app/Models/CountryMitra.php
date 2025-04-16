<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CountryMitra extends Model
{
    protected $table = 'country_mitra';

    protected $fillable = [
        'mitra_id',
        'name',
    ];

    public function mitra()
    {
        return $this->belongsTo(Mitra::class);
    }
}
