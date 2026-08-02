<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Pesanan;
use Illuminate\Http\Request;

class TransaksiKeuanganController extends Controller
{
    public function create()
    {
        return Inertia::render('Admin/Transaksi/TransaksiBaru');
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
        // Tidak digunakan — invoice manual tidak disimpan ke database
        return redirect()->route('transaksi.keuangan');
    }

    public function previewInvoiceManual(Request $request)
    {
        $raw  = $request->input('data');
        $data = json_decode($raw, true);

        if (!$data) abort(400, 'Data tidak valid.');

        $items = collect($data['items'] ?? [])->map(fn($i) => [
            'deskripsi'  => $i['deskripsi']  ?? '',
            'keterangan' => $i['keterangan'] ?? '',
            'qty'        => max(1, intval($i['qty']   ?? 1)),
            'harga'      => max(0, intval($i['harga'] ?? 0)),
        ])->filter(fn($i) => !empty($i['deskripsi']))->values();

        return view('invoice-manual', [
            'noInvoice'        => $data['no_invoice']        ?? '-',
            'tanggal'          => $data['tanggal']           ?? now()->toDateString(),
            'status'           => $data['status']            ?? 'Lunas',
            'nama'             => $data['nama']              ?? '-',
            'telepon'          => $data['telepon']           ?? '',
            'tanggalBerangkat' => $data['tanggal_berangkat'] ?? '',
            'keterangan'       => $data['keterangan']        ?? '',
            'dp'               => max(0, intval($data['dp'] ?? 0)),
            'items'            => $items,
        ]);
    }
}
