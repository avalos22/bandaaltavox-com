<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Permissions
        $permissions = [
            // Users
            'users.view', 'users.create', 'users.edit', 'users.delete',
            // Roles
            'roles.view', 'roles.create', 'roles.edit', 'roles.delete',
            // Settings
            'settings.view', 'settings.edit',
            // Packages
            'packages.view', 'packages.create', 'packages.edit', 'packages.delete',
            // Quotations
            'quotations.view', 'quotations.create', 'quotations.edit', 'quotations.delete',
            // Events
            'events.view', 'events.create', 'events.edit', 'events.delete',
            // Contracts
            'contracts.view', 'contracts.create', 'contracts.edit', 'contracts.delete',
            // Payments
            'payments.view', 'payments.create', 'payments.edit', 'payments.delete',
            // Gallery
            'gallery.view', 'gallery.manage',
            // Dashboard
            'dashboard.admin', 'dashboard.client',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Super Admin - has all permissions
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // Admin - all except role management
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $admin->givePermissionTo(
            collect($permissions)->reject(fn ($p) => str_starts_with($p, 'roles.'))->toArray()
        );

        // Vendedor - can manage quotations, events, clients
        $vendedor = Role::firstOrCreate(['name' => 'Vendedor']);
        $vendedor->givePermissionTo([
            'quotations.view', 'quotations.create', 'quotations.edit',
            'events.view', 'events.create', 'events.edit',
            'contracts.view',
            'payments.view', 'payments.create',
            'packages.view',
            'dashboard.admin',
        ]);

        // Cliente - can only see their own stuff
        $cliente = Role::firstOrCreate(['name' => 'Cliente']);
        $cliente->givePermissionTo([
            'dashboard.client',
            'events.view',
            'contracts.view',
            'payments.view',
            'quotations.view',
            'quotations.create',
        ]);
    }
}
