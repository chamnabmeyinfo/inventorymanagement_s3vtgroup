<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SyncApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = config('services.sync_api_key');
        if (empty($apiKey)) {
            return $next($request);
        }
        $provided = $request->header('X-API-Key');
        if ($provided !== $apiKey) {
            return response()->json(['error' => 'Invalid or missing API key'], 401);
        }
        return $next($request);
    }
}
