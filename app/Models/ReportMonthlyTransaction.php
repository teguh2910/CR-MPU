<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportMonthlyTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'part_number_id',
        'qty_budget',
        'qty_forecast',
        'source_type',
        'remarks',
        'month',
        'year',
        'created_by',
    ];

    protected $casts = [
        'qty_budget'  => 'integer',
        'qty_forecast' => 'integer',
        'year'        => 'integer',
    ];

    public function partNumber(): BelongsTo
    {
        return $this->belongsTo(PartNumber::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
