<?php

namespace App\Http\Middleware;

use App\Support\StoreTraffic;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RecordStoreTraffic
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        try {
            StoreTraffic::recordRequest($request);
        } catch (\Throwable) {
            // Never break the storefront for analytics logging.
        }

        return $response;
    }
}
