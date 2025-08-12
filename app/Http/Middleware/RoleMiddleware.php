<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();
        
        // Check if user is authenticated
        if (!$user) {
            abort(403, 'Unauthorized - Please log in');
        }
        
        // Convert roles string to array (handle pipe separated roles)
        $allowedRoles = [];
        foreach ($roles as $role) {
            if (str_contains($role, '|')) {
                $allowedRoles = array_merge($allowedRoles, explode('|', $role));
            } else {
                $allowedRoles[] = $role;
            }
        }
        
        // Check if user has any of the required roles
        if (!$user->hasAnyRole($allowedRoles)) {
            abort(403, 'Unauthorized - Insufficient permissions');
        }
        
        return $next($request);
    }
}
