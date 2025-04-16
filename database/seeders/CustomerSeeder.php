<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\CategoryCustomer;
use App\Models\Marketing;
use App\Models\User;
use App\Enums\UserStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            [
                'group_name'  => 'customer',
                'permissions' => [
                    'customer.create',
                    'customer.view',
                    'customer.edit',
                    'customer.delete',
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
        
        // Create a customer role with limited permissions
        $role = Role::firstOrCreate(['name' => 'customer']);
        $role->syncPermissions(['customer.view']);
        
        // Create some customer groups if none exist
        if (CustomerGroup::count() == 0) {
            CustomerGroup::create(['name' => 'Regular']);
            CustomerGroup::create(['name' => 'VIP']);
            CustomerGroup::create(['name' => 'Corporate']);
        }
        
        // Create some customer categories if none exist
        if (CategoryCustomer::count() == 0) {
            CategoryCustomer::create(['name' => 'Retail']);
            CategoryCustomer::create(['name' => 'Wholesale']);
            CategoryCustomer::create(['name' => 'Business']);
        }
        
        // Ensure we have marketing data
        if (Marketing::count() == 0) {
            $this->call(MarketingSeeder::class);
        }
        
        // Get all marketing IDs
        $marketingIds = Marketing::pluck('id')->toArray();
        $customerGroupIds = CustomerGroup::pluck('id')->toArray();
        $customerCategoryIds = CategoryCustomer::pluck('id')->toArray();
        
        // Create 25 customer records
        Customer::factory(10)->create([
            'marketing_id' => function() use ($marketingIds) {
                return $marketingIds[array_rand($marketingIds)];
            },
            'customer_group_id' => function() use ($customerGroupIds) {
                return $customerGroupIds[array_rand($customerGroupIds)];
            },
            'customer_category_id' => function() use ($customerCategoryIds) {
                return $customerCategoryIds[array_rand($customerCategoryIds)];
            },
        ])->each(function ($customer) use ($role) {
            // For some customers, create a user account (40% chance)
            if (rand(0, 100) < 40 && $customer->email) {
                $user = User::create([
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'password' => Hash::make('password123'),
                    'phone' => $customer->phone1,
                    'status' => UserStatus::ACTIVE,
                ]);
                
                $user->assignRole($role);
                
                // Link the user to this customer
                $customer->users_id = $user->id;
                $customer->save();
            }
        });
    }
}
