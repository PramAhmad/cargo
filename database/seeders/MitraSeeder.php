<?php

namespace Database\Seeders;

use App\Models\Mitra;
use App\Models\MitraGroup;
use App\Models\Bank;
use App\Models\User;
use App\Enums\UserStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class MitraSeeder extends Seeder
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
                'group_name'  => 'mitra',
                'permissions' => [
                    'mitra.create',
                    'mitra.view',
                    'mitra.edit',
                    'mitra.delete',
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
        
        $role = Role::firstOrCreate(['name' => 'mitra']);
        $role->syncPermissions(['mitra.view']);
        
        // Create some mitra groups if none exist
        if (MitraGroup::count() == 0) {
            MitraGroup::create(['name' => 'Shipping Partner']);
            MitraGroup::create(['name' => 'Trucking Provider']);
            MitraGroup::create(['name' => 'Warehouse Partner']);
            MitraGroup::create(['name' => 'Freight Forwarder']);
            MitraGroup::create(['name' => 'Customs Broker']);
        }
        
        // Ensure some banks exist
        if (Bank::count() == 0) {
            $banks = [
                ['code' => 'BCA', 'name' => 'Bank Central Asia'],
                ['code' => 'BNI', 'name' => 'Bank Negara Indonesia'],
                ['code' => 'BRI', 'name' => 'Bank Rakyat Indonesia'],
                ['code' => 'MANDIRI', 'name' => 'Bank Mandiri'],
                ['code' => 'CIMB', 'name' => 'CIMB Niaga']
            ];
            
            foreach ($banks as $bank) {
                Bank::create($bank);
            }
        }
        
        // Get the highest existing mitra code number to avoid duplicates
        $lastMitra = Mitra::orderBy('code', 'desc')->first();
        $nextNumber = 1;
        
        if ($lastMitra) {
            // Extract the numeric part from the last code
            if (preg_match('/^MTR(\d+)$/', $lastMitra->code, $matches)) {
                $nextNumber = (int)$matches[1] + 1;
            }
        }
        
        // Create 15 mitra records
        for ($i = 0; $i < 15; $i++) {
            $mitraGroupId = MitraGroup::inRandomOrder()->first()->id;
            $bankId = Bank::inRandomOrder()->first()->id;
            $paymentTerms = [0, 7, 14, 30, 45, 60];
            
            $code = 'MTR' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
            $nextNumber++;
            
            // Check if this code already exists (additional safeguard)
            while (Mitra::where('code', $code)->exists()) {
                $code = 'MTR' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
                $nextNumber++;
            }
            
            $mitra = Mitra::create([
                'mitra_group_id' => $mitraGroupId,
                'bank_id' => $bankId,
                'code' => $code,
                'name' => $faker->company,
                'address_office_indo' => $faker->address,
                'no_rek' => $faker->numerify('##########'),
                'atas_nama' => $faker->name,
                'phone1' => $faker->phoneNumber,
                'phone2' => rand(0, 1) ? $faker->phoneNumber : null,
                'email' => $faker->unique()->companyEmail,
                'website' => rand(0, 1) ? $faker->url : null,
                'birthdate' => null,
                'created_date' => $faker->dateTimeBetween('-3 years', 'now'),
                'ktp' => rand(0, 1) ? $faker->numerify('################') : null,
                'npwp' => rand(0, 1) ? $faker->numerify('##.###.###.#-###.###') : null,
                'tax_address' => rand(0, 1) ? $faker->address : null,
                'syarat_bayar' => $faker->randomElement($paymentTerms),
                'batas_tempo' => $faker->randomElement($paymentTerms),
                'status' => rand(0, 10) > 1, // 80% chance of being active
            ]);
            
            // For some mitra, create a user account (40% chance)
            if (rand(0, 10) < 4) {
                // Make sure the email is unique for users
                $email = $mitra->email;
                $counter = 1;
                while (User::where('email', $email)->exists()) {
                    $email = str_replace('@', $counter . '@', $mitra->email);
                    $counter++;
                }
                
                $user = User::create([
                    'name' => $faker->name,
                    'email' => $email,
                    'password' => Hash::make('password123'),
                    'phone' => $mitra->phone1,
                    'status' => UserStatus::ACTIVE,
                ]);
                
                $user->assignRole($role);
                
                // Link the user to this mitra
                $mitra->user_id = $user->id;
                $mitra->save();
            }
        }
        
        // Create at least one mitra with a user account for testing
        // Check if test mitra exists first to prevent duplicate entry
        if (!Mitra::where('email', 'test.mitra@example.com')->exists()) {
            // Generate a unique code for test mitra
            $testCode = 'MTR' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
            while (Mitra::where('code', $testCode)->exists()) {
                $nextNumber++;
                $testCode = 'MTR' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
            }
            
            $mitra = Mitra::create([
                'mitra_group_id' => MitraGroup::inRandomOrder()->first()->id,
                'bank_id' => Bank::inRandomOrder()->first()->id,
                'code' => $testCode,
                'name' => 'Test Logistics Partner',
                'address_office_indo' => $faker->address,
                'no_rek' => $faker->numerify('##########'),
                'atas_nama' => 'Test Mitra Account',
                'phone1' => '081234567890',
                'phone2' => '081234567891',
                'email' => 'test.mitra@example.com',
                'website' => 'https://testmitra.example.com',
                'created_date' => now(),
                'ktp' => $faker->numerify('################'),
                'npwp' => $faker->numerify('##.###.###.#-###.###'),
                'tax_address' => $faker->address,
                'syarat_bayar' => 30,
                'batas_tempo' => 14,
                'status' => true,
            ]);
            
            // Check if the test user already exists
            $user = User::where('email', 'test.mitra@example.com')->first();
            
            if (!$user) {
                $user = User::create([
                    'name' => 'Test Mitra User',
                    'email' => 'test.mitra@example.com',
                    'password' => Hash::make('password'),
                    'phone' => '081234567890',
                    'status' => UserStatus::ACTIVE,
                ]);
                
                $user->assignRole($role);
            }
            
            $mitra->user_id = $user->id;
            $mitra->save();
        }
    }
}
