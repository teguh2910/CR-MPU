<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ToolingSupplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'part_no_id',
        'tooling_price',
        'depre_per_pcs',
        'status',
    ];

    protected $casts = [
        'tooling_price' => 'decimal:4',
        'depre_per_pcs' => 'decimal:6',
    ];

    public function partNumber(): BelongsTo
    {
        return $this->belongsTo(PartNumber::class, 'part_no_id');
    }
}
