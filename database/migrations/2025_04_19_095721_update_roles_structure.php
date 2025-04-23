<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateRolesStructure extends Migration
{
    public function up()
    {
        // Cek apakah role dengan ID 2 sudah ada
        $roleExists = DB::table('roles')->where('id', 2)->exists();
        
        // 1. Tambahkan role baru (Admin/Dosen) dengan ID 2 jika belum ada
        if (!$roleExists) {
            DB::table('roles')->insert([
                'id' => 2,
                'role_name' => 'Admin'
            ]);
        } else {
            // Update role name jika sudah ada
            DB::table('roles')->where('id', 2)->update(['role_name' => 'Admin']);
        }
        
        // 2. Update role Mahasiswa dari ID 2 menjadi ID 3
        DB::statement('UPDATE users SET role_id = 3 WHERE role_id = 2');
        
        // 3. Update role_name untuk ID 1 menjadi Superadmin
        DB::statement('UPDATE roles SET role_name = "Superadmin" WHERE id = 1');
        
        // Cek apakah role dengan ID 3 sudah ada
        $role3Exists = DB::table('roles')->where('id', 3)->exists();
        
        // 4. Tambahkan role Mahasiswa dengan ID 3 jika belum ada
        if (!$role3Exists) {
            DB::table('roles')->insert([
                'id' => 3,
                'role_name' => 'Mahasiswa'
            ]);
        } else {
            // Update role name jika sudah ada
            DB::table('roles')->where('id', 3)->update(['role_name' => 'Mahasiswa']);
        }
    }

    public function down()
    {
        // Rollback changes if needed
        DB::statement('UPDATE users SET role_id = 2 WHERE role_id = 3');
        // Tidak menghapus role dengan ID 2 karena mungkin sudah ada sebelumnya
        DB::statement('UPDATE roles SET role_name = "Admin" WHERE id = 1');
        DB::statement('UPDATE roles SET role_name = "Mahasiswa" WHERE id = 3');
    }
}