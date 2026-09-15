<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discussion extends Model
{
    use HasFactory;

    protected $fillable = [
        'problem_id',
        'group_id',
        'kelas_id',
        'user_id',
        'title',
        'content',
    ];

    public function problem()
    {
        return $this->belongsTo(Problem::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function posts()
    {
        return $this->hasMany(DiscussionPost::class)->whereNull('parent_id')->orderBy('created_at', 'asc');
    }
}