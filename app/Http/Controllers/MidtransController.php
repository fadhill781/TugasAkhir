<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Midtrans\Config;
use Midtrans\Snap;
use App\Models\PaketWisata;

class MidtransController extends Controller
{
    public function index()
    {
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
            ->toArray();

        return Inertia::render('User/PaketWisata/Index', [
            'kategoris' => $kategoris
        ]);
    }

    public function showKategori($id)
    {
        $kategori = \App\Models\Kategori::with(['paketHaris.paketWisatas'])->findOrFail($id);
        
        // Get all paket wisatas from this kategori for gallery
        $galleryImages = [];
        foreach ($kategori->paketHaris as $paketHari) {
            foreach ($paketHari->paketWisatas as $pw) {
                if ($pw->image) {
                    $galleryImages[] = asset('storage/uploads/admin/paket_wisata/' . $pw->image);
                }
            }
        }
        
        // Get all paket haris with their paket wisatas
        $paketHaris = $kategori->paketHaris()
            ->with('paketWisatas')
            ->orderBy('durasi_hari', 'asc')
            ->get()
            ->map(function($paketHari) {
                return [
                    'id' => $paketHari->id,
                    'durasi_hari' => $paketHari->durasi_hari,
                    'harga' => $paketHari->harga,
                    'deskripsi' => $paketHari->deskripsi,
                    'paket_wisatas' => $paketHari->paketWisatas->map(function($pw) {
                        return array_merge($pw->toArray(), [
                            'image_url' => $pw->image ? asset('storage/uploads/admin/paket_wisata/' . $pw->image) : null
                        ]);
                    })
                ];
            });

        return Inertia::render('User/PaketWisata/KategoriDetail', [
            'kategori' => $kategori,
            'paketHaris' => $paketHaris,
            'galleryImages' => array_slice($galleryImages, 0, 6), // Limit to 6 images
        ]);
    }

    public function show($id)
    {
        // Get the paket wisata with its relations
        $paketWisata = PaketWisata::with('paketHari.kategori')->findOrFail($id);
        $kategori = $paketWisata->paketHari->kategori;
        
        // Get all paket haris for this kategori for the dropdown
        $paketHaris = \App\Models\PaketHari::where('kategori_id', $kategori->id)
            ->with(['paketWisatas' => function($q) {
                $q->select('id', 'paket_hari_id', 'nama_wisata', 'short_description', 'long_description');
            }])
            ->orderBy('durasi_hari', 'asc')
            ->get()
            ->map(function($ph) {
                // Ambil paket wisata pertama sebagai representasi deskripsi per durasi
                $pw = $ph->paketWisatas->first();
                return [
                    'id'                => $ph->id,
                    'durasi_hari'       => $ph->durasi_hari,
                    'harga'             => $ph->harga,
                    'deskripsi'         => $ph->deskripsi,
                    'short_description' => $pw?->short_description ?? '',
                    'long_description'  => $pw?->long_description  ?? '',
                ];
            });

        // Check for existing draft for this user and paket wisata
        $existingDraft = null;
        if (auth()->check()) {
            $existingDraft = \App\Models\Pesanan::where('user_id', auth()->id())
                ->where('is_draft', true)
                ->whereHas('paketHari', function($query) use ($kategori) {
                    $query->where('kategori_id', $kategori->id);
                })
                ->orderBy('updated_at', 'desc')
                ->first();
        }

        return Inertia::render('User/PaketWisata/Detail', [
            'paketWisata' => array_merge($paketWisata->toArray(), [
                'image_url' => $paketWisata->image ? asset('storage/uploads/admin/paket_wisata/' . $paketWisata->image) : null,
            ]),
            'kategori' => $kategori,
            'paketHaris' => $paketHaris,
            'existingDraft' => $existingDraft,
        ]);
    }

    public function saveDraft(Request $request)
    {
        // Validate draft data (more lenient than full booking)
        $validated = $request->validate([
            'paket_hari_id' => 'nullable|exists:paket_haris,id',
            'nama' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'telepon' => 'nullable|string|max:20',
            'jumlah_orang' => 'nullable|integer|min:1',
            'tanggal_berangkat' => 'nullable|date',
            'catatan' => 'nullable|string',
        ]);

        // Get paket hari if provided
        $totalHarga = 0;
        if (!empty($validated['paket_hari_id']) && !empty($validated['jumlah_orang'])) {
            $paketHari = \App\Models\PaketHari::find($validated['paket_hari_id']);
            if ($paketHari) {
                $totalHarga = $paketHari->harga * $validated['jumlah_orang'];
            }
        }

        // Check if draft already exists for this user
        $draft = \App\Models\Pesanan::where('user_id', auth()->id())
            ->where('is_draft', true)
            ->first();

        if ($draft) {
            // Update existing draft
            $draft->update(array_merge($validated, [
                'total' => $totalHarga,
                'waktu' => now(),
            ]));
        } else {
            // Create new draft
            $draft = \App\Models\Pesanan::create(array_merge($validated, [
                'user_id' => auth()->id(),
                'waktu' => now(),
                'total' => $totalHarga,
                'status' => 'draft',
                'is_draft' => true,
                'aksi' => 'Draft',
            ]));
        }

        return response()->json([
            'success' => true,
            'draft_id' => $draft->id,
            'message' => 'Draft berhasil disimpan'
        ]);
    }

    public function storeBooking(Request $request)
    {
        // Validate booking data
        $validated = $request->validate([
            'paket_hari_id' => 'required|exists:paket_haris,id',
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telepon' => 'required|string|max:20',
            'jumlah_orang' => 'required|integer|min:1',
            'tanggal_berangkat' => 'required|date',
            'catatan' => 'nullable|string',
            'draft_id' => 'nullable|exists:pesanans,id',
        ]);

        // Get paket hari to calculate total
        $paketHari = \App\Models\PaketHari::findOrFail($validated['paket_hari_id']);
        $totalHarga = $paketHari->harga * $validated['jumlah_orang'];

        // Create order ID
        $orderId = 'ORDER-' . time() . '-' . $validated['paket_hari_id'];

        // If updating from draft, update the draft record
        if (!empty($validated['draft_id'])) {
            $pesanan = \App\Models\Pesanan::findOrFail($validated['draft_id']);
            $pesanan->update([
                'paket_hari_id' => $validated['paket_hari_id'],
                'nama' => $validated['nama'],
                'email' => $validated['email'],
                'telepon' => $validated['telepon'],
                'jumlah_orang' => $validated['jumlah_orang'],
                'tanggal_berangkat' => $validated['tanggal_berangkat'],
                'catatan' => $validated['catatan'],
                'waktu' => now(),
                'total' => $totalHarga,
                'status' => 'pending',
                'order_id' => $orderId,
                'aksi' => 'Menunggu Pembayaran',
                'is_draft' => false,
            ]);
        } else {
            // Delete any existing drafts for this user
            \App\Models\Pesanan::where('user_id', auth()->id())
                ->where('is_draft', true)
                ->delete();

            // Create new order
            $pesanan = \App\Models\Pesanan::create([
                'user_id' => auth()->id(),
                'paket_hari_id' => $validated['paket_hari_id'],
                'nama' => $validated['nama'],
                'email' => $validated['email'],
                'telepon' => $validated['telepon'],
                'jumlah_orang' => $validated['jumlah_orang'],
                'tanggal_berangkat' => $validated['tanggal_berangkat'],
                'catatan' => $validated['catatan'],
                'waktu' => now(),
                'total' => $totalHarga,
                'status' => 'pending',
                'order_id' => $orderId,
                'aksi' => 'Menunggu Pembayaran',
                'is_draft' => false,
            ]);
        }

        // Send booking confirmation email
        try {
            $kategori = $paketHari->kategori->nama_kategori;
            $durasiHari = $paketHari->durasi_hari;
            
            \Mail::to($validated['email'])->send(
                new \App\Mail\BookingConfirmation($pesanan, $kategori, $durasiHari)
            );
        } catch (\Exception $e) {
            \Log::error('Failed to send booking confirmation email: ' . $e->getMessage());
        }

        // Store booking data in session for checkout
        session([
            'booking_data' => [
                'pesanan_id' => $pesanan->id,
                'paket_hari_id' => $validated['paket_hari_id'],
                'nama' => $validated['nama'],
                'email' => $validated['email'],
                'telepon' => $validated['telepon'],
                'jumlah_orang' => $validated['jumlah_orang'],
                'tanggal_berangkat' => $validated['tanggal_berangkat'],
                'catatan' => $validated['catatan'],
                'total_harga' => $totalHarga,
                'durasi_hari' => $paketHari->durasi_hari,
                'order_id' => $orderId,
            ]
        ]);

        // Get first paket wisata from this paket hari for display
        $paketWisata = PaketWisata::where('paket_hari_id', $validated['paket_hari_id'])->first();
        
        if (!$paketWisata) {
            return back()->with('error', 'Paket wisata tidak ditemukan');
        }

        // Redirect to checkout with paket wisata id
        return redirect()->route('checkout', ['id' => $paketWisata->id]);
    }

    public function resumeCheckout($pesananId)
    {
        $pesanan = \App\Models\Pesanan::with('paketHari.kategori')->findOrFail($pesananId);

        // Pastikan pesanan milik user yang login
        if ($pesanan->user_id !== auth()->id()) {
            abort(403);
        }

        $kategoriNama = $pesanan->paketHari?->kategori?->nama_kategori ?? 'Paket Wisata';
        $durasiHari   = $pesanan->paketHari?->durasi_hari ?? '';

        // Cari paket wisata jika ada, tapi tidak wajib
        $paketWisata = PaketWisata::where('paket_hari_id', $pesanan->paket_hari_id)->first();

        $packageData = [
            'id'               => $paketWisata?->id ?? $pesanan->paket_hari_id,
            'nama_wisata'      => $kategoriNama . ($durasiHari ? " ({$durasiHari} Hari)" : ''),
            'harga'            => $pesanan->total,
            'image'            => $paketWisata?->image ?? null,
            'short_description'=> $paketWisata?->short_description ?? null,
            'booking_info'     => [
                'nama'              => $pesanan->nama,
                'email'             => $pesanan->email,
                'telepon'           => $pesanan->telepon,
                'jumlah_orang'      => $pesanan->jumlah_orang,
                'tanggal_berangkat' => $pesanan->tanggal_berangkat,
                'catatan'           => $pesanan->catatan,
                'total_harga'       => $pesanan->total,
            ],
        ];

        return Inertia::render('Checkout', [
            'package'  => $packageData,
            'order_id' => $pesanan->order_id,
        ]);
    }

    public function checkout($id)
    {
        $package = PaketWisata::findOrFail($id);
        
        // Get booking data from session
        $bookingData = session('booking_data');
        
        // If no booking data, use default package price
        $totalHarga = $bookingData['total_harga'] ?? $package->harga;
        $orderId = $bookingData['order_id'] ?? 'ORDER-' . time() . '-' . $package->id;
        
        // Create modified package object with booking details
        $packageWithBooking = array_merge($package->toArray(), [
            'harga' => $totalHarga,
            'booking_info' => $bookingData,
        ]);

        return Inertia::render('Checkout', [
            'package' => $packageWithBooking,
            'order_id' => $orderId,
        ]);
    }

    public function createPayment(Request $request)
    {
        // Konfigurasi Midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;

        // Get booking data from session
        $bookingData = session('booking_data');
        
        // Use booking data if available, otherwise use default
        $customerName = $bookingData['nama'] ?? 'Customer';
        $customerEmail = $bookingData['email'] ?? 'customer@email.com';
        $customerPhone = $bookingData['telepon'] ?? '';

        $params = [
            'transaction_details' => [
                'order_id' => $request->order_id,
                'gross_amount' => (int) $request->amount,
            ],
            'customer_details' => [
                'first_name' => $customerName, 
                'email' => $customerEmail,
                'phone' => $customerPhone,
            ],
            'item_details' => [
                [
                    'id' => $request->package_id,
                    'price' => (int) $request->amount,
                    'quantity' => 1,
                    'name' => $request->package_name,
                ]
            ],
        ];

        $snapToken = Snap::getSnapToken($params);

        return response()->json([
            'snap_token' => $snapToken,
        ]);
    }

    public function handleNotification(Request $request)
    {
        // Konfigurasi Midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');

        try {
            $notification = new \Midtrans\Notification();
            
            $transactionStatus = $notification->transaction_status;
            $fraudStatus = $notification->fraud_status;
            $orderId = $notification->order_id;

            // Find pesanan by order_id
            $pesanan = \App\Models\Pesanan::where('order_id', $orderId)->first();

            if (!$pesanan) {
                return response()->json(['message' => 'Order not found'], 404);
            }

            $oldStatus = $pesanan->status;
            $newStatus = $oldStatus;

            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'accept') {
                    // Payment success
                    $pesanan->update([
                        'status' => 'confirmed',
                        'aksi' => 'Pembayaran Berhasil'
                    ]);
                    $newStatus = 'confirmed';
                }
            } elseif ($transactionStatus == 'settlement') {
                // Payment success
                $pesanan->update([
                    'status' => 'confirmed',
                    'aksi' => 'Pembayaran Berhasil'
                ]);
                $newStatus = 'confirmed';
            } elseif ($transactionStatus == 'pending') {
                // Payment pending
                $pesanan->update([
                    'status' => 'pending',
                    'aksi' => 'Menunggu Pembayaran'
                ]);
                $newStatus = 'pending';
            } elseif ($transactionStatus == 'deny' || $transactionStatus == 'expire' || $transactionStatus == 'cancel') {
                // Payment failed/cancelled
                $pesanan->update([
                    'status' => 'cancelled',
                    'aksi' => 'Pembayaran Gagal/Dibatalkan'
                ]);
                $newStatus = 'cancelled';
            }

            // Send email notification if status changed
            if ($oldStatus !== $newStatus && $pesanan->email) {
                try {
                    $paketHari = $pesanan->paketHari;
                    if ($paketHari && $paketHari->kategori) {
                        $kategori = $paketHari->kategori->nama_kategori;
                        $durasiHari = $paketHari->durasi_hari;
                        
                        \Mail::to($pesanan->email)->send(
                            new \App\Mail\PaymentStatusNotification($pesanan, $kategori, $durasiHari, $newStatus)
                        );
                    }
                } catch (\Exception $e) {
                    \Log::error('Failed to send payment status email: ' . $e->getMessage());
                }
            }

            return response()->json(['message' => 'Notification handled successfully']);

        } catch (\Exception $e) {
            \Log::error('Midtrans notification error: ' . $e->getMessage());
            return response()->json(['message' => 'Error processing notification'], 500);
        }
    }

    public function success()
    {
        return Inertia::render('Success');
    }

    /**
     * Update status directly from frontend after Midtrans callback.
     * This handles cases where webhook can't reach localhost (sandbox).
     */
    public function updateStatusFromClient(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|string',
            'transaction_status' => 'required|string',
            'fraud_status' => 'nullable|string',
        ]);

        $pesanan = \App\Models\Pesanan::where('order_id', $validated['order_id'])->first();

        if (!$pesanan) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $oldStatus = $pesanan->status;
        $transactionStatus = $validated['transaction_status'];
        $fraudStatus = $validated['fraud_status'] ?? 'accept';

        if ($transactionStatus === 'settlement' || ($transactionStatus === 'capture' && $fraudStatus === 'accept')) {
            $pesanan->update(['status' => 'confirmed', 'aksi' => 'Pembayaran Berhasil']);
            $newStatus = 'confirmed';
        } elseif ($transactionStatus === 'pending') {
            $pesanan->update(['status' => 'pending', 'aksi' => 'Menunggu Pembayaran']);
            $newStatus = 'pending';
        } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
            $pesanan->update(['status' => 'cancelled', 'aksi' => 'Pembayaran Gagal/Dibatalkan']);
            $newStatus = 'cancelled';
        } else {
            $newStatus = $oldStatus;
        }

        // Send email if status changed
        if ($oldStatus !== $newStatus && $pesanan->email) {
            try {
                $paketHari = $pesanan->paketHari;
                if ($paketHari && $paketHari->kategori) {
                    \Mail::to($pesanan->email)->send(
                        new \App\Mail\PaymentStatusNotification(
                            $pesanan,
                            $paketHari->kategori->nama_kategori,
                            $paketHari->durasi_hari,
                            $newStatus
                        )
                    );
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send payment status email: ' . $e->getMessage());
            }
        }

        return response()->json(['message' => 'Status updated', 'status' => $newStatus]);
    }
}
