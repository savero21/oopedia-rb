<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Material;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $role = $user->role;

        $mahasiswaCount = User::whereHas('role', function ($query) {
            $query->where('role_name', 'Mahasiswa');
        })->count();

        $materialCount = Material::count(); // Menghitung jumlah materi


        return view('dashboard.index', [
            'userName' => $user->name,
            'userRole' => $role->role_name, // Get the role name
            'mahasiswaCount' => $mahasiswaCount, // Tambahkan jumlah mahasiswa ke view
            'materialCount' => $materialCount // Tambahkan jumlah materi ke view
        ]);
    }
}