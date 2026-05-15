<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $userRole = $request->user()?->role?->name;

        if (!in_array($userRole, $roles)) {
            abort(403, 'Accès non autorisé.');
        }

        return $next($request);
    }
}
