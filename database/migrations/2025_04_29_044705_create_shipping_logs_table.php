<?php

use App\Enums\ShippingStatus;
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
        Schema::create('shipping_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('shipping_id');
           
            $table->enum('status', [
                ShippingStatus::waiting->value,
                ShippingStatus::rejected->value,
                ShippingStatus::sendAgentWarehouse->value,
                ShippingStatus::sendIndonesia->value,
                ShippingStatus::arrivedIndonesia->value,
                ShippingStatus::sendSidoarjo->value,
                ShippingStatus::arrivedSidoarjo->value,
                ShippingStatus::sendAddress->value,
                ShippingStatus::arrivedAddress->value,
                ShippingStatus::done->value,
            ])->default(ShippingStatus::waiting->value);
            $table->string('notes', 255)->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_logs');
    }
};
