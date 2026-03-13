<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'part_number_id',
        'cr_no',
        'activity',
        'year',
        'cr_satuan',
        'satuan',
        'plan_svp_month',
        'act_svp_month',
    ];

    protected $casts = [
        'year' => 'integer',
        'plan_svp_month' => 'date',
        'act_svp_month' => 'date',
    ];

    public function partNumber(): BelongsTo
    {
        return $this->belongsTo(PartNumber::class);
    }
}
