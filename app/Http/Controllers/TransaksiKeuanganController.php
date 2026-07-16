<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Pesanan;
use Illuminate\Http\Request;

class TransaksiKeuanganController extends Controller
{
    public function create()
    {
        $paketHaris = \App\Models\PaketHari::with('kategori')
            ->orderBy('kategori_id')
            ->orderBy('durasi_hari')
            ->get()
            ->map(fn($p) => [
                'id'         => $p->id,
                'durasi_hari'=> $p->durasi_hari,
                'harga'      => $p->harga,
                'kategori'   => $p->kategori?->nama_kategori ?? '-',
            ]);

        return Inertia::render('Admin/Transaksi/TransaksiBaru', [
            'paketHaris' => $paketHaris,
        ]);
    }

    public function index(Request $request)
    {
        $bulan  = $request->query('bulan', now()->month);
        $tahun  = $request->query('tahun', now()->year);

        // Semua pesanan yang sudah confirmed (lunas)
        $baseQuery = Pesanan::with('paketHari.kategori')
            ->where('status', 'confirmed');

        // Stats keseluruhan
        $totalPendapatan   = (clone $baseQuery)->sum('total');
        $totalTransaksi    = (clone $baseQuery)->count();

        // Stats bulan ini
        $pendapatanBulanIni = (clone $baseQuery)
            ->whereMonth('updated_at', $bulan)
            ->whereYear('updated_at', $tahun)
            ->sum('total');
        $transaksiBulanIni  = (clone $baseQuery)
            ->whereMonth('updated_at', $bulan)
            ->whereYear('updated_at', $tahun)
            ->count();

        // Pesanan pending (belum bayar)
        $totalPending = Pesanan::where('status', 'pending')->count();
        $nilaiPending = Pesanan::where('status', 'pending')->sum('total');

        // Riwayat transaksi (filter bulan/tahun)
        $transaksis = Pesanan::with('paketHari.kategori')
            ->when($request->query('status'), fn($q, $s) => $q->where('status', $s))
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(fn($p) => [
                'id'                => $p->id,
                'order_id'          => $p->order_id,
                'nama'              => $p->nama,
                'email'             => $p->email,
                'kategori'          => $p->paketHari?->kategori?->nama_kategori ?? '-',
                'paket_hari'        => $p->paketHari ? $p->paketHari->durasi_hari . ' Hari' : '-',
                'jumlah_orang'      => $p->jumlah_orang,
                'total'             => $p->total,
                'status'            => $p->status,
                'tanggal_berangkat' => $p->tanggal_berangkat,
                'waktu_bayar'       => $p->updated_at->format('d M Y H:i'),
                'created_at'        => $p->created_at->format('d M Y'),
            ]);

        // Pendapatan per bulan (12 bulan terakhir untuk chart)
        $pendapatanPerBulan = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $total = Pesanan::where('status', 'confirmed')
                ->whereMonth('updated_at', $date->month)
                ->whereYear('updated_at', $date->year)
                ->sum('total');
            $pendapatanPerBulan[] = [
                'bulan' => $date->format('M Y'),
                'total' => $total,
            ];
        }

        return Inertia::render('Admin/Transaksi/Dashboard', [
            'stats' => [
                'total_pendapatan'    => $totalPendapatan,
                'total_transaksi'     => $totalTransaksi,
                'pendapatan_bulan_ini'=> $pendapatanBulanIni,
                'transaksi_bulan_ini' => $transaksiBulanIni,
                'total_pending'       => $totalPending,
                'nilai_pending'       => $nilaiPending,
            ],
            'transaksis'           => $transaksis,
            'pendapatan_per_bulan' => $pendapatanPerBulan,
            'filter'               => ['bulan' => $bulan, 'tahun' => $tahun],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'              => 'required|string|max:255',
            'email'             => 'required|email|max:255',
            'telepon'           => 'required|string|max:20',
            'paket_hari_id'     => 'nullable|exists:paket_haris,id',
            'jumlah_orang'      => 'required|integer|min:1',
            'tanggal_berangkat' => 'required|date',
            'catatan'           => 'nullable|string',
            'total'             => 'required|integer|min:0',
            'status'            => 'required|in:pending,confirmed,cancelled',
            'waktu'             => 'required|date',
        ]);

        $aksi = match($validated['status']) {
            'confirmed' => 'Pembayaran Lunas',
            'pending'   => 'Menunggu Pembayaran',
            'cancelled' => 'Dibatalkan',
            default     => 'Menunggu Pembayaran',
        };

        \App\Models\Pesanan::create([
            'user_id'           => auth()->id(),
            'paket_hari_id'     => $validated['paket_hari_id'],
            'nama'              => $validated['nama'],
            'email'             => $validated['email'],
            'telepon'           => $validated['telepon'],
            'jumlah_orang'      => $validated['jumlah_orang'],
            'tanggal_berangkat' => $validated['tanggal_berangkat'],
            'catatan'           => $validated['catatan'],
            'waktu'             => $validated['waktu'],
            'total'             => $validated['total'],
            'status'            => $validated['status'],
            'aksi'              => $aksi,
            'order_id'          => 'ORDER-' . time(),
        ]);

        return redirect()->route('transaksi.keuangan')
            ->with('success', 'Transaksi berhasil disimpan.');
    }
}
