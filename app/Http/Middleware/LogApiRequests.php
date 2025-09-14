<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogApiRequests
{
	public function handle(Request $request, Closure $next)
	{
		$start = microtime(true);
		$response = $next($request);
		$durationMs = (int) ((microtime(true) - $start) * 1000);

		Log::info('API Request', [
			'method' => $request->method(),
			'path' => $request->path(),
			'ip' => $request->ip(),
			'status' => $response->getStatusCode(),
			'duration_ms' => $durationMs,
		]);

		return $response;
	}
}
