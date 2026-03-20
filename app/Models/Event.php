<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    protected $fillable = [
        'contract_id', 'client_id',
        'title', 'status', 'color',
        'event_type_label', 'event_date', 'time_start', 'time_end',
        'venue', 'city', 'is_outdoor', 'guest_count', 'hours_contracted',
        'client_name', 'client_phone',
        'notes', 'setup_notes',
    ];

    protected $casts = [
        'event_date' => 'date',
        'is_outdoor' => 'boolean',
    ];

    public const STATUSES = [
        'confirmed' => 'Confirmado',
        'tentative' => 'Tentativo',
        'in_progress' => 'En curso',
        'completed' => 'Completado',
        'cancelled' => 'Cancelado',
    ];

    public const STATUS_COLORS = [
        'confirmed' => '#f59e0b',
        'tentative' => '#94a3b8',
        'in_progress' => '#3b82f6',
        'completed' => '#10b981',
        'cancelled' => '#ef4444',
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }
}
