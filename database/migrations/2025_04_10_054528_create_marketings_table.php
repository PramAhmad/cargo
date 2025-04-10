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
        Schema::create('marketings', function (Blueprint $table) {
            $table->id();
            $table->timestamps();   
            $table->unsignedBigInteger('bank_id')->nullable();
            $table->unsignedBigInteger('marketing_group_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('code');
            $table->string('name');
            $table->string('no_rek')->nullable();
            $table->text('address')->nullable();
            $table->string('atas_nama')->nullable();
            $table->string('city')->nullable();
            $table->string('phone1')->nullable();
            $table->string('phone2')->nullable();
            $table->date('borndate')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('ktp')->nullable();
            $table->string('npwp')->nullable();
            $table->text('requirement')->nullable();
            $table->text('address_tax')->nullable();
            $table->date('due_date')->nullable();
            $table->enum('status', ['active', 'nonactive'])->default('active');
            $table->foreign('bank_id')->references('id')->on('banks')->onDelete(null);
            $table->foreign('marketing_group_id')->references('id')->on('marketing_groups')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketings');
    }
};
