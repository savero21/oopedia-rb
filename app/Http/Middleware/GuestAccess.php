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
            
            // Allow regular students to access all routes
            if ($user->role_id === 2) {
                return $next($request);
            }
            
            // Restrict guest access
            if ($user->role_id === 3) {
                $allowedRoutes = [
                    'mahasiswa.materials.index',
                    'mahasiswa.materials.show',
                    'mahasiswa.questions.check-answer',
                    'mahasiswa.materials.reset'
                ];

                if (!in_array($request->route()->getName(), $allowedRoutes)) {
                    return redirect()->route('mahasiswa.materials.index');
                }

                if ($request->route()->getName() === 'mahasiswa.materials.index') {
                    session()->flash('info', 'Anda masuk sebagai Tamu. Beberapa fitur mungkin dibatasi.');
                }
            }
        }

        return $next($request);
    }
} 