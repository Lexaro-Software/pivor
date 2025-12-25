# Architecture

Pivor uses a modular architecture built on Laravel 12 and Livewire 3.

## Project Structure

```
pivor/
├── app/
│   ├── Livewire/           # Global Livewire components
│   ├── Models/             # Core models (User)
│   └── Modules/            # Feature modules
│       ├── Clients/
│       ├── Contacts/
│       └── Communications/
├── database/
│   ├── factories/          # Test factories
│   ├── migrations/         # Core migrations
│   └── seeders/            # Database seeders
├── resources/
│   ├── css/                # Tailwind CSS
│   └── views/              # Blade templates
├── routes/
│   └── web.php             # Core routes
└── tests/
    ├── Feature/            # Feature tests
    └── Unit/               # Unit tests
```

## Module Structure

Each module follows a consistent structure:

```
Modules/Clients/
├── Controllers/            # HTTP controllers (if needed)
├── Livewire/               # Livewire components
│   ├── ClientForm.php
│   ├── ClientList.php
│   └── ClientShow.php
├── Models/
│   └── Client.php
├── Providers/
│   └── ClientsServiceProvider.php
├── database/
│   └── migrations/
├── resources/
│   └── views/
│       └── livewire/
└── routes/
    └── web.php
```

## Service Providers

Each module has a service provider that:

1. Loads views from the module's resources
2. Loads migrations from the module's database
3. Registers routes with auth middleware
4. Registers Livewire components

Example:

```php
Route::middleware(['web', 'auth'])
    ->prefix('clients')
    ->name('clients.')
    ->group(__DIR__ . '/../routes/web.php');
```

## Livewire Components

All forms use Livewire for real-time validation:

```php
protected function rules(): array
{
    return [
        'name' => 'required|string|max:255',
        'email' => 'nullable|email|max:255',
        // ...
    ];
}
```

## Models

Models use:

- `HasFactory` for testing
- `SoftDeletes` for safe deletion
- UUID generation on creation
- Scopes for common queries
- Accessors for computed attributes

## Frontend

- **Tailwind CSS 4** with class-based dark mode
- **Alpine.js** bundled with Livewire 3
- **Blade components** for reusable UI

---

[Back to Documentation](../README.md)
