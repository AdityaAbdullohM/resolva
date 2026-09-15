<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Problem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'judul',
        'deskripsi',
        'kompetensi_java',
        'deadline',
        'kelas_id',
        'mata_pelajaran_id',
        'user_id',
        'links',
        'instructions',
    ];

    protected $casts = [
        'links' => 'array',
        'instructions' => 'array',
    ];

    /**
     * Mendapatkan kelas tempat problem ini berada.
     */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    /**
     * Mendapatkan semua submission untuk problem ini.
     */
    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    /**
     * Problem files uploaded by teacher when creating problem.
     */
    public function files()
    {
        return $this->hasMany(ProblemFile::class);
    }

    /**
     * Mendapatkan semua diskusi untuk problem ini.
     */
    public function discussions()
    {
        return $this->hasMany(Discussion::class);
    }

    /**
     * Mendapatkan mata pelajaran yang terkait dengan problem ini.
     */
    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }
}
