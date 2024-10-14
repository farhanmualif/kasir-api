<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    protected function errorResponse($message, $code, $details = null): JsonResponse
    {
        $response = [
            'status' => 'error',
            'statusCode' => $code,
            'error' => [
                'code' => $this->getErrorCode($code),
                'message' => $message,
                'details' => $details,
                'timestamp' => now()->toIso8601String(),
                'path' => request()->path(),
                'suggestion' => $this->getSuggestion($code),
            ],
            'documentation_url' => config('app.url') . '/docs/errors',
        ];

        return response()->json($response, $code);
    }

    private function getErrorCode($statusCode): string
    {
        $codes = [
            404 => 'RESOURCE_NOT_FOUND',
            401 => 'UNAUTHORIZED',
            // Add more mappings as needed
        ];

        return $codes[$statusCode] ?? 'UNKNOWN_ERROR';
    }

    private function getSuggestion($statusCode): string
    {
        $suggestions = [
            404 => 'Please check if the resource ID is correct or refer to our documentation for more information.',
            401 => 'Please ensure you are properly authenticated.',
            // Add more suggestions as needed
        ];

        return $suggestions[$statusCode] ?? 'Please refer to our documentation for more information on this error.';
    }
}
