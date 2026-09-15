<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pertemuan extends Model
{
    use HasFactory;

    protected $fillable = [
        'kelas_id',
        'pertemuan_number',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }
}
