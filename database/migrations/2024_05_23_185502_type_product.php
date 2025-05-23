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
        Schema::create('type_product', function (Blueprint $table) {
            $table->unsignedBigInteger('type_id');
            $table->unsignedBigInteger('product_id');
            $table->primary(['type_id', 'product_id']);


            $table->foreign('type_id')->references('id')->on('types_products');
            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('type_product');
    }
};
