<?php

namespace Database\Seeders;

use App\Models\CategoryProduct;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Faker\Factory as Faker;

class CategoryProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        
        // Create permissions
        $permissions = [
            [
                'group_name'  => 'category_product',
                'permissions' => [
                    'category_product.create',
                    'category_product.view',
                    'category_product.edit',
                    'category_product.delete',
                ],
            ],
        ];

        foreach ($permissions as $group) {
            $groupName        = $group['group_name'];
            $groupPermissions = $group['permissions'];

            foreach ($groupPermissions as $permissionName) {
                Permission::firstOrCreate(['name' => $permissionName], [
                    'name' => $permissionName,
                    'group_name' => $groupName
                ]);
            }
        }
        
        // Pre-defined category products
        $categories = [
            'Electronics',
            'Clothing & Apparel',
            'Home & Furniture',
            'Beauty & Personal Care',
            'Food & Beverages',
            'Automotive Parts',
            'Sports & Outdoors',
            'Toys & Games',
            'Books & Media',
            'Office Supplies',
            'Medical Equipment',
            'Industrial Supplies',
            'Construction Materials',
            'Agricultural Products',
            'Chemicals & Raw Materials',
            'Fragile Items',
            'Hazardous Materials',
            'Perishable Goods',
            'Oversized Cargo',
            'Livestock & Animals'
        ];
        
        // Check if we already have some categories
        $existingCount = CategoryProduct::count();
        
        // If we have some but not all, only add the missing ones
        if ($existingCount > 0 && $existingCount < count($categories)) {
            $existingNames = CategoryProduct::pluck('name')->toArray();
            
            foreach ($categories as $category) {
                if (!in_array($category, $existingNames)) {
                    CategoryProduct::create(['name' => $category]);
                }
            }
        }
        // If we have none, add all predefined categories
        elseif ($existingCount === 0) {
            foreach ($categories as $category) {
                CategoryProduct::create(['name' => $category]);
            }
            
            // Add 5 more random categories for diversity
            for ($i = 0; $i < 5; $i++) {
                $randomCategory = $faker->unique()->words(rand(1, 3), true);
                $randomCategory = ucwords($randomCategory);
                
                if (!in_array($randomCategory, $categories)) {
                    CategoryProduct::create(['name' => $randomCategory]);
                }
            }
        }
        
        // Create a test category for development
        CategoryProduct::firstOrCreate(
            ['name' => 'Test Category'],
            ['name' => 'Test Category']
        );
    }
}
