<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kategori;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        $kategoris = [
            [
                'nama_kategori' => 'Jogjakarta',
                'deskripsi' => 'Wisata budaya dan sejarah di Jogjakarta'
            ],
            [
                'nama_kategori' => 'Bali',
                'deskripsi' => 'Wisata pantai dan budaya di Bali'
            ],
            [
                'nama_kategori' => 'Bromo',
                'deskripsi' => 'Wisata gunung dan alam di Bromo'
            ],
            [
                'nama_kategori' => 'Lombok',
                'deskripsi' => 'Wisata pantai dan gunung di Lombok'
            ],
            [
                'nama_kategori' => 'Bandung',
                'deskripsi' => 'Wisata kuliner dan alam di Bandung'
            ],
        ];

        foreach ($kategoris as $kategori) {
            Kategori::firstOrCreate(
                ['nama_kategori' => $kategori['nama_kategori']],
                ['deskripsi' => $kategori['deskripsi']]
            );
        }
    }
}
