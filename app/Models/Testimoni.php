<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimoni extends Model
{
    protected $fillable = [
        'testimoni',
        'nama_pentestimoni',
        'asal',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
