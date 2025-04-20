<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'name',
        'mit_price_cbm',
        'mit_price_kg',
        'cust_price_cbm',
        'cust_price_kg',
        'parent_id',
        'warehouse_id',
        'category_product_id',
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

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Get the parent that owns the Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Product::class, 'parent_id');
    }
    
    /**
     * Get the category that the product belongs to
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(CategoryProduct::class, 'category_product_id');
    }
    
    public function getLabelAttribute()
    {
        return $this->warehouse->name . ' (' . $this->warehouse->type . ') : ' . $this->name;
    }
    
    public function getLevelAttribute()
    {
        $level = 0;
        $parentId = $this->parent_id;
        while ($parentId !== null) {
            $level++;
            $parentId = Product::find($parentId)->parent_id;
        }
        return $level;
    }

    /**
     * Get the combined parent name and name attribute.
     *
     * @return string
     */
    public function getParentNameAttribute()
    {
        if ($this->parent) {
            return $this->parent->name . ' (' . $this->name . ')';
        }
        return $this->name;
    }
}