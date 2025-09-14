<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RateLimitMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $ip = $request->ip();
        $key = 'rate_limit_' . $ip;
        $count = cache()->get($key, 0);
        if ($count >= 10) {
            return response('Too Many Requests', Response::HTTP_TOO_MANY_REQUESTS);
        }
        cache()->put($key, $count + 1, 60);
        return $next($request);
    }
}
