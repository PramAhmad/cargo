<?php

namespace App\Models;

use App\Enums\PaymentType;
use App\Enums\ShippingStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shipping extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'invoice',
        'invoice_file',
        'packagelist_file',
        'transaction_date',
        'receipt_date',
        'stuffing_date',
        'due_date',
        'payment_type',
        'top',
        'status',
        'description',
        'bank_id',
        'rek_no',
        'rek_name',
        'marking',
        'supplier',
        'sup_agent',
        'cukai',
        'tbh',
        'ppnbm',
        'freight',
        'do',
        'pfpd',
        'charge',
        'jkt_sda',
        'sda_user',
        'bkr',
        'asuransi',
        'nilai',
        'nilai_biaya',
        'biaya',
        'pph',
        'biaya_kirim',
        'ppn',
        'ppn_total',
        'pajak',
        'ctns_total',
        'qty_total',
        'cbm_total',
        'gw',
        'gw_total',
        'grand_total',
        'gw_total',
        'kg_price',
        'total_price_gw',
        'cbm_total',
        'cbm_price',
        'total_price_cbm',
        'mitra_id',
        'product_id',
        'warehouse_id',
        'customer_id',

    ];

    protected $casts = [
        'transaction_date' => 'date',
        'receipt_date' => 'date',
        'stuffing_date' => 'date',
        'due_date' => 'date',
        'payment_type' => PaymentType::class,
        'top' => 'integer',
        'status' => ShippingStatus::class,
        'bank_id' => 'integer',
        'sup_agent' => 'double',
        'cukai' => 'double',
        'tbh' => 'double',
        'ppnbm' => 'double',
        'freight' => 'double',
        'do' => 'double',
        'pfpd' => 'double',
        'charge' => 'double',
        'jkt_sda' => 'double',
        'sda_user' => 'double',
        'bkr' => 'double',
        'asuransi' => 'double',
        'nilai' => 'double',
        'nilai_biaya' => 'double',
        'biaya' => 'double',
        'pph' => 'double',
        'biaya_kirim' => 'double',
        'ppn' => 'double',
        'ppn_total' => 'double',
        'ctns_total' => 'double',
        'qty_total' => 'double',
        'cbm_total' => 'double',
        'gw' => 'double',
        'gw_total' => 'double',
        'grand_total' => 'double',
        'kg_price' => 'double',
        'total_price_gw' => 'double',
        'cbm_price' => 'double',
        'total_price_cbm' => 'double',
        'product_id' => 'integer',
        'mitra_id' => 'integer',
        'warehouse_id' => 'integer',
        'customer_id' => 'integer',
        'marketing_id' => 'integer',
    ];

    public function shippingDetails(): HasMany
    {
        return $this->hasMany(ShippingDetail::class);
    }
    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
    public function marketing(): BelongsTo
    {
        return $this->belongsTo(Marketing::class);
    }

    public function mitra(): BelongsTo
    {
        return $this->belongsTo(Mitra::class);
    }
    public function logs(): HasMany
    {
        return $this->hasMany(ShippingLog::class);
    }

}
