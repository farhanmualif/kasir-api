<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('discounts', function (Blueprint $table) {
            // Hapus kolom yang tidak diperlukan
            $table->dropColumn('persentase');

            // Tambah kolom baru
            $table->enum('type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('value', 10, 2);
            $table->renameColumn('name', 'title'); // Optional: untuk konsistensi
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('discounts', function (Blueprint $table) {
            $table->dropColumn(['type', 'value']);
            $table->decimal('persentase', 5, 2)->nullable();
            $table->renameColumn('title', 'name');
        });
    }
};
