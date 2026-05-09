# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**QuickBite** is a multi-tenant SaaS restaurant management platform built on Laravel 13. Each restaurant (tenant) gets a completely isolated database. The platform manages orders, menus, tables, staff, and payments with real-time updates via WebSockets.

## Common Commands

### Development
```bash
composer dev          # Start everything in parallel: PHP server, queue, log tail, Vite
composer setup        # First-time setup: install deps, generate keys, migrate, build assets
npm run build         # Production Vite build
```

### Database
```bash
php artisan migrate                    # Central DB migrations
php artisan tenants:migrate            # All tenant DBs
php artisan tenants:migrate --tenants=<id>  # Specific tenant
php artisan db:seed                    # Central seeders
php artisan tenants:seed               # All tenant seeders
```

### Testing & Linting
```bash
composer test         # Clear config cache, then run PHPUnit
php artisan pint      # Lint and auto-fix PHP code
```

### Real-time Server
```bash
php artisan reverb:start   # Start WebSocket server (required for order broadcasts)
```

## Architecture

### Multi-Tenancy (Stancl Tenancy v3)
Tenant isolation is database-level. Each restaurant gets its own MySQL database named `tenant_{uuid}`.

- **Tenant identification**: Domain-based via [TenantMiddleware](app/Http/Middleware/TenantMiddleware.php) — looks up tenant by `$request->getHost()`
- **DB switching**: `DatabaseTenancyBootstrapper` swaps the active connection on every tenant request
- **Central DB** (`quickbite`): users, tenants, domains, teams, Sanctum tokens
- **Tenant DBs** (`tenant_{uuid}`): roles, tenant_users, categories, products, tables, orders, order_items, transactions, settings, cash_registers
- **Tenant migrations**: must go in [database/migrations/tenant/](database/migrations/tenant/) to run via `tenants:migrate`

### Authentication (Two Systems)
1. **Central users** — platform owners using Laravel Fortify + Jetstream. Standard `Auth::user()`.
2. **Tenant staff** — restaurant employees stored in the tenant DB as `TenantUser`. Authenticated via session: `session('tenant_user')`. Protected by `AuthenticateTenant` middleware (alias: `auth.tenant`).

### Routes
- [routes/web.php](routes/web.php) — central auth + super admin dashboard
- [routes/tenant.php](routes/tenant.php) — all restaurant-scoped routes (auto-wrapped with tenant middleware)
- [routes/api.php](routes/api.php) — Sanctum token-based API
- [routes/channels.php](routes/channels.php) — broadcasting channel definitions

### Role-Based Access (Tenant Staff)
Roles defined in tenant `roles` table: `admin`, `waiter`, `cashier`, `delivery_person`. `TenantUser` belongs to one role. Route groups in [routes/tenant.php](routes/tenant.php) scope by role. Views are organized by role: `tenant/admin/`, `tenant/caja/`, `tenant/mesero/`, `tenant/domiciliario/`, `tenant/client/`.

### Real-time Order Broadcasting
Orders broadcast via Reverb (Laravel's native WebSocket server). Events:
- `NewOrderReceived` — when a new order is placed
- `OrderStatusUpdated` — when order status changes

Channel: `orders.{tenant_id}` (tenant-scoped). Frontend subscribes via [resources/js/echo.js](resources/js/echo.js) using Laravel Echo + Pusher protocol.

### Order Lifecycle
`pending → confirmed → preparing → ready → dispatched → delivered` (or `cancelled`).
Order types: `table` (dine-in) and `delivery`. [OrderController](app/Http/Controllers/Tenant/OrderController.php) is the largest file (~855 lines) and handles the full lifecycle.

### Settings System
Tenant configuration is a key-value store in the tenant `settings` table. Keys include restaurant name, logo path, opening hours, bank details, delivery options. Accessed via [SettingsController](app/Http/Controllers/Tenant/SettingsController.php).

## Key Technical Details

- **PHP 8.3+**, **Laravel 13**, **Vite 8**, **Tailwind CSS 3.4**
- **DB**: MySQL. Default connection is `central`. Tenant connection is `tenant` (dynamic database name injected by bootstrapper).
- **Tests** use SQLite in-memory (`phpunit.xml` overrides DB connection). Tenant tests need to initialize tenancy manually.
- **Broadcasting**: Reverb is the active driver (`BROADCAST_CONNECTION=reverb`). Pusher SDK is also installed but used only as a protocol implementation underneath Reverb.
- **Assets**: Vite entry points are `resources/css/app.css` and `resources/js/app.js`. HMR runs on port 5173.
- **Local dev**: XAMPP-based. `APP_URL` should match the domain being used for tenant routing.

## Adding Tenant Features

1. Create migration in `database/migrations/tenant/` (not the root migrations folder)
2. Run `php artisan tenants:migrate` to apply to all tenants
3. Add controller in `app/Http/Controllers/Tenant/`
4. Add routes in `routes/tenant.php` within the appropriate role group
5. Add views in `resources/views/tenant/`
