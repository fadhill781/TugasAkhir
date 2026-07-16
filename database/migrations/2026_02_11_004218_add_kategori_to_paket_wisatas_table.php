<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paket_wisatas', function (Blueprint $table) {
            $table->string('kategori')->nullable()->after('nama_wisata'); // Kota A, Kota B, dll
        });
    }

    public function down(): void
    {
        Schema::table('paket_wisatas', function (Blueprint $table) {
            $table->dropColumn('kategori');
        });
    }
};
