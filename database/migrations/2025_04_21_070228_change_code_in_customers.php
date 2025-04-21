<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if the unique constraint exists and drop it
        if (Schema::hasTable('customers')) {
            $indexExists = collect(DB::select("SHOW INDEXES FROM customers WHERE Key_name = 'customers_code_unique'"))->count() > 0;
            
            if ($indexExists) {
                Schema::table('customers', function (Blueprint $table) {
                    $table->dropUnique('customers_code_unique');
                });
            }
        }
        
        // Change the column type
        Schema::table('customers', function (Blueprint $table) {
            $table->string('code')->nullable()->change();
        });
        
        // Check for duplicates before adding the unique constraint back
        $duplicates = DB::table('customers')
            ->select('code', DB::raw('COUNT(*) as count'))
            ->whereNotNull('code')
            ->groupBy('code')
            ->having('count', '>', 1)
            ->get();
            
        if ($duplicates->isEmpty()) {
            Schema::table('customers', function (Blueprint $table) {
                $table->unique('code');
            });
        } else {
            // Log or output a warning that uniqueness couldn't be restored
            error_log('Warning: Uniqueness constraint not applied due to duplicate entries in customers.code');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the unique constraint if it exists
        if (Schema::hasTable('customers')) {
            $indexExists = collect(DB::select("SHOW INDEXES FROM customers WHERE Key_name = 'customers_code_unique'"))->count() > 0;
            
            if ($indexExists) {
                Schema::table('customers', function (Blueprint $table) {
                    $table->dropUnique('customers_code_unique');
                });
            }
        }
        
        // Change back to bigInteger
        Schema::table('customers', function (Blueprint $table) {
            $table->bigInteger('code')->nullable()->change();
        });
        
        // Add the unique constraint back
        Schema::table('customers', function (Blueprint $table) {
            $table->unique('code');
        });
    }
};