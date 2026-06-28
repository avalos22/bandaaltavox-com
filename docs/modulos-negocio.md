# Documentación de Módulos de Negocio

> Stack: Laravel 11 + Inertia.js + Vue 3 + Spatie Permission v7  
> Propósito: Explicar cómo funcionan los módulos de **Paquetes**, **Cotizaciones**, **Contratos** y **Cobranza** para replicar este comportamiento en otros proyectos.

---

## Índice

1. [Visión general del flujo](#1-visión-general-del-flujo)
2. [Módulo: Catálogo (Paquetes y Add-ons)](#2-módulo-catálogo-paquetes-y-add-ons)
3. [Módulo: Cotizaciones](#3-módulo-cotizaciones)
4. [Módulo: Contratos](#4-módulo-contratos)
5. [Módulo: Cobranza (Pagos)](#5-módulo-cobranza-pagos)
6. [Portal del Cliente](#6-portal-del-cliente)
7. [Permisos por rol](#7-permisos-por-rol)
8. [Configuración global relevante](#8-configuración-global-relevante)
9. [Diagrama de relaciones entre modelos](#9-diagrama-de-relaciones-entre-modelos)

---

## 1. Visión general del flujo

El flujo comercial completo sigue esta secuencia lineal:

```
[Catálogo de paquetes/add-ons]
         ↓
[Cotización — el vendedor o cliente arma una propuesta]
         ↓
[Aprobación — la cotización pasa a estado "aceptada"]
         ↓
[Conversión — la cotización se "convierte" en Contrato]
         ↓  (crea automáticamente Usuario Cliente + Evento en agenda)
[Contrato activo — se registran pagos contra el contrato]
         ↓
[Cobranza — el módulo de pagos rastrea saldo pendiente vs. cobrado]
```

Cada paso tiene su propio estado controlado y solo puede avanzar o retroceder dentro de sus estados definidos.

---

## 2. Módulo: Catálogo (Paquetes y Add-ons)

### 2.1 Entidades del catálogo

El catálogo está compuesto por dos tipos de productos que se reutilizan en las cotizaciones:

| Entidad | Tabla | Propósito |
|---|---|---|
| `Package` | `packages` | Paquete principal (e.g. "Paquete Altavox 5 hrs") |
| `PackageInclude` | `package_includes` | Lista de beneficios/ítems incluidos en un paquete |
| `ServiceAddon` | `service_addons` | Servicio adicional vendible por separado (e.g. "Pantalla LED 3x2") |
| `EventType` | `event_types` | Tipo de evento (e.g. "Boda", "XV Años") — clasifica paquetes |

### 2.2 Schema: `packages`

```sql
id              bigint  PK
name            string              -- Nombre comercial
slug            string  unique       -- Auto-generado desde el nombre (Str::slug)
description     text    nullable
price           decimal(10,2)        -- Precio base
duration_hours  integer default 5    -- Horas de servicio incluidas
required_addon_subcategory string nullable  -- Obliga a elegir un add-on de esa subcategoría
image           string  nullable     -- Ruta en storage/public
is_active       boolean default true
is_featured     boolean default false
sort_order      integer default 0
```

Tabla pivot `event_type_package` (muchos a muchos):

```sql
event_type_id   FK → event_types.id  CASCADE DELETE
package_id      FK → packages.id     CASCADE DELETE
UNIQUE(event_type_id, package_id)
```

### 2.3 Schema: `package_includes`

```sql
id              bigint  PK
package_id      FK → packages.id  CASCADE DELETE
description     string            -- Texto del beneficio incluido
is_highlighted  boolean default false  -- Se muestra resaltado en la UI
sort_order      integer default 0
```

### 2.4 Schema: `service_addons`

```sql
id              bigint  PK
name            string
category        string   -- Clave del array CATEGORIES (e.g. "audio")
subcategory     string   nullable  -- Clave del array SUBCATEGORIES
description     text     nullable
price           decimal(10,2)   -- Precio que se cobra al cliente
supplier_price  decimal(10,2)   -- Costo con proveedor (margen interno)
unit            string   nullable  -- Unidad ("servicio", "pieza", etc.)
duration        string   nullable  -- Duración estándar
is_active       boolean default true
sort_order      integer default 0
```

Categorías y subcategorías están definidas como constantes en el modelo:

```php
// ServiceAddon::CATEGORIES
[
    'audio'                  => 'Audio',
    'pantallas_video'        => 'Pantallas y Video',
    'iluminacion'            => 'Iluminación',
    'efectos_especiales'     => 'Efectos Especiales',
    'mobiliario'             => 'Mobiliario',
    'entretenimiento'        => 'Entretenimiento',
    'produccion_estructuras' => 'Producción / Estructuras',
    'produccion_logistica'   => 'Producción / Logística',
    'video'                  => 'Video',
]

// ServiceAddon::SUBCATEGORIES  (map category → [subcategorías])
[
    'efectos_especiales' => ['Confeti / CO₂', 'Niebla', 'Pirotecnia fría'],
    'mobiliario'         => ['Decoración', 'Pistas de baile', 'Sillas', 'Mesas'],
    // ...
]
```

### 2.5 Lógica del catálogo

**Auto-slug al crear:**  
El modelo `Package` tiene un boot hook que genera el slug automáticamente si no se provee uno:

```php
static::creating(function (Package $package) {
    if (empty($package->slug)) {
        $package->slug = Str::slug($package->name);
    }
});
```

**Relación paquete ↔ tipo de evento:**  
Un paquete puede estar recomendado para N tipos de evento. En el wizard de cotización, los paquetes se filtran/sugieren según el tipo de evento seleccionado.

**`required_addon_subcategory`:**  
Campo opcional en `Package`. Si está definido, la UI obliga al usuario a seleccionar al menos un add-on de esa subcategoría al incluir el paquete en una cotización (e.g. un paquete de "solo banda" obliga a elegir un sistema de audio).

**Imagen del paquete:**  
Se sube y elimina con endpoints dedicados:
- `POST /admin/packages/{package}/upload-image` — guarda en `storage/app/public/packages/`
- `DELETE /admin/packages/{package}/delete-image` — elimina el archivo y limpia el campo

**Includes (beneficios):**  
Al actualizar un paquete se hace `delete()` de todos los includes existentes y se recrean desde cero. No se usan `sync()` ni IDs en el array de includes porque son simples textos ordenados.

### 2.6 Rutas del catálogo

```
GET    /admin/packages              packages.index    (permission:packages.view)
GET    /admin/packages/create       packages.create   (permission:packages.create)
POST   /admin/packages              packages.store    (permission:packages.create)
GET    /admin/packages/{id}/edit    packages.edit     (permission:packages.edit)
PUT    /admin/packages/{id}         packages.update   (permission:packages.edit)
POST   /admin/packages/{id}/upload-image  packages.upload-image  (permission:packages.edit)
DELETE /admin/packages/{id}/delete-image  packages.delete-image (permission:packages.edit)
DELETE /admin/packages/{id}         packages.destroy  (permission:packages.delete)

GET    /admin/addons                addons.index      (permission:packages.view)
GET    /admin/addons/create         addons.create     (permission:packages.create)
POST   /admin/addons                addons.store      (permission:packages.create)
GET    /admin/addons/{id}/edit      addons.edit       (permission:packages.edit)
PUT    /admin/addons/{id}           addons.update     (permission:packages.edit)
DELETE /admin/addons/{id}           addons.destroy    (permission:packages.delete)
```

> Los add-ons comparten el permiso `packages.*` porque forman parte del mismo catálogo.

---

## 3. Módulo: Cotizaciones

### 3.1 Concepto

Una cotización es una **propuesta de servicio** que puede ser creada por:
- Un **administrador/vendedor** desde el panel admin (con más libertad: ítems custom, descuentos)
- El **cliente** desde su portal (solo puede agregar paquetes y add-ons del catálogo activo)

La cotización almacena una **foto instantánea** de los datos del cliente y el evento en el momento de su creación. No tiene FK a un usuario cliente — solo guarda `client_email` como referencia.

### 3.2 Estados de una cotización

```
draft ──→ sent ──→ accepted ──→ converted  (cierre positivo)
                 └──→ rejected              (cierre negativo)
          └──→ expired                      (cierre por tiempo)
```

| Estado | Descripción |
|---|---|
| `draft` | Borrador inicial, recién creada |
| `sent` | Enviada al cliente para revisión |
| `accepted` | El cliente la aceptó verbalmente |
| `rejected` | El cliente la rechazó |
| `expired` | Venció la fecha de validez |
| `converted` | Se generó un contrato a partir de ella |

Los estados se definen en la constante `Quotation::STATUSES` y se actualizan vía `PATCH /admin/quotations/{id}/status`.

### 3.3 Schema: `quotations`

```sql
id                bigint  PK
quotation_number  string  unique    -- Formato: ALTAVOX-2026-030001
status            enum('draft','sent','accepted','rejected','expired','converted')

-- Datos del cliente (snapshot, no FK a users)
client_name       string
client_email      string  nullable
client_phone      string  nullable
client_address    string  nullable

-- Datos del evento
event_type_id     FK → event_types.id  NULL ON DELETE
event_date        date    nullable
event_time_start  string  nullable   -- "21:30"
event_time_end    string  nullable   -- "01:00"
event_venue       string  nullable
event_city        string  nullable
event_is_outdoor  boolean default false
guest_count       integer nullable
hours_contracted  integer default 5

-- Financiero
subtotal          decimal(10,2) default 0
discount          decimal(10,2) default 0
total             decimal(10,2) default 0   -- subtotal - discount

-- Metadata
observations      text    nullable
validity_days     integer default 15
expires_at        date    nullable
created_by        FK → users.id  NULL ON DELETE
converted_at      timestamp nullable
```

### 3.4 Schema: `quotation_items`

```sql
id            bigint  PK
quotation_id  FK → quotations.id  CASCADE DELETE
type          enum('package','addon','custom')  -- origen del ítem
reference_id  bigint  nullable  -- packages.id o service_addons.id
description   string            -- texto visible en la cotización
quantity      integer default 1
unit_price    decimal(10,2) default 0
total         decimal(10,2) default 0   -- quantity * unit_price
sort_order    integer default 0
```

### 3.5 Numeración automática

```php
// Formato: ALTAVOX-{año}-{mes}{secuencia_4_dígitos}
// Ejemplo: ALTAVOX-2026-030001, ALTAVOX-2026-030002, ...

public static function generateNumber(): string
{
    $year = date('Y');
    $month = date('m');
    $last = static::where('quotation_number', 'like', "ALTAVOX-{$year}-{$month}%")
        ->orderByDesc('quotation_number')
        ->value('quotation_number');

    $sequence = 1;
    if ($last) {
        $seqStr = substr($last, strlen("ALTAVOX-{$year}-{$month}"));
        $sequence = (int) $seqStr + 1;
    }
    return sprintf('ALTAVOX-%s-%s%04d', $year, $month, $sequence);
}
```

La secuencia **reinicia cada mes** porque el prefijo incluye año y mes.

### 3.6 Recálculo de totales

El método `recalculate()` del modelo recalcula `subtotal` y `total` desde los ítems:

```php
public function recalculate(): void
{
    $subtotal = $this->items()->sum('total');
    $this->update([
        'subtotal' => $subtotal,
        'total'    => $subtotal - $this->discount,
    ]);
}
```

Se llama explícitamente después de guardar los ítems, tanto en `store` como al editar ítems.

### 3.7 Wizard de cotización (frontend)

La página `Admin/Quotations/Wizard` (y su equivalente `Client/QuotationWizard`) recibe:

```js
{
  eventTypes:          [...],   // tipos de evento activos
  packages:            [...],   // paquetes activos con sus includes
  addons:              [...],   // add-ons activos
  addonCategories:     {...},   // mapa clave → etiqueta
  addonSubcategories:  {...},   // mapa categoría → [subcategorías]
  defaults: {
    validity_days:      15,     // leído de Setting
    deposit_percentage: 30,     // para calcular anticipo sugerido
  }
}
```

El wizard maneja un array `items` en estado local. Cada ítem tiene `type`, `reference_id`, `description`, `quantity`, `unit_price`. Los ítems de tipo `custom` solo están disponibles desde el panel admin.

### 3.8 Diferencias Admin vs. Cliente al crear cotización

| Aspecto | Admin/Vendedor | Cliente |
|---|---|---|
| Puede agregar ítems `custom` | ✅ Sí | ❌ No |
| Puede aplicar descuento | ✅ Sí | ❌ No |
| Datos del cliente | Los ingresa manualmente | Se toman del `auth()->user()` |
| Validación de fecha | Sin restricción | `'after:today'` obligatorio |
| Ruta | `POST /admin/quotations` | `POST /portal/quotations` |

### 3.9 Conversión a contrato

Desde la vista `Admin/Quotations/Show`, el administrador puede convertir la cotización a contrato. Esto se hace mediante `POST /admin/contracts` enviando:

```json
{
  "quotation_id": 5,
  "first_payment_amount": 12000,
  "first_payment_date": "2026-05-14",
  "clauses": ["Cláusula I...", "Cláusula II..."],
  "representative_name": "Cristian García G.",
  "observations": ""
}
```

> Ver sección 4 (Contratos) para el detalle de este proceso.

### 3.10 Rutas de cotizaciones

```
GET    /admin/quotations              quotations.index     (permission:quotations.view)
GET    /admin/quotations/create       quotations.create    (permission:quotations.create)
POST   /admin/quotations              quotations.store     (permission:quotations.create)
GET    /admin/quotations/{id}         quotations.show      (permission:quotations.view)
PATCH  /admin/quotations/{id}/status  quotations.update-status  (permission:quotations.edit)
DELETE /admin/quotations/{id}         quotations.destroy   (permission:quotations.delete)
```

---

## 4. Módulo: Contratos

### 4.1 Concepto

Un contrato es el documento legal generado al **cerrar una venta**. Se crea siempre a partir de una cotización existente y hereda todos los datos del evento y del cliente como **snapshot inmutable**. El contrato no se edita; su estado y sus pagos cambian, pero los datos del cliente/evento quedan fijos.

### 4.2 Estados de un contrato

```
pending ──→ active ──→ completed
                  └──→ cancelled
```

| Estado | Descripción |
|---|---|
| `pending` | Creado pero sin confirmar |
| `active` | Vigente; se están recibiendo pagos |
| `completed` | Servicio prestado y cobrado |
| `cancelled` | Cancelado |

### 4.3 Schema: `contracts`

```sql
id                    bigint  PK
contract_number       string  unique   -- Formato: C-ALTAVOX-2026-030001
quotation_id          FK → quotations.id  CASCADE DELETE
client_id             FK → users.id  NULL ON DELETE  -- Usuario creado al convertir
status                enum('pending','active','completed','cancelled') default 'pending'
contract_date         date                 -- Fecha de firma

-- Snapshot del cliente
client_name           string
client_email          string  nullable
client_phone          string  nullable
client_address        string  nullable

-- Snapshot del evento
event_type_label      string  nullable     -- Texto (no FK, snapshot)
event_date            date    nullable
event_time_start      string  nullable
event_time_end        string  nullable
event_venue           string  nullable
hours_contracted      integer default 5
event_is_outdoor      boolean default false

-- Financiero
total_amount          decimal(10,2)        -- Total de la cotización
first_payment_amount  decimal(10,2) default 0
first_payment_date    date  nullable

-- Contenido del contrato
observations          text    nullable
clauses               json    nullable     -- Array de strings con las cláusulas
representative_name   string               -- Nombre del representante de la empresa

-- Firma
signed_at             timestamp nullable
notes                 text      nullable
```

### 4.4 Proceso de conversión (cotización → contrato)

El método `ContractController@store` realiza **cuatro operaciones atómicas** en secuencia:

**Paso 1 — Verificar que no exista contrato previo:**
```php
if ($quotation->contract) {
    return back()->with('error', 'Esta cotización ya tiene un contrato.');
}
```

**Paso 2 — Crear o recuperar el usuario cliente:**

Si la cotización tiene `client_email`, se busca o crea un usuario con `firstOrCreate`:
- Contraseña aleatoria de 12 chars (el cliente accede por email OTP)
- `email_verified_at` = ahora
- `two_factor_type` = `'email'` y `two_factor_confirmed_at` = ahora (auto-confirmado)
- Se le asigna el rol `Cliente`

```php
$client = User::firstOrCreate(
    ['email' => $quotation->client_email],
    [
        'name'                    => $quotation->client_name,
        'password'                => Hash::make(Str::random(12)),
        'is_active'               => true,
        'email_verified_at'       => now(),
        'two_factor_type'         => 'email',
        'two_factor_confirmed_at' => now(),
    ]
);
$client->assignRole('Cliente');
```

**Paso 3 — Crear el contrato** con los datos de la cotización como snapshot.

**Paso 4 — Crear el evento en agenda:**

Se crea automáticamente un `Event` con estado `confirmed` usando los datos del evento de la cotización. Esto agrega el evento a la agenda sin intervención adicional.

**Paso 5 — Registrar el anticipo (si > 0):**

Si `first_payment_amount > 0`, se crea el primer `Payment` automáticamente con `notes = 'Anticipo al firmar contrato'`.

**Paso 6 — Marcar la cotización como convertida:**

```php
$quotation->update([
    'status'       => 'converted',
    'converted_at' => now(),
]);
```

Una cotización convertida **no puede volver** a ningún estado anterior.

### 4.5 Cláusulas por defecto

El modelo `Contract` define 10 cláusulas legales en español dentro de la constante `DEFAULT_CLAUSES`. Estas se envían al frontend en la vista `Admin/Quotations/Show` para que el admin pueda editarlas antes de convertir. Las cláusulas se almacenan en la columna `clauses` (tipo JSON) en el contrato, por lo que quedan registradas tal y como se aceptaron.

### 4.6 Cálculo de saldo

El modelo tiene dos métodos calculados (no columnas en DB):

```php
public function totalPaid(): float
{
    return (float) $this->payments()->sum('amount');
}

public function balance(): float
{
    return (float) $this->total_amount - $this->totalPaid();
}
```

Estos se calculan en tiempo real y se pasan al frontend como props (`totalPaid`, `balance`).

### 4.7 Rutas de contratos

```
GET    /admin/contracts              contracts.index         (permission:contracts.view)
POST   /admin/contracts              contracts.store         (permission:contracts.create)
GET    /admin/contracts/{id}         contracts.show          (permission:contracts.view)
PATCH  /admin/contracts/{id}/status  contracts.update-status (permission:contracts.edit)
```

> No hay ruta de edición directa de contratos. Los datos son snapshot y no se modifican.

---

## 5. Módulo: Cobranza (Pagos)

### 5.1 Concepto

El módulo de cobranza permite registrar y rastrear los pagos recibidos contra cada contrato. No hay facturación automática: cada pago es un registro manual ingresado por un admin o vendedor.

### 5.2 Schema: `payments`

```sql
id           bigint  PK
contract_id  FK → contracts.id  CASCADE DELETE
recorded_by  FK → users.id      NULL ON DELETE  -- Quién registró el pago
amount       decimal(10,2)
method       enum('cash','transfer','card','deposit','other') default 'cash'
payment_date date
reference    string  nullable   -- Número de referencia bancaria, recibo, etc.
notes        text    nullable
```

### 5.3 Métodos de pago

Definidos en `Payment::METHODS`:

| Clave | Etiqueta |
|---|---|
| `cash` | Efectivo |
| `transfer` | Transferencia |
| `card` | Tarjeta |
| `deposit` | Depósito |
| `other` | Otro |

### 5.4 Vista de índice de cobranza

La vista `Admin/Payments/Index` no lista pagos individuales sino **contratos con su resumen de pagos**. Usa eager loading con agregados:

```php
Contract::with(['client', 'event'])
    ->withSum('payments', 'amount')   // → payments_sum_amount
    ->withCount('payments')           // → payments_count
    ->where('status', '!=', 'cancelled')
    ->paginate(15);
```

**Filtros disponibles:**
- `pending` — contratos con saldo pendiente
- `paid` — contratos liquidados (pagos >= total)
- `overdue` — eventos ya pasados con saldo pendiente

**Estadísticas globales en el header:**

```php
$totalContracts  = Contract::where('status', '!=', 'cancelled')->count();
$totalRevenue    = Contract::where('status', '!=', 'cancelled')->sum('total_amount');
$totalCollected  = Payment::sum('amount');
$totalPending    = $totalRevenue - $totalCollected;
```

### 5.5 Registrar un pago

`POST /admin/payments/{contract}` con:

```json
{
  "amount": 15000,
  "method": "transfer",
  "payment_date": "2026-05-14",
  "reference": "SPEI-2024-001",
  "notes": "Segundo pago"
}
```

El `contract_id` y `recorded_by` se añaden en el controlador (no vienen del request).

### 5.6 Eliminación de pagos

`DELETE /admin/payments/{payment}/delete` — elimina el registro. No hay soft delete. Al eliminar un pago el saldo del contrato se recalcula automáticamente en la siguiente consulta (no hay columna de caché).

### 5.7 Rutas de cobranza

```
GET    /admin/payments                  payments.index    (permission:payments.view)
GET    /admin/payments/{contract}       payments.show     (permission:payments.view)
POST   /admin/payments/{contract}       payments.store    (permission:payments.create)
DELETE /admin/payments/{payment}/delete payments.destroy  (permission:payments.delete)
```

---

## 6. Portal del Cliente

El cliente tiene acceso de solo lectura a sus cotizaciones, contratos y pagos, más la capacidad de crear nuevas cotizaciones.

### 6.1 Acceso y seguridad

- Middleware: `auth, active, two-factor, role:Cliente`
- El cliente se autentica con email OTP (no TOTP)
- La autorización en las vistas de cotización se hace por `client_email`:

```php
if ($quotation->client_email !== $request->user()->email) {
    abort(403);
}
```

- La autorización en las vistas de contrato se hace por `client_id`:

```php
if ($contract->client_id !== $request->user()->id) {
    abort(403);
}
```

### 6.2 Rutas del portal

```
GET  /portal/dashboard              client.dashboard
GET  /portal/events                 client.events
GET  /portal/contracts              client.contracts
GET  /portal/contracts/{id}         client.contracts.show
GET  /portal/payments               client.payments
GET  /portal/quotations             client.quotations
GET  /portal/quotations/create      client.quotations.create
POST /portal/quotations             client.quotations.store
GET  /portal/quotations/{id}        client.quotations.show
```

### 6.3 Diferencias del wizard cliente vs. admin

El `ClientQuotationController@store` es casi idéntico al admin pero:
- Usa `$request->user()` para poblar los datos del cliente automáticamente
- No acepta ítems de tipo `custom` (solo `package` y `addon`)
- No acepta `discount`
- La fecha del evento tiene validación `'after:today'`
- Los `reference_id` son requeridos (no nullable como en admin)

---

## 7. Permisos por rol

Los permisos se definen en `database/seeders/RoleSeeder.php` y se registran con Spatie Permission.

| Permiso | Super Admin | Admin | Vendedor | Cliente |
|---|:---:|:---:|:---:|:---:|
| `packages.view` | ✅ | ✅ | ✅ | — |
| `packages.create/edit/delete` | ✅ | ✅ | — | — |
| `quotations.view` | ✅ | ✅ | ✅ | ✅ |
| `quotations.create` | ✅ | ✅ | ✅ | ✅ |
| `quotations.edit` | ✅ | ✅ | ✅ | — |
| `quotations.delete` | ✅ | ✅ | — | — |
| `contracts.view` | ✅ | ✅ | ✅ | ✅ |
| `contracts.create` | ✅ | ✅ | — | — |
| `contracts.edit` | ✅ | ✅ | — | — |
| `contracts.delete` | ✅ | ✅ | — | — |
| `payments.view` | ✅ | ✅ | ✅ | ✅ |
| `payments.create` | ✅ | ✅ | ✅ | — |
| `payments.delete` | ✅ | ✅ | — | — |

> El rol `Cliente` accede a sus datos solo a través del portal (`/portal/*`) con autorización por `client_email` o `client_id`, nunca por permisos de admin.

---

## 8. Configuración global relevante

El modelo `Setting` actúa como un almacén clave-valor:

```php
Setting::get('quotation_validity_days', 15)  // Días de validez por defecto de cotizaciones
Setting::get('deposit_percentage', 30)        // % de anticipo sugerido al convertir a contrato
```

Estos valores se leen en los controladores de cotizaciones y contratos para pre-rellenar valores por defecto, y se pasan al frontend como prop `defaults`.

---

## 9. Diagrama de relaciones entre modelos

```
EventType ──< Package (pivot: event_type_package)
Package ──< PackageInclude

ServiceAddon (tabla independiente, se referencia desde quotation_items.reference_id)

Quotation ──< QuotationItem
Quotation ──> EventType (FK nullable)
Quotation ──> User as creator (FK nullable)
Quotation ──| Contract (hasOne)

Contract ──> Quotation (FK)
Contract ──> User as client (FK nullable)
Contract ──| Event (hasOne)
Contract ──< Payment
```

### Reglas de integridad referencial

| Relación | ON DELETE |
|---|---|
| `quotation_items.quotation_id` | CASCADE |
| `contracts.quotation_id` | CASCADE |
| `contracts.client_id` | SET NULL |
| `payments.contract_id` | CASCADE |
| `payments.recorded_by` | SET NULL |
| `quotations.event_type_id` | SET NULL |
| `quotations.created_by` | SET NULL |
| `event_type_package.*` | CASCADE |
| `package_includes.package_id` | CASCADE |

---

## Guía de replicación

Para replicar estos módulos en otro proyecto, seguir este orden:

1. **Crear las migraciones** en el orden exacto del diagrama de dependencias:  
   `event_types` → `packages` + pivot → `package_includes` → `service_addons` → `quotations` → `quotation_items` → `contracts` → `events` → `payments`

2. **Crear los modelos** con sus constantes (`STATUSES`, `METHODS`, `CATEGORIES`), casts, relaciones y métodos calculados (`recalculate`, `totalPaid`, `balance`, `generateNumber`).

3. **Crear el seeder de permisos** con los slugs `resource.accion` y asignarlos a los roles.

4. **Crear los controladores** siguiendo el patrón Inertia: `index` con `paginate(15)->withQueryString()`, filtros devueltos como prop, flash con `->with('success', ...)`.

5. **Registrar las rutas** en grupos de middleware con el permiso correspondiente por ruta.

6. **Crear las páginas Vue** con el layout correcto (`AdminLayout` o `ClientLayout`), leyendo los props de Inertia.

7. **Configurar `Setting`** con los valores por defecto de `quotation_validity_days` y `deposit_percentage` en el seeder.
