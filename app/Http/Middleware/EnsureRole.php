<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Usage in routes: ->middleware(EnsureRole::class . ':admin')
 * Guests go to the right login page; wrong-role users go to their own home.
 */
class EnsureRole
{
    public function handle(Request $request, Closure $next, string $role = 'citizen'): Response
    {
        $user = $request->user();

        if (! $user) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route($role === 'admin' ? 'admin.login' : 'login');
        }

        if ($user->role !== $role) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Forbidden.'], 403);
            }
            return redirect()->route($user->isAdmin() ? 'admin.dashboard' : 'citizen.dashboard');
        }

        return $next($request);
    }
}
