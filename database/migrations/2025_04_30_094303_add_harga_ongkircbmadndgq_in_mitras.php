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
        Schema::table('mitras', function (Blueprint $table) {
            // drop harga_ongkir
            $table->dropColumn('harga_ongkir');
            // add harga_ongkircbmadndgq
            $table->integer('harga_ongkir_cbm')->default(0)->nullable();
            $table->integer('harga_ongkir_wg')->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mitras', function (Blueprint $table) {
            $table->dropColumn('harga_ongkir_cbm');
            $table->dropColumn('harga_ongkir_wg');
            $table->integer('harga_ongkir')->default(0)->nullable();
        });
    }
};
