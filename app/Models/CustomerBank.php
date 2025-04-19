<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerBank extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id',
        'bank_id',
        'rek_no',
        'rek_name',
        'is_default',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_default' => 'boolean',
    ];

    /**
     * Get the customer that owns the bank account.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the bank that the account belongs to.
     */
    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
    public function banks()
{
    return $this->hasMany(CustomerBank::class);
}

/**
 * Get the default bank account for the customer.
 */
public function defaultBank()
{
    return $this->hasOne(CustomerBank::class)->where('is_default', true);
}
}