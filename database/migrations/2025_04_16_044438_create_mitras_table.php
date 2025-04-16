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
        Schema::create('mitras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_id')->nullable()->constrained('banks')->nullOnDelete();
            $table->foreignId('mitra_group_id')->nullable()->constrained('mitra_groups')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            
            $table->string('code')->unique();
            $table->string('name');
            $table->string('address_office_indo')->nullable();
            $table->string('no_rek', 50)->nullable();
            $table->string('atas_nama')->nullable();
            $table->string('phone1', 20);
            $table->string('phone2', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->date('birthdate')->nullable();
            $table->date('borndate')->nullable();
            $table->date('created_date')->nullable();
            $table->string('ktp', 20)->nullable();
            $table->string('npwp', 30)->nullable();
            $table->text('tax_address')->nullable();
            $table->integer('syarat_bayar')->default(0)->comment('Payment terms in days');
            $table->integer('batas_tempo')->default(0)->comment('Due date in days');
            $table->boolean('status')->default(true);
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mitras');
    }
};
