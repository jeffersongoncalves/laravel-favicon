---
name: favicon-development
description: Development guide for laravel-favicon, a package that serves a favicon.ico route, a browserconfig.xml, and Apple/PNG touch icon head links from config-driven, Vite-resolved icon assets.
---

# Favicon Development Skill

## When to use this skill

- When developing or extending the laravel-favicon package
- When adding or modifying the registered routes
- When adjusting the Apple touch icon or Windows tile output
- When writing tests for the browserconfig / favicon endpoints
- When debugging Vite asset resolution for the icon paths

## Setup

### Requirements
- PHP 8.2+
- Laravel 11, 12, or 13
- `spatie/laravel-package-tools` ^1.14

### Installation

```bash
composer require jeffersongoncalves/laravel-favicon
```

Publish the config:

```bash
php artisan vendor:publish --tag="favicon-config"
```

## Package Structure

```
src/
  FaviconServiceProvider.php   # configurePackage()->name('laravel-favicon')->hasConfigFile()
                                # packageBooted() registers routes when enabled
  Favicon.php                  # abstract class of static methods:
                                #   routes()             -> registers the 2 routes
                                #   appleHeadLinks()      -> apple-touch-icon <link> data
                                #   iconHeadLinks()       -> PNG icon <link> data
                                #   msApplicationMeta()   -> msapplication-* metas
                                #   tileColor()           -> configured tile colour
                                #   getBrowserConfigXml() -> /browserconfig.xml (private)
                                #   getFavicon()          -> /favicon.ico (private)
config/
  favicon.php                  # enabled, favicon, browserconfig_url, tile_color
```

## How It Works

### Service Provider

`hasConfigFile()` (no argument) resolves to the package short name — `laravel-favicon` with the `laravel-` prefix stripped — so the file is `config/favicon.php` and the config key is `favicon`.

Routes are registered from `packageBooted()`, guarded by the enabled flag:

```php
public function packageBooted(): void
{
    if (config('favicon.enabled', false)) {
        Favicon::routes();
    }
}
```

`Favicon::routes()` also re-checks the flag, so it is safe to call directly.

### Content types

- `/browserconfig.xml` → `application/xml`.
- `/favicon.ico` → `image/x-icon` (served via `Vite::content(...)`).

## Testing Patterns

The icon paths flow through `Vite::asset()`, which has no compiled manifest in tests — swap the Vite resolver for a fake.

```php
use Illuminate\Foundation\Vite as FoundationVite;
use Illuminate\Support\Facades\Vite;

beforeEach(function () {
    $vite = Mockery::mock(FoundationVite::class);
    $vite->shouldReceive('asset')->andReturnUsing(fn (string $p) => 'https://cdn.test/'.$p);
    $vite->shouldReceive('content')->andReturnUsing(fn (string $p) => 'fake');
    Vite::swap($vite);
});
```

Keep the routes off at boot (set `favicon.enabled` false in `getEnvironmentSetUp`) and opt in per test:

```php
use JeffersonGoncalves\Favicon\Favicon;

it('serves browserconfig.xml', function () {
    config()->set('favicon.enabled', true);
    Favicon::routes();

    $response = $this->get('/browserconfig.xml');

    $response->assertOk();
    expect($response->headers->get('Content-Type'))->toContain('application/xml');
});

it('404s every route when disabled', function () {
    config()->set('favicon.enabled', false);
    $this->get('/browserconfig.xml')->assertNotFound();
    $this->get('/favicon.ico')->assertNotFound();
});
```

### Running Tests

```bash
# Run all tests
vendor/bin/pest

# Run with coverage
vendor/bin/pest --coverage

# Static analysis
vendor/bin/phpstan analyse

# Code formatting
vendor/bin/pint
```

## Adding a New Icon Variant

1. Add the size to `appleHeadLinks()` or `iconHeadLinks()` in `Favicon.php`.
2. Ship the matching asset under `resources/favicon/` in the consuming app.
3. Cover the new entry in the head-links test via `toContain([...])`.
