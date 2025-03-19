<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class MahasiswaSeeder extends Seeder
{
    public function run(): void
    {
        // Create or ensure Mahasiswa role exists
        $roleMahasiswa = Role::firstOrCreate(
            ['id' => 2],
            ['role_name' => 'Mahasiswa']
        );

        // Create mahasiswa users
        $mahasiswaList = [
            [
                'name' => 'Andi Pratama',
                'email' => 'andi@mahasiswa.com',
                'password' => 'mhs123'
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@mahasiswa.com',
                'password' => 'mhs123'
            ],
            [
                'name' => 'Citra Dewi',
                'email' => 'citra@mahasiswa.com',
                'password' => 'mhs123'
            ],
            [
                'name' => 'Deni Wijaya',
                'email' => 'deni@mahasiswa.com',
                'password' => 'mhs123'
            ],
            [
                'name' => 'Eva Putri',
                'email' => 'eva@mahasiswa.com',
                'password' => 'mhs123'
            ]
        ];

        foreach ($mahasiswaList as $mahasiswa) {
            User::create([
                'name' => $mahasiswa['name'],
                'email' => $mahasiswa['email'],
                'password' => Hash::make($mahasiswa['password']),
                'role_id' => $roleMahasiswa->id,
            ]);
        }
    }
} 