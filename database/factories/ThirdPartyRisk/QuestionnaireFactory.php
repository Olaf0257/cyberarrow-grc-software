<?php

namespace Database\Factories\ThirdPartyRisk;

use App\Models\ThirdPartyRisk\Questionnaire;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionnaireFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Questionnaire::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => "Questionnaire " . $this->faker->numberBetween(1, 99),
            'version' => "v." . $this->faker->numberBetween(1, 10),
            'is_default' => 0,
        ];
    }

    public function isDefault()
    {
        return $this->state(function () {
            return [
                'is_default' => 1
            ];
        });
    }
}
