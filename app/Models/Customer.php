<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'marketing_id',
        'bank_id',
        'customer_group_id',
        'customer_category_id',
        'code',
        'type',
        'phone1',
        'phone2',
        'name',
        'status',
        'country',
        'city',
        'borndate',
        'street1',
        'street2',
        'street_item',
        'email',
        'website',
        'created_date',
        'npwp',
        'tax_address',
        'users_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'borndate' => 'date',
        'created_date' => 'date',
    ];

    /**
     * Get the marketing that owns the customer.
     */
    public function marketing()
    {
        return $this->belongsTo(Marketing::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    /**
     * Get the bank associated with the customer.
     */
    public function banks()
    {
        return $this->hasMany(CustomerBank::class);
    }

    /**
     * Get the customer group that owns the customer.
     */
    public function customerGroup()
    {
        return $this->belongsTo(CustomerGroup::class);
    }

    /**
     * Get the customer category that owns the customer.
     */
    public function customerCategory()
    {
        return $this->belongsTo(CategoryCustomer::class, 'customer_category_id');
    }

    /**
     * Get orders for the customer.
     */
  

    /**
     * Get the full address of the customer.
     *
     * @return string
     */
    public function getFullAddressAttribute()
    {
        $address = [];
        
        if ($this->street1) {
            $address[] = $this->street1;
        }
        
        if ($this->street2) {
            $address[] = $this->street2;
        }
        
        if ($this->city) {
            $address[] = $this->city;
        }
        
        if ($this->country) {
            $address[] = $this->country;
        }
        
        return implode(', ', $address);
    }

    /**
     * Scope a query to only include active customers.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include customers of a specific type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to only include customers of a specific marketing.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $marketingId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByMarketing($query, $marketingId)
    {
        return $query->where('marketing_id', $marketingId);
    }

    /**
     * Generate a new customer code.
     *
     * @return int
     */
    public static function generateCustomerCode()
    {
        $lastCustomer = self::orderBy('id', 'desc')->first();
        
        if (!$lastCustomer) {
            return 10001;
        }
        
        return $lastCustomer->code + 1;
    }
}
