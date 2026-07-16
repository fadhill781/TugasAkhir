<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaketWisata extends Model
{
    protected $table = 'paket_wisatas';

    protected $fillable = [
        'nama_wisata',
        'paket_hari_id',
        'short_description',
        'long_description',
        'image',
        'harga',
        'active_days',
        'max_personil'
    ];

    public function paketHari()
    {
        return $this->belongsTo(PaketHari::class, 'paket_hari_id');
    }
}