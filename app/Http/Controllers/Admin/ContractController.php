<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Event;
use App\Models\Payment;
use App\Models\Quotation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ContractController extends Controller
{
    public function index(Request $request)
    {
        $contracts = Contract::with(['quotation', 'client'])
            ->when($request->search, function ($q, $s) {
                $q->where(function ($q) use ($s) {
                    $q->where('client_name', 'like', "%{$s}%")
                      ->orWhere('contract_number', 'like', "%{$s}%");
                });
            })
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Contracts/Index', [
            'contracts' => $contracts,
            'filters' => $request->only(['search', 'status']),
            'statuses' => Contract::STATUSES,
        ]);
    }

    /**
     * Convert a quotation to a contract (close the sale).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'quotation_id' => ['required', 'exists:quotations,id'],
            'first_payment_amount' => ['required', 'numeric', 'min:0'],
            'first_payment_date' => ['nullable', 'date'],
            'clauses' => ['required', 'array', 'min:1'],
            'clauses.*' => ['required', 'string'],
            'representative_name' => ['required', 'string', 'max:255'],
            'observations' => ['nullable', 'string'],
        ]);

        $quotation = Quotation::with(['eventType', 'items'])->findOrFail($validated['quotation_id']);

        // Prevent duplicate contracts
        if ($quotation->contract) {
            return back()->with('error', 'Esta cotización ya tiene un contrato.');
        }

        // Create client user if email provided and doesn't exist
        $clientId = null;
        if ($quotation->client_email) {
            $client = User::firstOrCreate(
                ['email' => $quotation->client_email],
                [
                    'name' => $quotation->client_name,
                    'password' => Hash::make(Str::random(12)),
                    'phone' => $quotation->client_phone ?? '',
                    'is_active' => true,
                    'email_verified_at' => now(),
                    'two_factor_type' => 'email',
                    'two_factor_confirmed_at' => now(),
                ]
            );
            $client->assignRole('Cliente');
            $clientId = $client->id;
        }

        $contract = Contract::create([
            'contract_number' => Contract::generateNumber(),
            'quotation_id' => $quotation->id,
            'client_id' => $clientId,
            'status' => 'active',
            'contract_date' => now()->toDateString(),
            'client_name' => $quotation->client_name,
            'client_email' => $quotation->client_email,
            'client_phone' => $quotation->client_phone,
            'client_address' => $quotation->client_address,
            'event_type_label' => $quotation->eventType?->name ?? 'Evento social',
            'event_date' => $quotation->event_date,
            'event_time_start' => $quotation->event_time_start,
            'event_time_end' => $quotation->event_time_end,
            'event_venue' => $quotation->event_venue,
            'hours_contracted' => $quotation->hours_contracted,
            'event_is_outdoor' => $quotation->event_is_outdoor,
            'total_amount' => $quotation->total,
            'first_payment_amount' => $validated['first_payment_amount'],
            'first_payment_date' => $validated['first_payment_date'] ?? now()->toDateString(),
            'observations' => $validated['observations'] ?? $quotation->observations,
            'clauses' => $validated['clauses'],
            'representative_name' => $validated['representative_name'],
        ]);

        // Create event in agenda
        $eventTypeLabel = $quotation->eventType?->name ?? 'Evento';
        Event::create([
            'contract_id' => $contract->id,
            'client_id' => $clientId,
            'title' => "{$eventTypeLabel} - {$quotation->client_name}",
            'status' => 'confirmed',
            'color' => Event::STATUS_COLORS['confirmed'],
            'event_type_label' => $eventTypeLabel,
            'event_date' => $quotation->event_date,
            'time_start' => $quotation->event_time_start,
            'time_end' => $quotation->event_time_end,
            'venue' => $quotation->event_venue,
            'city' => $quotation->event_city,
            'is_outdoor' => $quotation->event_is_outdoor,
            'guest_count' => $quotation->guest_count,
            'hours_contracted' => $quotation->hours_contracted,
            'client_name' => $quotation->client_name,
            'client_phone' => $quotation->client_phone,
        ]);

        // Record initial payment if amount > 0
        if ($validated['first_payment_amount'] > 0) {
            Payment::create([
                'contract_id' => $contract->id,
                'recorded_by' => auth()->id(),
                'amount' => $validated['first_payment_amount'],
                'method' => 'cash',
                'payment_date' => $validated['first_payment_date'] ?? now()->toDateString(),
                'notes' => 'Anticipo al firmar contrato',
            ]);
        }

        // Mark quotation as converted
        $quotation->update([
            'status' => 'converted',
            'converted_at' => now(),
        ]);

        return redirect()->route('admin.contracts.show', $contract)
            ->with('success', "Contrato {$contract->contract_number} creado. Cliente registrado.");
    }

    public function show(Contract $contract)
    {
        $contract->load(['quotation.items', 'client', 'payments.recorder', 'event']);

        return Inertia::render('Admin/Contracts/Show', [
            'contract' => $contract,
            'totalPaid' => $contract->totalPaid(),
            'balance' => $contract->balance(),
            'statuses' => Contract::STATUSES,
            'paymentMethods' => Payment::METHODS,
        ]);
    }

    public function updateStatus(Request $request, Contract $contract)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,active,completed,cancelled'],
        ]);

        $contract->update(['status' => $validated['status']]);

        return back()->with('success', 'Estado del contrato actualizado.');
    }
}
