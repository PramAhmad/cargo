<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Bank;
use App\Models\CategoryCustomer;
use App\Models\CustomerGroup;
use App\Models\MitraGroup;
use App\Models\MarketingGroup;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Permissions data
        $permissions = [
          
          
            [
                'group_name'  => 'banks',
                'permissions' => [
                    'banks.create',
                    'banks.view',
                    'banks.edit',
                    'banks.delete',
                ],
            ],
            [
                'group_name'  => 'category-customers',
                'permissions' => [
                    'category-customers.create',
                    'category-customers.view',
                    'category-customers.edit',
                    'category-customers.delete',
                ],
            ],
            [
                'group_name'  => 'customer-groups',
                'permissions' => [
                    'customer-groups.create',
                    'customer-groups.view',
                    'customer-groups.edit',
                    'customer-groups.delete',
                ],
            ],
            [
                'group_name'  => 'mitra-groups',
                'permissions' => [
                    'mitra-groups.create',
                    'mitra-groups.view',
                    'mitra-groups.edit',
                    'mitra-groups.delete',
                ],
            ],
            [
                'group_name'  => 'marketing-groups',
                'permissions' => [
                    'marketing-groups.create',
                    'marketing-groups.view',
                    'marketing-groups.edit',
                    'marketing-groups.delete',
                ],
            ],
        ];

        // Create permissions
        foreach ($permissions as $group) {
            $groupName        = $group['group_name'];
            $groupPermissions = $group['permissions'];

            foreach ($groupPermissions as $permissionName) {
                Permission::create(['name' => $permissionName, 'group_name' => $groupName]);
            }
        }   
        // assign all permission to super-admin
   
        
      
       
        // Seed Banks data
        $banks = [
            ['name' => 'Bank BCA'],
            ['name' => 'Bank Mandiri'],
            ['name' => 'Bank BNI'],
            ['name' => 'Bank BRI'],
            ['name' => 'Bank CIMB Niaga'],
            ['name' => 'Bank Danamon'],
            ['name' => 'Bank Permata'],
            ['name' => 'Bank BTN'],
            ['name' => 'Bank Syariah Indonesia'],
            ['name' => 'Bank OCBC NISP']
        ];
        
        foreach ($banks as $bank) {
            Bank::create($bank);
        }
        
        // Seed Category Customers data
        $categoryCustomers = [
            ['name' => 'Retail'],
            ['name' => 'Corporate'],
            ['name' => 'SME'],
            ['name' => 'Enterprise'],
            ['name' => 'Government']
        ];
        
        foreach ($categoryCustomers as $categoryCustomer) {
            CategoryCustomer::create($categoryCustomer);
        }
        
        // Seed Customer Groups data
        $customerGroups = [
            ['name' => 'Premium'],
            ['name' => 'Regular'],
            ['name' => 'VIP'],
            ['name' => 'Gold'],
            ['name' => 'Platinum']
        ];
        
        foreach ($customerGroups as $customerGroup) {
            CustomerGroup::create($customerGroup);
        }
        
        // Seed Mitra Groups data
        $mitraGroups = [
            ['name' => 'Distributor'],
            ['name' => 'Reseller'],
            ['name' => 'Agent'],
            ['name' => 'Dropshipper'],
            ['name' => 'Wholesaler']
        ];
        
        foreach ($mitraGroups as $mitraGroup) {
            MitraGroup::create($mitraGroup);
        }
        
        // Seed Marketing Groups data
        $marketingGroups = [
            ['name' => 'Digital Marketing'],
            ['name' => 'Traditional Marketing'],
            ['name' => 'Social Media Marketing'],
            ['name' => 'Content Marketing'],
            ['name' => 'Email Marketing']
        ];
        
        foreach ($marketingGroups as $marketingGroup) {
            MarketingGroup::create($marketingGroup);
        }
    }
}