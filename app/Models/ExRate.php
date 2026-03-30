<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'currency',
        'period_from',
        'period_to',
        'rate',
    ];

    protected $casts = [
        'period_from' => 'date',
        'period_to' => 'date',
        'rate' => 'decimal:6',
    ];
}
