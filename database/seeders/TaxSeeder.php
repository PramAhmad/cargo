<?php

namespace Database\Seeders;

use App\Models\Tax;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Tax::create([
            'name' => 'PPN',
            'type' => 'percentage',
            'value' => 10,
            'is_active' => true,
        ]);
        Tax::create([
            'name' => 'PPh',
            'type' => 'fixed',
            'value' => 12,
            'is_active' => true,
        ]);
    }
}
