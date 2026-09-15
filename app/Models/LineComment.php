<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LineComment extends Model
{
    protected $fillable = [
        'submission_id',
        'user_id',
        'line_number',
        'comment',
    ];

    /**
     * Get the submission that owns the line comment.
     */
    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    /**
     * Get the user (teacher) that owns the line comment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
