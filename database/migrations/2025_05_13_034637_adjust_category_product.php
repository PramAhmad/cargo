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
        // First, add the new columns to category_products table
        Schema::table('category_products', function (Blueprint $table) {
            $table->foreignId('mitra_id')->nullable()->after('id')->constrained('mitras')->nullOnDelete();
            $table->double('mit_price_cbm')->nullable()->after('name');
            $table->double('mit_price_kg')->nullable()->after('mit_price_cbm');
            $table->double('cust_price_cbm')->nullable()->after('mit_price_kg');
            $table->double('cust_price_kg')->nullable()->after('cust_price_cbm');
        });
        
        // Then, migrate data from products to category_products
        // This is more complex and we'll use DB facade for this
        \DB::statement("
            UPDATE category_products cp
            JOIN products p ON p.category_product_id = cp.id
            SET 
                cp.mit_price_cbm = p.mit_price_cbm,
                cp.mit_price_kg = p.mit_price_kg,
                cp.cust_price_cbm = p.cust_price_cbm,
                cp.cust_price_kg = p.cust_price_kg
            WHERE 
                p.mit_price_cbm IS NOT NULL
                OR p.mit_price_kg IS NOT NULL
                OR p.cust_price_cbm IS NOT NULL
                OR p.cust_price_kg IS NOT NULL
        ");
        
        // Finally, remove the price columns from products table
        Schema::table('products', function (Blueprint $table) {
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
       
        
        // Then, migrate data back from category_products to products
        \DB::statement("
            UPDATE products p
            JOIN category_products cp ON p.category_product_id = cp.id
            SET 
                p.mit_price_cbm = cp.mit_price_cbm,
                p.mit_price_kg = cp.mit_price_kg,
                p.cust_price_cbm = cp.cust_price_cbm,
                p.cust_price_kg = cp.cust_price_kg
        ");
        
        // Finally, remove the price columns and mitra_id from category_products table
        Schema::table('category_products', function (Blueprint $table) {
            $table->dropForeign(['mitra_id']);
            $table->dropColumn([
                'mitra_id',
                'mit_price_cbm',
                'mit_price_kg',
                'cust_price_cbm',
                'cust_price_kg'
            ]);
        });
    }
};
