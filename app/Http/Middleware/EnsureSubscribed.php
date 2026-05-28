<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSubscribed
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    //    public function handle(Request $request, Closure $next)
    //    {
    //        $user = $request->user();
    //
    //        if (!$user || !$user->isClient()) {
    //            return response()->json(['message' => 'Unauthorized.'], 403);
    //        }
    //
    //        if (!$user->client?->isSubscribed()) {
    //            return response()->json([
    //                'message' => 'An active subscription is required to access this feature.',
    //            ], 402);
    //        }
    //
    //        return $next($request);
    //    }

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $client = $user?->client;

        if (! $client || ! $client->hasStandardAccess()) {
            return response()->json([
                'message' => 'An active subscription is required.',
                'code' => 'SUBSCRIPTION_REQUIRED',
            ], 403);
        }

        return $next($request);
    }
}
