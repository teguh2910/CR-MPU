<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OtherCostSupplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'part_no_id',
        'remark',
        'cost',
    ];

    protected $casts = [
        'cost' => 'decimal:4',
    ];

    public function partNumber(): BelongsTo
    {
        return $this->belongsTo(PartNumber::class, 'part_no_id');
    }
}
