<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    protected $fillable = [
        'invoice_number',
        'transaction_mode',
        'payment_status',
        'user_id',
        'customer_name',
        'subtotal',
        'discount_amount',
        'total',
        'paid_amount',
        'change_amount',
        'item_count',
        'notes',
        'transacted_at',
        'due_date',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'change_amount' => 'decimal:2',
            'transacted_at' => 'datetime',
            'due_date' => 'datetime',
        ];
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }
}
