<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    // Nama tabel kalau tidak default 'settings'
    protected $table = 'settings';

    // Field yang bisa diisi mass assign
    protected $fillable = [
        'nama',
        'deskripsi',
        'image',
        'footer',
        // tambahkan field lain sesuai kebutuhan
    ];

    // Jika kamu ingin menyimpan file gambar di storage dan akses lewat URL,
    // kamu bisa buat accessor untuk url gambar:
    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }
}
