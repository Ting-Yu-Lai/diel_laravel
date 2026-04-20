# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
# Start dev server (all services in parallel: HTTP + queue + logs + Vite)
composer run dev

# Or individually
php artisan serve
npm run dev

# Database
php artisan migrate
php artisan migrate:fresh --seed

# Run all tests
php artisan test
# Run a single test file
php artisan test tests/Feature/ExampleTest.php
# Or with PHPUnit directly
./vendor/bin/phpunit --filter TestClassName

# Code style (Laravel Pint)
./vendor/bin/pint
./vendor/bin/pint --test   # dry run

# Clear caches
php artisan optimize:clear
```

---

## Architecture

### Three-Layer Pattern (Controller → Service → Repository)

Every domain feature follows this strict layering:

| Layer | Responsibility | ORM allowed? |
|---|---|---|
| **Repository** | All SQL / Eloquent queries | ✅ Yes |
| **Service** | Business logic coordination | ❌ No ORM |
| **Controller** | HTTP handling, lightweight form data queries | ✅ Direct Model use is OK |

**Service rule**: Services call Repository methods only. They never call `Model::create()`, query builders, or access relations directly. If a Service needs a relation value (e.g. a name for a delete log), the Controller fetches it first and passes it as a parameter.

**BaseRepository** (`app/Repositories/BaseRepository.php`) provides `all / find / findBy / create / update / delete`. Every Repository extends it and adds domain-specific query methods.

### Delete Logging Pattern

All destructive operations require:
1. `power == 1` permission check (`Session::get('power') != 1`)
2. A `reason` field (validated `required|string|max:500`)
3. An entry written to the corresponding `*_delete_logs` table via the Repository

### Admin Authentication

Custom session-based auth — **not** Laravel Sanctum/Breeze.

- Login stores `admin_id`, `power`, and `full_name` in the session.
- All backend routes are protected by `App\Http\Middleware\AdminAuth`.
- **`power == 1`**: superadmin — can delete records, manage admins, manage job titles.
- **`power != 1`**: regular admin — read/write most data, cannot delete.
- Check permission with `Session::get('power')` and `Session::get('admin_id')`.

### Route Conventions

All backend routes live under the `/backend` prefix with `AdminAuth` middleware. Named routes follow `backend.{resource}.{action}`.

**Important ordering rule**: JSON API routes (e.g. `customer/search-json`, `staff/search-json`, `treatment/by-category-json`) must be declared **before** their corresponding `Route::resource(...)` to prevent the `{model}` wildcard from capturing them.

Nested resource example: `treatment-record/{recordId}/item/{item}` — the outer param is always named `{recordId}`.

### JSON APIs for Autocomplete

Three search endpoints return `[{id, name, ...}]` JSON:
- `GET /backend/customer/search-json?q=` — name or phone
- `GET /backend/staff/search-json?role=doctor|nurse&q=` — filter by job title keyword
- `GET /backend/treatment/by-category-json?category_id=` — active treatments in a category

Frontend uses these with a consistent pattern: a visible text input + hidden `<input name="...">` that receives the selected ID, with 280 ms debounce and click-outside-to-close.

### Frontend Stack

- **Backend views**: Bootstrap 5.3 and Font Awesome 6 loaded from CDN — no build step needed for backend UI.
- **JavaScript**: Vanilla JS only (no Vue, Alpine, or Livewire).
- **Vite / Tailwind**: configured but used for the public-facing frontend, not the backend admin.
- Backend layout: `resources/views/backend/layouts/app.blade.php` with `@yield('content')`.

### Aggregate Recalculation

`TreatmentRecord` stores denormalised totals (`total_amount`, `total_cost`, `total_profit`, `item_count`). After every create/update/delete of a `TreatmentRecordItem`, `TreatmentRecordItemService::syncRecordTotals()` calls `TreatmentRecordItemRepository::sumByRecord()` and writes back via `TreatmentRecordRepository::update()`.
