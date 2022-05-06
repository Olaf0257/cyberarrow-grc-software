<?php

namespace Database\Factories;

use App\Models\Compliance\Standard;
use Illuminate\Database\Eloquent\Factories\Factory;

class ComplianceStandardFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Standard::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->sentence(),
            'version' => 'V'.$this->faker->randomFloat(1,1,10)
        ];
    }
}
