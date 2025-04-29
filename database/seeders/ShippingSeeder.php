<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class ShippingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            [
                'group_name'  => 'shipping',
                'permissions' => [                    
                    'shipping.create',
                    'shipping.view',
                    'shipping.edit',
                    'shipping.delete',
                ],
            ]
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
            
    }
}
