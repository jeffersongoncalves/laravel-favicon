<?php

declare(strict_types=1);

namespace JeffersonGoncalves\Favicon\Http\Controllers;

use Illuminate\Http\Response;
use JeffersonGoncalves\Favicon\Favicon;

class FaviconController
{
    public function __invoke(): Response
    {
        return Favicon::getFavicon();
    }
}
