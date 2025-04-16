<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Bank;
use App\Models\CustomerGroup;
use App\Models\CategoryCustomer;
use App\Models\Marketing;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Customer::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $types = ['individual', 'company', 'internal'];
        $type = $this->faker->randomElement($types);
        
        // Generate proper data based on customer type
        $name = $type === 'company' 
            ? $this->faker->company() 
            : $this->faker->name();
            
        // Bank ID (if any banks exist)
        $bankId = null;
        if (Bank::count() > 0) {
            $bankId = Bank::inRandomOrder()->first()->id;
        }
        
        // Generate a customer code
        $code = Customer::generateCustomerCode();
        
        return [
            'code' => $code,
            'type' => $type,
            'name' => $name,
            'phone1' => $this->faker->unique()->numberBetween(10000,1000000),
            'phone2' => $this->faker->optional(0.5)->numberBetween(10000,1000000),
            'email' => $this->faker->unique()->safeEmail(),
            'website' => $this->faker->optional(0.4)->url(),
            'status' => 'active', // 90% active
            'street1' => $this->faker->streetAddress(),
            'street2' => $this->faker->optional(0.5)->secondaryAddress(),
            'street_item' => $this->faker->optional(0.3)->secondaryAddress(),
            'city' => $this->faker->city(),
            'country' => 'Indonesia',
            'bank_id' => $bankId,
            'no_rek' => $this->faker->numerify('##########'),
            'atas_nama' => $this->faker->name(),
            'npwp' => 123456,
            'tax_address' => $this->faker->address(),
            'borndate' => $type === 'individual' ? $this->faker->dateTimeBetween('-60 years', '-18 years') : null,
            'created_date' => $this->faker->dateTimeBetween('-2 years', 'now'),
            // Foreign keys will be set in the seeder
            'marketing_id' => null, 
            'customer_group_id' => null,
            'customer_category_id' => null,
            'users_id' => null,
        ];
    }
    
    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        // After creating, hook to generate the code if needed
        return $this->afterMaking(function (Customer $customer) {
            if (!$customer->code) {
                $customer->code = Customer::generateCustomerCode();
            }
        });
    }
    
    /**
     * Indicate that the customer is an individual.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function individual()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'individual',
                'name' => $this->faker->name(),
                'borndate' => $this->faker->dateTimeBetween('-60 years', '-18 years'),
            ];
        });
    }
    
    /**
     * Indicate that the customer is a company.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function company()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'company',
                'name' => $this->faker->company(),
                'borndate' => null,
            ];
        });
    }
    
    /**
     * Indicate that the customer is internal.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function internal()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'internal',
                'name' => $this->faker->company() . ' (Internal)',
            ];
        });
    }
    
    /**
     * Indicate that the customer is active.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function active()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 1,
            ];
        });
    }
    
    /**
     * Indicate that the customer is inactive.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function inactive()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 0,
            ];
        });
    }
}