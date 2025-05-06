<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CountryWarehouse extends Model
{
    protected $table = 'country_warehouses';

    protected $fillable = [
        'warehouse_id',
        'name',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
