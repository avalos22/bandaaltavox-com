<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Event;
use App\Models\Payment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClientPortalController extends Controller
{
    public function dashboard(Request $request)
    {
        $userId = $request->user()->id;

        $upcomingEvents = Event::where('client_id', $userId)
            ->where('event_date', '>=', now())
            ->whereIn('status', ['confirmed', 'tentative', 'in_progress'])
            ->orderBy('event_date')
            ->take(5)
            ->get();

        $contracts = Contract::where('client_id', $userId)
            ->withSum('payments', 'amount')
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        $recentPayments = Payment::whereHas('contract', fn ($q) => $q->where('client_id', $userId))
            ->with('contract:id,contract_number')
            ->orderByDesc('payment_date')
            ->take(5)
            ->get();

        // Summary
        $totalContracts = Contract::where('client_id', $userId)->where('status', '!=', 'cancelled')->count();
        $totalOwed = Contract::where('client_id', $userId)->where('status', '!=', 'cancelled')->sum('total_amount');
        $totalPaid = Payment::whereHas('contract', fn ($q) => $q->where('client_id', $userId))->sum('amount');
        $nextEvent = $upcomingEvents->first();

        return Inertia::render('Client/Dashboard', [
            'upcomingEvents' => $upcomingEvents,
            'contracts' => $contracts,
            'recentPayments' => $recentPayments,
            'stats' => [
                'totalContracts' => $totalContracts,
                'totalOwed' => (float) $totalOwed,
                'totalPaid' => (float) $totalPaid,
                'balance' => max(0, (float) ($totalOwed - $totalPaid)),
            ],
            'nextEvent' => $nextEvent,
        ]);
    }

    public function events(Request $request)
    {
        $events = Event::where('client_id', $request->user()->id)
            ->with('contract:id,contract_number,total_amount')
            ->orderByDesc('event_date')
            ->paginate(10);

        return Inertia::render('Client/Events', [
            'events' => $events,
            'statuses' => Event::STATUSES,
            'statusColors' => Event::STATUS_COLORS,
        ]);
    }

    public function contracts(Request $request)
    {
        $contracts = Contract::where('client_id', $request->user()->id)
            ->withSum('payments', 'amount')
            ->orderByDesc('created_at')
            ->paginate(10);

        return Inertia::render('Client/Contracts', [
            'contracts' => $contracts,
        ]);
    }

    public function contractShow(Contract $contract, Request $request)
    {
        // Ensure the client owns this contract
        if ($contract->client_id !== $request->user()->id) {
            abort(403);
        }

        $contract->load(['payments', 'event', 'quotation.items']);

        return Inertia::render('Client/ContractShow', [
            'contract' => $contract,
            'totalPaid' => $contract->totalPaid(),
            'balance' => $contract->balance(),
        ]);
    }

    public function payments(Request $request)
    {
        $payments = Payment::whereHas('contract', fn ($q) => $q->where('client_id', $request->user()->id))
            ->with('contract:id,contract_number,total_amount')
            ->orderByDesc('payment_date')
            ->paginate(15);

        $totalPaid = Payment::whereHas('contract', fn ($q) => $q->where('client_id', $request->user()->id))->sum('amount');

        return Inertia::render('Client/Payments', [
            'payments' => $payments,
            'totalPaid' => (float) $totalPaid,
        ]);
    }
}
