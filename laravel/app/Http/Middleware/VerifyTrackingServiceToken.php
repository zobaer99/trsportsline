<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyTrackingServiceToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $expected = (string) config('services.tracking.service_token');
        $provided = (string) $request->bearerToken();
        abort_if($expected === '' || $provided === '' || !hash_equals($expected, $provided), 403);
        return $next($request);
    }
}
