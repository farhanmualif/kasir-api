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
       Schema::create('user_store', function(Blueprint $table){
        $table->unsignedBigInteger('user_id');
        $table->unsignedBigInteger('store_id');


        $table->foreign('user_id')->references('id')->on('users');
        $table->foreign('store_id')->references('id')->on('stores');
       });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_store');
    }
};
