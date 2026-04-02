<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        $query = User::with('roles');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->has('role') && $request->input('role') !== '') {
            $query->whereHas('roles', fn ($q) => $q->where('name', $request->input('role')));
        }

        if ($request->has('status') && $request->input('status') !== '') {
            $query->where('is_active', $request->input('status') === 'active');
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'roles' => Role::pluck('name'),
            'filters' => $request->only(['search', 'role', 'status']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Users/Create', [
            'roles' => Role::pluck('name'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'email'            => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone'            => ['nullable', 'string', 'max:20'],
            'password'         => ['required', Password::defaults()],
            'role'             => ['required', 'string', Rule::exists('roles', 'name')],
            'two_factor_type'  => ['nullable', 'string', Rule::in(['email', 'totp'])],
            'is_active'        => ['boolean'],
        ]);

        // Staff/Admin always use TOTP; clients choose (default: email)
        $tfType = $validated['role'] === 'Cliente'
            ? ($validated['two_factor_type'] ?? 'email')
            : 'totp';

        // Email 2FA is auto-confirmed; TOTP requires setup on first login
        $tfConfirmed = $tfType === 'email' ? now() : null;

        $user = User::create([
            'name'                    => $validated['name'],
            'email'                   => $validated['email'],
            'phone'                   => $validated['phone'] ?? '',
            'password'                => Hash::make($validated['password']),
            'is_active'               => $validated['is_active'] ?? true,
            'email_verified_at'       => now(),
            'two_factor_type'         => $tfType,
            'two_factor_confirmed_at' => $tfConfirmed,
        ]);

        $user->assignRole($validated['role']);

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $user): Response
    {
        return Inertia::render('Admin/Users/Edit', [
            'editUser' => $user->load('roles'),
            'roles' => Role::pluck('name'),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name'            => ['required', 'string', 'max:255'],
            'email'           => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone'           => ['nullable', 'string', 'max:20'],
            'password'        => ['nullable', Password::defaults()],
            'role'            => ['required', 'string', Rule::exists('roles', 'name')],
            'two_factor_type' => ['nullable', 'string', Rule::in(['email', 'totp'])],
            'is_active'       => ['boolean'],
        ]);

        $user->update([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'phone'     => $validated['phone'] ?? '',
            'is_active' => $validated['is_active'] ?? true,
        ]);

        if (!empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        // Handle 2FA type change for client users
        if ($validated['role'] === 'Cliente' && isset($validated['two_factor_type'])) {
            $newType = $validated['two_factor_type'];
            if ($newType !== $user->two_factor_type) {
                $user->update([
                    'two_factor_type'         => $newType,
                    'two_factor_secret'       => null,
                    'two_factor_confirmed_at' => $newType === 'email' ? now() : null,
                    'two_factor_email_code'   => null,
                    'two_factor_email_expires_at' => null,
                ]);
            }
        }

        $user->syncRoles([$validated['role']]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function resetTwoFactor(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes restablecer tu propio 2FA.');
        }

        $user->update([
            'two_factor_secret'           => null,
            'two_factor_confirmed_at'     => null,
            'two_factor_email_code'       => null,
            'two_factor_email_expires_at' => null,
        ]);

        return back()->with('success', "2FA restablecido para {$user->name}. Deberá configurarlo al próximo inicio de sesión.");
    }

    public function toggleActive(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes desactivar tu propia cuenta.');
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'activado' : 'desactivado';

        return back()->with('success', "Usuario {$status} correctamente.");
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }
}
