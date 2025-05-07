<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerGroupInternal extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('customer_groups')->insert([
            'id' => 998123,
            'name' => 'Internal',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
