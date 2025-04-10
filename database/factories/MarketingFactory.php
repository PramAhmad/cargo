<?php

namespace Database\Factories;

use App\Models\Bank;
use App\Models\MarketingGroup;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Marketing>
 */
class MarketingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => 'MKT-' . $this->faker->unique()->numberBetween(1000, 9999),
            'name' => $this->faker->name(),
            'marketing_group_id' => MarketingGroup::inRandomOrder()->first()->id ?? null,
            'bank_id' => Bank::inRandomOrder()->first()->id ?? null,
            'address' => $this->faker->address(),
            'city' => $this->faker->city(),
            'phone1' => $this->faker->phoneNumber(),
            'phone2' => $this->faker->optional(0.7)->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'website' => $this->faker->optional(0.6)->url(),
            'borndate' => $this->faker->optional(0.8)->date(),
            'atas_nama' => $this->faker->optional(0.9)->name(),
            'no_rek' => $this->faker->optional(0.9)->numerify('##############'),
            'ktp' => $this->faker->optional(0.8)->numerify('################'),
            'npwp' => $this->faker->optional(0.7)->numerify('##.###.###.#-###.###'),
            'requirement' => $this->faker->optional(0.6)->paragraph(),
            'address_tax' => $this->faker->optional(0.7)->address(),
            'due_date' => $this->faker->optional(0.8)->numberBetween(7, 60),
            'status' => $this->faker->boolean(80), // 80% active, 20% inactive
        ];
    }
}