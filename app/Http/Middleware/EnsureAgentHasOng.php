<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAgentHasOng
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()?->ong_id) {
            return response()->json([
                'message' => 'Vous n\'êtes rattaché à aucune ONG. Contactez votre représentant.',
            ], 403);
        }

        return $next($request);
    }
}
