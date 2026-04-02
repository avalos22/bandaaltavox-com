<?php

use App\Http\Controllers\Admin\ContractController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\QuotationController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ServiceAddonController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\Client\ClientPortalController;
use App\Http\Controllers\Client\ClientQuotationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Public Routes (Página web pública de la banda)
|--------------------------------------------------------------------------
*/
Route::get('/', [PublicController::class, 'home'])->name('home');

/*
|--------------------------------------------------------------------------
| Two-Factor Authentication Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'active'])->prefix('two-factor')->name('two-factor.')->group(function () {
    Route::get('/setup', [TwoFactorController::class, 'setup'])->name('setup');
    Route::post('/confirm-setup', [TwoFactorController::class, 'confirmSetup'])->name('confirm-setup');
    Route::get('/challenge', [TwoFactorController::class, 'challenge'])->name('challenge');
    Route::post('/verify', [TwoFactorController::class, 'verify'])->name('verify');
    Route::post('/resend', [TwoFactorController::class, 'resend'])->name('resend');
});

/*
|--------------------------------------------------------------------------
| Admin Routes (Panel de administración)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'active', 'verified', 'two-factor'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Users
    Route::middleware('permission:users.view')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create')->middleware('permission:users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store')->middleware('permission:users.create');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit')->middleware('permission:users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update')->middleware('permission:users.edit');
        Route::patch('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active')->middleware('permission:users.edit');
        Route::post('/users/{user}/reset-two-factor', [UserController::class, 'resetTwoFactor'])->name('users.reset-two-factor')->middleware('permission:users.edit');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy')->middleware('permission:users.delete');
    });

    // Roles
    Route::middleware('permission:roles.view')->group(function () {
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create')->middleware('permission:roles.create');
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store')->middleware('permission:roles.create');
        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit')->middleware('permission:roles.edit');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update')->middleware('permission:roles.edit');
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy')->middleware('permission:roles.delete');
    });

    // Packages
    Route::middleware('permission:packages.view')->group(function () {
        Route::get('/packages', [PackageController::class, 'index'])->name('packages.index');
        Route::get('/packages/create', [PackageController::class, 'create'])->name('packages.create')->middleware('permission:packages.create');
        Route::post('/packages', [PackageController::class, 'store'])->name('packages.store')->middleware('permission:packages.create');
        Route::get('/packages/{package}/edit', [PackageController::class, 'edit'])->name('packages.edit')->middleware('permission:packages.edit');
        Route::put('/packages/{package}', [PackageController::class, 'update'])->name('packages.update')->middleware('permission:packages.edit');
        Route::post('/packages/{package}/upload-image', [PackageController::class, 'uploadImage'])->name('packages.upload-image')->middleware('permission:packages.edit');
        Route::delete('/packages/{package}/delete-image', [PackageController::class, 'deleteImage'])->name('packages.delete-image')->middleware('permission:packages.edit');
        Route::delete('/packages/{package}', [PackageController::class, 'destroy'])->name('packages.destroy')->middleware('permission:packages.delete');
    });

    // Service Add-ons
    Route::middleware('permission:packages.view')->group(function () {
        Route::get('/addons', [ServiceAddonController::class, 'index'])->name('addons.index');
        Route::get('/addons/create', [ServiceAddonController::class, 'create'])->name('addons.create')->middleware('permission:packages.create');
        Route::post('/addons', [ServiceAddonController::class, 'store'])->name('addons.store')->middleware('permission:packages.create');
        Route::get('/addons/{addon}/edit', [ServiceAddonController::class, 'edit'])->name('addons.edit')->middleware('permission:packages.edit');
        Route::put('/addons/{addon}', [ServiceAddonController::class, 'update'])->name('addons.update')->middleware('permission:packages.edit');
        Route::delete('/addons/{addon}', [ServiceAddonController::class, 'destroy'])->name('addons.destroy')->middleware('permission:packages.delete');
    });

    // Settings
    Route::middleware('permission:settings.view')->group(function () {
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update')->middleware('permission:settings.edit');
        Route::post('/settings/upload-image', [SettingController::class, 'uploadImage'])->name('settings.upload-image')->middleware('permission:settings.edit');
        Route::delete('/settings/delete-image', [SettingController::class, 'deleteImage'])->name('settings.delete-image')->middleware('permission:settings.edit');
    });

    // Quotations
    Route::middleware('permission:quotations.view')->group(function () {
        Route::get('/quotations', [QuotationController::class, 'index'])->name('quotations.index');
        Route::get('/quotations/create', [QuotationController::class, 'create'])->name('quotations.create')->middleware('permission:quotations.create');
        Route::post('/quotations', [QuotationController::class, 'store'])->name('quotations.store')->middleware('permission:quotations.create');
        Route::get('/quotations/{quotation}', [QuotationController::class, 'show'])->name('quotations.show');
        Route::patch('/quotations/{quotation}/status', [QuotationController::class, 'updateStatus'])->name('quotations.update-status')->middleware('permission:quotations.edit');
        Route::delete('/quotations/{quotation}', [QuotationController::class, 'destroy'])->name('quotations.destroy')->middleware('permission:quotations.delete');
    });

    // Contracts
    Route::middleware('permission:contracts.view')->group(function () {
        Route::get('/contracts', [ContractController::class, 'index'])->name('contracts.index');
        Route::post('/contracts', [ContractController::class, 'store'])->name('contracts.store')->middleware('permission:contracts.create');
        Route::get('/contracts/{contract}', [ContractController::class, 'show'])->name('contracts.show');
        Route::patch('/contracts/{contract}/status', [ContractController::class, 'updateStatus'])->name('contracts.update-status')->middleware('permission:contracts.edit');
    });

    // Events (Agenda)
    Route::middleware('permission:events.view')->group(function () {
        Route::get('/events', [EventController::class, 'index'])->name('events.index');
        Route::post('/events', [EventController::class, 'store'])->name('events.store')->middleware('permission:events.create');
        Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
        Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update')->middleware('permission:events.edit');
        Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy')->middleware('permission:events.delete');
    });

    // Payments (Cobranza)
    Route::middleware('permission:payments.view')->group(function () {
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/{contract}', [PaymentController::class, 'show'])->name('payments.show');
        Route::post('/payments/{contract}', [PaymentController::class, 'store'])->name('payments.store')->middleware('permission:payments.create');
        Route::delete('/payments/{payment}/delete', [PaymentController::class, 'destroy'])->name('payments.destroy')->middleware('permission:payments.delete');
    });

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Client Portal Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'active', 'two-factor', 'role:Cliente'])->prefix('portal')->name('client.')->group(function () {
    Route::get('/dashboard', [ClientPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/events', [ClientPortalController::class, 'events'])->name('events');
    Route::get('/contracts', [ClientPortalController::class, 'contracts'])->name('contracts');
    Route::get('/contracts/{contract}', [ClientPortalController::class, 'contractShow'])->name('contracts.show');
    Route::get('/payments', [ClientPortalController::class, 'payments'])->name('payments');

    // Quotations
    Route::get('/quotations', [ClientQuotationController::class, 'index'])->name('quotations');
    Route::get('/quotations/create', [ClientQuotationController::class, 'create'])->name('quotations.create');
    Route::post('/quotations', [ClientQuotationController::class, 'store'])->name('quotations.store');
    Route::get('/quotations/{quotation}', [ClientQuotationController::class, 'show'])->name('quotations.show');
});

require __DIR__.'/auth.php';
