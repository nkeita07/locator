<?php

namespace App\Http\Middleware;

use Closure;

class RoleMiddleware
{
    public function handle($request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Utilisateur non authentifié'], 401);
        }

        // Vérifie si le user possède au moins un des rôles requis
        $hasRole = $user->roles()->whereIn('libelle', $roles)->exists();

        if (!$hasRole) {
            return response()->json([
                'message' => "Accès refusé : vous n'avez pas les permissions nécessaires."
            ], 403);
        }

        return $next($request);
    }
}
