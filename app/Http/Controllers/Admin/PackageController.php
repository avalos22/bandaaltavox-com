<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventType;
use App\Models\Package;
use App\Models\PackageInclude;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PackageController extends Controller
{
    public function index(Request $request)
    {
        $packages = Package::with(['includes', 'eventTypes'])
            ->withCount('includes')
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->when($request->status !== null && $request->status !== '', function ($q) use ($request) {
                $q->where('is_active', $request->status);
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return Inertia::render('Admin/Packages/Index', [
            'packages' => $packages,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Packages/Create', [
            'eventTypes'       => EventType::where('is_active', true)->orderBy('sort_order')->get(),
            'addonSubcategories' => \App\Models\ServiceAddon::SUBCATEGORIES,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'duration_hours' => ['required', 'integer', 'min:1'],
            'required_addon_subcategory' => ['nullable', 'string', 'max:100'],
            'is_active' => ['boolean'],
            'is_featured' => ['boolean'],
            'event_types' => ['array'],
            'event_types.*' => ['exists:event_types,id'],
            'includes' => ['array'],
            'includes.*.description' => ['required', 'string', 'max:500'],
            'includes.*.is_highlighted' => ['boolean'],
        ]);

        $package = Package::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'duration_hours' => $validated['duration_hours'],
            'required_addon_subcategory' => $validated['required_addon_subcategory'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
            'is_featured' => $validated['is_featured'] ?? false,
        ]);

        // Save includes
        foreach ($validated['includes'] ?? [] as $index => $include) {
            $package->includes()->create([
                'description' => $include['description'],
                'is_highlighted' => $include['is_highlighted'] ?? false,
                'sort_order' => $index,
            ]);
        }

        // Sync event types
        $package->eventTypes()->sync($validated['event_types'] ?? []);

        return redirect()->route('admin.packages.index')->with('success', 'Paquete creado correctamente.');
    }

    public function edit(Package $package)
    {
        $package->load(['includes', 'eventTypes']);

        return Inertia::render('Admin/Packages/Edit', [
            'editPackage'      => $package,
            'eventTypes'       => EventType::where('is_active', true)->orderBy('sort_order')->get(),
            'addonSubcategories' => \App\Models\ServiceAddon::SUBCATEGORIES,
        ]);
    }

    public function update(Request $request, Package $package)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'duration_hours' => ['required', 'integer', 'min:1'],
            'required_addon_subcategory' => ['nullable', 'string', 'max:100'],
            'is_active' => ['boolean'],
            'is_featured' => ['boolean'],
            'event_types' => ['array'],
            'event_types.*' => ['exists:event_types,id'],
            'includes' => ['array'],
            'includes.*.description' => ['required', 'string', 'max:500'],
            'includes.*.is_highlighted' => ['boolean'],
        ]);

        $package->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'duration_hours' => $validated['duration_hours'],
            'required_addon_subcategory' => $validated['required_addon_subcategory'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
            'is_featured' => $validated['is_featured'] ?? false,
        ]);

        // Rebuild includes
        $package->includes()->delete();
        foreach ($validated['includes'] ?? [] as $index => $include) {
            $package->includes()->create([
                'description' => $include['description'],
                'is_highlighted' => $include['is_highlighted'] ?? false,
                'sort_order' => $index,
            ]);
        }

        // Sync event types
        $package->eventTypes()->sync($validated['event_types'] ?? []);

        return redirect()->route('admin.packages.index')->with('success', 'Paquete actualizado correctamente.');
    }

    public function uploadImage(Request $request, Package $package)
    {
        $request->validate([
            'image' => ['required', 'image', 'max:2048'],
        ]);

        if ($package->image && Storage::disk('public')->exists($package->image)) {
            Storage::disk('public')->delete($package->image);
        }

        $path = $request->file('image')->store('packages', 'public');
        $package->update(['image' => $path]);

        return back()->with('success', 'Imagen del paquete actualizada.');
    }

    public function deleteImage(Package $package)
    {
        if ($package->image && Storage::disk('public')->exists($package->image)) {
            Storage::disk('public')->delete($package->image);
        }

        $package->update(['image' => null]);

        return back()->with('success', 'Imagen eliminada.');
    }

    public function destroy(Package $package)
    {
        if ($package->image && Storage::disk('public')->exists($package->image)) {
            Storage::disk('public')->delete($package->image);
        }

        $package->delete();

        return redirect()->route('admin.packages.index')->with('success', 'Paquete eliminado correctamente.');
    }
}
