<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentStatusNotification;

class PesananController extends Controller
{
    public function addpesanan()
    {
        return Inertia::render('Admin/Pesanan/AddPesanan', []);
    }

    public function store(Request $request)
{
   $validated = $request->validate([
    'nama' => 'required|string|max:255',
    'waktu' => 'required|date',
    'total' => 'required|integer|min:0',
    'aksi' => 'nullable|string|max:255',
]);

    Pesanan::create($validated);

    return redirect()->route('pesanan.index')->with('success', 'Pesanan berhasil ditambahkan.');
}


    public function update(Request $request, Pesanan $pesanan)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'telepon' => 'nullable|string|max:20',
            'jumlah_orang' => 'nullable|integer|min:1',
            'tanggal_berangkat' => 'nullable|date',
            'catatan' => 'nullable|string',
            'waktu' => 'required|date',
            'total' => 'required|integer|min:0',
            'status' => 'required|in:pending,confirmed,cancelled',
        ]);

        // Update aksi based on status
        $aksi = match($validated['status']) {
            'confirmed' => 'Pembayaran Lunas',
            'pending' => 'Menunggu Pembayaran',
            'cancelled' => 'Dibatalkan',
            default => 'Menunggu Pembayaran',
        };

        $validated['aksi'] = $aksi;

        $pesanan->update($validated);

        return redirect()->route('pesanan.index')->with('success', 'Pesanan berhasil diperbarui.');
    }

    public function updateStatus(Request $request, Pesanan $pesanan)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled',
        ]);

        $oldStatus = $pesanan->status;

        // Update status and aksi based on status
        $aksi = match($validated['status']) {
            'confirmed' => 'Pembayaran Lunas',
            'pending' => 'Menunggu Pembayaran',
            'cancelled' => 'Dibatalkan',
            default => 'Menunggu Pembayaran',
        };

        $pesanan->update([
            'status' => $validated['status'],
            'aksi' => $aksi,
        ]);

        // Kirim email notifikasi jika status berubah
        if ($oldStatus !== $validated['status'] && $pesanan->email) {
            try {
                $pesanan->load('paketHari.kategori');
                $paketHari = $pesanan->paketHari;
                if ($paketHari && $paketHari->kategori) {
                    Mail::to($pesanan->email)->send(
                        new PaymentStatusNotification(
                            $pesanan,
                            $paketHari->kategori->nama_kategori,
                            $paketHari->durasi_hari,
                            $validated['status']
                        )
                    );
                }
            } catch (\Exception $e) {
                Log::error('Failed to send payment status email: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Status pembayaran berhasil diperbarui.');
    }

    public function pesanan()
    {
        $pesanan = Pesanan::with('paketHari.kategori')
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
                    'kategori' => $p->paketHari && $p->paketHari->kategori ? $p->paketHari->kategori->nama_kategori : '-',
                    'jumlah_orang' => $p->jumlah_orang,
                    'tanggal_berangkat' => $p->tanggal_berangkat,
                    'waktu' => $p->waktu ?? $p->created_at->format('Y-m-d'),
                    'total' => $p->total,
                    'status' => $p->status,
                    'aksi' => $p->aksi,
                    'catatan' => $p->catatan,
                    'bukti_pembayaran' => $p->bukti_pembayaran 
                        ? asset('storage/bukti_pembayaran/' . $p->bukti_pembayaran) 
                        : null,
                ];
            });
            
        return Inertia::render('Admin/Pesanan/Pesanan', [
            'pesanan' => $pesanan
        ]);
    }

    public function editPesanan(Pesanan $pesanan)
    {
        return Inertia::render('Admin/Pesanan/EditPesanan', [
            'pesanan' => $pesanan
        ]);
    }

    public function destroy(Pesanan $pesanan)
    {
        $pesanan->delete();

        return redirect()->route('pesanan.index')->with('success', 'Pesanan berhasil dihapus.');
    }

    public function notaManual()
    {
        return Inertia::render('Admin/Pesanan/NotaManual');
    }

    public function printNotaManual(Request $request)
    {
        $raw = $request->input('data');
        $data = json_decode($raw, true);

        if (!$data) {
            abort(400, 'Data tidak valid.');
        }

        // Sanitasi items
        $items = collect($data['items'] ?? [])->map(function ($item) {
            return [
                'deskripsi'   => $item['deskripsi'] ?? '',
                'keterangan'  => $item['keterangan'] ?? '',
                'qty'         => max(1, intval($item['qty'] ?? 1)),
                'harga'       => max(0, intval($item['harga'] ?? 0)),
            ];
        })->filter(fn($i) => !empty($i['deskripsi']))->values();

        return view('nota-manual', [
            'noNota'           => $data['no_nota']           ?? '-',
            'tanggal'          => $data['tanggal']           ?? now()->toDateString(),
            'status'           => $data['status']            ?? 'Pending',
            'nama'             => $data['nama']              ?? '-',
            'telepon'          => $data['telepon']           ?? '',
            'alamat'           => $data['alamat']            ?? '',
            'tanggalBerangkat' => $data['tanggal_berangkat'] ?? '',
            'jumlahOrang'      => $data['jumlah_orang']      ?? '',
            'durasi'           => $data['durasi']            ?? '',
            'dp'               => max(0, intval($data['dp'] ?? 0)),
            'catatan'          => $data['catatan']           ?? '',
            'items'            => $items,
        ]);
    }

    public function generateNota($id)
    {
        $pesanan = Pesanan::with('paketHari.kategori')->findOrFail($id);

        $kategori = $pesanan->paketHari && $pesanan->paketHari->kategori
            ? $pesanan->paketHari->kategori->nama_kategori
            : '-';

        $paketHari = $pesanan->paketHari
            ? $pesanan->paketHari->durasi_hari . ' Hari'
            : '-';

        $hargaPerOrang = $pesanan->jumlah_orang > 0
            ? $pesanan->total / $pesanan->jumlah_orang
            : 0;

        return view('nota', [
            'pesanan'      => $pesanan,
            'kategori'     => $kategori,
            'paketHari'    => $paketHari,
            'hargaPerOrang'=> $hargaPerOrang,
        ]);
    }

    public function generateInvoice($id)
    {
        $pesanan = Pesanan::with('paketHari.kategori')->findOrFail($id);
        
        $kategori = $pesanan->paketHari && $pesanan->paketHari->kategori 
            ? $pesanan->paketHari->kategori->nama_kategori 
            : '-';
        
        $paketHari = $pesanan->paketHari 
            ? $pesanan->paketHari->durasi_hari . ' Hari' 
            : '-';
        
        $hargaPerOrang = $pesanan->jumlah_orang > 0 
            ? $pesanan->total / $pesanan->jumlah_orang 
            : 0;

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('invoice', [
            'pesanan' => $pesanan,
            'kategori' => $kategori,
            'paketHari' => $paketHari,
            'hargaPerOrang' => $hargaPerOrang,
        ]);

        return $pdf->download('invoice-' . $pesanan->order_id . '.pdf');
    }

    public function historyUser(Request $request)
    {
        $userId = auth()->id();
        
        $pesanans = Pesanan::with('paketHari.kategori')
            ->where('user_id', $userId)
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
                    'kategori' => $p->paketHari && $p->paketHari->kategori ? $p->paketHari->kategori->nama_kategori : '-',
                    'jumlah_orang' => $p->jumlah_orang,
                    'tanggal_berangkat' => $p->tanggal_berangkat,
                    'total' => $p->total,
                    'status' => $p->status,
                    'catatan' => $p->catatan,
                    'bukti_pembayaran' => $p->bukti_pembayaran,
                ];
            });

        return Inertia::render('User/HistoryPesanan', [
            'pesanans' => $pesanans
        ]);
    }

    public function uploadBukti(Request $request, $id)
    {
        $request->validate([
            'bukti' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $pesanan = Pesanan::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($request->hasFile('bukti')) {
            $file = $request->file('bukti');
            $filename = time() . '_bukti_' . $file->getClientOriginalName();
            $path = $file->storeAs('bukti_pembayaran', $filename, 'public');
            $pesanan->update(['bukti_pembayaran' => $filename]);
        }

        return back()->with('success', 'Bukti pembayaran berhasil diunggah.');
    }
}
