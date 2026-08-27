<div class="filament-hidden">

![Laravel Favicon](https://raw.githubusercontent.com/jeffersongoncalves/laravel-favicon/master/art/jeffersongoncalves-laravel-favicon.png)

</div>

# Laravel Favicon

[![Latest Version on Packagist](https://img.shields.io/packagist/v/jeffersongoncalves/laravel-favicon.svg?style=flat-square)](https://packagist.org/packages/jeffersongoncalves/laravel-favicon)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/jeffersongoncalves/laravel-favicon/run-tests.yml?branch=master&label=tests&style=flat-square)](https://github.com/jeffersongoncalves/laravel-favicon/actions?query=workflow%3Arun-tests+branch%3Amaster)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/jeffersongoncalves/laravel-favicon/fix-php-code-style-issues.yml?branch=master&label=code%20style&style=flat-square)](https://github.com/jeffersongoncalves/laravel-favicon/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/jeffersongoncalves/laravel-favicon.svg?style=flat-square)](https://packagist.org/packages/jeffersongoncalves/laravel-favicon)

This Laravel package serves a `favicon.ico` route, a `browserconfig.xml` for Windows tiles, and Apple/PNG touch icon `<head>` links from config-driven, Vite-resolved icon assets. It's the favicon/icon layer used standalone or underneath [`laravel-pwa-favicon`](https://github.com/jeffersongoncalves/laravel-pwa-favicon), which adds the PWA `manifest.json` on top.

## Installation

You can install the package via composer:

```bash
composer require jeffersongoncalves/laravel-favicon
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="favicon-config"
```

This is the contents of the published config file:

```php
return [
    'enabled' => true,
    'favicon' => 'resources/favicon/favicon.ico',
    'browserconfig_url' => '/browserconfig.xml',
    'tile_color' => '#ffffff',
];
```

> **Heads up — the `/favicon.ico` route.** When `enabled` is `true` and `favicon`
> is set, the package registers a PHP `GET /favicon.ico` route. If you also ship a
> static `public/favicon.ico`, the web server serves the static file first and the
> PHP route is never reached — so the static file always wins. To force the package
> to serve the favicon, remove the static `public/favicon.ico`. To disable the PHP
> route entirely, set `favicon` to `null`.

## Usage

Once installed, the package registers the following routes at the application root (when `favicon.enabled` is `true`):

- `GET /browserconfig.xml` — Windows tile configuration (`application/xml`)
- `GET /favicon.ico` — the favicon (registered only when `favicon.favicon` is set)

### The `favicon::head` Blade view

Renders every icon-related `<head>` tag — PNG icon links, Apple touch icons,
`msapplication-*` tile metas, and the `msapplication-config` meta — in one
place:

```blade
<head>
    {{-- ... --}}
    @include('favicon::head')
</head>
```

`browserConfigUrl` is the only param, defaulting to `config('favicon.browserconfig_url')`:

```blade
@include('favicon::head', ['browserConfigUrl' => '/browserconfig.xml'])
```

### Icon assets

Icon URLs are resolved through Vite (`Vite::asset(...)`), so the PNGs must live in your application under `resources/favicon/` and be part of your Vite build. The package expects these files:

```
resources/favicon/
  android-icon-192x192.png
  favicon-32x32.png
  favicon-96x96.png
  favicon-16x16.png
  apple-icon-57x57.png ... apple-icon-180x180.png
  ms-icon-70x70.png
  ms-icon-144x144.png
  ms-icon-150x150.png
  ms-icon-310x310.png
  favicon.ico
```

### Apple touch icons

iOS Safari ignores the manifest `icons` array, so add the Apple touch icon `<link>` tags to your `<head>`:

```php
use JeffersonGoncalves\Favicon\Favicon;

@foreach (Favicon::appleHeadLinks() as $link)
    <link rel="{{ $link['rel'] }}" sizes="{{ $link['sizes'] }}" href="{{ $link['href'] }}">
@endforeach
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Jèfferson Gonçalves](https://github.com/jeffersongoncalves)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
