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
        // First let's make sure all SEA and AIR prices are properly set
        // Copy data from general columns to specific columns if they're not set
        DB::statement('UPDATE category_products SET 
            mit_price_cbm_sea = CASE WHEN mit_price_cbm_sea IS NULL OR mit_price_cbm_sea = 0 THEN mit_price_cbm ELSE mit_price_cbm_sea END,
            mit_price_kg_sea = CASE WHEN mit_price_kg_sea IS NULL OR mit_price_kg_sea = 0 THEN mit_price_kg ELSE mit_price_kg_sea END,
            cust_price_cbm_sea = CASE WHEN cust_price_cbm_sea IS NULL OR cust_price_cbm_sea = 0 THEN cust_price_cbm ELSE cust_price_cbm_sea END,
            cust_price_kg_sea = CASE WHEN cust_price_kg_sea IS NULL OR cust_price_kg_sea = 0 THEN cust_price_kg ELSE cust_price_kg_sea END,
            mit_price_cbm_air = CASE WHEN mit_price_cbm_air IS NULL OR mit_price_cbm_air = 0 THEN mit_price_cbm ELSE mit_price_cbm_air END,
            mit_price_kg_air = CASE WHEN mit_price_kg_air IS NULL OR mit_price_kg_air = 0 THEN mit_price_kg ELSE mit_price_kg_air END,
            cust_price_cbm_air = CASE WHEN cust_price_cbm_air IS NULL OR cust_price_cbm_air = 0 THEN cust_price_cbm ELSE cust_price_cbm_air END,
            cust_price_kg_air = CASE WHEN cust_price_kg_air IS NULL OR cust_price_kg_air = 0 THEN cust_price_kg ELSE cust_price_kg_air END
        ');

        // Now we can safely drop the general pricing columns
        Schema::table('category_products', function (Blueprint $table) {
            $table->dropColumn([
                'mit_price_cbm',
                'mit_price_kg',
                'cust_price_cbm',
                'cust_price_kg'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate the general pricing columns
        Schema::table('category_products', function (Blueprint $table) {
            $table->double('mit_price_cbm', 15, 2)->default(0)->after('mitra_id');
            $table->double('mit_price_kg', 15, 2)->default(0)->after('mit_price_cbm');
            $table->double('cust_price_cbm', 15, 2)->default(0)->after('mit_price_kg');
            $table->double('cust_price_kg', 15, 2)->default(0)->after('cust_price_cbm');
        });

        // Set the general pricing columns based on SEA pricing
        DB::statement('UPDATE category_products SET 
            mit_price_cbm = mit_price_cbm_sea,
            mit_price_kg = mit_price_kg_sea,
            cust_price_cbm = cust_price_cbm_sea,
            cust_price_kg = cust_price_kg_sea
        ');
    }
};