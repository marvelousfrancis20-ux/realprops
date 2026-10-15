<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Ensures an agent has a verified email address before touching listings.
 */
class EnsureAgentIsVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->isAgent() && $user->email_verified_at === null) {
            return redirect()->route('verification.notice')
                ->with('status', 'Please verify your email address before managing listings.');
        }

        return $next($request);
    }
}
