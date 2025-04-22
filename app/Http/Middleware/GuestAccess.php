<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class GuestAccess
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            $user = auth()->user();
            
            // Allow regular students (role 3) and admin/superadmin (roles 1-2) to access all routes
            if ($user->role_id <= 3) {
                return $next($request);
            }
            
            // Restrict guest access (role 4)
            if ($user->role_id === 4) {
                $allowedRoutes = [
                    'mahasiswa.materials.index',
                    'mahasiswa.materials.show',
                    'mahasiswa.questions.check-answer',
                    'mahasiswa.materials.reset',
                    'logout'
                ];

                if (!in_array($request->route()->getName(), $allowedRoutes)) {
                    return redirect()->route('mahasiswa.materials.index');
                }
            }
        }

        return $next($request);
    }
} 