<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'contract_id', 'recorded_by',
        'amount', 'method', 'payment_date',
        'reference', 'notes',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public const METHODS = [
        'cash' => 'Efectivo',
        'transfer' => 'Transferencia',
        'card' => 'Tarjeta',
        'deposit' => 'Depósito',
        'other' => 'Otro',
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
