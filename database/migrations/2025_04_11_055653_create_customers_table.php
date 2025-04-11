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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('marketing_id');
            $table->unsignedBigInteger('bank_id');
            $table->unsignedBigInteger('customer_group_id');
            $table->unsignedBigInteger('customer_category_id');
            $table->bigInteger('code');
            $table->enum('type', ['individual', 'company', 'internal'])->default('individual')->nullable();
            $table->bigInteger('phone1')->nullable();
            $table->bigInteger('phone2')->nullable();
            $table->string('name');
            $table->enum('status', ['active', 'inactive']); 
            $table->bigInteger('no_rek');
            $table->string('atas_nama');
            $table->string('country');
            $table->string('city');
            $table->date('borndate')->nullable();
            $table->text('street1')->nullable();
            $table->text('street2')->nullable();
            $table->text('street_item')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->date('created_date');
            $table->bigInteger('npwp')->nullable();
            $table->string('tax_address')->nullable();
            $table->foreign('marketing_id')->references('id')->on('marketings')->onDelete('cascade');
            $table->foreign('bank_id')->references('id')->on('banks')->onDelete(null);
            $table->foreign('customer_group_id')->references('id')->on('customer_groups')->onDelete('cascade');
            $table->foreign('customer_category_id')->references('id')->on('category_customers')->onDelete(null);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};