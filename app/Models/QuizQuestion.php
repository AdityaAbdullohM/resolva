<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'pertanyaan',
        'pertanyaan_java',
        'nilai',
        'opsi_is_java',
        'tipe',
        'opsi_jawaban',
        'opsi_gambar',
        'jawaban_benar',
        'gambar',
        'explanation',
        'user_id',
    ];

    protected $casts = [
        'opsi_jawaban' => 'array',
        'opsi_is_java' => 'array',
        'opsi_gambar' => 'array',
        'nilai' => 'float',
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function answers()
    {
        return $this->hasMany(QuizAnswer::class);
    }
}
