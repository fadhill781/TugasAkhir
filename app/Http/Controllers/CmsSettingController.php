<?php

namespace App\Http\Controllers;

use App\Models\CmsSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class CmsSettingController extends Controller
{
   public function landing()
    {
        $setting = CmsSetting::first();

        $kategoris = \App\Models\Kategori::withCount('paketWisatas')
            ->get()
            ->map(function($kategori) {
                return [
                    'id'            => $kategori->id,
                    'nama_kategori' => $kategori->nama_kategori,
                    'deskripsi'     => $kategori->deskripsi,
                    'jumlah_paket'  => $kategori->paket_wisatas_count,
                    'image_url'     => $kategori->image
                        ? asset('storage/uploads/admin/kategori/' . $kategori->image)
                        : null,
                ];
            })
            ->take(3)
            ->toArray();

        $paketPopuler = \App\Models\PaketWisata::with('paketHari.kategori')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get()
            ->map(function($pw) {
                return [
                    'id' => $pw->id,
                    'nama_wisata' => $pw->nama_wisata,
                    'short_description' => $pw->short_description,
                    'max_personil' => $pw->max_personil,
                    'harga' => $pw->paketHari ? $pw->paketHari->harga : 0,
                    'durasi_hari' => $pw->paketHari ? $pw->paketHari->durasi_hari : 0,
                    'kategori' => $pw->paketHari && $pw->paketHari->kategori ? $pw->paketHari->kategori->nama_kategori : '-',
                    'image_url' => $pw->image ? asset('storage/uploads/admin/paket_wisata/' . $pw->image) : null,
                ];
            });

        $mobils = \App\Models\Mobil::take(6)->get()->map(function($m) {
            return [
                'id' => $m->id,
                'nama' => $m->nama,
                'harga' => $m->harga,
                'durasi' => $m->durasi,
                'image_url' => $m->image ? asset('storage/uploads/admin/mobil/' . $m->image) : null,
            ];
        });

        $testimonis = \App\Models\Testimoni::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        return Inertia::render('Landing', [
            'setting' => $setting ? array_merge($setting->toArray(), [
                'image_url' => $setting->image_url,
                'gallery_urls' => $setting->gallery_urls,
            ]) : null,
            'kategoris' => $kategoris,
            'paketPopuler' => $paketPopuler,
            'mobils' => $mobils,
            'testimonis' => $testimonis->toArray(),
        ]);
    }

    public function addcmssetting()
    {
        return Inertia::render('Admin/CMS/Setting/AddSetting');
    }

    public function create()
    {
        return Inertia::render('Admin/CMS/Setting/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'footer' => 'nullable|string|max:255',
            'gallery.*' => 'nullable|image|max:2048',
            'testimoni' => 'nullable|string',
            'nama_pentestimoni' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('cmssettings', 'public');
            $validated['image'] = $path;
        }

        // Handle multiple gallery images
        if ($request->hasFile('gallery')) {
            $galleryPaths = [];
            foreach ($request->file('gallery') as $file) {
                $path = $file->store('cmssettings/gallery', 'public');
                $galleryPaths[] = $path;
            }
            $validated['gallery'] = $galleryPaths;
        }

        CmsSetting::create($validated);

        return redirect()->route('cmssetting.index')->with('success', 'Setting berhasil dibuat.');
    }

    public function edit($id)
    {
        $cmsSetting = CmsSetting::findOrFail($id);
        
        return Inertia::render('Admin/CMS/Setting/EditSetting', [
            'cmsSetting' => $cmsSetting,
        ]);
    }

    public function cmssetting()
{
    $settings = CmsSetting::all();

    return Inertia::render('Admin/CMS/Setting/Setting', [
        'settings' => $settings,
    ]);
}

    public function update(Request $request, $id)
    {
        $cmsSetting = CmsSetting::findOrFail($id);
        
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'footer' => 'nullable|string|max:255',
            'gallery.*' => 'nullable|image|max:2048',
            'testimoni' => 'nullable|string',
            'nama_pentestimoni' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('image')) {
            // Hapus file lama jika ada
            if ($cmsSetting->image && Storage::disk('public')->exists($cmsSetting->image)) {
                Storage::disk('public')->delete($cmsSetting->image);
            }

            $path = $request->file('image')->store('cmssettings', 'public');
            $validated['image'] = $path;
        }

        // Handle multiple gallery images
        if ($request->hasFile('gallery')) {
            // Hapus gallery lama jika ada
            if ($cmsSetting->gallery) {
                foreach ($cmsSetting->gallery as $oldPath) {
                    if (Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->delete($oldPath);
                    }
                }
            }

            $galleryPaths = [];
            foreach ($request->file('gallery') as $file) {
                $path = $file->store('cmssettings/gallery', 'public');
                $galleryPaths[] = $path;
            }
            $validated['gallery'] = $galleryPaths;
        }

        $cmsSetting->update($validated);

        return redirect()->route('cmssetting.index')->with('success', 'Setting berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $cmsSetting = CmsSetting::findOrFail($id);
        
        if ($cmsSetting->image && Storage::disk('public')->exists($cmsSetting->image)) {
            Storage::disk('public')->delete($cmsSetting->image);
        }

        // Hapus gallery images jika ada
        if ($cmsSetting->gallery) {
            foreach ($cmsSetting->gallery as $galleryPath) {
                if (Storage::disk('public')->exists($galleryPath)) {
                    Storage::disk('public')->delete($galleryPath);
                }
            }
        }

        $cmsSetting->delete();

        return redirect()->route('cmssetting.index')->with('success', 'Setting berhasil dihapus.');
    }

    public function homepage()
    {
        $setting = CmsSetting::first();
    
        $kategoris = \App\Models\Kategori::withCount('paketWisatas')
            ->get()
            ->map(function($kategori) {
                return [
                    'id'            => $kategori->id,
                    'nama_kategori' => $kategori->nama_kategori,
                    'deskripsi'     => $kategori->deskripsi,
                    'jumlah_paket'  => $kategori->paket_wisatas_count,
                    'image_url'     => $kategori->image
                        ? asset('storage/uploads/admin/kategori/' . $kategori->image)
                        : null,
                ];
            })
            ->take(3)
            ->toArray();

    // Paket populer (ambil dari paket wisata terbaru)
    $paketPopuler = \App\Models\PaketWisata::with('paketHari.kategori')
        ->orderBy('created_at', 'desc')
        ->take(3)
        ->get()
        ->map(function($pw) {
            return [
                'id' => $pw->id,
                'nama_wisata' => $pw->nama_wisata,
                'short_description' => $pw->short_description,
                'max_personil' => $pw->max_personil,
                'harga' => $pw->paketHari ? $pw->paketHari->harga : 0,
                'durasi_hari' => $pw->paketHari ? $pw->paketHari->durasi_hari : 0,
                'kategori' => $pw->paketHari && $pw->paketHari->kategori ? $pw->paketHari->kategori->nama_kategori : '-',
                'image_url' => $pw->image ? asset('storage/uploads/admin/paket_wisata/' . $pw->image) : null,
            ];
        });

    // Mobil untuk sewa
    $mobils = \App\Models\Mobil::take(6)->get()->map(function($m) {
        return [
            'id' => $m->id,
            'nama' => $m->nama,
            'harga' => $m->harga,
            'durasi' => $m->durasi,
            'image_url' => $m->image ? asset('storage/uploads/admin/mobil/' . $m->image) : null,
        ];
    });

    $testimonis = \App\Models\Testimoni::where('is_active', true)
                    ->orderBy('created_at', 'desc')
                    ->take(3)
                    ->get();

    return Inertia::render('Homepage', [
        'setting' => $setting ? array_merge($setting->toArray(), [
            'image_url' => $setting->image_url,
            'gallery_urls' => $setting->gallery_urls
        ]) : null,
        'kategoris' => $kategoris,
        'paketPopuler' => $paketPopuler,
        'mobils' => $mobils,
        'testimonis' => $testimonis->toArray(),
    ]);
}

}
