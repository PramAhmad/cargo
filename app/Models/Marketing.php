<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Marketing extends Model
{
    use HasFactory;

    protected $table = "marketings";
    protected $fillable = [
        "code",
        "name",
        "address",
        "atas_nama",
        "city",
        "phone1",
        "phone2",
        "borndate",
        "email",
        "website",
        "ktp",
        "npwp",
        "requirement",
        "address_tax",
        "due_date",
        "status",
        "bank_id",
        "marketing_group_id",
        "user_id",
        "no_rek",
        "status",
    ];

    public $timestamps = false;
    
    /**
     * Get the bank associated with the marketing.
     */    public function marketingGroup()
    {
        return $this->belongsTo(MarketingGroup::class);
    }

    /**
     * Get the bank associated with the marketing.
     */
    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    /**
     * Get the user associated with the marketing.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function banks()
{
    return $this->hasMany(MarketingBank::class);
}

/**
 * Get the default bank account for the marketing.
 */
public function defaultBank()
{
    return $this->hasOne(MarketingBank::class)->where('is_default', true);
}
}