<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Event;
use App\Models\Payment;
use App\Models\Quotation;
use App\Models\Setting;
use Carbon\Carbon;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        $monthStart = $now->copy()->startOfMonth();
        $monthEnd = $now->copy()->endOfMonth();

        $eventsThisMonth = Event::whereBetween('event_date', [$monthStart, $monthEnd])->count();

        $pendingQuotations = Quotation::whereIn('status', ['draft', 'sent'])->count();

        $collectedThisMonth = Payment::whereBetween('payment_date', [$monthStart, $monthEnd])->sum('amount');

        $pendingPayments = Contract::where('status', 'active')
            ->get()
            ->sum(fn ($c) => $c->balance());

        $upcomingEvents = Event::where('event_date', '>=', $now->toDateString())
            ->whereIn('status', ['confirmed', 'tentative'])
            ->orderBy('event_date')
            ->limit(5)
            ->get();

        $recentPayments = Payment::with('contract')
            ->orderByDesc('payment_date')
            ->limit(5)
            ->get();

        return Inertia::render('Admin/Dashboard', [
            'businessName' => Setting::get('business_name', 'Banda Alta Vox'),
            'stats' => [
                'eventsThisMonth' => $eventsThisMonth,
                'pendingQuotations' => $pendingQuotations,
                'collectedThisMonth' => (float) $collectedThisMonth,
                'pendingPayments' => (float) $pendingPayments,
            ],
            'upcomingEvents' => $upcomingEvents,
            'recentPayments' => $recentPayments,
        ]);
    }
}
