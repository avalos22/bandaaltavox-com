<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceAddon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ServiceAddonController extends Controller
{
    public function index(Request $request)
    {
        $addons = ServiceAddon::query()
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->when($request->category, fn ($q, $c) => $q->where('category', $c))
            ->orderBy('category')
            ->orderBy('sort_order')
            ->get();

        return Inertia::render('Admin/Packages/Addons/Index', [
            'addons' => $addons,
            'categories' => ServiceAddon::CATEGORIES,
            'filters' => $request->only(['search', 'category']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Packages/Addons/Create', [
            'categories' => ServiceAddon::CATEGORIES,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'in:' . implode(',', array_keys(ServiceAddon::CATEGORIES))],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'unit' => ['required', 'string', 'max:50'],
            'is_active' => ['boolean'],
        ]);

        ServiceAddon::create($validated);

        return redirect()->route('admin.addons.index')->with('success', 'Servicio adicional creado.');
    }

    public function edit(ServiceAddon $addon)
    {
        return Inertia::render('Admin/Packages/Addons/Edit', [
            'editAddon' => $addon,
            'categories' => ServiceAddon::CATEGORIES,
        ]);
    }

    public function update(Request $request, ServiceAddon $addon)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'in:' . implode(',', array_keys(ServiceAddon::CATEGORIES))],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'unit' => ['required', 'string', 'max:50'],
            'is_active' => ['boolean'],
        ]);

        $addon->update($validated);

        return redirect()->route('admin.addons.index')->with('success', 'Servicio actualizado correctamente.');
    }

    public function destroy(ServiceAddon $addon)
    {
        $addon->delete();

        return redirect()->route('admin.addons.index')->with('success', 'Servicio eliminado.');
    }
}
