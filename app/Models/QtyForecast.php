<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QtyForecast extends Model
{
    use HasFactory;

    protected $fillable = [
        'update_qty_month_id',
        'part_number_id',
        'month',
        'qty',
        'year',
    ];

    protected $casts = [
        'qty' => 'integer',
        'year' => 'integer',
    ];

    public function updateQtyMonth(): BelongsTo
    {
        return $this->belongsTo(UpdateQtyMonth::class);
    }

    public function partNumber(): BelongsTo
    {
        return $this->belongsTo(PartNumber::class);
    }
}
