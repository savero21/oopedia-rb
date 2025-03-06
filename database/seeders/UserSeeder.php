<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class UserSeeder extends Seeder
{
    public function run()
    {
        $roleDosen = Role::firstOrCreate(['id' => 1], ['role_name' => 'Dosen']);
        
        $roleMahasiswa = Role::firstOrCreate(['id' => 2], ['role_name' => 'Mahasiswa']);

        User::factory()->create([
            'name' => 'Admin Dosen',
            'email' => 'admin@material.com',
            'password' => bcrypt('secret'),
            'role_id' => $roleDosen->id,
        ]);

        User::factory()->create([
            'name' => 'Mahasiswa User',
            'email' => 'mahasiswa@material.com',
            'password' => bcrypt('secret'),
            'role_id' => $roleMahasiswa->id,
        ]);
    }
}