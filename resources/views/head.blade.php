@php
    use JeffersonGoncalves\Favicon\Favicon;

    // All params are optional. Consumers (a Filament panel head hook, a public
    // site layout, or the laravel-pwa-favicon head view) render this partial
    // so the icon <head> tags stay identical across surfaces — one source of
    // truth, no duplicated markup.
    $browserConfigUrl = $browserConfigUrl ?? config('favicon.browserconfig_url', '/browserconfig.xml');
@endphp
@foreach (Favicon::iconHeadLinks() as $link)
    <link rel="{{ $link['rel'] }}" type="{{ $link['type'] }}" sizes="{{ $link['sizes'] }}" href="{{ $link['href'] }}">
@endforeach
@foreach (Favicon::appleHeadLinks() as $link)
    <link rel="{{ $link['rel'] }}" sizes="{{ $link['sizes'] }}" href="{{ $link['href'] }}">
@endforeach
@foreach (Favicon::msApplicationMeta() as $meta)
    <meta name="{{ $meta['name'] }}" content="{{ $meta['content'] }}">
@endforeach
<meta name="msapplication-config" content="{{ $browserConfigUrl }}">
