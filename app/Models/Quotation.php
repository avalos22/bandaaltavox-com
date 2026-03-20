<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Quotation extends Model
{
    protected $fillable = [
        'quotation_number', 'status',
        'client_name', 'client_email', 'client_phone', 'client_address',
        'event_type_id', 'event_date', 'event_time_start', 'event_time_end',
        'event_venue', 'event_city', 'event_is_outdoor', 'guest_count', 'hours_contracted',
        'subtotal', 'discount', 'total',
        'observations', 'validity_days', 'expires_at', 'created_by', 'converted_at',
    ];

    protected $casts = [
        'event_date' => 'date',
        'expires_at' => 'date',
        'converted_at' => 'datetime',
        'event_is_outdoor' => 'boolean',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public const STATUSES = [
        'draft' => 'Borrador',
        'sent' => 'Enviada',
        'accepted' => 'Aceptada',
        'rejected' => 'Rechazada',
        'expired' => 'Expirada',
        'converted' => 'Convertida',
    ];

    public static function generateNumber(): string
    {
        $year = date('Y');
        $month = date('m');
        // Buscar el último número con el formato ALTAVOX-2026-030001
        $last = static::where('quotation_number', 'like', "ALTAVOX-{$year}-{$month}%")
            ->orderByDesc('quotation_number')
            ->value('quotation_number');

        $sequence = 1;
        if ($last) {
            // Extraer el número de cotización del formato ALTAVOX-2026-030001
            $seqStr = substr($last, strlen("ALTAVOX-{$year}-{$month}"));
            $sequence = (int) $seqStr + 1;
        }

        return sprintf('ALTAVOX-%s-%s%04d', $year, $month, $sequence);
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuotationItem::class)->orderBy('sort_order');
    }

    public function eventType(): BelongsTo
    {
        return $this->belongsTo(EventType::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function contract(): HasOne
    {
        return $this->hasOne(Contract::class);
    }

    public function recalculate(): void
    {
        $subtotal = $this->items()->sum('total');
        $this->update([
            'subtotal' => $subtotal,
            'total' => $subtotal - $this->discount,
        ]);
    }
}
