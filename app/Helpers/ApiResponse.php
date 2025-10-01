<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    /**
     * @param bool $success
     * @param array|null $data
     * @param string|null $message
     * @param int|null $code
     * @return JsonResponse
     */
    public static function send(bool $success, ?array $data = null, ?string $message = null, ?int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => $success,
            'data' => $data,
            'message' => $message,
        ], $code);
    }
}
