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

    public function countries()
    {
        return $this->hasOne(CountryWarehouse::class);
    }
    public function mitra()
    {
        return $this->belongsTo(Mitra::class);
    }
    
    public function products()
    {
        return $this->hasMany(Product::class);
    }
    
    // Method untuk menghitung total produk
    public function getProductsCountAttribute()
    {
        return $this->products()->count();
    }
  
}