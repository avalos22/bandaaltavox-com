<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Contract extends Model
{
    protected $fillable = [
        'contract_number', 'quotation_id', 'client_id', 'status',
        'contract_date',
        'client_name', 'client_email', 'client_phone', 'client_address',
        'event_type_label', 'event_date', 'event_time_start', 'event_time_end',
        'event_venue', 'hours_contracted', 'event_is_outdoor',
        'total_amount', 'first_payment_amount', 'first_payment_date',
        'observations', 'clauses', 'representative_name',
        'signed_at', 'notes',
    ];

    protected $casts = [
        'contract_date' => 'date',
        'event_date' => 'date',
        'first_payment_date' => 'date',
        'signed_at' => 'datetime',
        'event_is_outdoor' => 'boolean',
        'total_amount' => 'decimal:2',
        'first_payment_amount' => 'decimal:2',
        'clauses' => 'array',
    ];

    public const STATUSES = [
        'pending' => 'Pendiente',
        'active' => 'Activo',
        'completed' => 'Completado',
        'cancelled' => 'Cancelado',
    ];

    public const DEFAULT_CLAUSES = [
        'I.- Brindará el servicio musical que a continuación se describe: Repertorio versátil - Audio e iluminación profesional - Escenario (en caso de requerirse).',
        'II.- En caso de cancelar el evento, no se devolverá el pago del anticipo.',
        'III.- El cliente está consciente que los integrantes pueden variar por razones diversas como enfermedad, despido, o cualquier otra razón, en caso de que esto sucediera, se reemplazarán con otro.',
        'IV.- El cliente se hace responsable de las necesidades del grupo y de las condiciones del lugar del evento, como seguridad para los integrantes y sus herramientas de trabajo, de no satisfacer esas necesidades, el grupo se verá en libertad de abandonar el evento.',
        'V.- 8 días hábiles antes de la fecha estipulada en el presente contrato, se deberá liquidar en su totalidad la cantidad acordada.',
        'VI.- Si el cliente llegara a cancelar dicho contrato 3 meses antes o menos del evento se tendrá que liquidar en su totalidad la cantidad acordada.',
        'VII.- En caso de que el evento sea al aire libre, el cliente se compromete a proveer el techado completo, seguro y firme para el grupo y el equipo.',
        'VIII.- Los precios no incluyen IVA.',
        'IX.- En caso de requerir tiempo extra el día del evento, el pago deberá ser en efectivo la cantidad equivalente a la banda en el momento solicitado.',
        'X.- En caso de requerir cambiar nuevamente la fecha se cobrará una penalización de $1,200 pesos por cambio.',
    ];

    public static function generateNumber(): string
    {
        $year = date('Y');
        $month = date('m');
        // Buscar el último número con el formato C-ALTAVOX-2026-030001
        $last = static::where('contract_number', 'like', "C-ALTAVOX-{$year}-{$month}%")
            ->orderByDesc('contract_number')
            ->value('contract_number');

        $sequence = 1;
        if ($last) {
            // Extraer el número de contrato del formato C-ALTAVOX-2026-030001
            $seqStr = substr($last, strlen("C-ALTAVOX-{$year}-{$month}"));
            $sequence = (int) $seqStr + 1;
        }

        return sprintf('C-ALTAVOX-%s-%s%04d', $year, $month, $sequence);
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function event(): HasOne
    {
        return $this->hasOne(Event::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class)->orderByDesc('payment_date');
    }

    public function totalPaid(): float
    {
        return (float) $this->payments()->sum('amount');
    }

    public function balance(): float
    {
        return (float) $this->total_amount - $this->totalPaid();
    }
}
