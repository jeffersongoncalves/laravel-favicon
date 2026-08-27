## Laravel Favicon

### Overview
Laravel Favicon registers root-level routes that serve a `favicon.ico` and a `browserconfig.xml` for Windows tiles, plus helpers for the Apple touch icon and PNG icon `<link>` tags.

**Namespace:** `JeffersonGoncalves\Favicon`
**Service Provider:** `FaviconServiceProvider` (auto-discovered)

### Key Concepts
- **Config-driven:** Everything is read from `config('favicon.*')` (published from `config/favicon.php`).
- **Enable flag:** `favicon.enabled` is a master switch — when false, no routes are registered.
- **Routes in packageBooted():** The provider calls `Favicon::routes()` from `packageBooted()`, guarded by the enabled flag.
- **Vite assets:** Icon URLs are resolved via `Vite::asset(...)`, so the PNGs must live under the consuming app's `resources/favicon/` and be part of its Vite build.

### Registered Routes

| Route | Content-Type | Registered when |
|-------|--------------|-----------------|
| `GET /browserconfig.xml` | `application/xml` | `favicon.enabled` is true |
| `GET /favicon.ico` | `image/x-icon` | enabled AND `favicon.favicon` is set |

### Configuration
- `enabled` — master switch for both routes.
- `favicon` — Vite-resolvable path to the `favicon.ico`; empty/null skips the route.
- `browserconfig_url` — URL emitted in the `msapplication-config` meta of the head view.
- `tile_color` — Windows pinned-tile colour (msapplication-TileColor meta + browserconfig.xml TileColor).

### Apple Touch Icons

@verbatim
<code-snippet name="apple-head-links" lang="php">
use JeffersonGoncalves\Favicon\Favicon;

@foreach (Favicon::appleHeadLinks() as $link)
    <link rel="{{ $link['rel'] }}" sizes="{{ $link['sizes'] }}" href="{{ $link['href'] }}">
@endforeach
</code-snippet>
@endverbatim

### Conventions
- Read all settings via `config('favicon.*')`, never hard-code icon paths.
- `Favicon` is an abstract class of static methods (`routes()`, `appleHeadLinks()`, `iconHeadLinks()`, `msApplicationMeta()`); it is not instantiated.
- Icon PNGs are the consuming app's responsibility under `resources/favicon/`.
- `laravel-pwa-favicon` builds on top of this package to add the PWA `manifest.json` — do not duplicate icon-link logic there.
