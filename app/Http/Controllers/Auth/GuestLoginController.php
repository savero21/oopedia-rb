<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GuestLoginController extends Controller
{
    public function login()
    {
        // Create a temporary guest user
        $guestUser = User::create([
            'name' => 'Tamu_' . Str::random(8),
            'email' => 'guest_' . Str::random(8) . '@temporary.com',
            'password' => Hash::make(Str::random(16)),
            'role_id' => 4 // Guest role (sekarang 4)
        ]);

        Auth::login($guestUser);
        
        return redirect()->route('mahasiswa.materials.index');
    }
} 