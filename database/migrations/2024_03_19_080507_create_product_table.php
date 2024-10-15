<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique()->default(DB::raw('(UUID())'));
            $table->string('name', 100);
            $table->string('barcode', 15)->nullable();
            $table->string('image')->default('product-default.png');
            $table->integer('stock');
            $table->decimal('selling_price');
            $table->decimal('purchase_price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
