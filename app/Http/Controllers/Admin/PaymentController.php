<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Payment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        // Show all contracts with payment summary
        $contracts = Contract::with(['client', 'event'])
            ->withSum('payments', 'amount')
            ->withCount('payments')
            ->when($request->search, function ($q, $s) {
                $q->where(function ($q) use ($s) {
                    $q->where('client_name', 'like', "%{$s}%")
                      ->orWhere('contract_number', 'like', "%{$s}%");
                });
            })
            ->when($request->filter, function ($q, $f) {
                match ($f) {
                    'pending' => $q->whereColumn(
                        \DB::raw('(SELECT COALESCE(SUM(amount), 0) FROM payments WHERE payments.contract_id = contracts.id)'),
                        '<',
                        'total_amount'
                    )->where('status', '!=', 'cancelled'),
                    'paid' => $q->whereColumn(
                        \DB::raw('(SELECT COALESCE(SUM(amount), 0) FROM payments WHERE payments.contract_id = contracts.id)'),
                        '>=',
                        'total_amount'
                    ),
                    'overdue' => $q->where('event_date', '<', now())
                        ->whereColumn(
                            \DB::raw('(SELECT COALESCE(SUM(amount), 0) FROM payments WHERE payments.contract_id = contracts.id)'),
                            '<',
                            'total_amount'
                        )->where('status', '!=', 'cancelled'),
                    default => $q,
                };
            })
            ->where('status', '!=', 'cancelled')
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        // Summary stats
        $totalContracts = Contract::where('status', '!=', 'cancelled')->count();
        $totalRevenue = Contract::where('status', '!=', 'cancelled')->sum('total_amount');
        $totalCollected = Payment::sum('amount');
        $totalPending = $totalRevenue - $totalCollected;

        return Inertia::render('Admin/Payments/Index', [
            'contracts' => $contracts,
            'filters' => $request->only(['search', 'filter']),
            'stats' => [
                'totalContracts' => $totalContracts,
                'totalRevenue' => (float) $totalRevenue,
                'totalCollected' => (float) $totalCollected,
                'totalPending' => max(0, (float) $totalPending),
            ],
        ]);
    }

    public function show(Contract $contract)
    {
        $contract->load(['payments.recorder', 'client', 'quotation', 'event']);

        return Inertia::render('Admin/Payments/Show', [
            'contract' => $contract,
            'totalPaid' => $contract->totalPaid(),
            'balance' => $contract->balance(),
            'paymentMethods' => Payment::METHODS,
        ]);
    }

    public function store(Request $request, Contract $contract)
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'method' => ['required', 'in:cash,transfer,card,deposit,other'],
            'payment_date' => ['required', 'date'],
            'reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['contract_id'] = $contract->id;
        $validated['recorded_by'] = auth()->id();

        Payment::create($validated);

        // Auto-complete contract if fully paid
        $newTotal = $contract->totalPaid() + $validated['amount'];
        if ($newTotal >= (float) $contract->total_amount && $contract->status === 'active') {
            // Don't auto-change — just notify, business may want manual control
        }

        return back()->with('success', 'Pago registrado correctamente.');
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();

        return back()->with('success', 'Pago eliminado.');
    }
}
