<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mobil extends Model
{
    protected $table = 'mobils';

    protected $fillable = [
        'nama',
        'harga',
        'image',
        'durasi',
    ];
}