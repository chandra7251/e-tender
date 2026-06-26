<?php
namespace App\Http\Traits;
use Illuminate\Http\JsonResponse;

/**
 * ApiResponse Trait
 * Deprecated: gunakan method di base Controller\Controller langsung.
 * Trait ini dipertahankan untuk backward compatibility controller lama.
 * Format: {status: true/false, message, data}
 */
trait ApiResponse
{
    protected function success(mixed $data = null, string $message = 'OK', int $status = 200): JsonResponse
    {
        return response()->json([
            'status'  => true,
            'message' => $message,
            'data'    => $data,
        ], $status);
    }

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

    protected function created(mixed $data = null, string $message = 'Created'): JsonResponse
    {
        return $this->success($data, $message, 201);
    }
}
