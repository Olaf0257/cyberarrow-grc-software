<?php

namespace Database\Factories;

use App\Models\Compliance\Evidence;
use App\Models\Compliance\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class EvidenceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Evidence::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'text_evidence_name' => $this->faker->sentence(),
            'text_evidence' => $this->faker->sentence(),
        ];
    }
}
