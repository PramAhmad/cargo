<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoryProduct extends Model
{
    protected $fillable = [
        'name',
        'mitra_id',
        'mit_price_cbm_sea',
        'mit_price_kg_sea',
        'cust_price_cbm_sea',
        'cust_price_kg_sea',
        'mit_price_cbm_air',
        'mit_price_kg_air',
        'cust_price_cbm_air',
        'cust_price_kg_air',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'mit_price_cbm_sea' => 'double',
        'mit_price_kg_sea' => 'double',
        'cust_price_cbm_sea' => 'double',
        'cust_price_kg_sea' => 'double',
        'mit_price_cbm_air' => 'double',
        'mit_price_kg_air' => 'double',
        'cust_price_cbm_air' => 'double',
        'cust_price_kg_air' => 'double',
    ];

    /**
     * Get the mitra that owns the category
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mitra(): BelongsTo
    {
        return $this->belongsTo(Mitra::class);
    }

    /**
     * Get the products for this category
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
    
    /**
     * Get formatted display name including mitra
     *
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        if ($this->mitra) {
            return "{$this->name} ({$this->mitra->name})";
        }
        return $this->name;
    }
    
    /**
     * Get price for a specific mode (sea/air) and type (mit/cust)
     *
     * @param string $mode 'sea' or 'air'
     * @param string $type 'mit' or 'cust'
     * @param string $unit 'cbm' or 'kg'
     * @return double
     */
    public function getPriceForMode(string $mode, string $type, string $unit): float
    {
        $column = "{$type}_price_{$unit}_{$mode}";
        return $this->$column ?? 0;
    }
}