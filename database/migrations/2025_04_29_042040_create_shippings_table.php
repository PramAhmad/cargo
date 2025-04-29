<?php

use App\Enums\PaymentType;
use App\Enums\ShippingStatus;
use App\Enums\ShippingType;
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
        Schema::create('shippings', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('invoice');
            $table->text('invoice_file')->nullable();
            $table->text('packagelist_file')->nullable();
            $table->date('transaction_date');
            $table->date('receipt_date')->nullable();
            $table->date('stuffing_date')->nullable();
            $table->date('due_date')->nullable();
            $table->enum('payment_type', [
                PaymentType::Transfer->value,
                PaymentType::COD->value,
                PaymentType::Tempo->value,
            ])->default(PaymentType::Transfer->value);
            $table->integer('top')->nullable();
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
            $table->string('description')->nullable();
            $table->foreignId('bank_id')->nullable();
            $table->string('rek_no', 255)->nullable();
            $table->string('rek_name', 255)->nullable();

            $table->string('marking', 255)->nullable();
            $table->string('supplier', 255)->nullable();
            $table->enum('shipping_type', [
                ShippingType::FCL->value,
                ShippingType::LCL->value,
            ])
                ->nullable()
                ->default(ShippingType::LCL->value);

            $table->double('sup_agent')->nullable();
            $table->double('cukai')->nullable();
            $table->double('tbh')->nullable();
            $table->double('ppnbm')->nullable();

            $table->double('freight')->nullable();
            $table->double('do')->nullable();
            $table->double('pfpd')->nullable();
            $table->double('charge')->nullable();

            $table->double('jkt_sda')->nullable();
            $table->double('sda_user')->nullable();
            $table->double('bkr')->nullable();
            $table->double('asuransi')->nullable();

            $table->double('nilai')->nullable();
            $table->double('nilai_biaya')->nullable();
            $table->double('biaya')->nullable();

            $table->double('pph')->nullable();
            $table->double('biaya_kirim')->nullable();
            $table->double('ppn')->nullable();
            $table->double('ppn_total')->nullable();
            $table->text('pajak')->nullable();
            $table->double('grand_total')->nullable();

            $table->double('ctns_total')->nullable();
            $table->double('qty_total')->nullable();
            $table->double('gw')->nullable();

            $table->double('gw_total')->nullable()->default(0);
            $table->double('kg_price')->nullable()->default(0);
            $table->double('total_price_gw')->nullable()->default(0);

            $table->double('cbm_total')->nullable()->default(0);
            $table->double('cbm_price')->nullable()->default(0);
            $table->double('total_price_cbm')->nullable()->default(0);
            $table->foreignId('mitra_id')->nullable();
            $table->foreignId('warehouse_id')->nullable();
            $table->foreignId('customer_id');
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shippings');
    }
};
