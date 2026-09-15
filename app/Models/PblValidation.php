<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Problem;
use App\Models\User;

class PblValidation extends Model
{
    use HasFactory;

    protected $table = 'pbl_validations';
    protected $fillable = ['problem_id','user_id','group_id','step','status','validated_by','validated_at'];

    public function problem()
    {
        return $this->belongsTo(Problem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
