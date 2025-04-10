<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Change the due_date column from its current type to integer in marketings table.
     */
    public function up(): void
    {
        Schema::table('marketings', function (Blueprint $table) {
            // Check if the column exists first
            if (Schema::hasColumn('marketings', 'due_date')) {
                // Simply modify the column type to integer
                $table->integer('due_date')->nullable()->change();
            } else {
                // If it doesn't exist, add it
                $table->integer('due_date')->nullable()->after('address_tax');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('marketings', function (Blueprint $table) {
            // Change back to string (varchar) if needed
            $table->string('due_date', 50)->nullable()->change();
        });
    }
};
