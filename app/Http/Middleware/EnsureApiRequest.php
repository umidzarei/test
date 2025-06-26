<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureApiRequest
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (! $request->expectsJson()) {
            return response()->apiResult(
                data: null,
                statusCode: 403,
                messages: [__('messages.api_only')]
            );
        }
        return $next($request);
    }
}
