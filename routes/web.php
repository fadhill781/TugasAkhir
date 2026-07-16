<?php

use App\Http\Controllers\MidtransController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\MobilController;
use App\Http\Controllers\PaketWisataController;
use App\Http\Controllers\CmsSettingController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\UserMiddleware;

// SuperAdmin routes
Route::middleware(['auth', 'superadmin'])->prefix('superadmin')->group(function () {
    Route::get('/users', [\App\Http\Controllers\UserManagementController::class, 'index'])->name('superadmin.users');
    Route::post('/users', [\App\Http\Controllers\UserManagementController::class, 'store'])->name('superadmin.users.store');
    Route::get('/users/{user}', [\App\Http\Controllers\UserManagementController::class, 'show'])->name('superadmin.users.show');
    Route::put('/users/{user}/role', [\App\Http\Controllers\UserManagementController::class, 'updateRole'])->name('superadmin.users.role');
    Route::put('/users/{user}/password', [\App\Http\Controllers\UserManagementController::class, 'resetPassword'])->name('superadmin.users.password');
    Route::put('/users/{user}/toggle-active', [\App\Http\Controllers\UserManagementController::class, 'toggleActive'])->name('superadmin.users.toggle');
});

use Inertia\Inertia;

Route::get('/', [CmsSettingController::class, 'landing']);

Route::get('/login-user', function() {
    // Force start session
    if (!session()->isStarted()) {
        session()->start();
    }
    return Inertia::render('Auth/LoginUser');
})->name('login');

Route::get('/login-admin', function() {
    // Force start session
    if (!session()->isStarted()) {
        session()->start();
    }
    return Inertia::render('Auth/LoginAdmin');
})->name('login.admin');

Route::get('/register', function() {
    // Force start session
    if (!session()->isStarted()) {
        session()->start();
    }
    return Inertia::render('Auth/Register');
})->name('register');

// Auth routes must be included before middleware groups
require __DIR__ . '/auth.php';

// Additional login POST routes for clarity
Route::post('/login-user', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store'])->name('login.user.post');
Route::post('/login-admin', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store'])->name('login.admin.post');

Route::get('/paket-wisata-public', [MidtransController::class, 'index'])->name('paket-wisata-public');
Route::get('/paket-wisata-public/{id}', [MidtransController::class, 'show'])->name('paket-wisata-public.show');
Route::get('/destinasi/{id}/paket', [MidtransController::class, 'showKategori'])->name('kategori.paket');
Route::get('/rental-mobil', [MobilController::class, 'rental'])->name('rental.mobil');
Route::get('/rental-mobil/{id}', [MobilController::class, 'detail'])->name('rental.mobil.detail');
Route::get('/galeri-public', [\App\Http\Controllers\GaleriController::class, 'publicIndex'])->name('galeri.public');
Route::post('/booking/draft', [MidtransController::class, 'saveDraft'])->middleware('auth')->name('booking.draft');
Route::post('/booking', [MidtransController::class, 'storeBooking'])->name('booking.store');
Route::get('/checkout/success', [MidtransController::class, 'success'])->name('checkout.success');
Route::get('/checkout/{id}', [MidtransController::class, 'checkout'])->name('checkout');

Route::post('/midtrans/pay', [MidtransController::class, 'createPayment'])
    ->name('midtrans.pay');

Route::post('/midtrans/notification', [MidtransController::class, 'handleNotification'])
    ->name('midtrans.notification');

// Called directly from frontend after Midtrans success callback
Route::post('/midtrans/update-status', [MidtransController::class, 'updateStatusFromClient'])
    ->name('midtrans.update-status');

Route::prefix('admin/transaksi')->group(function () {
    Route::get('/', [TransaksiController::class, 'index'])->name('transaksi.index');
    Route::get('/create', [TransaksiController::class, 'create'])->name('transaksi.create');
    Route::post('/', [TransaksiController::class, 'store'])->name('transaksi.store');
    Route::get('/{id}/edit', [TransaksiController::class, 'edit'])->name('transaksi.edit');
    Route::put('/{id}', [TransaksiController::class, 'update'])->name('transaksi.update');
    Route::delete('/{id}', [TransaksiController::class, 'destroy'])->name('transaksi.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', UserMiddleware::class])->group(function () {
    Route::get('/homepage', [CmsSettingController::class, 'homepage'])->name('homepage');
    Route::get('/riwayat-pesanan', [PesananController::class, 'historyUser'])->name('pesanan.history');
    Route::get('/user/invoice/{id}', [PesananController::class, 'generateInvoice'])->name('pesanan.invoice.user');
    Route::post('/pesanan/{id}/bukti', [PesananController::class, 'uploadBukti'])->name('pesanan.bukti');
    Route::get('/galeri', [\App\Http\Controllers\GaleriController::class, 'index'])->name('galeri.index');
});

Route::middleware(['auth', AdminMiddleware::class])->group(function () {

    Route::get('/admin/dashboard', [\App\Http\Controllers\AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/transaksi-keuangan', [\App\Http\Controllers\TransaksiKeuanganController::class, 'index'])->name('transaksi.keuangan');
    Route::get('/admin/transaksi-baru', [\App\Http\Controllers\TransaksiKeuanganController::class, 'create'])->name('transaksi.keuangan.create');
    Route::post('/admin/transaksi-baru', [\App\Http\Controllers\TransaksiKeuanganController::class, 'store'])->name('transaksi.keuangan.store');

    Route::get('add-paket-wisata', [PaketWisataController::class, 'addpaketwisata']);
    Route::post('add-paket-wisata', [PaketWisataController::class, 'store']);
    Route::get('/paket-wisata', [PaketWisataController::class, 'paketwisata'])->name('paketwisata.index');
    Route::get('paket-wisata/{paketwisata}/edit', [PaketWisataController::class, 'editpaketwisata']);
    Route::put('/paket-wisata/{paketwisata}', [PaketWisataController::class, 'update']);
    Route::delete('/paket-wisata/{paketwisata}', [PaketWisataController::class, 'destroy']);

    Route::get('add-mobil', [MobilController::class, 'addmobil']);
    Route::post('add-mobil', [MobilController::class, 'store']);
    Route::get('/mobil', [MobilController::class, 'mobil'])->name('mobil.index');
    Route::get('mobil/{mobil}/edit', [MobilController::class, 'editmobil']);
    Route::put('/mobil/{mobil}', [MobilController::class, 'update']);
    Route::delete('/mobil/{mobil}', [MobilController::class, 'destroy']);

    Route::get('/CMS/Setting/AddSetting', [CmsSettingController::class, 'addcmssetting'])->name('cmssetting.add');
    Route::post('/cms-setting', [CmsSettingController::class, 'store'])->name('cmssetting.store');
    Route::get('/cms-setting', [CmsSettingController::class, 'cmssetting'])->name('cmssetting.index');
    Route::get('/cms-setting/{id}/edit', [CmsSettingController::class, 'edit'])->name('cmssetting.edit');
    Route::put('/cms-setting/{id}', [CmsSettingController::class, 'update'])->name('cmssetting.update');
    Route::delete('/cms-setting/{id}', [CmsSettingController::class, 'destroy'])->name('cmssetting.destroy');

    // Testimoni Routes
    Route::get('/testimoni', [App\Http\Controllers\TestimoniController::class, 'index'])->name('testimoni.index');
    Route::get('/testimoni/create', [App\Http\Controllers\TestimoniController::class, 'create'])->name('testimoni.create');
    Route::post('/testimoni', [App\Http\Controllers\TestimoniController::class, 'store'])->name('testimoni.store');
    Route::get('/testimoni/{id}/edit', [App\Http\Controllers\TestimoniController::class, 'edit'])->name('testimoni.edit');
    Route::put('/testimoni/{id}', [App\Http\Controllers\TestimoniController::class, 'update'])->name('testimoni.update');
    Route::delete('/testimoni/{id}', [App\Http\Controllers\TestimoniController::class, 'destroy'])->name('testimoni.destroy');

    // Kategori Routes (semua berjalan di /paket-wisata)
    Route::get('/kategori', [App\Http\Controllers\KategoriController::class, 'index'])->name('kategori.index');
    Route::get('/kategori/create', [App\Http\Controllers\KategoriController::class, 'create'])->name('kategori.create');
    Route::post('/kategori', [App\Http\Controllers\KategoriController::class, 'store'])->name('kategori.store');
    Route::get('/kategori/{id}/edit', [App\Http\Controllers\KategoriController::class, 'edit'])->name('kategori.edit');
    Route::put('/kategori/{id}', [App\Http\Controllers\KategoriController::class, 'update'])->name('kategori.update');
    Route::delete('/kategori/{id}', [App\Http\Controllers\KategoriController::class, 'destroy'])->name('kategori.destroy');
    Route::get('/api/kategoris', [App\Http\Controllers\KategoriController::class, 'getKategoris'])->name('api.kategoris');
    Route::get('/api/kategoris/{id}/paket-haris', [App\Http\Controllers\KategoriController::class, 'getPaketHaris'])->name('api.kategoris.paket-haris');
    
    // Paket Hari Routes
    Route::get('/kategori/{id}/paket-hari', [App\Http\Controllers\KategoriController::class, 'paketHari'])->name('kategori.paket-hari');
    Route::post('/kategori/{id}/paket-hari', [App\Http\Controllers\KategoriController::class, 'storePaketHari'])->name('kategori.paket-hari.store');
    Route::put('/kategori/{kategoriId}/paket-hari/{paketId}', [App\Http\Controllers\KategoriController::class, 'updatePaketHari'])->name('kategori.paket-hari.update');
    Route::delete('/kategori/{kategoriId}/paket-hari/{paketId}', [App\Http\Controllers\KategoriController::class, 'destroyPaketHari'])->name('kategori.paket-hari.destroy');

    Route::get('add-pesanan', [PesananController::class, 'addpesanan']);
    Route::post('add-pesanan', [PesananController::class, 'store']);
    Route::get('/pesanan', [PesananController::class, 'pesanan'])->name('pesanan.index');
    Route::get('pesanan/{pesanan}/edit', [PesananController::class, 'editpesanan']);
    Route::put('/pesanan/{pesanan}', [PesananController::class, 'update']);
    Route::put('/pesanan/{pesanan}/status', [PesananController::class, 'updateStatus'])->name('pesanan.updateStatus');
    Route::get('/pesanan/{id}/invoice', [PesananController::class, 'generateInvoice'])->name('pesanan.invoice');
    Route::delete('/pesanan/{pesanan}', [PesananController::class, 'destroy']);

    // Galeri Routes (Admin)
    Route::get('/galeri-admin', [\App\Http\Controllers\GaleriController::class, 'adminIndex'])->name('admin.galeri.index');
    Route::get('/galeri-admin/create', [\App\Http\Controllers\GaleriController::class, 'create'])->name('admin.galeri.create');
    Route::post('/galeri-admin', [\App\Http\Controllers\GaleriController::class, 'store'])->name('admin.galeri.store');
    Route::get('/galeri-admin/{id}/edit', [\App\Http\Controllers\GaleriController::class, 'edit'])->name('admin.galeri.edit');
    Route::put('/galeri-admin/{id}', [\App\Http\Controllers\GaleriController::class, 'update'])->name('admin.galeri.update');
    Route::delete('/galeri-admin/{id}', [\App\Http\Controllers\GaleriController::class, 'destroy'])->name('admin.galeri.destroy');

});
