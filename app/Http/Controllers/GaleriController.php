<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Galeri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GaleriController extends Controller
{
    // User - Lihat galeri
    public function index()
    {
        $setting = \App\Models\CmsSetting::first();
        
        $gallery = $setting && $setting->gallery_urls ? $setting->gallery_urls : [];

        return Inertia::render('User/Galeri', [
            'gallery' => $gallery,
            'setting' => $setting ? [
                'nama' => $setting->nama,
                'deskripsi' => $setting->deskripsi,
            ] : null,
        ]);
    }

    // Public - Lihat galeri (untuk user yang belum login)
    public function publicIndex()
    {
        $setting = \App\Models\CmsSetting::first();
        
        $gallery = $setting && $setting->gallery_urls ? $setting->gallery_urls : [];

        return Inertia::render('GaleriPublic', [
            'gallery' => $gallery,
            'setting' => $setting ? [
                'nama' => $setting->nama,
                'deskripsi' => $setting->deskripsi,
            ] : null,
        ]);
    }

    // Admin - Lihat daftar galeri
    public function adminIndex()
    {
        $galeri = Galeri::orderBy('created_at', 'desc')->get()->map(function($g) {
            return [
                'id' => $g->id,
                'judul' => $g->judul,
                'deskripsi' => $g->deskripsi,
                'gambar' => $g->gambar ? asset('storage/galeri/' . $g->gambar) : null,
                'created_at' => $g->created_at->format('d M Y'),
            ];
        });

        return Inertia::render('Admin/Galeri/Galeri', [
            'galeri' => $galeri
        ]);
    }

    // Admin - Form tambah galeri
    public function create()
    {
        return Inertia::render('Admin/Galeri/AddGaleri');
    }

    // Admin - Simpan galeri baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'gambar' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('galeri', $filename, 'public');
            $validated['gambar'] = $filename;
        }

        Galeri::create($validated);

        return redirect()->route('admin.galeri.index')->with('success', 'Galeri berhasil ditambahkan.');
    }

    // Admin - Form edit galeri
    public function edit($id)
    {
        $galeri = Galeri::findOrFail($id);
        
        return Inertia::render('Admin/Galeri/EditGaleri', [
            'galeri' => [
                'id' => $galeri->id,
                'judul' => $galeri->judul,
                'deskripsi' => $galeri->deskripsi,
                'gambar' => $galeri->gambar ? asset('storage/galeri/' . $galeri->gambar) : null,
            ]
        ]);
    }

    // Admin - Update galeri
    public function update(Request $request, $id)
    {
        $galeri = Galeri::findOrFail($id);

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($galeri->gambar) {
                Storage::disk('public')->delete('galeri/' . $galeri->gambar);
            }

            $file = $request->file('gambar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('galeri', $filename, 'public');
            $validated['gambar'] = $filename;
        }

        $galeri->update($validated);

        return redirect()->route('admin.galeri.index')->with('success', 'Galeri berhasil diperbarui.');
    }

    // Admin - Hapus galeri
    public function destroy($id)
    {
        $galeri = Galeri::findOrFail($id);

        // Hapus gambar dari storage
        if ($galeri->gambar) {
            Storage::disk('public')->delete('galeri/' . $galeri->gambar);
        }

        $galeri->delete();

        return redirect()->route('admin.galeri.index')->with('success', 'Galeri berhasil dihapus.');
    }
}
