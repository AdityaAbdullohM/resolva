<?php

namespace Database\Factories;

use App\Models\Quiz;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuizFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Quiz::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'start_time' => now(),
            'end_time' => now()->addWeek(),
            'duration' => 60,
            'user_id' => User::factory()->state(['role' => 'guru']),
            'kelas_id' => Kelas::factory(),
        ];
    }
}
