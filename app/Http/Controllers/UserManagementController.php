<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class UserManagementController extends Controller
{
    /**
     * Tampilkan semua user (untuk superadmin)
     */
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')
            ->get()
            ->map(fn($u) => [
                'id'         => $u->id,
                'name'       => $u->name,
                'email'      => $u->email,
                'role'       => $u->role,
                'role_label' => $u->role_label,
                'is_active'  => $u->is_active,
                'created_at' => $u->created_at->format('d M Y'),
            ]);

        return Inertia::render('Admin/SuperAdmin/UserManagement', [
            'users' => $users,
        ]);
    }

    /**
     * Buat akun admin/user baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => ['required', Password::min(8)],
            'role'     => 'required|in:admin,user',
        ]);

        User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => $validated['role'],
        ]);

        return redirect()->route('superadmin.users')->with('success', 'Akun berhasil dibuat.');
    }

    /**
     * Update role user
     */
    public function updateRole(Request $request, User $user)
    {
        // Superadmin tidak bisa diubah rolenya
        if ($user->role === 'superadmin') {
            return back()->with('error', 'Role superadmin tidak dapat diubah.');
        }

        $validated = $request->validate([
            'role' => 'required|in:admin,user',
        ]);

        $user->update(['role' => $validated['role']]);

        return back()->with('success', 'Role berhasil diperbarui.');
    }

    /**
     * Reset password user
     */
    public function resetPassword(Request $request, User $user)
    {
        if ($user->role === 'superadmin') {
            return back()->with('error', 'Password superadmin tidak dapat direset dari sini.');
        }

        $validated = $request->validate([
            'password' => ['required', Password::min(8), 'confirmed'],
        ]);

        $user->update(['password' => Hash::make($validated['password'])]);

        return back()->with('success', 'Password berhasil direset.');
    }

    /**
     * Toggle status aktif/nonaktif user
     */
    public function toggleActive(User $user)
    {
        if ($user->role === 'superadmin') {
            return back()->with('error', 'Status superadmin tidak dapat diubah.');
        }

        // Cegah nonaktifkan diri sendiri
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $user->update(['is_active' => !$user->is_active]);

        $message = $user->is_active ? 'Akun berhasil diaktifkan.' : 'Akun berhasil dinonaktifkan.';
        
        return back()->with('success', $message);
    }

    /**
     * Lihat profil dan history pesanan user
     */
    public function show(User $user)
    {
        // Load pesanan user
        $pesanans = \App\Models\Pesanan::with('paketHari.kategori')
            ->where('user_id', $user->id)
            ->where('is_draft', false)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($p) {
                return [
                    'id' => $p->id,
                    'order_id' => $p->order_id,
                    'nama' => $p->nama,
                    'email' => $p->email,
                    'telepon' => $p->telepon,
                    'paket_hari' => $p->paketHari ? $p->paketHari->durasi_hari . ' Hari' : '-',
                    'kategori' => $p->paketHari?->kategori?->nama_kategori ?? '-',
                    'jumlah_orang' => $p->jumlah_orang,
                    'tanggal_berangkat' => $p->tanggal_berangkat,
                    'total' => $p->total,
                    'status' => $p->status,
                    'catatan' => $p->catatan,
                    'aksi' => $p->aksi,
                    'created_at' => $p->created_at->format('d M Y H:i'),
                    'bukti_pembayaran' => $p->bukti_pembayaran
                        ? asset('storage/bukti_pembayaran/' . $p->bukti_pembayaran)
                        : null,
                ];
            });

        return Inertia::render('Admin/SuperAdmin/UserProfile', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'role_label' => $user->role_label,
                'is_active' => $user->is_active,
                'created_at' => $user->created_at->format('d M Y H:i'),
            ],
            'pesanans' => $pesanans,
        ]);
    }
}
