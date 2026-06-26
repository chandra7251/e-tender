<?php
namespace App\Http\Controllers;
use Illuminate\Http\JsonResponse;
abstract class Controller
{
    /**
     * JSON success response.
     * Format: {status: true, message: string, data: mixed}
     */
    protected function success(mixed $data = null, string $message = 'OK', int $status = 200): JsonResponse
    {
        return response()->json([
            'status'  => true,
            'message' => $message,
            'data'    => $data,
        ], $status);
    }

    /**
     * JSON error response.
     * Format: {status: false, message: string, errors?: mixed}
     */
    protected function error(string $message = 'Error', mixed $errors = null, int $status = 400): JsonResponse
    {
        $payload = [
            'status'  => false,
            'message' => $message,
        ];
        if ($errors !== null) {
            $payload['errors'] = $errors;
        }
        return response()->json($payload, $status);
    }

    /**
     * JSON 201 Created response.
     */
    protected function created(mixed $data = null, string $message = 'Created'): JsonResponse
    {
        return $this->success($data, $message, 201);
    }
}
