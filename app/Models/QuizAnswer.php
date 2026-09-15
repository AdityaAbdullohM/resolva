<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_attempt_id',
        'quiz_question_id',
        // Some places expect `answer` while others use `answer_text`.
        // Keep both to remain compatible with existing code and DB.
        'answer_text',
        'answer',
        'is_correct',
        'points_awarded',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'points_awarded' => 'float',
    ];

    public function quizQuestion()
    {
        return $this->belongsTo(QuizQuestion::class);
    }

    /**
     * Compatibility alias: some code expects a `question` relation on QuizAnswer.
     */
    public function question()
    {
        return $this->belongsTo(QuizQuestion::class, 'quiz_question_id');
    }

    public function quizAttempt()
    {
        return $this->belongsTo(QuizAttempt::class);
    }
}
