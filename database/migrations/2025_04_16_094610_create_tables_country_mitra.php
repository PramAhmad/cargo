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
        Schema::create('country_mitra', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('mitra_id')->index();
            $table->string('name');
            $table->foreign('mitra_id')->references('id')->on('mitras')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('country_mitra');
        Schema::table('country_mitra', function (Blueprint $table) {
            $table->dropForeign(['mitra_id']);
        });
    }
};
