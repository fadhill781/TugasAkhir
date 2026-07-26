<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PaketHari;
use App\Models\PaketWisata;

class Kategori extends Model
{
    protected $fillable = [
        'nama_kategori',
        'deskripsi',
        'image',
    ];

    public function paketHaris()
    {
        return $this->hasMany(PaketHari::class);
    }

    public function paketWisatas()
    {
        return $this->hasManyThrough(PaketWisata::class, PaketHari::class);
    }
}
