<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'problem_id',
        'user_id',
    ];

    public function problem()
    {
        return $this->belongsTo(Problem::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'group_user');
    }

    public function kelas()
    {
        return $this->hasOneThrough(
            Kelas::class,
            Problem::class,
            'id', // Foreign key on Problem table
            'id', // Foreign key on Kelas table
            'problem_id', // Local key on Group table
            'kelas_id' // Local key on Problem table
        );
    }
}
