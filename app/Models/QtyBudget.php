<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QtyBudget extends Model
{
    use HasFactory;

    protected $fillable = [
        'part_number_id',
        'qty',
        'year',
        'month',
    ];

    protected $casts = [
        'qty' => 'integer',
        'year' => 'integer',
    ];

    public function partNumber(): BelongsTo
    {
        return $this->belongsTo(PartNumber::class);
    }
}
