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
        Schema::table('products', function (Blueprint $table) {
            // Ubah kolom uuid menjadi char(36) tanpa default UUID()
            $table->char('uuid', 36)->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Kembalikan ke definisi sebelumnya (opsional, sesuaikan jika perlu)
            $table->uuid('uuid')->nullable()->change();
        });
    }
};
