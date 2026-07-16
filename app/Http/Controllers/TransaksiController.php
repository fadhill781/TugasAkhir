<?php


namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TransaksiController extends Controller
{
    public function index()
    {
        $transaksi = Transaksi::latest()->get();
        return Inertia::render('admin/transaksi/transaksi', [
            'transaksi' => $transaksi
        ]);
    }

    public function create()
    {
        return Inertia::render('admin/transaksi/createtransaksi');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'waktu' => 'required|date',
            'total' => 'required|numeric'
        ]);

        Transaksi::create($request->only('nama', 'waktu', 'total'));

        return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $transaksi = Transaksi::findOrFail($id);
        return Inertia::render('admin/transaksi/createtransaksi', [
            'edit' => true,
            'transaksi' => $transaksi
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'waktu' => 'required|date',
            'total' => 'required|numeric'
        ]);

        $transaksi = Transaksi::findOrFail($id);
        $transaksi->update($request->only('nama', 'waktu', 'total'));

        return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil diupdate.');
    }

    public function destroy($id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $transaksi->delete();

        return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil dihapus.');
    }
}
