<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pesanans', function (Blueprint $table) {
            // Add new columns for booking system
            $table->foreignId('paket_hari_id')->nullable()->after('id')->constrained('paket_haris')->onDelete('set null');
            $table->string('email')->after('nama');
            $table->string('telepon')->after('email');
            $table->integer('jumlah_orang')->default(1)->after('telepon');
            $table->date('tanggal_berangkat')->after('jumlah_orang');
            $table->text('catatan')->nullable()->after('tanggal_berangkat');
            $table->string('status')->default('pending')->after('aksi'); // pending, confirmed, cancelled, completed
            $table->string('order_id')->nullable()->after('status');
            
            // Rename 'waktu' to 'created_at' is handled by timestamps
            // 'total' already exists for total price
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pesanans', function (Blueprint $table) {
            $table->dropForeign(['paket_hari_id']);
            $table->dropColumn([
                'paket_hari_id',
                'email',
                'telepon',
                'jumlah_orang',
                'tanggal_berangkat',
                'catatan',
                'status',
                'order_id',
            ]);
        });
    }
};
