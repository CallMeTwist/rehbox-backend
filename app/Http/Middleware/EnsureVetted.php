<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureVetted
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user || !$user->isPT()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        if (!$user->physiotherapist?->isVetted()) {
            return response()->json([
                'message' => 'Your account is pending vetting approval.',
                'vetting_status' => $user->physiotherapist?->vetting_status ?? 'pending',
            ], 403);
        }

        return $next($request);
    }
}
