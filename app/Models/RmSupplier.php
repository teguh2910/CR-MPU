<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RmSupplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'part_no_id',
        'period_from',
        'period_to',
        'rm_currency',
        'rm_basis_price',
        'rm_weight_gram',
    ];

    protected $casts = [
        'period_from' => 'date',
        'period_to' => 'date',
        'rm_basis_price' => 'decimal:4',
        'rm_weight_gram' => 'decimal:4',
    ];

    public function partNumber(): BelongsTo
    {
        return $this->belongsTo(PartNumber::class, 'part_no_id');
    }
}
