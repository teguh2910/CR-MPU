<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcessCostSupplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'part_no_id',
        'process_cost_total',
    ];

    protected $casts = [
        'process_cost_total' => 'decimal:4',
    ];

    public function partNumber(): BelongsTo
    {
        return $this->belongsTo(PartNumber::class, 'part_no_id');
    }
}
