<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class QuizController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $studentId = Auth::id();
        $quizzes = Quiz::with(['mataPelajaran', 'user', 'attempts' => function ($query) use ($studentId) {
            $query->where('user_id', $studentId);
        }])->paginate(12);

        return view('siswa.quizzes.index', compact('quizzes'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Quiz $quiz)
    {
        $quiz->load('questions');
        $attempt = $quiz->attempts()->where('user_id', Auth::id())->with('answers.question')->first();
        // If no attempt exists yet for this student, create an in-progress attempt now
        if (!$attempt) {
            $attempt = QuizAttempt::create([
                'quiz_id' => $quiz->id,
                'user_id' => Auth::id(),
                'score' => 0,
                'start_time' => Carbon::now()->setTimezone(config('app.timezone')),
                'end_time' => null,
                'status' => 'started',
            ]);
        }

        // ensure relation loaded
        $attempt->load('answers.question');

        return view('siswa.quizzes.show', compact('quiz', 'attempt'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function submit(Request $request, Quiz $quiz)
    {
        $request->validate([
            'answers' => ['required', 'array'],
        ]);
        $answers = $request->input('answers', []);

        // Load questions to iterate
        $quiz->load('questions');

        // Get or create an attempt for this student
        $attempt = $quiz->attempts()->where('user_id', Auth::id())->with('answers')->first();

        if ($attempt && ($attempt->status ?? '') === 'finished') {
            return redirect()->route('mahasiswa.quizzes.show', $quiz)->with('error', 'Anda sudah pernah mengerjakan kuis ini.');
        }

        if (!$attempt) {
            return redirect()->route('mahasiswa.quizzes.show', $quiz)->with('error', 'Silakan buka kuis terlebih dahulu untuk memulai sebelum mengirim jawaban.');
        }

        // clear any previous answers for this attempt
        $attempt->answers()->delete();

        // Prefer a client-provided start time when available. On some hosting setups the server
        // may record the attempt start and end with the same timestamp; prefer the client timestamp
        // if it is earlier or if the recorded start_time appears invalid (null or equal to end time).
        $clientStarted = $request->input('client_started_at');
        if ($clientStarted) {
                try {
                    // Parse client-sent ISO string (UTC) and convert to application timezone
                    $clientdt = Carbon::parse($clientStarted)->setTimezone(config('app.timezone'));
                $serverStart = $attempt->start_time ? Carbon::parse($attempt->start_time) : null;
                $serverEnd = $attempt->end_time ? Carbon::parse($attempt->end_time) : null;

                $shouldUpdate = false;
                if (is_null($serverStart)) {
                    $shouldUpdate = true;
                } else {
                    // If server start equals server end (likely placeholder), prefer client
                    if (!is_null($serverEnd) && $serverStart->equalTo($serverEnd)) {
                        $shouldUpdate = true;
                    }

                    // If client time is earlier than server start, prefer client
                    if ($clientdt->lessThan($serverStart)) {
                        $shouldUpdate = true;
                    }

                    // If client and server start differ significantly (clock skew), prefer client
                    try {
                        if ($clientdt->diffInSeconds($serverStart) > 60) {
                            $shouldUpdate = true;
                        }
                    } catch (\Exception $e) {
                        // ignore diff errors
                    }
                }

                if ($shouldUpdate) {
                    // Store normalized client start time (in app timezone)
                    $attempt->update(['start_time' => $clientdt]);
                    $attempt->refresh();
                }
            } catch (\Exception $e) {
                // ignore parse errors
            }
        }

        $totalScore = 0;
        $maxPossibleScore = 0;

        foreach ($quiz->questions as $question) {
            $isCorrect = false;
            $userAnswer = $answers[$question->id] ?? null;
            $qnilai = isset($question->nilai) ? (float) $question->nilai : 0;
            $maxPossibleScore += $qnilai;

            if ($userAnswer !== null) {
                if ($question->tipe === 'pilihan_ganda') {
                    $isCorrect = $userAnswer == $question->jawaban_benar;
                } elseif ($question->tipe === 'benar_salah') {
                    $isCorrect = $userAnswer == $question->jawaban_benar;
                } elseif ($question->tipe === 'isian') {
                    $isCorrect = mb_strtolower(trim($userAnswer)) == mb_strtolower(trim($question->jawaban_benar));
                }

                if ($isCorrect) {
                    $totalScore += $qnilai;
                }

                QuizAnswer::create([
                    'quiz_attempt_id' => $attempt->id,
                    'quiz_question_id' => $question->id,
                    'answer' => $userAnswer,
                    'is_correct' => $isCorrect,
                ]);
            }
        }

        if ($maxPossibleScore > 0) {
            $totalScore = round(($totalScore / $maxPossibleScore) * 100, 2);
        } else {
            $totalScore = 0;
        }

        // finalize attempt - store end_time normalized to app timezone
        $attempt->update([
            'score' => $totalScore,
            'end_time' => Carbon::now()->setTimezone(config('app.timezone')),
            'status' => 'finished',
        ]);

        // format display: integer without .0 when possible
        if (floor($totalScore) == $totalScore) {
            $displayScore = (int) $totalScore;
        } else {
            $displayScore = rtrim(rtrim(number_format($totalScore, 2, '.', ''), '0'), '.');
        }

        return redirect()->route('mahasiswa.quizzes.show', $quiz)
            ->with('success', "Kuis selesai! Skor Anda: " . $displayScore);
    }
}