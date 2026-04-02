<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventType;
use App\Models\Package;
use App\Models\Quotation;
use App\Models\ServiceAddon;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class QuotationController extends Controller
{
    public function index(Request $request)
    {
        $quotations = Quotation::with(['eventType', 'creator'])
            ->when($request->search, function ($q, $s) {
                $q->where(function ($q) use ($s) {
                    $q->where('client_name', 'like', "%{$s}%")
                      ->orWhere('quotation_number', 'like', "%{$s}%")
                      ->orWhere('client_email', 'like', "%{$s}%");
                });
            })
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Quotations/Index', [
            'quotations' => $quotations,
            'filters' => $request->only(['search', 'status']),
            'statuses' => Quotation::STATUSES,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Quotations/Wizard', [
            'eventTypes' => EventType::where('is_active', true)->orderBy('sort_order')->get(),
            'packages' => Package::with('includes')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(),
            'addons' => ServiceAddon::where('is_active', true)
                ->orderBy('category')
                ->orderBy('sort_order')
                ->get(),
            'addonCategories'    => ServiceAddon::CATEGORIES,
            'addonSubcategories' => ServiceAddon::SUBCATEGORIES,
            'defaults' => [
                'validity_days' => (int) Setting::get('quotation_validity_days', 15),
                'deposit_percentage' => (int) Setting::get('deposit_percentage', 30),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // Event
            'event_type_id' => ['nullable', 'exists:event_types,id'],
            'event_date' => ['nullable', 'date'],
            'event_time_start' => ['nullable', 'string'],
            'event_time_end' => ['nullable', 'string'],
            'event_venue' => ['nullable', 'string', 'max:500'],
            'event_city' => ['nullable', 'string', 'max:255'],
            'event_is_outdoor' => ['boolean'],
            'guest_count' => ['nullable', 'integer', 'min:1'],
            'hours_contracted' => ['required', 'integer', 'min:1'],
            // Client
            'client_name' => ['required', 'string', 'max:255'],
            'client_email' => ['nullable', 'email', 'max:255'],
            'client_phone' => ['nullable', 'string', 'max:50'],
            'client_address' => ['nullable', 'string', 'max:500'],
            // Items
            'items' => ['required', 'array', 'min:1'],
            'items.*.type' => ['required', 'in:package,addon,custom'],
            'items.*.reference_id' => ['nullable', 'integer'],
            'items.*.description' => ['required', 'string', 'max:500'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            // Meta
            'observations' => ['nullable', 'string'],
            'discount' => ['nullable', 'numeric', 'min:0'],
        ]);

        // Por defecto, la validez es de 30 días
        $validityDays = (int) Setting::get('quotation_validity_days', 30);

        $quotation = Quotation::create([
            'quotation_number' => Quotation::generateNumber(),
            'status' => 'draft',
            'client_name' => $validated['client_name'],
            'client_email' => $validated['client_email'] ?? null,
            'client_phone' => $validated['client_phone'] ?? null,
            'client_address' => $validated['client_address'] ?? null,
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
            'discount' => $validated['discount'] ?? 0,
            'validity_days' => $validityDays,
            'expires_at' => now()->addDays($validityDays),
            'created_by' => auth()->id(),
        ]);

        // Create items
        foreach ($validated['items'] as $index => $item) {
            $total = $item['quantity'] * $item['unit_price'];
            $quotation->items()->create([
                'type' => $item['type'],
                'reference_id' => $item['reference_id'] ?? null,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total' => $total,
                'sort_order' => $index,
            ]);
        }

        $quotation->recalculate();

        return redirect()->route('admin.quotations.show', $quotation)
            ->with('success', "Cotización {$quotation->quotation_number} creada.");
    }

    public function show(Quotation $quotation)
    {
        $quotation->load(['items', 'eventType', 'creator', 'contract']);

        $depositPercentage = (int) Setting::get('deposit_percentage', 30);

        return Inertia::render('Admin/Quotations/Show', [
            'quotation' => $quotation,
            'depositPercentage' => $depositPercentage,
            'statuses' => Quotation::STATUSES,
            'defaultClauses' => \App\Models\Contract::DEFAULT_CLAUSES,
        ]);
    }

    public function updateStatus(Request $request, Quotation $quotation)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:draft,sent,accepted,rejected,expired'],
        ]);

        $quotation->update(['status' => $validated['status']]);

        return back()->with('success', 'Estado actualizado.');
    }

    public function destroy(Quotation $quotation)
    {
        $number = $quotation->quotation_number;
        $quotation->delete();

        return redirect()->route('admin.quotations.index')
            ->with('success', "Cotización {$number} eliminada.");
    }
}
