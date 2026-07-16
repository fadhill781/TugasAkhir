<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmsSetting extends Model
{
    protected $table = 'cms_settings';

    protected $fillable = [
        'nama',
        'deskripsi',
        'image',
        'footer',
        'gallery',
        'testimoni',
        'nama_pentestimoni',
    ];

    protected $casts = [
        'gallery' => 'array',
    ];

     public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    public function getGalleryUrlsAttribute()
    {
        if (!$this->gallery) {
            return [];
        }
        
        return array_map(function($path) {
            return asset('storage/' . $path);
        }, $this->gallery);
    }
}

