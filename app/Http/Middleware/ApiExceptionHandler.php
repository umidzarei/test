<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class ApiExceptionHandler
{
    public function handle(Request $request, Closure $next)
    {
        try {
            return $next($request);
        } catch (ValidationException $e) {
            return response()->apiResult(
                messages: [$e->getMessage()],
                statusCode: $e->status,
                metadata: $e->errors(),
            );
        } catch (NotFoundHttpException $e) {
            return response()->apiResult(
                data: null,
                messages: [__('messages.not_found')],
                statusCode: 404
            );
        } catch (AuthorizationException $e) {
            return response()->apiResult(
                data: null,
                messages: [__('messages.unauthorized')],
                statusCode: 403
            );
        } catch (Throwable $e) {
            if (app()->environment('production')) {
                return response()->apiResult(
                    data: null,
                    messages: [__('messages.error')],
                    statusCode: 500
                );
            } else {
                return response()->apiResult(
                    data: null,
                    messages: [$e->getMessage()],
                    statusCode: $e->getCode() ?: 500
                );
            }
        }
    }
}
