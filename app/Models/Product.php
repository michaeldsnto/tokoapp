<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'code',
        'price',
        'price_per_unit',
        'price_per_pack',
        'price_per_dozen',
        'image_path',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'price_per_unit' => 'decimal:2',
            'price_per_pack' => 'decimal:2',
            'price_per_dozen' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function transactionDetails(): HasMany
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public static function unitOptions(): array
    {
        return [
            'satuan' => 'Satuan',
            'pak' => 'Pak',
            'lusin' => 'Lusin',
        ];
    }

    public function getPriceForUnit(string $unitType): float
    {
        return match ($unitType) {
            'pak' => (float) $this->price_per_pack,
            'lusin' => (float) $this->price_per_dozen,
            default => (float) $this->price_per_unit,
        };
    }
}
