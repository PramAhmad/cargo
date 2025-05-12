<?php

use App\Enums\TaxType;
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
        Schema::create('taxes', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->enum('type', [
                TaxType::PERCENTAGE->value,
                TaxType::FIXED->value,
            ])->default(TaxType::PERCENTAGE->value);
            $table->string('value');
            $table->boolean('is_active')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxes');
    }
};
