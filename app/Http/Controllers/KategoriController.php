<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class KategoriController extends Controller
{
    public function index()
    {
        $kategoris = Kategori::withCount('paketHaris')
            ->get()
            ->map(function($kategori) {
                return [
                    'id' => $kategori->id,
                    'nama_kategori' => $kategori->nama_kategori,
                    'deskripsi' => $kategori->deskripsi,
                    'image' => $kategori->image,
                    'image_url' => $kategori->image
                        ? asset('storage/uploads/admin/kategori/' . $kategori->image)
                        : null,
                    'jumlah_paket' => $kategori->paket_haris_count,
                    'created_at' => $kategori->created_at,
                    'updated_at' => $kategori->updated_at,
                ];
            });
            
        return Inertia::render('Admin/PaketWisata/Kategori/Index', [
            'kategoris' => $kategoris
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/PaketWisata/Kategori/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategoris,nama_kategori',
            'deskripsi'     => 'nullable|string',
            'image'         => 'nullable|file|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $image     = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->storeAs('uploads/admin/kategori', $imageName, 'public');
            $validated['image'] = $imageName;
        }

        Kategori::create($validated);
        return Redirect::route('paketwisata.index');
    }

    public function edit($id)
    {
        $kategori = Kategori::findOrFail($id);
        return Inertia::render('Admin/PaketWisata/Kategori/Edit', [
            'kategori' => array_merge($kategori->toArray(), [
                'image_url' => $kategori->image
                    ? asset('storage/uploads/admin/kategori/' . $kategori->image)
                    : null,
            ]),
        ]);
    }

    public function update(Request $request, $id)
    {
        $kategori = Kategori::findOrFail($id);

        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategoris,nama_kategori,' . $id,
            'deskripsi'     => 'nullable|string',
            'image'         => 'nullable|file|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Hapus gambar lama
            if ($kategori->image) {
                Storage::disk('public')->delete('uploads/admin/kategori/' . $kategori->image);
            }
            $image     = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->storeAs('uploads/admin/kategori', $imageName, 'public');
            $validated['image'] = $imageName;
        }

        $kategori->update($validated);
        return Redirect::route('paketwisata.index');
    }

    public function destroy($id)
    {
        $kategori = Kategori::findOrFail($id);
        if ($kategori->image) {
            Storage::disk('public')->delete('uploads/admin/kategori/' . $kategori->image);
        }
        $kategori->delete();
        return Redirect::route('paketwisata.index');
    }

    // API endpoint untuk dropdown
    public function getKategoris()
    {
        $kategoris = Kategori::all();
        return response()->json($kategoris);
    }

    // API endpoint untuk paket haris berdasarkan kategori
    public function getPaketHaris($id)
    {
        $kategori = Kategori::findOrFail($id);
        $paketHaris = $kategori->paketHaris()->orderBy('durasi_hari', 'asc')->get();
        return response()->json($paketHaris);
    }

    // Paket Hari Management
    public function paketHari($id)
    {
        $kategori = Kategori::findOrFail($id);
        $paketHari = $kategori->paketHaris()->orderBy('durasi_hari', 'asc')->get();
        
        return Inertia::render('Admin/PaketWisata/Kategori/PaketHari', [
            'kategori' => $kategori,
            'paketHari' => $paketHari
        ]);
    }

    public function storePaketHari(Request $request, $id)
    {
        $validated = $request->validate([
            'durasi_hari' => 'required|integer|min:1',
            'harga' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
        ]);

        $kategori = Kategori::findOrFail($id);
        $kategori->paketHaris()->create($validated);

        return Redirect::route('kategori.paket-hari', $id);
    }

    public function updatePaketHari(Request $request, $kategoriId, $paketId)
    {
        $validated = $request->validate([
            'durasi_hari' => 'required|integer|min:1',
            'harga' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
        ]);

        $paketHari = \App\Models\PaketHari::findOrFail($paketId);
        $paketHari->update($validated);

        return Redirect::route('kategori.paket-hari', $kategoriId);
    }

    public function destroyPaketHari($kategoriId, $paketId)
    {
        $paketHari = \App\Models\PaketHari::findOrFail($paketId);
        $paketHari->delete();

        return Redirect::route('kategori.paket-hari', $kategoriId);
    }
}
