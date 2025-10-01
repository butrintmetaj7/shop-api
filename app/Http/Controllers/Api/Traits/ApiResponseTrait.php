<?php

namespace App\Http\Controllers\Api\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

trait ApiResponseTrait
{
    protected function successResponse(string $message, mixed $data = null, int $statusCode = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $statusCode);
    }

    protected function errorResponse(string $message, mixed $errors = null, int $statusCode = 400): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }

    protected function authResponse(string $message, $user, string $token, int $statusCode = 200): JsonResponse
    {
        return $this->successResponse($message, [
            'user' => $user,
            'token' => $token,
        ], $statusCode);
    }

    protected function successResponseWithPagination(LengthAwarePaginator $paginator, $resourceCollection, int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $resourceCollection,
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),    
        ], $statusCode);
    }
}
