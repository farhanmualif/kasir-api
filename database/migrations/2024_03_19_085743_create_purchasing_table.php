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
        Schema::create('purchasing', function (Blueprint $table) {
            $table->id();
            $table->string("no_purchasing");
            $table->unsignedBigInteger("product_id");
            $table->integer("quantity");
            $table->string("description");
            $table->decimal("total_payment");
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('product');
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchasing');
    }
};
