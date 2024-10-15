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
        Schema::create('detail_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_transaction');
            $table->unsignedBigInteger('id_product');
            $table->integer('item_price');
            $table->integer('total_price');
            $table->integer('quantity');
            $table->timestamps();

            $table->foreign('id_transaction')->references('id')->on('transactions')->onDelete('cascade');
            $table->foreign('id_product')->references('id')->on('products')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_transactions');
    }
};
