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
                    'mahasiswa.materials.questions.index',
                    'mahasiswa.materials.questions.show',
                    'mahasiswa.materials.questions.review',
                    'mahasiswa.materials.questions.levels',
                    'mahasiswa.questions.check-answer',
                    'mahasiswa.questions.show',
                    'mahasiswa.materials.reset',
                    'logout',
                    'login',
                    'register'
                ];

                if (!in_array($request->route()->getName(), $allowedRoutes)) {
                    return redirect()->route('mahasiswa.materials.index')
                        ->with('info', 'Fitur ini hanya tersedia untuk mahasiswa terdaftar.');
                }
            }
        }

        return $next($request);
    }
} 