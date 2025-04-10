<?php

namespace Database\Seeders;

use App\Models\Marketing;
use App\Models\MarketingGroup;
use App\Models\User;
use App\Enums\UserStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class MarketingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            [
                'group_name'  => 'marketing',
                'permissions' => [
                    'marketing.create',
                    'marketing.view',
                    'marketing.edit',
                    'marketing.delete',
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
        
        $role = Role::firstOrCreate(['name' => 'marketing']);
        $role->syncPermissions(['marketing.view']);
        
        // Create some marketing groups if none exist
        if (MarketingGroup::count() == 0) {
            MarketingGroup::create(['name' => 'Corporate', 'code' => 'CRP', 'status' => 1]);
            MarketingGroup::create(['name' => 'Individual', 'code' => 'IND', 'status' => 1]);
            MarketingGroup::create(['name' => 'Agency', 'code' => 'AGN', 'status' => 1]);
        }
        
        // Create 10 marketing records
        Marketing::factory(10)->create()->each(function ($marketing) use ($role) {
            // For some marketing contacts, create a user account (50% chance)
            if (rand(0, 1) && $marketing->email) {
                $user = User::create([
                    'name' => $marketing->name,
                    'email' => $marketing->email,
                    'password' => Hash::make('password123'),
                    'phone' => $marketing->phone1,
                    'status' => UserStatus::ACTIVE,
                ]);
                
                $user->assignRole($role);
                
                // Link the user to this marketing
                $marketing->user_id = $user->id;
                $marketing->save();
            }
        });
    }
}
