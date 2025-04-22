<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminApproved;
use Illuminate\Support\Facades\DB;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        // Hanya superadmin yang bisa mengakses manajemen admin
        if (auth()->user()->role_id != 1) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Anda tidak memiliki akses untuk manajemen admin');
        }
        
        $query = User::where('role_id', 2); // Hanya tampilkan admin
        
        // Filter berdasarkan pencarian
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        $users = $query->paginate(10);
        
        // Hitung jumlah admin yang pending untuk badge notifikasi
        $pendingAdminsCount = User::where('role_id', 2)
                                 ->where('is_approved', false)
                                 ->count();
        
        return view('admin.users.index', compact('users', 'pendingAdminsCount'));
    }
    
    public function create()
    {
        // Hanya superadmin yang bisa membuat admin baru
        if (auth()->user()->role_id != 1) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Anda tidak memiliki akses untuk membuat admin baru');
        }
        
        $roles = Role::whereIn('id', [1, 2])->get();
        return view('admin.users.create', compact('roles'));
    }
    
    public function store(Request $request)
    {
        // Hanya superadmin yang bisa membuat admin baru
        if (auth()->user()->role_id != 1) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Anda tidak memiliki akses untuk membuat admin baru');
        }
        
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role_id' => ['required', 'in:1,2'],
        ]);
        
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
        ]);
        
        return redirect()->route('admin.users.index')
            ->with('success', 'Admin berhasil ditambahkan');
    }
    
    public function edit(User $user)
    {
        // Superadmin dapat mengedit semua admin
        // Admin hanya dapat mengedit dirinya sendiri
        if (auth()->user()->role_id != 1 && auth()->id() != $user->id) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Anda tidak memiliki akses untuk mengedit admin ini');
        }
        
        $roles = Role::whereIn('id', [1, 2])->get();
        return view('admin.users.edit', compact('user', 'roles'));
    }
    
    public function update(Request $request, User $user)
    {
        // Superadmin dapat mengedit semua admin
        // Admin hanya dapat mengedit dirinya sendiri
        if (auth()->user()->role_id != 1 && auth()->id() != $user->id) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Anda tidak memiliki akses untuk mengedit admin ini');
        }
        
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ];
        
        // Hanya superadmin yang bisa mengubah role
        if (auth()->user()->role_id == 1) {
            $rules['role_id'] = ['required', 'in:1,2'];
        }
        
        // Password opsional saat update
        if ($request->filled('password')) {
            $rules['password'] = ['confirmed', Rules\Password::defaults()];
        }
        
        $request->validate($rules);
        
        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];
        
        // Hanya superadmin yang bisa mengubah role
        if (auth()->user()->role_id == 1 && $request->has('role_id')) {
            $data['role_id'] = $request->role_id;
        }
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        
        $user->update($data);
        
        return redirect()->route('admin.users.index')
            ->with('success', 'Admin berhasil diperbarui');
    }
    
    public function destroy(User $user)
    {
        // Hanya superadmin yang bisa menghapus admin
        if (auth()->user()->role_id != 1) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Anda tidak memiliki akses untuk menghapus admin');
        }
        
        // Tidak bisa menghapus diri sendiri
        if (auth()->id() == $user->id) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri');
        }
        
        $user->delete();
        
        return redirect()->route('admin.users.index')
            ->with('success', 'Admin berhasil dihapus');
    }
    
    public function pendingAdmins()
    {
        // Hanya superadmin yang bisa mengakses halaman pending admin
        if (auth()->user()->role_id != 1) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Anda tidak memiliki akses untuk halaman ini');
        }
        
        // Refresh cache dan ambil data terbaru
        DB::statement("SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED");
        
        // Ambil semua admin yang belum diapprove
        $pendingAdmins = User::where('role_id', 2)
            ->where('is_approved', false)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('admin.users.pending', compact('pendingAdmins'));
    }
    
    public function approveAdmin(User $user)
    {
        // Hanya superadmin yang bisa menyetujui admin
        if (auth()->user()->role_id != 1) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Anda tidak memiliki akses untuk menyetujui admin');
        }
        
        // Pastikan user adalah admin yang belum diapprove
        if ($user->role_id != 2 || $user->is_approved) {
            return redirect()->route('admin.pending-admins')
                ->with('error', 'User ini bukan admin yang pending atau sudah diapprove');
        }
        
        try {
            // Gunakan transaksi database untuk memastikan perubahan tersimpan
            DB::beginTransaction();
            
            // Update status approval dengan query langsung ke database
            DB::table('users')
                ->where('id', $user->id)
                ->update(['is_approved' => true]);
            
            // Commit transaksi
            DB::commit();
            
            // Refresh cache
            DB::statement("SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED");
            
            return redirect()->route('admin.pending-admins')
                ->with('success', 'Admin berhasil disetujui');
        } catch (\Exception $e) {
            // Rollback jika terjadi error
            DB::rollBack();
            
            return redirect()->route('admin.pending-admins')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    public function rejectAdmin(User $user)
    {
        // Hanya superadmin yang bisa menolak admin
        if (auth()->user()->role_id != 1) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Anda tidak memiliki akses untuk menolak admin');
        }
        
        // Pastikan user adalah admin yang belum diapprove
        if ($user->role_id != 2 || $user->is_approved) {
            return redirect()->route('admin.pending-admins')
                ->with('error', 'User ini bukan admin yang pending atau sudah diapprove');
        }
        
        try {
            // Gunakan transaksi database untuk memastikan perubahan tersimpan
            DB::beginTransaction();
            
            // Update role dan status approval dengan query langsung ke database
            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'role_id' => 3,
                    'is_approved' => true
                ]);
            
            // Commit transaksi
            DB::commit();
            
            // Refresh cache
            DB::statement("SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED");
            
            return redirect()->route('admin.pending-admins')
                ->with('success', 'Admin ditolak dan diubah menjadi mahasiswa');
        } catch (\Exception $e) {
            // Rollback jika terjadi error
            DB::rollBack();
            
            return redirect()->route('admin.pending-admins')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
} 