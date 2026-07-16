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
        // Drop foreign key using raw SQL (safer across different DB states)
        try {
            \DB::statement('ALTER TABLE paket_wisatas DROP FOREIGN KEY paket_wisatas_kategori_id_foreign');
        } catch (\Exception $e) {
            // Foreign key doesn't exist, skip
        }

        Schema::table('paket_wisatas', function (Blueprint $table) {
            // Drop kategori_id column if exists
            if (Schema::hasColumn('paket_wisatas', 'kategori_id')) {
                $table->dropColumn('kategori_id');
            }
            
            // Add paket_hari_id column if not exists
            if (!Schema::hasColumn('paket_wisatas', 'paket_hari_id')) {
                $table->foreignId('paket_hari_id')->nullable()->after('id')->constrained('paket_haris')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paket_wisatas', function (Blueprint $table) {
            // Drop paket_hari_id column
            $table->dropForeign(['paket_hari_id']);
            $table->dropColumn('paket_hari_id');
            
            // Add back kategori_id column
            $table->foreignId('kategori_id')->nullable()->after('id');
        });
    }
};
