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
        Schema::create('shipping_details', function (Blueprint $table) {
            $table->id();
            $table->string('product_image')->nullable();
            $table->string('ctn')->default(0);
            $table->double('qty_per_ctn')->default(0);
            $table->double('ctns')->default(0);
            $table->double('qty')->default(0);
            $table->double('length')->default(0);
            $table->double('width')->default(0);
            $table->double('high')->default(0);
            $table->double('volume')->default(0);
            $table->double('gw_per_ctn')->default(0);
            $table->double('total_gw')->default(0);
            $table->foreignId('shipping_id');
            $table->foreignId('product_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_details');
    }
};
