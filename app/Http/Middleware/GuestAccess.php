<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class GuestAccess
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->role_id === 3) {
            // Batasi akses hanya ke materi
            $allowedRoutes = [
                'mahasiswa.materials.index',
                'mahasiswa.materials.show',
                'mahasiswa.questions.check-answer'
            ];

            if (!in_array($request->route()->getName(), $allowedRoutes)) {
                return redirect()->route('mahasiswa.materials.index');
            }

            // Tambahkan alert untuk tamu
            if ($request->route()->getName() === 'mahasiswa.materials.index') {
                session()->flash('info', 'Anda masuk sebagai Tamu. Beberapa fitur mungkin dibatasi.');
            }
        }

        return $next($request);
    }
} 