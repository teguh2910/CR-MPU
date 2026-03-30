<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartNumber extends Model
{
    use HasFactory;

    protected $fillable = [
        'part_no',
        'part_sap',
        'part_name',
        'supplier_id',
        'product_id',
        'category_id',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function qtyForecasts(): HasMany
    {
        return $this->hasMany(QtyForecast::class);
    }

    public function qtyBudgets(): HasMany
    {
        return $this->hasMany(QtyBudget::class);
    }

    public function rmSuppliers(): HasMany
    {
        return $this->hasMany(RmSupplier::class, 'part_no_id');
    }

    public function processCostSuppliers(): HasMany
    {
        return $this->hasMany(ProcessCostSupplier::class, 'part_no_id');
    }

    public function toolingSuppliers(): HasMany
    {
        return $this->hasMany(ToolingSupplier::class, 'part_no_id');
    }

    public function otherCostSuppliers(): HasMany
    {
        return $this->hasMany(OtherCostSupplier::class, 'part_no_id');
    }

    public function fohProfiySuppliers(): HasMany
    {
        return $this->hasMany(FohProfiySupplier::class, 'part_no_id');
    }
}
