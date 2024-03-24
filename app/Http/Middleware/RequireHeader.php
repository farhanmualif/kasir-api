<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireHeader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->header("Content-Type") != "application/json") {
            return response()->json([
                "message" => "header Content-Type must be application/json"
            ]);
        }
        if ($request->header("Accept") != "application/json") {
            return response()->json([
                "message" => "header Accept must be application/json"
            ]);
        }
        return $next($request);
    }
}
