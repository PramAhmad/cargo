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
        Schema::table('category_products', function (Blueprint $table) {
            // Add SEA transport columns
            $table->double('mit_price_cbm_sea', 15, 2)->nullable()->after('mit_price_cbm');
            $table->double('mit_price_kg_sea', 15, 2)->nullable()->after('mit_price_kg');
            $table->double('cust_price_cbm_sea', 15, 2)->nullable()->after('cust_price_cbm');
            $table->double('cust_price_kg_sea', 15, 2)->nullable()->after('cust_price_kg');
            
            // Add AIR transport columns
            $table->double('mit_price_cbm_air', 15, 2)->default(0)->after('mit_price_cbm_sea');
            $table->double('mit_price_kg_air', 15, 2)->default(0)->after('mit_price_kg_sea');
            $table->double('cust_price_cbm_air', 15, 2)->default(0)->after('cust_price_cbm_sea');
            $table->double('cust_price_kg_air', 15, 2)->default(0)->after('cust_price_kg_sea');
        });
        
        // Copy existing data to the new SEA columns
        DB::statement('UPDATE category_products SET 
            mit_price_cbm_sea = mit_price_cbm,
            mit_price_kg_sea = mit_price_kg,
            cust_price_cbm_sea = cust_price_cbm,
            cust_price_kg_sea = cust_price_kg
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('category_products', function (Blueprint $table) {
            // Remove SEA and AIR transport columns
            $table->dropColumn([
                'mit_price_cbm_sea',
                'mit_price_kg_sea',
                'cust_price_cbm_sea',
                'cust_price_kg_sea',
                'mit_price_cbm_air',
                'mit_price_kg_air',
                'cust_price_cbm_air',
                'cust_price_kg_air'
            ]);
        });
    }
};