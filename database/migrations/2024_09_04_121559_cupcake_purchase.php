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
        Schema::create('cupcake_purchase', function (Blueprint $table) {
            $table->foreignId('cupcake_id')->onUpdate('cascade')->onDelete('null');
            $table->foreignId('purchase_id')->onUpdate('cascade')->onDelete('cascade');
            $table->integer('quantity');
            $table->integer('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cupcake_purchase');
    }
};
