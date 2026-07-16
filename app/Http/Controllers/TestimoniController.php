<?php

namespace App\Http\Controllers;

use App\Models\Testimoni;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TestimoniController extends Controller
{
    public function index()
    {
        $testimonis = Testimoni::orderBy('created_at', 'desc')->get();
        
        return Inertia::render('Admin/Testimoni/Index', [
            'testimonis' => $testimonis,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Testimoni/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'testimoni' => 'required|string',
            'nama_pentestimoni' => 'required|string|max:255',
            'asal' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        Testimoni::create($validated);

        return redirect()->route('testimoni.index')->with('success', 'Testimoni berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $testimoni = Testimoni::findOrFail($id);
        
        return Inertia::render('Admin/Testimoni/Edit', [
            'testimoni' => $testimoni,
        ]);
    }

    public function update(Request $request, $id)
    {
        $testimoni = Testimoni::findOrFail($id);
        
        $validated = $request->validate([
            'testimoni' => 'required|string',
            'nama_pentestimoni' => 'required|string|max:255',
            'asal' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $testimoni->update($validated);

        return redirect()->route('testimoni.index')->with('success', 'Testimoni berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $testimoni = Testimoni::findOrFail($id);
        $testimoni->delete();

        return redirect()->route('testimoni.index')->with('success', 'Testimoni berhasil dihapus.');
    }
}
