<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MitraBank extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'mitra_id',
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
     * Get the mitra that owns the bank account.
     */
    public function mitra()
    {
        return $this->belongsTo(Mitra::class);
    }

    /**
     * Get the bank that the account belongs to.
     */
    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
}