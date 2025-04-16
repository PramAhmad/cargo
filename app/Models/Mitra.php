<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mitra extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'bank_id',
        'mitra_group_id',
        'user_id',
        'code',
        'name',
        'address_office_indo',
        'no_rek',
        'atas_nama',
        'phone1',
        'phone2',
        'email',
        'website',
        'birthdate',
        'borndate',
        'created_date',
        'ktp',
        'npwp',
        'tax_address',
        'syarat_bayar',
        'batas_tempo',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'birthdate' => 'date',
        'borndate' => 'date',
        'created_date' => 'date',
        'status' => 'boolean',
    ];

    /**
     * Get the bank associated with the mitra.
     */
    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    /**
     * Get the mitra group associated with the mitra.
     */
    public function mitraGroup()
    {
        return $this->belongsTo(MitraGroup::class);
    }

    /**
     * Get the user associated with the mitra.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate a unique mitra code.
     *
     * @return string
     */
    public static function generateMitraCode()
    {
        $prefix = 'MTR';
        $lastMitra = self::orderBy('id', 'desc')->first();
        
        if (!$lastMitra) {
            $number = 1;
        } else {
            $code = $lastMitra->code;
            if (preg_match('/^' . $prefix . '(\d+)$/', $code, $matches)) {
                $number = (int)$matches[1] + 1;
            } else {
                $number = 1;
            }
        }
        
        return $prefix . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Scope a query to only include active mitras.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Get the formatted full address.
     *
     * @return string
     */
    public function getFullAddressAttribute()
    {
        return $this->address_office_indo ?? '-';
    }
    
    /**
     * Get the formatted payment terms.
     *
     * @return string
     */
    public function getPaymentTermsAttribute()
    {
        return $this->syarat_bayar > 0 ? $this->syarat_bayar . ' days' : 'Cash';
    }
    
    /**
     * Get the formatted due date terms.
     *
     * @return string
     */
    public function getDueTermsAttribute()
    {
        return $this->batas_tempo > 0 ? $this->batas_tempo . ' days' : 'N/A';
    }
}