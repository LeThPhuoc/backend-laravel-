<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JwtAny
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        foreach (['boss', 'staff'] as $guard) {

        auth()->shouldUse($guard);

        try {
            if ($user = auth()->authenticate()) {
                $request->merge(['auth_user' => $user, 'role' => $guard]);
                return $next($request);
            }
        } catch (\Exception $e) {}
    }

    return response()->json(['message' => 'Unauthorized'], 401);
    }
}
