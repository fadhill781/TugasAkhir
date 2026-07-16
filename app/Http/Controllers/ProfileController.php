<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        $pesanans = \App\Models\Pesanan::with('paketHari.kategori')
            ->where('user_id', $request->user()->id)
            ->where('is_draft', false) // Exclude drafts from history
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
                    'bukti_pembayaran' => $p->bukti_pembayaran
                        ? asset('storage/bukti_pembayaran/' . $p->bukti_pembayaran)
                        : null,
                ];
            });

        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
            'pesanans' => $pesanans,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
