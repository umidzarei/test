<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AuthGuard
{
    public function handle($request, Closure $next, $guard)
    {
        Auth::shouldUse($guard);

        if (! Auth::check()) {
            return response()->apiResult(
                data: null,
                statusCode: 401,
                messages: ['messages.unauthorized']
            );
        }

        return $next($request);
    }
}
