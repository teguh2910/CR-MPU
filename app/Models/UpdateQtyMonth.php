<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UpdateQtyMonth extends Model
{
    use HasFactory;

    protected $fillable = [
        'month',
        'year',
    ];

    protected $casts = [
        'year' => 'integer',
    ];

    public function qtyForecasts(): HasMany
    {
        return $this->hasMany(QtyForecast::class);
    }
}
