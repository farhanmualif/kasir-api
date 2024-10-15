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
            $table->string("no_purchasing", 21);
            $table->unsignedBigInteger("product_id");
            $table->integer("quantity");
            $table->string("description", 200)->nullable();
            $table->decimal("total_payment");
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
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
