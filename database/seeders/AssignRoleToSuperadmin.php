<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AssignRoleToSuperadmin extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdminRole = Role::findByName('superadmin');
        $permissions = Permission::all();
        foreach ($permissions as $permission) {
            $superAdminRole->givePermissionTo($permission);
        }
    }
}
