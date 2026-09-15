<?php

namespace Database\Factories;

use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class KelasFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Kelas::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'nama' => $this->faker->unique()->word,
            'jurusan' => $this->faker->randomElement(['RPL', 'TKJ', 'MM']),
            'tahun_ajaran_id' => TahunAjaran::factory(),
            'semester_id' => Semester::factory(),
            'user_id' => User::factory()->state(['role' => 'guru']),
        ];
    }
}
