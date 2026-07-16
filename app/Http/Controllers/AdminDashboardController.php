<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Pesanan;
use App\Models\PaketWisata;
use App\Models\Mobil;
use App\Models\Kategori;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Stats
        $totalPesanan   = Pesanan::count();
        $pesananPending = Pesanan::where('status', 'pending')->count();
        $pesananLunas   = Pesanan::where('status', 'confirmed')->count();
        $totalPendapatan = Pesanan::where('status', 'confirmed')->sum('total');
        $totalPaket     = PaketWisata::count();
        $totalMobil     = Mobil::count();
        $totalKategori  = Kategori::count();

        // Pesanan terbaru
        $pesananTerbaru = Pesanan::with('paketHari.kategori')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(fn($p) => [
                'id'                => $p->id,
                'order_id'          => $p->order_id,
                'nama'              => $p->nama,
                'kategori'          => $p->paketHari?->kategori?->nama_kategori ?? '-',
                'total'             => $p->total,
                'status'            => $p->status,
                'tanggal_berangkat' => $p->tanggal_berangkat,
                'created_at'        => $p->created_at->format('d M Y'),
            ]);

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'total_pesanan'    => $totalPesanan,
                'pesanan_pending'  => $pesananPending,
                'pesanan_lunas'    => $pesananLunas,
                'total_pendapatan' => $totalPendapatan,
                'total_paket'      => $totalPaket,
                'total_mobil'      => $totalMobil,
                'total_kategori'   => $totalKategori,
            ],
            'pesanan_terbaru' => $pesananTerbaru,
        ]);
    }
}
