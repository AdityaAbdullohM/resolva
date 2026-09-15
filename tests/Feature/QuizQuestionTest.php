<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Quiz;
use App\Models\Kelas;
use App\Models\QuizQuestion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizQuestionTest extends TestCase
{
    use RefreshDatabase;

    private User $guru;
    private Kelas $kelas;
    private Quiz $quiz;

    protected function setUp(): void
    {
        parent::setUp();

        $this->guru = User::factory()->create(['role' => 'guru']);
        $this->kelas = Kelas::factory()->create();
        $this->quiz = Quiz::factory()->create([
            'user_id' => $this->guru->id,
            'kelas_id' => $this->kelas->id,
        ]);
    }

    public function test_teacher_can_add_multiple_choice_question_to_quiz()
    {
        $this->actingAs($this->guru);

        $questionData = [
            'pertanyaan' => 'What is 2 + 2?',
            'tipe' => 'pilihan_ganda',
            'opsi_jawaban' => '2, 3, 4, 5',
            'jawaban_benar' => '4',
        ];

        $response = $this->post(route('dosen.kuis.questions.store', $this->quiz), $questionData);

        $response->assertRedirect(route('dosen.kuis.show', $this->quiz));
        $this->assertDatabaseHas('quiz_questions', [
            'quiz_id' => $this->quiz->id,
            'pertanyaan' => 'What is 2 + 2?',
            'tipe' => 'pilihan_ganda',
            'opsi_jawaban' => json_encode(['2', '3', '4', '5']),
            'jawaban_benar' => '2', // Index of '4'
        ]);
    }

    public function test_teacher_can_see_question_details_on_quiz_show_page()
    {
        $this->actingAs($this->guru);

        $question = QuizQuestion::factory()->create([
            'quiz_id' => $this->quiz->id,
            'pertanyaan' => 'This is a test question',
            'tipe' => 'pilihan_ganda',
            'opsi_jawaban' => ['Option A', 'Option B', 'Option C'],
            'jawaban_benar' => 1, // Option B
        ]);

        $response = $this->get(route('dosen.kuis.show', $this->quiz));

        $response->assertStatus(200);
        $response->assertSee('This is a test question');
        $response->assertSee('Option A');
        $response->assertSee('Option B');
        $response->assertSee('Option C');
        $response->assertSee('Option B (Jawaban Benar)');
    }

    public function test_validation_fails_if_correct_answer_not_in_options()
    {
        $this->actingAs($this->guru);

        $questionData = [
            'pertanyaan' => 'What is the capital of France?',
            'tipe' => 'pilihan_ganda',
            'opsi_jawaban' => 'London, Berlin, Madrid',
            'jawaban_benar' => 'Paris', // Not in options
        ];

        $response = $this->post(route('dosen.kuis.questions.store', $this->quiz), $questionData);

        $response->assertSessionHasErrors('jawaban_benar');
    }
}
