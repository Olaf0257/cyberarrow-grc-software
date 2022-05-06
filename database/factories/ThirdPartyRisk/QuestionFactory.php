<?php

namespace Database\Factories\ThirdPartyRisk;

use App\Models\ThirdPartyRisk\Domain;
use App\Models\ThirdPartyRisk\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Question::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'text' => $this->faker->sentence(),
            'domain_id' => function () {
                return Domain::inRandomOrder()->first()->id;
            }
        ];
    }

    public function for_questionnaire($questionnaire_id)
    {
        return $this->state(function () use ($questionnaire_id) {
            return [
                'questionnaire_id' => $questionnaire_id
            ];
        });
    }
}
