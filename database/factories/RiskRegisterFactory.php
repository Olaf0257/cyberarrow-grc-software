<?php

namespace Database\Factories;

use App\Models\RiskManagement\RiskCategory;
use App\Models\RiskManagement\RiskRegister;
use Illuminate\Database\Eloquent\Factories\Factory;

class RiskRegisterFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = RiskRegister::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'risk_name' => $this->faker->sentence(),
            'risk_description' => $this->faker->paragraph(),
            'treatment' => $this->faker->paragraph(),
            'category' => function(){
                return RiskCategory::create(['name' => 'Information Security Management and Governance', 'order_number' => 1])->id;
            },
            'affected_properties' => ['Confidentiality', 'Integrity', 'Availability'],
            'treatment_options' => $this->faker->randomElement(['Mitigate', 'Accept']),
            'affected_functions_or_assets' => $this->faker->sentence(),
            'likelihood' => $this->faker->numberBetween(0,4),
            'impact' => $this->faker->numberBetween(0,4),
            'data_scope' => 0
        ];
    }
}
