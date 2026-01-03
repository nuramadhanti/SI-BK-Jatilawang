<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogRequests
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);

        // Log incoming request
        if ($this->shouldLog($request)) {
            Log::channel('daily')->info('Incoming Request', [
                'method' => $request->method(),
                'path' => $request->path(),
                'url' => $request->url(),
                'ip' => $request->ip(),
                'user_id' => auth()->id(),
                'user_name' => auth()->user()?->name ?? 'Guest',
                'user_agent' => $request->userAgent(),
            ]);
        }

        $response = $next($request);

        // Log response timing
        $duration = round((microtime(true) - $startTime) * 1000, 2); // ms

        if ($this->shouldLog($request)) {
            Log::channel('daily')->info('Response Sent', [
                'method' => $request->method(),
                'path' => $request->path(),
                'status_code' => $response->getStatusCode(),
                'duration_ms' => $duration,
                'user_id' => auth()->id(),
            ]);

            // Log slow requests (> 1 second)
            if ($duration > 1000) {
                Log::channel('daily')->warning('Slow Request Detected', [
                    'method' => $request->method(),
                    'path' => $request->path(),
                    'duration_ms' => $duration,
                    'user_id' => auth()->id(),
                ]);
            }
        }

        return $response;
    }

    /**
     * Determine if the request should be logged
     */
    private function shouldLog(Request $request): bool
    {
        // Skip logging for static assets
        $skip = [
            'assets',
            'css',
            'js',
            'images',
            'storage',
            'vendor',
            'favicon',
        ];

        foreach ($skip as $path) {
            if ($request->is($path . '/*') || $request->is($path)) {
                return false;
            }
        }

        return true;
    }
}
