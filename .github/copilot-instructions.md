# Banda Alta Voz — Project Guidelines

## Stack

- **Backend**: Laravel 11, PHP 8.2+
- **Frontend**: Inertia.js + Vue 3 (Composition API), Tailwind CSS 4
- **Auth**: Laravel Breeze + Spatie Permission v7
- **Build**: Vite 8 (client + SSR bundles), `vite build && vite build --ssr`
- **DB**: MySQL 8 — local: `bandaaltavox`, root, no password
- **Tests**: PHPUnit, SQLite in-memory (`:memory:`)

## Build & Test Commands

```bash
composer run setup   # First-run: install, .env, key:generate, migrate, npm install, build
composer run dev     # Concurrent: artisan serve + queue:listen + pail + vite dev
composer run test    # config:clear + artisan test
npm run build        # vite build + vite build --ssr
```

## Architecture

### Route Groups

| Group | Prefix | Middleware stack |
|-------|--------|-----------------|
| Public | `/` | none |
| Admin | `/admin` | `auth, active, verified, two-factor` + `permission:X` per route |
| Client portal | `/portal` | `auth, active, two-factor, role:Cliente` |
| 2FA challenge | `/two-factor` | `auth, active` |

### Directory Layout

```
app/Http/Controllers/Admin/   # Admin CRUD controllers
app/Http/Controllers/Client/  # Client-scoped portal controllers
app/Http/Controllers/Auth/    # Breeze + TwoFactorController
app/Models/                   # Eloquent models
resources/js/Pages/Admin/     # Inertia admin pages
resources/js/Pages/Client/    # Inertia client portal pages
resources/js/Layouts/         # AdminLayout, ClientLayout, GuestLayout
resources/js/Components/      # Shared UI: ConfirmModal, FlashMessages, Pagination, SearchInput, StatusBadge
```

### Global Inertia Shared Props (via `HandleInertiaRequests`)

```js
page.props.auth.user          // authenticated user
page.props.auth.roles         // role name array
page.props.auth.permissions   // all permission slugs
page.props.flash.success      // success toast
page.props.flash.error        // error toast
page.props.ziggy              // Ziggy route helper
```

## Conventions

### Inertia Controller Pattern

Always follow this structure when returning Inertia responses:

```php
return Inertia::render('Admin/Section/PageName', [
    'records' => $query->paginate(15)->withQueryString(),
    'filters' => $request->only(['search', 'status']),
    'statuses' => Model::STATUSES,
]);
```

- Paginate with **15 items** and always call `withQueryString()`
- Pass request filters back as a `filters` prop
- Flash with `->with('success', '...')` or `->with('error', '...')`

### Permissions & Roles

- Roles: `Super Admin`, `Admin`, `Vendedor`, `Cliente`
- Route guard: `middleware('permission:resource.action')` — e.g., `users.view`, `quotations.create`
- Client-only routes: `middleware('role:Cliente')`
- Check permissions in Vue via `page.props.auth.permissions` (already filtered in `AdminLayout` sidebar)

### 2FA

- **Staff/Admin**: TOTP (Google Authenticator, `pragmarx/google2fa-laravel`); must complete setup before access
- **Clients**: Email OTP, auto-confirmed on account creation — no setup step
- Session flag: `two_factor_verified` enforced by `EnsureTwoFactorVerified` middleware

### Models — Key Business Objects

| Model | Notable features |
|-------|-----------------|
| `Quotation` | Statuses: `draft→sent→accepted/rejected/expired/converted`; number: `ALTAVOX-YYYY-MM####`; `recalculate()` recomputes totals |
| `Contract` | Statuses: `pending→active→completed/cancelled`; number: `C-ALTAVOX-YYYY-MM####`; `totalPaid()`, `balance()`; 10 default Spanish legal clauses |
| `Event` | Belongs to Contract; statuses with color mapping |
| `Package` | Auto-slugged; `belongsToMany EventType` |
| `Setting` | Key-value store — use `Setting::get('key', $default)` |
| `User` | Spatie `HasRoles`; 2FA columns (`two_factor_type`, `two_factor_secret`, etc.) |

## Pitfalls

- **SSR build is required** for production — run `npm run build` (runs both client and SSR builds).
- **2FA middleware is NOT optional for admin routes** — never omit `two-factor` from the admin middleware stack.
- **`is_active` check** — users can be deactivated; the `EnsureUserIsActive` middleware blocks inactive users at login.
- **Permissions are seeded, not hardcoded** — adding a new `permission:X` in routes requires seeding the permission via `PermissionSeeder` and assigning it to the appropriate roles.
- **Test DB is in-memory SQLite** (`phpunit.xml`) — avoid MySQL-specific syntax in migrations.
