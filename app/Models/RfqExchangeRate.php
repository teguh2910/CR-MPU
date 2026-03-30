<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RfqExchangeRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'rfq_id',
        'currency',
        'rate',
        'sort_order',
    ];

    public function rfq(): BelongsTo
    {
        return $this->belongsTo(Rfq::class);
    }
}
