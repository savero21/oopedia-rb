<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user(); // Mendapatkan pengguna yang sedang login
        $role = $user->role; // Assuming you have a relationship defined in the User model
        return view('dashboard.index', [
            'userName' => $user->name,
            'userRole' => $role->role_name // Get the role name
        ]);
    }
}