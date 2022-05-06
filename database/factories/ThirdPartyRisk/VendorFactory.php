<?php

namespace Database\Factories\ThirdPartyRisk;

use App\Models\ThirdPartyRisk\Domain;
use App\Models\ThirdPartyRisk\Industry;
use App\Models\ThirdPartyRisk\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

class VendorFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Vendor::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => "Vendor " . $this->faker->numberBetween(1, 99),
            'contact_name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'status' => 'active',
            'industry_id' => function () {
                return Industry::inRandomOrder()->first()->id;
            }
        ];
    }

    public function disabled()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'disabled'
            ];
        });
    }
}
