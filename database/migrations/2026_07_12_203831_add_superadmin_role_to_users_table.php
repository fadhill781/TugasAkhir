<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Role column already exists, just ensure 'superadmin' value is valid
        // The column already supports string values, so no change needed
        // We just need to update the existing enum if it exists or leave as is
        Schema::table('users', function (Blueprint $table) {
            // Ensure role can hold 'superadmin' - if it's already a string column, this is a no-op
            // If needed, modify the column
        });
    }

    public function down(): void
    {
        //
    }
};
