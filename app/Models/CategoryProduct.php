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
        'mit_price_cbm',
        'mit_price_kg',
        'cust_price_cbm',
        'cust_price_kg',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'mit_price_cbm' => 'double',
        'mit_price_kg' => 'double',
        'cust_price_cbm' => 'double',
        'cust_price_kg' => 'double',
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
}
