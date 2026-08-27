<?php

declare(strict_types=1);

namespace JeffersonGoncalves\Favicon;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FaviconServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-favicon')
            ->hasConfigFile()
            ->hasViews();
    }

    public function packageBooted(): void
    {
        if (config('favicon.enabled', false)) {
            Favicon::routes();
        }
    }
}
