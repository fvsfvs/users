<?php

namespace App\Http\Middleware;

use App\Helpers\ApiResponse;
use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiAuthenticate extends Middleware
{
    protected function redirectTo(Request $request): null
    {
        return null;
    }

    public function handle($request, Closure $next, ...$guards): JsonResponse|Closure
    {
        try {
            $this->authenticate($request, $guards);
        } catch (AuthenticationException $e) {
            return ApiResponse::send(false, null, 'Unauthorized', 401);
        }

        return $next($request);
    }
}
