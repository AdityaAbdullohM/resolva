<?php

namespace App\Policies;

use App\Models\Quiz;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class QuizPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the quiz.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Quiz  $quiz
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Quiz $quiz)
    {
        return $user->id === $quiz->user_id;
    }

    /**
     * Determine whether the user can create a question for the quiz.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Quiz  $quiz
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function createQuestion(User $user, Quiz $quiz)
    {
        return $user->id === $quiz->user_id;
    }

    /**
     * Determine whether the user can update a question for the quiz.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Quiz  $quiz
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function updateQuestion(User $user, Quiz $quiz)
    {
        return $user->id === $quiz->user_id;
    }

    /**
     * Determine whether the user can delete a question for the quiz.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Quiz  $quiz
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function deleteQuestion(User $user, Quiz $quiz)
    {
        return $user->id === $quiz->user_id;
    }

    /**
     * Determine whether the user can view the quiz.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Quiz  $quiz
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Quiz $quiz)
    {
        return $user->id === $quiz->user_id;
    }

    /**
     * Determine whether the user can delete the quiz.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Quiz  $quiz
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Quiz $quiz)
    {
        return $user->id === $quiz->user_id;
    }
}
