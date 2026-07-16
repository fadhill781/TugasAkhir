<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksis'; 

    protected $fillable = [
        'nama_pemesan',
        'paket',
        'tanggal',
        'jumlah_orang',
        'status',
        'total_harga',
    ];
}
