<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        // Events for the calendar (full month + buffer days)
        $startDate = now()->setYear($year)->setMonth($month)->startOfMonth()->subDays(7);
        $endDate = now()->setYear($year)->setMonth($month)->endOfMonth()->addDays(7);

        $events = Event::with(['contract'])
            ->whereBetween('event_date', [$startDate, $endDate])
            ->orderBy('event_date')
            ->get()
            ->map(fn ($e) => [
                'id' => $e->id,
                'title' => $e->title,
                'date' => $e->event_date->format('Y-m-d'),
                'time_start' => $e->time_start,
                'time_end' => $e->time_end,
                'status' => $e->status,
                'color' => $e->color,
                'venue' => $e->venue,
                'client_name' => $e->client_name,
                'client_phone' => $e->client_phone,
                'contract_id' => $e->contract_id,
            ]);

        // Upcoming events list
        $upcoming = Event::with(['contract'])
            ->where('event_date', '>=', now()->toDateString())
            ->whereIn('status', ['confirmed', 'tentative'])
            ->orderBy('event_date')
            ->limit(10)
            ->get();

        return Inertia::render('Admin/Events/Index', [
            'events' => $events,
            'upcoming' => $upcoming,
            'month' => (int) $month,
            'year' => (int) $year,
            'statuses' => Event::STATUSES,
            'statusColors' => Event::STATUS_COLORS,
        ]);
    }

    public function show(Event $event)
    {
        $event->load(['contract.payments', 'client']);

        return Inertia::render('Admin/Events/Show', [
            'event' => $event,
            'statuses' => Event::STATUSES,
        ]);
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'status' => ['sometimes', 'in:confirmed,tentative,in_progress,completed,cancelled'],
            'event_date' => ['sometimes', 'date'],
            'time_start' => ['nullable', 'string'],
            'time_end' => ['nullable', 'string'],
            'venue' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'setup_notes' => ['nullable', 'string'],
        ]);

        // Auto-set color from status
        if (isset($validated['status'])) {
            $validated['color'] = Event::STATUS_COLORS[$validated['status']] ?? '#f59e0b';
        }

        $event->update($validated);

        return back()->with('success', 'Evento actualizado.');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'event_date' => ['required', 'date'],
            'time_start' => ['nullable', 'string'],
            'time_end' => ['nullable', 'string'],
            'venue' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:255'],
            'is_outdoor' => ['boolean'],
            'guest_count' => ['nullable', 'integer', 'min:1'],
            'hours_contracted' => ['nullable', 'integer', 'min:1'],
            'client_name' => ['required', 'string', 'max:255'],
            'client_phone' => ['nullable', 'string', 'max:50'],
            'event_type_label' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'setup_notes' => ['nullable', 'string'],
        ]);

        $validated['status'] = 'tentative';
        $validated['color'] = Event::STATUS_COLORS['tentative'];

        Event::create($validated);

        return back()->with('success', 'Evento creado.');
    }

    public function destroy(Event $event)
    {
        $event->delete();

        return redirect()->route('admin.events.index')
            ->with('success', 'Evento eliminado.');
    }
}
