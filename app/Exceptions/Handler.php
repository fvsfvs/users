<?php

namespace App\Exceptions;

use App\Helpers\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class Handler
{

    public function __invoke(Exceptions $exceptions): void
    {
        $exceptions->renderable(function (Throwable $e, $request) {

            if ($e instanceof MethodNotAllowedHttpException) {
                return ApiResponse::send(false, null, 'Method not allowed', 405);
            }

            if ($e instanceof AuthenticationException) {
                return ApiResponse::send(false, null, 'Unauthorized', 401);
            }

            if ($e instanceof ValidationException) {
                return ApiResponse::send(false, $e->errors(), 'Validation failed', 422);
            }

            if ($e instanceof NotFoundHttpException) {
                return ApiResponse::send(false, null, 'Not found', 404);
            }

            if ($e instanceof HttpExceptionInterface) {
                $status  = $e->getStatusCode();
                $message = $e->getMessage() ?: 'HTTP error';
                return ApiResponse::send(false, null, $message, $status);
            }

            $message = config('app.debug')
                ? $e->getMessage()
                : 'Server error';

            return ApiResponse::send(false, null, $message, 500);
        });
    }
}
