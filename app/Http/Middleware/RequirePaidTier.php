<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequirePaidTier
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $client = $user?->client;

        if (! $client || ! $client->isPaid()) {
            return response()->json(
                ['message' => 'This feature requires a paid subscription.'],
                403
            );
        }

        return $next($request);
    }
}
