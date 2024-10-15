<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\JsonResponse;

class CustomizeValidationErrors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($response instanceof JsonResponse && $response->getStatusCode() === 422) {
            $data = $response->getData(true);

            $customizedResponse = [
                'status' => false,
                'message' => $data['errors'] ?? []
            ];

            $response->setData($customizedResponse);
        }

        return $response;
    }
}
