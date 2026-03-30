<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rfq extends Model
{
    use HasFactory;

    protected $fillable = [
        'to_company',
        'to_pic',
        'rfq_date',
        'from_company',
        'from_department',
        'from_pic',
        'tel',
        'email',
        'model',
        'customer',
        'product_name',
        'standard_qty',
        'drawing_timing',
        'ots_target',
        'otop_target',
        'sop',
        'target_note',
        'quotation_due_date',
        'delivery_location',
        'price_incoterm',
        'tooling_payment_method',
        'raw_material_period',
        'material_type',
        'material_cps_price',
        'exchange_period',
        'status',
        'created_by',
    ];

    protected $casts = [
        'rfq_date' => 'date',
        'ots_target' => 'date',
        'otop_target' => 'date',
        'quotation_due_date' => 'date',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(RfqItem::class)->orderBy('sort_order');
    }

    public function exchangeRates(): HasMany
    {
        return $this->hasMany(RfqExchangeRate::class)->orderBy('sort_order');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
