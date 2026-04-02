<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\EventType;
use App\Models\Package;
use App\Models\Quotation;
use App\Models\ServiceAddon;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClientQuotationController extends Controller
{
    public function index(Request $request)
    {
        $quotations = Quotation::where('client_email', $request->user()->email)
            ->with('eventType')
            ->orderByDesc('created_at')
            ->paginate(10);

        return Inertia::render('Client/Quotations', [
            'quotations' => $quotations,
            'statuses' => Quotation::STATUSES,
        ]);
    }

    public function show(Quotation $quotation, Request $request)
    {
        // Ensure client owns this quotation
        if ($quotation->client_email !== $request->user()->email) {
            abort(403);
        }

        $quotation->load(['items', 'eventType']);

        return Inertia::render('Client/QuotationShow', [
            'quotation' => $quotation,
            'statuses' => Quotation::STATUSES,
        ]);
    }

    public function create()
    {
        return Inertia::render('Client/QuotationWizard', [
            'eventTypes' => EventType::where('is_active', true)->orderBy('sort_order')->get(),
            'packages' => Package::with('includes')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(),
            'addons' => ServiceAddon::where('is_active', true)
                ->orderBy('category')
                ->orderBy('sort_order')
                ->get(),
            'addonCategories' => ServiceAddon::CATEGORIES,
            'addonSubcategories' => ServiceAddon::SUBCATEGORIES,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_type_id' => ['nullable', 'exists:event_types,id'],
            'event_date' => ['nullable', 'date', 'after:today'],
            'event_time_start' => ['nullable', 'string'],
            'event_time_end' => ['nullable', 'string'],
            'event_venue' => ['nullable', 'string', 'max:500'],
            'event_city' => ['nullable', 'string', 'max:255'],
            'event_is_outdoor' => ['boolean'],
            'guest_count' => ['nullable', 'integer', 'min:1'],
            'hours_contracted' => ['required', 'integer', 'min:1'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.type' => ['required', 'in:package,addon'],
            'items.*.reference_id' => ['required', 'integer'],
            'items.*.description' => ['required', 'string', 'max:500'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'observations' => ['nullable', 'string', 'max:2000'],
        ]);

        $user = $request->user();
        $validityDays = (int) Setting::get('quotation_validity_days', 30);

        $quotation = Quotation::create([
            'quotation_number' => Quotation::generateNumber(),
            'status' => 'draft',
            'client_name' => $user->name,
            'client_email' => $user->email,
            'client_phone' => $user->phone,
            'event_type_id' => $validated['event_type_id'] ?? null,
            'event_date' => $validated['event_date'] ?? null,
            'event_time_start' => $validated['event_time_start'] ?? null,
            'event_time_end' => $validated['event_time_end'] ?? null,
            'event_venue' => $validated['event_venue'] ?? null,
            'event_city' => $validated['event_city'] ?? null,
            'event_is_outdoor' => $validated['event_is_outdoor'] ?? false,
            'guest_count' => $validated['guest_count'] ?? null,
            'hours_contracted' => $validated['hours_contracted'],
            'observations' => $validated['observations'] ?? null,
            'discount' => 0,
            'validity_days' => $validityDays,
            'expires_at' => now()->addDays($validityDays),
            'created_by' => $user->id,
        ]);

        foreach ($validated['items'] as $index => $item) {
            $total = $item['quantity'] * $item['unit_price'];
            $quotation->items()->create([
                'type' => $item['type'],
                'reference_id' => $item['reference_id'],
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total' => $total,
                'sort_order' => $index,
            ]);
        }

        $quotation->recalculate();

        return redirect()->route('client.quotations.show', $quotation)
            ->with('success', 'Tu solicitud de cotización ha sido enviada correctamente.');
    }
}
