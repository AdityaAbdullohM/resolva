<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // Import HasMany

class Submission extends Model
{
    use HasFactory;

    protected $fillable = [
        'problem_id',
        'user_id',
        'content',
        'file_path',
        'submitted_at',
        'status',
        'nilai',
        'feedback',
        'stage_contents',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'submitted_at' => 'datetime',
        'file_path' => 'array',
        'stage_contents' => 'array',
    ];

    /**
     * Mendapatkan problem dari submission ini.
     */
    public function problem()
    {
        return $this->belongsTo(Problem::class);
    }

    /**
     * Mendapatkan user (siswa) yang membuat submission ini.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the line comments for the submission.
     */
    public function lineComments(): HasMany
    {
        return $this->hasMany(LineComment::class);
    }
}
