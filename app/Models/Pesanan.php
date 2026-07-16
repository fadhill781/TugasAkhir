<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    protected $table = 'pesanans';
    
    protected $fillable = [
        'user_id',
        'paket_hari_id',
        'nama',
        'email',
        'telepon',
        'jumlah_orang',
        'tanggal_berangkat',
        'catatan',
        'waktu',
        'total',
        'aksi',
        'status',
        'order_id',
        'bukti_pembayaran',
        'is_draft',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function paketHari()
    {
        return $this->belongsTo(PaketHari::class, 'paket_hari_id');
    }
}
