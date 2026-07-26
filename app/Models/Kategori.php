<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
