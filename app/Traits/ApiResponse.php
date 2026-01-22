<?php

namespace App\Traits;

trait ApiResponse
{
    /**
     * Success response
     */
    protected function success($data = null, string $message = 'Success', int $statusCode = 200): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    /**
     * Error response
     */
    protected function error(string $message = 'Error', int $statusCode = 400, $data = null): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    /**
     * Unauthorized response
     */
    protected function unauthorized(string $message = 'Unauthorized'): \Illuminate\Http\JsonResponse
    {
        return $this->error($message, 401);
    }

    /**
     * Forbidden response
     */
    protected function forbidden(string $message = 'Forbidden'): \Illuminate\Http\JsonResponse
    {
        return $this->error($message, 403);
    }

    /**
     * Not found response
     */
    protected function notFound(string $message = 'Not Found'): \Illuminate\Http\JsonResponse
    {
        return $this->error($message, 404);
    }
}
