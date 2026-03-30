<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FohProfiySupplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'part_no_id',
        'percentage',
        'remarks',
    ];

    protected $casts = [
        'percentage' => 'decimal:4',
    ];

    public function partNumber(): BelongsTo
    {
        return $this->belongsTo(PartNumber::class, 'part_no_id');
    }
}
