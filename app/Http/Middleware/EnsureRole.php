<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Restricts a route to specific admin roles, e.g. role:super_admin */
class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if ($user === null || ($roles !== [] && ! in_array($user->role, $roles, true))) {
            abort(403, 'You do not have permission to access this section.');
        }

        return $next($request);
    }
}
