<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingDetail extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        // Relasi
        'shipping_id',
        'product_id',
        
        
        'ctn',
        'qty_per_ctn',
        'ctns',
        'qty',
        
        // Dimensi dan berat
        'length',
        'width',
        'high',
        'volume',
        'gw_per_ctn',
        'total_gw',
        
        // Gambar dan deskripsi
        'product_image',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'qty_per_ctn' => 'float',
        'ctns' => 'float',
        'qty' => 'float',
        'length' => 'float',
        'width' => 'float',
        'high' => 'float',
        'volume' => 'float',
        'gw_per_ctn' => 'float',
        'total_gw' => 'float',
        'price_kg' => 'float',
        'price_cbm' => 'float',
    ];

    /**
     * Get the shipping that owns the shipping detail.
     */
    public function shipping()
    {
        return $this->belongsTo(Shipping::class);
    }

    /**
     * Get the product that owns the shipping detail.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Calculate total volume based on dimensions and carton quantity
     */
    public function calculateVolume()
    {
        if ($this->length && $this->width && $this->high && $this->ctns) {
            // Convert cm to m and calculate volume in cubic meters
            $this->volume = ($this->length * $this->width * $this->high / 1000000) * $this->ctns;
            return $this->volume;
        }
        return 0;
    }

    /**
     * Calculate total weight based on GW per carton and carton quantity
     */
    public function calculateTotalGW()
    {
        if ($this->gw_per_ctn && $this->ctns) {
            $this->total_gw = $this->gw_per_ctn * $this->ctns;
            return $this->total_gw;
        }
        return 0;
    }

    /**
     * Calculate total quantity based on quantity per carton and carton quantity
     */
    public function calculateTotalQty()
    {
        if ($this->qty_per_ctn && $this->ctns) {
            $this->qty = $this->qty_per_ctn * $this->ctns;
            return $this->qty;
        }
        return 0;
    }
}