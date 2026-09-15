<?php

namespace Database\Factories;

use App\Models\QuizQuestion;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuizQuestionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = QuizQuestion::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $type = $this->faker->randomElement(['pilihan_ganda', 'benar_salah', 'isian']);
        $options = null;
        $answer = null;

        switch ($type) {
            case 'pilihan_ganda':
                $options = $this->faker->words(4);
                $answer = $this->faker->numberBetween(0, 3);
                break;
            case 'benar_salah':
                $answer = $this->faker->randomElement(['Benar', 'Salah']);
                break;
            case 'isian':
                $answer = $this->faker->word;
                break;
        }

        return [
            'quiz_id' => Quiz::factory(),
            'pertanyaan' => $this->faker->sentence . '?',
            'tipe' => $type,
            'opsi_jawaban' => $options,
            'jawaban_benar' => $answer,
            'user_id' => User::factory()->state(['role' => 'guru']),
        ];
    }
}
