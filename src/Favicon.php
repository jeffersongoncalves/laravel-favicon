<?php

declare(strict_types=1);

namespace JeffersonGoncalves\Favicon;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Vite;
use JeffersonGoncalves\Favicon\Http\Controllers\BrowserConfigController;
use JeffersonGoncalves\Favicon\Http\Controllers\FaviconController;

abstract class Favicon
{
    public static function routes(): void
    {
        if (config('favicon.enabled', false)) {
            // Invokable controllers (not closures) so the consuming app can
            // run `php artisan route:cache` in production — closures throw a
            // "Unable to prepare route for serialization" error there.
            Route::get('browserconfig.xml', BrowserConfigController::class);
            if (! empty(config('favicon.favicon'))) {
                Route::get('favicon.ico', FaviconController::class);
            }
        }
    }

    /**
     * Apple touch icons for the iOS Safari "Add to Home Screen" PWA install
     * path. iOS ignores the standard manifest icons array, so these
     * `apple-touch-icon` `<link>` tags must be present in <head>.
     *
     * @return array<int, array{rel: string, sizes: string, href: string}>
     */
    public static function appleHeadLinks(): array
    {
        $sizes = ['57x57', '60x60', '72x72', '76x76', '114x114', '120x120', '144x144', '152x152', '180x180'];

        $links = [];

        foreach ($sizes as $size) {
            $links[] = [
                'rel' => 'apple-touch-icon',
                'sizes' => $size,
                'href' => Vite::asset("resources/favicon/apple-icon-{$size}.png"),
            ];
        }

        return $links;
    }

    /**
     * Standard `<link rel="icon">` PNG tags (favicon shown in the browser tab
     * and bookmarks). Covers the Android 192 master plus the 32/96/16 desktop
     * sizes from the realfavicongenerator asset set.
     *
     * @return array<int, array{rel: string, type: string, sizes: string, href: string}>
     */
    public static function iconHeadLinks(): array
    {
        return [
            ['rel' => 'icon', 'type' => 'image/png', 'sizes' => '192x192', 'href' => Vite::asset('resources/favicon/android-icon-192x192.png')],
            ['rel' => 'icon', 'type' => 'image/png', 'sizes' => '32x32', 'href' => Vite::asset('resources/favicon/favicon-32x32.png')],
            ['rel' => 'icon', 'type' => 'image/png', 'sizes' => '96x96', 'href' => Vite::asset('resources/favicon/favicon-96x96.png')],
            ['rel' => 'icon', 'type' => 'image/png', 'sizes' => '16x16', 'href' => Vite::asset('resources/favicon/favicon-16x16.png')],
        ];
    }

    /**
     * Legacy Windows pinned-tile metas (`msapplication-*`).
     *
     * @return array<int, array{name: string, content: string}>
     */
    public static function msApplicationMeta(): array
    {
        return [
            ['name' => 'msapplication-TileColor', 'content' => self::tileColor()],
            ['name' => 'msapplication-TileImage', 'content' => Vite::asset('resources/favicon/ms-icon-144x144.png')],
        ];
    }

    public static function tileColor(): string
    {
        return (string) config('favicon.tile_color', '#ffffff');
    }

    public static function getBrowserConfigXml(): Response
    {
        // Every value is interpolated into XML, so escape with ENT_XML1 to
        // keep a stray `&`/`<`/`"` in a Vite URL or a configured tile colour
        // from producing a malformed document.
        $esc = static fn (string $v): string => htmlspecialchars($v, ENT_XML1 | ENT_QUOTES, 'UTF-8');

        $square70x70logo = $esc(Vite::asset('resources/favicon/ms-icon-70x70.png'));
        $square150x150logo = $esc(Vite::asset('resources/favicon/ms-icon-150x150.png'));
        $square310x310logo = $esc(Vite::asset('resources/favicon/ms-icon-310x310.png'));
        $tileColor = $esc(self::tileColor());
        $xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>
<browserconfig>
    <msapplication>
        <tile>
            <square70x70logo src=\"{$square70x70logo}\"/>
            <square150x150logo src=\"{$square150x150logo}\"/>
            <square310x310logo src=\"{$square310x310logo}\"/>
            <TileColor>{$tileColor}</TileColor>
        </tile>
    </msapplication>
</browserconfig>";

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }

    public static function getFavicon(): Response
    {
        return response(Vite::content((string) config('favicon.favicon')), 200, ['Content-Type' => 'image/x-icon']);
    }
}
