<?php

namespace Database\Factories\ThirdPartyRisk;

use App\Models\ThirdPartyRisk\Domain;
use App\Models\ThirdPartyRisk\Project;
use App\Models\ThirdPartyRisk\Question;
use App\Models\ThirdPartyRisk\Questionnaire;
use App\Models\ThirdPartyRisk\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Project::class;
    private $yesterday = "-1 day";

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => "Project " . $this->faker->numberBetween(1, 99),
            'questionnaire_id' => function () {
                return Questionnaire::factory()->has(Question::factory()->count(5)->for(Domain::first()))->create()->id;
            },
            'vendor_id' => function () {
                return Vendor::factory()->create()->id;
            },
            'launch_date' => $this->faker->dateTimeBetween('now', '+1 week', 'CET'),
            'due_date' => $this->faker->dateTimeBetween('+1 week', '+3 weeks', 'CET'),
            'timezone' => "Europe/Bucharest",
            'frequency' => "Annually",
            'status' => 'active'
        ];
    }

    public function in_progress()
    {
        return $this->state(function () {
            return [
                'launch_date' => $this->faker->dateTimeBetween('-2 weeks', $this->yesterday, 'CET'),
                'due_date' => $this->faker->dateTimeBetween('+1 day', '+3 weeks', 'CET'),
            ];
        });
    }

    public function not_started()
    {
        return $this->state(function () {
            return [
                'launch_date' => $this->faker->dateTimeBetween('+1 day', '+2 weeks', 'CET'),
            ];
        });
    }

    public function overdue()
    {
        return $this->state(function () {
            return [
                'launch_date' => $this->faker->dateTimeBetween('-3 weeks', '-2 weeks', 'CET'),
                'due_date' => $this->faker->dateTimeBetween('-1 weeks', $this->yesterday, 'CET'),
            ];
        });
    }

    public function completed()
    {
        return $this->state(function () {
            return [
                'status' => 'archived',
                'launch_date' => $this->faker->dateTimeBetween('-12 months', '-1 weeks', 'CET'),
                'due_date' => $this->faker->dateTimeBetween('-1 week', $this->yesterday, 'CET'),
            ];
        });
    }
}
