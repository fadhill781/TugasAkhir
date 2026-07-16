<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaketHari extends Model
{
    protected $fillable = [
        'kategori_id',
        'durasi_hari',
        'harga',
        'deskripsi',
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    public function paketWisatas()
    {
        return $this->hasMany(PaketWisata::class, 'paket_hari_id');
    }
}
