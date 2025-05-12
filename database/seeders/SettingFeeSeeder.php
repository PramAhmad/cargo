<?php

namespace Database\Seeders;

use AnisAronno\LaravelSettings\Models\SettingsProperty;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class SettingFeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    protected $model = SettingsProperty::class;

    public function run(): void
    {
        $this->call([
            LaravelSettingsSeeder::class,
        ]);
        
        setSettings('fee_layanan', 0);
        setSettings('fee_marketing', 0);      
    }
}
