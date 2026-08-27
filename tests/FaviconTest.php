<?php

use JeffersonGoncalves\Favicon\Favicon;

it('serves browserconfig.xml as application/xml', function () {
    config()->set('favicon.enabled', true);

    Favicon::routes();

    $response = $this->get('/browserconfig.xml');

    $response->assertOk();

    expect($response->headers->get('Content-Type'))
        ->toContain('application/xml');

    expect($response->getContent())
        ->toContain('<browserconfig>')
        ->toContain('square150x150logo');
});

it('escapes special characters in the browserconfig.xml tile color', function () {
    config()->set('favicon.enabled', true);
    config()->set('favicon.tile_color', '#fff" & <bad>');

    Favicon::routes();

    $response = $this->get('/browserconfig.xml');

    $response->assertOk();

    expect($response->getContent())
        ->toContain('&amp;')
        ->toContain('&lt;bad&gt;')
        ->toContain('&quot;')
        ->not->toContain('<bad>');
});

it('does not register the routes when disabled', function () {
    config()->set('favicon.enabled', false);

    $this->get('/browserconfig.xml')->assertNotFound();
    $this->get('/favicon.ico')->assertNotFound();
});

it('serves the favicon.ico when a favicon path is configured', function () {
    config()->set('favicon.enabled', true);
    config()->set('favicon.favicon', 'resources/favicon/favicon.ico');

    Favicon::routes();

    $response = $this->get('/favicon.ico');

    $response->assertOk();

    expect($response->headers->get('Content-Type'))->toContain('image/x-icon');
});

it('returns the favicon body content from Vite', function () {
    config()->set('favicon.enabled', true);
    config()->set('favicon.favicon', 'resources/favicon/favicon.ico');

    Favicon::routes();

    $response = $this->get('/favicon.ico');

    $response->assertOk();
    expect($response->getContent())->toBe('fake-favicon-content');
});

it('does not register the favicon route when the favicon path is empty', function () {
    config()->set('favicon.enabled', true);
    config()->set('favicon.favicon', '');

    Favicon::routes();

    $this->get('/favicon.ico')->assertNotFound();
    // The other route is still registered.
    $this->get('/browserconfig.xml')->assertOk();
});

it('builds apple touch icon head links for every iOS size', function () {
    $links = Favicon::appleHeadLinks();

    expect($links)->toHaveCount(9);

    expect($links[0])->toMatchArray([
        'rel' => 'apple-touch-icon',
        'sizes' => '57x57',
        'href' => 'https://cdn.test/resources/favicon/apple-icon-57x57.png',
    ]);
});

it('builds standard png icon head links', function () {
    $links = Favicon::iconHeadLinks();

    expect($links)->toHaveCount(4);
    expect($links[0])->toMatchArray([
        'rel' => 'icon',
        'type' => 'image/png',
        'sizes' => '192x192',
        'href' => 'https://cdn.test/resources/favicon/android-icon-192x192.png',
    ]);
});

it('builds msapplication tile metas using the configured tile color', function () {
    config()->set('favicon.tile_color', '#0B0A09');

    $metas = Favicon::msApplicationMeta();

    expect($metas)->toContain(['name' => 'msapplication-TileColor', 'content' => '#0B0A09']);
    expect($metas)->toContain([
        'name' => 'msapplication-TileImage',
        'content' => 'https://cdn.test/resources/favicon/ms-icon-144x144.png',
    ]);
});

it('exposes the tile color', function () {
    config()->set('favicon.tile_color', '#123456');

    expect(Favicon::tileColor())->toBe('#123456');
});

it('renders the head view with icon links and the browserconfig meta', function () {
    $html = view('favicon::head')->render();

    expect($html)
        ->toContain('rel="apple-touch-icon"')
        ->toContain('rel="icon"')
        ->toContain('name="msapplication-TileColor"')
        ->toContain('<meta name="msapplication-config" content="/browserconfig.xml">');
});

it('lets the browserconfig url be overridden via config', function () {
    config()->set('favicon.browserconfig_url', '/custom/browserconfig.xml');

    $html = view('favicon::head')->render();

    expect($html)->toContain('<meta name="msapplication-config" content="/custom/browserconfig.xml">');
});
