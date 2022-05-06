<?php

namespace Database\Factories;

use App\Models\Compliance\Project;
use App\Models\Compliance\Standard;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Project::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => 'Project '.$this->faker->numberBetween(1,10),
            'description' => $this->faker->paragraph(),
            'standard_id' => function(){
                return Standard::factory()->create()->id;
            },
            'standard' => function(){
                return Standard::first()->name;
            }
        ];
    }
}
