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
        // Remove bank_id from mitras table
        Schema::table('mitras', function (Blueprint $table) {
            $table->dropForeign(['bank_id']);
            $table->dropColumn(['bank_id', 'no_rek', 'atas_nama']);
        });
        
        // Remove bank_id from marketings table
        Schema::table('marketings', function (Blueprint $table) {
            $table->dropForeign(['bank_id']);
            $table->dropColumn(['bank_id', 'no_rek', 'atas_nama']);
        });
        
        // Remove bank_id from customers table
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['bank_id']);
            $table->dropColumn(['bank_id', 'no_rek', 'atas_nama']);
        });
        
        // Create new mitra_banks table
        Schema::create('mitra_banks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mitra_id')->constrained()->onDelete('cascade');
            $table->foreignId('bank_id')->nullable()->constrained()->onDelete('set null');
            $table->text('rek_no')->nullable();
            $table->text('rek_name')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            
            // Add a unique constraint to ensure only one default bank per mitra
        });
        
        // Create new marketing_banks table
        Schema::create('marketing_banks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marketing_id')->constrained()->onDelete('cascade');
            $table->foreignId('bank_id')->nullable()->constrained()->onDelete('set null');
            $table->text('rek_no')->nullable();
            $table->text('rek_name')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            
            // Add a unique constraint to ensure only one default bank per marketing
        });
        
        // Create new customer_banks table
        Schema::create('customer_banks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('bank_id')->nullable()->constrained()->onDelete('set null');
            $table->text('rek_no')->nullable();
            $table->text('rek_name')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            
            // Add a unique constraint to ensure only one default bank per customer
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the new tables
        Schema::dropIfExists('mitra_banks');
        Schema::dropIfExists('marketing_banks');
        Schema::dropIfExists('customer_banks');
        
        // Add back bank_id to mitras table
        Schema::table('mitras', function (Blueprint $table) {
            $table->foreignId('bank_id')->nullable()->constrained()->onDelete('set null');
            $table->string('no_rek')->nullable();
            $table->string('atas_nama')->nullable();
        });
        
        // Add back bank_id to marketings table
        Schema::table('marketings', function (Blueprint $table) {
            $table->foreignId('bank_id')->nullable()->constrained()->onDelete('set null');
            $table->string('no_rek')->nullable();
            $table->string('atas_nama')->nullable();
        });
        
        // Add back bank_id to customers table
        Schema::table('customers', function (Blueprint $table) {
            $table->foreignId('bank_id')->nullable()->constrained()->onDelete('set null');
            $table->string('no_rek')->nullable();
            $table->string('atas_nama')->nullable();
        });
    }
};
